@extends('layouts.app', ['title' => 'Atur Petugas Ronda'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Atur Petugas Ronda Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Tambahkan warga atau petugas ke dalam jadwal ronda mingguan.</p>
        </div>
        <a href="{{ route('ronda.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('ronda.store') }}" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Pilih Hari Tugas *</label>
                    <select name="hari" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach($hariList as $hari)
                            <option value="{{ $hari }}">{{ $hari }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Wilayah RT *</label>
                    <select name="rt" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="01">RT 01</option>
                        <option value="02">RT 02</option>
                        <option value="03">RT 03</option>
                        <option value="04">RT 04</option>
                        <option value="05">RT 05</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Nama Petugas Ronda / Warga *</label>
                <input type="text" name="nama_petugas" required placeholder="Contoh: Budi Santoso / Pak RT Agus" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Pos Ronda *</label>
                    <input type="text" name="pos_ronda" required value="Pos Ronda Utama RT 01" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Jam Jaga *</label>
                    <input type="text" name="jam_jaga" required value="22:00 - 04:00 WIB" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('ronda.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    Simpan Jadwal Ronda
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
