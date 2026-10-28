<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\WaLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FoonteService
{
    protected string $token;
    protected string $url;

    public function __construct()
    {
        $this->token = (string) config('services.fonnte.token', '');
        $this->url = (string) config('services.fonnte.url', 'https://api.fonnte.com/send');
    }

    /**
     * Mengirim pesan teks WhatsApp ke nomor target melalui API Foonte
     *
     * @param string $target Nomor WhatsApp tujuan (format 08... atau 62...)
     * @param string $message Isi pesan yang akan dikirim
     * @param string|null $inboxid ID pesan asli jika ingin membalas dalam bentuk quote chat
     * @return array
     */
    public function sendMessage(string $target, string $message, ?string $inboxid = null): array
    {
        // Bersihkan karakter non-numerik pada target
        $cleanTarget = preg_replace('/[^0-9]/', '', $target);

        if (empty($cleanTarget) || empty(trim($message))) {
            return [
                'status' => false,
                'message' => 'Nomor target atau pesan tidak boleh kosong.',
            ];
        }

        $payload = [
            'target' => $cleanTarget,
            'message' => $message,
        ];

        if (!empty($inboxid)) {
            $payload['inboxid'] = $inboxid;
        }

        // Jika token belum diatur (misal saat testing awal), catat log simulasi tanpa error
        if (empty($this->token)) {
            Log::info("Foonte Token belum dikonfigurasi. Simulasi kirim pesan ke {$cleanTarget}: {$message}");
            return [
                'status' => true,
                'simulated' => true,
                'message' => 'Simulasi kirim berhasil (Foonte token belum diatur di .env).',
                'target' => $cleanTarget,
            ];
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => $this->token,
                ])
                ->asForm()
                ->post($this->url, $payload);

            $data = $response->json() ?? [];

            if ($response->successful()) {
                Log::info("Berhasil mengirim pesan WA ke {$cleanTarget} via Foonte.");
                return [
                    'status' => true,
                    'data' => $data,
                ];
            }

            Log::warning("Foonte API merespons dengan status {$response->status()}: " . $response->body());
            return [
                'status' => false,
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error("Koneksi ke Foonte API gagal: " . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mengirim katalog layanan dan template format keluhan kepada pemohon
     */
    public function sendCatalogAndFormat(string $target, ?string $name = null, ?string $inboxid = null): string
    {
        $salam = $name ? "Halo {$name}," : "Halo,";

        $message = "{$salam} Terima kasih telah menghubungi Layanan Helpdesk Diskominfo Provinsi Kalimantan Barat.\n\n"
            . "Format laporan pengaduan yang Anda kirimkan belum sesuai aturan. Silakan sampaikan keluhan Anda dengan mengisi format resmi berikut:\n\n"
            . "*Katalog layanan :*\n"
            . "1. Reset password email kalbarprov\n"
            . "2. Gangguan akses internet\n"
            . "3. Kendala absensi\n"
            . "4. Knedala aplikasi e-aktivity\n\n"
            . "*Format keluhan :*\n"
            . "Nama :\n"
            . "Jabatan :\n"
            . "OPD :\n"
            . "Nomor Katalog :\n"
            . "Detail Pengaduan :\n\n"
            . "*Contoh Pengisian:*\n"
            . "Nama : Budi Santoso\n"
            . "Jabatan : Pranata Komputer\n"
            . "OPD : Dinas Kominfo\n"
            . "Nomor Katalog : 2\n"
            . "Detail Pengaduan : Koneksi internet di ruang server tidak dapat terhubung sejak pagi.\n\n"
            . "_Silakan salin format di atas dan kirimkan kembali agar tiket Anda dapat diproses secara otomatis oleh sistem._";

        $this->sendMessage($target, $message, $inboxid);

        return $message;
    }

    /**
     * Mengirim notifikasi konfirmasi bahwa tiket berhasil dibuat ke WhatsApp pemohon
     */
    public function sendTicketCreatedNotification(string $target, Ticket $ticket, ?string $inboxid = null): string
    {
        $kategoriNama = $ticket->kategori->nama_layanan ?? 'Umum';

        $message = "Terima kasih, laporan pengaduan Anda telah kami terima dan tiket resmi telah berhasil dibuat.\n\n"
            . "*Detail Tiket Anda:*\n"
            . "• *Nomor Tiket:* #{$ticket->id}\n"
            . "• *Nama Pemohon:* {$ticket->nama_pemohon}\n"
            . "• *Kategori Layanan:* {$kategoriNama}\n"
            . "• *Status Tiket:* Menunggu Penugasan (Open)\n\n"
            . "Petugas Helpdesk kami akan segera menindaklanjuti keluhan Anda. Anda dapat langsung membalas pesan ini kapan saja untuk berkomunikasi dengan petugas.";

        $this->sendMessage($target, $message, $inboxid);

        return $message;
    }

    /**
     * Mengirimkan pesan balasan petugas helpdesk dari dashboard ke WhatsApp pemohon
     */
    public function sendOfficerReply(WaLog $waLog): ?array
    {
        $ticket = $waLog->ticket;
        if (!$ticket || empty($ticket->nomor_wa)) {
            return null;
        }

        $officerMessage = "Halo {$ticket->nama_pemohon},\n\n"
            . "[Tanggapan Petugas Helpdesk Tiket #{$ticket->id}]:\n"
            . "{$waLog->message}";

        return $this->sendMessage($ticket->nomor_wa, $officerMessage);
    }
}
