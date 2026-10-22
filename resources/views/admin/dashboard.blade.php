@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <div class="flex flex-col w-full">
        <div class="p-gutter flex flex-col gap-stack_lg">
            <!-- Top Action & Title Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack_md">
                <div>
                    <div class="flex items-center gap-stack_sm">
                        <span class="inline-flex w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span
                            class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest font-bold">
                            Pusat Kendali PINTAS • Live SLA Status
                        </span>
                    </div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mt-1">Dashboard</h1>
                </div>
                <div class="flex items-center gap-stack_sm self-start md:self-auto">
                    <button
                        class="inline-flex items-center gap-stack_sm px-stack_md py-2 bg-surface-container rounded-lg text-on-surface hover:bg-surface-container-high transition-colors font-label-md text-label-md"
                        type="button">
                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        <span>Hari Ini: {{ now()->translatedFormat('d F Y') }}</span>
                    </button>
                </div>
            </div>

            <!-- Strictly 3 Top KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                <!-- Card 1: Tiket Terbuka -->
                <a href="{{ route('admin.dashboard', ['status' => $selectedStatus === 'open' ? 'all' : 'open', 'kategori_id' => $selectedCategory, 'search' => $search]) }}"
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-gutter shadow-sm hover:shadow-md transition-all cursor-pointer {{ $selectedStatus === 'open' ? 'ring-2 ring-error border-l-4 border-l-error' : '' }}"
                    title="Klik untuk menyaring tiket Open">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-error/5 pointer-events-none"></div>
                    <div class="flex items-center justify-between">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            Tiket Terbuka
                        </span>
                        <div
                            class="w-10 h-10 rounded-lg bg-error-container text-on-error-container flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">error</span>
                        </div>
                    </div>
                    <div class="mt-stack_md flex items-baseline gap-stack_sm">
                        <span class="font-headline-lg text-[36px] font-bold text-on-surface leading-none">{{ $totalOpen }}</span>
                    </div>
                </a>

                <!-- Card 2: In Progress -->
                <a href="{{ route('admin.dashboard', ['status' => $selectedStatus === 'progress' ? 'all' : 'progress', 'kategori_id' => $selectedCategory, 'search' => $search]) }}"
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-gutter shadow-sm hover:shadow-md transition-all cursor-pointer {{ $selectedStatus === 'progress' ? 'ring-2 ring-primary border-l-4 border-l-primary' : '' }}"
                    title="Klik untuk menyaring tiket In Progress">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-primary/5 pointer-events-none"></div>
                    <div class="flex items-center justify-between">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            In Progress
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">sync</span>
                        </div>
                    </div>
                    <div class="mt-stack_md flex items-baseline gap-stack_sm">
                        <span class="font-headline-lg text-[36px] font-bold text-on-surface leading-none">{{ $totalProgress }}</span>
                    </div>
                </a>

                <!-- Card 3: Sudah Diselesaikan -->
                <a href="{{ route('admin.dashboard', ['status' => $selectedStatus === 'selesai' ? 'all' : 'selesai', 'kategori_id' => $selectedCategory, 'search' => $search]) }}"
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-gutter shadow-sm hover:shadow-md transition-all cursor-pointer {{ $selectedStatus === 'selesai' ? 'ring-2 ring-emerald-600 border-l-4 border-l-emerald-600' : '' }}"
                    title="Klik untuk menyaring tiket Selesai">
                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-surface-container-highest pointer-events-none">
                    </div>
                    <div class="flex items-center justify-between">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            Sudah Diselesaikan
                        </span>
                        <div
                            class="w-10 h-10 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">check_circle</span>
                        </div>
                    </div>
                    <div class="mt-stack_md flex items-baseline gap-stack_sm">
                        <span class="font-headline-lg text-[36px] font-bold text-on-surface leading-none">{{ $totalSelesai }}</span>
                    </div>
                </a>
            </div>

            <!-- Main Content: Tiket Berdasarkan Label Layanan -->
            <div class="flex flex-col gap-stack_md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-stack_sm">
                    <div class="flex items-center gap-3">
                        <h2 class="font-headline-md text-headline-md text-on-surface">Tiket Berdasarkan Label Layanan</h2>
                        @if($selectedStatus !== 'all' || $selectedCategory !== 'all' || !empty($search))
                            <a href="{{ route('admin.dashboard') }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-outline-variant/60 bg-surface-container-lowest hover:bg-surface-container text-xs text-on-surface-variant hover:text-on-surface transition-colors shadow-xs"
                                title="Reset semua filter">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                                <span>Reset Filter</span>
                            </a>
                        @endif
                    </div>

                    <!-- Service Filter Dropdown (Dinamis dari Database) -->
                    <div class="relative flex items-center min-w-[220px]">
                        <select
                            class="w-full appearance-none pl-3 pr-9 py-2 bg-surface-container-lowest border border-outline-variant/60 rounded-lg font-label-md text-label-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary shadow-xs transition-colors cursor-pointer hover:bg-surface-container-low"
                            id="category-dropdown-filter"
                            onchange="window.location.href='{{ route('admin.dashboard') }}?status={{ $selectedStatus }}&search={{ urlencode($search ?? '') }}&kategori_id=' + this.value">
                            <option value="all" {{ $selectedCategory === 'all' ? 'selected' : '' }}>
                                Semua Kategori ({{ $totalTickets }})
                            </option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (string)$selectedCategory === (string)$category->id ? 'selected' : '' }}>
                                    {{ $category->nama_layanan }} ({{ $category->tickets_count }})
                                </option>
                            @endforeach
                        </select>
                        <span
                            class="material-symbols-outlined absolute right-2.5 pointer-events-none text-[18px] text-on-surface-variant">expand_more</span>
                    </div>
                </div>

                <!-- Single Full-Width Grid -->
                <div class="grid grid-cols-1 gap-gutter">
                    <!-- Main Table View (Full Width) -->
                    <div
                        class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col h-full w-full">
                        <!-- Table Toolbar -->
                        <div
                            class="p-stack_md bg-surface-container-low/60 flex flex-col sm:flex-row items-center justify-between gap-stack_sm">
                            <form action="{{ route('admin.dashboard') }}" method="GET" class="relative w-full sm:w-72">
                                @if($selectedCategory !== 'all')
                                    <input type="hidden" name="kategori_id" value="{{ $selectedCategory }}">
                                @endif
                                @if($selectedStatus !== 'all')
                                    <input type="hidden" name="status" value="{{ $selectedStatus }}">
                                @endif
                                <span
                                    class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">filter_list</span>
                                <input
                                    class="w-full pl-8 pr-stack_sm py-1.5 bg-surface-container-lowest rounded font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
                                    id="ticket-search"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Cari ID tiket, kendala, atau staf..."
                                    type="text">
                            </form>
                            @if($selectedStatus !== 'all')
                                <div class="text-xs text-on-surface-variant">
                                    Menyaring status: <strong class="capitalize">{{ $selectedStatus }}</strong>
                                </div>
                            @endif
                        </div>

                        <!-- Active Tickets Table (Dinamis dari Database) -->
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                                        <th class="py-3 px-stack_md font-semibold">ID &amp; Urgensi</th>
                                        <th class="py-3 px-stack_md font-semibold">Deskripsi Insiden</th>
                                        <th class="py-3 px-stack_md font-semibold">Label Layanan</th>
                                        <th class="py-3 px-stack_md font-semibold">Staf Ditugaskan</th>
                                        <th class="py-3 px-stack_md font-semibold">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="font-body-sm text-body-sm text-on-surface divide-y divide-surface-container"
                                    id="ticket-table-body">
                                    @forelse($tickets as $ticket)
                                        <tr class="hover:bg-surface-container-low/50 transition-colors ticket-row"
                                            data-service="{{ $ticket->id_kategori_api }}">
                                            {{-- 1. ID & Urgensi --}}
                                            <td class="py-4 px-stack_md whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span class="font-semibold text-primary font-body-md">#{{ $ticket->id }}</span>
                                                    <span class="text-[11px] text-on-surface-variant mt-0.5">
                                                        {{ $ticket->created_at ? $ticket->created_at->format('d M Y, H:i') : '-' }}
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- 2. Deskripsi Insiden --}}
                                            <td class="py-4 px-stack_md max-w-sm">
                                                <div class="font-medium text-on-surface line-clamp-2" title="{{ $ticket->keluhan }}">
                                                    {{ $ticket->keluhan }}
                                                </div>
                                                <div
                                                    class="font-label-sm text-label-sm text-on-surface-variant mt-1 flex items-center gap-1.5 flex-wrap">
                                                    <span class="material-symbols-outlined text-[13px]">person</span>
                                                    <span class="font-semibold text-on-surface">{{ $ticket->nama_pemohon ?? 'Pemohon Umum' }}</span>
                                                    @if($ticket->nomor_wa)
                                                        <span>• {{ $ticket->nomor_wa }}</span>
                                                    @endif
                                                    <span>• {{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '-' }}</span>
                                                </div>
                                            </td>

                                            {{-- 3. Label Layanan --}}
                                            <td class="py-4 px-stack_md whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-primary-fixed text-primary font-label-sm text-label-sm font-semibold">
                                                    <span class="material-symbols-outlined text-[14px]">label</span>
                                                    {{ $ticket->kategori->nama_layanan ?? 'Umum' }}
                                                </span>
                                            </td>

                                            {{-- 4. Staf Ditugaskan --}}
                                            <td class="py-4 px-stack_md whitespace-nowrap">
                                                @if($ticket->user)
                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="w-7 h-7 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-sm font-bold text-[10px]">
                                                            {{ strtoupper(substr($ticket->user->name, 0, 2)) }}
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="font-medium text-on-surface leading-none">{{ $ticket->user->name }}</span>
                                                            <span class="font-label-sm text-[10px] text-on-surface-variant mt-0.5">
                                                                {{ $ticket->user->bidangStaf->nama_bidang ?? ($ticket->user->role === 'front_desk' ? 'Front Desk' : 'Staf Bidang') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="w-7 h-7 rounded-full bg-surface-dim text-on-surface-variant flex items-center justify-center font-label-sm font-bold text-[10px]">
                                                            NA
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="font-medium text-on-surface-variant leading-none italic">Unassigned</span>
                                                            <span class="font-label-sm text-[10px] text-amber-700 font-semibold mt-0.5">Antrean Masuk</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- 5. Status --}}
                                            <td class="py-4 px-stack_md whitespace-nowrap">
                                                @if($ticket->status === 'open')
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200/80 font-label-sm text-label-sm font-semibold">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Open
                                                    </span>
                                                @elseif($ticket->status === 'progress')
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200/80 font-label-sm text-label-sm font-semibold">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> In Progress
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 font-label-sm text-label-sm font-semibold">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-12 text-center text-on-surface-variant">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant mb-3">
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

                        <!-- Table Footer (Dinamis dari Paginator) -->
                        <div
                            class="p-stack_md bg-surface-container-low/40 flex flex-col sm:flex-row items-center justify-between gap-3 font-label-sm text-label-sm text-on-surface-variant">
                            <span>
                                @if($tickets->total() > 0)
                                    Menampilkan {{ $tickets->firstItem() }} - {{ $tickets->lastItem() }} dari {{ $tickets->total() }} tiket aktif
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

            if (categoryFilter) {
                categoryFilter.addEventListener('change', filterRows);
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterRows);
            }
        });
    </script>
@endsection