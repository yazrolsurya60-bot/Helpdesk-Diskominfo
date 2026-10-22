<?php

namespace Tests\Feature;

use App\Models\BidangStaf;
use App\Models\KategoriLayanan;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $stafUser;
    protected KategoriLayanan $kategoriJaringan;
    protected KategoriLayanan $kategoriAplikasi;

    protected function setUp(): void
    {
        parent::setUp();

        $bidang = BidangStaf::create([
            'nama_bidang' => 'Bidang Aplikasi & Informatika',
        ]);

        $this->adminUser = User::factory()->create([
            'name' => 'Super Admin',
            'role' => 'admin',
            'email' => 'admin@pintas.com',
        ]);

        $this->stafUser = User::factory()->create([
            'name' => 'Bambang Pamungkas',
            'role' => 'staf_bidang',
            'bidang_id' => $bidang->id,
            'email' => 'bambang@pintas.com',
        ]);

        $this->kategoriJaringan = KategoriLayanan::create([
            'nama_layanan' => 'Jaringan & Server',
            'status_aktif' => true,
        ]);

        $this->kategoriAplikasi = KategoriLayanan::create([
            'nama_layanan' => 'Perangkat Lunak',
            'status_aktif' => true,
        ]);
    }

    public function test_admin_dashboard_displays_dynamic_kpi_cards_and_tickets(): void
    {
        // Buat 3 tiket dengan variasi status
        $t1 = Ticket::create([
            'user_id' => null,
            'nomor_wa' => '081234567891',
            'nama_pemohon' => 'Budi Santoso (Dinas Pendidikan)',
            'keluhan' => 'Koneksi internet di ruang server Gedung B terputus',
            'id_kategori_api' => $this->kategoriJaringan->id,
            'status' => 'open',
        ]);

        $t2 = Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081298765432',
            'nama_pemohon' => 'Siti Rahmawati (Bappeda)',
            'keluhan' => 'Permohonan aktivasi akun email dinas dan e-Planning',
            'id_kategori_api' => $this->kategoriAplikasi->id,
            'status' => 'progress',
            'assigned_at' => now(),
        ]);

        $t3 = Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081311223344',
            'nama_pemohon' => 'Hendra Wijaya (Kecamatan Sukamaju)',
            'keluhan' => 'Reset kata sandi aplikasi SIPD',
            'id_kategori_api' => $this->kategoriAplikasi->id,
            'status' => 'selesai',
            'assigned_at' => now()->subHours(3),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Verifikasi Nilai KPI Cards Dinamis
        $response->assertSee('Tiket Terbuka');
        $response->assertSee('In Progress');
        $response->assertSee('Sudah Diselesaikan');

        // Verifikasi Data Tiket Muncul di Tabel
        $response->assertSee('#' . $t1->id);
        $response->assertSee('Budi Santoso (Dinas Pendidikan)');
        $response->assertSee('Koneksi internet di ruang server Gedung B terputus');
        $response->assertSee('Jaringan &amp; Server', false);
        $response->assertSee('Unassigned');

        $response->assertSee('#' . $t2->id);
        $response->assertSee('Siti Rahmawati (Bappeda)');
        $response->assertSee('Bambang Pamungkas');

        $response->assertSee('#' . $t3->id);
        $response->assertSee('Hendra Wijaya (Kecamatan Sukamaju)');

        // Verifikasi Kategori Dropdown Dinamis
        $response->assertSee('Jaringan &amp; Server (1)', false);
        $response->assertSee('Perangkat Lunak (2)', false);
        $response->assertSee('Semua Kategori (3)');
    }

    public function test_admin_dashboard_can_filter_by_status(): void
    {
        Ticket::create([
            'user_id' => null,
            'nomor_wa' => '081234567891',
            'nama_pemohon' => 'Budi Santoso',
            'keluhan' => 'Kendala Jaringan Open',
            'id_kategori_api' => $this->kategoriJaringan->id,
            'status' => 'open',
        ]);

        Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081298765432',
            'nama_pemohon' => 'Siti Rahmawati',
            'keluhan' => 'Kendala Aplikasi Progress',
            'id_kategori_api' => $this->kategoriAplikasi->id,
            'status' => 'progress',
        ]);

        // Filter status 'open'
        $responseOpen = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard', ['status' => 'open']));

        $responseOpen->assertStatus(200);
        $responseOpen->assertSee('Kendala Jaringan Open');
        $responseOpen->assertDontSee('Kendala Aplikasi Progress');

        // Filter status 'progress'
        $responseProgress = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard', ['status' => 'progress']));

        $responseProgress->assertStatus(200);
        $responseProgress->assertSee('Kendala Aplikasi Progress');
        $responseProgress->assertDontSee('Kendala Jaringan Open');
    }

    public function test_admin_dashboard_can_search_ticket(): void
    {
        Ticket::create([
            'user_id' => null,
            'nomor_wa' => '081234567891',
            'nama_pemohon' => 'Budi Santoso',
            'keluhan' => 'Kerusakan Switch Ruang Data',
            'id_kategori_api' => $this->kategoriJaringan->id,
            'status' => 'open',
        ]);

        Ticket::create([
            'user_id' => null,
            'nomor_wa' => '081298765432',
            'nama_pemohon' => 'Siti Rahmawati',
            'keluhan' => 'Aplikasi SIPD Error',
            'id_kategori_api' => $this->kategoriAplikasi->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard', ['search' => 'Switch']));

        $response->assertStatus(200);
        $response->assertSee('Kerusakan Switch Ruang Data');
        $response->assertDontSee('Aplikasi SIPD Error');
    }
}
