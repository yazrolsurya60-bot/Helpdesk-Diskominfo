<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\KategoriLayanan;
use App\Models\User;
use App\Models\RiwayatTiket;
use App\Models\NotifikasiInternal;
use App\Models\WaLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SlaService;

class FrontdeskController extends Controller
{
    /**
     * Semua Tiket Masuk — Dashboard Utama Front Desk
     * Menampilkan seluruh tiket masuk dengan KPI, filter, dan aksi distribusi/ambil/ubah kategori.
     */
    public function index(Request $request)
    {
        $search = $request->input('q');
        $kategoriId = $request->input('kategori');

        $statTotal = Ticket::count();
        $statOpen = Ticket::where('status', 'open')->count();
        $statClaimedFrontDesk = Ticket::where('status', 'progress')->count();

        $query = Ticket::with(['kategori', 'user']);

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

        // Staf bidang untuk modal distribusi
        $stafBidang = User::where('role', 'staf_bidang')->with('bidangStaf')->get();

        return view('frontdesk.index', compact(
            'statTotal',
            'statOpen',
            'statClaimedFrontDesk',
            'tickets',
            'categories',
            'stafBidang',
            'search',
            'kategoriId'
        ));
    }

    /**
     * Front Desk mengambil tiket untuk ditangani sendiri (Self-Assign/Triage L1)
     */
    public function ambilTiket(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
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

        return redirect()->route('frontdesk.ruang-kerja')
            ->with('success', "Tiket #{$ticket->id} berhasil diambil ke ruang kerja Anda.");
    }

