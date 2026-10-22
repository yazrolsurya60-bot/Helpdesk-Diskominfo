<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kinerja Helpdesk PINTAS - Diskominfo</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            background: #fff;
            font-size: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: normal;
            margin: 4px 0 0 0;
            color: #475569;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 11px;
            color: #334155;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .kpi-card {
            border: 1px solid #cbd5e1;
            padding: 10px;
            border-radius: 6px;
            background: #f8fafc;
        }

        .kpi-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }

        .kpi-val {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            color: #334155;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-primary {
            background: #dbeafe;
            color: #1e40af;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print"
        style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; background: #e2e8f0; padding: 10px 16px; border-radius: 6px;">
        <span><strong>Pratinjau Dokumen Cetak / PDF</strong> (Gunakan fungsi cetak browser untuk menyimpan sebagai
            PDF)</span>
        <button onclick="window.print()"
            style="background: #1e3a8a; color: #fff; border: none; padding: 6px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Cetak / Simpan PDF
        </button>
    </div>

    <div class="header">
        <h1>Pemerintah Kabupaten / Kota — Dinas Komunikasi dan Informatika</h1>
        <h2>Laporan Rekap Kinerja &amp; Penanganan Tiket Helpdesk (Sistem PINTAS)</h2>
    </div>

    <div class="meta">
        <div>
            <strong>Periode Laporan:</strong>
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }}
            s/d
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}
        </div>
        <div>
            <strong>Dicetak Pada:</strong> {{ now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

    <!-- Ringkasan KPI -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-title">Total Tiket Masuk</div>
            <div class="kpi-val">{{ $totalTickets }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Tiket Selesai</div>
            <div class="kpi-val" style="color: #16a34a;">{{ $totalResolved }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Sedang Ditangani</div>
            <div class="kpi-val" style="color: #d97706;">{{ $totalProgress }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Tingkat Penyelesaian</div>
            <div class="kpi-val" style="color: #2563eb;">
                {{ $totalTickets > 0 ? round(($totalResolved / $totalTickets) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    <h3 style="font-size: 13px; margin: 16px 0 8px 0; text-transform: uppercase;">1. Tabel Rekap Kinerja Staf &amp;
        Bidang</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Nama Personel</th>
                <th>Bidang / Unit Kerja</th>
                <th>Peran</th>
                <th class="text-center">Tiket Ditangani</th>
                <th class="text-center">Tiket Selesai</th>
                <th class="text-center">Tingkat Sukses</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffPerformance as $idx => $st)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $st->name }}</strong></td>
                    <td>{{ $st->bidang }}</td>
                    <td>{{ $st->role === 'front_desk' ? 'Front Desk' : 'Staf Bidang' }}</td>
                    <td class="text-center">{{ $st->handled }}</td>
                    <td class="text-center">{{ $st->resolved }}</td>
                    <td class="text-center">{{ $st->rate }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data staf bidang terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="font-size: 13px; margin: 24px 0 8px 0; text-transform: uppercase;">2. Daftar Tiket Penugasan Terkait</h3>
    <table>
        <thead>
            <tr>
                <th>No Tiket</th>
                <th>Tgl Masuk</th>
                <th>Pemohon</th>
                <th>Kategori Layanan</th>
                <th>Kendala</th>
                <th>Penanggung Jawab</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $t)
                <tr>
                    <td><strong>#TKT-{{ $t->id }}</strong></td>
                    <td>{{ $t->created_at ? $t->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $t->nama_pemohon ?? '-' }}</td>
                    <td>{{ $t->kategori->nama_layanan ?? 'Umum' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($t->keluhan, 60) }}</td>
                    <td>{{ $t->user->name ?? 'Belum Ditugaskan' }}</td>
                    <td>
                        @if($t->status === 'selesai')
                            <span class="badge badge-success">Selesai</span>
                        @elseif($t->status === 'progress')
                            <span class="badge badge-warning">In Progress</span>
                        @else
                            <span class="badge badge-primary">Open</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data tiket pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div style="font-weight: bold; margin-top: 4px;">Administrator Helpdesk PINTAS</div>
            <div style="height: 60px;"></div>
            <div style="border-bottom: 1px solid #000; font-weight: bold;">{{ auth()->user()->name ?? 'Administrator' }}
            </div>
            <div style="font-size: 10px; color: #475569; margin-top: 2px;">Dinas Komunikasi dan Informatika</div>
        </div>
    </div>
</body>

</html>