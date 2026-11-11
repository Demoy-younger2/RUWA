@extends('layouts.app', ['title' => 'Data Kependudukan Warga'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-users text-emerald-600"></i>
                <span>Data Kependudukan Warga</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Basis data kependudukan terpadu, nomor KK, status domisili dan demografi warga.
            </p>
        </div>

        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt']))
            <a href="{{ route('warga.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Warga Baru</span>
            </a>
        @endif
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('warga.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan Nama, NIK, No KK, atau Alamat..." class="w-full pl-10 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50">
            </div>

            <div>
                <select name="rt" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50">
                    <option value="">Semua Wilayah RT</option>
                    @foreach($rtList as $rt)
                        <option value="{{ $rt }}" {{ request('rt') == $rt ? 'selected' : '' }}>RT {{ $rt }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 transition">
                    Filter Data
                </button>
                <a href="{{ route('warga.index') }}" class="py-2 px-3 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table of Citizens (Responsive Mobile Cards & Desktop Table) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Nama Lengkap & NIK</th>
                        <th class="py-3 px-4">No. Kartu Keluarga</th>
                        <th class="py-3 px-4">Status Keluarga</th>
                        <th class="py-3 px-4">RT / RW</th>
                        <th class="py-3 px-4">Status Tinggal</th>
                        <th class="py-3 px-4">No. HP</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($wargas as $warga)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ $warga->jenis_kelamin === 'L' ? 'L' : 'P' }}
                                    </div>
                                    <div>
                                        <a href="{{ route('warga.show', $warga->id) }}" class="font-bold text-slate-900 hover:text-emerald-600">
                                            {{ $warga->nama_lengkap }}
                                        </a>
                                        <p class="text-[11px] font-mono text-slate-400">{{ $warga->nik }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">
                                {{ $warga->no_kk ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $warga->status_keluarga === 'Kepala Keluarga' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $warga->status_keluarga }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-700">
                                RT {{ $warga->rt }} / RW {{ $warga->rw }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $warga->status_tinggal === 'Tetap' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $warga->status_tinggal }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $warga->no_hp ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('warga.show', $warga->id) }}" title="Lihat Profil" class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt']))
                                        <a href="{{ route('warga.edit', $warga->id) }}" title="Edit Warga" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('warga.destroy', $warga->id) }}" onsubmit="return confirm('Hapus data warga ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Warga" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Tidak ada data warga yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wargas->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $wargas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
