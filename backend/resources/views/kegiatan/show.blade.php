@extends('layouts.app', ['title' => $kegiatan->judul])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('kegiatan.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Agenda
        </a>
        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'sekretaris_rw']))
            <a href="{{ route('kegiatan.edit', $kegiatan->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Agenda
            </a>
        @endif
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                    {{ $kegiatan->kategori }}
                </span>
                <span class="text-xs font-bold px-3 py-1 rounded-full {{ $kegiatan->status === 'akan_datang' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                    {{ ucfirst(str_replace('_', ' ', $kegiatan->status)) }}
                </span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ $kegiatan->judul }}</h1>
            <p class="text-xs text-slate-500 mt-1">Penyelenggara: <strong class="text-slate-800">{{ $kegiatan->penyelenggara }}</strong></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
            <div class="space-y-2">
                <p class="text-slate-600 flex items-center gap-2">
                    <i class="fa-regular fa-clock text-emerald-600 w-4"></i>
                    <span><strong>Mulai:</strong> {{ $kegiatan->tanggal_mulai->format('d F Y, H:i') }} WIB</span>
                </p>
                @if($kegiatan->tanggal_selesai)
                    <p class="text-slate-600 flex items-center gap-2">
                        <i class="fa-regular fa-calendar-check text-blue-600 w-4"></i>
                        <span><strong>Selesai:</strong> {{ $kegiatan->tanggal_selesai->format('d F Y, H:i') }} WIB</span>
                    </p>
                @endif
            </div>
            <div class="space-y-2">
                <p class="text-slate-600 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-rose-500 w-4"></i>
                    <span><strong>Lokasi:</strong> {{ $kegiatan->lokasi }}</span>
                </p>
                <p class="text-slate-600 flex items-center gap-2">
                    <i class="fa-solid fa-coins text-amber-500 w-4"></i>
                    <span><strong>Anggaran:</strong> Rp {{ number_format($kegiatan->anggaran, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider text-slate-400">Deskripsi & Instruksi Kegiatan</h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $kegiatan->deskripsi }}
            </p>
        </div>
    </div>

</div>
@endsection
