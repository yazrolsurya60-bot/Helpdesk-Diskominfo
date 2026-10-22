<?php

namespace Tests\Feature;

use App\Models\BidangStaf;
use App\Models\KategoriLayanan;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoReleaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected User $stafUser;
    protected KategoriLayanan $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $bidang = BidangStaf::create([
            'nama_bidang' => 'Bidang Aplikasi & Informatika',
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

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset mock time
        parent::tearDown();
    }

    public function test_auto_release_releases_tickets_exceeding_two_working_hours(): void
    {
        // Set mock time: Senin pukul 11:00
        Carbon::setTestNow(Carbon::parse('2026-10-19 11:00:00'));

        // Tiket diambil Senin pukul 08:30 (sudah 2.5 jam kerja)
        $stuckTicket = Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Wijaya',
            'keluhan' => 'Aplikasi absensi error',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'progress',
            'assigned_at' => Carbon::parse('2026-10-19 08:30:00'),
        ]);

        $this->artisan('tickets:auto-release')
            ->expectsOutput('Successfully auto-released 1 tickets.')
            ->assertExitCode(0);

        $stuckTicket->refresh();
        $this->assertSame('open', $stuckTicket->status);
        $this->assertNull($stuckTicket->user_id);
        $this->assertNull($stuckTicket->assigned_at);

        $this->assertDatabaseHas('riwayat_tiket', [
            'ticket_id' => $stuckTicket->id,
            'user_id' => null,
            'aksi' => 'dialihkan',
        ]);

        $this->assertDatabaseHas('notifikasi_internal', [
            'user_id' => $this->stafUser->id,
            'judul' => "Tugas tiket #{$stuckTicket->id} dicabut otomatis (melebihi 2 jam).",
            'is_read' => 0,
        ]);
    }

    public function test_auto_release_does_not_release_ticket_overnight_if_working_hours_under_two_hours(): void
    {
        // Tiket diambil Senin pukul 15:00 (hanya 45 menit sebelum kantor tutup pukul 15:45)
        $ticket = Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Wijaya',
            'keluhan' => 'Aplikasi absensi error',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'progress',
            'assigned_at' => Carbon::parse('2026-10-19 15:00:00'),
        ]);

        // Dicek Selasa pagi pukul 08:00 (sudah lewat 17 jam kalender, tapi baru 75 menit jam kerja)
        Carbon::setTestNow(Carbon::parse('2026-10-20 08:00:00'));

        $this->artisan('tickets:auto-release')
            ->expectsOutput('No stuck tickets found to auto-release.')
            ->assertExitCode(0);

        $ticket->refresh();
        $this->assertSame('progress', $ticket->status);
        $this->assertSame($this->stafUser->id, $ticket->user_id);
    }

    public function test_auto_release_releases_ticket_next_day_when_working_hours_reach_two_hours(): void
    {
        // Tiket diambil Senin pukul 15:00 (45 menit kerja)
        $ticket = Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Wijaya',
            'keluhan' => 'Aplikasi absensi error',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'progress',
            'assigned_at' => Carbon::parse('2026-10-19 15:00:00'),
        ]);

        // Dicek Selasa pukul 08:45 (kantor buka 07:30 -> sudah 75 menit kerja di Selasa)
        // Total jam kerja: 45 + 75 = 120 menit (tepat batas SLA)
        Carbon::setTestNow(Carbon::parse('2026-10-20 08:45:00'));

        $this->artisan('tickets:auto-release')
            ->expectsOutput('Successfully auto-released 1 tickets.')
            ->assertExitCode(0);

        $ticket->refresh();
        $this->assertSame('open', $ticket->status);
        $this->assertNull($ticket->user_id);
    }

    public function test_auto_release_ignores_completed_or_open_tickets(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-19 14:00:00'));

        Ticket::create([
            'user_id' => $this->stafUser->id,
            'nomor_wa' => '081234567890',
            'nama_pemohon' => 'Andi Wijaya',
            'keluhan' => 'Tiket Selesai',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'selesai',
            'assigned_at' => Carbon::parse('2026-10-19 08:00:00'),
        ]);

        Ticket::create([
            'user_id' => null,
            'nomor_wa' => '081234567891',
            'nama_pemohon' => 'Siti Aminah',
            'keluhan' => 'Tiket Open',
            'id_kategori_api' => $this->kategori->id,
            'status' => 'open',
            'assigned_at' => null,
        ]);

        $this->artisan('tickets:auto-release')
            ->expectsOutput('No stuck tickets found to auto-release.')
            ->assertExitCode(0);
    }
}
