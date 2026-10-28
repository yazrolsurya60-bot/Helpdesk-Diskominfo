<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RiwayatTiket;
use App\Models\Ticket;
use App\Models\WaLog;
use App\Services\CatalogResolver;
use App\Services\ComplaintFormatParser;
use App\Services\FoonteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected FoonteService $foonteService
    ) {}

    /**
     * Menerima payload webhook pesan masuk WhatsApp dari Foonte Gateway
     */
    public function handle(Request $request): JsonResponse
    {
        // 1. Ekstrak data dari request Foonte (mendukung JSON dan form-urlencoded)
        $sender = (string) $request->input('sender', '');
        $message = trim((string) $request->input('message', ''));
        $name = $request->input('name');
        $inboxid = $request->input('inboxid');

        Log::info("Webhook Foonte diterima dari {$sender}: {$message}");

        if (empty($sender) || empty($message)) {
            return response()->json([
                'status' => false,
                'message' => 'Parameter sender dan message wajib diisi.',
            ], 400);
        }

        // 2. Normalisasi nomor telepon pengirim
        $cleanNumber = preg_replace('/[^0-9]/', '', $sender);
        $normalizedNumber = str_starts_with($cleanNumber, '62')
            ? '0' . substr($cleanNumber, 2)
            : $cleanNumber;

        // 3. Cek apakah pemohon telah memiliki tiket yang sedang aktif (Open / In Progress)
        $activeTicket = Ticket::where(function ($q) use ($cleanNumber, $normalizedNumber) {
            $q->where('nomor_wa', $normalizedNumber)
                ->orWhere('nomor_wa', $cleanNumber)
                ->orWhere('nomor_wa', 'like', "%{$normalizedNumber}%");
        })
            ->whereIn('status', ['open', 'progress'])
            ->latest()
            ->first();

        // JIKA PEMOHON MEMILIKI TIKET AKTIF:
        // Setiap chat masuk langsung diteruskan ke timeline live chat tiket tersebut
        if ($activeTicket) {
            $waLog = WaLog::create([
                'ticket_id' => $activeTicket->id,
                'sender_type' => 'pemohon',
                'message' => $message,
            ]);

            Log::info("Pesan dari {$sender} berhasil dimasukkan ke riwayat chat tiket #{$activeTicket->id}.");

            return response()->json([
                'status' => true,
                'action' => 'chat_appended',
                'ticket_id' => $activeTicket->id,
                'message' => "Pesan berhasil ditambahkan ke percakapan tiket #{$activeTicket->id}.",
            ]);
        }

        // JIKA PEMOHON BELUM MEMILIKI TIKET AKTIF:
        // Validasi format keluhan pengaduan
        $parsed = ComplaintFormatParser::parse($message);

        // SKENARIO A: Format TIDAK VALID -> Chatbot mengirim katalog layanan & template format
        if ($parsed === null) {
            $reply = $this->foonteService->sendCatalogAndFormat($sender, $name, $inboxid);

            return response()->json([
                'status' => true,
                'action' => 'catalog_sent',
                'reply' => $reply,
                'message' => 'Format pesan tidak sesuai, katalog layanan dan template format keluhan telah dikirimkan ke pemohon.',
            ]);
        }

        // SKENARIO B: Format VALID -> Sistem otomatis membuat tiket baru berstatus Open
        $category = CatalogResolver::resolveCategory($parsed['nomor_katalog']);

        // Format nama pemohon dengan OPD jika tersedia
        $namaPemohon = $parsed['nama'];
        if (!empty($parsed['opd']) && $parsed['opd'] !== '-') {
            $namaPemohon .= " ({$parsed['opd']})";
        }

        // Format keluhan terstruktur untuk disimpan di tiket
        $keluhan = "Jabatan: {$parsed['jabatan']}\n"
            . "OPD: {$parsed['opd']}\n\n"
            . "Detail Pengaduan:\n{$parsed['detail_pengaduan']}";

        $ticket = DB::transaction(function () use ($normalizedNumber, $namaPemohon, $keluhan, $category, $message) {
            $ticket = Ticket::create([
                'user_id' => null,
                'nomor_wa' => $normalizedNumber,
                'nama_pemohon' => $namaPemohon,
                'keluhan' => $keluhan,
                'id_kategori_api' => $category->id,
                'status' => 'open',
                'assigned_at' => null,
            ]);

            // Catat ke jejak audit riwayat tiket
            RiwayatTiket::create([
                'ticket_id' => $ticket->id,
                'user_id' => null, // Dibuat otomatis oleh Sistem
                'aksi' => 'dibuat',
            ]);

            // Rekam pesan awal pemohon ke riwayat chat
            WaLog::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'pemohon',
                'message' => $message,
            ]);

            return $ticket;
        });

        // Kirim balasan konfirmasi tiket ke WhatsApp pemohon
        $confirmationReply = $this->foonteService->sendTicketCreatedNotification($sender, $ticket, $inboxid);

        return response()->json([
            'status' => true,
            'action' => 'ticket_created',
            'ticket_id' => $ticket->id,
            'reply' => $confirmationReply,
            'message' => "Tiket #{$ticket->id} berhasil dibuat otomatis dari WhatsApp.",
        ], 201);
    }
}
