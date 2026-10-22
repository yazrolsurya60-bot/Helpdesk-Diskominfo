<?php

namespace Tests\Feature;

use App\Models\BidangStaf;
use App\Models\KategoriLayanan;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StafDisposisiTest extends TestCase
{
    use RefreshDatabase;

    protected User $stafPengirim;
    protected User $stafPenerima;
    protected KategoriLayanan $kategoriLama;
    protected KategoriLayanan $kategoriBaru;
    protected Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $bidangJaringan = BidangStaf::create(['nama_bidang' => 'Bidang Jaringan & Komunikasi']);
        $bidangAplikasi = BidangStaf::create(['nama_bidang' => 'Bidang Aplikasi Informatika']);

        $this->stafPengirim = User::factory()->create([
            'name' => 'Bambang Jaringan',
            'role' => 'staf_bidang',
            'bidang_id' => $bidangJaringan->id,
            'email' => 'bambang@pintas.com',
        ]);

        $this->stafPenerima = User::factory()->create([
            'name' => 'Dedi Aplikasi',
            'role' => 'staf_bidang',
            'bidang_id' => $bidangAplikasi->id,
            'email' => 'dedi@pintas.com',
        ]);

        $this->kategoriLama = KategoriLayanan::create([
            'nama_layanan' => 'Jaringan & Server',
            'status_aktif' => true,
        ]);

        $this->kategoriBaru = KategoriLayanan::create([
            'nama_layanan' => 'Perangkat Lunak',
            'status_aktif' => true,
        ]);

        $this->ticket = Ticket::create([
            'user_id' => $this->stafPengirim->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Budi Santoso',
            'keluhan' => 'Aplikasi SIPD error koneksi basis data',
            'id_kategori_api' => $this->kategoriLama->id,
            'status' => 'progress',
            'assigned_at' => now(),
        ]);
    }

    public function test_staf_can_disposisi_ticket_directly_to_another_staff(): void
    {
        $response = $this->actingAs($this->stafPengirim)
            ->post(route('staf.tickets.disposisi', $this->ticket), [
                'id_kategori_api' => $this->kategoriBaru->id,
                'target_user_id' => $this->stafPenerima->id,
            ]);

        $response->assertRedirect(route('staf.tiket-saya'));
        $response->assertSessionHas('success');

        // Verifikasi kepemilikan tiket berpindah ke staf penerima
        $this->assertDatabaseHas('tickets', [
            'id' => $this->ticket->id,
            'user_id' => $this->stafPenerima->id,
            'id_kategori_api' => $this->kategoriBaru->id,
            'status' => 'progress',
        ]);

        // Verifikasi riwayat tiket mencatat 'dialihkan'
        $this->assertDatabaseHas('riwayat_tiket', [
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->stafPengirim->id,
            'aksi' => 'dialihkan',
        ]);

        // Verifikasi notifikasi internal masuk ke staf penerima
        $this->assertDatabaseHas('notifikasi_internal', [
            'user_id' => $this->stafPenerima->id,
            'is_read' => false,
        ]);
    }

    public function test_disposisi_fails_without_target_user(): void
    {
        $response = $this->actingAs($this->stafPengirim)
            ->post(route('staf.tickets.disposisi', $this->ticket), [
                'id_kategori_api' => $this->kategoriBaru->id,
                'target_user_id' => '',
            ]);

        $response->assertSessionHasErrors(['target_user_id']);
    }
}
