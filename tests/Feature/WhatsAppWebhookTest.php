<?php

namespace Tests\Feature;

use App\Models\KategoriLayanan;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WaLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed kategori layanan standar Format.md
        KategoriLayanan::create([
            'nama_layanan' => 'Reset password email kalbarprov',
            'status_aktif' => true,
        ]);
        KategoriLayanan::create([
            'nama_layanan' => 'Gangguan akses internet',
            'status_aktif' => true,
        ]);
        KategoriLayanan::create([
            'nama_layanan' => 'Kendala absensi',
            'status_aktif' => true,
        ]);
        KategoriLayanan::create([
            'nama_layanan' => 'Kendala aplikasi e-aktivity',
            'status_aktif' => true,
        ]);
    }

    public function test_webhook_rejects_empty_payload(): void
    {
        $response = $this->postJson('/api/webhook/whatsapp', []);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => false,
        ]);
    }

    public function test_webhook_sends_catalog_and_format_when_message_format_is_invalid(): void
    {
        Http::fake();

        $response = $this->postJson('/api/webhook/whatsapp', [
            'sender' => '081299998888',
            'message' => 'Halo admin, mau tanya syarat reset password bagaimana ya?',
            'name' => 'Ahmad',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'action' => 'catalog_sent',
        ]);

        $this->assertStringContainsString('Katalog layanan :', $response->json('reply'));
        $this->assertStringContainsString('Format keluhan :', $response->json('reply'));
        $this->assertStringContainsString('Reset password email kalbarprov', $response->json('reply'));

        // Pastikan tidak ada tiket yang dibuat
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_webhook_automatically_creates_ticket_when_format_is_valid(): void
    {
        Http::fake();

        $validComplaint = "Format keluhan :\n"
            . "Nama : Ahmad Fauzi\n"
            . "Jabatan : Pranata Komputer\n"
            . "OPD : Dinas Kesehatan\n"
            . "Nomor Katalog : 2\n"
            . "Detail Pengaduan : Jaringan internet di ruang verifikasi tidak bisa terhubung sejak jam 08.30.";

        $response = $this->postJson('/api/webhook/whatsapp', [
            'sender' => '6281234567890',
            'message' => $validComplaint,
            'name' => 'Ahmad Fauzi',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => true,
            'action' => 'ticket_created',
        ]);

        $ticketId = $response->json('ticket_id');
        $this->assertNotNull($ticketId);

        // Verifikasi data tiket di database
        $this->assertDatabaseHas('tickets', [
            'id' => $ticketId,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Ahmad Fauzi (Dinas Kesehatan)',
            'status' => 'open',
            'user_id' => null,
        ]);

        // Verifikasi jejak audit pembuatan tiket
        $this->assertDatabaseHas('riwayat_tiket', [
            'ticket_id' => $ticketId,
            'aksi' => 'dibuat',
            'user_id' => null,
        ]);

        // Verifikasi log pesan awal pemohon masuk ke wa_logs
        $this->assertDatabaseHas('wa_logs', [
            'ticket_id' => $ticketId,
            'sender_type' => 'pemohon',
            'message' => $validComplaint,
        ]);

        // Verifikasi balasan konfirmasi tiket berisi nomor tiket
        $this->assertStringContainsString("#{$ticketId}", $response->json('reply'));
        $this->assertStringContainsString('Gangguan akses internet', $response->json('reply'));
    }

    public function test_webhook_appends_chat_to_existing_active_ticket(): void
    {
        Http::fake();

        // 1. Buat tiket aktif milik pemohon
        $ticket = Ticket::create([
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Ahmad Fauzi (Dinas Kesehatan)',
            'keluhan' => 'Jaringan internet error',
            'id_kategori_api' => 2,
            'status' => 'progress',
            'assigned_at' => now(),
        ]);

        // 2. Pemohon mengirim pesan baru via WhatsApp
        $followUpMessage = 'Pak, apakah teknisinya sudah jalan ke lokasi? Kami butuh segera.';
        $response = $this->postJson('/api/webhook/whatsapp', [
            'sender' => '081234567890',
            'message' => $followUpMessage,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'action' => 'chat_appended',
            'ticket_id' => $ticket->id,
        ]);

        // Pastikan tiket tidak bertambah (tetap 1)
        $this->assertDatabaseCount('tickets', 1);

        // Pastikan pesan pemohon tersimpan di wa_logs pada tiket yang sama
        $this->assertDatabaseHas('wa_logs', [
            'ticket_id' => $ticket->id,
            'sender_type' => 'pemohon',
            'message' => $followUpMessage,
        ]);
    }

    public function test_officer_reply_in_chat_triggers_foonte_outgoing_message(): void
    {
        config(['services.fonnte.token' => 'dummy_test_token']);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true, 'id' => '123'], 200),
        ]);

        $ticket = Ticket::create([
            'nomor_wa' => '081299998888',
            'nama_pemohon' => 'Siti Aminah',
            'keluhan' => 'Lupa password akun',
            'id_kategori_api' => 1,
            'status' => 'progress',
            'assigned_at' => now(),
        ]);

        // Simulasikan petugas membalas chat dari dashboard (menghasilkan record wa_logs sender_type = pegawai)
        WaLog::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'pegawai',
            'message' => 'Baik bu, password sudah di-reset menjadi Kalbar@2026.',
        ]);

        // Verifikasi Http client memanggil Foonte API dengan nomor pemohon dan isi pesan balasan
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request['target'] === '081299998888'
                && str_contains($request['message'], 'Kalbar@2026');
        });
    }

    public function test_fallback_route_without_api_prefix_also_works(): void
    {
        Http::fake();

        $response = $this->postJson('/webhook/whatsapp', [
            'sender' => '081211112222',
            'message' => 'Tes ping webhook',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'action' => 'catalog_sent',
        ]);
    }
}
