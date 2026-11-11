@extends('layouts.app', ['title' => 'Daftarkan Lapak UMKM'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Daftarkan Usaha / Produk Warga</h1>
            <p class="text-xs text-slate-500 mt-1">Promosikan produk kuliner, jasa, atau kerajinan Anda ke seluruh warga RW 05.</p>
        </div>
        <a href="{{ route('umkm.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('umkm.store') }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700">Nama Usaha / Nama Toko *</label>
                <input type="text" name="nama_usaha" required placeholder="Contoh: Dapur Bu Siti / Servis Elektronik Berkah" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Kategori Usaha *</label>
                    <select name="kategori" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Harga Mulai Dari (Rp) *</label>
                    <input type="number" name="harga_mulai" required min="0" placeholder="Contoh: 15000" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Nomor WhatsApp Pesanan *</label>
                    <input type="text" name="no_whatsapp" required placeholder="08xxxxxxxxxx" value="{{ Auth::user()->phone }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Lokasi RT</label>
                    <select name="rt" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white">
                        <option value="01" {{ Auth::user()->rt == '01' ? 'selected' : '' }}>RT 01</option>
                        <option value="02" {{ Auth::user()->rt == '02' ? 'selected' : '' }}>RT 02</option>
                        <option value="03" {{ Auth::user()->rt == '03' ? 'selected' : '' }}>RT 03</option>
                        <option value="04" {{ Auth::user()->rt == '04' ? 'selected' : '' }}>RT 04</option>
                        <option value="05" {{ Auth::user()->rt == '05' ? 'selected' : '' }}>RT 05</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Deskripsi Lengkap Usaha & Menu Produk *</label>
                <textarea name="deskripsi" required rows="3" placeholder="Ceritakan produk yang Anda jual, menu favorit, keunggulan layanan, dan cara pemesanan..." class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Alamat Tempat Usaha / Pengambilan</label>
                <input type="text" name="alamat" placeholder="Jl. Melati No. 8, Sukamaju..." class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('umkm.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    Publikasikan Lapak
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
