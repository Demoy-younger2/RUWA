@extends('layouts.app', ['title' => 'Koperasi Warga Mandiri'])

@section('content')
<div class="space-y-6" x-data="{ transaksiModal: false, defaultJenis: 'simpanan_sukarela', defaultAnggotaId: '{{ $myAnggota->id ?? '' }}' }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-hand-holding-dollar text-emerald-600"></i>
                <span>Koperasi Warga Mandiri RW 05</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Layanan simpan pinjam berazaskan gotong royong, dana bergulir UMKM, dan kesejahteraan ekonomi warga.
            </p>
        </div>

        @if($myAnggota)
            <div class="flex items-center gap-2">
                <button type="button" @click="defaultJenis = 'simpanan_sukarela'; defaultAnggotaId = '{{ $myAnggota->id }}'; transaksiModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Setor Simpanan</span>
                </button>
                <button type="button" @click="defaultJenis = 'pinjaman'; defaultAnggotaId = '{{ $myAnggota->id }}'; transaksiModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-amber-700 bg-amber-100 hover:bg-amber-200 transition">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    <span>Ajukan Pinjaman</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Cooperative Overview Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Simpanan Terkumpul</span>
                <span class="text-xl font-extrabold text-slate-900 mt-0.5 block">Rp {{ number_format($totalSimpananSemua, 0, ',', '.') }}</span>
                <span class="text-[10px] text-emerald-600 font-semibold">Dana abadi & kas simpanan warga</span>
            </div>
        </div>

        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-handshake-angle"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pinjaman Bergulir</span>
                <span class="text-xl font-extrabold text-slate-900 mt-0.5 block">Rp {{ number_format($totalPinjamanSemua, 0, ',', '.') }}</span>
                <span class="text-[10px] text-amber-600 font-semibold">Pinjaman usaha produktif berjalan</span>
            </div>
        </div>

        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Anggota Terdaftar</span>
                <span class="text-xl font-extrabold text-slate-900 mt-0.5 block">{{ $totalAnggotaAktif }} Orang</span>
                <span class="text-[10px] text-indigo-600 font-semibold">Warga RT 01 s/d RT 06</span>
            </div>
        </div>
    </div>

    <!-- Citizen Membership Status / Card -->
    @if(!$myAnggota)
        <!-- Registration Banner -->
        <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-700 to-slate-900 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-3 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-[11px] font-bold tracking-wider uppercase">
                    <i class="fa-solid fa-gem text-amber-300"></i>
                    <span>Ekonomi Kerakyatan</span>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight">Bergabung Menjadi Anggota Koperasi Warga</h2>
                <p class="text-xs sm:text-sm text-emerald-100 leading-relaxed">
                    Nikmati kemudahan pinjaman modal usaha UMKM tanpa jaminan rumit, bunga ramah tetangga, pembagian Sisa Hasil Usaha (SHU) tahunan, dan tabungan masa depan keluarga.
                </p>
                <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-emerald-200 pt-1">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-amber-300"></i> Simpanan Pokok Awal Rp 100.000</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-amber-300"></i> Transparan & Tercatat Online</span>
                </div>
            </div>

            <div class="shrink-0 w-full sm:w-auto text-center">
                <form method="POST" action="{{ route('koperasi.daftar') }}" onsubmit="return confirm('Konfirmasi pendaftaran menjadi anggota Koperasi Warga RW 05?')">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-white text-emerald-800 hover:bg-amber-300 hover:text-amber-950 font-extrabold text-sm shadow-xl transition transform active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-id-card"></i>
                        <span>Daftar Sekarang</span>
                    </button>
                </form>
                <span class="text-[10px] text-emerald-200 mt-2 block">Otomatis aktif & mendapat nomor ID anggota</span>
            </div>
        </div>
    @else
        <!-- Member Digital Pass Card & Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Digital Member Card -->
            <div class="p-6 rounded-3xl bg-gradient-to-tr from-slate-900 via-emerald-950 to-teal-800 text-white shadow-xl flex flex-col justify-between space-y-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-seedling text-amber-400 text-lg"></i>
                        <span class="text-xs font-black tracking-widest uppercase">KARTU ANGGOTA KOPERASI</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        {{ strtoupper($myAnggota->status) }}
                    </span>
                </div>

                <div class="space-y-1 relative z-10">
                    <span class="text-[10px] text-emerald-300 font-mono tracking-widest block">NOMOR ANGGOTA</span>
                    <span class="text-xl sm:text-2xl font-mono font-extrabold tracking-widest text-amber-300 block">
                        {{ $myAnggota->no_anggota }}
                    </span>
                    <h3 class="text-base font-bold text-white pt-1">{{ Auth::user()->name }}</h3>
                    <p class="text-[11px] text-emerald-200">RT {{ Auth::user()->rt ?? '01' }} / RW 05 • Bergabung: {{ \Carbon\Carbon::parse($myAnggota->tanggal_daftar)->format('d M Y') }}</p>
                </div>

                <div class="pt-4 border-t border-white/10 grid grid-cols-2 gap-4 relative z-10 text-xs">
                    <div>
                        <span class="text-[10px] text-emerald-300 block">Saldo Simpanan:</span>
                        <span class="text-sm font-extrabold text-white">Rp {{ number_format($myAnggota->total_simpanan, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-amber-300 block">Kewajiban Pinjaman:</span>
                        <span class="text-sm font-extrabold text-amber-300">Rp {{ number_format($myAnggota->total_pinjaman, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Fast Action Hub -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between space-y-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        <span>Aksi Finansial Cepat</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Pilih jenis transaksi simpanan atau pinjaman anggota di bawah ini:</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <button type="button" @click="defaultJenis = 'simpanan_wajib'; defaultAnggotaId = '{{ $myAnggota->id }}'; transaksiModal = true" class="p-3.5 rounded-2xl bg-slate-50 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 border border-slate-100 transition text-center space-y-2 group">
                        <div class="w-10 h-10 rounded-xl bg-white group-hover:bg-emerald-600 group-hover:text-white text-emerald-600 mx-auto flex items-center justify-center text-sm shadow-xs transition">
                            <i class="fa-solid fa-piggy-bank"></i>
                        </div>
                        <span class="text-xs font-bold block">Simpanan Wajib</span>
                        <span class="text-[10px] text-slate-400 block">Rp 20.000 / bln</span>
                    </button>

                    <button type="button" @click="defaultJenis = 'simpanan_sukarela'; defaultAnggotaId = '{{ $myAnggota->id }}'; transaksiModal = true" class="p-3.5 rounded-2xl bg-slate-50 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 border border-slate-100 transition text-center space-y-2 group">
                        <div class="w-10 h-10 rounded-xl bg-white group-hover:bg-emerald-600 group-hover:text-white text-emerald-600 mx-auto flex items-center justify-center text-sm shadow-xs transition">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <span class="text-xs font-bold block">Simpan Sukarela</span>
                        <span class="text-[10px] text-slate-400 block">Bebas nominal</span>
                    </button>

                    <button type="button" @click="defaultJenis = 'angsuran'; defaultAnggotaId = '{{ $myAnggota->id }}'; transaksiModal = true" class="p-3.5 rounded-2xl bg-slate-50 hover:bg-blue-50 hover:text-blue-700 text-slate-700 border border-slate-100 transition text-center space-y-2 group">
                        <div class="w-10 h-10 rounded-xl bg-white group-hover:bg-blue-600 group-hover:text-white text-blue-600 mx-auto flex items-center justify-center text-sm shadow-xs transition">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <span class="text-xs font-bold block">Bayar Angsuran</span>
                        <span class="text-[10px] text-slate-400 block">Pelunasan kredit</span>
                    </button>

                    <button type="button" @click="defaultJenis = 'penarikan'; defaultAnggotaId = '{{ $myAnggota->id }}'; transaksiModal = true" class="p-3.5 rounded-2xl bg-slate-50 hover:bg-rose-50 hover:text-rose-700 text-slate-700 border border-slate-100 transition text-center space-y-2 group">
                        <div class="w-10 h-10 rounded-xl bg-white group-hover:bg-rose-600 group-hover:text-white text-rose-600 mx-auto flex items-center justify-center text-sm shadow-xs transition">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>
                        <span class="text-xs font-bold block">Tarik Tabungan</span>
                        <span class="text-[10px] text-slate-400 block">Pencairan saldo</span>
                    </button>
                </div>

                <div class="p-3 rounded-2xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-between text-xs text-emerald-900">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-shield-heart text-emerald-600"></i>
                        <span>SHU Periode Berjalan dialokasikan setiap tutup buku bulan Desember.</span>
                    </span>
                    <span class="font-extrabold text-emerald-800">Bunga 0.8% / bln</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Transactions History & Members Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- History Transaksi Terkini -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                        <span>Mutasi & Transaksi Koperasi Terakhir</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan simpanan, penarikan, pinjaman, dan angsuran warga</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Anggota</th>
                            <th class="py-3 px-3">Jenis Transaksi</th>
                            <th class="py-3 px-3">Keterangan</th>
                            <th class="py-3 px-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksis as $trx)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3 text-slate-500 font-mono whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-bold text-slate-900 block">{{ $trx->anggota->user->name ?? 'Anggota' }}</span>
                                    <span class="text-[10px] text-emerald-600 font-mono">{{ $trx->anggota->no_anggota ?? '' }}</span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ in_array($trx->jenis, ['simpanan_pokok', 'simpanan_wajib', 'simpanan_sukarela']) ? 'bg-emerald-100 text-emerald-800' : ($trx->jenis === 'pinjaman' ? 'bg-amber-100 text-amber-800' : ($trx->jenis === 'angsuran' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800')) }}">
                                        {{ ucwords(str_replace('_', ' ', $trx->jenis)) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-slate-600 max-w-xs truncate" title="{{ $trx->keterangan }}">
                                    {{ $trx->keterangan }}
                                </td>
                                <td class="py-3 px-3 text-right font-extrabold {{ in_array($trx->jenis, ['simpanan_pokok', 'simpanan_wajib', 'simpanan_sukarela', 'angsuran']) ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ in_array($trx->jenis, ['simpanan_pokok', 'simpanan_wajib', 'simpanan_sukarela', 'angsuran']) ? '+' : '-' }} Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Belum ada catatan mutasi transaksi koperasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daftar Anggota Aktif (Ringkasan Pengurus) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-address-book text-emerald-600"></i>
                        <span>Daftar Anggota</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $semuaAnggota->total() }} anggota terdaftar</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto space-y-2 pr-1">
                @forelse($semuaAnggota as $agt)
                    <div class="pt-3 pb-2 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <span class="font-bold text-slate-900 block">{{ $agt->user->name ?? 'Warga' }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $agt->no_anggota }} • RT {{ $agt->user->rt ?? '01' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-extrabold text-emerald-600 block">Rp {{ number_format($agt->total_simpanan, 0, ',', '.') }}</span>
                            @if($agt->total_pinjaman > 0)
                                <span class="text-[10px] text-amber-600 font-semibold block">Pinjam: Rp {{ number_format($agt->total_pinjaman, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada anggota koperasi terdaftar.</p>
                @endforelse
            </div>

            <div class="pt-2">
                {{ $semuaAnggota->links() }}
            </div>
        </div>

    </div>

    <!-- Modal Form Transaksi Koperasi -->
    <div x-show="transaksiModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl" @click.away="transaksiModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Form Transaksi Koperasi</h3>
                        <p class="text-xs text-slate-400">Pencatatan simpanan, pinjaman, dan angsuran</p>
                    </div>
                </div>
                <button type="button" @click="transaksiModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('koperasi.transaksi') }}" class="space-y-4">
                @csrf

                <!-- Pilih Anggota -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Anggota Koperasi <span class="text-rose-500">*</span></label>
                    @if(Auth::user()->isWarga() && $myAnggota)
                        <input type="hidden" name="koperasi_anggota_id" value="{{ $myAnggota->id }}">
                        <div class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800">
                            {{ Auth::user()->name }} ({{ $myAnggota->no_anggota }})
                        </div>
                    @else
                        <select name="koperasi_anggota_id" x-model="defaultAnggotaId" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none bg-white">
                            @foreach($semuaAnggota as $agt)
                                <option value="{{ $agt->id }}" {{ ($myAnggota && $myAnggota->id === $agt->id) ? 'selected' : '' }}>
                                    {{ $agt->user->name ?? 'Warga' }} - {{ $agt->no_anggota }} (Simpanan: Rp {{ number_format($agt->total_simpanan, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Jenis Transaksi -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Jenis Transaksi <span class="text-rose-500">*</span></label>
                    <select name="jenis" x-model="defaultJenis" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none bg-white">
                        <option value="simpanan_wajib">Setor Simpanan Wajib (Bulanan)</option>
                        <option value="simpanan_sukarela">Setor Simpanan Sukarela</option>
                        <option value="pinjaman">Pengajuan Pinjaman Modal Warga</option>
                        <option value="angsuran">Pembayaran Angsuran Pinjaman</option>
                        <option value="penarikan">Penarikan Tabungan Sukarela</option>
                    </select>
                </div>

                <!-- Nominal -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Nominal Transaksi (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">Rp</span>
                        <input type="number" name="nominal" min="5000" step="1000" required placeholder="Contoh: 100000" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-extrabold focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Keterangan / Berita Acara</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Setoran simpanan bulan ini / Tambahan modal warung" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs outline-none">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="transaksiModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20">
                        Proses Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
