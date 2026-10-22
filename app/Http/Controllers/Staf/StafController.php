<?php

namespace App\Http\Controllers\Staf;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\KategoriLayanan;
use App\Models\BidangStaf;
use App\Models\User;
use App\Models\RiwayatTiket;
use App\Models\NotifikasiInternal;
use App\Models\WaLog;
use App\Services\SlaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StafController extends Controller
{
    /**
     * Menampilkan Dashboard Umum (Antrean Tiket Masuk)
     */
    public function dashboard(Request $request)
    {
        $search = $request->input('q');
        $kategoriId = $request->input('kategori');

        // Statistik KPI Sesuai Desain Stitch
        $statTotal = Ticket::count();
        $statOpen = Ticket::where('status', 'open')->count();
        $statClaimed = Ticket::where('status', 'progress')->count();

        // Query Tiket Masuk (Status Open)
        $query = Ticket::with(['kategori', 'user'])
            ->where('status', 'open');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('keluhan', 'like', "%{$search}%")
                    ->orWhere('nomor_wa', 'like', "%{$search}%");
            });
        }

        if ($kategoriId) {
            $query->where('id_kategori_api', $kategoriId);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();
        $categories = KategoriLayanan::where('status_aktif', true)->get();

        return view('staf.dashboard', compact(
            'statTotal',
            'statOpen',
            'statClaimed',
            'tickets',
            'categories',
            'search',
            'kategoriId'
        ));
    }

    /**
     * Mengambil Tugas (Self-Assign) dari Antrean
     */
    public function ambilTiket(Request $request, Ticket $ticket)
    {
        if ($ticket->status !== 'open') {
            return back()->with('error', 'Tiket ini sudah diambil atau tidak lagi berstatus open.');
        }

        DB::transaction(function () use ($ticket) {
            $ticket->update([
                'user_id' => Auth::id(),
                'status' => 'progress',
                'assigned_at' => now(),
            ]);

            RiwayatTiket::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'aksi' => 'diambil',
            ]);
        });

        return redirect()->route('staf.tiket-saya')->with('success', "Tiket #{$ticket->id} berhasil diambil dan dimasukkan ke tugas aktif Anda.");
    }

    /**
     * Menampilkan Dashboard Tugas Pribadi (Tiket Saya)
     */
    public function tiketSaya(Request $request)
    {
        $search = $request->input('q');

        // Statistik KPI Tiket Saya
        $statActive = Ticket::where('user_id', Auth::id())
            ->where('status', 'progress')
            ->count();

        $statCompletedToday = Ticket::where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->whereDate('updated_at', today())
            ->count();

        // Query Tiket Ditugaskan ke Staf Ini
        $query = Ticket::with(['kategori', 'user'])
            ->where('user_id', Auth::id())
            ->where('status', 'progress');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('keluhan', 'like', "%{$search}%")
                    ->orWhere('nomor_wa', 'like', "%{$search}%");
            });
        }

        $myTickets = $query->orderBy('assigned_at', 'desc')->get();
        $categories = KategoriLayanan::where('status_aktif', true)->get();
        $bidangList = BidangStaf::all();
        $otherStaff = User::where('role', 'staf_bidang')
            ->where('id', '!=', Auth::id())
            ->with('bidangStaf')
            ->get();

        return view('staf.tiket-saya', compact(
            'statActive',
            'statCompletedToday',
            'myTickets',
            'categories',
            'bidangList',
            'otherStaff',
            'search'
        ));
    }

    /**
     * Mengalihkan / Disposisi Tugas & Mengubah Label Layanan
     */
    public function disposisi(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            return back()->with('error', 'Anda hanya dapat mengalihkan tiket yang sedang Anda tangani.');
        }

        $request->validate([
            'id_kategori_api' => 'required|exists:kategori_layanan,id',
            'target_user_id' => 'required|exists:users,id',
        ]);

        DB::transaction(function () use ($request, $ticket) {
            $ticket->update([
                'id_kategori_api' => $request->id_kategori_api,
                'user_id' => $request->target_user_id,
                'status' => 'progress',
                'assigned_at' => now(),
            ]);

            RiwayatTiket::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'aksi' => 'dialihkan',
            ]);

            // Kirim notifikasi ke staf penerima
            NotifikasiInternal::create([
                'user_id' => $request->target_user_id,
                'judul' => "Tiket #{$ticket->id} telah didisposisikan kepada Anda oleh " . Auth::user()->name,
                'is_read' => false,
            ]);
        });

        return redirect()->route('staf.tiket-saya')->with('success', "Tiket #{$ticket->id} berhasil dialihkan ke staf bidang tujuan.");
    }

    /**
     * Menyelesaikan Tugas
     */
    public function selesai(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            return back()->with('error', 'Anda hanya dapat menyelesaikan tiket yang sedang Anda tangani.');
        }

        DB::transaction(function () use ($ticket) {
            $ticket->update([
                'status' => 'selesai',
            ]);

            RiwayatTiket::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'aksi' => 'selesai',
            ]);
        });

        return redirect()->route('staf.tiket-saya')->with('success', "Tiket #{$ticket->id} berhasil diselesaikan.");
    }

    /**
     * Tampilan Ruang Kerja Detail Tiket & Chat WhatsApp
     */
    public function chat(Ticket $ticket)
    {
        $ticket->load([
            'kategori',
            'user.bidangStaf',
            'riwayat' => fn($q) => $q->with('user')->latest(),
            'lampiran',
            'waLogs',
        ]);

        $workingMinutes = SlaService::getWorkingMinutesElapsed($ticket->assigned_at);
        $hoursHeld = round($workingMinutes / 60, 1);
        $isPastSla = SlaService::isPastSla($ticket->assigned_at);

        $backRoute = $ticket->user_id === Auth::id()
            ? route('staf.tiket-saya')
            : route('staf.dashboard');
        $backLabel = $ticket->user_id === Auth::id()
            ? 'Kembali ke Tiket Saya'
            : 'Kembali ke Antrean';

        return view('tickets.chat', [
            'ticket' => $ticket,
            'workingMinutes' => $workingMinutes,
            'hoursHeld' => $hoursHeld,
            'isPastSla' => $isPastSla,
            'backRoute' => $backRoute,
            'backLabel' => $backLabel,
            'sendRoute' => route('staf.tickets.chat.send', $ticket->id),
            'finishRoute' => route('staf.tickets.selesai', $ticket->id),
            'userRole' => 'staf_bidang',
        ]);
    }

    /**
     * Kirim Pesan Balasan Chat WhatsApp
     */
    public function kirimPesan(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        WaLog::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'pegawai',
            'message' => trim($request->message),
        ]);

        return redirect()->route('staf.tickets.chat', $ticket->id)->with('success', 'Pesan balasan berhasil terkirim ke WhatsApp pemohon.');
    }
}
