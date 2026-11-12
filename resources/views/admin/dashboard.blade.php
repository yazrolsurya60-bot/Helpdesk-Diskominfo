@extends('layouts.admin')
@section('title', 'Dashboard Utama')
@section('content')
    <div class="flex flex-col w-full">
        <div class="p-4 sm:p-6 lg:p-8 flex flex-col gap-6 w-full">
            <!-- 1. Header Title & Top Info Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <div class="inline-flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-primary">
                            PUSAT KENDALI ADMIN
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold font-headline-lg text-on-surface tracking-tight mt-1">
                        Dashboard Utama
                    </h1>
                    <p class="text-sm font-body-md text-on-surface-variant">
                        Pantau metrik tiket, status SLA penanganan, serta distribusi layanan helpdesk secara real-time.
                    </p>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-2 bg-surface-container rounded-lg text-on-surface font-label-md text-label-md border border-outline-variant/30 shadow-xs">
                        <span class="material-symbols-outlined text-[18px] text-primary">calendar_today</span>
                        <span>Hari Ini: {{ now()->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Top 4 Metric KPI Cards (Grid 4 Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Card 1: Total Tiket -->
                <a href="{{ route('admin.dashboard', ['status' => 'all', 'kategori_id' => $selectedCategory, 'search' => $search]) }}"
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border {{ $selectedStatus === 'all' ? 'border-primary ring-2 ring-primary/20 bg-blue-50/20' : 'border-outline-variant/40' }} shadow-sm hover:shadow-md transition-all cursor-pointer"
                    title="Klik untuk melihat semua tiket (terbuka, in progress, dan selesai)">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            Total Tiket
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">confirmation_number</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">{{ $totalTickets }}</span>
                    </div>
                </a>

                <!-- Card 2: Tiket Terbuka (Open) -->
                <a href="{{ route('admin.dashboard', ['status' => $selectedStatus === 'open' ? 'all' : 'open', 'kategori_id' => $selectedCategory, 'search' => $search]) }}"
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border {{ $selectedStatus === 'open' ? 'border-blue-600 ring-2 ring-blue-600/20 bg-blue-50/20' : 'border-outline-variant/40' }} shadow-sm hover:shadow-md transition-all cursor-pointer"
                    title="Klik untuk menyaring tiket Open">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            Tiket Terbuka
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">error</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">{{ $totalOpen }}</span>
                    </div>
                </a>

                <!-- Card 3: Sedang Ditangani (In Progress) -->
                <a href="{{ route('admin.dashboard', ['status' => $selectedStatus === 'progress' ? 'all' : 'progress', 'kategori_id' => $selectedCategory, 'search' => $search]) }}"
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border {{ $selectedStatus === 'progress' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/20' : 'border-outline-variant/40' }} shadow-sm hover:shadow-md transition-all cursor-pointer"
                    title="Klik untuk menyaring tiket In Progress">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            In Progress
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">sync</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">{{ $totalProgress }}</span>
                    </div>
                </a>

                <!-- Card 4: Sudah Diselesaikan -->
                <a href="{{ route('admin.dashboard', ['status' => $selectedStatus === 'selesai' ? 'all' : 'selesai', 'kategori_id' => $selectedCategory, 'search' => $search]) }}"
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border {{ $selectedStatus === 'selesai' ? 'border-emerald-600 ring-2 ring-emerald-600/20 bg-emerald-50/20' : 'border-outline-variant/40' }} shadow-sm hover:shadow-md transition-all cursor-pointer"
                    title="Klik untuk menyaring tiket Selesai">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            Sudah Diselesaikan
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">check_circle</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">{{ $totalSelesai }}</span>
                    </div>
                </a>
            </div>

            <!-- 3. Integrated Table Container with Filter Toolbar (Sesuai Aturan Front Desk) -->
            <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/40 overflow-hidden flex flex-col w-full">
                <!-- Filter Bar (Top Toolbar Terintegrasi) -->
                <form action="{{ route('admin.dashboard') }}" method="GET"
                    class="p-4 bg-surface-container-low/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-b border-outline-variant/40">
                    
                    @if($selectedStatus !== 'all')
                        <input type="hidden" name="status" value="{{ $selectedStatus }}">
                    @endif

                    <div class="flex flex-col sm:flex-row flex-1 items-stretch sm:items-center gap-3 w-full">
                        <!-- Input Pencarian -->
                        <div class="relative w-full sm:w-80">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">filter_list</span>
                            <input
                                class="w-full pl-9 pr-8 py-2 bg-surface-container-lowest rounded-lg border border-outline-variant/50 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
                                id="ticket-search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Cari ID tiket, kendala, atau staf..."
                                type="text">
                            @if(!empty($search))
                                <a href="{{ route('admin.dashboard', ['status' => $selectedStatus, 'kategori_id' => $selectedCategory]) }}"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface"
                                    title="Hapus pencarian">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </a>
                            @endif
                        </div>

                        <!-- Dropdown Filter Kategori Layanan -->
                        <div class="w-full sm:w-64">
                            <select
                                class="w-full py-2 px-3 font-body-sm text-body-sm bg-surface-container-lowest border border-outline-variant/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-on-surface shadow-xs cursor-pointer"
                                id="category-dropdown-filter"
                                name="kategori_id"
                                onchange="this.form.submit()">
                                <option value="all" {{ $selectedCategory === 'all' ? 'selected' : '' }}>
                                    Semua Kategori ({{ $totalTickets }})
                                </option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string)$selectedCategory === (string)$category->id ? 'selected' : '' }}>
                                        {{ $category->nama_layanan }} ({{ $category->tickets_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pill Status Aktif (Jika Sedang Filter Status) -->
                        @if($selectedStatus !== 'all')
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-200">
                                <span>Status:</span>
                                <strong class="capitalize">{{ $selectedStatus }}</strong>
                                <a href="{{ route('admin.dashboard', ['status' => 'all', 'kategori_id' => $selectedCategory, 'search' => $search]) }}"
                                    class="text-blue-700 hover:text-blue-900 ml-1 flex items-center"
                                    title="Hapus filter status">
                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Reset & Refresh -->
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        @if($selectedStatus !== 'all' || $selectedCategory !== 'all' || !empty($search))
                            <a href="{{ route('admin.dashboard') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors text-xs font-medium shadow-xs"
                                title="Reset semua filter">
                                <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                                <span>Reset Filter</span>
                            </a>
                        @endif
                        <a href="{{ route('admin.dashboard', ['status' => $selectedStatus, 'kategori_id' => $selectedCategory]) }}"
                            class="p-2 rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors flex items-center justify-center shadow-xs"
                            title="Muat Ulang Halaman">
                            <span class="material-symbols-outlined text-[20px]">refresh</span>
                        </a>
                    </div>
                </form>

                <!-- Data Table Responsif -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full min-w-[750px] text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low/60 text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider border-b border-outline-variant/40">
                                <th class="py-3.5 px-5 font-semibold">ID &amp; Urgensi</th>
                                <th class="py-3.5 px-5 font-semibold">Deskripsi Insiden</th>
                                <th class="py-3.5 px-5 font-semibold">Label Layanan</th>
                                <th class="py-3.5 px-5 font-semibold">Staf Ditugaskan</th>
                                <th class="py-3.5 px-5 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-sm text-body-sm text-on-surface divide-y divide-outline-variant/20" id="ticket-table-body">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-primary/5 transition-colors ticket-row" data-service="{{ $ticket->id_kategori_api }}">
                                    <!-- 1. ID & Urgensi -->
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-primary font-body-md">#{{ $ticket->id }}</span>
                                            <span class="text-xs text-on-surface-variant mt-0.5">
                                                {{ $ticket->created_at ? $ticket->created_at->format('d M Y, H:i') : '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 2. Deskripsi Insiden -->
                                    <td class="py-4 px-5 max-w-sm">
                                        <div class="font-medium text-on-surface line-clamp-2" title="{{ $ticket->keluhan }}">
                                            {{ $ticket->keluhan }}
                                        </div>
                                        <div class="font-label-sm text-xs text-on-surface-variant mt-1.5 flex items-center gap-1.5 flex-wrap">
                                            <span class="material-symbols-outlined text-[14px]">person</span>
                                            <span class="font-semibold text-on-surface">{{ $ticket->nama_pemohon ?? 'Pemohon Umum' }}</span>
                                            @if($ticket->nomor_wa)
                                                <span>• {{ $ticket->nomor_wa }}</span>
                                            @endif
                                            <span>• {{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '-' }}</span>
                                        </div>
                                    </td>

                                    <!-- 3. Label Layanan -->
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded bg-surface-container text-on-surface font-label-sm text-label-sm font-medium">
                                            {{ $ticket->kategori->nama_layanan ?? 'Umum' }}
                                        </span>
                                    </td>

                                    <!-- 4. Staf Ditugaskan -->
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        @if($ticket->user)
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-sm font-bold text-xs shrink-0 shadow-xs">
                                                    {{ strtoupper(substr($ticket->user->name, 0, 2)) }}
                                                </div>
                                                <div class="flex flex-col min-w-0">
                                                    <span class="font-medium text-on-surface leading-tight truncate">{{ $ticket->user->name }}</span>
                                                    <span class="text-[11px] text-on-surface-variant mt-0.5">
                                                        {{ $ticket->user->bidangStaf->nama_bidang ?? ($ticket->user->role === 'front_desk' ? 'Front Desk' : 'Staf Bidang') }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center font-label-sm font-bold text-xs shrink-0">
                                                    --
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-on-surface-variant leading-tight italic">Unassigned</span>
                                                    <span class="text-[11px] text-amber-700 font-semibold mt-0.5">Antrean Masuk</span>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- 5. Status -->
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        @if($ticket->status === 'open')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Open
                                            </span>
                                        @elseif($ticket->status === 'progress')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> In Progress
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-on-surface-variant">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant mb-3">
                                                <span class="material-symbols-outlined text-2xl">search_off</span>
                                            </div>
                                            <span class="text-base font-semibold text-on-surface">Tidak ada tiket ditemukan</span>
                                            <p class="text-xs text-on-surface-variant mt-1 max-w-sm">
                                                Tidak ada data tiket yang sesuai dengan filter atau kata kunci pencarian saat ini.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer (Pagination & Counter) -->
                <div class="p-4 bg-surface-container-lowest border-t border-outline-variant/30 flex flex-col sm:flex-row items-center justify-between gap-3 font-label-sm text-label-sm text-on-surface-variant">
                    <span>
                        @if($tickets->total() > 0)
                            Menampilkan <strong class="text-on-surface">{{ $tickets->firstItem() }}</strong> - <strong class="text-on-surface">{{ $tickets->lastItem() }}</strong> dari <strong class="text-on-surface">{{ $tickets->total() }}</strong> tiket aktif
                        @else
                            Menampilkan 0 tiket
                        @endif
                    </span>
                    @if($tickets->hasPages())
                        <div>
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryFilter = document.getElementById('category-dropdown-filter');
            const searchInput = document.getElementById('ticket-search');
            const rows = document.querySelectorAll('.ticket-row');

            function filterRows() {
                const selectedCat = categoryFilter ? categoryFilter.value : 'all';
                const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';

                rows.forEach(row => {
                    const rowService = row.getAttribute('data-service');
                    const matchesCategory = (selectedCat === 'all' || rowService === selectedCat);
                    const rowText = row.innerText.toLowerCase();
                    const matchesSearch = !searchQuery || rowText.includes(searchQuery);

                    if (matchesCategory && matchesSearch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterRows);
            }
        });
    </script>
@endsection
