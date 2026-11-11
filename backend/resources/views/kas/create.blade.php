@extends('layouts.app', ['title' => 'Catat Transaksi Kas'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Formulir Kas Keuangan</h1>
            <p class="text-xs text-slate-500 mt-1">Pencatatan pemasukan atau pengeluaran kas RT / RW.</p>
        </div>
        <a href="{{ route('kas.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('kas.store') }}" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Tingkat Kas *</label>
                    <select name="tingkat" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="RW">Kas RW 05</option>
                        <option value="RT">Kas RT</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Jenis Transaksi *</label>
                    <select name="jenis" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="pemasukan">Pemasukan (+)</option>
                        <option value="pengeluaran">Pengeluaran (-)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Kategori Kas *</label>
                <select name="kategori" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                    @foreach($kategoriList as $kat)
                        @php
                            $namaKat = is_object($kat) ? $kat->nama : $kat;
                            $jenisKat = is_object($kat) ? ' (' . strtoupper($kat->jenis) . ')' : '';
                        @endphp
                        <option value="{{ $namaKat }}">{{ $namaKat }}{{ $jenisKat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Penyetor / Penerima (Pihak Terkait)</label>
                    <input type="text" name="pihak_terkait" placeholder="Contoh: Toko Bangunan / Bpk. Budi" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Metode Pembayaran</label>
                    <select name="metode" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Nominal Transaksi (Rp) *</label>
                    <input type="number" name="jumlah" required min="1000" placeholder="Contoh: 250000" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Tanggal Transaksi *</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Keterangan / Uraian *</label>
                <textarea name="keterangan" required rows="3" placeholder="Uraikan detail transaksi, kwitansi, atau tujuan pengeluaran..." class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('kas.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    Simpan Transaksi Kas
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
