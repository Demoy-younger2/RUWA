<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BankSampah;
use App\Models\TransaksiBankSampah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankSampahController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $kategoriList = BankSampah::all();

        $query = TransaksiBankSampah::with(['user', 'bankSampah']);

        if ($user->isWarga()) {
            $query->where('user_id', $user->id);
        }

        $transaksis = $query->latest('tanggal')->latest('id')->paginate(10)->withQueryString();

        // Hitung Saldo Warga Aktif
        $mySetor = TransaksiBankSampah::where('user_id', $user->id)->where('jenis_transaksi', 'setor')->sum('total_rupiah');
        $myTarik = TransaksiBankSampah::where('user_id', $user->id)->where('jenis_transaksi', 'tarik_saldo')->sum('total_rupiah');
        $mySaldo = $mySetor - $myTarik;

        // Total Lingkungan
        $totalSampahTerkumpul = TransaksiBankSampah::where('jenis_transaksi', 'setor')->sum('berat');
        $totalPerputaranRupiah = TransaksiBankSampah::where('jenis_transaksi', 'setor')->sum('total_rupiah');

        $wargaUsers = User::where('role', 'warga')->get();

        return view('bank_sampah.index', compact(
            'kategoriList',
            'transaksis',
            'mySaldo',
            'totalSampahTerkumpul',
            'totalPerputaranRupiah',
            'wargaUsers'
        ));
    }

    public function setor(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'bank_sampah_id' => ['required', 'exists:bank_sampahs,id'],
            'berat' => ['required', 'numeric', 'min:0.1'],
            'catatan' => ['nullable', 'string'],
        ]);

        $currentUser = Auth::user();
        $targetUserId = $currentUser->isWarga() ? $currentUser->id : ($validated['user_id'] ?? $currentUser->id);

        $kategori = BankSampah::findOrFail($validated['bank_sampah_id']);
        $totalRupiah = $validated['berat'] * $kategori->harga_per_kg;

        $kodeTrx = 'TRX-BS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        TransaksiBankSampah::create([
            'kode_transaksi' => $kodeTrx,
            'user_id' => $targetUserId,
            'bank_sampah_id' => $kategori->id,
            'jenis_transaksi' => 'setor',
            'berat' => $validated['berat'],
            'total_rupiah' => $totalRupiah,
            'tanggal' => now(),
            'status' => 'selesai',
            'catatan' => $validated['catatan'] ?? 'Penyetoran sampah daur ulang: ' . $kategori->nama_kategori,
        ]);

        ActivityLog::record('Bank Sampah', 'Setor', 'Setoran sampah daur ulang ' . $kategori->nama_kategori . ' seberat ' . $validated['berat'] . ' Kg (Nilai: Rp ' . number_format($totalRupiah, 0, ',', '.') . ').');

        return redirect()->route('bank-sampah.index')->with('success', 'Setoran sampah berhasil dicatat! Saldo bertambah Rp ' . number_format($totalRupiah, 0, ',', '.'));
    }

    public function tarikSaldo(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'nominal' => ['required', 'numeric', 'min:10000'],
            'catatan' => ['nullable', 'string'],
        ]);

        // Cek saldo cukup
        $mySetor = TransaksiBankSampah::where('user_id', $user->id)->where('jenis_transaksi', 'setor')->sum('total_rupiah');
        $myTarik = TransaksiBankSampah::where('user_id', $user->id)->where('jenis_transaksi', 'tarik_saldo')->sum('total_rupiah');
        $currentSaldo = $mySetor - $myTarik;

        if ($validated['nominal'] > $currentSaldo) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi. Saldo Anda saat ini: Rp ' . number_format($currentSaldo, 0, ',', '.'));
        }

        $kodeTrx = 'TRX-WD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        TransaksiBankSampah::create([
            'kode_transaksi' => $kodeTrx,
            'user_id' => $user->id,
            'jenis_transaksi' => 'tarik_saldo',
            'berat' => 0,
            'total_rupiah' => $validated['nominal'],
            'tanggal' => now(),
            'status' => 'selesai',
            'catatan' => $validated['catatan'] ?? 'Penarikan saldo tunai Bank Sampah',
        ]);

        ActivityLog::record('Bank Sampah', 'Transaksi', 'Penarikan saldo tunai bank sampah oleh ' . $user->name . ' sebesar Rp ' . number_format($validated['nominal'], 0, ',', '.') . '.');

        return redirect()->route('bank-sampah.index')->with('success', 'Penarikan saldo sebesar Rp ' . number_format($validated['nominal'], 0, ',', '.') . ' berhasil dicatat.');
    }

    public function storeKategori(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'bendahara_rw'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string'],
            'harga_per_kg' => ['required', 'numeric', 'min:100'],
            'satuan' => ['required', 'string'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        BankSampah::create($validated);

        return redirect()->route('bank-sampah.index')->with('success', 'Kategori sampah baru berhasil ditambahkan.');
    }
}
