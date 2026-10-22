<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidangStaf;
use App\Models\KategoriLayanan;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman rekap laporan & tabel laporan kinerja staf/dinas.
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kategoriId = $request->query('kategori_id', 'all');

        // Query tiket dengan filter tanggal dan kategori
        $ticketQuery = Ticket::with(['user.bidangStaf', 'kategori']);

        if ($startDate) {
            $ticketQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $ticketQuery->whereDate('created_at', '<=', $endDate);
        }

        if ($kategoriId && $kategoriId !== 'all') {
            $ticketQuery->where('id_kategori_api', $kategoriId);
        }

        // Hitung total tiket & metrik
        $totalTickets = (clone $ticketQuery)->count();
        $totalResolved = (clone $ticketQuery)->where('status', 'selesai')->count();
        $totalProgress = (clone $ticketQuery)->where('status', 'progress')->count();
        $totalOpen = (clone $ticketQuery)->where('status', 'open')->count();
        $resolutionRate = $totalTickets > 0 ? round(($totalResolved / $totalTickets) * 100, 1) : 0;

        // Query hitung total tiket per staf/dinas (Rekap Kinerja)
        $staffUsers = User::whereIn('role', ['staf_bidang', 'front_desk'])
            ->with('bidangStaf')
            ->orderBy('name')
            ->get();

        $staffPerformance = $staffUsers->map(function ($staff) use ($startDate, $endDate, $kategoriId) {
            $staffTicketsQuery = Ticket::where('user_id', $staff->id);

            if ($startDate) {
                $staffTicketsQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $staffTicketsQuery->whereDate('created_at', '<=', $endDate);
            }
            if ($kategoriId && $kategoriId !== 'all') {
                $staffTicketsQuery->where('id_kategori_api', $kategoriId);
            }

            $handled = (clone $staffTicketsQuery)->count();
            $resolved = (clone $staffTicketsQuery)->where('status', 'selesai')->count();
            $inProgress = (clone $staffTicketsQuery)->where('status', 'progress')->count();
            $rate = $handled > 0 ? round(($resolved / $handled) * 100, 1) : 0;

            return (object) [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'role' => $staff->role,
                'bidang' => $staff->bidangStaf->nama_bidang ?? ($staff->role === 'front_desk' ? 'Front Desk Operasional' : 'Staf Bidang Operasional'),
                'handled' => $handled,
                'resolved' => $resolved,
                'in_progress' => $inProgress,
                'rate' => $rate,
            ];
        });

        // Daftar kategori untuk dropdown
        $categories = KategoriLayanan::where('status_aktif', true)->get();

        // Data tren grafik (Per Hari, Per Bulan, Per Tahun)
        $chartData = $this->getTrendChartData($startDate, $endDate, $kategoriId);

        return view('admin.reports.index', compact(
            'totalTickets',
            'totalResolved',
            'totalProgress',
            'totalOpen',
            'resolutionRate',
            'staffPerformance',
            'categories',
            'startDate',
            'endDate',
            'kategoriId',
            'chartData'
        ));
    }

    /**
     * Menghasilkan data tren grafik per hari, per bulan, dan per tahun
     */
    protected function getTrendChartData(?string $startDate, ?string $endDate, ?string $kategoriId): array
    {
        $baseQuery = function () use ($kategoriId) {
            $q = Ticket::query();
            if ($kategoriId && $kategoriId !== 'all') {
                $q->where('id_kategori_api', $kategoriId);
            }
            return $q;
        };

        $indoMonths = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // 1. Per Hari (Daily)
        $dayData = [];
        if ($startDate && $endDate && $startDate === $endDate) {
            // Rentang 1 hari -> Tampilkan interval jam operasional
            $targetDate = $startDate;
            $hours = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'];
            for ($i = 0; $i < count($hours) - 1; $i++) {
                $startHour = $hours[$i];
                $endHour = $hours[$i + 1];
                $masuk = $baseQuery()
                    ->whereDate('created_at', $targetDate)
                    ->whereTime('created_at', '>=', $startHour)
                    ->whereTime('created_at', '<', $endHour)
                    ->count();
                $selesai = $baseQuery()
                    ->whereDate('created_at', $targetDate)
                    ->whereTime('created_at', '>=', $startHour)
                    ->whereTime('created_at', '<', $endHour)
                    ->where('status', 'selesai')
                    ->count();
                $rate = $masuk > 0 ? round(($selesai / $masuk) * 100, 1) : 0;
                $dayData[] = [
                    'label' => $startHour,
                    'tooltip_title' => "Interval {$startHour} - {$endHour} WIB",
                    'masuk' => $masuk,
                    'selesai' => $selesai,
                    'rate' => $rate,
                ];
            }
        } elseif ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $diffDays = $start->diffInDays($end);
            
            $step = $diffDays > 14 ? (int) ceil($diffDays / 7) : 1;
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $curDate = $cursor->format('Y-m-d');
                $masuk = $baseQuery()->whereDate('created_at', $curDate)->count();
                $selesai = $baseQuery()->whereDate('created_at', $curDate)->where('status', 'selesai')->count();
                $rate = $masuk > 0 ? round(($selesai / $masuk) * 100, 1) : 0;
                $dayData[] = [
                    'label' => $cursor->format('d') . ' ' . $indoMonths[(int) $cursor->format('m')],
                    'tooltip_title' => $cursor->format('d') . ' ' . $indoMonths[(int) $cursor->format('m')] . ' ' . $cursor->format('Y'),
                    'masuk' => $masuk,
                    'selesai' => $selesai,
                    'rate' => $rate,
                ];
                $cursor->addDays($step);
            }
        } else {
            // Default: 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $curDate = $date->format('Y-m-d');
                $masuk = $baseQuery()->whereDate('created_at', $curDate)->count();
                $selesai = $baseQuery()->whereDate('created_at', $curDate)->where('status', 'selesai')->count();
                $rate = $masuk > 0 ? round(($selesai / $masuk) * 100, 1) : 0;
                $dayData[] = [
                    'label' => $date->format('d') . ' ' . $indoMonths[(int) $date->format('m')],
                    'tooltip_title' => $date->format('d') . ' ' . $indoMonths[(int) $date->format('m')] . ' ' . $date->format('Y'),
                    'masuk' => $masuk,
                    'selesai' => $selesai,
                    'rate' => $rate,
                ];
            }
        }

        // 2. Per Bulan (Monthly) - 12 Bulan Tahun Ini / Target
        $monthData = [];
        $targetYear = $endDate ? \Carbon\Carbon::parse($endDate)->year : now()->year;
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($m = 1; $m <= 12; $m++) {
            $masuk = $baseQuery()
                ->whereYear('created_at', $targetYear)
                ->whereMonth('created_at', $m)
                ->count();
            $selesai = $baseQuery()
                ->whereYear('created_at', $targetYear)
                ->whereMonth('created_at', $m)
                ->where('status', 'selesai')
                ->count();
            $rate = $masuk > 0 ? round(($selesai / $masuk) * 100, 1) : 0;
            $monthData[] = [
                'label' => $monthNames[$m - 1],
                'tooltip_title' => "Bulan " . $monthNames[$m - 1] . " " . $targetYear,
                'masuk' => $masuk,
                'selesai' => $selesai,
                'rate' => $rate,
            ];
        }

        // 3. Per Tahun (Yearly) - 5 Tahun Terakhir
        $yearData = [];
        $currentYear = now()->year;
        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $masuk = $baseQuery()->whereYear('created_at', $y)->count();
            $selesai = $baseQuery()->whereYear('created_at', $y)->where('status', 'selesai')->count();
            $rate = $masuk > 0 ? round(($selesai / $masuk) * 100, 1) : 0;
            $yearData[] = [
                'label' => (string) $y,
                'tooltip_title' => "Tahun {$y}",
                'masuk' => $masuk,
                'selesai' => $selesai,
                'rate' => $rate,
            ];
        }

        return [
            'day' => $dayData,
            'month' => $monthData,
            'year' => $yearData,
        ];
    }

    /**
     * Mengekspor laporan dataset tiket ke file Spreadsheet/Excel/CSV.
     */
    public function export(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kategoriId = $request->query('kategori_id', 'all');
        $format = $request->query('format', 'excel'); // excel / csv

        $ticketQuery = Ticket::with(['user.bidangStaf', 'kategori']);

        if ($startDate) {
            $ticketQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $ticketQuery->whereDate('created_at', '<=', $endDate);
        }

        if ($kategoriId && $kategoriId !== 'all') {
            $ticketQuery->where('id_kategori_api', $kategoriId);
        }

        $tickets = $ticketQuery->orderBy('created_at', 'desc')->get();

        // Alur Flowchart: Apakah berhasil diproses? Jika dataset kosong atau error:
        if ($tickets->isEmpty()) {
            return redirect()->route('admin.reports.index', $request->query())
                ->with('error', 'Gagal memproses ekspor laporan: Tidak ditemukan data tiket pada filter tanggal atau layanan yang dipilih.');
        }

        $extension = ($format === 'csv') ? 'csv' : 'csv';
        $filename = 'laporan-kinerja-pintas-' . date('Ymd-His') . '.' . $extension;

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return new StreamedResponse(function () use ($tickets) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 agar Excel membuka teks dan karakter dengan sempurna
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Baris Judul Laporan
            fputcsv($handle, ['REKAP LAPORAN PENYELESAIAN TIKET HELPDESK PINTAS'], ';');
            fputcsv($handle, ['Tanggal Ekspor', date('Y-m-d H:i:s')], ';');
            fputcsv($handle, ['Total Data', $tickets->count() . ' Tiket'], ';');
            fputcsv($handle, [], ';');

            // Header kolom
            fputcsv($handle, [
                'ID Tiket',
                'Tanggal Masuk',
                'Nama Pemohon',
                'No WhatsApp',
                'Kategori Layanan',
                'Kendala / Permohonan',
                'Staf Penanggung Jawab',
                'Bidang / Departemen',
                'Status',
                'Waktu Ditugaskan'
            ], ';');

            foreach ($tickets as $t) {
                fputcsv($handle, [
                    '#TKT-' . $t->id,
                    $t->created_at ? $t->created_at->format('Y-m-d H:i') : '-',
                    $t->nama_pemohon ?? '-',
                    $t->nomor_wa ?? '-',
                    $t->kategori->nama_layanan ?? 'Umum',
                    str_replace(["\r", "\n", "\t"], ' ', $t->keluhan ?? '-'),
                    $t->user->name ?? 'Belum Ditugaskan',
                    $t->user->bidangStaf->nama_bidang ?? ($t->user ? ($t->user->role === 'front_desk' ? 'Front Desk' : 'Staf Bidang') : '-'),
                    strtoupper($t->status),
                    $t->assigned_at ? $t->assigned_at->format('Y-m-d H:i') : '-'
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Menghasilkan tampilan cetak / Print PDF resmi dengan kop surat laporan PINTAS.
     */
    public function printReport(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kategoriId = $request->query('kategori_id', 'all');

        $ticketQuery = Ticket::with(['user.bidangStaf', 'kategori']);

        if ($startDate) {
            $ticketQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $ticketQuery->whereDate('created_at', '<=', $endDate);
        }

        if ($kategoriId && $kategoriId !== 'all') {
            $ticketQuery->where('id_kategori_api', $kategoriId);
        }

        $tickets = $ticketQuery->orderBy('created_at', 'desc')->get();
        $totalTickets = $tickets->count();
        $totalResolved = $tickets->where('status', 'selesai')->count();
        $totalProgress = $tickets->where('status', 'progress')->count();
        $totalOpen = $tickets->where('status', 'open')->count();

        $staffUsers = User::whereIn('role', ['staf_bidang', 'front_desk'])
            ->with('bidangStaf')
            ->orderBy('name')
            ->get();

        $staffPerformance = $staffUsers->map(function ($staff) use ($tickets) {
            $handled = $tickets->where('user_id', $staff->id)->count();
            $resolved = $tickets->where('user_id', $staff->id)->where('status', 'selesai')->count();
            $rate = $handled > 0 ? round(($resolved / $handled) * 100, 1) : 0;

            return (object) [
                'name' => $staff->name,
                'role' => $staff->role,
                'bidang' => $staff->bidangStaf->nama_bidang ?? ($staff->role === 'front_desk' ? 'Front Desk' : 'Staf Bidang'),
                'handled' => $handled,
                'resolved' => $resolved,
                'rate' => $rate,
            ];
        });

        return view('admin.reports.print', compact(
            'tickets',
            'totalTickets',
            'totalResolved',
            'totalProgress',
            'totalOpen',
            'staffPerformance',
            'startDate',
            'endDate'
        ));
    }
}
