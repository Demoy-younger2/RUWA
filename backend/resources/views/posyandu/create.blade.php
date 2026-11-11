@extends('layouts.app', ['title' => 'Catat Pemeriksaan Posyandu'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{ tipe: 'balita' }">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Catat Pemeriksaan Posyandu</h1>
            <p class="text-xs text-slate-500 mt-1">Layanan Posyandu Melati RW 05 untuk Balita dan Lansia.</p>
        </div>
        <a href="{{ route('posyandu.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('posyandu.store') }}" class="space-y-4 text-xs">
            @csrf

            <!-- Selector Kategori (Balita vs Lansia) -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kategori Peserta Posyandu *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2 p-3 rounded-2xl border cursor-pointer transition" :class="tipe === 'balita' ? 'bg-pink-50 border-pink-500 text-pink-700 font-bold' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="kategori" value="balita" x-model="tipe" class="sr-only">
                        <i class="fa-solid fa-baby text-base"></i>
                        <span>Posyandu Balita</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 rounded-2xl border cursor-pointer transition" :class="tipe === 'lansia' ? 'bg-purple-50 border-purple-500 text-purple-700 font-bold' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="kategori" value="lansia" x-model="tipe" class="sr-only">
                        <i class="fa-solid fa-person-cane text-base"></i>
                        <span>Posyandu Lansia</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Nama Peserta *</label>
                    <input type="text" name="nama_peserta" required placeholder="Nama balita / lansia" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">NIK (Jika Ada)</label>
                    <input type="text" name="nik" maxlength="16" placeholder="3201xxxxxxxxxxxx" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Tanggal Pemeriksaan *</label>
                    <input type="date" name="tanggal_pemeriksaan" required value="{{ date('Y-m-d') }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">
                        <span x-text="tipe === 'balita' ? 'Usia (Bulan) *' : 'Usia (Tahun) *'"></span>
                    </label>
                    <input type="number" name="usia_bulan_tahun" required placeholder="Contoh: 24" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
            </div>

            <!-- Bagian Khusus Balita -->
            <div x-show="tipe === 'balita'" class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-pink-50/50 border border-pink-100">
                <div>
                    <label class="block font-bold text-pink-900">Berat Badan (Kg)</label>
                    <input type="number" step="0.1" name="berat_badan" placeholder="12.5" class="mt-1 block w-full px-3 py-2 rounded-xl border border-pink-200 bg-white font-mono">
                </div>
                <div>
                    <label class="block font-bold text-pink-900">Tinggi / Panjang Badan (Cm)</label>
                    <input type="number" step="0.1" name="tinggi_badan" placeholder="85.0" class="mt-1 block w-full px-3 py-2 rounded-xl border border-pink-200 bg-white font-mono">
                </div>
            </div>

            <!-- Bagian Khusus Lansia -->
            <div x-show="tipe === 'lansia'" class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-purple-50/50 border border-purple-100">
                <div>
                    <label class="block font-bold text-purple-900">Tekanan Darah (Tensi)</label>
                    <input type="text" name="tensi_darah" placeholder="Contoh: 120/80 mmHg" class="mt-1 block w-full px-3 py-2 rounded-xl border border-purple-200 bg-white">
                </div>
                <div>
                    <label class="block font-bold text-purple-900">Gula Darah Sewaktu (mg/dL)</label>
                    <input type="number" name="gula_darah" placeholder="Contoh: 115" class="mt-1 block w-full px-3 py-2 rounded-xl border border-purple-200 bg-white font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Status Kesehatan / Status Gizi</label>
                <input type="text" name="status_kesehatan" placeholder="Contoh: Gizi Baik (Pita Hijau) / Normal Sehat" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700">Imunisasi / Vitamin / Obat yang Diberikan</label>
                <input type="text" name="imunisasi_atau_vitamin" placeholder="Contoh: Vitamin A Kapsul Biru & Polio 3" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700">Catatan & Saran Bidan / Kader</label>
                <textarea name="catatan_kader" rows="2" placeholder="Saran pola makan, stimulasi tumbuh kembang, atau anjuran kontrol lanjutan..." class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('posyandu.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    Simpan Catatan Posyandu
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
