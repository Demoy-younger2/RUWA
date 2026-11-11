<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Kas::with('user');

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        } elseif (in_array($user->role, ['rt', 'sekretaris_rt', 'bendahara_rt'])) {
            $query->where('tingkat', 'RT')->where('rt', $user->rt ?? '01');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $kasList = $query->latest('tanggal')->latest('id')->paginate(10)->withQueryString();

        $totalPemasukan = Kas::when($request->filled('tingkat'), fn($q) => $q->where('tingkat', $request->tingkat))
            ->where('jenis', 'pemasukan')->sum('jumlah');

        $totalPengeluaran = Kas::when($request->filled('tingkat'), fn($q) => $q->where('tingkat', $request->tingkat))
            ->where('jenis', 'pengeluaran')->sum('jumlah');

        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        $kategoriList = [
            'Iuran Warga Tingkat RW',
            'Iuran Keamanan & Kebersihan',
            'Kas RW',
            'Kas RT',
            'Dana Sosial & Sumbangan',
            'Operasional Ronda',
            'Pemeliharaan Lampu & Sarana',
            'Lain-lain',
        ];

        return view('kas.index', compact('kasList', 'totalPemasukan', 'totalPengeluaran', 'saldoAkhir', 'kategoriList'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt'])) {
            return redirect()->route('kas.index')->with('error', 'Hanya bendahara/pengurus yang dapat mencatat transaksi kas.');
        }

        $kategoriList = [
            'Iuran Warga Tingkat RW',
            'Iuran Keamanan & Kebersihan',
            'Kas RW',
            'Kas RT',
            'Dana Sosial & Sumbangan',
            'Operasional Ronda',
            'Pemeliharaan Lampu & Sarana',
            'Lain-lain',
        ];

        return view('kas.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'tingkat' => ['required', 'in:RW,RT'],
            'rt' => ['nullable', 'string', 'max:5'],
            'kategori' => ['required', 'string'],
            'jenis' => ['required', 'in:pemasukan,pengeluaran'],
            'jumlah' => ['required', 'numeric', 'min:1000'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string'],
        ]);

        Kas::create([
            'tingkat' => $validated['tingkat'],
            'rt' => $validated['rt'] ?? ($user->rt ?? '01'),
            'rw' => $user->rw ?? '05',
            'user_id' => $user->id,
            'kategori' => $validated['kategori'],
            'jenis' => $validated['jenis'],
            'jumlah' => $validated['jumlah'],
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'],
        ]);

        ActivityLog::record('Kas', 'Transaksi', 'Mencatat ' . strtoupper($validated['jenis']) . ' kas ' . $validated['tingkat'] . ' sebesar Rp ' . number_format($validated['jumlah'], 0, ',', '.') . ' (' . $validated['kategori'] . ' - ' . $validated['keterangan'] . ').');

        return redirect()->route('kas.index')->with('success', 'Transaksi kas berhasil dicatat.');
    }

    public function destroy(Kas $ka)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        ActivityLog::record('Kas', 'Hapus', 'Menghapus catatan kas ' . $ka->tingkat . ' senilai Rp ' . number_format($ka->jumlah, 0, ',', '.') . ' (' . $ka->keterangan . ').');

        $ka->delete();
        return redirect()->route('kas.index')->with('success', 'Catatan kas berhasil dihapus.');
    }
}
