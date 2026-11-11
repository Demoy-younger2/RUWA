<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Aspirasi;
use App\Models\BankSampah;
use App\Models\Kas;
use App\Models\Kegiatan;
use App\Models\KoperasiAnggota;
use App\Models\Pengaturan;
use App\Models\Posyandu;
use App\Models\Ronda;
use App\Models\Rukem;
use App\Models\Surat;
use App\Models\TransaksiBankSampah;
use App\Models\Umkm;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pengaturan = Pengaturan::first() ?? new Pengaturan();

        // 1. General Metrics
        $totalWarga = Warga::count();
        $totalKK = Warga::where('status_keluarga', 'Kepala Keluarga')->count();
        $totalUser = User::count();

        // 2. Kas Metrics
        $totalKasRW = Kas::where('tingkat', 'RW')->where('jenis', 'pemasukan')->sum('jumlah')
            - Kas::where('tingkat', 'RW')->where('jenis', 'pengeluaran')->sum('jumlah');

        $totalKasRT = Kas::where('tingkat', 'RT')->where('jenis', 'pemasukan')->sum('jumlah')
            - Kas::where('tingkat', 'RT')->where('jenis', 'pengeluaran')->sum('jumlah');

        // 3. Surat Metrics
        $suratMenungguRT = Surat::where('status', 'diajukan')->count();
        $suratMenungguRW = Surat::where('status', 'disetujui_rt')->count();
        $suratSelesai = Surat::whereIn('status', ['disetujui_rw', 'selesai'])->count();

        // 4. Other Modules
        $totalUmkm = Umkm::where('status', 'aktif')->count();
        $totalKegiatanMendatang = Kegiatan::where('status', 'akan_datang')->count();
        $totalAspirasiMenunggu = Aspirasi::where('status', 'menunggu')->count();
        $totalAset = Aset::sum('jumlah_total');
        $asetDipinjam = Aset::sum('jumlah_total') - Aset::sum('jumlah_tersedia');

        // Recent items
        $recentSurats = Surat::with(['user', 'warga'])->latest()->take(5)->get();
        $upcomingKegiatans = Kegiatan::where('tanggal_mulai', '>=', now()->subDays(1))
            ->orderBy('tanggal_mulai', 'asc')->take(4)->get();
        $recentAspirasis = Aspirasi::with('user')->latest()->take(4)->get();
        $todayRondas = Ronda::where('hari', now()->locale('id')->isoFormat('dddd'))
            ->orWhere('status_kehadiran', 'hadir')->latest()->take(6)->get();

        // Specific Citizen Metrics if role is warga
        $mySurats = [];
        $myBankSampahSaldo = 0;
        $myRondas = [];
        $myAspirasis = [];
        $myPinjamanAset = [];
        $myKoperasi = null;

        if ($user->isWarga()) {
            $mySurats = Surat::where('user_id', $user->id)->latest()->take(5)->get();
            $mySetor = TransaksiBankSampah::where('user_id', $user->id)->where('jenis_transaksi', 'setor')->sum('total_rupiah');
            $myTarik = TransaksiBankSampah::where('user_id', $user->id)->where('jenis_transaksi', 'tarik_saldo')->sum('total_rupiah');
            $myBankSampahSaldo = $mySetor - $myTarik;
            $myRondas = Ronda::where('user_id', $user->id)->orWhere('nama_petugas', 'like', '%' . $user->name . '%')->take(4)->get();
            $myAspirasis = Aspirasi::where('user_id', $user->id)->latest()->take(4)->get();
            $myKoperasi = KoperasiAnggota::with('transaksis')->where('user_id', $user->id)->first();
        }

        return view('dashboard.index', compact(
            'user',
            'pengaturan',
            'totalWarga',
            'totalKK',
            'totalUser',
            'totalKasRW',
            'totalKasRT',
            'suratMenungguRT',
            'suratMenungguRW',
            'suratSelesai',
            'totalUmkm',
            'totalKegiatanMendatang',
            'totalAspirasiMenunggu',
            'totalAset',
            'asetDipinjam',
            'recentSurats',
            'upcomingKegiatans',
            'recentAspirasis',
            'todayRondas',
            'mySurats',
            'myBankSampahSaldo',
            'myRondas',
            'myAspirasis',
            'myKoperasi'
        ));
    }
}
