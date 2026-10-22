@extends('layouts.frontdesk')

@section('title', 'Monitoring Staf Bidang & Tarik Tiket')

@section('content')
    <div class="flex flex-col w-full">
        <div class="p-4 sm:p-6 lg:p-8 flex flex-col gap-6 w-full">
            <!-- 1. Header Title Section -->
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-2">
                    <span
                        class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-primary">
                        SLA &amp; WORKLOAD GOVERNANCE
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold font-headline-lg text-on-surface tracking-tight mt-1">
                    Monitoring Staf Bidang — Pemantauan &amp; Tarik Tiket Terabaikan
                </h1>
                <p class="text-sm font-body-md text-on-surface-variant">
                    Pantau beban kerja seluruh staf bidang/teknisi, lacak durasi tiket yang belum ditindaklanjuti, dan tarik
                    kembali tiket yang terabaikan ke antrean utama.
                </p>
            </div>

            <!-- 2. Top 3 Metric Cards (Grid 3 Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Card 1: Tiket di Tangan Staf -->
                <div
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            TIKET DI TANGAN STAF
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">pending_actions</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold text-on-surface leading-none">
                            {{ $statTiketDiTanganStaf }}
                        </span>
                    </div>
                </div>

                <!-- Card 2: Tiket > 2 Jam di Staf -->
                <div
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            TIKET &gt; 2 JAM DI STAF
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">timer_off</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold leading-none text-red-600">
                            {{ $statMendekatiBatasSla }}
                        </span>
                    </div>
                </div>

                <!-- Card 3: Tiket Ditarik Hari Ini -->
                <div
                    class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 sm:p-6 border border-outline-variant/40 shadow-sm hover:shadow-md transition-shadow sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between">
                        <span
                            class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">
                            TIKET DITARIK HARI INI
                        </span>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">undo</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline">
                        <span class="font-headline-lg text-3xl font-bold leading-none text-on-surface">
                            {{ $statDitarikHariIni }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. Table Container with Integrated Filter Bar -->
            <div
                class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/40 overflow-hidden flex flex-col w-full">
                <!-- Filter Bar -->
                <form method="GET" action="{{ route('frontdesk.monitoring') }}"
                    class="p-4 bg-surface-container-low/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-b border-outline-variant/40">
                    <!-- Sisi Kiri: Input Pencarian -->
                    <div class="relative w-full sm:w-80 md:w-96">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">filter_list</span>
                        <input
                            class="w-full pl-9 pr-8 py-2 bg-surface-container-lowest rounded-lg border border-outline-variant/50 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
                            name="q" value="{{ $search }}" placeholder="Cari nama teknisi, ID tiket, kendala..."
                            type="text">
                        @if($search)
                            <a href="{{ route('frontdesk.monitoring', ['bidang' => $bidangFilter]) }}"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </a>
                        @endif
                    </div>

                    <!-- Sisi Kanan: Filter Bidang & Refresh Button -->
                    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                        <div class="w-full sm:w-64">
                            <select name="bidang" onchange="this.form.submit()"
                                class="w-full py-2 px-3 font-body-sm text-body-sm bg-surface-container-lowest border border-outline-variant/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-on-surface shadow-xs cursor-pointer">
                                <option value="">Semua Bidang</option>
                                @foreach($bidangList as $b)
                                    <option value="{{ $b->id }}" {{ $bidangFilter == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama_bidang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <a href="{{ route('frontdesk.monitoring') }}"
                            class="p-2 rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors flex items-center justify-center shadow-xs shrink-0"
                            title="Muat Ulang Monitoring">
                            <span class="material-symbols-outlined text-[20px]">refresh</span>
                        </a>
                    </div>
                </form>

                <!-- 4. Table: Monitoring Staf Bidang -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full min-w-215.5 text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-surface-container-low/60 text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider border-b border-outline-variant/40">
                                <th class="py-3.5 px-5 font-semibold">ID TIKET</th>
                                <th class="py-3.5 px-5 font-semibold">STAF BIDANG PENANGGUNG JAWAB</th>
                                <th class="py-3.5 px-5 font-semibold">PELAPOR &amp; KENDALA</th>
                                <th class="py-3.5 px-5 font-semibold">KATEGORI</th>
                                <th class="py-3.5 px-5 font-semibold">STATUS &amp; DURASI</th>
                                <th class="py-3.5 px-5 text-right font-semibold">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-sm text-body-sm text-on-surface divide-y divide-outline-variant/20">
                            @forelse($ticketsMonitoring as $ticket)
                                @php
                                    $workingMinutes = \App\Services\SlaService::getWorkingMinutesElapsed($ticket->assigned_at);
                                    $hoursHeld = round($workingMinutes / 60, 1);
                                    $isUrgent = $workingMinutes >= 120;
                                @endphp
                                <tr onclick="window.location='{{ route('frontdesk.tickets.chat', $ticket->id) }}'" class="hover:bg-primary/5 hover:border-l-4 hover:border-l-primary cursor-pointer transition-all group">
                                    <td class="py-4 px-5 whitespace-nowrap font-bold text-primary">
                                        #{{ $ticket->id }}
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-on-surface">
                                        <div class="flex items-center gap-2.5">
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-100 text-primary flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($ticket->user->name ?? 'ST', 0, 2)) }}
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span
                                                    class="font-semibold text-on-surface truncate">{{ $ticket->user->name ?? 'Tidak diketahui' }}</span>
                                                <span
                                                    class="text-xs text-on-surface-variant truncate">{{ $ticket->user->bidangStaf->nama_bidang ?? 'Staf Bidang' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-on-surface max-w-xs">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-on-surface">
                                                {{ $ticket->nama_pemohon }}
                                                <span class="text-xs text-on-surface-variant font-normal">•
                                                    {{ $ticket->nomor_wa }}</span>
                                            </span>
                                            <span class="text-xs text-on-surface-variant truncate mt-0.5"
                                                title="{{ $ticket->keluhan }}">
                                                {{ $ticket->keluhan }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded bg-surface-container text-on-surface font-label-sm text-label-sm font-medium">
                                            {{ $ticket->kategori->nama_layanan ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $isUrgent ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full {{ $isUrgent ? 'bg-red-600 animate-ping' : 'bg-amber-500' }}"></span>
                                                In Progress
                                            </span>
                                            <span
                                                class="text-xs {{ $isUrgent ? 'text-red-600 font-semibold' : 'text-on-surface-variant' }} mt-0.5"
                                                title="Durasi dihitung berdasarkan jam operasional kantor (Senin-Jumat, 07:30 - 15:45)">
                                                Kerja: {{ $hoursHeld }} jam
                                                ({{ $ticket->assigned_at ? $ticket->assigned_at->diffForHumans() : '-' }})
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                        <button type="button"
                                            onclick="openRecallModal({{ $ticket->id }}, '{{ addslashes($ticket->user->name ?? 'Staf') }}', '{{ addslashes($ticket->keluhan) }}', '{{ $hoursHeld }} jam kerja ({{ $ticket->assigned_at ? $ticket->assigned_at->diffForHumans() : '-' }})')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors"
                                            title="Tarik kembali ke Antrean Utama">
                                            <span class="material-symbols-outlined text-[16px]">undo</span>
                                            <span>Tarik Tiket</span>
                                        </button>
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
                                            <span class="text-base font-semibold text-on-surface">Tidak ada tiket di tangan staf
                                                bidang</span>
                                            <p class="text-xs text-on-surface-variant mt-1 max-w-sm">Saat ini seluruh tiket yang
                                                ditangani staf bidang berjalan lancar atau belum ada penugasan aktif.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer / Pagination -->
                @if($ticketsMonitoring->hasPages())
                    <div
                        class="p-4 bg-surface-container-lowest border-t border-outline-variant/30 flex items-center justify-between font-label-sm text-label-sm text-on-surface-variant">
                        {{ $ticketsMonitoring->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL: Konfirmasi Tarik Tiket --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity hidden"
        id="recallModal">
        <div class="bg-surface-container-lowest rounded-xl max-w-lg w-full p-6 shadow-xl border border-outline-variant/40 flex flex-col gap-4 transform transition-all scale-95 opacity-0"
            id="recallModalBox">
            <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">undo</span>
                </div>
                <button
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors"
                    onclick="closeRecallModal()" type="button">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="flex flex-col gap-1">
                <h3 class="text-lg font-bold font-headline-sm text-on-surface">Konfirmasi Penarikan Tiket</h3>
                <p class="text-xs font-body-sm text-on-surface-variant">Tiket ini akan dicabut dari penanggung jawab saat
                    ini dan dikembalikan ke antrean utama agar staf lain dapat segera menanganinya.</p>
            </div>

            {{-- Ticket Data Summary Card --}}
            <div class="p-4 rounded-lg bg-surface-container-low/70 border border-outline-variant/30 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm uppercase text-on-surface-variant font-semibold">No.
                        Tiket</span>
                    <span class="font-bold text-primary text-sm font-headline-sm" id="modalTicketId"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm uppercase text-on-surface-variant font-semibold">Penanggung
                        Jawab</span>
                    <span class="font-semibold text-on-surface text-sm" id="modalStaffName"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm uppercase text-on-surface-variant font-semibold">Kendala
                        Pelapor</span>
                    <span class="text-xs text-on-surface truncate max-w-240px" id="modalIssue"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm uppercase text-on-surface-variant font-semibold">Waktu
                        Ditangani</span>
                    <span class="text-xs text-red-600 font-bold" id="modalDormancy"></span>
                </div>
            </div>

            {{-- Form Reason for Recall --}}
            <form id="recallForm" method="POST">
                @csrf
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1.5">
                        <label class="font-label-sm text-label-sm uppercase font-semibold text-on-surface"
                            for="recallReason">Alasan Penarikan ke Antrean Utama <span class="text-red-500">*</span></label>
                        <select name="alasan"
                            class="w-full bg-surface-container-lowest border border-outline-variant/50 rounded-lg p-2.5 text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
                            id="recallReason" required>
                            <option value="Terabaikan > 2 Jam tanpa catatan progres lapangan">Terabaikan > 2 Jam tanpa
                                catatan progres lapangan</option>
                            <option value="Eskalasi mendesak dari Pimpinan / SLA Kritis">Eskalasi mendesak dari Pimpinan /
                                SLA Kritis</option>
                            <option value="Teknisi berhalangan / sedang menangani insiden lain">Teknisi berhalangan / sedang
                                menangani insiden lain</option>
                            <option value="Peralihan ke shift teknisi cadangan">Peralihan ke shift teknisi cadangan</option>
                            <option value="Permintaan langsung dari pelapor / penyesuaian triase">Permintaan langsung dari
                                pelapor / penyesuaian triase</option>
                        </select>
                    </div>

                    <div class="p-3 rounded-lg bg-blue-50 border border-blue-200/60 flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-primary text-[18px]">notifications_active</span>
                        <span class="text-xs text-blue-900 leading-relaxed">Teknisi penanggung jawab akan menerima
                            notifikasi pembatalan penugasan secara otomatis.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            class="px-4 py-2 rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-on-surface hover:bg-surface-container font-medium text-xs transition-colors shadow-xs"
                            onclick="closeRecallModal()" type="button">
                            Batal
                        </button>
                        <button
                            class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium text-xs shadow-sm flex items-center gap-1.5 transition-colors"
                            type="submit">
                            <span class="material-symbols-outlined text-[16px]">undo</span>
                            <span>Tarik Tiket Sekarang</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRecallModal(ticketId, staffName, issue, dormancy) {
            document.getElementById('modalTicketId').textContent = '#' + ticketId;
            document.getElementById('modalStaffName').textContent = staffName;
            document.getElementById('modalIssue').textContent = issue;
            document.getElementById('modalDormancy').textContent = dormancy;
            document.getElementById('recallForm').action = '/frontdesk/tickets/' + ticketId + '/tarik';

            const modal = document.getElementById('recallModal');
            const box = document.getElementById('recallModalBox');

            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeRecallModal() {
            const modal = document.getElementById('recallModal');
            const box = document.getElementById('recallModalBox');

            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }

        document.getElementById('recallModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeRecallModal();
        });
    </script>
@endsection