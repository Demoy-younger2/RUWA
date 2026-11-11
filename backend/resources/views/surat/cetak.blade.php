<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat - {{ $surat->nomor_surat ?? 'Surat Pengantar' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; }
        @media print {
            .no-print { display: none !important; }
            body { background-color: #fff; }
            .print-container { box-shadow: none !important; border: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 py-8 px-4">

    <!-- Action Bar (Hidden when printing) -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print font-sans">
        <a href="{{ route('surat.show', $surat->id) }}" class="flex items-center gap-2 text-xs font-bold text-slate-600 bg-white px-4 py-2 rounded-xl shadow-xs border border-slate-200 hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Sistem
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="flex items-center gap-2 text-xs font-bold text-white bg-emerald-600 px-5 py-2.5 rounded-xl shadow-md hover:bg-emerald-700 transition">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Official A4 Paper Container -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 shadow-xl border border-slate-200 print-container">
        
        <!-- Kop Surat Resmi Lingkungan RW -->
        <div class="text-center border-b-4 border-double border-black pb-3 mb-6 relative">
            <h3 class="text-sm font-bold uppercase tracking-wider">Pemerintah {{ $pengaturan->nama_kota ?? 'Kabupaten Sukabumi' }}</h3>
            <h3 class="text-sm font-bold uppercase tracking-wider">Kecamatan {{ $pengaturan->nama_kecamatan ?? 'Cibadak' }} &bull; {{ $pengaturan->nama_kelurahan ?? 'Kelurahan Sukamaju' }}</h3>
            <h1 class="text-xl font-extrabold uppercase tracking-wide mt-1">RUKUN WARGA {{ $pengaturan->nama_rw ?? '05' }}</h1>
            <p class="text-[11px] italic mt-0.5">{{ $pengaturan->alamat_sekretariat ?? 'Sekretariat: Balai Warga RW 05, Sukamaju' }} &bull; Telp: {{ $pengaturan->kontak_rw ?? '0812-3456-7890' }}</p>
        </div>

        <!-- Judul & Nomor Surat -->
        <div class="text-center mb-6">
            <h2 class="text-base font-bold uppercase underline tracking-wide">{{ strtoupper($surat->jenis_surat) }}</h2>
            <p class="text-xs font-mono font-bold mt-1">Nomor: {{ $surat->nomor_surat ?? '470/.../SRW/' . date('Y') }}</p>
        </div>

        <!-- Isi Surat -->
        <div class="text-[13px] leading-relaxed space-y-4">
            <p>
                Yang bertanda tangan di bawah ini Ketua Rukun Tetangga (RT) {{ $surat->user->rt ?? '01' }} dan Ketua Rukun Warga (RW) {{ $surat->user->rw ?? '05' }} Kelurahan {{ $pengaturan->nama_kelurahan ?? 'Sukamaju' }}, Kecamatan {{ $pengaturan->nama_kecamatan ?? 'Cibadak' }}, {{ $pengaturan->nama_kota ?? 'Kabupaten Sukabumi' }}, dengan ini menerangkan bahwa:
            </p>

            <!-- Tabel Identitas -->
            <table class="w-full my-2 text-[13px] ml-4">
                <tr>
                    <td class="w-48 py-1">Nama Lengkap</td>
                    <td class="w-4 py-1">:</td>
                    <td class="font-bold py-1">{{ strtoupper($surat->user->name ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="py-1">NIK</td>
                    <td class="py-1">:</td>
                    <td class="font-mono py-1">{{ $surat->user->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1">No. Kartu Keluarga</td>
                    <td class="py-1">:</td>
                    <td class="font-mono py-1">{{ $surat->warga->no_kk ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1">Tempat / Tanggal Lahir</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ $surat->warga->tempat_lahir ?? 'Sukabumi' }}, {{ $surat->warga->tanggal_lahir ? $surat->warga->tanggal_lahir->format('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1">Jenis Kelamin</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ ($surat->warga->jenis_kelamin ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td class="py-1">Agama</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ $surat->warga->agama ?? 'Islam' }}</td>
                </tr>
                <tr>
                    <td class="py-1">Pekerjaan</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ $surat->warga->pekerjaan ?? 'Karyawan Swasta' }}</td>
                </tr>
                <tr>
                    <td class="py-1">Alamat Domisili</td>
                    <td class="py-1">:</td>
                    <td class="py-1">{{ $surat->warga->alamat ?? 'Jl. Melati RT ' . ($surat->user->rt ?? '01') . ' RW ' . ($surat->user->rw ?? '05') }}</td>
                </tr>
            </table>

            <p class="mt-3">
                Orang tersebut di atas adalah benar-benar warga yang bertempat tinggal menetap di wilayah kami dan tercatat berkelakuan baik dalam kehidupan bermasyarakat.
            </p>

            <p>
                Surat keterangan/pengantar ini diberikan kepada yang bersangkutan untuk keperluan:
            </p>

            <div class="p-3 bg-slate-50 border border-slate-300 font-bold italic text-center rounded-sm">
                "{{ $surat->keperluan }}"
            </div>

            @if($surat->keterangan_tambahan)
                <p>
                    Keterangan lain: {{ $surat->keterangan_tambahan }}
                </p>
            @endif

            <p class="mt-3">
                Demikian surat pengantar ini dibuat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya oleh pihak yang berkepentingan.
            </p>
        </div>

        <!-- Tanda Tangan Pejabat Lingkungan RT & RW -->
        <div class="mt-10 grid grid-cols-2 text-center text-xs">
            <!-- RT -->
            <div class="space-y-1">
                <p>Mengetahui,</p>
                <p class="font-bold">Ketua RT {{ $surat->user->rt ?? '01' }}</p>
                
                <!-- Digital Verification Badge -->
                <div class="py-4 font-sans">
                    <span class="inline-block px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-300 rounded text-[10px] font-bold">
                        <i class="fa-solid fa-check-circle text-emerald-600"></i> TERVERIFIKASI DIGITAL
                        <br><span class="text-[9px] font-mono font-normal">{{ $surat->tanggal_disetujui_rt ? $surat->tanggal_disetujui_rt->format('d/m/Y H:i') : date('d/m/Y') }}</span>
                    </span>
                </div>

                <p class="font-bold underline text-sm mt-2">{{ $surat->disetujui_rt_oleh ?? 'Agus Budiman' }}</p>
            </div>

            <!-- RW -->
            <div class="space-y-1">
                <p>{{ $pengaturan->nama_kelurahan ?? 'Sukamaju' }}, {{ $surat->tanggal_disetujui_rw ? $surat->tanggal_disetujui_rw->format('d F Y') : now()->format('d F Y') }}</p>
                <p class="font-bold">Ketua RW {{ $pengaturan->nama_rw ?? '05' }}</p>
                
                <!-- Digital Verification Stamp -->
                <div class="py-4 font-sans">
                    <span class="inline-block px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-300 rounded text-[10px] font-bold">
                        <i class="fa-solid fa-stamp text-emerald-600"></i> SAH DITANDATANGANI
                        <br><span class="text-[9px] font-mono font-normal">{{ $surat->nomor_surat ?? '470/SRW' }}</span>
                    </span>
                </div>

                <p class="font-bold underline text-sm mt-2">{{ $pengaturan->nama_ketua_rw ?? 'H. Suryana, S.Pd.' }}</p>
            </div>
        </div>

        <!-- QR Code Verification Stamp at bottom -->
        <div class="mt-12 pt-4 border-t border-slate-300 flex items-center justify-between text-[10px] text-slate-500 font-sans">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 border border-slate-400 p-0.5 flex items-center justify-center font-mono font-bold text-[8px] text-center">
                    QR-VERIF
                </div>
                <div>
                    <span class="font-bold block text-slate-700">Validasi Digital SiRW-Digital</span>
                    <span>Dokumen resmi diterbitkan melalui sistem portal digital RW 05.</span>
                </div>
            </div>
            <div class="text-right font-mono">
                ID Dokumen: {{ strtoupper(md5($surat->id . ($surat->nomor_surat ?? ''))) }}
            </div>
        </div>

    </div>

</body>
</html>
