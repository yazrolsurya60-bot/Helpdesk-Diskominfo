<?php

namespace Database\Seeders;

use App\Models\NotifikasiInternal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $frontDesk = User::where('role', 'front_desk')->first();
        $staf = User::where('role', 'staf_bidang')->first();

        $now = Carbon::now();

        // 1. Notifikasi untuk Staf Bidang
        if ($staf) {
            NotifikasiInternal::create([
                'user_id' => $staf->id,
                'judul' => 'Tiket #1042 telah didistribusikan kepada Anda oleh Front Desk (Petugas Front Desk)',
                'is_read' => false,
                'created_at' => $now->copy()->subMinutes(15),
                'updated_at' => $now->copy()->subMinutes(15),
            ]);

            NotifikasiInternal::create([
                'user_id' => $staf->id,
                'judul' => 'Tiket #1044 telah didisposisikan kepada Anda oleh Teknisi Rekan',
                'is_read' => false,
                'created_at' => $now->copy()->subMinutes(45),
                'updated_at' => $now->copy()->subMinutes(45),
            ]);

            NotifikasiInternal::create([
                'user_id' => $staf->id,
                'judul' => 'Tiket #1039 telah ditarik kembali oleh Front Desk. Alasan: Melebihi batas toleransi SLA (2 jam kerja).',
                'is_read' => false,
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ]);

            NotifikasiInternal::create([
                'user_id' => $staf->id,
                'judul' => 'Tiket #1030 telah berhasil diselesaikan dan ditutup.',
                'is_read' => true,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ]);
        }

        // 2. Notifikasi untuk Front Desk
        if ($frontDesk) {
            NotifikasiInternal::create([
                'user_id' => $frontDesk->id,
                'judul' => 'Tiket #1040 telah selesai dikerjakan oleh teknisi Staf Bidang Jaringan.',
                'is_read' => false,
                'created_at' => $now->copy()->subMinutes(20),
                'updated_at' => $now->copy()->subMinutes(20),
            ]);

            NotifikasiInternal::create([
                'user_id' => $frontDesk->id,
                'judul' => 'Tiket #1043 telah berhasil didistribusikan ke Staf Bidang.',
                'is_read' => false,
                'created_at' => $now->copy()->subHours(1),
                'updated_at' => $now->copy()->subHours(1),
            ]);

            NotifikasiInternal::create([
                'user_id' => $frontDesk->id,
                'judul' => 'Tiket #1038 telah ditarik kembali ke antrean utama.',
                'is_read' => true,
                'created_at' => $now->copy()->subHours(3),
                'updated_at' => $now->copy()->subHours(3),
            ]);
        }

        // 3. Notifikasi untuk Admin
        if ($admin) {
            NotifikasiInternal::create([
                'user_id' => $admin->id,
                'judul' => 'Tiket #1039 telah ditarik otomatis oleh sistem SLA ke antrean terbuka.',
                'is_read' => false,
                'created_at' => $now->copy()->subMinutes(30),
                'updated_at' => $now->copy()->subMinutes(30),
            ]);

            NotifikasiInternal::create([
                'user_id' => $admin->id,
                'judul' => 'Rekapitulasi laporan operasional bulanan siap untuk diekspor.',
                'is_read' => false,
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ]);

            NotifikasiInternal::create([
                'user_id' => $admin->id,
                'judul' => 'Pembaruan data pengguna sistem Helpdesk berhasil disimpan.',
                'is_read' => true,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ]);
        }
    }
}
