<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. bidang_staf
        Schema::create('bidang_staf', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bidang');
            $table->timestamps();
        });

        // 2. update users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('bidang_id')->nullable()->constrained('bidang_staf')->onDelete('set null');
            $table->string('username')->nullable()->unique();
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'front_desk', 'staf_bidang'])->default('staf_bidang')->after('password');
        });

        // 3. notifikasi_internal
        Schema::create('notifikasi_internal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // 4. kategori_layanan
        Schema::create('kategori_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_layanan');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 5. tickets
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // staf yang mengambil tugas
            $table->string('nomor_wa');
            $table->string('nama_pemohon')->nullable();
            $table->text('keluhan');
            $table->foreignId('id_kategori_api')->nullable()->constrained('kategori_layanan')->onDelete('set null');
            $table->enum('status', ['open', 'progress', 'selesai'])->default('open');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
        });

        // 6. wa_logs
        Schema::create('wa_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->enum('sender_type', ['pemohon', 'pegawai']);
            $table->text('message');
            $table->timestamps();
        });

        // 7. riwayat_tiket
        Schema::create('riwayat_tiket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('aksi', ['dibuat', 'diambil', 'dialihkan', 'update_label', 'selesai', 'didistribusikan', 'ditarik_manual']);
            $table->timestamps();
        });

        // 8. lampiran_tiket
        Schema::create('lampiran_tiket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lampiran_tiket');
        Schema::dropIfExists('riwayat_tiket');
        Schema::dropIfExists('wa_logs');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('kategori_layanan');
        Schema::dropIfExists('notifikasi_internal');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['bidang_id']);
            $table->dropColumn('bidang_id');
            $table->dropColumn('username');
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'frontdesk', 'staf'])->default('staf');
        });

        Schema::dropIfExists('bidang_staf');
    }
};
