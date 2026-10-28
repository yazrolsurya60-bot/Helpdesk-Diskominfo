<?php

namespace App\Services;

class ComplaintFormatParser
{
    /**
     * Memvalidasi dan mengekstrak format keluhan dari pesan WhatsApp pemohon.
     *
     * Format yang diharapkan (sesuai Format.md):
     * Nama :
     * Jabatan :
     * OPD :
     * Nomor Katalog :
     * Detail Pengaduan :
     *
     * @param string $message
     * @return array|null Mengembalikan array asosiatif jika format valid, atau null jika tidak valid
     */
    public static function parse(string $message): ?array
    {
        if (empty(trim($message))) {
            return null;
        }

        // Normalisasi enter baris baru
        $clean = preg_replace('/\r\n|\r/', "\n", $message);

        // Hapus simbol markdown tebal/miring khas WhatsApp (*tebal*, _miring_)
        $clean = preg_replace('/[*_~]/', '', $clean);

        // Pola regex toleran terhadap spasi dan urutan field
        $patternNama = '/(?:^|\n)\s*Nama\s*:\s*(.+?)(?=\n\s*(?:Jabatan|OPD|Instansi|Nomor\s*Katalog|No\s*Katalog|Katalog|Detail\s*Pengaduan|Pengaduan|Keluhan)\s*:|$)/is';
        preg_match($patternNama, $clean, $matchNama);

        $patternJabatan = '/(?:^|\n)\s*Jabatan\s*:\s*(.+?)(?=\n\s*(?:Nama|OPD|Instansi|Nomor\s*Katalog|No\s*Katalog|Katalog|Detail\s*Pengaduan|Pengaduan|Keluhan)\s*:|$)/is';
        preg_match($patternJabatan, $clean, $matchJabatan);

        $patternOpd = '/(?:^|\n)\s*(?:OPD|Instansi)\s*:\s*(.+?)(?=\n\s*(?:Nama|Jabatan|Nomor\s*Katalog|No\s*Katalog|Katalog|Detail\s*Pengaduan|Pengaduan|Keluhan)\s*:|$)/is';
        preg_match($patternOpd, $clean, $matchOpd);

        $patternKatalog = '/(?:^|\n)\s*(?:Nomor\s*Katalog|No\s*Katalog|Katalog)\s*:\s*(.+?)(?=\n\s*(?:Nama|Jabatan|OPD|Instansi|Detail\s*Pengaduan|Pengaduan|Keluhan)\s*:|$)/is';
        preg_match($patternKatalog, $clean, $matchKatalog);

        $patternPengaduan = '/(?:^|\n)\s*(?:Detail\s*Pengaduan|Pengaduan|Keluhan)\s*:\s*(.+)$/is';
        preg_match($patternPengaduan, $clean, $matchPengaduan);

        $nama = isset($matchNama[1]) ? trim($matchNama[1]) : null;
        $jabatan = isset($matchJabatan[1]) ? trim($matchJabatan[1]) : null;
        $opd = isset($matchOpd[1]) ? trim($matchOpd[1]) : null;
        $katalog = isset($matchKatalog[1]) ? trim($matchKatalog[1]) : null;
        $detail = isset($matchPengaduan[1]) ? trim($matchPengaduan[1]) : null;

        // Validasi kelengkapan: Nama, Nomor Katalog, dan Detail Pengaduan wajib terisi non-empty
        if (empty($nama) || empty($katalog) || empty($detail)) {
            return null;
        }

        return [
            'nama' => $nama,
            'jabatan' => $jabatan ?: '-',
            'opd' => $opd ?: '-',
            'nomor_katalog' => $katalog,
            'detail_pengaduan' => $detail,
        ];
    }
}
