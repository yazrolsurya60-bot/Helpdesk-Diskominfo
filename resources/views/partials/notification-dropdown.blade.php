@php
    $currentUserId = auth()->id();
    $unreadNotifCount = $currentUserId 
        ? \App\Models\NotifikasiInternal::where('user_id', $currentUserId)->where('is_read', false)->count() 
        : 0;
    $notifications = $currentUserId 
        ? \App\Models\NotifikasiInternal::where('user_id', $currentUserId)->latest()->take(6)->get() 
        : collect();
@endphp

<div class="relative" x-data="{ notifDropdownOpen: false }" @click.outside="notifDropdownOpen = false">
    <!-- Notification Bell Button -->
    <button @click="notifDropdownOpen = !notifDropdownOpen"
        class="relative p-2 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
        type="button" title="Pemberitahuan &amp; Notifikasi Tugas">
        <span class="material-symbols-outlined text-[22px]">notifications</span>
        @if($unreadNotifCount > 0)
            <span
                class="absolute top-1.5 right-1.5 w-4 h-4 bg-error text-on-error font-label-sm text-label-sm rounded-full flex items-center justify-center text-[10px] leading-none font-bold">
                {{ $unreadNotifCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown Panel -->
    <div x-show="notifDropdownOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         style="display: none;"
         class="absolute right-0 top-full mt-2 w-80 sm:w-96 bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/40 z-50 overflow-hidden flex flex-col">
        
        <!-- Dropdown Header -->
        <div class="px-4 py-3 bg-surface-container-low/60 border-b border-outline-variant/30 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">notifications_active</span>
                <span class="font-headline-sm text-sm font-bold text-on-surface">Notifikasi Tugas</span>
            </div>
            <div class="flex items-center gap-2">
                @if($unreadNotifCount > 0)
                    <form action="{{ route('notifikasi.read-all') }}" method="POST" class="inline m-0">
                        @csrf
                        <button type="submit" class="text-[11px] font-semibold text-primary hover:text-primary/80 hover:underline transition-colors focus:outline-none">
                            Tandai Semua Dibaca
                        </button>
                    </form>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-primary/10 text-primary">
                        {{ $unreadNotifCount }} Baru
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-surface-container text-on-surface-variant">
                        Semua Terbaca
                    </span>
                @endif
            </div>
        </div>

        <!-- Notification List -->
        <div class="divide-y divide-outline-variant/20 max-h-80 overflow-y-auto">
            @forelse($notifications as $notif)
                @php
                    $isTarik = str_contains(strtolower($notif->judul), 'ditarik');
                    $isDistribusi = str_contains(strtolower($notif->judul), 'didistribusikan');
                    $isDisposisi = str_contains(strtolower($notif->judul), 'didisposisikan');
                    $isSelesai = str_contains(strtolower($notif->judul), 'selesai');
                @endphp
                <form action="{{ route('notifikasi.read', $notif->id) }}" method="POST" class="block m-0 p-0">
                    @csrf
                    <button type="submit"
                        class="w-full text-left p-3.5 hover:bg-surface-container-low/80 transition-colors flex items-start gap-3 focus:outline-none focus:bg-surface-container-low {{ !$notif->is_read ? 'bg-primary/5' : '' }}">
                        <!-- Icon Indicator -->
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5
                            @if($isTarik) bg-red-100 text-red-700
                            @elseif($isDistribusi) bg-blue-100 text-blue-700
                            @elseif($isDisposisi) bg-amber-100 text-amber-700
                            @elseif($isSelesai) bg-emerald-100 text-emerald-700
                            @else bg-surface-container text-primary @endif">
                            <span class="material-symbols-outlined text-[18px]">
                                @if($isTarik) undo
                                @elseif($isDistribusi) forward
                                @elseif($isDisposisi) swap_horiz
                                @elseif($isSelesai) check_circle
                                @else info @endif
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-xs leading-snug {{ !$notif->is_read ? 'font-semibold text-on-surface' : 'font-normal text-on-surface-variant' }}">
                                {{ $notif->judul }}
                            </p>
                            <span class="text-[10px] text-on-surface-variant/80 mt-1 block">
                                {{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Baru saja' }}
                            </span>
                        </div>

                        <!-- Unread Dot -->
                        @if(!$notif->is_read)
                            <span class="w-2 h-2 rounded-full bg-primary flex-shrink-0 mt-1.5" title="Belum dibaca"></span>
                        @endif
                    </button>
                </form>
            @empty
                <div class="py-8 px-4 text-center text-on-surface-variant flex flex-col items-center justify-center">
                    <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant mb-2">
                        <span class="material-symbols-outlined text-xl">notifications_none</span>
                    </div>
                    <span class="text-xs font-semibold text-on-surface">Tidak ada notifikasi</span>
                    <p class="text-[11px] text-on-surface-variant mt-0.5 max-w-xs">
                        Pemberitahuan penugasan, disposisi, dan status tiket akan muncul di sini.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Dropdown Footer -->
        <div class="p-2.5 bg-surface-container-low/40 border-t border-outline-variant/30 flex items-center justify-between px-4">
            <span class="text-[10px] text-on-surface-variant">Pemberitahuan otomatis sistem Helpdesk PINTAS</span>
            @if($unreadNotifCount > 0)
                <form action="{{ route('notifikasi.read-all') }}" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit" class="text-[10px] font-semibold text-primary hover:underline focus:outline-none">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
