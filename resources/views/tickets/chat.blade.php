<!DOCTYPE html>
<html class="h-full bg-slate-50" lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Detail Tiket #{{ $ticket->id }} | Helpdesk PINTAS Diskominfo</title>
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Tailwind CSS with custom styling -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Inter"', 'sans-serif'],
                        chat: ['"Inter"', 'sans-serif']
                    },
                    colors: {
                        navy: {
                            50: '#f0f5ff',
                            100: '#e0eaff',
                            500: '#3b82f6',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a'
                        },
                        whatsapp: {
                            DEFAULT: '#25D366',
                            dark: '#128C7E',
                            teal: '#075E54',
                            light: '#E7FCE8',
                            bubble: '#d9fdd3'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom clean scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* WhatsApp subtle dot pattern background */
        .chat-pattern-bg {
            background-color: #efeae2;
            background-image: radial-gradient(#d3cbbf 0.75px, transparent 0.75px);
            background-size: 16px 16px;
        }
    </style>
</head>

<body class="h-full flex flex-col font-sans text-slate-800 bg-[#f4f3fa] antialiased overflow-hidden">

    <!-- 1. TOP HEADER -->
    <header class="bg-white border-b border-slate-200/90 shrink-0 z-20 shadow-xs">
        <div class="px-5 py-3 max-w-[1920px] mx-auto flex flex-wrap items-center justify-between gap-4">
            <!-- Left: Back Button -->
            <div class="flex items-center gap-3">
                <a href="{{ $backRoute }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 hover:text-navy-900 transition-colors border border-slate-200 shadow-xs">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>{{ $backLabel }}</span>
                </a>
            </div>

            <!-- Center: Ticket ID & Status Badge -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase tracking-wider font-bold text-slate-400">Nomor:</span>
                    <span
                        class="text-base font-extrabold text-navy-950 font-mono tracking-tight">#{{ $ticket->id }}</span>
                </div>

                @if($ticket->status === 'progress')
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $isPastSla ? 'bg-red-50 text-red-700 border border-red-300' : 'bg-amber-50 text-amber-800 border border-amber-300' }}">
                        <span
                            class="w-2 h-2 rounded-full {{ $isPastSla ? 'bg-red-600 animate-ping' : 'bg-amber-500 animate-pulse' }}"></span>
                        {{ $isPastSla ? 'Melebihi SLA' : 'In Progress' }}
                    </span>
                @elseif($ticket->status === 'selesai')
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                        <i class="fa-solid fa-check-circle text-emerald-600 text-[11px]"></i>
                        Selesai
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-300">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Open
                    </span>
                @endif
            </div>

            <!-- Right: Timestamps & Priority -->
            <div class="flex items-center gap-4 text-xs">
                <div class="hidden sm:flex items-center gap-2 text-slate-500">
                    <i class="fa-regular fa-clock text-slate-400"></i>
                    <span>Dibuat: <strong
                            class="text-slate-700 font-medium">{{ $ticket->created_at ? $ticket->created_at->format('d M Y, H:i') : '-' }}
                            WIB</strong></span>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. MAIN WORKSPACE (SPLIT 2 COLUMNS: 35% LEFT, 65% RIGHT) -->
    <main
        class="flex-1 max-w-[1920px] w-full mx-auto p-3 lg:p-4 gap-4 grid grid-cols-1 lg:grid-cols-12 overflow-hidden">

        <!-- ========================================== -->
        <!-- KOLOM KIRI: INFO & KONTROL TIKET (~35%)    -->
        <!-- ========================================== -->
        <section
            class="lg:col-span-4 xl:col-span-4 h-full flex flex-col bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <!-- Header Panel Kiri -->
            <div
                class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-navy-900 text-sm"></i>
                    <h2 class="font-bold text-sm text-slate-900 tracking-tight">Informasi &amp; Kontrol Tiket</h2>
                </div>
                <span class="text-[11px] text-slate-500 font-mono font-medium">ID: #{{ $ticket->id }}</span>
            </div>

            <!-- Scrollable Body Panel Kiri -->
            <div class="flex-1 overflow-y-auto p-5 space-y-5 divide-y divide-slate-100">

                <!-- Section 1: Data Pemohon -->
                <div class="pt-0 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Data Pemohon</span>
                        <span
                            class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-[9px]"></i> Terverifikasi WhatsApp
                        </span>
                    </div>

                    <!-- User Card -->
                    <div class="flex items-start gap-3.5 p-3 rounded-xl bg-slate-50/90 border border-slate-200/80">
                        <div
                            class="w-11 h-11 rounded-xl bg-linear-to-tr from-navy-900 to-navy-800 text-white font-bold text-sm flex items-center justify-center shadow-xs shrink-0">
                            {{ strtoupper(substr($ticket->nama_pemohon ?? 'PM', 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-900 text-sm truncate">
                                {{ $ticket->nama_pemohon ?? 'Pemohon Umum' }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5 truncate">
                                <i class="fa-solid fa-user-tag text-slate-400 text-[11px]"></i>
                                Pelapor Pengaduan Layanan
                            </p>
                            <!-- Direct WhatsApp Link -->
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->nomor_wa) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 mt-2 text-xs font-semibold text-whatsapp-teal hover:text-whatsapp transition-colors">
                                <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                                <span class="font-mono">{{ $ticket->nomor_wa }}</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400 ml-0.5"></i>
                            </a>
                        </div>
                    </div>

                    <!-- SLA & Waktu Durasi -->
                    <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[11px]">Waktu Pengajuan</span>
                            <span
                                class="font-semibold text-slate-700 mt-0.5 block truncate">{{ $ticket->created_at ? $ticket->created_at->format('d M, H:i') : '-' }}
                                WIB</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[11px]">Durasi Kerja SLA</span>
                            <span
                                class="font-semibold {{ $isPastSla ? 'text-red-600' : 'text-amber-700' }} mt-0.5 block flex items-center gap-1 truncate"
                                title="Dihitung di jam kerja (Senin-Jumat, 07:30 - 15:45)">
                                <i class="fa-regular fa-hourglass-half text-[11px]"></i> {{ $hoursHeld }} Jam
                                ({{ $workingMinutes }} mnt)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Keluhan Awal -->
                <div class="pt-4 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Keluhan Awal</span>
                    <div
                        class="p-3.5 bg-amber-50/50 rounded-xl border-l-4 border-l-amber-400 border border-slate-200/80 relative">
                        <i
                            class="fa-solid fa-quote-left absolute right-3 top-3 text-slate-200 text-xl pointer-events-none"></i>
                        <p class="text-xs leading-relaxed text-slate-700 italic pr-4 whitespace-pre-line">
                            "{{ $ticket->keluhan }}"
                        </p>
                    </div>
                </div>

                <!-- Section 3: Kategori Layanan & Petugas -->
                <div class="pt-4 space-y-2.5">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Kategori
                        Layanan</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-navy-900 border border-blue-200">
                            <i class="fa-solid fa-network-wired text-navy-800 text-xs"></i>
                            {{ $ticket->kategori->nama_layanan ?? 'Umum' }}
                        </span>
                        @if($ticket->user)
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="fa-solid fa-user-check text-[11px] text-slate-500"></i>
                                Petugas: <strong class="ml-0.5">{{ $ticket->user->name }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Section 4: Lampiran Berkas (Jika ada) -->
                @if($ticket->lampiran && $ticket->lampiran->count() > 0)
                    <div class="pt-4 space-y-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Lampiran Berkas
                            ({{ $ticket->lampiran->count() }})</span>
                        <div class="space-y-1.5">
                            @foreach($ticket->lampiran as $lampiran)
                                <a href="{{ asset('storage/' . $lampiran->file_path) }}" target="_blank"
                                    class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs text-slate-700 transition">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fa-solid fa-paperclip text-slate-400"></i>
                                        <span class="truncate">{{ basename($lampiran->file_path) }}</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-down text-slate-400 text-[11px]"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Section 5: Aktivitas & Jejak Audit Terkini -->
                <div class="pt-4 space-y-2.5">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Aktivitas &amp; Jejak
                        Audit</span>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @forelse($ticket->riwayat as $log)
                            <div class="flex items-start gap-2.5 text-xs text-slate-600">
                                <span class="w-2 h-2 rounded-full bg-navy-500 mt-1.5 shrink-0"></span>
                                <div>
                                    <span
                                        class="font-medium text-slate-800 capitalize">{{ str_replace('_', ' ', $log->aksi) }}</span>
                                    <span
                                        class="text-[10px] text-slate-400 ml-1">({{ $log->created_at ? $log->created_at->format('d/m H:i') : '-' }})</span>
                                    <p class="text-[11px] text-slate-500">Oleh: {{ $log->user->name ?? 'Sistem' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Belum ada riwayat aktivitas.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Sticky Bottom Actions -->
            <div class="p-4 bg-slate-50/90 border-t border-slate-200/90 shrink-0 space-y-2">
                @if($ticket->status === 'selesai')
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-center">
                        <span class="text-xs font-semibold text-emerald-800 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-check-double text-emerald-600"></i>
                            Tiket ini telah diselesaikan
                        </span>
                    </div>
                @elseif($ticket->user_id === auth()->id())
                    <button type="button" onclick="openSelesaiModal()"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold py-3 px-4 rounded-xl flex items-center justify-center gap-2 shadow-xs transition-all duration-150 transform hover:-translate-y-0.5 text-sm">
                        <i class="fa-solid fa-circle-check text-base"></i>
                        <span>SELESAIKAN TUGAS</span>
                    </button>
                @elseif($ticket->status === 'open')
                    <div class="p-3 bg-blue-50 rounded-xl border border-blue-200 text-center">
                        <span class="text-xs font-medium text-blue-800 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-info-circle text-blue-600"></i>
                            Tiket berstatus Open di antrean umum
                        </span>
                    </div>
                @else
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-center">
                        <span class="text-xs font-medium text-amber-800 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-user-clock text-amber-600"></i>
                            Sedang ditangani oleh {{ $ticket->user->name ?? 'Petugas lain' }}
                        </span>
                    </div>
                @endif
            </div>
        </section>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: RUANG CHAT WHATSAPP (~65%)   -->
        <!-- ========================================== -->
        <section
            class="lg:col-span-8 xl:col-span-8 h-full flex flex-col bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">

            <!-- Chat Header (WhatsApp Web Enterprise Style) -->
            <div
                class="px-5 py-3 bg-[#f0f2f5] border-b border-slate-200/90 flex items-center justify-between shrink-0 z-10">
                <!-- Left: Profile & WhatsApp Status -->
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div
                            class="w-10 h-10 rounded-full bg-slate-300 text-slate-700 font-bold flex items-center justify-center text-sm border-2 border-white shadow-xs">
                            {{ strtoupper(substr($ticket->nama_pemohon ?? 'P', 0, 2)) }}
                        </div>
                        <span
                            class="absolute bottom-0 right-0 w-3 h-3 bg-whatsapp border-2 border-white rounded-full"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-900 text-sm leading-tight">
                                {{ $ticket->nama_pemohon ?? 'Pemohon' }}
                            </h3>
                            <span
                                class="px-1.5 py-0.5 text-[10px] bg-slate-200 text-slate-700 rounded font-medium">Pemohon</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-0.5">
                            <span class="text-emerald-700 font-medium flex items-center gap-1">
                                <i class="fa-brands fa-whatsapp text-emerald-600 text-xs"></i> {{ $ticket->nomor_wa }}
                            </span>
                            <span>•</span>
                            <span class="text-[11px] text-slate-500">WhatsApp Gateway Helpdesk</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Utility Actions -->
                <div class="flex items-center gap-1 text-slate-600">
                    <button type="button" onclick="location.reload()"
                        class="p-2 rounded-full hover:bg-slate-200 text-slate-600 hover:text-slate-800 transition"
                        title="Muat Ulang Pesan">
                        <i class="fa-solid fa-arrows-rotate text-sm"></i>
                    </button>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->nomor_wa) }}" target="_blank"
                        class="p-2 rounded-full hover:bg-slate-200 text-slate-600 hover:text-emerald-600 transition"
                        title="Buka di WhatsApp Web Langsung">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Chat Body: Messages Timeline -->
            <div id="chatTimeline" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 chat-pattern-bg font-chat">

                <!-- Date Separator -->
                <div class="flex justify-center my-2">
                    <span
                        class="px-3 py-1 rounded-lg bg-white/90 shadow-2xs border border-slate-200/60 text-[11px] font-semibold text-slate-600 uppercase tracking-wide">
                        {{ $ticket->created_at ? $ticket->created_at->format('l, d F Y') : 'Hari Ini' }}
                    </span>
                </div>

                <!-- Encryption Notice -->
                <div class="flex justify-center my-1">
                    <div
                        class="max-w-md bg-amber-100/90 border border-amber-200 text-amber-900 text-xs text-center px-3 py-1.5 rounded-lg shadow-2xs">
                        <i class="fa-solid fa-lock text-[10px] mr-1"></i> Pesan terenkripsi aman secara end-to-end
                        melalui WhatsApp Gateway Helpdesk.
                    </div>
                </div>

                <!-- Pesan Awal (Keluhan Pemohon) -->
                <div class="flex items-end gap-2 max-w-xl">
                    <div
                        class="w-7 h-7 rounded-full bg-slate-300 text-slate-700 text-xs font-bold flex items-center justify-center shrink-0 mb-1">
                        {{ strtoupper(substr($ticket->nama_pemohon ?? 'P', 0, 2)) }}
                    </div>
                    <div
                        class="bg-white rounded-2xl rounded-bl-sm p-3.5 shadow-xs border border-slate-200/70 text-slate-800 text-sm">
                        <div class="flex items-center justify-between gap-4 mb-1">
                            <span class="text-[11px] font-bold text-navy-900">{{ $ticket->nama_pemohon ?? 'Pemohon' }}
                                (Keluhan Masuk)</span>
                        </div>
                        <p class="leading-relaxed whitespace-pre-line">{{ $ticket->keluhan }}</p>
                        <div class="flex items-center justify-end gap-1 mt-1 text-[10px] text-slate-400 font-mono">
                            <span>{{ $ticket->created_at ? $ticket->created_at->format('H:i') : '-' }}</span>
                            <i class="fa-solid fa-check text-slate-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Loop Riwayat Pesan dari wa_logs -->
                @forelse($ticket->waLogs as $log)
                    @if($log->sender_type === 'pemohon')
                        <!-- Bubble Kiri: Pesan dari Pemohon -->
                        <div class="flex items-end gap-2 max-w-xl">
                            <div
                                class="w-7 h-7 rounded-full bg-slate-300 text-slate-700 text-xs font-bold flex items-center justify-center shrink-0 mb-1">
                                {{ strtoupper(substr($ticket->nama_pemohon ?? 'P', 0, 2)) }}
                            </div>
                            <div
                                class="bg-white rounded-2xl rounded-bl-sm p-3.5 shadow-xs border border-slate-200/70 text-slate-800 text-sm">
                                <div class="flex items-center justify-between gap-4 mb-1">
                                    <span
                                        class="text-[11px] font-bold text-navy-900">{{ $ticket->nama_pemohon ?? 'Pemohon' }}</span>
                                </div>
                                <p class="leading-relaxed whitespace-pre-line">{{ $log->message }}</p>
                                <div class="flex items-center justify-end gap-1 mt-1 text-[10px] text-slate-400 font-mono">
                                    <span>{{ $log->created_at ? $log->created_at->format('H:i') : '-' }}</span>
                                    <i class="fa-solid fa-check text-slate-400"></i>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Bubble Kanan: Balasan Petugas Helpdesk (Hijau WhatsApp) -->
                        <div class="flex items-end justify-end gap-2 ml-auto max-w-xl">
                            <div
                                class="bg-whatsapp-bubble rounded-2xl rounded-br-sm p-3.5 shadow-xs border border-emerald-200/80 text-slate-900 text-sm">
                                <div class="flex items-center justify-between gap-4 mb-1">
                                    <span class="text-[11px] font-bold text-emerald-900">Anda (Petugas Helpdesk)</span>
                                </div>
                                <p class="leading-relaxed whitespace-pre-line">{{ $log->message }}</p>
                                <div class="flex items-center justify-end gap-1 mt-1 text-[10px] text-slate-500 font-mono">
                                    <span>{{ $log->created_at ? $log->created_at->format('H:i') : '-' }}</span>
                                    <i class="fa-solid fa-check-double text-blue-500"></i>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <!-- Placeholder jika belum ada pesan balasan -->
                    <div class="text-center py-4">
                        <span
                            class="text-xs text-slate-500 bg-white/80 px-3 py-1.5 rounded-full border border-slate-200 shadow-2xs">
                            Belum ada balasan percakapan. Ketik pesan di bawah untuk membalas ke WhatsApp pemohon.
                        </span>
                    </div>
                @endforelse

            </div>

            <!-- Chat Footer / Form Input Balasan WhatsApp -->
            <div class="p-3 bg-[#f0f2f5] border-t border-slate-200/90 shrink-0">
                @if(session('success'))
                    <div
                        class="mb-2 p-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-lg flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            {{ session('success') }}
                        </span>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
                    </div>
                @endif

                @if($ticket->status !== 'selesai')
                    <form action="{{ $sendRoute }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <!-- Attachment & Emoticon Buttons -->
                        <div class="flex items-center gap-0.5 text-slate-500">
                            <button type="button" class="p-2.5 rounded-full hover:bg-slate-200 text-slate-600 transition"
                                title="Emoticon">
                                <i class="fa-regular fa-face-smile text-lg"></i>
                            </button>
                        </div>

                        <!-- Text Input -->
                        <div class="flex-1 relative">
                            <input type="text" name="message" id="messageInput" required autocomplete="off"
                                class="w-full py-2.5 px-4 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                                placeholder="Ketik pesan balasan ke pemohon via WhatsApp...">
                        </div>

                        <!-- Send Button -->
                        <button type="submit"
                            class="bg-whatsapp hover:bg-whatsapp-dark active:bg-whatsapp-teal text-white font-bold px-5 py-2.5 rounded-xl shadow-xs transition-colors flex items-center gap-2 text-sm shrink-0">
                            <span>KIRIM</span>
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </form>
                @else
                    <div class="text-center py-2 text-xs text-slate-500 italic">
                        Tiket ini sudah berstatus selesai. Percakapan chat telah diarsipkan.
                    </div>
                @endif
            </div>

        </section>
    </main>

    <!-- Modal Konfirmasi Selesaikan Tugas -->
    <div id="selesaiModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
            <div
                class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="text-center">
                <h3 class="text-base font-bold text-slate-900">Konfirmasi Penyelesaian Tiket</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Apakah Anda yakin telah menyelesaikan kendala tiket <strong>#{{ $ticket->id }}</strong> milik
                    <strong>{{ $ticket->nama_pemohon }}</strong>?
                </p>
            </div>
            <form action="{{ $finishRoute }}" method="POST" class="pt-2 flex items-center justify-end gap-2.5">
                @csrf
                <button type="button" onclick="closeSelesaiModal()"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white shadow-xs transition">
                    Ya, Selesaikan Tiket
                </button>
            </form>
        </div>
    </div>

    <!-- Script: Auto-Scroll Chat Timeline to Bottom -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const chatTimeline = document.getElementById('chatTimeline');
            if (chatTimeline) {
                chatTimeline.scrollTop = chatTimeline.scrollHeight;
            }
        });

        function openSelesaiModal() {
            document.getElementById('selesaiModal').classList.remove('hidden');
        }

        function closeSelesaiModal() {
            document.getElementById('selesaiModal').classList.add('hidden');
        }
    </script>
</body>

</html>