<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\RiwayatTiket;
use App\Models\WaLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TicketSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Tiket 1: Perbaikan Jaringan
        $t1 = Ticket::create([
            'user_id' => null,
            'nomor_wa' => '081234567891',
            'nama_pemohon' => 'Budi Santoso (Dinas Pendidikan)',
            'keluhan' => 'Koneksi internet di ruang server Gedung B terputus sejak pukul 09:00 WIB, mohon bantuan teknisi jaringan untuk pengecekan kabel LAN dan switch.',
            'id_kategori_api' => 1, // Perbaikan Jaringan
            'status' => 'open',
            'assigned_at' => null,
            'created_at' => $now->copy()->subMinutes(25),
            'updated_at' => $now->copy()->subMinutes(25),
        ]);

        RiwayatTiket::create([
            'ticket_id' => $t1->id,
            'user_id' => null,
            'aksi' => 'dibuat',
            'created_at' => $now->copy()->subMinutes(25),
            'updated_at' => $now->copy()->subMinutes(25),
        ]);

        WaLog::create([
            'ticket_id' => $t1->id,
            'sender_type' => 'pemohon',
            'message' => 'Selamat siang admin, mohon bantuannya koneksi internet di ruang server Gedung B terputus sejak jam 9 pagi tadi. Kabel LAN dan switch sudah kami cek tapi lampu indikator merah. Terima kasih.',
            'created_at' => $now->copy()->subMinutes(25),
            'updated_at' => $now->copy()->subMinutes(25),
        ]);

        // 2. Tiket 2: Pembuatan Akun
        $t2 = Ticket::create([
            'user_id' => null,
            'nomor_wa' => '081298765432',
            'nama_pemohon' => 'Siti Rahmawati (Bappeda)',
            'keluhan' => 'Permohonan aktivasi akun email dinas (@diskominfo.go.id) dan hak akses sistem perencanaan e-Planning untuk staf baru bidang litbang.',
            'id_kategori_api' => 2, // Pembuatan Akun
            'status' => 'open',
            'assigned_at' => null,
            'created_at' => $now->copy()->subMinutes(15),
            'updated_at' => $now->copy()->subMinutes(15),
        ]);

        RiwayatTiket::create([
            'ticket_id' => $t2->id,
            'user_id' => null,
            'aksi' => 'dibuat',
            'created_at' => $now->copy()->subMinutes(15),
            'updated_at' => $now->copy()->subMinutes(15),
        ]);

        WaLog::create([
            'ticket_id' => $t2->id,
            'sender_type' => 'pemohon',
            'message' => 'Halo Helpdesk Diskominfo, kami dari Bappeda ingin mengajukan permohonan pembuatan akun email dinas dan hak akses e-Planning untuk pegawai baru kami. Dokumen SK penugasan sudah kami siapkan.',
            'created_at' => $now->copy()->subMinutes(15),
            'updated_at' => $now->copy()->subMinutes(15),
        ]);

        // 3. Tiket 3: Perbaikan Jaringan
        $t3 = Ticket::create([
            'user_id' => null,
            'nomor_wa' => '085712345678',
            'nama_pemohon' => 'Hendra Wijaya (Kecamatan Sukamaju)',
            'keluhan' => 'Akses WiFi layanan publik di kantor kecamatan Sukamaju lambat dan sering RTO (Request Time Out) saat pelayanan rekam KTP elektronik.',
            'id_kategori_api' => 1, // Perbaikan Jaringan
            'status' => 'open',
            'assigned_at' => null,
            'created_at' => $now->copy()->subMinutes(5),
            'updated_at' => $now->copy()->subMinutes(5),
        ]);

        RiwayatTiket::create([
            'ticket_id' => $t3->id,
            'user_id' => null,
            'aksi' => 'dibuat',
            'created_at' => $now->copy()->subMinutes(5),
            'updated_at' => $now->copy()->subMinutes(5),
        ]);

        WaLog::create([
            'ticket_id' => $t3->id,
            'sender_type' => 'pemohon',
            'message' => 'Siang tim Helpdesk, wifi di ruang pelayanan kantor kecamatan sangat lambat dan sering putus, warga sedang antre rekam KTP. Mohon bantuannya segera dilakukan pengecekan.',
            'created_at' => $now->copy()->subMinutes(5),
            'updated_at' => $now->copy()->subMinutes(5),
        ]);
    }
}
