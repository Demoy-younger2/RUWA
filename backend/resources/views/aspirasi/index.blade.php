@extends('layouts.app', ['title' => 'Aspirasi & Pengaduan Warga'])

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-comments text-amber-500"></i>
                <span>Aspirasi & Pengaduan Warga</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kanal resmi penyampaian saran, usulan pembangunan infrastruktur, dan aduan lingkungan kepada pengurus RT & RW.
            </p>
        </div>

        <a href="{{ route('aspirasi.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Tulis Aspirasi / Aduan</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap gap-2 text-xs font-semibold">
        <a href="{{ route('aspirasi.index') }}" class="px-3.5 py-1.5 rounded-xl transition {{ !request('status') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua Aduan
        </a>
        <a href="{{ route('aspirasi.index', ['status' => 'menunggu']) }}" class="px-3.5 py-1.5 rounded-xl transition {{ request('status') === 'menunggu' ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Menunggu Tindak Lanjut
        </a>
        <a href="{{ route('aspirasi.index', ['status' => 'diproses']) }}" class="px-3.5 py-1.5 rounded-xl transition {{ request('status') === 'diproses' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Sedang Diproses
        </a>
        <a href="{{ route('aspirasi.index', ['status' => 'selesai']) }}" class="px-3.5 py-1.5 rounded-xl transition {{ request('status') === 'selesai' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Telah Ditindaklanjuti
        </a>
    </div>

    <!-- Aspirasi Cards List -->
    <div class="space-y-4">
        @forelse($aspirasis as $item)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4" x-data="{ tanggapiOpen: false }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-800">
                            {{ $item->kategori }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $item->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'diproses' ? 'bg-blue-100 text-blue-800' : ($item->status === 'ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')) }}">
                            {{ ucfirst($item->status) }}
                        </span>
                        @if($item->is_anonim)
                            <span class="text-[10px] text-slate-400 font-semibold italic">Pengirim Anonim</span>
                        @else
                            <span class="text-xs font-bold text-slate-700">Oleh: {{ $item->user->name ?? 'Warga' }}</span>
                        @endif
                    </div>
                    <span class="text-[11px] text-slate-400 font-mono">{{ $item->created_at->format('d M Y, H:i') }}</span>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">{{ $item->judul }}</h3>
                    <p class="text-xs text-slate-700 mt-2 leading-relaxed whitespace-pre-line">{{ $item->isi }}</p>
                    @if($item->lokasi)
                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-map-pin text-rose-500 text-[11px]"></i>
                            <span>Lokasi: {{ $item->lokasi }}</span>
                        </p>
                    @endif
                </div>

                <!-- Respon Pengurus jika ada -->
                @if($item->tanggapan)
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-xs text-emerald-900 space-y-1">
                        <div class="flex items-center justify-between font-bold text-emerald-800">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-reply"></i>
                                <span>Tanggapan Resmi Pengurus:</span>
                            </span>
                            @if($item->ditanggapi_oleh)
                                <span class="text-[11px] font-normal text-emerald-700">({{ $item->ditanggapi_oleh }})</span>
                            @endif
                        </div>
                        <p class="italic leading-relaxed text-slate-800">{{ $item->tanggapan }}</p>
                    </div>
                @endif

                <!-- Tombol Respon untuk Pengurus RT/RW -->
                @if(!Auth::user()->isWarga())
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <button type="button" @click="tanggapiOpen = !tanggapiOpen" class="font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-comment-dots"></i>
                            <span x-text="tanggapiOpen ? 'Tutup Form Respon' : 'Beri Tanggapan / Update Status'"></span>
                        </button>
                    </div>

                    <!-- Dropdown Form Tanggapan -->
                    <div x-show="tanggapiOpen" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 mt-3 space-y-3">
                        <form method="POST" action="{{ route('aspirasi.tanggapi', $item->id) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Status Tindak Lanjut</label>
                                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white">
                                    <option value="diproses" {{ $item->status === 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                    <option value="selesai" {{ $item->status === 'selesai' ? 'selected' : '' }}>Telah Selesai Ditindaklanjuti</option>
                                    <option value="ditolak" {{ $item->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Isi Tanggapan Pengurus</label>
                                <textarea name="tanggapan" required rows="2" placeholder="Tuliskan respon resmi, solusi atau jadwal pengerjaan..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white">{{ $item->tanggapan }}</textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="tanggapiOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm">
                                    Kirim Tanggapan
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="py-12 text-center text-xs text-slate-400 bg-white rounded-3xl border border-slate-200">
                Belum ada aspirasi atau aduan dalam kategori ini.
            </div>
        @endforelse
    </div>

    @if($aspirasis->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $aspirasis->links() }}
        </div>
    @endif

</div>
@endsection
