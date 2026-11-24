@php
    $user = Auth::user();
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Kelompok 1: Utama -->
<div class="space-y-1">
    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Utama</p>
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-gauge-high w-4 text-center text-sm {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>Dashboard</span>
    </a>
</div>

<!-- Kelompok 2: Kependudukan & Layanan -->
<div class="space-y-1">
    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Kependudukan</p>
    
    <!-- Data Warga -->
    <a href="{{ route('warga.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('warga.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-users w-4 text-center text-sm {{ request()->routeIs('warga.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
            <span>Data Warga</span>
        </div>
        @if(in_array($user->role, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt']))
            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.5 rounded-full">Kelola</span>
        @endif
    </a>

    <!-- Surat Menyurat -->
    <a href="{{ route('surat.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('surat.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-envelope-open-text w-4 text-center text-sm {{ request()->routeIs('surat.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
            <span>Surat Menyurat</span>
        </div>
        <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-1.5 py-0.5 rounded-full">Digital</span>
    </a>

    <!-- Aspirasi Warga -->
    <a href="{{ route('aspirasi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('aspirasi.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-comments w-4 text-center text-sm {{ request()->routeIs('aspirasi.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>Aspirasi & Aduan</span>
    </a>

    <!-- RUKEM (Rukun Kematian) -->
    <a href="{{ route('rukem.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('rukem.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-hand-holding-heart w-4 text-center text-sm {{ request()->routeIs('rukem.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>RUKEM (Kematian)</span>
    </a>
</div>

<!-- Kelompok 3: Keuangan & Kas Lingkungan -->
<div class="space-y-1">
    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Keuangan & Kas</p>

    <!-- Pemasukan -->
    <a href="{{ route('kas.pemasukan') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('kas.pemasukan') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-arrow-down w-4 text-center text-sm {{ request()->routeIs('kas.pemasukan') ? 'text-emerald-600' : 'text-emerald-500' }}"></i>
            <span>Pemasukan</span>
        </div>
        <span class="text-[9px] bg-emerald-100 text-emerald-700 font-bold px-1.5 py-0.5 rounded-md">+Masuk</span>
    </a>

    <!-- Pengeluaran -->
    <a href="{{ route('kas.pengeluaran') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('kas.pengeluaran') ? 'bg-rose-50 text-rose-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-arrow-up w-4 text-center text-sm {{ request()->routeIs('kas.pengeluaran') ? 'text-rose-600' : 'text-rose-500' }}"></i>
            <span>Pengeluaran</span>
        </div>
        <span class="text-[9px] bg-rose-100 text-rose-700 font-bold px-1.5 py-0.5 rounded-md">-Keluar</span>
    </a>

    <!-- Semua Transaksi (Buku Kas Umum) -->
    <a href="{{ route('kas.semua') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('kas.semua') || (request()->routeIs('kas.index') && !request()->routeIs('kas.pemasukan') && !request()->routeIs('kas.pengeluaran')) ? 'bg-teal-50 text-teal-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-book-bookmark w-4 text-center text-sm {{ request()->routeIs('kas.semua') ? 'text-teal-600' : 'text-slate-400' }}"></i>
            <span>Semua Transaksi</span>
        </div>
        <span class="text-[9px] bg-teal-100 text-teal-800 font-bold px-1.5 py-0.5 rounded-md">Buku Kas</span>
    </a>

    <!-- Kategori Kas -->
    <a href="{{ route('kas.kategori') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('kas.kategori*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-tags w-4 text-center text-sm {{ request()->routeIs('kas.kategori*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
            <span>Kategori</span>
        </div>
        <span class="text-[9px] bg-slate-100 text-slate-600 font-semibold px-1.5 py-0.5 rounded-md">Master</span>
    </a>

    <!-- Kelola Tagihan Warga -->
    <a href="{{ route('kas.tagihan') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('kas.tagihan*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-file-invoice-dollar w-4 text-center text-sm {{ request()->routeIs('kas.tagihan*') ? 'text-emerald-600' : 'text-blue-500' }}"></i>
            <span>Kelola Tagihan</span>
        </div>
        <span class="text-[9px] bg-blue-100 text-blue-700 font-bold px-1.5 py-0.5 rounded-md">Iuran</span>
    </a>

    <!-- Kelola Rekening & QRIS -->
    <a href="{{ route('kas.rekening') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('kas.rekening*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-qrcode w-4 text-center text-sm {{ request()->routeIs('kas.rekening*') ? 'text-emerald-600' : 'text-purple-500' }}"></i>
            <span>Rekening & QRIS</span>
        </div>
        <span class="text-[9px] bg-purple-100 text-purple-700 font-bold px-1.5 py-0.5 rounded-md">Bank</span>
    </a>
</div>

<!-- Kelompok 3.1: Usaha & Ekonomi Warga -->
<div class="space-y-1">
    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Usaha & Ekonomi</p>

    <!-- Bank Sampah -->
    <a href="{{ route('bank-sampah.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('bank-sampah.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-recycle w-4 text-center text-sm {{ request()->routeIs('bank-sampah.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>Bank Sampah</span>
    </a>

    <!-- UMKM Warga -->
    <a href="{{ route('umkm.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('umkm.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-store w-4 text-center text-sm {{ request()->routeIs('umkm.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>Pasar UMKM</span>
    </a>

    <!-- Koperasi -->
    <a href="{{ route('koperasi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('koperasi.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-scale-balanced w-4 text-center text-sm {{ request()->routeIs('koperasi.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>Koperasi Warga</span>
    </a>
</div>

<!-- Kelompok 4: Lingkungan & Keamanan -->
<div class="space-y-1">
    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Lingkungan</p>

    <!-- Keamanan & Ronda -->
    <a href="{{ route('ronda.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('ronda.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-shield-halved w-4 text-center text-sm {{ request()->routeIs('ronda.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
            <span>Keamanan & Ronda</span>
        </div>
        <span class="text-[10px] bg-rose-100 text-rose-800 font-bold px-1.5 py-0.5 rounded-full">SOS</span>
    </a>

    <!-- Posyandu -->
    <a href="{{ route('posyandu.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('posyandu.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-heart-pulse w-4 text-center text-sm {{ request()->routeIs('posyandu.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>Posyandu Pintar</span>
    </a>

    <!-- Kegiatan & Agenda -->
    <a href="{{ route('kegiatan.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('kegiatan.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-calendar-days w-4 text-center text-sm {{ request()->routeIs('kegiatan.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>Kegiatan & Agenda</span>
    </a>

    <!-- Aset RW / RT -->
    <a href="{{ route('aset.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('aset.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-boxes-stacked w-4 text-center text-sm {{ request()->routeIs('aset.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>Aset & Inventaris</span>
    </a>
</div>

<!-- Kelompok 5: Sistem & Konfigurasi -->
<div class="space-y-1">
    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Sistem & IoT</p>

    <!-- IoT e-KTP & RFID Reader ESP32 -->
    <a href="{{ route('rfid.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('rfid.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-id-card w-4 text-center text-sm {{ request()->routeIs('rfid.*') ? 'text-emerald-600' : 'text-teal-500' }}"></i>
            <span>IoT e-KTP & RFID</span>
        </div>
        <span class="text-[9px] bg-teal-50 text-teal-700 border border-teal-200 font-bold px-1.5 py-0.5 rounded-md flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
            ESP32
        </span>
    </a>

    <!-- Log Aktivitas & Audit -->
    <a href="{{ route('log-aktivitas.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('log-aktivitas.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-clock-rotate-left w-4 text-center text-sm {{ request()->routeIs('log-aktivitas.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
            <span>Log Aktivitas</span>
        </div>
        <span class="text-[10px] bg-slate-100 text-slate-700 font-bold px-1.5 py-0.5 rounded-full">Audit</span>
    </a>

    <!-- Pengaturan & Hak Akses -->
    <a href="{{ route('pengaturan.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('pengaturan.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
        <i class="fa-solid fa-sliders w-4 text-center text-sm {{ request()->routeIs('pengaturan.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
        <span>Pengaturan & Hak Akses</span>
    </a>
</div>
