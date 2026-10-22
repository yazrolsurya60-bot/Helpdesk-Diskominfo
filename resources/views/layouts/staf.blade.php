<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="web_dashboard" name="shell-type" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@600;700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <style>
        @layer base {

            html,
            body {
                margin: 0;
                padding: 0;
            }

            body {
                overscroll-behavior: none;
            }

            main> :first-child {
                margin-top: 0 !important;
            }

            main> :last-child {
                margin-bottom: 0 !important;
            }
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-dim": "#dad9e1",
                        "surface-container": "#eeedf4",
                        "primary": "#1e3a8a",
                        "on-error": "#ffffff",
                        "inverse-surface": "#2f3036",
                        "surface-container-low": "#f4f3fa",
                        "tertiary-container": "#3e4041",
                        "on-tertiary-fixed": "#1a1c1c",
                        "on-secondary-fixed": "#191c1e",
                        "primary-fixed-dim": "#b6c4ff",
                        "on-background": "#1a1b21",
                        "inverse-on-surface": "#f1f0f7",
                        "surface": "#faf8ff",
                        "secondary-fixed-dim": "#c4c7c9",
                        "background": "#faf8ff",
                        "on-tertiary": "#ffffff",
                        "surface-container-high": "#e9e7ef",
                        "tertiary": "#282a2b",
                        "tertiary-fixed": "#e2e2e2",
                        "on-error-container": "#93000a",
                        "on-secondary-container": "#626567",
                        "on-tertiary-container": "#abacac",
                        "outline": "#757682",
                        "outline-variant": "#c5c5d3",
                        "on-secondary": "#ffffff",
                        "on-primary-fixed": "#00164e",
                        "primary-fixed": "#dce1ff",
                        "surface-bright": "#faf8ff",
                        "inverse-primary": "#b6c4ff",
                        "secondary": "#5c5f61",
                        "tertiary-fixed-dim": "#c6c6c7",
                        "secondary-container": "#e0e3e5",
                        "secondary-fixed": "#e0e3e5",
                        "on-surface-variant": "#444651",
                        "surface-container-highest": "#e3e1e9",
                        "surface-container-lowest": "#ffffff",
                        "surface-variant": "#e3e1e9",
                        "error-container": "#ffdad6",
                        "surface-tint": "#1e3a8a",
                        "on-primary-fixed-variant": "#264191",
                        "on-secondary-fixed-variant": "#444749",
                        "on-tertiary-fixed-variant": "#454747",
                        "primary-container": "#bfdbfe",
                        "on-primary": "#ffffff",
                        "on-surface": "#1a1b21",
                        "error": "#ba1a1a",
                        "on-primary-container": "#1e3a8a"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    spacing: {
                        margin_container: "32px",
                        gutter: "24px",
                        stack_md: "16px",
                        stack_sm: "8px",
                        sidebar_width: "260px",
                        stack_lg: "24px",
                        header_height: "64px"
                    },
                    fontFamily: {
                        "headline-sm": ["Plus Jakarta Sans"],
                        "label-md": ["Inter"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "headline-lg": ["Plus Jakarta Sans"],
                        "body-lg": ["Inter"],
                        "headline-md": ["Plus Jakarta Sans"],
                        "headline-lg-mobile": ["Plus Jakarta Sans"],
                        "label-sm": ["Inter"]
                    },
                    fontSize: {
                        "headline-sm": ["16px", { lineHeight: "24px", fontWeight: "600" }],
                        "label-md": ["12px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }],
                        "body-md": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                        "body-sm": ["13px", { lineHeight: "18px", fontWeight: "400" }],
                        "headline-lg": ["24px", { lineHeight: "32px", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "body-lg": ["16px", { lineHeight: "24px", fontWeight: "400" }],
                        "headline-md": ["20px", { lineHeight: "28px", fontWeight: "600" }],
                        "headline-lg-mobile": ["20px", { lineHeight: "28px", fontWeight: "700" }],
                        "label-sm": ["11px", { lineHeight: "14px", fontWeight: "500" }]
                    }
                }
            }
        };
    </script>
    <title>PINTAS - @yield('title', 'Staf Bidang')</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-surface font-body-md text-on-surface antialiased" x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"
        @click="sidebarOpen = false"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed left-0 top-0 h-full w-[260px] bg-primary text-on-primary z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)] transition-transform duration-300 lg:translate-x-0">
        <div class="flex flex-col">
            <div class="h-[64px] px-gutter flex items-center gap-stack_sm">
                <div
                    class="w-8 h-8 rounded-lg bg-surface-container-lowest text-primary flex items-center justify-center font-headline-sm text-headline-sm font-bold shadow-sm">
                    P
                </div>
                <div class="flex flex-col leading-tight">
                    <span
                        class="font-headline-sm text-headline-sm text-on-primary tracking-tight font-bold">PINTAS</span>
                </div>
            </div>
            <div class="px-gutter pt-stack_md pb-stack_sm">
                <span
                    class="font-label-sm text-label-sm text-primary-fixed-dim uppercase tracking-wider font-semibold">Menu
                    Utama</span>
            </div>
            <nav class="flex flex-col px-stack_sm space-y-1">
                <a aria-current="{{ Route::currentRouteName() === 'staf.dashboard' ? 'page' : 'false' }}"
                    class="flex items-center gap-stack_sm px-stack_md py-stack_sm rounded transition-colors {{ Route::currentRouteName() === 'staf.dashboard' ? 'bg-white/10 text-on-primary font-semibold shadow-sm border-l-4 border-white' : 'text-primary-fixed-dim hover:bg-white/5 hover:text-on-primary border-l-4 border-transparent' }}"
                    href="{{ route('staf.dashboard') }}">
                    <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                    <span class="font-body-md text-body-md">Dashboard Umum</span>
                </a>
                <a aria-current="{{ Route::currentRouteName() === 'staf.tiket-saya' ? 'page' : 'false' }}"
                    class="flex items-center gap-stack_sm px-stack_md py-stack_sm rounded transition-colors {{ Route::currentRouteName() === 'staf.tiket-saya' ? 'bg-white/10 text-on-primary font-semibold shadow-sm border-l-4 border-white' : 'text-primary-fixed-dim hover:bg-white/5 hover:text-on-primary border-l-4 border-transparent' }}"
                    href="{{ route('staf.tiket-saya') }}">
                    <span class="material-symbols-outlined text-[20px]">assignment_ind</span>
                    <span class="font-body-md text-body-md">Tiket Saya</span>
                </a>
            </nav>
        </div>
        <div class="mt-auto">
            <div class="px-gutter">
                <div class="bg-white/10 rounded-lg p-3 flex flex-col gap-2.5 shadow-sm border border-white/10">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-full bg-surface-container-lowest text-primary flex items-center justify-center font-label-md text-label-md font-bold shrink-0 shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name ?? 'ST', 0, 2)) }}
                        </div>
                        <div class="flex flex-col min-w-0 leading-tight">
                            <span
                                class="font-body-sm text-body-sm font-semibold text-on-primary truncate">{{ auth()->user()->name ?? 'Staf Bidang' }}</span>
                            <span class="font-label-sm text-[11px] text-primary-fixed-dim truncate">{{
                                auth()->user()->email ?? 'staf@pintas.com' }}</span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-primary/20 flex items-center justify-between">
                        <span
                            class="font-label-sm text-[10px] text-primary-fixed-dim uppercase tracking-wider">{{ auth()->user()->bidangStaf->nama_bidang ?? 'Staf Operasional' }}</span>
                        <span class="material-symbols-outlined text-primary-fixed-dim text-[16px]">verified</span>
                    </div>
                </div>
            </div>
            <div class="px-gutter pb-gutter mt-2">
                <form action="{{ route('logout') }}" class="w-full" method="POST">
                    @csrf
                    <button
                        class="w-full flex items-center gap-stack_sm px-stack_md py-stack_sm rounded text-error hover:bg-error-container hover:text-on-error-container transition-colors"
                        type="submit">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        <span class="font-body-md text-body-md font-semibold">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="lg:pl-[260px] flex flex-col min-h-screen">
        <header
            class="fixed top-0 left-0 lg:left-[260px] right-0 h-[64px] bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-gutter">
            <div class="flex items-center gap-4 lg:hidden">
                <button @click="sidebarOpen = true"
                    class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="font-headline-sm font-bold text-primary">PINTAS</div>
            </div>

            <div class="flex items-center justify-end w-full lg:w-auto ml-auto">
                @include('partials.notification-dropdown')
            </div>
        </header>

        <main class="relative pt-[64px] flex-grow bg-surface w-full overflow-x-hidden">
            @if (session('success'))
                <div class="fixed top-[80px] right-gutter z-50 animate-in slide-in-from-top-2 fade-in duration-300">
                    <div
                        class="flex items-center gap-3 bg-surface-container-lowest border-l-4 border-emerald-600 px-4 py-3 rounded shadow-lg">
                        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                        <span class="font-body-md text-body-md text-on-surface">{{ session('success') }}</span>
                        <button type="button" class="ml-4 text-on-surface-variant hover:text-on-surface"
                            onclick="this.parentElement.parentElement.remove()">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="fixed top-[80px] right-gutter z-50 animate-in slide-in-from-top-2 fade-in duration-300">
                    <div
                        class="flex items-center gap-3 bg-surface-container-lowest border-l-4 border-error px-4 py-3 rounded shadow-lg">
                        <span class="material-symbols-outlined text-error">error</span>
                        <span class="font-body-md text-body-md text-on-surface">{{ session('error') }}</span>
                        <button type="button" class="ml-4 text-on-surface-variant hover:text-on-surface"
                            onclick="this.parentElement.parentElement.remove()">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>

</html>