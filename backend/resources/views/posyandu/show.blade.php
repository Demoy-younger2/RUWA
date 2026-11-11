@extends('layouts.app', ['title' => 'Kartu Posyandu: ' . $posyandu->nama_peserta])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('posyandu.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Posyandu
        </a>
    </div>

    <!-- KMS / Health Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 {{ $posyandu->kategori === 'balita' ? 'bg-gradient-to-r from-pink-600 to-rose-600' : 'bg-gradient-to-r from-purple-700 to-indigo-800' }} text-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/80 block">
                    KARTU PEMERIKSAAN KESEHATAN DIGITAL (POSYANDU)
                </span>
                <h1 class="text-xl font-extrabold mt-1">{{ $posyandu->nama_peserta }}</h1>
                <p class="text-xs text-white/80 mt-0.5">Kategori: {{ ucfirst($posyandu->kategori) }} &bull; Usia: {{ $posyandu->usia_bulan_tahun }} {{ $posyandu->kategori === 'balita' ? 'Bulan' : 'Tahun' }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl">
                <i class="fa-solid {{ $posyandu->kategori === 'balita' ? 'fa-baby' : 'fa-person-cane' }}"></i>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6 text-xs">
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Pemeriksaan</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $posyandu->tanggal_pemeriksaan->format('d F Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Kader / Tenaga Kesehatan</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $posyandu->nama_kader ?? 'Bidan Puskesmas' }}</span>
                </div>
            </div>

            <!-- Hasil Pengukuran -->
            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider text-slate-400 mb-3">Hasil Pemeriksaan Fisik & Laboratorium Sederhana</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @if($posyandu->kategori === 'balita')
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Berat Badan</span>
                            <p class="text-lg font-extrabold text-slate-900 mt-1">{{ $posyandu->berat_badan ?? '-' }} <span class="text-xs font-normal">kg</span></p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Tinggi Badan</span>
                            <p class="text-lg font-extrabold text-slate-900 mt-1">{{ $posyandu->tinggi_badan ?? '-' }} <span class="text-xs font-normal">cm</span></p>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Tekanan Darah</span>
                            <p class="text-base font-extrabold text-slate-900 mt-1">{{ $posyandu->tensi_darah ?? '-' }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Gula Darah</span>
                            <p class="text-base font-extrabold text-slate-900 mt-1">{{ $posyandu->gula_darah ? $posyandu->gula_darah . ' mg/dL' : '-' }}</p>
                        </div>
                    @endif

                    <div class="col-span-2 p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                        <span class="text-[10px] text-emerald-800 font-bold uppercase">Kesimpulan Kesehatan</span>
                        <p class="text-sm font-extrabold text-emerald-700 mt-1">{{ $posyandu->status_kesehatan ?? 'Sehat / Normal' }}</p>
                    </div>
                </div>
            </div>

            @if($posyandu->imunisasi_atau_vitamin)
                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100 text-blue-900">
                    <span class="font-bold block text-[11px] mb-0.5">Vaksinasi / Suplemen Diberikan:</span>
                    <span class="font-semibold">{{ $posyandu->imunisasi_atau_vitamin }}</span>
                </div>
            @endif

            @if($posyandu->catatan_kader)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-slate-700">
                    <span class="font-bold block text-[11px] text-slate-900 mb-0.5">Catatan & Anjuran Kader:</span>
                    <p class="italic leading-relaxed">{{ $posyandu->catatan_kader }}</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
