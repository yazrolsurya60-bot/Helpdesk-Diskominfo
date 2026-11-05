<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ticket;
use App\Models\KategoriLayanan;
use App\Models\BidangStaf;
use App\Models\WaLog;
use App\Services\SlaService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. Metrik Top KPI Cards (Dinamis dari Database)
        $totalOpen = Ticket::where('status', 'open')->count();
        $totalProgress = Ticket::where('status', 'progress')->count();
        $totalSelesai = Ticket::where('status', 'selesai')->count();
        $totalTickets = Ticket::count();

        // 2. Kategori Layanan untuk Filter Dropdown dengan counter tiket
        $categories = KategoriLayanan::where('status_aktif', true)
            ->withCount('tickets')
            ->orderBy('nama_layanan')
            ->get();

        // 3. Query Tiket dengan filter server-side
        $selectedCategory = $request->query('kategori_id', 'all');
        $selectedStatus = $request->query('status', 'all');
        $search = $request->query('search');

        $query = Ticket::with(['user.bidangStaf', 'kategori'])
            ->latest();

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('id_kategori_api', $selectedCategory);
        }

        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('keluhan', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('nomor_wa', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(10)->withQueryString();

        return view('admin.dashboard', compact(
            'totalOpen',
            'totalProgress',
            'totalSelesai',
            'totalTickets',
            'categories',
            'tickets',
            'selectedCategory',
            'selectedStatus',
            'search'
        ));
    }

    public function index()
    {
        $users = User::with('bidangStaf')->get();
        $bidangList = BidangStaf::orderBy('nama_bidang')->get();
        return view('admin.users.index', compact('users', 'bidangList'));
    }

    public function create()
    {
        $bidangList = BidangStaf::orderBy('nama_bidang')->get();
        return view('admin.users.create', compact('bidangList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,front_desk,staf_bidang',
            'bidang_id' => 'nullable|exists:bidang_staf,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'bidang_id' => $request->role === 'staf_bidang' ? $request->bidang_id : null,
        ]);

        // Fitur: Notifikasi Pembaruan Data
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $bidangList = BidangStaf::orderBy('nama_bidang')->get();
        return view('admin.users.edit', compact('user', 'bidangList'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,front_desk,staf_bidang',
            'bidang_id' => 'nullable|exists:bidang_staf,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->bidang_id = $request->role === 'staf_bidang' ? $request->bidang_id : null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Fitur: Notifikasi Pembaruan Data
        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        // Fitur: Notifikasi Pembaruan Data
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Manajemen Bidang Staf — Tampilan Daftar & Pencarian
     */
    public function indexBidang(Request $request)
    {
        $search = $request->query('q') ?? $request->query('search');

        $query = BidangStaf::withCount('users');

        if ($search) {
            $query->where('nama_bidang', 'like', "%{$search}%");
        }

        $bidangList = $query->orderBy('nama_bidang', 'asc')->paginate(10)->withQueryString();
        $totalBidang = BidangStaf::count();
        $totalStafTerdistribusi = User::whereNotNull('bidang_id')->count();

        return view('admin.bidang.index', compact('bidangList', 'totalBidang', 'totalStafTerdistribusi', 'search'));
    }

    /**
     * Simpan Data Bidang Baru
     */
    public function storeBidang(Request $request)
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255|unique:bidang_staf,nama_bidang',
        ], [
            'nama_bidang.required' => 'Nama bidang wajib diisi.',
            'nama_bidang.unique' => 'Nama bidang sudah terdaftar dalam sistem.',
        ]);

        BidangStaf::create([
            'nama_bidang' => $request->nama_bidang,
        ]);

        return redirect()->route('admin.bidang.index')->with('success', 'Data bidang berhasil ditambahkan!');
    }

    /**
     * Perbarui Data Bidang
     */
    public function updateBidang(Request $request, BidangStaf $bidang)
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255|unique:bidang_staf,nama_bidang,' . $bidang->id,
        ], [
            'nama_bidang.required' => 'Nama bidang wajib diisi.',
            'nama_bidang.unique' => 'Nama bidang sudah terdaftar dalam sistem.',
        ]);

        $bidang->update([
            'nama_bidang' => $request->nama_bidang,
        ]);

        return redirect()->route('admin.bidang.index')->with('success', 'Data bidang berhasil diperbarui!');
    }

    /**
     * Hapus Data Bidang
     */
    public function destroyBidang(BidangStaf $bidang)
    {
        if ($bidang->users()->count() > 0) {
            return redirect()->route('admin.bidang.index')->with('error', 'Tidak dapat menghapus bidang karena masih memiliki staf teknis terkait. Alihkan staf ke bidang lain terlebih dahulu.');
        }

        $bidang->delete();

        return redirect()->route('admin.bidang.index')->with('success', 'Data bidang berhasil dihapus!');
    }
}
