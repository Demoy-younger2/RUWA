@extends('layouts.app', ['title' => 'Rincian Surat: ' . $surat->jenis_surat])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Rincian Permohonan Surat</h1>
            <p class="text-xs text-slate-500 mt-1">Status tahapan verifikasi pengantar RT dan penerbitan RW.</p>
        </div>
        <div class="flex items-center gap-2">
            @if($surat->status === 'selesai')
                <a href="{{ route('surat.cetak', $surat->id) }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Surat Resmi</span>
                </a>
            @endif
            <a href="{{ route('surat.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Card Detail -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Top Status Banner -->
        <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block">Jenis Permohonan</span>
                <h2 class="text-lg font-bold mt-0.5">{{ $surat->jenis_surat }}</h2>
                <p class="text-xs text-slate-300 font-mono mt-1">Nomor Surat: {{ $surat->nomor_surat ?? 'Belum Terbit (Menunggu Persetujuan RW)' }}</p>
            </div>
            <div>
                <span class="px-3 py-1.5 rounded-full text-xs font-bold border {{ $surat->status_badge }}">
                    {{ $surat->status_label }}
                </span>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6 text-xs">
            <!-- Identitas Pemohon -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100">
                <div class="space-y-3">
                    <h3 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider text-slate-400">Data Pemohon</h3>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Lengkap</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $surat->user->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">NIK (KTP)</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $surat->user->nik ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Wilayah RT / RW</span>
                        <span class="font-semibold text-slate-800">RT {{ $surat->user->rt ?? '01' }} / RW {{ $surat->user->rw ?? '05' }}</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider text-slate-400">Rincian Keperluan</h3>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Keperluan Pembuatan Surat</span>
                        <p class="font-bold text-slate-900 mt-0.5 leading-relaxed">{{ $surat->keperluan }}</p>
                    </div>
                    @if($surat->keterangan_tambahan)
                        <div>
                            <span class="text-slate-400 block text-[11px]">Keterangan Tambahan</span>
                            <p class="text-slate-700 mt-0.5">{{ $surat->keterangan_tambahan }}</p>
                        </div>
                    @endif
                    <div>
                        <span class="text-slate-400 block text-[11px]">Tanggal Pengajuan</span>
                        <span class="text-slate-700">{{ $surat->tanggal_diajukan->format('d F Y, H:i') }} WIB</span>
                    </div>
                </div>
            </div>

            <!-- Approval Progress Track Timeline -->
            <div>
                <h3 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider text-slate-400 mb-4">Riwayat Persetujuan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Step 1: Diajukan -->
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                        <div class="flex items-center gap-2 text-emerald-800 font-bold mb-1">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>1. Diajukan Warga</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">{{ $surat->tanggal_diajukan->format('d M Y, H:i') }}</p>
                    </div>

                    <!-- Step 2: RT -->
                    <div class="p-4 rounded-2xl {{ $surat->tanggal_disetujui_rt ? 'bg-emerald-50 border border-emerald-100 text-emerald-800' : 'bg-slate-50 border border-slate-200 text-slate-500' }}">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <i class="fa-solid {{ $surat->tanggal_disetujui_rt ? 'fa-circle-check text-emerald-600' : 'fa-hourglass text-slate-400' }}"></i>
                            <span>2. Verifikasi RT</span>
                        </div>
                        @if($surat->tanggal_disetujui_rt)
                            <p class="text-[11px] text-emerald-700 font-semibold">{{ $surat->tanggal_disetujui_rt->format('d M Y, H:i') }}</p>
                            <p class="text-[10px] text-emerald-600 mt-0.5 italic">Oleh: {{ $surat->disetujui_rt_oleh }}</p>
                            @if($surat->catatan_rt)
                                <p class="text-[10px] text-slate-600 mt-1">Catatan: {{ $surat->catatan_rt }}</p>
                            @endif
                        @else
                            <p class="text-[11px] text-slate-400">Menunggu persetujuan RT</p>
                        @endif
                    </div>

                    <!-- Step 3: RW -->
                    <div class="p-4 rounded-2xl {{ $surat->tanggal_disetujui_rw ? 'bg-emerald-50 border border-emerald-100 text-emerald-800' : 'bg-slate-50 border border-slate-200 text-slate-500' }}">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <i class="fa-solid {{ $surat->tanggal_disetujui_rw ? 'fa-circle-check text-emerald-600' : 'fa-hourglass text-slate-400' }}"></i>
                            <span>3. Persetujuan RW</span>
                        </div>
                        @if($surat->tanggal_disetujui_rw)
                            <p class="text-[11px] text-emerald-700 font-semibold">{{ $surat->tanggal_disetujui_rw->format('d M Y, H:i') }}</p>
                            <p class="text-[10px] text-emerald-600 mt-0.5 italic">Oleh: {{ $surat->disetujui_rw_oleh }}</p>
                            @if($surat->catatan_rw)
                                <p class="text-[10px] text-slate-600 mt-1">Catatan: {{ $surat->catatan_rw }}</p>
                            @endif
                        @else
                            <p class="text-[11px] text-slate-400">Menunggu persetujuan RW</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Role Action Form Box (RT & RW Approval Panels) -->
            @php $userRole = Auth::user()->role; @endphp

            <!-- Tahap 1: Persetujuan Tingkat RT -->
            @if(in_array($userRole, ['superadmin', 'rt', 'sekretaris_rt']) && $surat->status === 'diajukan')
                <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 space-y-3">
                    <div class="flex items-center gap-2 text-amber-900 font-bold">
                        <i class="fa-solid fa-stamp"></i>
                        <span>Aksi Verifikasi Tingkat RT ({{ Auth::user()->role_title }})</span>
                    </div>
                    <form method="POST" action="{{ route('surat.approve_rt', $surat->id) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-slate-700 font-semibold mb-1">Catatan Verifikasi RT (Opsional)</label>
                            <input type="text" name="catatan_rt" value="Data warga telah diverifikasi dan memenuhi syarat." class="w-full px-3 py-2 text-xs rounded-xl border border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                                <i class="fa-solid fa-check mr-1"></i> Setujui di Tingkat RT & Teruskan ke RW
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Tahap 2: Persetujuan Tingkat RW -->
            @if(in_array($userRole, ['superadmin', 'rw', 'sekretaris_rw']) && in_array($surat->status, ['disetujui_rt', 'diajukan']))
                <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-3">
                    <div class="flex items-center gap-2 text-emerald-900 font-bold">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>Penerbitan Surat Resmi Tingkat RW ({{ Auth::user()->role_title }})</span>
                    </div>
                    <form method="POST" action="{{ route('surat.approve_rw', $surat->id) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-slate-700 font-semibold mb-1">Catatan Pengesahan RW (Opsional)</label>
                            <input type="text" name="catatan_rw" value="Surat pengantar telah disetujui dan ditandatangani secara digital." class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                                <i class="fa-solid fa-file-signature mr-1"></i> Setujui RW & Terbitkan Nomor Surat Resmi
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Form Penolakan jika data tidak valid -->
            @if(in_array($userRole, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt']) && in_array($surat->status, ['diajukan', 'disetujui_rt']))
                <div class="pt-2" x-data="{ openReject: false }">
                    <button type="button" @click="openReject = !openReject" class="text-rose-600 hover:text-rose-800 font-bold text-[11px] underline">
                        Tolak permohonan surat ini?
                    </button>
                    <div x-show="openReject" class="mt-3 p-4 rounded-2xl bg-rose-50 border border-rose-200 space-y-2">
                        <form method="POST" action="{{ route('surat.reject', $surat->id) }}" class="space-y-2">
                            @csrf
                            <label class="block font-bold text-rose-900">Alasan Penolakan Permohonan</label>
                            <input type="text" name="alasan_penolakan" required placeholder="Contoh: Berkas lampiran belum lengkap / NIK tidak sesuai KTP" class="w-full px-3 py-2 text-xs rounded-xl border border-rose-300 focus:outline-none focus:ring-2 focus:ring-rose-500 bg-white">
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700">
                                Konfirmasi Tolak Surat
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
