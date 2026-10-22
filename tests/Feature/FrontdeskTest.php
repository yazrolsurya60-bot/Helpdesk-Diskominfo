<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Models\KategoriLayanan;
use App\Models\BidangStaf;

class FrontdeskTest extends TestCase
{
    use RefreshDatabase;

    protected User $frontDeskUser;
    protected User $stafUser;
    protected KategoriLayanan $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $bidang = BidangStaf::create([
            'nama_bidang' => 'Bidang Jaringan & Infrastruktur',
        ]);

        $this->frontDeskUser = User::factory()->create([
            'role' => 'front_desk',
            'email' => 'frontdesk@pintas.com',
        ]);

        $this->stafUser = User::factory()->create([
            'role' => 'staf_bidang',
            'bidang_id' => $bidang->id,
            'email' => 'staf@pintas.com',
        ]);

        $this->kategori = KategoriLayanan::create([
            'nama_layanan' => 'Jaringan & Internet',
            'status_aktif' => true,
        ]);
    }

    public function test_frontdesk_dashboard_can_be_accessed(): void
    {
        $response = $this->actingAs($this->frontDeskUser)->get(route('frontdesk.index'));
        $response->assertStatus(200);
        $response->assertSee('Semua Tiket Masuk');
    }

    public function test_frontdesk_ruang_kerja_can_be_accessed(): void
    {
        $response = $this->actingAs($this->frontDeskUser)->get(route('frontdesk.ruang-kerja'));
        $response->assertStatus(200);
        $response->assertSee('Tiket Saya');
    }

    public function test_frontdesk_monitoring_can_be_accessed(): void
    {
        $response = $this->actingAs($this->frontDeskUser)->get(route('frontdesk.monitoring'));
        $response->assertStatus(200);
        $response->assertSee('Monitoring Staf Bidang');
    }

    public function test_frontdesk_can_take_ticket(): void
    {
        $ticket = Ticket::create([
            'nama_pemohon' => 'Budi Sudarsono',
            'nomor_wa' => '08123456789',
            'keluhan' => 'Koneksi internet putus',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.ambil', $ticket));

        $response->assertRedirect(route('frontdesk.ruang-kerja'));
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'user_id' => $this->frontDeskUser->id,
            'status' => 'progress',
        ]);
    }

    public function test_frontdesk_can_distribute_ticket_to_staff(): void
    {
        $ticket = Ticket::create([
            'nama_pemohon' => 'Siti Aminah',
            'nomor_wa' => '08129876543',
            'keluhan' => 'Server email error',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.distribusi', $ticket), [
                'target_user_id' => $this->stafUser->id,
            ]);

        $response->assertRedirect(route('frontdesk.index'));
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'user_id' => $this->stafUser->id,
            'status' => 'progress',
        ]);
        $this->assertDatabaseHas('riwayat_tiket', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->frontDeskUser->id,
            'aksi' => 'didistribusikan',
        ]);
    }

    public function test_frontdesk_can_change_ticket_category(): void
    {
        $newKategori = KategoriLayanan::create([
            'nama_layanan' => 'Hardware & Komputer',
            'status_aktif' => true,
        ]);

        $ticket = Ticket::create([
            'nama_pemohon' => 'Ahmad Dani',
            'nomor_wa' => '08121111222',
            'keluhan' => 'Monitor blank',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.ubah-kategori', $ticket), [
                'id_kategori_api' => $newKategori->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'id_kategori_api' => $newKategori->id,
        ]);
    }

    public function test_frontdesk_can_resolve_self_assigned_ticket(): void
    {
        $ticket = Ticket::create([
            'nama_pemohon' => 'Doni Monardo',
            'nomor_wa' => '08123333444',
            'keluhan' => 'Reset password email',
            'id_kategori_api' => $this->kategori->id,
            'user_id' => $this->frontDeskUser->id,
            'status' => 'progress',
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.selesai', $ticket));

        $response->assertRedirect(route('frontdesk.ruang-kerja'));
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'selesai',
        ]);
    }

    public function test_frontdesk_can_recall_abandoned_ticket_from_staff(): void
    {
        $ticket = Ticket::create([
            'nama_pemohon' => 'Rina Gunawan',
            'nomor_wa' => '08125555666',
            'keluhan' => 'Kabel LAN lepas di ruang rapat',
            'id_kategori_api' => $this->kategori->id,
            'user_id' => $this->stafUser->id,
            'status' => 'progress',
            'assigned_at' => now()->subHours(3),
        ]);

        $response = $this->actingAs($this->frontDeskUser)
            ->post(route('frontdesk.tickets.tarik', $ticket), [
                'alasan' => 'Terabaikan > 2 Jam tanpa catatan progres lapangan',
            ]);

        $response->assertRedirect(route('frontdesk.monitoring'));
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'user_id' => null,
            'status' => 'open',
        ]);
        $this->assertDatabaseHas('riwayat_tiket', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->frontDeskUser->id,
            'aksi' => 'ditarik_manual',
        ]);
    }
}
