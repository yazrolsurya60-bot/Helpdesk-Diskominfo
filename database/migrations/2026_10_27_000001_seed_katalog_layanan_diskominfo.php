<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $katalog = [
            'Reset password email kalbarprov',
            'Gangguan akses internet',
            'Kendala absensi',
            'Kendala aplikasi e-aktivity',
        ];

        foreach ($katalog as $nama) {
            DB::table('kategori_layanan')->updateOrInsert(
                ['nama_layanan' => $nama],
                [
                    'status_aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('kategori_layanan')->whereIn('nama_layanan', [
            'Reset password email kalbarprov',
            'Gangguan akses internet',
            'Kendala absensi',
            'Kendala aplikasi e-aktivity',
        ])->delete();
    }
};
