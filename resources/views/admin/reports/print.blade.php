<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kinerja Helpdesk PINTAS - Diskominfo</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            background: #fff;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header h1 {
            font-size: 17px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }

        .header h2 {
            font-size: 13px;
            font-weight: normal;
            margin: 4px 0 0 0;
            color: #475569;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 11px;
            color: #334155;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .kpi-card {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
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

        /* Chart Section Styling */
        .chart-section {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 24px;
            background: #ffffff;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .chart-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin: 0;
        }

        .chart-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        .chart-legend {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        .legend-masuk {
            background-color: #2563eb !important;
        }

        .legend-selesai {
            background-color: #10b981 !important;
        }

        .chart-wrapper {
            position: relative;
            height: 175px;
            margin-top: 14px;
            margin-bottom: 10px;
        }

        .chart-y-axis {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            pointer-events: none;
        }

        .chart-grid-line {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .chart-y-val {
            width: 28px;
            text-align: right;
            padding-right: 6px;
            font-size: 9px;
            color: #64748b;
            font-family: monospace;
        }

        .chart-line {
            flex: 1;
            height: 1px;
            background-color: #e2e8f0;
        }

        .chart-bars-container {
            position: absolute;
            top: 0;
            left: 32px;
            right: 8px;
            bottom: 0;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            gap: 6px;
        }

        .chart-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            justify-content: flex-end;
        }

        .chart-col-bars {
            width: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 3px;
            height: calc(100% - 24px);
        }

        .bar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            justify-content: flex-end;
        }

        .bar-val {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
            line-height: 1;
        }

        .bar-masuk {
            width: 14px;
            background-color: #2563eb !important;
            border-radius: 2px 2px 0 0;
        }

        .bar-selesai {
            width: 14px;
            background-color: #10b981 !important;
            border-radius: 2px 2px 0 0;
        }

        .chart-x-label {
            height: 20px;
            margin-top: 4px;
            font-size: 9px;
            color: #475569;
            white-space: nowrap;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .chart-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 0;
            font-size: 10px;
        }

        .chart-summary-table th,
        .chart-summary-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 10px;
        }

        /* General Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
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
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background: #dcfce7 !important;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7 !important;
            color: #92400e;
        }

        .badge-primary {
            background: #dbeafe !important;
            color: #1e40af;
        }

        .footer {
            margin-top: 36px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 220px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .chart-section {
                border-color: #94a3b8;
            }
        }
    </style>
</head>

<body>
    <!-- Toolbar Aksi Dokumen (Hanya tampil di layar pratinjau, disembunyikan saat dicetak) -->
    <div class="no-print"
        style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #cbd5e1; padding: 10px 16px; border-radius: 6px; flex-wrap: wrap; gap: 8px;">
        <div style="font-size: 11px; color: #334155;">
            <strong>Pratinjau Dokumen Cetak / PDF</strong> — Format grafik dan filter data mengikuti pengaturan pada menu Rekap Laporan.
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.close()"
                style="background: #ffffff; color: #475569; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-weight: 500; font-size: 11px;">
                Tutup
            </button>
            <button onclick="window.print()"
                style="background: #1e3a8a; color: #fff; border: none; padding: 6px 18px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 11px; box-shadow: 0 1px 3px rgba(0,0,0,0.15);">
                Cetak Dokumen
            </button>
        </div>
    </div>

    <!-- Kop Surat Resmi -->
    <div class="header">
        <h1>Pemerintah Provinsi Kalimantan Barat — Dinas Komunikasi dan Informatika</h1>
        <h2>Laporan Rekap Kinerja &amp; Penanganan Tiket Helpdesk (Sistem PINTAS)</h2>
    </div>

    <!-- Informasi Meta Laporan & Filter -->
    <div class="meta">
        <div>
            <strong>Periode Laporan:</strong>
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Semua Periode' }}
            s/d
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}
            <span style="margin-left: 14px;">
                <strong>Kategori Layanan:</strong>
                {{ $selectedKategori->nama_layanan ?? 'Semua Layanan' }}
            </span>
            <span style="margin-left: 14px;">
                <strong>Periode Grafik:</strong>
                {{ ($period ?? 'day') === 'month' ? 'Bulanan' : (($period ?? 'day') === 'year' ? 'Tahunan' : 'Harian') }}
            </span>
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

    <!-- ============================================== -->
    <!-- 1. GRAFIK TREN & PENYELESAIAN TIKET            -->
    <!-- ============================================== -->
    <div class="chart-section">
        <div class="chart-header">
            <div>
                <h3 class="chart-title">1. Grafik Tren &amp; Penyelesaian Tiket ({{ ($period ?? 'day') === 'month' ? 'Bulanan' : (($period ?? 'day') === 'year' ? 'Tahunan' : 'Harian') }})</h3>
                <div id="chartSubtitle" class="chart-subtitle">
                    @if(($period ?? 'day') === 'month')
                        Visualisasi tren bulanan tiket masuk &amp; tiket diselesaikan tahun ini
                    @elseif(($period ?? 'day') === 'year')
                        Visualisasi tren tahunan tiket masuk &amp; tiket diselesaikan (5 tahun terakhir)
                    @else
                        Visualisasi perbandingan tiket masuk &amp; tiket diselesaikan per interval waktu / hari
                    @endif
                </div>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color legend-masuk"></span>
                    <strong>Tiket Masuk</strong>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-selesai"></span>
                    <strong>Tiket Diselesaikan</strong>
                </div>
            </div>
        </div>

        <!-- Area Visualisasi Batang Grafik -->
        <div class="chart-wrapper">
            <!-- Skala Sumbu Y -->
            <div id="chartYAxis" class="chart-y-axis"></div>
            <!-- Kluster Batang Grafik Dinamis -->
            <div id="chartBarsContainer" class="chart-bars-container"></div>
        </div>

        <!-- Tabel Ringkasan Angka Grafik (Print-Friendly) -->
        <table class="chart-summary-table" id="chartSummaryTable">
            <thead id="chartSummaryThead"></thead>
            <tbody id="chartSummaryTbody"></tbody>
        </table>
    </div>

    <!-- ============================================== -->
    <!-- 2. TABEL REKAP KINERJA STAF & BIDANG           -->
    <!-- ============================================== -->
    <h3 style="font-size: 12px; margin: 20px 0 8px 0; text-transform: uppercase;">2. Tabel Rekap Kinerja Staf &amp; Bidang</h3>
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
                    <td class="text-center font-bold">{{ $st->rate }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data staf bidang terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ============================================== -->
    <!-- 3. DAFTAR TIKET PENUGASAN TERKAIT              -->
    <!-- ============================================== -->
    <h3 style="font-size: 12px; margin: 24px 0 8px 0; text-transform: uppercase;">3. Daftar Tiket Penugasan Terkait</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">No Tiket</th>
                <th style="width: 14%;">Tgl Masuk</th>
                <th style="width: 18%;">Pemohon</th>
                <th style="width: 16%;">Kategori Layanan</th>
                <th>Kendala</th>
                <th style="width: 15%;">Penanggung Jawab</th>
                <th style="width: 10%;" class="text-center">Status</th>
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
                    <td class="text-center">
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

    <!-- Tanda Tangan Resmi Pengesahan -->
    <div class="footer">
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div style="font-weight: bold; margin-top: 4px;">Kepala / Administrator Helpdesk</div>
            <div style="height: 60px;"></div>
            <div style="border-bottom: 1px solid #0f172a; font-weight: bold;">
                {{ auth()->user()->name ?? 'Administrator' }}
            </div>
            <div style="font-size: 10px; color: #475569; margin-top: 2px;">Dinas Komunikasi dan Informatika</div>
        </div>
    </div>

    <!-- Script Pengolah Grafik Tren Terintegrasi Filter -->
    <script>
        const trendData = @json($chartData ?? ['day' => [], 'month' => [], 'year' => []]);
        let currentPeriod = '{{ $period ?? "day" }}';
        if (!trendData[currentPeriod] || trendData[currentPeriod].length === 0) {
            currentPeriod = 'day';
        }

        function renderPrintChart(period) {
            currentPeriod = period;
            const data = trendData[period] || [];

            // 1. Tentukan skala sumbu Y maksimal
            let maxVal = 0;
            data.forEach(item => {
                if (item.masuk > maxVal) maxVal = item.masuk;
                if (item.selesai > maxVal) maxVal = item.selesai;
            });

            let maxScale = 5;
            if (maxVal > 0) {
                maxScale = Math.ceil(maxVal * 1.15);
                if (maxScale <= 5) maxScale = 5;
                else if (maxScale <= 10) maxScale = 10;
                else if (maxScale <= 20) maxScale = 20;
                else if (maxScale <= 40) maxScale = 40;
                else if (maxScale <= 60) maxScale = 60;
                else if (maxScale <= 80) maxScale = 80;
                else if (maxScale <= 100) maxScale = 100;
                else maxScale = Math.ceil(maxScale / 20) * 20;
            }

            // 4. Render Gridlines & Nilai Sumbu Y
            const chartYAxis = document.getElementById('chartYAxis');
            if (chartYAxis) {
                const step = maxScale / 4;
                const yValues = [maxScale, Math.round(step * 3), Math.round(step * 2), Math.round(step), 0];
                chartYAxis.innerHTML = yValues.map((val, idx) => `
                    <div class="chart-grid-line">
                        <span class="chart-y-val">${val}</span>
                        <div class="chart-line" style="${idx === 4 ? 'background-color: #94a3b8;' : ''}"></div>
                    </div>
                `).join('');
            }

            // 5. Render Batang Grafik
            const chartBarsContainer = document.getElementById('chartBarsContainer');
            if (!chartBarsContainer) return;

            if (data.length === 0) {
                chartBarsContainer.innerHTML = '<div style="width: 100%; text-align: center; color: #94a3b8; font-size: 11px; padding-top: 60px;">Tidak ada data tiket pada periode ini.</div>';
                return;
            }

            chartBarsContainer.innerHTML = data.map(item => {
                const hMasuk = maxScale > 0 ? (item.masuk / maxScale) * 100 : 0;
                const hSelesai = maxScale > 0 ? (item.selesai / maxScale) * 100 : 0;
                const styleMasuk = item.masuk > 0 ? `height: ${Math.max(hMasuk, 4).toFixed(1)}%;` : 'height: 2px; opacity: 0.3;';
                const styleSelesai = item.selesai > 0 ? `height: ${Math.max(hSelesai, 4).toFixed(1)}%;` : 'height: 2px; opacity: 0.3;';

                return `
                    <div class="chart-col">
                        <div class="chart-col-bars">
                            <div class="bar-container">
                                ${item.masuk > 0 ? `<span class="bar-val" style="color: #1e40af;">${item.masuk}</span>` : ''}
                                <div class="bar-masuk" style="${styleMasuk}"></div>
                            </div>
                            <div class="bar-container">
                                ${item.selesai > 0 ? `<span class="bar-val" style="color: #166534;">${item.selesai}</span>` : ''}
                                <div class="bar-selesai" style="${styleSelesai}"></div>
                            </div>
                        </div>
                        <span class="chart-x-label" title="${item.tooltip_title || item.label}">${item.label}</span>
                    </div>
                `;
            }).join('');

            // 6. Render Ringkasan Tabel Bawah Grafik
            const thead = document.getElementById('chartSummaryThead');
            const tbody = document.getElementById('chartSummaryTbody');
            if (thead && tbody) {
                thead.innerHTML = `
                    <tr>
                        <th style="width: 22%;">Kategori Data</th>
                        ${data.map(d => `<th class="text-center">${d.label}</th>`).join('')}
                        <th class="text-center" style="background-color: #e2e8f0; width: 10%;">Total</th>
                    </tr>
                `;

                const totalM = data.reduce((acc, cur) => acc + cur.masuk, 0);
                const totalS = data.reduce((acc, cur) => acc + cur.selesai, 0);
                const avgRate = totalM > 0 ? Math.round((totalS / totalM) * 100) : 0;

                tbody.innerHTML = `
                    <tr>
                        <td>
                            <span class="legend-color legend-masuk" style="display:inline-block; vertical-align:middle; margin-right:4px;"></span>
                            <strong>Tiket Masuk</strong>
                        </td>
                        ${data.map(d => `<td class="text-center font-bold" style="color: #1e40af;">${d.masuk}</td>`).join('')}
                        <td class="text-center font-bold" style="background-color: #f8fafc; color: #1e40af;">${totalM}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="legend-color legend-selesai" style="display:inline-block; vertical-align:middle; margin-right:4px;"></span>
                            <strong>Tiket Diselesaikan</strong>
                        </td>
                        ${data.map(d => `<td class="text-center font-bold" style="color: #166534;">${d.selesai}</td>`).join('')}
                        <td class="text-center font-bold" style="background-color: #f8fafc; color: #166534;">${totalS}</td>
                    </tr>
                    <tr>
                        <td><strong>Tingkat Penyelesaian</strong></td>
                        ${data.map(d => `<td class="text-center">${d.rate}%</td>`).join('')}
                        <td class="text-center font-bold" style="background-color: #f8fafc; color: #2563eb;">${avgRate}%</td>
                    </tr>
                `;
            }
        }

        // Jalankan render awal saat halaman siap
        document.addEventListener('DOMContentLoaded', function () {
            renderPrintChart(currentPeriod);
        });
    </script>
</body>

</html>
