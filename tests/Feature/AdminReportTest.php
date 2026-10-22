<?php

namespace Tests\Feature;

use App\Models\BidangStaf;
use App\Models\KategoriLayanan;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $stafUser;
    protected KategoriLayanan $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $bidang = BidangStaf::create([
            'nama_bidang' => 'Bidang Aplikasi & Informatika',
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@pintas.com',
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
    }

    public function test_admin_can_access_reports_and_see_performance_table(): void
    {
        Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Wijaya',
            'keluhan' => 'Aplikasi absensi error',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'selesai',
            'assigned_at' => now()->subHours(2),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Tabel Laporan Kinerja Staf &amp; Bidang', false);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Perangkat Lunak');
    }

    public function test_admin_can_filter_reports_by_date_and_category(): void
    {
        Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Wijaya',
            'keluhan' => 'Aplikasi error',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'selesai',
            'created_at' => '2026-10-01 10:00:00',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.index', [
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-02',
            'kategori_id' => $this->kategori->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
    }

    public function test_admin_can_export_reports_when_data_exists(): void
    {
        Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Wijaya',
            'keluhan' => 'Gangguan Server',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.export', ['format' => 'excel']));

        $response->assertStatus(200);
        $this->assertTrue($response->headers->get('content-type') === 'text/csv; charset=UTF-8');
    }

    public function test_admin_export_handles_empty_dataset_with_error_flash(): void
    {
        // Tanpa ada tiket
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.export', [
            'start_date' => '2020-01-01',
            'end_date' => '2020-01-02',
        ]));

        $response->assertRedirect(route('admin.reports.index', [
            'start_date' => '2020-01-01',
            'end_date' => '2020-01-02',
        ]));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_view_printable_report(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.print'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Rekap Kinerja &amp; Penanganan Tiket Helpdesk', false);
    }

    public function test_admin_reports_provides_trend_chart_data_for_day_month_and_year(): void
    {
        Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Wijaya',
            'keluhan' => 'Router restart berkala',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'selesai',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewHas('chartData');
        $chartData = $response->viewData('chartData');

        $this->assertArrayHasKey('day', $chartData);
        $this->assertArrayHasKey('month', $chartData);
        $this->assertArrayHasKey('year', $chartData);
        $this->assertNotEmpty($chartData['day']);
        $this->assertCount(12, $chartData['month']);
        $this->assertCount(5, $chartData['year']);
    }
}
