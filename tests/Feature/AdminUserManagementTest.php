<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BidangStaf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'name' => 'Super Administrator',
            'role' => 'admin',
            'email' => 'admin@pintas.com',
        ]);

        $this->targetUser = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john.doe@pintas.com',
            'role' => 'front_desk',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_admin_can_view_user_management_with_edit_modal(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('User Management');
        $response->assertSee('John Doe');
        $response->assertSee('editUserModal');
        $response->assertSee('editUserBtn');
        $response->assertSee('data-user-id="' . $this->targetUser->id . '"', false);
        $response->assertSee('data-name="John Doe"', false);
    }

    public function test_admin_can_update_user_via_modal_action(): void
    {
        $response = $this->actingAs($this->adminUser)->put(route('admin.users.update', $this->targetUser->id), [
            'name' => 'John Doe Updated',
            'email' => 'john.updated@pintas.com',
            'role' => 'staf_bidang',
            'password' => '', // kosong tidak mengubah password
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->targetUser->refresh();
        $this->assertEquals('John Doe Updated', $this->targetUser->name);
        $this->assertEquals('john.updated@pintas.com', $this->targetUser->email);
        $this->assertEquals('staf_bidang', $this->targetUser->role);
        $this->assertTrue(Hash::check('password123', $this->targetUser->password));
    }

    public function test_admin_can_update_user_and_change_password(): void
    {
        $response = $this->actingAs($this->adminUser)->put(route('admin.users.update', $this->targetUser->id), [
            'name' => 'John Doe',
            'email' => 'john.doe@pintas.com',
            'role' => 'front_desk',
            'password' => 'newsecretpassword',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->targetUser->refresh();
        $this->assertTrue(Hash::check('newsecretpassword', $this->targetUser->password));
    }

    public function test_admin_can_store_user_with_bidang_id(): void
    {
        $bidang = BidangStaf::create(['nama_bidang' => 'Bidang Aplikasi']);

        $response = $this->actingAs($this->adminUser)->post(route('admin.users.store'), [
            'name' => 'Staf Baru',
            'email' => 'stafbaru@pintas.com',
            'role' => 'staf_bidang',
            'password' => 'password123',
            'bidang_id' => $bidang->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $newUser = User::where('email', 'stafbaru@pintas.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals($bidang->id, $newUser->bidang_id);
    }

    public function test_admin_can_update_user_with_bidang_id(): void
    {
        $bidang = BidangStaf::create(['nama_bidang' => 'Bidang Jaringan']);

        $response = $this->actingAs($this->adminUser)->put(route('admin.users.update', $this->targetUser->id), [
            'name' => 'John Doe Staf',
            'email' => 'john.staf@pintas.com',
            'role' => 'staf_bidang',
            'password' => '',
            'bidang_id' => $bidang->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->targetUser->refresh();
        $this->assertEquals('staf_bidang', $this->targetUser->role);
        $this->assertEquals($bidang->id, $this->targetUser->bidang_id);
    }
}
