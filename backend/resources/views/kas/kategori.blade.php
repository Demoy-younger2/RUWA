@extends('layouts.app', ['title' => 'Master Kategori Kas'])

@section('content')
<div class="space-y-6">

    <!-- Header & Submenu Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-tags text-emerald-600"></i>
                <span>Master Kategori Kas</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola kategori pos pemasukan dan pengeluaran kas tingkat RW 05 dan RT 01.
            </p>
        </div>

        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt']))
            <button type="button" onclick="document.getElementById('modalTambahKategori').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition">
                <i class="fa-solid fa-plus"></i>
                <span>+ Tambah Kategori Baru</span>
            </button>
        @endif
    </div>

    <!-- Navigation Sub-Menu Tab -->
    <div class="flex items-center gap-2 overflow-x-auto border-b border-slate-200 pb-2 text-xs font-bold">
        <a href="{{ route('kas.semua') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Semua Transaksi</a>
        <a href="{{ route('kas.pemasukan') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Pemasukan</a>
        <a href="{{ route('kas.pengeluaran') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Pengeluaran</a>
        <a href="{{ route('kas.kategori') }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white shadow-xs">Kategori Kas</a>
        <a href="{{ route('kas.tagihan') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Kelola Tagihan</a>
        <a href="{{ route('kas.rekening') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Rekening & QRIS</a>
    </div>

    <!-- Grid / Table Kategori -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4">Nama Kategori</th>
                        <th class="py-3 px-4">Tingkat</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($kategoriList as $k)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                @if($k->jenis === 'pemasukan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        <i class="fa-solid fa-arrow-down text-[9px]"></i> Pemasukan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700">
                                        <i class="fa-solid fa-arrow-up text-[9px]"></i> Pengeluaran
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $k->nama }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-600">{{ $k->tingkat }}</td>
                            <td class="py-3 px-4 text-slate-500 max-w-xs truncate">{{ $k->deskripsi ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $k->is_aktif ? 'bg-teal-50 text-teal-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $k->is_aktif ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if(in_array(Auth::user()->role, ['superadmin', 'bendahara_rw']))
                                    <form action="{{ route('kas.kategori.destroy', $k->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition" title="Hapus">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada kategori kas tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div id="modalTambahKategori" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-extrabold text-slate-900">Tambah Kategori Kas Baru</h3>
        <form action="{{ route('kas.kategori.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Kategori</label>
                <input type="text" name="nama" required placeholder="Contoh: Iuran Kebersihan Lingkungan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none focus:border-emerald-600">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Arus</label>
                    <select name="jenis" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                        <option value="pemasukan">Pemasukan (+)</option>
                        <option value="pengeluaran">Pengeluaran (-)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tingkat</label>
                    <select name="tingkat" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                        <option value="RW">Tingkat RW</option>
                        <option value="RT">Tingkat RT</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Deskripsi / Peruntukan</label>
                <textarea name="deskripsi" rows="2" placeholder="Keterangan singkat kategori..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalTambahKategori').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold shadow-md">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>
@endsection
