@extends('layouts.frontdesk')

@section('title', 'Tiket Saya — Tugas & Penanganan')

@section('content')
    <div class="flex flex-col w-full">
        <div class="p-4 sm:p-6 lg:p-8 flex flex-col gap-6 w-full">
            <!-- 1. Page Header -->
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-2">
                    <span
                        class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-primary">
                        FRONT DESK OPERATIONS
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold font-headline-lg text-on-surface tracking-tight mt-1">
                    Tiket Saya — Tugas &amp; Penanganan
                </h1>
                <p class="text-sm font-body-md text-on-surface-variant">
                    Kelola dan perbarui status pengerjaan tiket aktif yang telah Anda ambil dari antrean umum.
                </p>
            </div>

            <!-- 2. Metric Cards Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <!-- Card 1: Tiket Aktif Saya -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/40 shadow-sm flex items-center justify-between">
                    <div class="flex flex-col gap-2">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider font-semibold text-on-surface-variant">
                            TIKET AKTIF SAYA
                        </span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
                                {{ $statActive }}
                            </span>
                            <span class="font-body-sm text-sm text-on-surface-variant font-medium">
                                tiket ditugaskan
                            </span>
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-primary-fixed/50 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">assignment_ind</span>
                    </div>
                </div>

                <!-- Card 2: Selesai Hari Ini -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/40 shadow-sm flex items-center justify-between">
                    <div class="flex flex-col gap-2">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider font-semibold text-on-surface-variant">
                            SELESAI HARI INI
                        </span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
                                {{ $statCompletedToday }}
                            </span>
                            <span class="font-body-sm text-sm text-on-surface-variant font-medium">
                                tiket terselesaikan
                            </span>
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">check_circle</span>
                    </div>
                </div>
            </div>

            <!-- 3. Main Work Table View Container -->
            <div
                class="bg-surface-container-lowest rounded-xl shadow-sm flex flex-col overflow-hidden border border-outline-variant/40 w-full">
                <!-- Filter & Search Bar -->
                <form method="GET" action="{{ route('frontdesk.ruang-kerja') }}"
                    class="p-4 bg-surface-container-low/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-b border-outline-variant/40">
                    <div class="flex flex-col sm:flex-row flex-1 items-stretch sm:items-center gap-3 w-full">
                        <div class="relative w-full sm:max-w-md">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/80 text-[18px]">search</span>
                            <input
                                class="w-full pl-9 pr-8 py-2 bg-surface-container-lowest border border-outline-variant/40 rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant/70 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs transition-colors"
                                name="q" value="{{ $search }}" placeholder="Cari ID tiket, pelapor, kendala..." type="text">
                            @if($search)
                                <a href="{{ route('frontdesk.ruang-kerja', ['kategori' => $kategoriId]) }}"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </a>
                            @endif
                        </div>

                        <!-- <div class="w-full sm:w-64">
                                                    <select name="kategori" onchange="this.form.submit()"
                                                        class="w-full py-2 px-3 font-body-sm text-body-sm bg-surface-container-lowest border border-outline-variant/40 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-on-surface shadow-xs">
                                                        <option value="">Semua Kategori</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ $kategoriId == $cat->id ? 'selected' : '' }}>
                                                                {{ $cat->nama_layanan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div> -->
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <button type="submit"
                            class="px-4 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                            Cari
                        </button>
                        <a href="{{ route('frontdesk.ruang-kerja') }}"
                            class="p-2 rounded-lg border border-outline-variant/40 hover:bg-surface-container-low text-on-surface-variant hover:text-on-surface shadow-xs transition-colors shrink-0"
                            title="Segarkan">
                            <span class="material-symbols-outlined text-[20px]">refresh</span>
                        </a>
                    </div>
                </form>

                <!-- Data Table -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full min-w-190px text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-surface-container-low/60 text-on-surface-variant font-label-md text-label-md uppercase tracking-wider border-b border-outline-variant/40">
                                <th class="py-3.5 px-5 font-semibold">ID Tiket</th>
                                <th class="py-3.5 px-5 font-semibold">Pelapor &amp; No Whatsapp</th>
                                <th class="py-3.5 px-5 font-semibold">Rincian Kendala</th>
                                <th class="py-3.5 px-5 font-semibold">Kategori</th>
                                <th class="py-3.5 px-5 font-semibold">Waktu Penugasan</th>
                                <th class="py-3.5 px-5 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-on-surface font-body-sm text-body-sm divide-y divide-outline-variant/20">
                            @forelse($myTickets as $ticket)
                                <tr onclick="window.location='{{ route('frontdesk.tickets.chat', $ticket->id) }}'"
                                    class="hover:bg-primary/5 hover:border-l-4 hover:border-l-primary cursor-pointer transition-all group">
                                    <td class="py-4 px-5 align-top">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-8 bg-primary rounded-full shrink-0"></div>
                                            <span class="font-bold text-primary font-headline-sm">#{{ $ticket->id }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 align-top whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-semibold text-on-surface">{{ $ticket->nama_pemohon ?? 'Pemohon' }}</span>
                                            <span class="text-xs text-on-surface-variant mt-0.5">{{ $ticket->nomor_wa }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 align-top max-w-xs">
                                        <p class="font-medium text-on-surface leading-snug">{{ $ticket->keluhan }}</p>
                                    </td>
                                    <td class="py-4 px-5 align-top whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded bg-surface-container text-on-surface font-label-sm text-label-sm font-medium">
                                            {{ $ticket->kategori->nama_layanan ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 align-top whitespace-nowrap">
                                        <div class="flex flex-col text-xs text-on-surface-variant">
                                            <span
                                                class="font-semibold text-on-surface">{{ $ticket->assigned_at ? $ticket->assigned_at->diffForHumans() : '-' }}</span>
                                            <span>{{ $ticket->assigned_at ? $ticket->assigned_at->format('d M Y, H:i') : '' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 align-top text-right whitespace-nowrap"
                                        onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Tombol Selesai -->
                                            <form action="{{ route('frontdesk.tickets.selesai', $ticket) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin tugas pada tiket #{{ $ticket->id }} ini telah selesai dikerjakan?');"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition-colors flex items-center gap-1.5"
                                                    title="Tandai selesai &amp; tutup tiket">
                                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                                    <span>Selesai</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-on-surface-variant">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant mb-3">
                                                <span class="material-symbols-outlined text-2xl">task_alt</span>
                                            </div>
                                            <span class="text-base font-semibold text-on-surface">Tidak ada tiket aktif yang
                                                ditugaskan</span>
                                            <p class="text-xs text-on-surface-variant mt-1 max-w-sm">Anda saat ini belum
                                                memiliki tiket yang sedang dalam pengerjaan. Silakan ambil dari antrean utama.
                                            </p>
                                            <a href="{{ route('frontdesk.index') }}"
                                                class="mt-4 px-4 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                                                Lihat Antrean Tiket Masuk
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer / Pagination -->
                @if($myTickets->hasPages())
                    <div
                        class="p-4 bg-surface-container-lowest border-t border-outline-variant/30 flex items-center justify-between font-label-sm text-label-sm text-on-surface-variant">
                        {{ $myTickets->links() }}
                    </div>
                @else
                    <div
                        class="p-4 bg-surface-container-low/40 flex items-center justify-between border-t border-outline-variant/30 text-xs text-on-surface-variant">
                        <span>Menampilkan {{ $myTickets->count() }} tiket aktif</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection