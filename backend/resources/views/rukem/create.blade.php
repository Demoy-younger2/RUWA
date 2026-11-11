@extends('layouts.app', ['title' => 'Lapor Berita Duka & RUKEM'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Lapor Berita Duka & Permohonan RUKEM</h1>
            <p class="text-xs text-slate-500 mt-1">Siaga penyerahan santunan kematian dan penyiapan perlengkapan jenazah.</p>
        </div>
        <a href="{{ route('rukem.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('rukem.store') }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700">Nama Lengkap Almarhum / Almarhumah *</label>
                <input type="text" name="nama_almarhum" required placeholder="Contoh: Alm. Bapak Suwandi bin Kartono" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Tanggal Meninggal *</label>
                    <input type="date" name="tanggal_meninggal" required value="{{ date('Y-m-d') }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Jam / Waktu Meninggal</label>
                    <input type="text" name="jam_meninggal" placeholder="Contoh: 04:30 WIB" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Nama Keluarga / Ahli Waris *</label>
                    <input type="text" name="nama_ahli_waris" required placeholder="Nama penerima santunan" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">No. HP / WhatsApp Ahli Waris</label>
                    <input type="text" name="no_hp_ahli_waris" placeholder="08xxxxxxxxxx" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block font-bold text-slate-700">Alamat Rumah Duka *</label>
                    <input type="text" name="alamat_duka" required placeholder="Jl. Melati No. 12" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">RT *</label>
                    <select name="rt" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white">
                        <option value="01">RT 01</option>
                        <option value="02">RT 02</option>
                        <option value="03">RT 03</option>
                        <option value="04">RT 04</option>
                        <option value="05">RT 05</option>
                    </select>
                    <input type="hidden" name="rw" value="05">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Rencana Lokasi Pemakaman</label>
                    <input type="text" name="lokasi_pemakaman" value="TPU Sukamaju" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Perkiraan Jam Pemakaman</label>
                    <input type="text" name="jam_pemakaman" placeholder="Contoh: Ba'da Dzuhur (13:00 WIB)" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Besaran Santunan RUKEM (Rp) *</label>
                    <input type="number" name="jumlah_santunan" required value="1500000" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Perlengkapan yang Dibutuhkan</label>
                    <input type="text" name="fasilitas_disiapkan" value="Keranda Jenazah, Tenda Duka 2 Unit, Pemandian, Sound Pengajian" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('rukem.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition">
                    Kirim Laporan Duka
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
