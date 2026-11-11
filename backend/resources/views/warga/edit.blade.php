@extends('layouts.app', ['title' => 'Edit Data Warga: ' . $warga->nama_lengkap])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Data Warga</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui rincian identitas dan data kependudukan warga.</p>
        </div>
        <a href="{{ route('warga.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('warga.update', $warga->id) }}" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Nomor Induk Kependudukan (NIK) *</label>
                    <input type="text" name="nik" required maxlength="16" value="{{ old('nik', $warga->nik) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Nomor Kartu Keluarga (No. KK)</label>
                    <input type="text" name="no_kk" maxlength="16" value="{{ old('no_kk', $warga->no_kk) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" required value="{{ old('nama_lengkap', $warga->nama_lengkap) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $warga->tempat_lahir) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $warga->tanggal_lahir?->format('Y-m-d')) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Jenis Kelamin *</label>
                    <select name="jenis_kelamin" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="L" {{ $warga->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $warga->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Agama *</label>
                    <select name="agama" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $ag)
                            <option value="{{ $ag }}" {{ $warga->agama === $ag ? 'selected' : '' }}>{{ $ag }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Pendidikan Terakhir</label>
                    <input type="text" name="pendidikan" value="{{ old('pendidikan', $warga->pendidikan) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Pekerjaan</label>
                    <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $warga->pekerjaan) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Status Perkawinan</label>
                    <select name="status_perkawinan" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach(['Kawin', 'Belum Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sp)
                            <option value="{{ $sp }}" {{ $warga->status_perkawinan === $sp ? 'selected' : '' }}>{{ $sp }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Status Dalam Keluarga</label>
                    <select name="status_keluarga" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach(['Kepala Keluarga', 'Istri', 'Anak', 'Orang Tua / Famili Lain'] as $sk)
                            <option value="{{ $sk }}" {{ $warga->status_keluarga === $sk ? 'selected' : '' }}>{{ $sk }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Status Domisili / Tinggal</label>
                    <select name="status_tinggal" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach(['Tetap', 'Kontrak', 'Kos'] as $st)
                            <option value="{{ $st }}" {{ $warga->status_tinggal === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Wilayah RT *</label>
                    <select name="rt" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        @foreach(['01', '02', '03', '04', '05'] as $r)
                            <option value="{{ $r }}" {{ $warga->rt === $r ? 'selected' : '' }}>RT {{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Wilayah RW *</label>
                    <input type="text" name="rw" readonly value="{{ $warga->rw }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $warga->no_hp) }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Alamat Lengkap</label>
                <textarea name="alamat" rows="2" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('alamat', $warga->alamat) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('warga.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
