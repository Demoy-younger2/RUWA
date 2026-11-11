<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>SiRW Digital - Portal Resmi RW 05 Kelurahan Sukamaju</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ mobileNav: false }">

    <!-- Top Announcement Bar -->
    <div class="bg-emerald-800 text-emerald-100 text-[11px] sm:text-xs py-2 px-4 text-center font-medium">
        <span>✨ Selamat datang di Portal Digital {{ $pengaturan->nama_rw ?? 'RW 05' }} {{ $pengaturan->nama_kelurahan ?? 'Sukamaju' }} - Layanan Warga Praktis & Transparan</span>
    </div>

    <!-- Navigation Header -->
    <nav class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand -->
            <a href="{{ route('welcome') }}" class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 font-bold text-xl">
                    <i class="fa-solid fa-users-line"></i>
                </div>
                <div>
                    <span class="text-base font-extrabold text-slate-900 tracking-tight block leading-tight">SiRW<span class="text-emerald-600">Digital</span></span>
                    <span class="text-[11px] font-semibold text-slate-500 tracking-wider uppercase block">{{ $pengaturan->nama_rw ?? 'RW 05' }}</span>
                </div>
            </a>

            <!-- Desktop Links -->
            <div class="hidden md:flex items-center gap-6 text-xs font-semibold text-slate-600">
                <a href="#layanan" class="hover:text-emerald-600 transition">Layanan Unggulan</a>
                <a href="#cek-surat" class="hover:text-emerald-600 transition">Cek Status Surat</a>
                <a href="#umkm" class="hover:text-emerald-600 transition">Pasar UMKM</a>
                <a href="#kegiatan" class="hover:text-emerald-600 transition">Agenda Warga</a>
                <a href="#kontak" class="hover:text-emerald-600 transition">Kontak Sekretariat</a>
            </div>

            <!-- CTA Buttons -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 border border-slate-200 transition">
                        Masuk Akun
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/20 transition">
                        <i class="fa-solid fa-user-plus text-[10px]"></i>
                        <span>Daftar Warga</span>
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileNav = !mobileNav" class="md:hidden p-2 text-slate-600 hover:text-slate-900">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileNav" @click.away="mobileNav = false" x-transition class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-4 space-y-2 text-xs font-medium">
            <a href="#layanan" @click="mobileNav = false" class="block py-2 text-slate-700">Layanan Unggulan</a>
            <a href="#cek-surat" @click="mobileNav = false" class="block py-2 text-slate-700">Cek Status Surat</a>
            <a href="#umkm" @click="mobileNav = false" class="block py-2 text-slate-700">Pasar UMKM</a>
            <a href="#kegiatan" @click="mobileNav = false" class="block py-2 text-slate-700">Agenda Warga</a>
            <a href="#kontak" @click="mobileNav = false" class="block py-2 text-slate-700">Kontak Sekretariat</a>
            <div class="pt-2 border-t border-slate-100 flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center py-2.5 rounded-xl font-bold text-white bg-emerald-600">Buka Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2 rounded-xl font-bold border border-slate-300">Masuk</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center py-2 rounded-xl font-bold bg-emerald-600 text-white">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative overflow-hidden bg-gradient-to-b from-emerald-900 via-slate-900 to-slate-900 text-white pt-14 pb-20 sm:pt-20 sm:pb-28">
        <!-- Background Pattern Decor -->
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:16px_16px]"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Portal Komunitas Warga Cerdas & Terpadu
                </span>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-white">
                    Membangun Lingkungan <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400">Rukun, Aman & Modern</span>
                </h1>
                <p class="mt-4 sm:mt-6 text-sm sm:text-lg text-slate-300 leading-relaxed">
                    {{ $pengaturan->sambutan_rw ?? 'Sistem terpadu pelayanan administrasi surat menyurat online, transparansi kas keuangan RT/RW, sistem keamanan ronda 24 jam, bank sampah mandiri, hingga pengembangan usaha mikro warga.' }}
                </p>

                <!-- Action CTA -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 shadow-xl shadow-emerald-500/30 transition transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Masuk ke Dashboard Utama</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 shadow-xl shadow-emerald-500/30 transition transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>Masuk / Coba 8 Demo Role</span>
                        </a>
                        <a href="#cek-surat" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl text-xs sm:text-sm font-bold text-slate-200 bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-sm transition">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Lacak Permohonan Surat</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Live Stats Banner -->
            <div class="mt-14 sm:mt-20 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 max-w-4xl mx-auto">
                <div class="p-4 sm:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-400">{{ $totalWarga }}</p>
                    <p class="text-xs text-slate-300 font-semibold mt-1">Warga Terdata</p>
                </div>
                <div class="p-4 sm:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                    <p class="text-2xl sm:text-3xl font-extrabold text-teal-400">{{ $totalKK }}</p>
                    <p class="text-xs text-slate-300 font-semibold mt-1">Kepala Keluarga</p>
                </div>
                <div class="p-4 sm:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                    <p class="text-2xl sm:text-3xl font-extrabold text-cyan-400">{{ $totalRT }}</p>
                    <p class="text-xs text-slate-300 font-semibold mt-1">Rukun Tetangga</p>
                </div>
                <div class="p-4 sm:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-400">{{ $totalSuratSelesai }}</p>
                    <p class="text-xs text-slate-300 font-semibold mt-1">Surat Diterbitkan</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Section: Layanan Unggulan -->
    <section id="layanan" class="py-16 sm:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Ekosistem Pelayanan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                    Layanan Lengkap untuk Seluruh Warga
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">
                    Integrasi 14 modul pelayanan digital untuk mempermudah koordinasi RT, RW, dan warga masyarakat.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- 1. Surat -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Surat Menyurat Digital</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Pengajuan pengantar SKCK, Surat Domisili, Surat Usaha, SKTM, dan Izin Keramaian secara online dengan persetujuan bertahap RT & RW.
                    </p>
                </div>

                <!-- 2. Kas -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Transparansi Kas RW & RT</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Pencatatan iuran warga, operasional lingkungan, dan laporan keuangan terbuka yang dapat dipantau oleh setiap warga.
                    </p>
                </div>

                <!-- 3. Ronda & SOS -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Keamanan Ronda & Panic Button</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Jadwal ronda malam, presensi jaga pos, dan tombol SOS darurat untuk menyiagakan regu keamanan dan pengurus saat situasi kritis.
                    </p>
                </div>

                <!-- 4. Bank Sampah -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-recycle"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Bank Sampah Lingkungan</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Pilah sampah anorganik (botol plastik, kardus, minyak jelantah) menjadi saldo tabungan rupiah yang dapat ditarik kapan saja.
                    </p>
                </div>

                <!-- 5. UMKM Warga -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pasar UMKM Warga</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Katalog promosi produk kuliner, sembako, dan aneka jasa warga sekitar dengan pemesanan langsung via chat WhatsApp penjual.
                    </p>
                </div>

                <!-- 6. Posyandu & RUKEM -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Posyandu & RUKEM Duka</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Pemantauan tumbuh kembang balita, posyandu lansia, serta layanan solidaritas kematian (RUKEM) siaga perlengkapan dan santunan duka.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Cek Status Surat Cepat (Public Tracking) -->
    <section id="cek-surat" class="py-16 bg-slate-100/70 border-y border-slate-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Pelacakan Mandiri</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                Lacak Status Surat Anda
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-2 mb-6">
                Masukkan Nomor Surat atau NIK 16 digit pemohon untuk melihat proses verifikasi RT dan persetujuan RW secara real-time.
            </p>

            <form action="{{ route('welcome.check_surat') }}" method="POST" class="bg-white p-3 sm:p-4 rounded-3xl shadow-xl border border-slate-200 flex flex-col sm:flex-row gap-2">
                @csrf
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="keyword" required placeholder="Ketik Nomor Surat (contoh: 470/012/SRW/2026) atau NIK..." class="w-full pl-11 pr-4 py-3 rounded-2xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50">
                </div>
                <button type="submit" class="px-6 py-3 rounded-2xl font-bold text-xs sm:text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/30 transition">
                    Cari Status Surat
                </button>
            </form>
        </div>
    </section>

    <!-- Section: UMKM Warga Showcase -->
    <section id="umkm" class="py-16 sm:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10">
                <div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Perekonomian Lokal</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Produk & Usaha Warga Terpilih</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Beli dan dukung usaha tetangga kita sendiri di lingkungan RW 05.</p>
                </div>
                <a href="{{ route('umkm.index') }}" class="mt-4 sm:mt-0 text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5">
                    <span>Lihat Semua Produk</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($umkms as $item)
                    <div class="rounded-3xl bg-slate-50 border border-slate-200 p-5 flex flex-col justify-between hover:shadow-lg transition">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">
                                    {{ $item->kategori }}
                                </span>
                                <span class="text-xs font-semibold text-slate-500">
                                    <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> RT {{ $item->rt ?? '01' }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 line-clamp-1">{{ $item->nama_usaha }}</h3>
                            <p class="text-xs text-slate-500 mt-1">Oleh: {{ $item->nama_pemilik }}</p>
                            <p class="text-xs text-slate-600 mt-3 line-clamp-2 leading-relaxed">{{ $item->deskripsi }}</p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-slate-200/70 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">Mulai Dari</span>
                                <span class="text-sm font-extrabold text-emerald-600">Rp {{ number_format($item->harga_mulai, 0, ',', '.') }}</span>
                            </div>
                            <a href="https://wa.me/{{ $item->no_whatsapp }}?text=Halo%20{{ urlencode($item->nama_pemilik) }},%20saya%20warga%20RW%2005%20ingin%20memesan%20produk%20{{ urlencode($item->nama_usaha) }}" target="_blank" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>Pesan</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section: Agenda & Kegiatan Terdekat -->
    <section id="kegiatan" class="py-16 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Kebersamaan Warga</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Agenda & Kegiatan Mendatang</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Jangan lewatkan agenda silaturahmi, gotong royong, dan kegiatan bersama.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($kegiatans as $keg)
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                    {{ $keg->kategori }}
                                </span>
                                <span class="text-xs font-bold text-emerald-600">
                                    <i class="fa-regular fa-clock mr-1"></i> {{ $keg->tanggal_mulai->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">{{ $keg->judul }}</h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-map-pin text-rose-500 text-[11px]"></i>
                                <span>{{ $keg->lokasi }}</span>
                            </p>
                            <p class="text-xs text-slate-600 mt-3 line-clamp-3 leading-relaxed">{{ $keg->deskripsi }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-400 font-medium">
                            Penyelenggara: <span class="font-bold text-slate-700">{{ $keg->penyelenggara }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer & Kontak Sekretariat -->
    <footer id="kontak" class="bg-slate-900 text-slate-400 text-xs py-14 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Col 1: About -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 text-white mb-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-users-line"></i>
                    </div>
                    <span class="text-base font-extrabold">{{ $pengaturan->nama_aplikasi ?? 'SiRW-Digital' }}</span>
                </div>
                <p class="text-slate-400 text-xs leading-relaxed max-w-md">
                    Sistem Informasi Rukun Warga Terpadu. Menghubungkan seluruh aparatur pengurus RW, RT, sekretaris, bendahara, serta seluruh warga dalam satu genggaman yang modern dan transparan.
                </p>
                <div class="mt-4 text-[11px] text-slate-500">
                    Ketua RW: <strong class="text-slate-300">{{ $pengaturan->nama_ketua_rw ?? 'H. Suryana, S.Pd.' }}</strong>
                </div>
            </div>

            <!-- Col 2: Navigasi Cepat -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Menu Layanan</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('surat.index') }}" class="hover:text-emerald-400">Surat Menyurat Online</a></li>
                    <li><a href="{{ route('kas.index') }}" class="hover:text-emerald-400">Transparansi Kas RW & RT</a></li>
                    <li><a href="{{ route('ronda.index') }}" class="hover:text-emerald-400">Keamanan & Ronda</a></li>
                    <li><a href="{{ route('bank-sampah.index') }}" class="hover:text-emerald-400">Bank Sampah Digital</a></li>
                    <li><a href="{{ route('umkm.index') }}" class="hover:text-emerald-400">Pasar UMKM Warga</a></li>
                </ul>
            </div>

            <!-- Col 3: Kontak Sekretariat -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Sekretariat RW</h4>
                <p class="text-slate-300 text-xs leading-relaxed mb-2">
                    {{ $pengaturan->alamat_sekretariat ?? 'Balai Warga RW 05, Sukamaju' }}
                </p>
                <p class="text-slate-400 text-xs mb-1">
                    <i class="fa-solid fa-phone text-emerald-500 mr-1.5"></i> {{ $pengaturan->kontak_rw ?? '0812-3456-7890' }}
                </p>
                <p class="text-slate-400 text-xs">
                    <i class="fa-solid fa-envelope text-emerald-500 mr-1.5"></i> {{ $pengaturan->email_rw ?? 'sekretariat@rw05.id' }}
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pt-6 border-t border-slate-800 text-center text-[11px] text-slate-500">
            &copy; {{ date('Y') }} {{ $pengaturan->nama_rw ?? 'RW 05' }} {{ $pengaturan->nama_kelurahan ?? 'Sukamaju' }}. Dikembangkan dengan Laravel 12 & Modern Responsive UI.
        </div>
    </footer>

</body>
</html>
