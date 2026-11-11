@extends('layouts.app', ['title' => 'Kelola Tagihan & Iuran Warga'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i>
                <span>Kelola Tagihan & Iuran Warga</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pencatatan iuran wajib bulanan, sumbangan pembangunan, dan verifikasi status pembayaran warga.
            </p>
        </div>

        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt']))
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('modalGenerateTagihan').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Generate Massal</span>
                </button>
                <button type="button" onclick="document.getElementById('modalTambahTagihan').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>+ Buat Tagihan</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Navigation Sub-Menu Tab -->
    <div class="flex items-center gap-2 overflow-x-auto border-b border-slate-200 pb-2 text-xs font-bold">
        <a href="{{ route('kas.semua') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Semua Transaksi</a>
        <a href="{{ route('kas.pemasukan') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Pemasukan</a>
        <a href="{{ route('kas.pengeluaran') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Pengeluaran</a>
        <a href="{{ route('kas.kategori') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Kategori Kas</a>
        <a href="{{ route('kas.tagihan') }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white shadow-xs">Kelola Tagihan</a>
        <a href="{{ route('kas.rekening') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Rekening & QRIS</a>
    </div>

    <!-- Tabel Tagihan -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Kode & Judul Tagihan</th>
                        <th class="py-3 px-4">Warga & Rumah</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4">Nominal</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Pembayaran</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($tagihans as $t)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <span class="font-mono text-[11px] font-bold text-emerald-700 block">{{ $t->kode_tagihan }}</span>
                                <span class="font-semibold text-slate-800 text-xs">{{ $t->judul }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $t->kategori }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-800 block">{{ $t->nama_warga }}</span>
                                <span class="text-[11px] text-slate-500">{{ $t->nomor_rumah ?? 'RT ' . $t->rt }}</span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-600">{{ $t->periode }}</td>
                            <td class="py-3 px-4 font-extrabold text-slate-900">
                                Rp {{ number_format($t->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                @if($t->status === 'lunas')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Lunas
                                    </span>
                                @elseif($t->status === 'menunggu_verifikasi')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                                        <i class="fa-solid fa-clock text-[9px]"></i> Menunggu Verifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-[11px] text-slate-700 block">{{ $t->metode_pembayaran ?? '-' }}</span>
                                @if($t->diverifikasi_oleh)
                                    <span class="text-[10px] text-slate-400">Verif: {{ $t->diverifikasi_oleh }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($t->status === 'menunggu_verifikasi' && in_array(Auth::user()->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt']))
                                    <form action="{{ route('kas.tagihan.verifikasi', $t->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="lunas">
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[10px] shadow-xs hover:bg-emerald-700" title="Verifikasi Lunas">
                                            ✓ Setujui
                                        </button>
                                    </form>
                                @endif

                                @if(in_array(Auth::user()->role, ['superadmin', 'bendahara_rw']))
                                    <form action="{{ route('kas.tagihan.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tagihan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition" title="Hapus">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada daftar tagihan iuran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tagihans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $tagihans->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Tagihan Satuan -->
<div id="modalTambahTagihan" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-extrabold text-slate-900">Buat Tagihan Baru</h3>
        <form action="{{ route('kas.tagihan.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Judul Tagihan</label>
                <input type="text" name="judul" required placeholder="Contoh: Iuran Kebersihan RT 01 Bulan November" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tingkat</label>
                    <select name="tingkat" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                        <option value="RT">Tingkat RT</option>
                        <option value="RW">Tingkat RW</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">RT</label>
                    <input type="text" name="rt" value="{{ Auth::user()->rt ?? '01' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Warga Penerima Tagihan</label>
                <input type="text" name="nama_warga" required placeholder="Nama kepala keluarga / warga..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Periode</label>
                    <input type="text" name="periode" required value="November 2026" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" required min="5000" step="5000" value="50000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kategori</label>
                <input type="text" name="kategori" value="Iuran Kebersihan Lingkungan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalTambahTagihan').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold shadow-md">Terbitkan Tagihan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Generate Tagihan Massal -->
<div id="modalGenerateTagihan" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-extrabold text-slate-900">⚡ Generate Tagihan Massal ke Seluruh Warga</h3>
        <p class="text-xs text-slate-500">Menerbitkan tagihan secara serentak ke semua warga terdaftar.</p>
        <form action="{{ route('kas.tagihan.generate') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Judul Tagihan</label>
                <input type="text" name="judul" required value="Iuran Warga & Kebersihan Bulan November 2026" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tingkat</label>
                    <select name="tingkat" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                        <option value="RT">Tingkat RT</option>
                        <option value="RW">Tingkat RW</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Target RT</label>
                    <input type="text" name="rt" value="01" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Periode</label>
                    <input type="text" name="periode" required value="November 2026" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Nominal per KK (Rp)</label>
                    <input type="number" name="nominal" required min="5000" step="5000" value="50000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kategori</label>
                <input type="text" name="kategori" value="Iuran Kebersihan Lingkungan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalGenerateTagihan').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold shadow-md">Generate Sekarang</button>
            </div>
        </form>
    </div>
</div>
@endsection
