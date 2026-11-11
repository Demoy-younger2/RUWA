@extends('layouts.app', ['title' => 'Posyandu Pintar'])

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-heart-pulse text-rose-500"></i>
                <span>Posyandu Pintar (Balita & Lansia)</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pencatatan tumbuh kembang balita, imunisasi, serta monitoring kesehatan berkala bagi lansia.
            </p>
        </div>

        <a href="{{ route('posyandu.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Catat Pemeriksaan Baru</span>
        </a>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Balita Terpantau</span>
                <div class="w-8 h-8 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-baby"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3">{{ $totalBalita }}</p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Penimbangan & vitamin rutin</span>
        </div>

        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Lansia Terdata</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-person-cane"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3">{{ $totalLansia }}</p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Pemeriksaan tensi & gula darah</span>
        </div>

        <div class="p-5 rounded-3xl bg-gradient-to-tr from-rose-700 to-pink-700 text-white shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-rose-200 uppercase tracking-wider">Pemeriksaan Bulan Ini</span>
                <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-notes-medical"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-white mt-3">{{ $totalPemeriksaanBulanIni }}</p>
            <span class="text-[11px] text-rose-200 mt-0.5 block">Posyandu aktif & terjadwal</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap gap-2 text-xs font-semibold">
        <a href="{{ route('posyandu.index') }}" class="px-3.5 py-1.5 rounded-xl transition {{ !request('kategori') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua Peserta
        </a>
        <a href="{{ route('posyandu.index', ['kategori' => 'balita']) }}" class="px-3.5 py-1.5 rounded-xl transition {{ request('kategori') === 'balita' ? 'bg-pink-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Posyandu Balita (Anak)
        </a>
        <a href="{{ route('posyandu.index', ['kategori' => 'lansia']) }}" class="px-3.5 py-1.5 rounded-xl transition {{ request('kategori') === 'lansia' ? 'bg-purple-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Posyandu Lansia
        </a>
    </div>

    <!-- Health Check Records Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Nama Peserta</th>
                        <th class="py-3 px-4">Kategori / Usia</th>
                        <th class="py-3 px-4">Hasil Pengukuran</th>
                        <th class="py-3 px-4">Status Kesehatan</th>
                        <th class="py-3 px-4">Imunisasi / Vitamin</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($posyandus as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono text-slate-500">
                                {{ $item->tanggal_pemeriksaan->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->nama_peserta }}</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $item->nik ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->kategori === 'balita' ? 'bg-pink-100 text-pink-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($item->kategori) }}
                                </span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">
                                    {{ $item->usia_bulan_tahun ? $item->usia_bulan_tahun . ($item->kategori === 'balita' ? ' Bln' : ' Thn') : '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                @if($item->kategori === 'balita')
                                    <span>BB: <strong>{{ $item->berat_badan ?? '-' }} kg</strong></span> &bull;
                                    <span>TB: <strong>{{ $item->tinggi_badan ?? '-' }} cm</strong></span>
                                @else
                                    <span>Tensi: <strong>{{ $item->tensi_darah ?? '-' }}</strong></span> &bull;
                                    <span>Gula: <strong>{{ $item->gula_darah ? $item->gula_darah . ' mg/dL' : '-' }}</strong></span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    {{ $item->status_kesehatan ?? 'Sehat' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate">
                                {{ $item->imunisasi_atau_vitamin ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('posyandu.show', $item->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50" title="Lihat Kartu KMS">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt']))
                                        <form method="POST" action="{{ route('posyandu.destroy', $item->id) }}" onsubmit="return confirm('Hapus data posyandu ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada catatan posyandu pada kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posyandus->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $posyandus->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
