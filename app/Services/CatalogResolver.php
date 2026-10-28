<?php

namespace App\Services;

use App\Models\KategoriLayanan;

class CatalogResolver
{
    /**
     * Pemetaan katalog layanan default berdasarkan Format.md
     */
    public const DEFAULT_CATALOG = [
        1 => 'Reset password email kalbarprov',
        2 => 'Gangguan akses internet',
        3 => 'Kendala absensi',
        4 => 'Kendala aplikasi e-aktivity',
    ];

    /**
     * Mengambil model KategoriLayanan berdasarkan input nomor katalog / nama layanan
     *
     * @param string|int $input
     * @return KategoriLayanan
     */
    public static function resolveCategory(string|int $input): KategoriLayanan
    {
        $inputStr = trim((string)$input);

        // 1. Cek nomor indeks katalog default (1 s.d. 4)
        if (is_numeric($inputStr) && isset(self::DEFAULT_CATALOG[(int)$inputStr])) {
            $targetName = self::DEFAULT_CATALOG[(int)$inputStr];
            return KategoriLayanan::firstOrCreate(
                ['nama_layanan' => $targetName],
                ['status_aktif' => true]
            );
        }

        // 2. Cek jika input berupa ID langsung di database
        if (is_numeric($inputStr)) {
            $cat = KategoriLayanan::find((int)$inputStr);
            if ($cat) {
                return $cat;
            }
        }

        // 3. Cek pencarian berdasarkan kemiripan nama
        foreach (self::DEFAULT_CATALOG as $id => $name) {
            if (stripos($inputStr, $name) !== false || stripos($name, $inputStr) !== false) {
                return KategoriLayanan::firstOrCreate(
                    ['nama_layanan' => $name],
                    ['status_aktif' => true]
                );
            }
        }

        $existing = KategoriLayanan::where('nama_layanan', 'like', "%{$inputStr}%")->first();
        if ($existing) {
            return $existing;
        }

        // 4. Fallback ke kategori pertama yang aktif atau buat baru
        return KategoriLayanan::where('status_aktif', true)->first()
            ?? KategoriLayanan::firstOrCreate(['nama_layanan' => 'Layanan Umum'], ['status_aktif' => true]);
    }
}
