@extends('layouts.app', ['title' => 'Catat Aset Baru - Inventaris RW/RT'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Breadcrumb & Title -->
    <div>
        <a href="{{ route('aset.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-teal-600 transition mb-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Katalog Aset</span>
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
            <i class="fa-solid fa-box-open text-teal-600"></i>
            <span>Pencatatan Inventaris Aset Baru</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Tambah perlengkapan, tenda, kursi, proyektor, atau alat kebersihan bersama ke dalam inventaris resmi RW/RT.
        </p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('aset.store') }}" class="space-y-6">
            @csrf

            <!-- Nama Aset -->
            <div class="space-y-1.5">
                <label for="nama_aset" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Nama Barang / Fasilitas <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama_aset" name="nama_aset" value="{{ old('nama_aset') }}" required placeholder="Contoh: Tenda Kerucut 3x3 Meter, Kursi Plastik Napolly" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition @error('nama_aset') border-rose-500 @enderror">
                @error('nama_aset')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kategori -->
                <div class="space-y-1.5">
                    <label for="kategori" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kategori Perlengkapan <span class="text-rose-500">*</span>
                    </label>
                    <select id="kategori" name="kategori" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-white @error('kategori') border-rose-500 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                    @error('kategori')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kepemilikan Tingkat -->
                <div class="space-y-1.5">
                    <label for="tingkat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kepemilikan / Tingkat <span class="text-rose-500">*</span>
                    </label>
                    <select id="tingkat" name="tingkat" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-white @error('tingkat') border-rose-500 @enderror">
                        <option value="RW" {{ old('tingkat', (Auth::user()->isRt() ? 'RT' : 'RW')) === 'RW' ? 'selected' : '' }}>Aset Lingkungan RW (RW 05)</option>
                        <option value="RT" {{ old('tingkat', (Auth::user()->isRt() ? 'RT' : 'RW')) === 'RT' ? 'selected' : '' }}>Aset Lingkungan RT (RT {{ Auth::user()->rt ?? '01' }})</option>
                    </select>
                    @error('tingkat')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Jumlah Total -->
                <div class="space-y-1.5">
                    <label for="jumlah_total" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Jumlah Total (Unit / Buah) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="jumlah_total" name="jumlah_total" value="{{ old('jumlah_total', 1) }}" min="1" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition @error('jumlah_total') border-rose-500 @enderror">
                    @error('jumlah_total')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kondisi -->
                <div class="space-y-1.5">
                    <label for="kondisi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kondisi Fisik Barang <span class="text-rose-500">*</span>
                    </label>
                    <select id="kondisi" name="kondisi" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-white @error('kondisi') border-rose-500 @enderror">
                        <option value="Baik" {{ old('kondisi') === 'Baik' ? 'selected' : '' }}>Baik (Siap Pakai)</option>
                        <option value="Rusak Ringan" {{ old('kondisi') === 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan (Bisa Digunakan Dengan Catatan)</option>
                        <option value="Rusak Berat" {{ old('kondisi') === 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat (Perlu Reparasi / Afkir)</option>
                    </select>
                    @error('kondisi')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Lokasi Penyimpanan -->
            <div class="space-y-1.5">
                <label for="lokasi_penyimpanan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Lokasi Penyimpanan / Gudang <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-warehouse text-sm"></i>
                    </span>
                    <input type="text" id="lokasi_penyimpanan" name="lokasi_penyimpanan" value="{{ old('lokasi_penyimpanan') }}" required placeholder="Contoh: Gudang Balai RW 05 / Rumah Pak RT 02" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition @error('lokasi_penyimpanan') border-rose-500 @enderror">
                </div>
                @error('lokasi_penyimpanan')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan Tambahan -->
            <div class="space-y-1.5">
                <label for="keterangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Keterangan & Catatan Spesifikasi
                </label>
                <textarea id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Hibah donasi warga tahun 2025, warna biru, dilengkapi 4 tali penahan angin..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition resize-y @error('keterangan') border-rose-500 @enderror">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('aset.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Data Aset</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
