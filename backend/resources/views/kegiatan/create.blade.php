@extends('layouts.app', ['title' => 'Buat Agenda Kegiatan Baru'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Buat Agenda Kegiatan Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Publikasikan jadwal pertemuan, gotong royong, atau acara lingkungan.</p>
        </div>
        <a href="{{ route('kegiatan.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('kegiatan.store') }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700">Nama / Judul Kegiatan *</label>
                <input type="text" name="judul" required placeholder="Contoh: Kerja Bakti Bersih Saluran Air RT 01" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Kategori Kegiatan *</label>
                    <select name="kategori" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Penyelenggara *</label>
                    <input type="text" name="penyelenggara" required value="Pengurus RW 05" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Waktu Mulai *</label>
                    <input type="datetime-local" name="tanggal_mulai" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Waktu Selesai (Opsional)</label>
                    <input type="datetime-local" name="tanggal_selesai" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Lokasi Pelaksanaan *</label>
                <input type="text" name="lokasi" required placeholder="Contoh: Balai Warga RW 05 / Lapangan RT 02" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700">Deskripsi & Rincian Agenda *</label>
                <textarea name="deskripsi" required rows="3" placeholder="Uraikan tujuan acara, perlengkapan yang perlu dibawa warga, dan agenda..." class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Rencana Anggaran (Rp)</label>
                    <input type="number" name="anggaran" value="0" min="0" placeholder="0" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Status Agenda</label>
                    <select name="status" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white">
                        <option value="akan_datang">Akan Datang</option>
                        <option value="berlangsung">Sedang Berlangsung</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('kegiatan.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    Publikasikan Agenda
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
