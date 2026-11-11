@extends('layouts.app', ['title' => $umkm->nama_usaha])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('umkm.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Pasar UMKM
        </a>
        @if(Auth::user()->id === $umkm->user_id || Auth::user()->isSuperAdmin())
            <a href="{{ route('umkm.edit', $umkm->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Usaha
            </a>
        @endif
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-800">
                    {{ $umkm->kategori }}
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-2">{{ $umkm->nama_usaha }}</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Pemilik: <strong class="text-slate-800">{{ $umkm->nama_pemilik }}</strong> &bull; RT {{ $umkm->rt ?? '01' }} / RW {{ $umkm->rw ?? '05' }}
                </p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-[11px] text-slate-400 font-semibold block uppercase">Harga Mulai</span>
                <span class="text-2xl font-extrabold text-emerald-600">Rp {{ number_format($umkm->harga_mulai, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider text-slate-400">Deskripsi Produk & Layanan</h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $umkm->deskripsi }}
            </p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
            <p class="text-slate-600">
                <strong class="text-slate-800">Alamat Usaha:</strong> {{ $umkm->alamat ?? 'Wilayah RT ' . $umkm->rt }}
            </p>
            <p class="text-slate-600">
                <strong class="text-slate-800">Nomor Kontak:</strong> {{ $umkm->no_whatsapp }}
            </p>
        </div>

        <div class="pt-4">
            <a href="https://wa.me/{{ $umkm->no_whatsapp }}?text=Halo%20{{ urlencode($umkm->nama_pemilik) }},%20saya%20warga%20RW%2005%20ingin%20memesan%20dari%20{{ urlencode($umkm->nama_usaha) }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-2xl text-xs sm:text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/30 transition">
                <i class="fa-brands fa-whatsapp text-lg"></i>
                <span>Chat Penjual Sekarang via WhatsApp</span>
            </a>
        </div>
    </div>

</div>
@endsection
