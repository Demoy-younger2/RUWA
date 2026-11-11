@extends('layouts.app', ['title' => 'Tambah Data Warga'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Formulir Tambah Data Warga</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi data kependudukan sesuai dengan Kartu Tanda Penduduk (KTP) dan Kartu Keluarga (KK).</p>
        </div>
        <a href="{{ route('warga.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('warga.store') }}" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Nomor Induk Kependudukan (NIK) *</label>
                    <input type="text" name="nik" required maxlength="16" value="{{ old('nik') }}" placeholder="3201xxxxxxxxxxxx" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Nomor Kartu Keluarga (No. KK)</label>
                    <input type="text" name="no_kk" maxlength="16" value="{{ old('no_kk') }}" placeholder="3201xxxxxxxxxxxx" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" required value="{{ old('nama_lengkap') }}" placeholder="Nama sesuai KTP" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Kota kelahiran" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Jenis Kelamin *</label>
                    <select name="jenis_kelamin" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Agama *</label>
                    <select name="agama" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Pendidikan Terakhir</label>
                    <input type="text" name="pendidikan" value="{{ old('pendidikan') }}" placeholder="Contoh: S1 / SMA / SMP" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Pekerjaan</label>
                    <input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}" placeholder="Contoh: Karyawan Swasta / Wiraswasta" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700">Status Perkawinan</label>
                    <select name="status_perkawinan" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="Kawin">Kawin</option>
                        <option value="Belum Kawin">Belum Kawin</option>
                        <option value="Cerai Hidup">Cerai Hidup</option>
                        <option value="Cerai Mati">Cerai Mati</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Status Dalam Keluarga</label>
                    <select name="status_keluarga" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="Kepala Keluarga">Kepala Keluarga</option>
                        <option value="Istri">Istri</option>
                        <option value="Anak">Anak</option>
                        <option value="Orang Tua / Famili Lain">Orang Tua / Famili Lain</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Status Domisili / Tinggal</label>
                    <select name="status_tinggal" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="Tetap">Tetap (Rumah Sendiri)</option>
                        <option value="Kontrak">Kontrak</option>
                        <option value="Kos">Kos</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                <div>
                    <label class="block font-bold text-slate-700">Wilayah RW *</label>
                    <input type="text" name="rw" readonly value="05" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Alamat Lengkap Rumah</label>
                <textarea name="alamat" rows="2" placeholder="Nama Jalan, Blok, Nomor Rumah..." class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('alamat') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('warga.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    Simpan Data Warga
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
