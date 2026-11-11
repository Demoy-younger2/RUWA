@extends('layouts.app', ['title' => 'Kelola Rekening & QRIS Kas'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-qrcode text-emerald-600"></i>
                <span>Kelola Rekening & QRIS Kas</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Daftar rekening bank resmi dan kode QRIS untuk mempermudah warga membayar iuran lingkungan.
            </p>
        </div>

        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt']))
            <button type="button" onclick="document.getElementById('modalTambahRekening').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition">
                <i class="fa-solid fa-plus"></i>
                <span>+ Tambah Rekening / QRIS</span>
            </button>
        @endif
    </div>

    <!-- Navigation Sub-Menu Tab -->
    <div class="flex items-center gap-2 overflow-x-auto border-b border-slate-200 pb-2 text-xs font-bold">
        <a href="{{ route('kas.semua') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Semua Transaksi</a>
        <a href="{{ route('kas.pemasukan') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Pemasukan</a>
        <a href="{{ route('kas.pengeluaran') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Pengeluaran</a>
        <a href="{{ route('kas.kategori') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Kategori Kas</a>
        <a href="{{ route('kas.tagihan') }}" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Kelola Tagihan</a>
        <a href="{{ route('kas.rekening') }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white shadow-xs">Rekening & QRIS</a>
    </div>

    <!-- Cards Grid Rekening -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($rekeningList as $r)
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between hover:border-emerald-300 transition relative overflow-hidden">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $r->tingkat === 'RW' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }} uppercase">
                                Kas {{ $r->tingkat }} {{ $r->tingkat === 'RT' ? $r->rt : '05' }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-sm mt-1">{{ $r->nama_bank }}</h3>
                        </div>
                        <div class="w-9 h-9 rounded-2xl {{ $r->tipe === 'qris' ? 'bg-purple-50 text-purple-600' : ($r->tipe === 'ewallet' ? 'bg-lime-50 text-lime-600' : 'bg-blue-50 text-blue-600') }} flex items-center justify-center text-sm">
                            <i class="fa-solid {{ $r->tipe === 'qris' ? 'fa-qrcode' : ($r->tipe === 'ewallet' ? 'fa-wallet' : 'fa-building-columns') }}"></i>
                        </div>
                    </div>

                    @if($r->tipe === 'qris' && $r->qris_image)
                        <div class="flex flex-col items-center justify-center p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <img src="{{ $r->qris_image }}" alt="QRIS" class="w-36 h-36 object-contain rounded-xl shadow-xs">
                            <span class="text-[10px] font-bold text-slate-500 mt-2">Scan QRIS dari Mobile Banking / E-Wallet</span>
                        </div>
                    @endif

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Nomor Rekening / ID</span>
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-extrabold text-slate-900 text-base tracking-wide">{{ $r->nomor_rekening }}</span>
                        </div>
                        <span class="text-xs font-semibold text-slate-600 block">a/n {{ $r->atas_nama }}</span>
                    </div>

                    @if($r->keterangan)
                        <p class="text-xs text-slate-500">{{ $r->keterangan }}</p>
                    @endif
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="inline-flex items-center gap-1 font-bold text-[11px] {{ $r->is_aktif ? 'text-teal-600' : 'text-slate-400' }}">
                        <span class="w-2 h-2 rounded-full {{ $r->is_aktif ? 'bg-teal-500' : 'bg-slate-300' }}"></span>
                        {{ $r->is_aktif ? 'Aktif' : 'Non-aktif' }}
                    </span>

                    @if(in_array(Auth::user()->role, ['superadmin', 'bendahara_rw']))
                        <form action="{{ route('kas.rekening.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition" title="Hapus Rekening">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-400">
                Belum ada rekening atau QRIS terdaftar.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Rekening / QRIS -->
<div id="modalTambahRekening" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-base font-extrabold text-slate-900">Tambah Rekening / QRIS Baru</h3>
        <form action="{{ route('kas.rekening.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tingkat</label>
                    <select name="tingkat" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                        <option value="RW">Kas RW</option>
                        <option value="RT">Kas RT</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">RT</label>
                    <input type="text" name="rt" value="01" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tipe Akun</label>
                <select name="tipe" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
                    <option value="bank">Rekening Bank (BCA, Mandiri, BRI, BNI)</option>
                    <option value="qris">QRIS (Semua Bank & E-Wallet)</option>
                    <option value="ewallet">E-Wallet (GoPay, OVO, DANA)</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Bank / Merchant</label>
                <input type="text" name="nama_bank" required placeholder="Contoh: Bank Central Asia (BCA)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nomor Rekening / ID Akun</label>
                <input type="text" name="nomor_rekening" required placeholder="Contoh: 7120-8899-01" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Atas Nama (Pemilik)</label>
                <input type="text" name="atas_nama" required placeholder="Contoh: KAS RW 05 SUKAMAJU" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Keterangan Tambahan</label>
                <input type="text" name="keterangan" placeholder="Contoh: Rekening utama penerimaan iuran bulanan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 outline-none">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalTambahRekening').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold shadow-md">Simpan Rekening</button>
            </div>
        </form>
    </div>
</div>
@endsection
