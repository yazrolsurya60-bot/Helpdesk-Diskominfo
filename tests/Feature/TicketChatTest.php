<?php

namespace Tests\Feature;

use App\Models\BidangStaf;
use App\Models\KategoriLayanan;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WaLog;
use App\Models\LampiranTiket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $frontDeskUser;
    protected User $stafUser;
    protected KategoriLayanan $kategori;
    protected Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $bidang = BidangStaf::create([
            'nama_bidang' => 'Bidang Aplikasi & Informatika',
        ]);

        $this->frontDeskUser = User::factory()->create([
            'role' => 'front_desk',
            'email' => 'frontdesk@pintas.com',
        ]);

        $this->stafUser = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => 'staf_bidang',
            'bidang_id' => $bidang->id,
            'email' => 'budi@pintas.com',
        ]);

        $this->kategori = KategoriLayanan::create([
            'nama_layanan' => 'Perangkat Lunak',
            'status_aktif' => true,
        ]);

        $this->ticket = Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Pratama',
            'keluhan' => 'Koneksi aplikasi error tidak bisa login',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'progress',
            'assigned_at' => now(),
        ]);
    }

    public function test_staf_can_access_ticket_chat_page(): void
    {
        $response = $this->actingAs($this->stafUser)
            ->get(route('staf.tickets.chat', $this->ticket));

        $response->assertStatus(200);
        $response->assertSee('Informasi &amp; Kontrol Tiket', false);
        $response->assertSee('Andi Pratama');
        $response->assertSee('Koneksi aplikasi error tidak bisa login');
        $response->assertSee('WhatsApp Gateway Helpdesk');
    }

    public function test_staf_can_send_chat_reply(): void
    {
        $response = $this->actingAs($this->stafUser)
            ->post(route('staf.tickets.chat.send', $this->ticket), [
                'message' => 'Baik pak, tim teknisi sedang melakukan pengecekan server.',
            ]);

        $response->assertRedirect(route('staf.tickets.chat', $this->ticket));

        $this->assertDatabaseHas('wa_logs', [
            'ticket_id' => $this->ticket->id,
            'sender_type' => 'pegawai',
            'message' => 'Baik pak, tim teknisi sedang melakukan pengecekan server.',
        ]);
    }

    public function test_frontdesk_can_access_ticket_chat_page(): void
    {
        $response = $this->actingAs($this->frontDeskUser)
            ->get(route('frontdesk.tickets.chat', $this->ticket));

        $response->assertStatus(200);
        $response->assertSee('Informasi &amp; Kontrol Tiket', false);
        $response->assertSee('Andi Pratama');
    }

    public function test_frontdesk_can_send_chat_reply_when_handling_own_ticket(): void
    {
        $fdTicket = Ticket::create([
            'user_id' => $this->frontDeskUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Pratama',
            'keluhan' => 'Koneksi aplikasi error tidak bisa login',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'progress',
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.chat.send', $fdTicket), [
                'message' => 'Pesan diterima, tiket sedang kami tangani di ruang kerja.',
            ]);

        $response->assertRedirect(route('frontdesk.tickets.chat', $fdTicket));

        $this->assertDatabaseHas('wa_logs', [
            'ticket_id' => $fdTicket->id,
            'sender_type' => 'pegawai',
            'message' => 'Pesan diterima, tiket sedang kami tangani di ruang kerja.',
        ]);
    }

    public function test_staf_viewing_open_ticket_sees_correct_back_button(): void
    {
        $openTicket = Ticket::create([
            'user_id' => null,
            'nomor_wa' => '089988776655',
            'nama_pemohon' => 'Budi Santoso (Dinas)',
            'keluhan' => 'Jaringan internet lantai 2 padam',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->stafUser)
            ->get(route('staf.tickets.chat', $openTicket));

        $response->assertStatus(200);
        $response->assertSee('Kembali ke Antrean');
        $response->assertSee('Tiket berstatus Open di antrean umum');
    }

    public function test_table_rows_contain_clickable_chat_route(): void
    {
        // Staf - Tiket Saya view
        $responseStaf = $this->actingAs($this->stafUser)
            ->get(route('staf.tiket-saya'));
        $responseStaf->assertStatus(200);
        $responseStaf->assertSee("window.location='" . route('staf.tickets.chat', $this->ticket->id) . "'", false);
        $responseStaf->assertSee('event.stopPropagation()', false);

        // Front Desk - Antrean Masuk view
        $openTicket = Ticket::create([
            'user_id' => null,
            'nomor_wa' => '089988776655',
            'nama_pemohon' => 'Pelapor Umum',
            'keluhan' => 'Masalah teknis',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'open',
        ]);

        $responseFd = $this->actingAs($this->frontDeskUser)
            ->get(route('frontdesk.index'));
        $responseFd->assertStatus(200);
        $responseFd->assertSee("window.location='" . route('frontdesk.tickets.chat', $openTicket->id) . "'", false);
        $responseFd->assertSee('event.stopPropagation()', false);
    }

    public function test_frontdesk_viewing_completed_ticket_sees_back_to_dashboard_button(): void
    {
        $completedTicket = Ticket::create([
            'user_id' => $this->frontDeskUser->id,
            'nomor_wa' => '081299998888',
            'nama_pemohon' => 'Siti Aminah',
            'keluhan' => 'Konsultasi selesai',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->frontDeskUser)
            ->get(route('frontdesk.tickets.chat', $completedTicket));

        $response->assertStatus(200);
        $response->assertSee(route('frontdesk.index'));
        $response->assertSee('Kembali ke Dashboard');
        $response->assertDontSee('Kembali ke Ruang Kerja');
    }

    public function test_frontdesk_viewing_own_progress_ticket_sees_back_to_ruang_kerja_button(): void
    {
        $progressTicket = Ticket::create([
            'user_id' => $this->frontDeskUser->id,
            'nomor_wa' => '081299998888',
            'nama_pemohon' => 'Siti Aminah',
            'keluhan' => 'Tiket sedang diproses',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'progress',
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($this->frontDeskUser)
            ->get(route('frontdesk.tickets.chat', $progressTicket));

        $response->assertStatus(200);
        $response->assertSee(route('frontdesk.ruang-kerja'));
        $response->assertSee('Kembali ke Ruang Kerja');
    }

    public function test_frontdesk_viewing_staf_progress_ticket_sees_back_to_monitoring_button(): void
    {
        $response = $this->actingAs($this->frontDeskUser)
            ->get(route('frontdesk.tickets.chat', $this->ticket));

        $response->assertStatus(200);
        $response->assertSee(route('frontdesk.monitoring'));
        $response->assertSee('Kembali ke Monitoring');
    }

    public function test_staf_viewing_completed_ticket_sees_back_to_dashboard_button(): void
    {
        $completedTicket = Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081299997777',
            'nama_pemohon' => 'Rian Hidayat',
            'keluhan' => 'Perbaikan laptop sudah selesai',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->stafUser)
            ->get(route('staf.tickets.chat', $completedTicket));

        $response->assertStatus(200);
        $response->assertSee(route('staf.dashboard'));
        $response->assertSee('Kembali ke Dashboard');
        $response->assertDontSee('Kembali ke Tiket Saya');
    }

    public function test_staf_can_send_chat_with_uploaded_photo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('bukti_perbaikan_server.jpg', 640, 480);

        $response = $this->actingAs($this->stafUser)
            ->post(route('staf.tickets.chat.send', $this->ticket), [
                'message' => 'Berikut foto kabel server setelah diperbaiki.',
                'lampiran' => $file,
            ]);

        $response->assertRedirect(route('staf.tickets.chat', $this->ticket));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lampiran_tiket', [
            'ticket_id' => $this->ticket->id,
        ]);

        $lampiran = LampiranTiket::where('ticket_id', $this->ticket->id)->first();
        $this->assertNotNull($lampiran);
        Storage::disk('public')->assertExists($lampiran->file_path);

        $this->assertDatabaseHas('wa_logs', [
            'ticket_id' => $this->ticket->id,
            'sender_type' => 'pegawai',
        ]);
    }

    public function test_frontdesk_can_send_chat_with_uploaded_photo(): void
    {
        Storage::fake('public');

        $fdTicket = Ticket::create([
            'user_id' => $this->frontDeskUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Pratama',
            'keluhan' => 'Koneksi aplikasi error tidak bisa login',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'progress',
            'assigned_at' => now(),
        ]);

        $file = UploadedFile::fake()->image('foto_kondisi.png', 400, 300);

        $response = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.chat.send', $fdTicket), [
                'message' => 'Laporan foto fisik perangkat.',
                'lampiran' => $file,
            ]);

        $response->assertRedirect(route('frontdesk.tickets.chat', $fdTicket));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lampiran_tiket', [
            'ticket_id' => $fdTicket->id,
        ]);
    }

    public function test_upload_rejects_non_image_files_and_files_exceeding_5mb(): void
    {
        Storage::fake('public');

        // 1. Dokumen PDF harus ditolak (khusus gambar)
        $pdfFile = UploadedFile::fake()->create('dokumen.pdf', 1024, 'application/pdf');

        $responsePdf = $this->actingAs($this->stafUser)
            ->post(route('staf.tickets.chat.send', $this->ticket), [
                'message' => 'Lampiran PDF.',
                'lampiran' => $pdfFile,
            ]);

        $responsePdf->assertSessionHasErrors('lampiran');

        // 2. Foto melebihi 5 MB (6000 KB) harus ditolak
        $largePhoto = UploadedFile::fake()->create('foto_raksasa.jpg', 6000, 'image/jpeg');

        $responseLarge = $this->actingAs($this->stafUser)
            ->post(route('staf.tickets.chat.send', $this->ticket), [
                'message' => 'Foto terlalu besar.',
                'lampiran' => $largePhoto,
            ]);

        $responseLarge->assertSessionHasErrors('lampiran');
    }

    public function test_user_can_download_uploaded_lampiran_photo(): void
    {
        Storage::fake('public');

        $filePath = 'lampiran_tiket/test_image.jpg';
        Storage::disk('public')->put($filePath, 'fake image content');

        $lampiran = LampiranTiket::create([
            'ticket_id' => $this->ticket->id,
            'file_path' => $filePath,
        ]);

        $response = $this->actingAs($this->stafUser)
            ->get(route('staf.tickets.lampiran.download', [
                'ticket' => $this->ticket->id,
                'lampiran' => $lampiran->id,
            ]));

        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('content-disposition'));
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('test_image.jpg', $response->headers->get('content-disposition'));
    }

    public function test_unauthorized_download_attempt_for_different_ticket_is_aborted(): void
    {
        Storage::fake('public');

        $otherTicket = Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081211112222',
            'nama_pemohon' => 'Tiket Lain',
            'keluhan' => 'Kendala lain',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'progress',
        ]);

        $filePath = 'lampiran_tiket/other_image.png';
        Storage::disk('public')->put($filePath, 'fake image content');

        $lampiranOther = LampiranTiket::create([
            'ticket_id' => $otherTicket->id,
            'file_path' => $filePath,
        ]);

        // Mencoba mengakses lampiran milik otherTicket menggunakan id ticket pertama -> harus 403
        $response = $this->actingAs($this->stafUser)
            ->get(route('staf.tickets.lampiran.download', [
                'ticket' => $this->ticket->id,
                'lampiran' => $lampiranOther->id,
            ]));

        $response->assertStatus(403);
    }

    public function test_chat_sidebar_lampiran_section_auto_hides_when_no_photos_exist(): void
    {
        // 1. Tiket tanpa lampiran foto -> Seksi 4 tidak ditampilkan (Pilihan 1: Auto-Hide)
        $responseEmpty = $this->actingAs($this->stafUser)
            ->get(route('staf.tickets.chat', $this->ticket));

        $responseEmpty->assertStatus(200);
        $responseEmpty->assertDontSee('Lampiran Foto');

        // 2. Tiket dengan lampiran foto -> Seksi 4 muncul otomatis
        Storage::fake('public');
        $filePath = 'lampiran_tiket/foto_muncul.jpg';
        Storage::disk('public')->put($filePath, 'fake content');

        LampiranTiket::create([
            'ticket_id' => $this->ticket->id,
            'file_path' => $filePath,
        ]);

        $responseWithPhoto = $this->actingAs($this->stafUser)
            ->get(route('staf.tickets.chat', $this->ticket));

        $responseWithPhoto->assertStatus(200);
        $responseWithPhoto->assertSee('Lampiran Foto (1)');
        $responseWithPhoto->assertSee('foto_muncul.jpg');
        $responseWithPhoto->assertSee('Unduh');
        $responseWithPhoto->assertSee('Lihat');
    }

    public function test_frontdesk_monitoring_staf_ticket_cannot_send_chat_and_sees_seal(): void
    {
        // Tiket ditangani oleh stafUser
        $responseView = $this->actingAs($this->frontDeskUser)
            ->get(route('frontdesk.tickets.chat', $this->ticket));

        $responseView->assertStatus(200);
        $responseView->assertSee('Tiket ini sedang ditangani oleh Budi Santoso');
        $responseView->assertSee('Mode pemantauan (Read-Only)');
        $responseView->assertDontSee('id="chatSendForm"', false);

        // Frontdesk mencoba post langsung -> diblokir oleh guard clause
        $responsePost = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.chat.send', $this->ticket), [
                'message' => 'Pesan paksa dari frontdesk saat monitoring.',
            ]);

        $responsePost->assertRedirect(route('frontdesk.tickets.chat', $this->ticket));
        $responsePost->assertSessionHas('error');

        $this->assertDatabaseMissing('wa_logs', [
            'ticket_id' => $this->ticket->id,
            'message' => 'Pesan paksa dari frontdesk saat monitoring.',
        ]);
    }

    public function test_user_cannot_send_chat_on_open_ticket_and_sees_seal(): void
    {
        $openTicket = Ticket::create([
            'user_id' => null,
            'nomor_wa' => '089988776655',
            'nama_pemohon' => 'Warga Umum',
            'keluhan' => 'Layanan wifi lambat',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'open',
        ]);

        $responseView = $this->actingAs($this->frontDeskUser)
            ->get(route('frontdesk.tickets.chat', $openTicket));

        $responseView->assertStatus(200);
        $responseView->assertSee('Tiket ini masih berstatus Open di antrean umum');
        $responseView->assertDontSee('id="chatSendForm"', false);

        $responsePost = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.chat.send', $openTicket), [
                'message' => 'Mencoba membalas tiket open.',
            ]);

        $responsePost->assertRedirect(route('frontdesk.tickets.chat', $openTicket));
        $responsePost->assertSessionHas('error');

        $this->assertDatabaseMissing('wa_logs', [
            'ticket_id' => $openTicket->id,
            'message' => 'Mencoba membalas tiket open.',
        ]);
    }

    public function test_user_cannot_send_chat_on_completed_ticket_and_sees_seal(): void
    {
        $closedTicket = Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '089988776655',
            'nama_pemohon' => 'Warga Selesai',
            'keluhan' => 'Layanan wifi selesai',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'selesai',
        ]);

        $responseView = $this->actingAs($this->stafUser)
            ->get(route('staf.tickets.chat', $closedTicket));

        $responseView->assertStatus(200);
        $responseView->assertSee('Tiket ini sudah berstatus selesai. Percakapan chat telah diarsipkan.');
        $responseView->assertDontSee('id="chatSendForm"', false);

        $responsePost = $this->actingAs($this->stafUser)
            ->post(route('staf.tickets.chat.send', $closedTicket), [
                'message' => 'Mencoba membalas tiket selesai.',
            ]);

        $responsePost->assertRedirect(route('staf.tickets.chat', $closedTicket));
        $responsePost->assertSessionHas('error');

        $this->assertDatabaseMissing('wa_logs', [
            'ticket_id' => $closedTicket->id,
            'message' => 'Mencoba membalas tiket selesai.',
        ]);
    }

    public function test_another_staf_cannot_send_chat_on_staf_ticket(): void
    {
        $otherStaf = User::factory()->create([
            'name' => 'Staf Lain',
            'role' => 'staf_bidang',
            'email' => 'staflain@pintas.com',
        ]);

        $responsePost = $this->actingAs($otherStaf)
            ->post(route('staf.tickets.chat.send', $this->ticket), [
                'message' => 'Mencoba membalas tiket staf lain.',
            ]);

        $responsePost->assertRedirect(route('staf.tickets.chat', $this->ticket));
        $responsePost->assertSessionHas('error');

        $this->assertDatabaseMissing('wa_logs', [
            'ticket_id' => $this->ticket->id,
            'message' => 'Mencoba membalas tiket staf lain.',
        ]);
    }
}
