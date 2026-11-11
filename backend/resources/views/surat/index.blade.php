@extends('layouts.app', ['title' => 'Surat Menyurat Digital'])

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-envelope-open-text text-blue-600"></i>
                <span>Surat Menyurat Digital</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Layanan permohonan surat pengantar SKCK, domisili, izin usaha, SKTM, dan izin kegiatan dengan alur verifikasi RT & RW.
            </p>
        </div>

        <a href="{{ route('surat.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Buat Permohonan Surat</span>
        </a>
    </div>

    <!-- Filter Tab Bar -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap gap-2 text-xs font-semibold">
        <a href="{{ route('surat.index') }}" class="px-3 py-1.5 rounded-xl transition {{ !request('status') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua ({{ \App\Models\Surat::count() }})
        </a>
        <a href="{{ route('surat.index', ['status' => 'diajukan']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'diajukan' ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Menunggu Verifikasi RT
        </a>
        <a href="{{ route('surat.index', ['status' => 'disetujui_rt']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'disetujui_rt' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Menunggu Persetujuan RW
        </a>
        <a href="{{ route('surat.index', ['status' => 'selesai']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'selesai' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Selesai / Terbit
        </a>
        <a href="{{ route('surat.index', ['status' => 'ditolak']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'ditolak' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Ditolak
        </a>
    </div>

    <!-- Letters List Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Nomor & Tanggal</th>
                        <th class="py-3 px-4">Jenis Surat</th>
                        <th class="py-3 px-4">Nama Pemohon</th>
                        <th class="py-3 px-4">Keperluan</th>
                        <th class="py-3 px-4">Status Alur</th>
                        <th class="py-3 px-4 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($surats as $surat)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono">
                                <span class="font-bold text-slate-900 block text-xs">{{ $surat->nomor_surat ?? 'Menunggu Persetujuan' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $surat->tanggal_diajukan->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                {{ $surat->jenis_surat }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $surat->user->name ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400">RT {{ $surat->user->rt ?? '01' }} / RW {{ $surat->user->rw ?? '05' }}</span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs text-slate-600 truncate">
                                {{ $surat->keperluan }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $surat->status_badge }}">
                                    {{ $surat->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('surat.show', $surat->id) }}" class="px-3 py-1.5 rounded-xl font-bold text-xs bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                        Detail
                                    </a>

                                    @if($surat->status === 'selesai')
                                        <a href="{{ route('surat.cetak', $surat->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl font-bold text-xs bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition flex items-center gap-1">
                                            <i class="fa-solid fa-print"></i> Cetak
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada permohonan surat dalam kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($surats->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $surats->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
