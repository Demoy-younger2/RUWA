@extends('layouts.app', ['title' => 'RUKEM - Rukun Kematian Warga'])

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-hand-holding-heart text-rose-600"></i>
                <span>RUKEM (Rukun Kematian & Solidaritas Duka)</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Layanan siaga duka warga, penyaluran santunan kematian, serta fasilitas pemandian, keranda, dan tenda duka.
            </p>
        </div>

        <a href="{{ route('rukem.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Lapor Berita Duka</span>
        </a>
    </div>

    <!-- Santunan Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Santunan Disalurkan</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-3">
                Rp {{ number_format($totalSantunanDiserahkan, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Diserahkan tunai ke ahli waris</span>
        </div>

        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Santunan Dalam Proses</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-hourglass"></i>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-amber-600 mt-3">
                Rp {{ number_format($totalSantunanTertunda, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Menunggu penyerahan resmi</span>
        </div>

        <div class="p-5 rounded-3xl bg-slate-900 text-white shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Fasilitas Siaga Duka</span>
                <div class="w-8 h-8 rounded-xl bg-white/10 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-toolbox"></i>
                </div>
            </div>
            <p class="text-xs text-slate-300 mt-3 font-medium leading-relaxed">
                Keranda Stenlis, Tenda Duka 2 Unit, Pemandian Jenazah, Sound Pengajian tersedia di Balai RW 05.
            </p>
        </div>
    </div>

    <!-- Lelayu Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-book-bible text-rose-600"></i>
                <span>Catatan Riwayat Duka & Penyaluran Santunan</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Nama Almarhum / Wafat</th>
                        <th class="py-3 px-4">Keluarga Ahli Waris</th>
                        <th class="py-3 px-4">Alamat Duka</th>
                        <th class="py-3 px-4">Pemakaman</th>
                        <th class="py-3 px-4 text-right">Santunan Duka</th>
                        <th class="py-3 px-4 text-center">Status Santunan</th>
                        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt']))
                            <th class="py-3 px-4 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rukems as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block text-xs">{{ $item->nama_almarhum }}</span>
                                <span class="text-[10px] text-slate-400">Wafat: {{ $item->tanggal_meninggal->format('d/m/Y') }} ({{ $item->jam_meninggal ?? 'Pagi' }})</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800 block">{{ $item->nama_ahli_waris }}</span>
                                <span class="text-[10px] text-slate-400">{{ $item->no_hp_ahli_waris ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 max-w-xs">
                                {{ $item->alamat_duka }} (RT {{ $item->rt }})
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <span>{{ $item->lokasi_pemakaman ?? 'TPU Sukamaju' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($item->jumlah_santunan, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $item->status_santunan === 'sudah_diserahkan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $item->status_santunan === 'sudah_diserahkan' ? 'Sudah Diserahkan' : 'Belum Diserahkan' }}
                                </span>
                                @if($item->petugas_penyerah)
                                    <span class="block text-[9px] text-slate-400 mt-0.5">Oleh: {{ $item->petugas_penyerah }}</span>
                                @endif
                            </td>
                            @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt']))
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($item->status_santunan === 'belum_diserahkan')
                                            <form method="POST" action="{{ route('rukem.serahkan', $item->id) }}" onsubmit="return confirm('Konfirmasi bahwa santunan telah diserahkan?')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-xl text-[10px] font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                                                    Serahkan
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('rukem.destroy', $item->id) }}" onsubmit="return confirm('Hapus catatan duka ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada laporan lelayu atau berita duka.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rukems->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $rukems->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
