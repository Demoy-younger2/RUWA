<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\KoperasiAnggota;
use App\Models\KoperasiTransaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KoperasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Cari data anggota user login jika warga
        $myAnggota = KoperasiAnggota::with('transaksis')->where('user_id', $user->id)->first();

        // Seluruh anggota (untuk pengurus)
        $semuaAnggota = KoperasiAnggota::with(['user', 'transaksis'])->paginate(10);

        // Seluruh transaksi
        $transaksis = KoperasiTransaksi::with('anggota.user')->latest('tanggal')->take(10)->get();

        $totalSimpananSemua = KoperasiAnggota::sum('total_simpanan');
        $totalPinjamanSemua = KoperasiAnggota::sum('total_pinjaman');
        $totalAnggotaAktif = KoperasiAnggota::where('status', 'aktif')->count();

        return view('koperasi.index', compact(
            'myAnggota',
            'semuaAnggota',
            'transaksis',
            'totalSimpananSemua',
            'totalPinjamanSemua',
            'totalAnggotaAktif'
        ));
    }

    public function daftarAnggota(Request $request)
    {
        $user = Auth::user();

        if (KoperasiAnggota::where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('warning', 'Anda sudah terdaftar sebagai anggota Koperasi.');
        }

        $urut = KoperasiAnggota::count() + 1;
        $noAnggota = sprintf('KOP-RW05-%03d', $urut);

        $anggota = KoperasiAnggota::create([
            'user_id' => $user->id,
            'no_anggota' => $noAnggota,
            'tanggal_daftar' => now(),
            'total_simpanan' => 100000, // Simpanan Pokok awal
            'total_pinjaman' => 0,
            'status' => 'aktif',
        ]);

        KoperasiTransaksi::create([
            'koperasi_anggota_id' => $anggota->id,
            'jenis' => 'simpanan_pokok',
            'nominal' => 100000,
            'tanggal' => now(),
            'keterangan' => 'Pembayaran Simpanan Pokok saat pendaftaran anggota koperasi baru',
            'status' => 'lunas',
        ]);

        ActivityLog::record('Koperasi', 'Daftar', 'Warga ' . $user->name . ' resmi mendaftar menjadi Anggota Koperasi (No: ' . $noAnggota . ') dengan simpanan pokok Rp 100.000.');

        return redirect()->route('koperasi.index')->with('success', 'Selamat! Anda resmi terdaftar sebagai anggota koperasi warga dengan nomor: ' . $noAnggota);
    }

    public function transaksi(Request $request)
    {
        $validated = $request->validate([
            'koperasi_anggota_id' => ['required', 'exists:koperasi_anggotas,id'],
            'jenis' => ['required', 'in:simpanan_pokok,simpanan_wajib,simpanan_sukarela,pinjaman,angsuran,penarikan'],
            'nominal' => ['required', 'numeric', 'min:5000'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $anggota = KoperasiAnggota::findOrFail($validated['koperasi_anggota_id']);

        if (in_array($validated['jenis'], ['simpanan_pokok', 'simpanan_wajib', 'simpanan_sukarela'])) {
            $anggota->increment('total_simpanan', $validated['nominal']);
        } elseif ($validated['jenis'] === 'penarikan') {
            if ($validated['nominal'] > $anggota->total_simpanan) {
                return redirect()->back()->with('error', 'Penarikan melebihi saldo simpanan.');
            }
            $anggota->decrement('total_simpanan', $validated['nominal']);
        } elseif ($validated['jenis'] === 'pinjaman') {
            $anggota->increment('total_pinjaman', $validated['nominal']);
        } elseif ($validated['jenis'] === 'angsuran') {
            $anggota->decrement('total_pinjaman', min($anggota->total_pinjaman, $validated['nominal']));
        }

        KoperasiTransaksi::create([
            'koperasi_anggota_id' => $anggota->id,
            'jenis' => $validated['jenis'],
            'nominal' => $validated['nominal'],
            'tanggal' => now(),
            'keterangan' => $validated['keterangan'] ?? ucfirst(str_replace('_', ' ', $validated['jenis'])),
            'status' => 'lunas',
        ]);

        ActivityLog::record('Koperasi', 'Transaksi', 'Transaksi ' . ucwords(str_replace('_', ' ', $validated['jenis'])) . ' anggota ' . ($anggota->user->name ?? 'Warga') . ' (' . $anggota->no_anggota . ') sebesar Rp ' . number_format($validated['nominal'], 0, ',', '.') . '.');

        return redirect()->route('koperasi.index')->with('success', 'Transaksi koperasi berhasil diproses.');
    }
}
