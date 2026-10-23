<?php

namespace Tests\Feature;

use App\Models\BidangStaf;
use App\Models\KategoriLayanan;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WaLog;
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

    public function test_frontdesk_can_send_chat_reply(): void
    {
        $response = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.chat.send', $this->ticket), [
                'message' => 'Pesan diterima, tiket sedang kami triase.',
            ]);

        $response->assertRedirect(route('frontdesk.tickets.chat', $this->ticket));

        $this->assertDatabaseHas('wa_logs', [
            'ticket_id' => $this->ticket->id,
            'sender_type' => 'pegawai',
            'message' => 'Pesan diterima, tiket sedang kami triase.',
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
}
