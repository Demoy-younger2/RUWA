<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'SiRW-Digital') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Hide scrollbar for tabs */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased flex flex-col" x-data="{ sidebarOpen: false, quickRoleOpen: false }">

    <div class="min-h-full flex flex-col">
        <!-- Top App Navigation Bar -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
            <div class="px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
                <!-- Left: Hamburger + Logo -->
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 font-bold text-xl">
                            <i class="fa-solid fa-users-line"></i>
                        </div>
                        <div class="hidden sm:block">
                            <span class="text-base font-extrabold text-slate-900 tracking-tight block leading-tight">SiRW<span class="text-emerald-600">Digital</span></span>
                            <span class="text-[11px] font-semibold text-slate-500 tracking-wider uppercase block">RW 05 Sukamaju</span>
                        </div>
                    </a>
                </div>

                <!-- Center: Quick Role Switcher + IoT RFID Badge -->
                <div class="flex items-center gap-2">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="truncate max-w-[130px] sm:max-w-none">{{ Auth::user()->role_title }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-emerald-600"></i>
                        </button>

                        <!-- Dropdown for fast switching between all 8 roles -->
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50">
                            <div class="px-3 py-1.5 border-b border-slate-100 mb-1">
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Uji Coba Hak Akses Role</p>
                            </div>
                            @php
                                $allRoles = [
                                    'superadmin' => ['Superadmin', 'fa-shield-halved', 'text-purple-600'],
                                    'rw' => ['Ketua RW', 'fa-building-columns', 'text-blue-600'],
                                    'rt' => ['Ketua RT 01', 'fa-house-user', 'text-emerald-600'],
                                    'sekretaris_rw' => ['Sekretaris RW', 'fa-file-signature', 'text-amber-600'],
                                    'bendahara_rw' => ['Bendahara RW', 'fa-money-bill-transfer', 'text-teal-600'],
                                    'sekretaris_rt' => ['Sekretaris RT 01', 'fa-pen-to-square', 'text-orange-600'],
                                    'bendahara_rt' => ['Bendahara RT 01', 'fa-wallet', 'text-lime-600'],
                                    'warga' => ['Warga (Budi)', 'fa-user', 'text-sky-600'],
                                ];
                            @endphp
                            <div class="max-h-60 overflow-y-auto">
                                @foreach($allRoles as $roleKey => $roleData)
                                    <a href="{{ route('quick.login', $roleKey) }}" class="flex items-center justify-between px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition {{ Auth::user()->role === $roleKey ? 'bg-emerald-50 font-bold text-emerald-800' : '' }}">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid {{ $roleData[1] }} {{ $roleData[2] }} w-4 text-center"></i>
                                            <span>{{ $roleData[0] }}</span>
                                        </div>
                                        @if(Auth::user()->role === $roleKey)
                                            <i class="fa-solid fa-check text-emerald-600"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Shortcut IoT RFID e-KTP -->
                    <a href="{{ route('rfid.index') }}" title="Registrasi e-KTP / Kartu RFID (ESP32)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition shadow-xs {{ Auth::user()->rfid_uid ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100' : 'bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100 animate-pulse' }}">
                        <i class="fa-solid fa-id-card {{ Auth::user()->rfid_uid ? 'text-indigo-600' : 'text-amber-600' }}"></i>
                        <span class="hidden sm:inline">
                            @if(Auth::user()->rfid_uid)
                                e-KTP: {{ substr(Auth::user()->rfid_uid, 0, 7) }}..
                            @else
                                + Daftarkan e-KTP
                            @endif
                        </span>
                    </a>
                </div>

                <!-- Right: User Avatar + Logout -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('welcome') }}" target="_blank" title="Lihat Halaman Publik" class="hidden md:flex items-center gap-1.5 text-xs text-slate-600 hover:text-emerald-600 px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-emerald-200 transition">
                        <i class="fa-solid fa-globe"></i>
                        <span>Web Publik</span>
                    </a>

                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1 rounded-full hover:ring-2 hover:ring-emerald-400 focus:outline-none transition">
                            <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm border-2 border-white shadow-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </button>

                        <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded-full {{ Auth::user()->role_badge_color }}">
                                    {{ Auth::user()->role_title }}
                                </span>
                            </div>
                            <a href="{{ route('pengaturan.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-gear w-4 text-slate-400"></i>
                                <span>Pengaturan Akun</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 font-medium">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-rose-400"></i>
                                    <span>Keluar Sistem</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body Wrapper -->
        <div class="flex-1 flex overflow-hidden">
            <!-- Desktop Sidebar -->
            <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-200 overflow-y-auto">
                <div class="p-4 flex-1 flex flex-col space-y-6">
                    @include('layouts.sidebar_links')
                </div>
                <!-- Sidebar Footer Status -->
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Sistem Siaga</span>
                        </span>
                        <span class="font-mono text-[11px]">v12.0</span>
                    </div>
                </div>
            </aside>

            <!-- Mobile Off-Canvas Drawer -->
            <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
                <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="sidebarOpen = false"></div>

                <div class="fixed inset-0 flex">
                    <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative mr-16 flex w-full max-w-xs flex-1">
                        <div class="flex flex-col w-full bg-white shadow-2xl h-full overflow-y-auto">
                            <!-- Drawer Header -->
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-lg">
                                        <i class="fa-solid fa-users-line"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-extrabold text-slate-900">SiRW-Digital</h2>
                                        <p class="text-[11px] text-slate-500">RW 05 Sukamaju</p>
                                    </div>
                                </div>
                                <button @click="sidebarOpen = false" class="p-2 text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>

                            <!-- Drawer Links -->
                            <div class="p-4 flex-1">
                                @include('layouts.sidebar_links')
                            </div>

                            <!-- Logout Button in Mobile Drawer -->
                            <div class="p-4 border-t border-slate-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 transition">
                                        <i class="fa-solid fa-power-off"></i>
                                        <span>Keluar Akun</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto pb-24 lg:pb-12 bg-slate-50">
                <!-- Flash Notification Banner -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 ml-3">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button @click="show = false" class="text-rose-500 hover:text-rose-700 ml-3">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm shadow-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </div>
                                <span>{{ session('warning') }}</span>
                            </div>
                            <button @click="show = false" class="text-amber-500 hover:text-amber-700 ml-3">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Page Content Injected Here -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 pb-24 lg:pb-8">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Mobile Bottom Navigation Bar (Sticky for Smartphone usability) -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur border-t border-slate-200 px-2 py-1.5 flex justify-around items-center shadow-lg">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-1 px-3 text-[11px] font-semibold {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="fa-solid fa-gauge-high text-lg"></i>
                <span class="mt-0.5">Beranda</span>
            </a>
            <a href="{{ route('surat.index') }}" class="flex flex-col items-center py-1 px-3 text-[11px] font-semibold {{ request()->routeIs('surat.*') ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="fa-solid fa-envelope-open-text text-lg"></i>
                <span class="mt-0.5">Surat</span>
            </a>
            <a href="{{ route('ronda.index') }}" class="flex flex-col items-center py-1 px-3 text-[11px] font-semibold {{ request()->routeIs('ronda.*') ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="fa-solid fa-shield-halved text-lg"></i>
                <span class="mt-0.5">Ronda</span>
            </a>
            <a href="{{ route('kas.index') }}" class="flex flex-col items-center py-1 px-3 text-[11px] font-semibold {{ request()->routeIs('kas.*') ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="fa-solid fa-wallet text-lg"></i>
                <span class="mt-0.5">Kas</span>
            </a>
            <button @click="sidebarOpen = true" class="flex flex-col items-center py-1 px-3 text-[11px] font-semibold text-slate-500 hover:text-slate-800 focus:outline-none">
                <i class="fa-solid fa-grid-2 text-lg fa-table-cells-large"></i>
                <span class="mt-0.5">Menu</span>
            </button>
        </nav>
    </div>

    @stack('scripts')
</body>
</html>
