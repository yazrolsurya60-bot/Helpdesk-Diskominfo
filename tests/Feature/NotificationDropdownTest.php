<?php

namespace Tests\Feature;

use App\Models\NotifikasiInternal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationDropdownTest extends TestCase
{
    use RefreshDatabase;

    protected User $staf;
    protected User $frontDesk;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staf = User::factory()->create([
            'name' => 'Staf Bidang',
            'role' => 'staf_bidang',
            'email' => 'staf@pintas.com',
        ]);

        $this->frontDesk = User::factory()->create([
            'name' => 'Front Desk',
            'role' => 'front_desk',
            'email' => 'frontdesk@pintas.com',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'role' => 'admin',
            'email' => 'admin@pintas.com',
        ]);
    }

    public function test_staf_sees_notification_dropdown_with_unread_badge(): void
    {
        NotifikasiInternal::create([
            'user_id' => $this->staf->id,
            'judul' => 'Tiket #1042 telah didistribusikan kepada Anda',
            'is_read' => false,
        ]);

        NotifikasiInternal::create([
            'user_id' => $this->staf->id,
            'judul' => 'Tiket #1039 telah ditarik kembali oleh Front Desk',
            'is_read' => true,
        ]);

        $response = $this->actingAs($this->staf)
            ->get(route('staf.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Notifikasi Tugas');
        $response->assertSee('Tiket #1042 telah didistribusikan kepada Anda');
        $response->assertSee('Tiket #1039 telah ditarik kembali oleh Front Desk');
        $response->assertSee('1 Baru');
        // Pastikan badge merah tidak menggunakan animasi animate-pulse
        $response->assertDontSee('animate-pulse');
    }

    public function test_frontdesk_sees_notification_dropdown(): void
    {
        NotifikasiInternal::create([
            'user_id' => $this->frontDesk->id,
            'judul' => 'Tiket #1040 telah selesai dikerjakan',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->frontDesk)
            ->get(route('frontdesk.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifikasi Tugas');
        $response->assertSee('Tiket #1040 telah selesai dikerjakan');
    }

    public function test_admin_sees_notification_dropdown(): void
    {
        NotifikasiInternal::create([
            'user_id' => $this->admin->id,
            'judul' => 'Tiket #1039 telah dilepas otomatis oleh sistem SLA',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Notifikasi Tugas');
        $response->assertSee('Tiket #1039 telah dilepas otomatis oleh sistem SLA');
    }

    public function test_user_can_mark_single_notification_as_read_and_badge_disappears(): void
    {
        $notif = NotifikasiInternal::create([
            'user_id' => $this->staf->id,
            'judul' => 'Pemberitahuan pembaruan sistem tanpa nomor tiket',
            'is_read' => false,
        ]);

        $this->actingAs($this->staf);

        // Sebelum dibaca, dashboard menampilkan 1 Baru
        $resBefore = $this->get(route('staf.dashboard'));
        $resBefore->assertSee('1 Baru');

        // Klik baca notifikasi
        $response = $this->post(route('notifikasi.read', $notif->id));
        $response->assertRedirect();

        // Notifikasi di database sekarang sudah is_read = true
        $this->assertDatabaseHas('notifikasi_internal', [
            'id' => $notif->id,
            'is_read' => true,
        ]);

        // Setelah dibaca, badge merah hilang dan status menjadi Semua Terbaca
        $resAfter = $this->get(route('staf.dashboard'));
        $resAfter->assertDontSee('1 Baru');
        $resAfter->assertSee('Semua Terbaca');
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        NotifikasiInternal::create([
            'user_id' => $this->frontDesk->id,
            'judul' => 'Tiket #1001 baru masuk',
            'is_read' => false,
        ]);

        NotifikasiInternal::create([
            'user_id' => $this->frontDesk->id,
            'judul' => 'Tiket #1002 baru masuk',
            'is_read' => false,
        ]);

        $this->actingAs($this->frontDesk);

        $resBefore = $this->get(route('frontdesk.index'));
        $resBefore->assertSee('2 Baru');

        // Klik Tandai Semua Dibaca
        $response = $this->post(route('notifikasi.read-all'));
        $response->assertRedirect();

        // Seluruh notifikasi frontdesk menjadi is_read = true
        $this->assertEquals(0, NotifikasiInternal::where('user_id', $this->frontDesk->id)->where('is_read', false)->count());

        $resAfter = $this->get(route('frontdesk.index'));
        $resAfter->assertDontSee('2 Baru');
        $resAfter->assertSee('Semua Terbaca');
    }

    public function test_user_cannot_mark_other_users_notification(): void
    {
        $otherNotif = NotifikasiInternal::create([
            'user_id' => $this->admin->id,
            'judul' => 'Notifikasi milik Admin',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->staf)
            ->post(route('notifikasi.read', $otherNotif->id));

        $response->assertStatus(403);
    }
}
