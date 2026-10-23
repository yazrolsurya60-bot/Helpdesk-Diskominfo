@extends('layouts.frontdesk')

@section('title', 'Dashboard Umum — Antrean Tiket Masuk')

@section('content')
    <div class="flex flex-col w-full">
        <div class="p-4 sm:p-6 lg:p-8 flex flex-col gap-6 w-full">
            <!-- 1. Header Title Section -->
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-2">
                    <span
                        class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-primary">
                        FRONT DESK OPERATIONS
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold font-headline-lg text-on-surface tracking-tight mt-1">
                    Dashboard Umum — Antrean Tiket Masuk
                </h1>
                <p class="text-sm font-body-md text-on-surface-variant">
                    Pantau seluruh tiket yang masuk dari pengguna dan ambil tiket sesuai keahlian teknis anda.
                </p>
            </div>

            <!-- 2. Top 3 Metric Cards (Grid 3 Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Card 1: Total Tiket Masuk -->
                <div
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            TOTAL TIKET MASUK
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">confirmation_number</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
                            {{ $statTotal }}
                        </span>
                    </div>
                </div>

                <!-- Card 2: Belum Diambil (Open) -->
                <div
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            BELUM DIAMBIL (OPEN)
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">schedule</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold leading-none text-on-surface">
                            {{ $statOpen }}
                        </span>
                    </div>
                </div>

                <!-- Card 3: Sedang Ditangani -->
                <div
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            SUDAH DIAMBIL
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">assignment_turned_in</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold leading-none text-on-surface">
                            {{ $statClaimedFrontDesk }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. Table Container with Integrated Filter Bar -->
            <div
                class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/40 overflow-hidden flex flex-col w-full">
                <!-- Filter Bar -->
                <form method="GET" action="{{ route('frontdesk.index') }}"
                    class="p-4 bg-surface-container-low/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-b border-outline-variant/40">
                    <div class="flex flex-col sm:flex-row flex-1 items-stretch sm:items-center gap-3 w-full">
                        <div class="relative w-full sm:w-80">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">filter_list</span>
                            <input
                                class="w-full pl-9 pr-8 py-2 bg-surface-container-lowest rounded-lg border border-outline-variant/50 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
                                name="q" value="{{ $search }}" placeholder="Cari ID tiket, pelapor, kendala..." type="text">
                            @if($search)
                                <a href="{{ route('frontdesk.index', ['kategori' => $kategoriId]) }}"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </a>
                            @endif
                        </div>

                        <div class="w-full sm:w-64">
                            <select name="kategori" onchange="this.form.submit()"
                                class="w-full py-2 px-3 font-body-sm text-body-sm bg-surface-container-lowest border border-outline-variant/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-on-surface shadow-xs">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $kategoriId == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_layanan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <a href="{{ route('frontdesk.index') }}"
                            class="p-2 rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors flex items-center justify-center shadow-xs"
                            title="Muat Ulang Antrean">
                            <span class="material-symbols-outlined text-[20px]">refresh</span>
                        </a>
                    </div>
                </form>

                <!-- 4. Table: Semua Tiket Masuk -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full min-w-190px text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-surface-container-low/60 text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider border-b border-outline-variant/40">
                                <th class="py-3.5 px-5 font-semibold">ID TIKET</th>
                                <th class="py-3.5 px-5 font-semibold">PELAPOR &amp; NO WHATSAPP</th>
                                <th class="py-3.5 px-5 font-semibold">KENDALA</th>
                                <th class="py-3.5 px-5 font-semibold">KATEGORI</th>
                                <th class="py-3.5 px-5 font-semibold">STATUS</th>
                                <th class="py-3.5 px-5 font-semibold">WAKTU MASUK</th>
                                <th class="py-3.5 px-5 text-right font-semibold">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-sm text-body-sm text-on-surface divide-y divide-outline-variant/20">
                            @forelse($tickets as $ticket)
                                <tr onclick="window.location='{{ route('frontdesk.tickets.chat', $ticket->id) }}'"
                                    class="hover:bg-primary/5 hover:border-l-4 hover:border-l-primary cursor-pointer transition-all group">
                                    <td class="py-4 px-5 whitespace-nowrap font-bold text-primary">
                                        #{{ $ticket->id }}
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-on-surface">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $ticket->nama_pemohon ?? 'Pemohon' }}</span>
                                            <span class="text-xs text-on-surface-variant">{{ $ticket->nomor_wa }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-on-surface max-w-xs truncate" title="{{ $ticket->keluhan }}">
                                        {{ $ticket->keluhan }}
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded bg-surface-container text-on-surface font-label-sm text-label-sm font-medium">
                                            {{ $ticket->kategori->nama_layanan ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        @if($ticket->status === 'open')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Open
                                            </span>
                                        @elseif($ticket->status === 'progress')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> In Progress
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-on-surface-variant text-xs">
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </td>
                                    <td class="py-4 px-5 text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($ticket->status === 'open')
                                                {{-- Ambil Tiket --}}
                                                <form action="{{ route('frontdesk.tickets.ambil', $ticket) }}" method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin mengambil tiket #{{ $ticket->id }} ini?');"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1e3a8a] hover:bg-[#172554] text-white text-xs font-medium rounded-lg shadow-sm transition-colors"
                                                        title="Ambil Tiket">
                                                        <span class="material-symbols-outlined text-[16px]">touch_app</span>
                                                        <span>Ambil Tiket</span>
                                                    </button>
                                                </form>
                                                {{-- Distribusi --}}
                                                <button type="button"
                                                    onclick="openDistribusiModal({{ $ticket->id }}, '{{ addslashes($ticket->nama_pemohon) }}', '{{ addslashes($ticket->keluhan) }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface-container border border-outline-variant/60 text-on-surface hover:bg-surface-container-high text-xs font-medium rounded-lg shadow-xs transition-colors"
                                                    title="Distribusi Tiket">
                                                    <span class="material-symbols-outlined text-[16px] text-primary">forward</span>
                                                    <span>Distribusi</span>
                                                </button>
                                                {{-- Ubah Kategori --}}
                                                <button type="button"
                                                    onclick="openKategoriModal({{ $ticket->id }}, '{{ $ticket->id_kategori_api }}')"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 border border-outline-variant/60 text-on-surface-variant hover:text-on-surface hover:bg-surface-container text-xs font-medium rounded-lg shadow-xs transition-colors"
                                                    title="Ubah Kategori">
                                                    <span class="material-symbols-outlined text-[16px]">label</span>
                                                    <span class="hidden sm:inline">Ubah Label</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-on-surface-variant">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant mb-3">
                                                <span class="material-symbols-outlined text-2xl">search_off</span>
                                            </div>
                                            <span class="text-base font-semibold text-on-surface">Tidak ada tiket di antrean
                                                masuk</span>
                                            <p class="text-xs text-on-surface-variant mt-1 max-w-sm">Saat ini belum ada
                                                tiket berstatus open yang tersedia untuk diambil.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer / Pagination -->
                @if($tickets->hasPages())
                    <div
                        class="p-4 bg-surface-container-lowest border-t border-outline-variant/30 flex items-center justify-between font-label-sm text-label-sm text-on-surface-variant">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL: Distribusi Tiket --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity hidden"
        id="distribusiModal">
        <div class="bg-surface-container-lowest rounded-xl max-w-lg w-full p-6 shadow-xl border border-outline-variant/40 flex flex-col gap-4 transform transition-all scale-95 opacity-0"
            id="distribusiModalBox">
            <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">forward</span>
                </div>
                <button
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors"
                    onclick="closeDistribusiModal()" type="button">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="flex flex-col gap-1">
                <h3 class="text-lg font-bold font-headline-md text-on-surface">Distribusikan Tiket ke Staf Bidang</h3>
                <p class="text-xs font-body-sm text-on-surface-variant">Tiket: <span class="font-semibold text-on-surface"
                        id="distribusiTicketInfo"></span></p>
            </div>
            <form id="distribusiForm" method="POST">
                @csrf
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant"
                            for="target_user_id">Pilih Staf Bidang <span class="text-error">*</span></label>
                        <select name="target_user_id" id="target_user_id"
                            class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
                            required>
                            <option value="">-- Pilih Staf Penerima --</option>
                            @foreach($stafBidang as $staf)
                                <option value="{{ $staf->id }}">{{ $staf->name }} —
                                    {{ $staf->bidangStaf->nama_bidang ?? 'Belum ada bidang' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/30">
                        <button
                            class="px-4 py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high text-xs font-medium transition-colors"
                            onclick="closeDistribusiModal()" type="button">Batal</button>
                        <button
                            class="px-4 py-2 rounded-lg bg-[#1e3a8a] hover:bg-[#172554] text-white text-xs font-medium shadow-sm flex items-center gap-1.5 transition-all"
                            type="submit">
                            <span class="material-symbols-outlined text-[16px]">forward</span>
                            <span>Distribusikan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Ubah Kategori --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity hidden"
        id="kategoriModal">
        <div class="bg-surface-container-lowest rounded-xl max-w-lg w-full p-6 shadow-xl border border-outline-variant/40 flex flex-col gap-4 transform transition-all scale-95 opacity-0"
            id="kategoriModalBox">
            <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">label</span>
                </div>
                <button
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors"
                    onclick="closeKategoriModal()" type="button">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="flex flex-col gap-1">
                <h3 class="text-lg font-bold font-headline-md text-on-surface">Ubah Kategori Layanan</h3>
                <p class="text-xs font-body-sm text-on-surface-variant">Pilih kategori layanan baru untuk tiket ini.</p>
            </div>
            <form id="kategoriForm" method="POST">
                @csrf
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="font-label-sm text-xs uppercase font-semibold text-on-surface-variant"
                            for="id_kategori_api">Kategori Layanan <span class="text-error">*</span></label>
                        <select name="id_kategori_api" id="id_kategori_api"
                            class="w-full bg-surface-container-lowest text-on-surface rounded-lg p-2.5 text-sm font-body-sm border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
                            required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama_layanan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/30">
                        <button
                            class="px-4 py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high text-xs font-medium transition-colors"
                            onclick="closeKategoriModal()" type="button">Batal</button>
                        <button
                            class="px-4 py-2 rounded-lg bg-[#1e3a8a] hover:bg-[#172554] text-white text-xs font-medium shadow-sm flex items-center gap-1.5 transition-all"
                            type="submit">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Distribusi Modal
        function openDistribusiModal(ticketId, pelapor, kendala) {
            document.getElementById('distribusiTicketInfo').textContent = '#TKT-' + ticketId + ' — ' + pelapor + ': ' + kendala;
            document.getElementById('distribusiForm').action = '/frontdesk/tickets/' + ticketId + '/distribusi';
            const modal = document.getElementById('distribusiModal');
            const box = document.getElementById('distribusiModalBox');
            modal.classList.remove('hidden');
            setTimeout(() => { box.classList.remove('scale-95', 'opacity-0'); box.classList.add('scale-100', 'opacity-100'); }, 10);
        }
        function closeDistribusiModal() {
            const modal = document.getElementById('distribusiModal');
            const box = document.getElementById('distribusiModalBox');
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); }, 150);
        }

        // Kategori Modal
        function openKategoriModal(ticketId, currentKategoriId) {
            document.getElementById('kategoriForm').action = '/frontdesk/tickets/' + ticketId + '/ubah-kategori';
            if (currentKategoriId) {
                document.getElementById('id_kategori_api').value = currentKategoriId;
            }
            const modal = document.getElementById('kategoriModal');
            const box = document.getElementById('kategoriModalBox');
            modal.classList.remove('hidden');
            setTimeout(() => { box.classList.remove('scale-95', 'opacity-0'); box.classList.add('scale-100', 'opacity-100'); }, 10);
        }
        function closeKategoriModal() {
            const modal = document.getElementById('kategoriModal');
            const box = document.getElementById('kategoriModalBox');
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); }, 150);
        }

        // Close modals on backdrop click
        document.getElementById('distribusiModal')?.addEventListener('click', function (e) { if (e.target === this) closeDistribusiModal(); });
        document.getElementById('kategoriModal')?.addEventListener('click', function (e) { if (e.target === this) closeKategoriModal(); });
    </script>
@endsection