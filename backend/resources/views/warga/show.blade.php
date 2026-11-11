@extends('layouts.app', ['title' => 'Biodata: ' . $warga->nama_lengkap])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Rincian Biodata Warga</h1>
            <p class="text-xs text-slate-500 mt-1">Kartu identitas kependudukan digital RW 05 Sukamaju.</p>
        </div>
        <div class="flex items-center gap-2">
            @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt']))
                <a href="{{ route('warga.edit', $warga->id) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Data
                </a>
            @endif
            <a href="{{ route('warga.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Citizen Card Profile View -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Top Profile Banner -->
        <div class="p-6 sm:p-8 bg-gradient-to-r from-emerald-800 to-teal-800 text-white flex flex-col sm:flex-row items-center sm:items-start gap-5">
            <div class="w-20 h-20 rounded-2xl bg-white text-emerald-800 flex items-center justify-center font-extrabold text-2xl shadow-lg border-2 border-white/20 shrink-0">
                {{ strtoupper(substr($warga->nama_lengkap, 0, 1)) }}
            </div>
            <div class="text-center sm:text-left">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white">
                        {{ $warga->status_keluarga }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400 text-emerald-950">
                        {{ $warga->status_tinggal }}
                    </span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white">{{ $warga->nama_lengkap }}</h2>
                <p class="text-xs text-emerald-200 font-mono mt-0.5">NIK: {{ $warga->nik }} &bull; No. KK: {{ $warga->no_kk ?? '-' }}</p>
                <p class="text-xs text-slate-200 mt-2">
                    <i class="fa-solid fa-location-dot mr-1 text-emerald-300"></i> RT {{ $warga->rt }} / RW {{ $warga->rw }} &bull; {{ $warga->alamat ?? 'Alamat belum dilengkapi' }}
                </p>
            </div>
        </div>

        <!-- Detail Information Grid -->
        <div class="p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div class="space-y-4">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider border-b border-slate-100 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-emerald-600"></i>
                    <span>Informasi Pribadi</span>
                </h3>

                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Tempat, Tanggal Lahir</span>
                    <span class="font-bold text-slate-800">{{ $warga->tempat_lahir ?? '-' }}, {{ $warga->tanggal_lahir ? $warga->tanggal_lahir->format('d F Y') : '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Jenis Kelamin</span>
                    <span class="font-bold text-slate-800">{{ $warga->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Agama</span>
                    <span class="font-bold text-slate-800">{{ $warga->agama }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Status Perkawinan</span>
                    <span class="font-bold text-slate-800">{{ $warga->status_perkawinan }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider border-b border-slate-100 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase text-emerald-600"></i>
                    <span>Pendidikan & Pekerjaan</span>
                </h3>

                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Pendidikan Terakhir</span>
                    <span class="font-bold text-slate-800">{{ $warga->pendidikan ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Pekerjaan</span>
                    <span class="font-bold text-slate-800">{{ $warga->pekerjaan ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">No. WhatsApp / HP</span>
                    <span class="font-bold text-slate-800">{{ $warga->no_hp ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400">Akun Pengguna Aplikasi</span>
                    <span class="font-bold text-slate-800">{{ $warga->user ? $warga->user->email . ' (' . $warga->user->role_title . ')' : 'Belum Registrasi Akun' }}</span>
                </div>
            </div>
        </div>

        <!-- Riwayat Pengajuan Surat oleh Warga Ini -->
        @if($warga->surats->count() > 0)
            <div class="p-6 sm:p-8 bg-slate-50 border-t border-slate-100">
                <h3 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600"></i>
                    <span>Riwayat Pengajuan Surat</span>
                </h3>
                <div class="space-y-2">
                    @foreach($warga->surats as $s)
                        <div class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between text-xs">
                            <div>
                                <p class="font-bold text-slate-900">{{ $s->jenis_surat }}</p>
                                <p class="text-[11px] text-slate-400">{{ $s->tanggal_diajukan->format('d M Y') }} &bull; {{ $s->nomor_surat ?? 'Nomor belum terbit' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $s->status_badge }}">
                                {{ $s->status_label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
