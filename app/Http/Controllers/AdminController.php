<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ticket;
use App\Models\KategoriLayanan;
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
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,front_desk,staf_bidang',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Fitur: Notifikasi Pembaruan Data
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,front_desk,staf_bidang',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

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
}
