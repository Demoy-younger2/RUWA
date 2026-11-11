@extends('layouts.app', ['title' => 'Ajukan Permohonan Surat'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Formulir Permohonan Surat</h1>
            <p class="text-xs text-slate-500 mt-1">Pilih jenis surat dan masukkan keperluan Anda secara jelas.</p>
        </div>
        <a href="{{ route('surat.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
        <form method="POST" action="{{ route('surat.store') }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700">Pilih Jenis Surat *</label>
                <select name="jenis_surat" required class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-xs">
                    <option value="">-- Pilih Jenis Surat Pengantar --</option>
                    @foreach($jenisSuratList as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Keperluan Pembuatan Surat *</label>
                <textarea name="keperluan" required rows="3" placeholder="Contoh: Persyaratan melamar pekerjaan di instansi pemerintah / pembukaan rekening bank..." class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-xs">{{ old('keperluan') }}</textarea>
                <span class="text-[11px] text-slate-400 mt-1 block">Tuliskan tujuan keperluan dengan lengkap untuk dicantumkan pada isi surat pengantar.</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700">Keterangan Tambahan (Opsional)</label>
                <textarea name="keterangan_tambahan" rows="2" placeholder="Catatan pendukung jika ada (misal: nama usaha, alamat usaha, nomor berkas)..." class="mt-1 block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-xs">{{ old('keterangan_tambahan') }}</textarea>
            </div>

            <!-- Petunjuk Alur -->
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100 text-blue-900 text-xs">
                <p class="font-bold flex items-center gap-1.5 mb-1 text-blue-800">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Alur Penerbitan Surat:</span>
                </p>
                <ol class="list-decimal list-inside space-y-0.5 text-[11px] text-blue-800/80">
                    <li>Warga mengajukan permohonan melalui form ini.</li>
                    <li>Ketua / Sekretaris RT melakukan verifikasi berkas dan memberikan persetujuan awal.</li>
                    <li>Ketua / Sekretaris RW melakukan validasi akhir dan menerbitkan Nomor Surat Resmi.</li>
                    <li>Surat selesai dan siap diunduh atau dicetak langsung.</li>
                </ol>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('surat.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    Kirim Permohonan Surat
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
