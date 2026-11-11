<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Pengaturan;
use App\Models\Surat;
use App\Models\Umkm;
use App\Models\Warga;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::first() ?? new Pengaturan();
        $totalWarga = Warga::count();
        $totalKK = Warga::where('status_keluarga', 'Kepala Keluarga')->count();
        $totalRT = Warga::distinct()->pluck('rt')->filter()->count() ?: 3;
        $totalSuratSelesai = Surat::whereIn('status', ['disetujui_rw', 'selesai'])->count();

        $umkms = Umkm::where('status', 'aktif')->take(6)->get();
        $kegiatans = Kegiatan::where('status', 'akan_datang')
            ->orderBy('tanggal_mulai', 'asc')->take(3)->get();

        return view('welcome', compact(
            'pengaturan',
            'totalWarga',
            'totalKK',
            'totalRT',
            'totalSuratSelesai',
            'umkms',
            'kegiatans'
        ));
    }

    public function checkSurat(Request $request)
    {
        $request->validate([
            'keyword' => ['required', 'string', 'min:3'],
        ]);

        $keyword = $request->keyword;
        $surat = Surat::where('nomor_surat', $keyword)
            ->orWhereHas('user', function ($q) use ($keyword) {
                $q->where('nik', $keyword);
            })
            ->with(['user', 'warga'])
            ->first();

        return view('welcome_check_surat', compact('surat', 'keyword'));
    }
}
