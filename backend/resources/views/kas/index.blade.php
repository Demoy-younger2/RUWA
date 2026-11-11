@extends('layouts.app', ['title' => 'Transparansi Kas RW & RT'])

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-coins text-emerald-600"></i>
                <span>Transparansi Kas RW & RT</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Laporan keuangan terbuka, pencatatan iuran lingkungan, pemeliharaan sarana, dan saldo kas secara transparan.
            </p>
        </div>

        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt']))
            <a href="{{ route('kas.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                <span>Catat Transaksi Kas</span>
            </a>
        @endif
    </div>

    <!-- Saldo & Metrics Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Pemasukan</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrow-down-left"></i>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-3">
                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Iuran warga & sumbangan</span>
        </div>

        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Pengeluaran</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrow-up-right"></i>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-rose-600 mt-3">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Operasional & perawatan</span>
        </div>

        <div class="p-5 rounded-3xl bg-gradient-to-tr from-emerald-800 to-teal-800 text-white shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider">Saldo Kas Bersih</span>
                <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-white mt-3">
                Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-emerald-200 mt-0.5 block">Saldo real-time kas lingkungan</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex flex-wrap items-center gap-1.5 font-semibold">
            <a href="{{ route('kas.index') }}" class="px-3 py-1.5 rounded-xl transition {{ !request('tingkat') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Semua Tingkat
            </a>
            <a href="{{ route('kas.index', ['tingkat' => 'RW']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('tingkat') === 'RW' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Kas RW 05
            </a>
            <a href="{{ route('kas.index', ['tingkat' => 'RT']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('tingkat') === 'RT' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Kas RT
            </a>
        </div>

        <form method="GET" action="{{ route('kas.index') }}" class="flex items-center gap-2">
            @if(request('tingkat')) <input type="hidden" name="tingkat" value="{{ request('tingkat') }}"> @endif
            <select name="jenis" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                <option value="">Semua Jenis Transaksi</option>
                <option value="pemasukan" {{ request('jenis') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                <option value="pengeluaran" {{ request('jenis') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
        </form>
    </div>

    <!-- Kas Transaction Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Tingkat</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-right">Nominal (Rp)</th>
                        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt']))
                            <th class="py-3 px-4 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kasList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-mono text-slate-500">
                                {{ $item->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->tingkat === 'RW' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $item->tingkat }} {{ $item->tingkat === 'RT' ? '01' : '05' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $item->kategori }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-sm">
                                {{ $item->keterangan }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-xs {{ $item->jenis === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $item->jenis === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt']))
                                <td class="py-3 px-4 text-center">
                                    <form method="POST" action="{{ route('kas.destroy', $item->id) }}" onsubmit="return confirm('Hapus pencatatan kas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada riwayat transaksi kas yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kasList->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $kasList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
