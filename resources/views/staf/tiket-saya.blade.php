@extends('layouts.staf')

@section('title', 'Tiket Saya — Tugas & Penanganan')

@section('content')
    <div class="flex flex-col w-full">
        <div class="p-4 sm:p-6 lg:p-8 flex flex-col gap-6 w-full" x-data="{
                                                                                        disposisiModalOpen: false,
                                                                                        selectedTicket: null,
                                                                                        openDisposisi(ticket) {
                                                                                            this.selectedTicket = ticket;
                                                                                            this.disposisiModalOpen = true;
                                                                                        }
                                                                                    }">
            <!-- 1. Page Header -->
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-2">
                    <span
                        class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-primary">
                        STAF OPERASIONAL LAPANGAN
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
                <form method="GET" action="{{ route('staf.tiket-saya') }}"
                    class="p-4 bg-surface-container-low/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-b border-outline-variant/40">
                    <div class="relative w-full sm:max-w-md">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/80 text-[18px]">search</span>
                        <input
                            class="w-full pl-9 pr-3 py-2 bg-surface-container-lowest border border-outline-variant/40 rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant/70 focus:outline-none focus:ring-2 focus:ring-primary shadow-xs transition-colors"
                            name="q" value="{{ $search }}" placeholder="Cari ID tiket, pelapor, kendala..." type="text">
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        @if($search)
                            <a href="{{ route('staf.tiket-saya') }}"
                                class="px-3 py-1.5 text-xs text-on-surface-variant hover:text-on-surface">
                                Reset
                            </a>
                        @endif
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-primary-container transition-colors">
                            Cari
                        </button>
                        <a href="{{ route('staf.tiket-saya') }}"
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
                                <tr onclick="window.location='{{ route('staf.tickets.chat', $ticket->id) }}'"
                                    class="hover:bg-primary/5 hover:border-l-4 hover:border-l-primary cursor-pointer transition-all group">
                                    <td class="py-4 px-5 align-top">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-8 bg-primary rounded-full shrink-0"></div>
                                            <span class="font-bold text-primary font-headline-sm">#{{ $ticket->id }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 align-top">
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
                                            <!-- Tombol Disposisi & Ubah Label -->
                                            <button type="button" @click="openDisposisi({{ json_encode($ticket) }})"
                                                class="px-3 py-1.5 rounded-lg border border-outline-variant/80 bg-surface-container-lowest hover:bg-surface-container-low text-on-surface font-semibold text-xs transition-colors flex items-center gap-1.5 shadow-xs"
                                                title="Disposisi ke teknisi lain atau ubah kategori">
                                                <span
                                                    class="material-symbols-outlined text-[16px] text-on-surface-variant">swap_horiz</span>
                                                <span>Disposisi</span>
                                            </button>

                                            <!-- Tombol Selesai -->
                                            <form action="{{ route('staf.tickets.selesai', $ticket) }}" method="POST"
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
                                                memiliki tiket yang sedang dalam pengerjaan. Silakan ambil dari antrean umum.
                                            </p>
                                            <a href="{{ route('staf.dashboard') }}"
                                                class="mt-4 px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-primary-container transition-colors">
                                                Lihat Antrean Tiket Masuk
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div
                    class="p-4 bg-surface-container-low/40 flex items-center justify-between border-t border-outline-variant/30 text-xs text-on-surface-variant">
                    <span>Menampilkan {{ count($myTickets) }} tiket aktif</span>
                </div>
            </div>

            <!-- 4. Modal Disposisi / Pengalihan & Ubah Label Layanan -->
            <div x-show="disposisiModalOpen" x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 overflow-y-auto"
                style="display: none;">
                <div @click.away="disposisiModalOpen = false"
                    class="bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/40 w-full max-w-lg my-8 overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-200">
                    <!-- Modal Header -->
                    <div
                        class="px-6 py-4 bg-surface-container-low/60 border-b border-outline-variant/40 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[22px]">swap_horiz</span>
                            <h3 class="font-headline-sm text-base font-bold text-on-surface">
                                Disposisi &amp; Ubah Label Tiket <span
                                    x-text="'#' + (selectedTicket ? selectedTicket.id : '')"></span>
                            </h3>
                        </div>
                        <button @click="disposisiModalOpen = false" class="text-on-surface-variant hover:text-on-surface">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>

                    <!-- Modal Body / Form -->
                    <form :action="selectedTicket ? '{{ url('staf/tickets') }}/' + selectedTicket.id + '/disposisi' : '#'"
                        method="POST" class="p-6 flex flex-col gap-4">
                        @csrf
                        <div
                            class="bg-surface-container-low/40 p-3 rounded-lg border border-outline-variant/30 text-xs text-on-surface-variant">
                            <span class="font-semibold text-on-surface">Kendala: </span>
                            <span x-text="selectedTicket ? selectedTicket.keluhan : ''"></span>
                        </div>

                        <!-- Ubah Label Layanan (Kategori) -->
                        <div>
                            <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1.5">
                                Kategori / Label Layanan Baru <span class="text-error">*</span>
                            </label>
                            <select name="id_kategori_api" required
                                class="w-full py-2.5 px-3 text-xs bg-surface-container-lowest border border-outline-variant/60 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-on-surface">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        :selected="selectedTicket && selectedTicket.id_kategori_api == {{ $cat->id }}">
                                        {{ $cat->nama_layanan }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-on-surface-variant mt-1">Perbarui kategori jika tiket ini dialihkan
                                ke jenis layanan yang berbeda.</p>
                        </div>

                        <!-- Pilih Staf Teknisi Tujuan -->
                        <div>
                            <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1.5">
                                Pilih Staf Teknisi Tujuan <span class="text-error">*</span>
                            </label>
                            <select name="target_user_id" required
                                class="w-full py-2.5 px-3 text-xs bg-surface-container-lowest border border-outline-variant/60 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-on-surface">
                                <option value="">-- Pilih Staf Penerima --</option>
                                @foreach($otherStaff as $staff)
                                    <option value="{{ $staff->id }}">
                                        {{ $staff->name }} ({{ $staff->bidangStaf->nama_bidang ?? 'Staf Bidang' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-on-surface-variant mt-1">Pilih staf teknisi yang bertugas untuk
                                melanjutkan penanganan kendala ini.</p>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline-variant/40">
                            <button type="button" @click="disposisiModalOpen = false"
                                class="px-4 py-2 border border-outline-variant/60 rounded-lg text-xs font-semibold text-on-surface-variant hover:bg-surface-container-low transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary hover:bg-[#172554] text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">send</span>
                                <span>Simpan &amp; Disposisi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection