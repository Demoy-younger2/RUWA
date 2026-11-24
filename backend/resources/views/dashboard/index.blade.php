@extends('layouts.app', ['title' => 'Dashboard ' . $user->role_title])

@section('content')
<div class="space-y-6">

    <!-- Hero Banner with Role Greeting -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-800 via-teal-800 to-slate-900 text-white p-6 sm:p-8 shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-white/10 backdrop-blur-md text-emerald-200 border border-white/15">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        {{ $user->role_title }}
                    </span>
                    <span class="text-xs text-slate-300">RT {{ $user->rt ?? '01' }} / RW {{ $user->rw ?? '05' }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight">
                    Halo, {{ $user->name }}! 👋
                </h1>
                <p class="text-xs sm:text-sm text-slate-200 mt-1 max-w-2xl leading-relaxed">
                    Selamat datang di panel kendali {{ $pengaturan->nama_rw ?? 'RW 05' }} {{ $pengaturan->nama_kelurahan ?? 'Sukamaju' }}. Hari ini {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 pt-2 md:pt-0">
                <a href="{{ route('rfid.index') }}" class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition">
                    <i class="fa-solid fa-id-card text-indigo-200"></i>
                    <span>Daftarkan e-KTP</span>
                </a>
                <a href="{{ route('surat.create') }}" class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-900 bg-white hover:bg-slate-100 shadow-md transition">
                    <i class="fa-solid fa-file-circle-plus text-emerald-600"></i>
                    <span>Ajukan Surat</span>
                </a>
                <a href="{{ route('aspirasi.create') }}" class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600/80 hover:bg-emerald-600 border border-white/20 transition">
                    <i class="fa-solid fa-bullhorn text-xs"></i>
                    <span>Kirim Aspirasi</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stat KPI Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        <!-- 1. Total Warga -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Warga</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $totalWarga }}</span>
                <span class="text-[11px] text-slate-500 block font-medium mt-0.5">{{ $totalKK }} Kepala Keluarga</span>
            </div>
        </div>

        <!-- 2. Kas RW Transparan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Saldo Kas RW</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xl sm:text-2xl font-extrabold text-emerald-600">Rp {{ number_format($totalKasRW, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-500 block font-medium mt-0.5">Kas RT 01: Rp {{ number_format($totalKasRT, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 3. Surat Menunggu -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Surat Diproses</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $suratMenungguRT + $suratMenungguRW }}</span>
                <span class="text-[11px] text-slate-500 block font-medium mt-0.5">{{ $suratSelesai }} surat telah selesai/terbit</span>
            </div>
        </div>

        <!-- 4. UMKM & Aset -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Ekonomi & Fasilitas</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $totalUmkm }} UMKM</span>
                <span class="text-[11px] text-slate-500 block font-medium mt-0.5">{{ $totalAset }} Aset ({{ $asetDipinjam }} dipinjam)</span>
            </div>
        </div>
    </div>

    <!-- Citizen Hub Widget (Only if logged in as warga) -->
    @if($user->isWarga())
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-address-card text-emerald-600"></i>
                    <span>Layanan Mandiri Warga Saya</span>
                </h2>
                <span class="text-[11px] font-semibold text-slate-400">Ringkasan Akun Anda</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-emerald-800">Saldo Bank Sampah</span>
                        <p class="text-lg font-extrabold text-emerald-700 mt-1">Rp {{ number_format($myBankSampahSaldo, 0, ',', '.') }}</p>
                    </div>
                    <a href="{{ route('bank-sampah.index') }}" class="p-2.5 rounded-xl bg-white text-emerald-600 shadow-xs hover:bg-emerald-600 hover:text-white transition">
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-blue-800">Status Koperasi</span>
                        @if($myKoperasi)
                            <p class="text-xs font-bold text-blue-700 mt-1">Simpanan: Rp {{ number_format($myKoperasi->total_simpanan, 0, ',', '.') }}</p>
                        @else
                            <p class="text-xs text-blue-600 mt-1">Belum Terdaftar Anggota</p>
                        @endif
                    </div>
                    <a href="{{ route('koperasi.index') }}" class="p-2.5 rounded-xl bg-white text-blue-600 shadow-xs hover:bg-blue-600 hover:text-white transition">
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-100 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-amber-800">Permohonan Surat Saya</span>
                        <p class="text-lg font-extrabold text-amber-700 mt-1">{{ count($mySurats) }} Pengajuan</p>
                    </div>
                    <a href="{{ route('surat.index') }}" class="p-2.5 rounded-xl bg-white text-amber-600 shadow-xs hover:bg-amber-600 hover:text-white transition">
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Content Columns: Left (Letters & Events) + Right (Ronda & Complaints) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Surat Menyurat Antrean & Kegiatan -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Antrean Surat Menyurat Terkini -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-envelope-open-text text-blue-600"></i>
                            <span>Permohonan Surat Terkini</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar pengajuan surat menyurat warga</p>
                    </div>
                    <a href="{{ route('surat.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                        Lihat Semua
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($recentSurats as $surat)
                        <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-regular fa-file-lines"></i>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-slate-900">{{ $surat->jenis_surat }}</h3>
                                    <p class="text-[11px] text-slate-500">
                                        Pemohon: <span class="font-semibold text-slate-700">{{ $surat->user->name ?? '-' }}</span> (RT {{ $surat->user->rt ?? '01' }})
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">
                                        Keperluan: {{ $surat->keperluan }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $surat->status_badge }}">
                                    {{ $surat->status_label }}
                                </span>
                                <a href="{{ route('surat.show', $surat->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 text-xs">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            Belum ada pengajuan surat.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Agenda Kegiatan Lingkungan -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-calendar-days text-emerald-600"></i>
                            <span>Agenda & Kegiatan Mendatang</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal gotong royong dan pertemuan warga</p>
                    </div>
                    <a href="{{ route('kegiatan.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                        Lihat Kalender
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($upcomingKegiatans as $keg)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                        {{ $keg->kategori }}
                                    </span>
                                    <span class="text-[11px] font-bold text-slate-600">
                                        {{ $keg->tanggal_mulai->format('d M, H:i') }}
                                    </span>
                                </div>
                                <h3 class="text-xs font-bold text-slate-900 line-clamp-1">{{ $keg->judul }}</h3>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $keg->deskripsi }}</p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px] text-slate-500">
                                <span><i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> {{ $keg->lokasi }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 py-6 text-center text-xs text-slate-400">
                            Belum ada agenda kegiatan mendatang.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right 1 Col: Keamanan/Ronda Siaga + Aspirasi -->
        <div class="space-y-6">

            <!-- Keamanan & Regu Ronda Malam -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6" x-data="{ panicModal: false }">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-rose-600"></i>
                            <span>Siaga Ronda Malam</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal hari ini & darurat</p>
                    </div>
                    <!-- Panic Button SOS Trigger -->
                    <button @click="panicModal = true" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/30 transition animate-pulse">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>SOS</span>
                    </button>
                </div>

                <div class="space-y-2 mb-4">
                    @forelse($todayRondas as $ron)
                        <div class="p-2.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[11px]">
                                    <i class="fa-solid fa-user-shield"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-xs">{{ $ron->nama_petugas }}</p>
                                    <p class="text-[10px] text-slate-400">RT {{ $ron->rt }} &bull; {{ $ron->jam_jaga }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $ron->status_kehadiran === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                {{ ucfirst($ron->status_kehadiran) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Belum ada jadwal ronda hari ini.</p>
                    @endforelse
                </div>

                <a href="{{ route('ronda.index') }}" class="block text-center w-full py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Lihat Seluruh Jadwal Ronda
                </a>

                <!-- Panic Button Modal -->
                <div x-show="panicModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-transition>
                    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-rose-200" @click.away="panicModal = false">
                        <div class="flex items-center gap-3 text-rose-600 mb-3">
                            <div class="w-10 h-10 rounded-2xl bg-rose-100 flex items-center justify-center text-xl">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Tombol Darurat (SOS)</h3>
                                <p class="text-xs text-slate-500">Lapor kejadian genting ke pos ronda & pengurus</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('ronda.panic_button') }}" class="space-y-3 mt-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700">Jenis Kondisi Darurat</label>
                                <select name="jenis_darurat" required class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                                    <option value="Pencurian / Maling">Pencurian / Orang Mencurigakan</option>
                                    <option value="Kebakaran">Kebakaran</option>
                                    <option value="Medis / Butuh Ambulans">Darurat Medis / Ambulans</option>
                                    <option value="Bencana Alam / Pohon Tumbang">Bencana Alam / Pohon Tumbang</option>
                                    <option value="Keributan / Gangguan Keamanan">Keributan / Tawuran</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700">Lokasi Tepat Kejadian</label>
                                <input type="text" name="lokasi_kejadian" required placeholder="Contoh: Depan Pos Ronda RT 01 / Gang 4 No. 12" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700">Keterangan Singkat</label>
                                <textarea name="keterangan" required rows="2" placeholder="Jelaskan situasi darurat..." class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300"></textarea>
                            </div>

                            <div class="pt-2 flex items-center gap-2">
                                <button type="button" @click="panicModal = false" class="flex-1 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/30">
                                    Kirim Sinyal SOS
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Aspirasi & Pengaduan Terbaru -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-comments text-amber-500"></i>
                            <span>Suara & Aspirasi Warga</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Saran & aduan lingkungan terkini</p>
                    </div>
                    <a href="{{ route('aspirasi.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                        Buka
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentAspirasis as $asp)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                    {{ $asp->kategori }}
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $asp->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($asp->status) }}
                                </span>
                            </div>
                            <h4 class="font-bold text-slate-900 mt-1 line-clamp-1">{{ $asp->judul }}</h4>
                            <p class="text-[11px] text-slate-600 mt-1 line-clamp-2">{{ $asp->isi }}</p>
                            @if($asp->tanggapan)
                                <div class="mt-2 p-2 rounded-xl bg-emerald-50 text-[11px] text-emerald-800 border border-emerald-100">
                                    <span class="font-bold block">Tanggapan Pengurus:</span>
                                    <span class="italic">{{ $asp->tanggapan }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Belum ada aspirasi atau aduan.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
