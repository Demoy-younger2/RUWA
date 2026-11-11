<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pelacakan Surat - SiRW Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-full flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-xl mx-auto w-full">
        <div class="text-center mb-6">
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-600 transition mb-3">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Halaman Depan</span>
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900">Hasil Pelacakan Surat</h1>
            <p class="text-xs text-slate-500 mt-1">Kata kunci pencarian: <span class="font-mono font-bold text-slate-700 bg-white px-2 py-0.5 rounded-md">{{ $keyword }}</span></p>
        </div>

        @if($surat)
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
                <!-- Status Header Banner -->
                <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block">Status Pengajuan</span>
                        <h2 class="text-lg font-bold mt-0.5">{{ $surat->status_label }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl {{ $surat->status === 'selesai' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-400/30' : 'bg-amber-500/20 text-amber-400 border border-amber-400/30' }}">
                        @if($surat->status === 'selesai')
                            <i class="fa-solid fa-circle-check"></i>
                        @elseif($surat->status === 'ditolak')
                            <i class="fa-solid fa-circle-xmark"></i>
                        @else
                            <i class="fa-solid fa-clock-rotate-left animate-spin"></i>
                        @endif
                    </div>
                </div>

                <!-- Detail Body -->
                <div class="p-6 space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-slate-400 text-[10px] block font-semibold">Nomor Surat Resmi</span>
                            <span class="font-mono font-bold text-slate-900 text-sm">{{ $surat->nomor_surat ?? 'Dalam Proses Penerbitan' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block font-semibold">Jenis Surat</span>
                            <span class="font-bold text-slate-900">{{ $surat->jenis_surat }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-slate-400 text-[10px] block font-semibold">Nama Pemohon</span>
                            <span class="font-bold text-slate-800">{{ $surat->user->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block font-semibold">Domisili RT / RW</span>
                            <span class="font-bold text-slate-800">RT {{ $surat->user->rt ?? '01' }} / RW {{ $surat->user->rw ?? '05' }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 text-[10px] block font-semibold">Keperluan Pengajuan</span>
                        <p class="font-medium text-slate-700 mt-1 leading-relaxed">{{ $surat->keperluan }}</p>
                    </div>

                    <!-- Timeline Tahapan -->
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <span class="text-slate-400 text-[10px] block font-bold uppercase tracking-wider mb-3">Kronologi Verifikasi</span>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">Diajukan oleh Warga</p>
                                    <p class="text-[11px] text-slate-400">{{ $surat->tanggal_diajukan->format('d M Y, H:i') }} WIB</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full {{ $surat->tanggal_disetujui_rt ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-400' }} flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                                    <i class="fa-solid {{ $surat->tanggal_disetujui_rt ? 'fa-check' : 'fa-hourglass' }}"></i>
                                </div>
                                <div>
                                    <p class="font-bold {{ $surat->tanggal_disetujui_rt ? 'text-slate-800' : 'text-slate-400' }}">Verifikasi Pengurus RT</p>
                                    @if($surat->tanggal_disetujui_rt)
                                        <p class="text-[11px] text-emerald-600 font-semibold">Disetujui: {{ $surat->tanggal_disetujui_rt->format('d M Y, H:i') }} WIB</p>
                                        @if($surat->catatan_rt)
                                            <p class="text-[11px] text-slate-500 italic mt-0.5">"{{ $surat->catatan_rt }}"</p>
                                        @endif
                                    @else
                                        <p class="text-[11px] text-slate-400">Menunggu verifikasi Ketua / Sekretaris RT</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full {{ $surat->tanggal_disetujui_rw ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-400' }} flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                                    <i class="fa-solid {{ $surat->tanggal_disetujui_rw ? 'fa-check' : 'fa-hourglass' }}"></i>
                                </div>
                                <div>
                                    <p class="font-bold {{ $surat->tanggal_disetujui_rw ? 'text-slate-800' : 'text-slate-400' }}">Persetujuan Akhir Ketua RW</p>
                                    @if($surat->tanggal_disetujui_rw)
                                        <p class="text-[11px] text-emerald-600 font-semibold">Disetujui: {{ $surat->tanggal_disetujui_rw->format('d M Y, H:i') }} WIB</p>
                                    @else
                                        <p class="text-[11px] text-slate-400">Menunggu tanda tangan digital Ketua RW</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($surat->status === 'selesai')
                        <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                            <a href="{{ route('surat.cetak', $surat->id) }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                                <i class="fa-solid fa-print"></i>
                                <span>Lihat & Cetak Surat Resmi</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white rounded-3xl p-8 text-center shadow-lg border border-slate-200">
                <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Surat Tidak Ditemukan</h3>
                <p class="text-xs text-slate-500 mt-2 max-w-sm mx-auto">
                    Data pengajuan surat dengan nomor atau NIK "<strong class="text-slate-700">{{ $keyword }}</strong>" tidak ditemukan dalam database. Pastikan nomor surat atau NIK yang Anda masukkan sudah sesuai.
                </p>
                <div class="mt-6">
                    <a href="{{ route('welcome') }}#cek-surat" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Cari Ulang</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

</body>
</html>
