@extends('layouts.app', ['title' => 'Edit UMKM: ' . $umkm->nama_usaha])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Informasi Usaha UMKM</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui rincian produk, harga, dan nomor kontak pemesanan.</p>
        </div>
        <a href="{{ route('umkm.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('umkm.update', $umkm->id) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-slate-700">Nama Usaha / Nama Toko *</label>
                <input type="text" name="nama_usaha" required value="{{ old('nama_usaha', $umkm->nama_usaha) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Kategori Usaha *</label>
                    <select name="kategori" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}" {{ $umkm->kategori === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Harga Mulai Dari (Rp) *</label>
                    <input type="number" name="harga_mulai" required min="0" value="{{ old('harga_mulai', $umkm->harga_mulai) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Nomor WhatsApp Pesanan *</label>
                    <input type="text" name="no_whatsapp" required value="{{ old('no_whatsapp', $umkm->no_whatsapp) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Lokasi RT</label>
                    <select name="rt" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white">
                        @foreach(['01', '02', '03', '04', '05'] as $r)
                            <option value="{{ $r }}" {{ $umkm->rt == $r ? 'selected' : '' }}>RT {{ $r }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Deskripsi Lengkap Usaha *</label>
                <textarea name="deskripsi" required rows="3" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('deskripsi', $umkm->deskripsi) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Alamat Tempat Usaha</label>
                <input type="text" name="alamat" value="{{ old('alamat', $umkm->alamat) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <button type="submit" form="delete-form" class="font-bold text-rose-600 hover:text-rose-700">
                    Hapus Produk
                </button>
                <div class="flex items-center gap-2">
                    <a href="{{ route('umkm.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" method="POST" action="{{ route('umkm.destroy', $umkm->id) }}" onsubmit="return confirm('Hapus produk UMKM ini?')">
            @csrf
            @method('DELETE')
        </form>
    </div>

</div>
@endsection
