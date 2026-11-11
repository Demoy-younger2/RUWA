@extends('layouts.app', ['title' => 'Bank Sampah Lingkungan'])

@section('content')
<div class="space-y-6" x-data="{ setorModal: false, tarikModal: false, kategoriModal: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-recycle text-teal-600"></i>
                <span>Bank Sampah Lingkungan RW 05</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Ubah sampah anorganik rumah tangga menjadi tabungan rupiah bernilai ekonomis.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button @click="setorModal = true" class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                <span>Setor Sampah</span>
            </button>

            @if(Auth::user()->isWarga())
                <button @click="tarikModal = true" class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-800 bg-white border border-slate-300 hover:bg-slate-50 transition">
                    <i class="fa-solid fa-hand-holding-dollar text-emerald-600"></i>
                    <span>Tarik Saldo</span>
                </button>
            @endif

            @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'bendahara_rw']))
                <button @click="kategoriModal = true" class="flex items-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    <i class="fa-solid fa-tag"></i>
                    <span>Tambah Kategori</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Saldo & Metrics Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Saldo Warga -->
        <div class="p-5 rounded-3xl bg-gradient-to-tr from-teal-800 to-emerald-800 text-white shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-teal-200 uppercase tracking-wider">Saldo Tabungan Saya</span>
                <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-white mt-3">
                Rp {{ number_format($mySaldo, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-teal-200 mt-0.5 block">Akun: {{ Auth::user()->name }}</span>
        </div>

        <!-- Total Terkumpul -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Sampah Terkumpul</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-weight-hanging"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3">
                {{ number_format($totalSampahTerkumpul, 1, ',', '.') }} <span class="text-base font-normal text-slate-500">kg</span>
            </p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Tereduksi dari tempat pembuangan akhir</span>
        </div>

        <!-- Perputaran Nilai Rupiah -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Perputaran Nilai Sampah</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3">
                Rp {{ number_format($totalPerputaranRupiah, 0, ',', '.') }}
            </p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Total hasil tabungan seluruh warga</span>
        </div>
    </div>

    <!-- Daftar Kategori Harga Sampah Daur Ulang -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
        <h2 class="text-sm font-bold text-slate-900 uppercase text-[11px] tracking-wider mb-4 flex items-center gap-2">
            <i class="fa-solid fa-tags text-teal-600"></i>
            <span>Daftar Harga Beli Sampah Terpilah</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($kategoriList as $kat)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">{{ $kat->nama_kategori }}</h3>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $kat->deskripsi ?? 'Sampah bersih & terpilah' }}</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-slate-200/50 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase">Harga / {{ $kat->satuan }}</span>
                        <span class="text-xs font-extrabold text-emerald-600">Rp {{ number_format($kat->harga_per_kg, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Riwayat Transaksi Bank Sampah -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                <span>Riwayat Transaksi Penyetoran & Penarikan Saldo</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Kode & Tanggal</th>
                        <th class="py-3 px-4">Warga</th>
                        <th class="py-3 px-4">Jenis Transaksi</th>
                        <th class="py-3 px-4">Kategori / Berat</th>
                        <th class="py-3 px-4 text-right">Nilai Rupiah</th>
                        <th class="py-3 px-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transaksis as $trx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-mono">
                                <span class="font-bold text-slate-900 block">{{ $trx->kode_transaksi }}</span>
                                <span class="text-[10px] text-slate-400">{{ $trx->tanggal->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ $trx->user->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $trx->jenis_transaksi === 'setor' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $trx->jenis_transaksi === 'setor' ? 'Setor Sampah' : 'Tarik Saldo' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($trx->jenis_transaksi === 'setor')
                                    <span class="font-bold text-slate-900">{{ $trx->bankSampah->nama_kategori ?? '-' }}</span>
                                    <span class="text-[11px] text-slate-500 block">({{ $trx->berat }} kg)</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-xs {{ $trx->jenis_transaksi === 'setor' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $trx->jenis_transaksi === 'setor' ? '+' : '-' }} Rp {{ number_format($trx->total_rupiah, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                {{ $trx->catatan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada riwayat transaksi bank sampah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transaksis->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Setor Sampah -->
    <div x-show="setorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-transition>
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200" @click.away="setorModal = false">
            <h3 class="text-base font-extrabold text-slate-900 mb-1">Formulir Setor Sampah Daur Ulang</h3>
            <p class="text-xs text-slate-500 mb-4">Catat timbangan sampah terpilah warga.</p>

            <form method="POST" action="{{ route('bank-sampah.setor') }}" class="space-y-3 text-xs">
                @csrf
                @if(!Auth::user()->isWarga())
                    <div>
                        <label class="block font-bold text-slate-700">Pilih Warga Penyetor *</label>
                        <select name="user_id" required class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white">
                            @foreach($wargaUsers as $wu)
                                <option value="{{ $wu->id }}">{{ $wu->name }} (RT {{ $wu->rt ?? '01' }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block font-bold text-slate-700">Kategori Sampah *</label>
                    <select name="bank_sampah_id" required class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white">
                        @foreach($kategoriList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }} (Rp {{ number_format($k->harga_per_kg, 0, ',', '.') }}/{{ $k->satuan }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700">Berat Timbangan (Kg) *</label>
                    <input type="number" step="0.1" name="berat" required min="0.1" placeholder="Contoh: 5.5" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="catatan" placeholder="Contoh: 2 kantong botol bersih" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                </div>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button" @click="setorModal = false" class="py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="py-2.5 px-5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md">
                        Simpan Setoran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tarik Saldo -->
    <div x-show="tarikModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-transition>
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200" @click.away="tarikModal = false">
            <h3 class="text-base font-extrabold text-slate-900 mb-1">Tarik Saldo Tunai Bank Sampah</h3>
            <p class="text-xs text-slate-500 mb-4">Saldo Anda saat ini: <strong class="text-emerald-700">Rp {{ number_format($mySaldo, 0, ',', '.') }}</strong></p>

            <form method="POST" action="{{ route('bank-sampah.tarik_saldo') }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700">Nominal Penarikan (Rp) *</label>
                    <input type="number" name="nominal" required min="10000" max="{{ $mySaldo }}" placeholder="Contoh: 50000" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Keterangan</label>
                    <input type="text" name="catatan" value="Penarikan tunai saldo Bank Sampah RW 05" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                </div>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button" @click="tarikModal = false" class="py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="py-2.5 px-5 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-md">
                        Konfirmasi Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Kategori (Pengurus) -->
    <div x-show="kategoriModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-transition>
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200" @click.away="kategoriModal = false">
            <h3 class="text-base font-extrabold text-slate-900 mb-1">Tambah Kategori Sampah Baru</h3>
            <p class="text-xs text-slate-500 mb-4">Atur jenis sampah dan patokan harga beli per satuan.</p>

            <form method="POST" action="{{ route('bank-sampah.kategori.store') }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700">Nama Kategori Sampah *</label>
                    <input type="text" name="nama_kategori" required placeholder="Contoh: Kaleng Minuman / Tembaga" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700">Harga Per Satuan (Rp) *</label>
                        <input type="number" name="harga_per_kg" required min="100" placeholder="5000" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700">Satuan *</label>
                        <select name="satuan" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white">
                            <option value="kg">kg</option>
                            <option value="liter">liter</option>
                            <option value="buah">buah</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Keterangan / Kriteria</label>
                    <input type="text" name="deskripsi" placeholder="Syarat kondisi sampah diterima..." class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                </div>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button" @click="kategoriModal = false" class="py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="py-2.5 px-5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-black shadow-md">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
