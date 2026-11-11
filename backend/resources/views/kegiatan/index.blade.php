@extends('layouts.app', ['title' => 'Kegiatan & Agenda Warga'])

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-calendar-days text-emerald-600"></i>
                <span>Kegiatan & Agenda Lingkungan</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Informasi jadwal kerja bakti, rapat warga, peringatan hari besar, senam sehat dan kegiatan sosial RW 05.
            </p>
        </div>

        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt']))
            <a href="{{ route('kegiatan.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                <span>Buat Agenda Baru</span>
            </a>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap gap-2 text-xs font-semibold">
        <a href="{{ route('kegiatan.index') }}" class="px-3.5 py-1.5 rounded-xl transition {{ !request('status') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua Agenda
        </a>
        <a href="{{ route('kegiatan.index', ['status' => 'akan_datang']) }}" class="px-3.5 py-1.5 rounded-xl transition {{ request('status') === 'akan_datang' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Akan Datang
        </a>
        <a href="{{ route('kegiatan.index', ['status' => 'berlangsung']) }}" class="px-3.5 py-1.5 rounded-xl transition {{ request('status') === 'berlangsung' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Sedang Berjalan
        </a>
        <a href="{{ route('kegiatan.index', ['status' => 'selesai']) }}" class="px-3.5 py-1.5 rounded-xl transition {{ request('status') === 'selesai' ? 'bg-slate-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Selesai
        </a>
    </div>

    <!-- Activities Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($kegiatans as $keg)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between hover:shadow-lg transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-800">
                            {{ $keg->kategori }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $keg->status === 'akan_datang' ? 'bg-emerald-100 text-emerald-800' : ($keg->status === 'berlangsung' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                            {{ ucfirst(str_replace('_', ' ', $keg->status)) }}
                        </span>
                    </div>

                    <h3 class="text-sm font-bold text-slate-900 line-clamp-2 hover:text-emerald-600">
                        <a href="{{ route('kegiatan.show', $keg->id) }}">{{ $keg->judul }}</a>
                    </h3>

                    <div class="mt-3 space-y-1.5 text-xs text-slate-500">
                        <p class="flex items-center gap-2">
                            <i class="fa-regular fa-clock text-emerald-600 text-[11px] w-4"></i>
                            <span class="font-bold text-slate-700">{{ $keg->tanggal_mulai->format('d M Y, H:i') }} WIB</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-rose-500 text-[11px] w-4"></i>
                            <span class="line-clamp-1">{{ $keg->lokasi }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="fa-solid fa-users text-slate-400 text-[11px] w-4"></i>
                            <span>{{ $keg->penyelenggara }}</span>
                        </p>
                    </div>

                    <p class="text-xs text-slate-600 mt-3 line-clamp-3 leading-relaxed">
                        {{ $keg->deskripsi }}
                    </p>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold">Estimasi Anggaran</span>
                        <span class="font-extrabold text-slate-800">Rp {{ number_format($keg->anggaran, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('kegiatan.show', $keg->id) }}" class="px-3 py-1.5 rounded-xl font-bold text-xs bg-slate-100 text-slate-700 hover:bg-slate-200">
                            Detail
                        </a>
                        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'sekretaris_rw']))
                            <a href="{{ route('kegiatan.edit', $keg->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-12 text-center text-xs text-slate-400 bg-white rounded-3xl border border-slate-200">
                Belum ada agenda kegiatan yang terjadwal.
            </div>
        @endforelse
    </div>

    @if($kegiatans->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $kegiatans->links() }}
        </div>
    @endif

</div>
@endsection