    /**
     * Distribusi tiket ke Staf Bidang tertentu
     */
    public function distribusi(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
        ]);

        DB::transaction(function () use ($request, $ticket) {
            $ticket->update([
                'user_id' => $request->target_user_id,
                'status' => 'progress',
                'assigned_at' => now(),
            ]);

            RiwayatTiket::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'aksi' => 'didistribusikan',
            ]);

            NotifikasiInternal::create([
                'user_id' => $request->target_user_id,
                'judul' => "Tiket #{$ticket->id} telah didistribusikan kepada Anda oleh Front Desk (" . Auth::user()->name . ")",
                'is_read' => false,
            ]);
        });

        return redirect()->route('frontdesk.index')
            ->with('success', "Tiket #{$ticket->id} berhasil didistribusikan ke staf bidang.");
    }

    /**
     * Ubah kategori tiket
     */
    public function ubahKategori(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'id_kategori_api' => 'required|exists:kategori_layanan,id',
        ]);

        DB::transaction(function () use ($request, $ticket) {
            $ticket->update([
                'id_kategori_api' => $request->id_kategori_api,
            ]);

            RiwayatTiket::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'aksi' => 'update_label',
            ]);
        });

        return back()->with('success', "Kategori tiket #{$ticket->id} berhasil diperbarui.");
    }

    /**
     * Ruang Kerja Saya — Tiket yang diambil Front Desk sendiri
     */
    public function ruangKerja(Request $request)
    {
        $search = $request->input('q');
        $kategoriId = $request->input('kategori');

        $statActive = Ticket::where('user_id', Auth::id())
            ->where('status', 'progress')
            ->count();

        $statCompletedToday = Ticket::where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->whereDate('updated_at', today())
            ->count();

        $query = Ticket::with(['kategori'])
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

        if ($kategoriId) {
            $query->where('id_kategori_api', $kategoriId);
        }

        $myTickets = $query->orderBy('assigned_at', 'desc')->paginate(10)->withQueryString();
        $categories = KategoriLayanan::where('status_aktif', true)->get();

        return view('frontdesk.ruang-kerja', compact(
            'statActive',
            'statCompletedToday',
            'myTickets',
            'categories',
            'search',
            'kategoriId'
        ));
    }

    /**
     * Selesaikan tiket dari ruang kerja Front Desk
     */
    public function selesai(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
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

        return redirect()->route('frontdesk.ruang-kerja')
            ->with('success', "Tiket #{$ticket->id} berhasil diselesaikan.");
    }

    /**
     * Monitoring Staf Bidang & Tarik Tiket
     * Pantau beban kerja staf bidang, lacak durasi tiket, dan tarik kembali tiket terabaikan.
     */
    public function monitoring(Request $request)
    {
        $search = $request->input('q');
        $bidangFilter = $request->input('bidang');

        // KPI Cards
        $statTiketDiTanganStaf = Ticket::where('status', 'progress')
            ->whereHas('user', fn($q) => $q->where('role', 'staf_bidang'))
            ->count();

        // Tiket > 2 jam kerja di tangan staf (melebihi batas SLA kerja aktif)
        $statMendekatiBatasSla = Ticket::where('status', 'progress')
            ->whereHas('user', fn($q) => $q->where('role', 'staf_bidang'))
            ->where('assigned_at', '<=', now()->subHours(2))
            ->get()
            ->filter(fn($ticket) => SlaService::isPastSla($ticket->assigned_at))
            ->count();

        $statDitarikHariIni = RiwayatTiket::whereIn('aksi', ['ditarik_manual', 'dialihkan'])
            ->whereDate('created_at', today())
            ->whereHas('user', fn($q) => $q->where('role', 'front_desk'))
            ->count();

        // Query tiket yang sedang di-handle oleh staf bidang
        $query = Ticket::with(['kategori', 'user.bidangStaf'])
            ->where('status', 'progress')
            ->whereHas('user', fn($q) => $q->where('role', 'staf_bidang'));

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('keluhan', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($bidangFilter) {
            $query->whereHas('user', fn($q) => $q->where('bidang_id', $bidangFilter));
        }

        $ticketsMonitoring = $query->orderBy('assigned_at', 'asc')->paginate(10)->withQueryString();
        $bidangList = \App\Models\BidangStaf::all();

        return view('frontdesk.monitoring', compact(
            'statTiketDiTanganStaf',
            'statMendekatiBatasSla',
            'statDitarikHariIni',
            'ticketsMonitoring',
            'bidangList',
            'search',
            'bidangFilter'
        ));
    }

    /**
     * Tarik tiket kembali dari staf bidang ke antrean open
     */
    public function tarikTiket(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        $previousUserId = $ticket->user_id;

        DB::transaction(function () use ($request, $ticket, $previousUserId) {
            $ticket->update([
                'user_id' => null,
                'status' => 'open',
                'assigned_at' => null,
            ]);

            RiwayatTiket::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'aksi' => 'ditarik_manual',
            ]);

            // Notify staf yang tiketnya ditarik
            if ($previousUserId) {
                NotifikasiInternal::create([
                    'user_id' => $previousUserId,
                    'judul' => "Tiket #{$ticket->id} telah ditarik kembali oleh Front Desk. Alasan: {$request->alasan}",
                    'is_read' => false,
                ]);
            }
        });

        return redirect()->route('frontdesk.monitoring')
            ->with('success', "Tiket #{$ticket->id} berhasil ditarik kembali ke antrean utama.");
    }

    /**
     * Tampilan Ruang Kerja Detail Tiket & Chat WhatsApp (Front Desk)
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

        // Tentukan link kembali: jika dipegang sendiri arahkan ke ruang-kerja, jika masih open ke index
        $backRoute = $ticket->user_id === Auth::id()
            ? route('frontdesk.ruang-kerja')
            : ($ticket->status === 'open' ? route('frontdesk.index') : route('frontdesk.monitoring'));
        $backLabel = $ticket->user_id === Auth::id()
            ? 'Kembali ke Ruang Kerja'
            : ($ticket->status === 'open' ? 'Kembali ke Dashboard' : 'Kembali ke Monitoring');

        return view('tickets.chat', [
            'ticket' => $ticket,
            'workingMinutes' => $workingMinutes,
            'hoursHeld' => $hoursHeld,
            'isPastSla' => $isPastSla,
            'backRoute' => $backRoute,
            'backLabel' => $backLabel,
            'sendRoute' => route('frontdesk.tickets.chat.send', $ticket->id),
            'finishRoute' => route('frontdesk.tickets.selesai', $ticket->id),
            'userRole' => 'front_desk',
        ]);
    }

    /**
     * Kirim Pesan Balasan Chat WhatsApp (Front Desk)
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

        return redirect()->route('frontdesk.tickets.chat', $ticket->id)->with('success', 'Pesan balasan berhasil terkirim ke WhatsApp pemohon.');
    }
}
