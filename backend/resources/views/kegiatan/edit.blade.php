@extends('layouts.app', ['title' => 'Edit Agenda Kegiatan'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Agenda Kegiatan</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui jadwal, lokasi, atau status kegiatan.</p>
        </div>
        <a href="{{ route('kegiatan.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('kegiatan.update', $kegiatan->id) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-slate-700">Nama / Judul Kegiatan *</label>
                <input type="text" name="judul" required value="{{ old('judul', $kegiatan->judul) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Kategori Kegiatan *</label>
                    <select name="kategori" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}" {{ $kegiatan->kategori === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Penyelenggara *</label>
                    <input type="text" name="penyelenggara" required value="{{ old('penyelenggara', $kegiatan->penyelenggara) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Waktu Mulai *</label>
                    <input type="datetime-local" name="tanggal_mulai" required value="{{ $kegiatan->tanggal_mulai->format('Y-m-d\TH:i') }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Waktu Selesai</label>
                    <input type="datetime-local" name="tanggal_selesai" value="{{ $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d\TH:i') : '' }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Lokasi Pelaksanaan *</label>
                <input type="text" name="lokasi" required value="{{ old('lokasi', $kegiatan->lokasi) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div>
                <label class="block font-bold text-slate-700">Deskripsi Agenda *</label>
                <textarea name="deskripsi" required rows="3" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Rencana Anggaran (Rp)</label>
                    <input type="number" name="anggaran" value="{{ old('anggaran', $kegiatan->anggaran) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Status Agenda</label>
                    <select name="status" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white">
                        <option value="akan_datang" {{ $kegiatan->status === 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
                        <option value="berlangsung" {{ $kegiatan->status === 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                        <option value="selesai" {{ $kegiatan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ $kegiatan->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <button type="submit" form="delete-kegiatan" class="font-bold text-rose-600 hover:text-rose-700">
                    Hapus Agenda
                </button>
                <div class="flex items-center gap-2">
                    <a href="{{ route('kegiatan.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-kegiatan" method="POST" action="{{ route('kegiatan.destroy', $kegiatan->id) }}" onsubmit="return confirm('Hapus agenda kegiatan ini?')">
            @csrf
            @method('DELETE')
        </form>
    </div>

</div>
@endsection
