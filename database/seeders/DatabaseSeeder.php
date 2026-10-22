<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Insert Bidang Staf
        DB::table('bidang_staf')->insert([
            ['nama_bidang' => 'Bidang Jaringan'],
            ['nama_bidang' => 'Bidang Aplikasi'],
        ]);

        // Insert Kategori Layanan
        DB::table('kategori_layanan')->insert([
            ['nama_layanan' => 'Perbaikan Jaringan', 'status_aktif' => true],
            ['nama_layanan' => 'Pembuatan Akun', 'status_aktif' => true],
        ]);

        // Membuat akun perdana untuk Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@pintas.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        
        // Opsional: Membuat akun dummy untuk Front Desk dan Staf
        User::create([
            'name' => 'Petugas Front Desk',
            'username' => 'frontdesk',
            'email' => 'frontdesk@pintas.com',
            'password' => Hash::make('password123'),
            'role' => 'front_desk',
        ]);
        
        User::create([
            'name' => 'Staf Bidang Jaringan',
            'username' => 'staf',
            'email' => 'staf@pintas.com',
            'password' => Hash::make('password123'),
            'role' => 'staf_bidang',
            'bidang_id' => 1,
        ]);
    }
}
