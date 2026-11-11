@extends('layouts.app', ['title' => 'Tulis Aspirasi & Aduan Warga'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Breadcrumb & Title -->
    <div>
        <a href="{{ route('aspirasi.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 transition mb-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Aspirasi</span>
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
            <i class="fa-solid fa-comment-dots text-emerald-600"></i>
            <span>Tulis Aspirasi / Aduan Warga</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Sampaikan masukan konstruktif, kritik membangun, atau laporan kendala fasilitas umum di lingkungan RT & RW Anda.
        </p>
    </div>

    <!-- Info Box -->
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-emerald-600 mt-0.5 text-base"></i>
        <div class="text-xs text-emerald-900 leading-relaxed space-y-1">
            <p class="font-bold">Komitmen Keterbukaan & Kerahasiaan Lingkungan</p>
            <p class="text-emerald-800">
                Aduan Anda akan langsung masuk ke dasbor pengurus RT dan Ketua RW untuk diverifikasi dan ditindaklanjuti. Anda juga dapat memilih opsi <strong>Pengirim Anonim</strong> jika menghendaki kerahasiaan identitas.
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('aspirasi.store') }}" class="space-y-6">
            @csrf

            <!-- Kategori -->
            <div class="space-y-1.5">
                <label for="kategori" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Kategori Aduan / Bidang <span class="text-rose-500">*</span>
                </label>
                <select id="kategori" name="kategori" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-white @error('kategori') border-rose-500 @enderror">
                    <option value="">-- Pilih Kategori Permasalahan --</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
                @error('kategori')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Judul -->
            <div class="space-y-1.5">
                <label for="judul" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Judul Singkat / Subjek <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Lampu Penerangan Jalan Gang 3 Padam" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition @error('judul') border-rose-500 @enderror">
                @error('judul')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi Kejadian -->
            <div class="space-y-1.5">
                <label for="lokasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Lokasi / Patokan Tempat
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-map-location-dot text-sm"></i>
                    </span>
                    <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Depan Pos Kamling RT 02 / Dekat Lapangan Bulutangkis" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition @error('lokasi') border-rose-500 @enderror">
                </div>
                <p class="text-[11px] text-slate-400">Tuliskan patokan lokasi sedetil mungkin agar petugas ronda/pengurus dapat langsung mengecek ke lokasi.</p>
                @error('lokasi')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Isi Aduan / Saran -->
            <div class="space-y-1.5">
                <label for="isi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Uraian Aspirasi & Kronologi <span class="text-rose-500">*</span>
                </label>
                <textarea id="isi" name="isi" rows="5" required placeholder="Jelaskan secara lengkap detail situasi, keluhan, atau usulan solusi yang Anda ajukan untuk kenyamanan bersama..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition resize-y @error('isi') border-rose-500 @enderror">{{ old('isi') }}</textarea>
                @error('isi')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Checkbox Pengirim Anonim -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                <div class="flex items-center h-5">
                    <input id="is_anonim" name="is_anonim" type="checkbox" value="1" {{ old('is_anonim') ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 bg-white border-slate-300 rounded focus:ring-emerald-500">
                </div>
                <label for="is_anonim" class="text-xs text-slate-700 font-medium cursor-pointer select-none">
                    <span class="font-bold text-slate-900 block">Kirim sebagai Anonim</span>
                    Centang jika Anda tidak ingin nama dan identitas Anda ditampilkan pada daftar aduan umum warga. Pengurus tetap dapat melihat waktu pelaporan secara objektif.
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('aspirasi.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirimkan Aspirasi</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
