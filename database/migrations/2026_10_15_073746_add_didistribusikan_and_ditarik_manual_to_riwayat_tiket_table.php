<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE riwayat_tiket MODIFY COLUMN aksi ENUM('dibuat', 'diambil', 'dialihkan', 'update_label', 'selesai', 'didistribusikan', 'ditarik_manual') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE riwayat_tiket MODIFY COLUMN aksi ENUM('dibuat', 'diambil', 'dialihkan', 'update_label', 'selesai') NOT NULL");
        }
    }
};
