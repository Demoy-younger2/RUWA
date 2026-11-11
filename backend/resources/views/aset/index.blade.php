@extends('layouts.app', ['title' => 'Inventaris & Peminjaman Aset Warga'])

@section('content')
<div class="space-y-6" x-data="{ pinjamModal: false, selectedAset: null, updateModal: false, selectedPinjam: null }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-boxes-stacked text-teal-600"></i>
                <span>Inventaris & Fasilitas Bersama</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Katalog perlengkapan lingkungan RW/RT (tenda, kursi, sound system, alat kebersihan) dan layanan peminjaman warga.
            </p>
        </div>

        @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt']))
            <a href="{{ route('aset.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                <span>Catat Aset Baru</span>
            </a>
        @endif
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Jenis Aset</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-900">{{ $asets->total() }}</span>
                <span class="text-xs text-slate-400 font-semibold">item</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-teal-600 uppercase tracking-wider block">Total Unit Tersedia</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-teal-600">{{ $asets->sum('jumlah_tersedia') }}</span>
                <span class="text-xs text-teal-500 font-semibold">unit siap pakai</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Sedang Dipinjam</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-amber-600">{{ $peminjamans->where('status', 'dipinjam')->count() }}</span>
                <span class="text-xs text-amber-500 font-semibold">agenda</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider block">Menunggu Konfirmasi</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-blue-600">{{ $peminjamans->where('status', 'menunggu')->count() }}</span>
                <span class="text-xs text-blue-500 font-semibold">permohonan</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
            <a href="{{ route('aset.index') }}" class="px-3 py-1.5 rounded-xl transition {{ !request('tingkat') && !request('kategori') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Semua Aset
            </a>
            <a href="{{ route('aset.index', ['tingkat' => 'RW']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('tingkat') === 'RW' ? 'bg-teal-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Aset RW
            </a>
            <a href="{{ route('aset.index', ['tingkat' => 'RT']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('tingkat') === 'RT' ? 'bg-teal-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Aset RT
            </a>
        </div>

        <!-- Filter Kategori Dropdown -->
        <form method="GET" action="{{ route('aset.index') }}" class="flex items-center gap-2">
            @if(request('tingkat'))
                <input type="hidden" name="tingkat" value="{{ request('tingkat') }}">
            @endif
            <select name="kategori" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:ring-1 focus:ring-teal-500 outline-none">
                <option value="">Semua Kategori</option>
                @foreach($kategoriList as $kat)
                    <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Aset Catalog Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($asets as $aset)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between space-y-4 hover:border-teal-300 transition group">
                <div>
                    <!-- Top badges -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                            {{ $aset->kode_aset }}
                        </span>
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $aset->tingkat === 'RW' ? 'bg-indigo-100 text-indigo-700' : 'bg-sky-100 text-sky-700' }}">
                                Tingkat {{ $aset->tingkat }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $aset->kondisi === 'Baik' ? 'bg-emerald-100 text-emerald-800' : ($aset->kondisi === 'Rusak Ringan' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ $aset->kondisi }}
                            </span>
                        </div>
                    </div>

                    <!-- Item Name & Category -->
                    <div class="mt-3">
                        <span class="text-[11px] font-semibold text-teal-600 block">{{ $aset->kategori }}</span>
                        <h3 class="text-base font-extrabold text-slate-900 group-hover:text-teal-700 transition">{{ $aset->nama_aset }}</h3>
                        @if($aset->keterangan)
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $aset->keterangan }}</p>
                        @endif
                    </div>

                    <!-- Availability & Storage Details -->
                    <div class="mt-4 pt-3 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Ketersediaan Unit:</span>
                            <span class="font-extrabold {{ $aset->jumlah_tersedia > 0 ? 'text-teal-600' : 'text-rose-600' }}">
                                {{ $aset->jumlah_tersedia }} <span class="text-slate-400 font-normal">/ {{ $aset->jumlah_total }} unit</span>
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-teal-500 h-1.5 rounded-full" style="width: {{ $aset->jumlah_total > 0 ? ($aset->jumlah_tersedia / $aset->jumlah_total) * 100 : 0 }}%"></div>
                        </div>

                        <div class="flex items-center gap-1.5 text-slate-500 pt-1">
                            <i class="fa-solid fa-location-dot text-slate-400 text-[11px]"></i>
                            <span class="truncate">Disimpan di: <strong>{{ $aset->lokasi_penyimpanan }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if($aset->jumlah_tersedia > 0)
                        <button type="button" @click="selectedAset = {{ json_encode($aset) }}; pinjamModal = true" class="flex-1 py-2 px-3 rounded-xl bg-teal-50 hover:bg-teal-600 text-teal-700 hover:text-white font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-hand-holding-hand"></i>
                            <span>Ajukan Pinjam</span>
                        </button>
                    @else
                        <span class="flex-1 py-2 px-3 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs text-center cursor-not-allowed">
                            Semua Sedang Dipakai
                        </span>
                    @endif

                    @if(in_array(Auth::user()->role, ['superadmin', 'rw']))
                        <form method="POST" action="{{ route('aset.destroy', $aset->id) }}" onsubmit="return confirm('Hapus aset ini dari daftar inventaris?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Aset">
                                <i class="fa-regular fa-trash-can text-sm"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl border border-slate-200 p-12 text-center">
                <i class="fa-solid fa-box-open text-4xl text-slate-300 mb-3"></i>
                <h4 class="text-base font-bold text-slate-800">Belum ada inventaris aset</h4>
                <p class="text-xs text-slate-500 mt-1">Data fasilitas bersama RW/RT belum ditambahkan atau filter tidak cocok.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $asets->links() }}
    </div>

    <!-- Section Riwayat Peminjaman Aset -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-teal-600"></i>
                    <span>Riwayat & Status Peminjaman Aset</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ Auth::user()->isWarga() ? 'Daftar permohonan peminjaman aset yang Anda ajukan' : 'Kelola persetujuan dan pengembalian aset warga' }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-3 px-3">Peminjam</th>
                        <th class="py-3 px-3">Nama Aset</th>
                        <th class="py-3 px-3">Jumlah</th>
                        <th class="py-3 px-3">Periode Pemakaian</th>
                        <th class="py-3 px-3">Keperluan</th>
                        <th class="py-3 px-3">Status</th>
                        @if(!Auth::user()->isWarga())
                            <th class="py-3 px-3 text-right">Aksi Tindak Lanjut</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($peminjamans as $pinjam)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-900 block">{{ $pinjam->user->name ?? 'Warga' }}</span>
                                <span class="text-[10px] text-slate-400">RT {{ $pinjam->user->rt ?? '-' }}/RW {{ $pinjam->user->rw ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-extrabold text-slate-800 block">{{ $pinjam->aset->nama_aset ?? '-' }}</span>
                                <span class="text-[10px] text-teal-600 font-mono">{{ $pinjam->aset->kode_aset ?? '' }}</span>
                            </td>
                            <td class="py-3 px-3 font-extrabold text-slate-900">
                                {{ $pinjam->jumlah_pinjam }} unit
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-medium text-slate-700 block">{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d M Y') }} s/d</span>
                                <span class="text-slate-500 font-medium">{{ \Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana)->format('d M Y') }}</span>
                            </td>
                            <td class="py-3 px-3 text-slate-600 max-w-xs truncate" title="{{ $pinjam->keperluan }}">
                                {{ $pinjam->keperluan }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $pinjam->status === 'disetujui' ? 'bg-blue-100 text-blue-800' : ($pinjam->status === 'dipinjam' ? 'bg-amber-100 text-amber-800' : ($pinjam->status === 'dikembalikan' ? 'bg-emerald-100 text-emerald-800' : ($pinjam->status === 'ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700'))) }}">
                                    {{ ucfirst($pinjam->status) }}
                                </span>
                            </td>
                            @if(!Auth::user()->isWarga())
                                <td class="py-3 px-3 text-right">
                                    <button type="button" @click="selectedPinjam = {{ json_encode($pinjam) }}; updateModal = true" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-teal-50 hover:text-teal-700 text-slate-700 text-xs font-bold transition">
                                        Update Status
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ !Auth::user()->isWarga() ? 7 : 6 }}" class="py-8 text-center text-slate-400">
                                Belum ada catatan peminjaman aset.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Ajukan Pinjam Aset -->
    <div x-show="pinjamModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl" @click.away="pinjamModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-hand-holding-hand"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Form Peminjaman Fasilitas</h3>
                        <p class="text-xs text-slate-400" x-text="selectedAset ? selectedAset.nama_aset : ''"></p>
                    </div>
                </div>
                <button type="button" @click="pinjamModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('aset.pinjam') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="aset_id" :value="selectedAset ? selectedAset.id : ''">

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Jumlah Unit Yang Dipinjam <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah_pinjam" min="1" :max="selectedAset ? selectedAset.jumlah_tersedia : 1" value="1" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    <p class="text-[11px] text-slate-400" x-text="'Maksimal unit tersedia: ' + (selectedAset ? selectedAset.jumlah_tersedia : 0) + ' unit'"></p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_pinjam" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Rencana Kembali <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_kembali_rencana" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+1 day')) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Keperluan & Alamat Penggunaan <span class="text-rose-500">*</span></label>
                    <textarea name="keperluan" rows="3" required placeholder="Contoh: Acara syukuran keluarga di Jl. Melati No. 4" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500 outline-none resize-none"></textarea>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 text-[11px] text-amber-800 border border-amber-100 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
                    <span>Peminjam wajib menjaga keutuhan, kebersihan, serta mengembalikan aset tepat waktu sesuai batas peminjaman.</span>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="pinjamModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20">
                        Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form Update Status Peminjaman (Pengurus) -->
    @if(!Auth::user()->isWarga())
        <div x-show="updateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl" @click.away="updateModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900">Perbarui Status Peminjaman</h3>
                    <button type="button" @click="updateModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form method="POST" :action="'/aset/peminjaman/' + (selectedPinjam ? selectedPinjam.id : '') + '/update-status'" class="space-y-4">
                    @csrf

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Pilih Status Baru <span class="text-rose-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none bg-white">
                            <option value="disetujui">Disetujui (Menunggu Pengambilan)</option>
                            <option value="dipinjam">Dipinjam (Barang Sedang Dibawa)</option>
                            <option value="dikembalikan">Dikembalikan (Stok Masuk Kembali)</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Catatan Petugas</label>
                        <textarea name="catatan" rows="3" placeholder="Contoh: Kondisi barang diperiksa lengkap, dikembalikan utuh." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs outline-none resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="updateModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
