@extends('layouts.admin')
@section('title', 'Reports & Analytics — Rekap Laporan')
@section('content')
  <div class="flex flex-col w-full">
    <div class="px-margin_container py-stack_lg flex flex-col gap-stack_lg">

      <!-- Alert Pesan Error / Sukses Sesuai Alur Flowchart -->
      @if(session('error'))
        <div class="p-4 rounded-xl bg-error-container text-on-error-container flex items-center justify-between gap-3 shadow-sm border border-error/20">
          <div class="flex items-center gap-2.5">
            <span class="material-symbols-outlined text-[22px] text-error">error</span>
            <span class="font-body-sm text-body-sm font-medium">{{ session('error') }}</span>
          </div>
          <button type="button" onclick="this.parentElement.remove()" class="text-on-error-container/70 hover:text-on-error-container">
            <span class="material-symbols-outlined text-[18px]">close</span>
          </button>
        </div>
      @endif

      @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-between gap-3 shadow-sm border border-emerald-200">
          <div class="flex items-center gap-2.5">
            <span class="material-symbols-outlined text-[22px] text-emerald-600">check_circle</span>
            <span class="font-body-sm text-body-sm font-medium">{{ session('success') }}</span>
          </div>
          <button type="button" onclick="this.parentElement.remove()" class="text-emerald-800/70 hover:text-emerald-800">
            <span class="material-symbols-outlined text-[18px]">close</span>
          </button>
        </div>
      @endif

      <!-- Header Section with Title, Subtitle and Action Menu -->
      <div
        class="flex flex-col md:flex-row md:items-center justify-between gap-stack_md bg-surface-container-lowest p-stack_lg rounded-xl shadow-sm">
        <div class="flex flex-col gap-1">
          <div class="flex items-center gap-2">
            <span class="text-on-surface-variant font-label-sm text-label-sm flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-primary inline-block"></span> Terakhir disinkronkan: Data Real-Time
            </span>
          </div>
          <h1 class="font-headline-lg text-headline-lg text-on-background tracking-tight font-bold">Reports &amp; Rekap Laporan</h1>
          <p class="font-body-md text-body-md text-on-surface-variant">Laporan performa kerja staf, dinas/bidang, dan statistik penyelesaian tiket layanan PINTAS</p>
        </div>

        <!-- Tombol Export Laporan (Sesuai Sequence Diagram) -->
        <div class="flex items-center gap-stack_sm flex-wrap">
          <div class="relative" id="exportDropdownContainer">
            <button
              class="inline-flex items-center gap-2 px-stack_md py-2.5 rounded-lg bg-primary text-on-primary font-headline-sm text-headline-sm hover:bg-primary/90 hover:shadow-md transition-all cursor-pointer"
              id="exportMenuBtn" type="button">
              <span class="material-symbols-outlined text-[18px]">ios_share</span>
              <span>Export Laporan</span>
              <span class="material-symbols-outlined text-[16px] transition-transform duration-200"
                id="exportArrow">keyboard_arrow_down</span>
            </button>
            <div class="hidden absolute right-0 mt-2 w-56 bg-surface-container-lowest rounded-xl shadow-xl p-1.5 z-30 border border-outline-variant/30"
              id="exportMenu">
              <a class="flex items-center gap-2.5 px-3 py-2.5 text-on-surface rounded-lg hover:bg-surface-container-low transition-colors font-body-sm text-body-sm"
                href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate, 'kategori_id' => $kategoriId]) }}"
                target="_blank" title="Cetak / Pratinjau Dokumen PDF">
                <span class="material-symbols-outlined text-error text-[20px]">picture_as_pdf</span>
                <span class="font-medium">Dokumen PDF (.pdf)</span>
              </a>
              <a class="flex items-center gap-2.5 px-3 py-2.5 text-on-surface rounded-lg hover:bg-surface-container-low transition-colors font-body-sm text-body-sm"
                href="{{ route('admin.reports.export', ['format' => 'excel', 'start_date' => $startDate, 'end_date' => $endDate, 'kategori_id' => $kategoriId]) }}"
                title="Unduh Spreadsheet Excel">
                <span class="material-symbols-outlined text-primary text-[20px]">table_view</span>
                <span class="font-medium">Spreadsheet Excel (.csv)</span>
              </a>
              <a class="flex items-center gap-2.5 px-3 py-2.5 text-on-surface rounded-lg hover:bg-surface-container-low transition-colors font-body-sm text-body-sm"
                href="{{ route('admin.reports.export', ['format' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate, 'kategori_id' => $kategoriId]) }}"
                title="Unduh Data Mentah CSV">
                <span class="material-symbols-outlined text-secondary text-[20px]">csv</span>
                <span class="font-medium">Data Mentah CSV (.csv)</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Strictly 2 Top KPI Cards (Terkoneksi Data Database) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
        <!-- Card 1: Total Tickets -->
        <div
          class="relative overflow-hidden bg-surface-container-lowest p-gutter rounded-xl shadow-sm hover:shadow-md transition-shadow">
          <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary-container"></div>
          <div class="flex items-start justify-between">
            <div class="flex flex-col gap-1">
              <div class="flex items-center gap-2">
                <span
                  class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">Total
                  TIKET</span>
              </div>
              <div class="flex items-baseline gap-3 mt-2">
                <span
                  class="font-headline-lg text-[36px] font-bold text-on-surface tracking-tight leading-none">{{ $totalTickets }}</span>
                <span class="text-xs text-on-surface-variant font-medium">Tiket Tercatat</span>
              </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary shadow-sm">
              <span class="material-symbols-outlined text-[26px]">confirmation_number</span>
            </div>
          </div>
        </div>
        <!-- Card 2: Total Resolved -->
        <div
          class="relative overflow-hidden bg-surface-container-lowest p-gutter rounded-xl shadow-sm hover:shadow-md transition-shadow">
          <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#10b981]"></div>
          <div class="flex items-start justify-between">
            <div class="flex flex-col gap-1">
              <div class="flex items-center gap-2">
                <span
                  class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">Total
                  Terselesaikan</span>
              </div>
              <div class="flex items-baseline gap-3 mt-2">
                <span
                  class="font-headline-lg text-[36px] font-bold text-on-surface tracking-tight leading-none">{{ $totalResolved }}</span>
                <span class="text-xs text-on-surface-variant font-medium">Tiket Selesai</span>
              </div>
            </div>
            <div
              class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-[#10b981] shadow-sm">
              <span class="material-symbols-outlined text-[26px]">check_circle</span>
            </div>
          </div>
          <!-- Resolution progress track -->
          <div class="mt-4 pt-3 flex flex-col gap-1.5">
            <div class="flex justify-between items-center font-label-sm text-label-sm text-on-surface-variant">
              <span>Rasio Penyelesaian</span>
              <span class="font-semibold text-on-surface">{{ $totalResolved }} / {{ $totalTickets }} Tiket ({{ $resolutionRate }}%)</span>
            </div>
            <div class="w-full h-2 rounded-full bg-surface-container overflow-hidden">
              <div class="h-full bg-[#10b981] rounded-full transition-all duration-500" style="width: {{ $resolutionRate }}%;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- FORM FILTER: RENTANG WAKTU TANGGAL (DISEBELAH KIRI SEMUA LAYANAN) -->
      <form method="GET" action="{{ route('admin.reports.index') }}"
        class="bg-surface-container-lowest p-4 sm:p-5 rounded-xl shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4 border border-outline-variant/30">
        
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
          <!-- 1. Form Input Rentang Waktu Tanggal (Disebelah Kiri "Semua Layanan") -->
          <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
            <div class="flex items-center bg-surface-container-low rounded-lg px-3 py-1.5 border border-outline-variant/40 flex-1 sm:flex-initial">
              <span class="material-symbols-outlined text-[18px] text-on-surface-variant mr-1.5">calendar_month</span>
              <label for="startDate" class="text-xs font-semibold text-on-surface-variant mr-1.5">Dari:</label>
              <input type="date" id="startDate" name="start_date" value="{{ $startDate }}"
                class="bg-transparent font-body-sm text-body-sm text-on-surface focus:outline-none cursor-pointer">
            </div>
            <span class="text-on-surface-variant text-xs font-medium shrink-0">s/d</span>
            <div class="flex items-center bg-surface-container-low rounded-lg px-3 py-1.5 border border-outline-variant/40 flex-1 sm:flex-initial">
              <span class="material-symbols-outlined text-[18px] text-on-surface-variant mr-1.5">event</span>
              <label for="endDate" class="text-xs font-semibold text-on-surface-variant mr-1.5">Sampai:</label>
              <input type="date" id="endDate" name="end_date" value="{{ $endDate }}"
                class="bg-transparent font-body-sm text-body-sm text-on-surface focus:outline-none cursor-pointer">
            </div>
          </div>

          <!-- 2. Dropdown Kategori / "Semua Layanan" (Disebelah Kanan Rentang Tanggal) -->
          <div class="relative w-full sm:w-auto">
            <label class="sr-only" for="categoryFilter">Kategori Layanan</label>
            <div class="flex items-center bg-surface-container-low rounded-lg px-3 py-1.5 border border-outline-variant/40">
              <span class="material-symbols-outlined text-[18px] text-on-surface-variant mr-1.5">tune</span>
              <select name="kategori_id"
                class="bg-transparent font-body-sm text-body-sm text-on-surface focus:outline-none cursor-pointer pr-4 py-0.5 w-full sm:w-auto"
                id="categoryFilter">
                <option value="all">Semua Layanan</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ $kategoriId == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nama_layanan }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- 3. Tombol Aksi Terapkan Filter & Reset -->
          <div class="flex items-center gap-2">
            <button type="submit"
              class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary text-on-primary font-headline-sm text-xs font-semibold hover:bg-primary/90 shadow-sm transition-all cursor-pointer">
              <span class="material-symbols-outlined text-[16px]">filter_alt</span>
              <span>Terapkan Filter</span>
            </button>

            @if($startDate || $endDate || ($kategoriId && $kategoriId !== 'all'))
              <a href="{{ route('admin.reports.index') }}"
                class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-surface-container-low text-on-surface-variant hover:text-on-surface text-xs font-medium transition-colors border border-outline-variant/30"
                title="Hapus Filter">
                <span class="material-symbols-outlined text-[16px]">close</span>
                <span>Reset</span>
              </a>
            @endif
          </div>
        </div>

        <div class="text-xs text-on-surface-variant flex items-center gap-1">
          <span class="material-symbols-outlined text-[16px]">info</span>
          <span>Filter otomatis membatasi data KPI, tabel rekap kinerja, dan berkas ekspor.</span>
        </div>
      </form>

      <!-- TABEL LAPORAN KINERJA (DILETAKKAN SEBELUM TABEL GRAFIK) -->
      <!-- Desain mengacu pada referensi tabel di menu "User Management" -->
      <div class="bg-surface-container-lowest rounded-xl shadow-sm flex flex-col overflow-hidden border border-outline-variant/20">
        <!-- Table Toolbar Header -->
        <div class="p-stack_lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-stack_md border-b border-outline-variant/20">
          <div class="flex items-center gap-stack_sm">
            <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-primary shadow-sm shrink-0">
              <span class="material-symbols-outlined text-[22px]">badge</span>
            </div>
            <div class="flex flex-col">
              <div class="flex items-center gap-2">
                <h2 class="font-headline-md text-headline-md font-bold text-on-surface">Tabel Laporan Kinerja Staf &amp; Bidang</h2>
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-semibold">
                  {{ $staffPerformance->count() }} Personel
                </span>
              </div>
              <p class="text-xs text-on-surface-variant font-body-sm">
                Rekapitulasi beban kerja, penanganan tiket, dan rasio penyelesaian staf helpdesk
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-on-surface-variant font-medium">
              Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Semua' }} s/d {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Sekarang' }}
            </span>
          </div>
        </div>

        <!-- Data Table (Gaya User Management) -->
        <div class="w-full overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[800px]" id="performanceTable">
            <thead class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
              <tr>
                <th class="py-3 px-stack_lg font-semibold" scope="col">Nama Staf &amp; Email</th>
                <th class="py-3 px-stack_md font-semibold" scope="col">Bidang / Unit Kerja</th>
                <th class="py-3 px-stack_md font-semibold" scope="col">Peran (Role)</th>
                <th class="py-3 px-stack_md font-semibold text-center" scope="col">Tiket Ditangani</th>
                <th class="py-3 px-stack_md font-semibold text-center" scope="col">Tiket Selesai</th>
                <th class="py-3 px-stack_md font-semibold text-center" scope="col">Tingkat Penyelesaian</th>
                <th class="py-3 px-stack_lg text-right font-semibold" scope="col">Status Kinerja</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20 text-body-sm font-body-sm text-on-surface">
              @forelse($staffPerformance as $staf)
                <tr class="hover:bg-surface-container-low/70 transition-colors">
                  <!-- Kolom Staf dengan Avatar Inisial (Mirip User Management) -->
                  <td class="py-3.5 px-stack_lg">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-full bg-primary-fixed text-primary font-headline-sm text-headline-sm font-bold flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr($staf->name, 0, 2)) }}
                      </div>
                      <div class="flex flex-col min-w-0">
                        <span class="font-headline-sm text-headline-sm font-semibold text-on-surface truncate">{{ $staf->name }}</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1 truncate">
                          <span class="material-symbols-outlined text-[14px]">mail</span>
                          {{ $staf->email }}
                        </span>
                      </div>
                    </div>
                  </td>

                  <!-- Kolom Bidang / Unit Kerja -->
                  <td class="py-3.5 px-stack_md font-medium text-on-surface">
                    {{ $staf->bidang }}
                  </td>

                  <!-- Kolom Peran (Role Badge) -->
                  <td class="py-3.5 px-stack_md">
                    @if($staf->role === 'front_desk')
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 font-label-sm text-label-sm uppercase tracking-wider font-semibold">
                        Front Desk
                      </span>
                    @else
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 font-label-sm text-label-sm uppercase tracking-wider font-semibold">
                        Staf Bidang
                      </span>
                    @endif
                  </td>

                  <!-- Kolom Tiket Ditangani -->
                  <td class="py-3.5 px-stack_md font-bold text-on-surface text-center">
                    {{ $staf->handled }}
                  </td>

                  <!-- Kolom Tiket Selesai -->
                  <td class="py-3.5 px-stack_md font-bold text-emerald-600 text-center">
                    {{ $staf->resolved }}
                  </td>

                  <!-- Kolom Tingkat Penyelesaian -->
                  <td class="py-3.5 px-stack_md text-center">
                    <div class="flex flex-col items-center gap-1">
                      <span class="font-semibold text-on-surface text-xs">{{ $staf->rate }}%</span>
                      <div class="w-24 h-1.5 rounded-full bg-surface-container overflow-hidden">
                        <div class="h-full bg-primary rounded-full" style="width: {{ $staf->rate }}%;"></div>
                      </div>
                    </div>
                  </td>

                  <!-- Kolom Status Performa -->
                  <td class="py-3.5 px-stack_lg text-right">
                    @if($staf->handled > 0 && $staf->rate >= 75)
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Optimal
                      </span>
                    @elseif($staf->handled > 0)
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Aktif
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant text-xs font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-outline"></span> Standby
                      </span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="py-12 text-center text-on-surface-variant">
                    <div class="flex flex-col items-center justify-center gap-2">
                      <span class="material-symbols-outlined text-[40px] text-outline-variant">person_off</span>
                      <p class="font-medium text-sm">Tidak ada staf teknis terdaftar dalam sistem.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- GRAFIK TREN & PENYELESAIAN TIKET (DILETAKKAN SETELAH TABEL LAPORAN KINERJA) -->
      <div class="bg-surface-container-lowest p-gutter rounded-xl shadow-sm flex flex-col gap-stack_lg border border-outline-variant/20">
        <!-- Chart Controls & Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-stack_md pb-stack_sm">
          <div class="flex flex-col">
            <h2 class="font-headline-md text-headline-md text-on-surface tracking-tight font-bold">Grafik Tren &amp; Penyelesaian Tiket</h2>
            <span id="chartSubtitle" class="font-body-sm text-body-sm text-on-surface-variant">Visualisasi perbandingan tiket masuk &amp; tiket diselesaikan berdasarkan interval waktu operasional</span>
          </div>
          <div class="flex flex-wrap items-center gap-stack_sm">
            <!-- Time-period Pill Buttons -->
            <div class="inline-flex p-1 bg-surface-container rounded-lg" role="tablist">
              <button
                class="px-3.5 py-1.5 rounded-md font-label-md text-label-md bg-surface-container-lowest text-primary font-bold shadow-sm transition-all cursor-pointer"
                id="btnFilterDay" type="button">Hari ini / Per Hari</button>
              <button
                class="px-3.5 py-1.5 rounded-md font-label-md text-label-md text-on-surface-variant hover:text-on-surface transition-all cursor-pointer"
                id="btnFilterMonth" type="button">Per Bulan</button>
              <button
                class="px-3.5 py-1.5 rounded-md font-label-md text-label-md text-on-surface-variant hover:text-on-surface transition-all cursor-pointer"
                id="btnFilterYear" type="button">Per Tahun</button>
            </div>
          </div>
        </div>

        <!-- Chart Legend & Status Meta -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
          <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
              <span class="w-3.5 h-3.5 rounded bg-primary-container"></span>
              <span class="font-body-sm text-body-sm font-semibold text-on-surface">Tiket Masuk</span>
              <span class="font-label-sm text-label-sm text-on-surface-variant">(Total Masuk)</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3.5 h-3.5 rounded bg-[#10b981]"></span>
              <span class="font-body-sm text-body-sm font-semibold text-on-surface">Tiket Diselesaikan</span>
              <span class="font-label-sm text-label-sm text-on-surface-variant">(Resolved)</span>
            </div>
          </div>
          <div id="chartPeriodMeta" class="text-xs text-on-surface-variant font-medium"></div>
        </div>

        <!-- Main Visualized Bar Chart Area -->
        <div class="relative w-full overflow-x-auto py-2">
          <div class="min-w-[620px] h-[340px] flex flex-col justify-between relative pt-6">
            <!-- Dynamic Background Grid Lines (Values 0 to maxScale) -->
            <div id="chartYAxis"
              class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-9 pl-12 text-on-surface-variant font-label-sm text-label-sm">
              <div class="w-full flex items-center justify-between">
                <span class="w-8 text-right pr-2">80</span>
                <div class="flex-1 h-px bg-surface-container"></div>
              </div>
              <div class="w-full flex items-center justify-between">
                <span class="w-8 text-right pr-2">60</span>
                <div class="flex-1 h-px bg-surface-container"></div>
              </div>
              <div class="w-full flex items-center justify-between">
                <span class="w-8 text-right pr-2">40</span>
                <div class="flex-1 h-px bg-surface-container"></div>
              </div>
              <div class="w-full flex items-center justify-between">
                <span class="w-8 text-right pr-2">20</span>
                <div class="flex-1 h-px bg-surface-container"></div>
              </div>
              <div class="w-full flex items-center justify-between">
                <span class="w-8 text-right pr-2">0</span>
                <div class="flex-1 h-px bg-surface-container-highest"></div>
              </div>
            </div>

            <!-- Dynamic Bar Clusters Container -->
            <div id="chartBarsContainer" class="relative z-10 h-full flex items-end justify-between pl-14 pr-4 pb-9 gap-2 sm:gap-4">
              <!-- Populated via renderChart() in JavaScript -->
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>

  <script>
    (function initReportsAnalytics() {
      // Dropdown toggle for Export Report
      const exportBtn = document.getElementById('exportMenuBtn');
      const exportMenu = document.getElementById('exportMenu');
      const exportArrow = document.getElementById('exportArrow');

      if (exportBtn && exportMenu && exportArrow) {
        exportBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          const isHidden = exportMenu.classList.contains('hidden');
          exportMenu.classList.toggle('hidden', !isHidden);
          exportArrow.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
        });

        document.addEventListener('click', () => {
          exportMenu.classList.add('hidden');
          exportArrow.style.transform = 'rotate(0deg)';
        });
      }

      // Dataset Tren Grafik dari Backend Laravel
      const trendData = @json($chartData ?? ['day' => [], 'month' => [], 'year' => []]);

      const periodButtons = {
        day: document.getElementById('btnFilterDay'),
        month: document.getElementById('btnFilterMonth'),
        year: document.getElementById('btnFilterYear')
      };

      const chartSubtitle = document.getElementById('chartSubtitle');
      const chartPeriodMeta = document.getElementById('chartPeriodMeta');
      const chartYAxis = document.getElementById('chartYAxis');
      const chartBarsContainer = document.getElementById('chartBarsContainer');

      const subtitles = {
        day: 'Visualisasi perbandingan tiket masuk & tiket diselesaikan per hari / jam kerja',
        month: 'Visualisasi tren bulanan tiket masuk & tiket diselesaikan tahun ini',
        year: 'Visualisasi tren tahunan tiket masuk & tiket diselesaikan (5 tahun terakhir)'
      };

      function renderChart(period) {
        const data = trendData[period] || [];

        // 1. Update button styling
        Object.keys(periodButtons).forEach((key) => {
          const btn = periodButtons[key];
          if (!btn) return;
          if (key === period) {
            btn.className = 'px-3.5 py-1.5 rounded-md font-label-md text-label-md bg-surface-container-lowest text-primary font-bold shadow-sm transition-all cursor-pointer';
          } else {
            btn.className = 'px-3.5 py-1.5 rounded-md font-label-md text-label-md text-on-surface-variant hover:text-on-surface transition-all cursor-pointer';
          }
        });

        // 2. Update subtitle
        if (chartSubtitle && subtitles[period]) {
          chartSubtitle.textContent = subtitles[period];
        }

        // 3. Hitung total per periode & cari nilai maksimal
        let totalMasuk = 0;
        let totalSelesai = 0;
        let maxVal = 0;

        data.forEach(item => {
          totalMasuk += item.masuk;
          totalSelesai += item.selesai;
          if (item.masuk > maxVal) maxVal = item.masuk;
          if (item.selesai > maxVal) maxVal = item.selesai;
        });

        // 4. Update ringkasan meta angka periode
        if (chartPeriodMeta) {
          const avgRate = totalMasuk > 0 ? Math.round((totalSelesai / totalMasuk) * 100) : 0;
          chartPeriodMeta.innerHTML = `<span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg bg-surface-container-low border border-outline-variant/30 text-on-surface font-medium">` +
            `Total Periode Ini: <strong class="text-primary font-bold">${totalMasuk} Masuk</strong> • ` +
            `<strong class="text-[#10b981] font-bold">${totalSelesai} Selesai</strong> (${avgRate}% Diselesaikan)</span>`;
        }

        // 5. Tentukan skala sumbu Y yang proporsional
        let maxScale = 10;
        if (maxVal > 0) {
          maxScale = Math.ceil(maxVal * 1.15);
          if (maxScale <= 10) maxScale = 10;
          else if (maxScale <= 20) maxScale = 20;
          else if (maxScale <= 40) maxScale = 40;
          else if (maxScale <= 60) maxScale = 60;
          else if (maxScale <= 80) maxScale = 80;
          else if (maxScale <= 100) maxScale = 100;
          else maxScale = Math.ceil(maxScale / 20) * 20;
        }

        // Render Skala Y-Axis
        if (chartYAxis) {
          const step = maxScale / 4;
          const yValues = [maxScale, Math.round(step * 3), Math.round(step * 2), Math.round(step), 0];
          chartYAxis.innerHTML = yValues.map((val, idx) => `
            <div class="w-full flex items-center justify-between">
              <span class="w-8 text-right pr-2">${val}</span>
              <div class="flex-1 h-px ${idx === 4 ? 'bg-surface-container-highest' : 'bg-surface-container'}"></div>
            </div>
          `).join('');
        }

        // 6. Render Batang Grafik
        if (!chartBarsContainer) return;

        if (data.length === 0) {
          chartBarsContainer.innerHTML = `<div class="w-full h-full flex flex-col items-center justify-center text-on-surface-variant gap-2 py-10">` +
            `<span class="material-symbols-outlined text-[32px] text-outline-variant">bar_chart</span>` +
            `<span class="text-xs">Tidak ada data tiket pada periode ini.</span></div>`;
          return;
        }

        chartBarsContainer.innerHTML = data.map(item => {
          const hMasuk = maxScale > 0 ? Math.min(100, (item.masuk / maxScale) * 100) : 0;
          const hSelesai = maxScale > 0 ? Math.min(100, (item.selesai / maxScale) * 100) : 0;

          const styleMasuk = item.masuk > 0 ? `height: ${Math.max(hMasuk, 5).toFixed(1)}%;` : `height: 3px; opacity: 0.35;`;
          const styleSelesai = item.selesai > 0 ? `height: ${Math.max(hSelesai, 5).toFixed(1)}%;` : `height: 3px; opacity: 0.35;`;

          return `
            <div class="flex-1 flex flex-col items-center h-full justify-end relative group cursor-pointer min-w-[36px]">
              <!-- Hover Tooltip -->
              <div class="absolute -top-14 hidden group-hover:flex flex-col items-center z-30 pointer-events-none drop-shadow-xl animate-in fade-in zoom-in-95 duration-100">
                <div class="bg-inverse-surface text-inverse-on-surface px-3 py-2 rounded-lg font-body-sm text-body-sm shadow-md flex flex-col gap-0.5 whitespace-nowrap">
                  <span class="font-headline-sm text-label-sm text-primary-fixed-dim font-bold uppercase tracking-wider">${item.tooltip_title || item.label}</span>
                  <div class="flex items-center justify-between gap-4 text-xs font-label-sm text-label-sm">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-primary-fixed"></span> Masuk: <strong>${item.masuk}</strong></span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#10b981]"></span> Selesai: <strong>${item.selesai}</strong></span>
                  </div>
                  <span class="text-[10px] text-surface-dim mt-0.5">Tingkat Penanganan: ${item.rate}%</span>
                </div>
                <div class="w-2 h-2 bg-inverse-surface rotate-45 -mt-1"></div>
              </div>

              <!-- Bar Pillars -->
              <div class="w-full flex items-end justify-center gap-1 sm:gap-2 h-full">
                <div class="w-3.5 sm:w-6 md:w-8 bg-primary-container rounded-t transition-all duration-300 group-hover:brightness-110 shadow-xs" style="${styleMasuk}" title="Tiket Masuk: ${item.masuk}">
                  <span class="sr-only">Tiket Masuk: ${item.masuk}</span>
                </div>
                <div class="w-3.5 sm:w-6 md:w-8 bg-[#10b981] rounded-t transition-all duration-300 group-hover:brightness-110 shadow-xs" style="${styleSelesai}" title="Tiket Diselesaikan: ${item.selesai}">
                  <span class="sr-only">Tiket Diselesaikan: ${item.selesai}</span>
                </div>
              </div>

              <!-- X-Axis Label -->
              <span class="mt-3 font-label-md text-label-md text-on-surface-variant font-medium group-hover:text-primary transition-colors text-center whitespace-nowrap overflow-hidden text-ellipsis max-w-full">
                ${item.label}
              </span>
            </div>
          `;
        }).join('');
      }

      // Pasang Event Listener pada ketiga tombol
      if (periodButtons.day) {
        periodButtons.day.addEventListener('click', () => renderChart('day'));
      }
      if (periodButtons.month) {
        periodButtons.month.addEventListener('click', () => renderChart('month'));
      }
      if (periodButtons.year) {
        periodButtons.year.addEventListener('click', () => renderChart('year'));
      }

      // Inisialisasi awal render 'day'
      renderChart('day');
    })();
  </script>
@endsection