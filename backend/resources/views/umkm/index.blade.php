@extends('layouts.app', ['title' => 'Pasar UMKM Warga'])

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-store text-amber-500"></i>
                <span>Pasar UMKM Warga RW 05</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Etalase promosi kuliner, produk kerajinan, sembako, dan jasa kreasi warga lingkungan kita.
            </p>
        </div>

        <a href="{{ route('umkm.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Daftarkan Usaha Saya</span>
        </a>
    </div>

    <!-- Filter & Kategori Chips -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-xs space-y-3">
        <form method="GET" action="{{ route('umkm.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama toko, produk makanan, atau jasa..." class="w-full pl-10 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 transition">
                    Cari
                </button>
                <a href="{{ route('umkm.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition text-center">
                    Reset
                </a>
            </div>
        </form>

        <div class="flex flex-wrap gap-1.5 pt-2 border-t border-slate-100 text-xs font-semibold">
            <a href="{{ route('umkm.index') }}" class="px-3 py-1 rounded-full transition {{ !request('kategori') ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Kategori
            </a>
            @foreach($kategoriList as $kat)
                <a href="{{ route('umkm.index', ['kategori' => $kat]) }}" class="px-3 py-1 rounded-full transition {{ request('kategori') === $kat ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $kat }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($umkms as $item)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between hover:shadow-lg transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">
                            {{ $item->kategori }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500">
                            <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> RT {{ $item->rt ?? '01' }}
                        </span>
                    </div>

                    <h3 class="text-sm font-bold text-slate-900 line-clamp-1 hover:text-emerald-600">
                        <a href="{{ route('umkm.show', $item->id) }}">{{ $item->nama_usaha }}</a>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pemilik: <span class="font-semibold text-slate-700">{{ $item->nama_pemilik }}</span></p>

                    <p class="text-xs text-slate-600 mt-3 line-clamp-3 leading-relaxed">
                        {{ $item->deskripsi }}
                    </p>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 font-semibold block">Harga Mulai</span>
                        <span class="text-sm font-extrabold text-emerald-600">Rp {{ number_format($item->harga_mulai, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="https://wa.me/{{ $item->no_whatsapp }}?text=Halo%20{{ urlencode($item->nama_pemilik) }},%20saya%20warga%20RW%2005%20ingin%20memesan%20dari%20{{ urlencode($item->nama_usaha) }}" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Hubungi</span>
                        </a>

                        @if(Auth::user()->id === $item->user_id || Auth::user()->isSuperAdmin())
                            <a href="{{ route('umkm.edit', $item->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-12 text-center text-xs text-slate-400 bg-white rounded-3xl border border-slate-200">
                Belum ada produk atau usaha UMKM pada kategori ini.
            </div>
        @endforelse
    </div>

    @if($umkms->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $umkms->links() }}
        </div>
    @endif

</div>
@endsection
