<?php

namespace Tests\Feature;

use App\Models\BidangStaf;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBidangManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'name' => 'Super Administrator',
            'role' => 'admin',
            'email' => 'admin@pintas.com',
        ]);
    }

    public function test_admin_can_view_bidang_management_page(): void
    {
        $bidang = BidangStaf::create(['nama_bidang' => 'Bidang Layanan E-Government']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.bidang.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Bidang');
        $response->assertSee('Bidang Layanan E-Government');
        $response->assertSee('addBidangModal');
        $response->assertSee('editBidangModal');
        $response->assertSee('Total Bidang');
    }

    public function test_admin_can_search_bidang_by_keyword(): void
    {
        BidangStaf::create(['nama_bidang' => 'Infrastruktur Jaringan']);
        BidangStaf::create(['nama_bidang' => 'Statistik & Persandian']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.bidang.index', ['search' => 'Jaringan']));

        $response->assertStatus(200);
        $response->assertSee('Infrastruktur Jaringan');
        $response->assertDontSee('Statistik & Persandian');
    }

    public function test_admin_can_store_new_bidang(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.bidang.store'), [
            'nama_bidang' => 'Teknologi Informasi & Komunikasi',
        ]);

        $response->assertRedirect(route('admin.bidang.index'));
        $response->assertSessionHas('success', 'Data bidang berhasil ditambahkan!');

        $this->assertDatabaseHas('bidang_staf', [
            'nama_bidang' => 'Teknologi Informasi & Komunikasi',
        ]);
    }

    public function test_admin_cannot_store_duplicate_bidang(): void
    {
        BidangStaf::create(['nama_bidang' => 'Bidang Informasi Publik']);

        $response = $this->actingAs($this->adminUser)->post(route('admin.bidang.store'), [
            'nama_bidang' => 'Bidang Informasi Publik',
        ]);

        $response->assertSessionHasErrors(['nama_bidang']);
        $this->assertEquals(1, BidangStaf::where('nama_bidang', 'Bidang Informasi Publik')->count());
    }

    public function test_admin_can_update_bidang(): void
    {
        $bidang = BidangStaf::create(['nama_bidang' => 'Aplikasi Informatika Lama']);

        $response = $this->actingAs($this->adminUser)->put(route('admin.bidang.update', $bidang->id), [
            'nama_bidang' => 'Aplikasi Informatika Baru',
        ]);

        $response->assertRedirect(route('admin.bidang.index'));
        $response->assertSessionHas('success', 'Data bidang berhasil diperbarui!');

        $this->assertDatabaseHas('bidang_staf', [
            'id' => $bidang->id,
            'nama_bidang' => 'Aplikasi Informatika Baru',
        ]);
    }

    public function test_admin_can_delete_bidang_without_users(): void
    {
        $bidang = BidangStaf::create(['nama_bidang' => 'Bidang Kosong']);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.bidang.destroy', $bidang->id));

        $response->assertRedirect(route('admin.bidang.index'));
        $response->assertSessionHas('success', 'Data bidang berhasil dihapus!');

        $this->assertDatabaseMissing('bidang_staf', [
            'id' => $bidang->id,
        ]);
    }

    public function test_admin_cannot_delete_bidang_with_assigned_users(): void
    {
        $bidang = BidangStaf::create(['nama_bidang' => 'Bidang Ada Staf']);

        User::factory()->create([
            'name' => 'Staf Teknis',
            'role' => 'staf_bidang',
            'bidang_id' => $bidang->id,
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.bidang.destroy', $bidang->id));

        $response->assertRedirect(route('admin.bidang.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('bidang_staf', [
            'id' => $bidang->id,
        ]);
    }
}
