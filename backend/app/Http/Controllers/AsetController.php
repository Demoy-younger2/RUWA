<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Aset;
use App\Models\PeminjamanAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Aset::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        $asets = $query->paginate(9)->withQueryString();

        // Peminjaman list
        $peminjamanQuery = PeminjamanAset::with(['aset', 'user']);
        if ($user->isWarga()) {
            $peminjamanQuery->where('user_id', $user->id);
        }
        $peminjamans = $peminjamanQuery->latest()->take(10)->get();

        $kategoriList = [
            'Tenda',
            'Meja & Kursi',
            'Sound System',
            'Kebersihan & Taman',
            'Keamanan & Ronda',
            'Elektronik & Proyektor',
            'Lain-lain',
        ];

        return view('aset.index', compact('asets', 'peminjamans', 'kategoriList'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt'])) {
            return redirect()->route('aset.index')->with('error', 'Hanya pengurus yang dapat menambah inventaris aset.');
        }

        $kategoriList = [
            'Tenda',
            'Meja & Kursi',
            'Sound System',
            'Kebersihan & Taman',
            'Keamanan & Ronda',
            'Elektronik & Proyektor',
            'Lain-lain',
        ];

        return view('aset.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'nama_aset' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string'],
            'tingkat' => ['required', 'in:RW,RT'],
            'jumlah_total' => ['required', 'integer', 'min:1'],
            'kondisi' => ['required', 'in:Baik,Rusak Ringan,Rusak Berat'],
            'lokasi_penyimpanan' => ['required', 'string'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $urut = Aset::count() + 1;
        $kodeAset = sprintf('AST-%s-%03d', $validated['tingkat'], $urut);

        Aset::create(array_merge($validated, [
            'kode_aset' => $kodeAset,
            'rt' => $user->rt ?? '01',
            'rw' => $user->rw ?? '05',
            'jumlah_tersedia' => $validated['jumlah_total'],
        ]));

        ActivityLog::record('Aset', 'Tambah', 'Menambahkan aset baru: ' . $validated['nama_aset'] . ' (' . $validated['jumlah_total'] . ' unit, Tingkat ' . $validated['tingkat'] . ').');

        return redirect()->route('aset.index')->with('success', 'Aset baru berhasil dicatatkan dalam inventaris.');
    }

    public function pinjam(Request $request)
    {
        $validated = $request->validate([
            'aset_id' => ['required', 'exists:asets,id'],
            'jumlah_pinjam' => ['required', 'integer', 'min:1'],
            'tanggal_pinjam' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'keperluan' => ['required', 'string'],
        ]);

        $aset = Aset::findOrFail($validated['aset_id']);

        if ($validated['jumlah_pinjam'] > $aset->jumlah_tersedia) {
            return redirect()->back()->with('error', 'Jumlah yang dipinjam melebihi stok tersedia (' . $aset->jumlah_tersedia . ' unit).');
        }

        $user = Auth::user();

        PeminjamanAset::create([
            'aset_id' => $aset->id,
            'user_id' => $user->id,
            'jumlah_pinjam' => $validated['jumlah_pinjam'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
            'keperluan' => $validated['keperluan'],
            'status' => 'menunggu',
        ]);

        ActivityLog::record('Aset', 'Peminjaman', 'Warga ' . $user->name . ' mengajukan peminjaman aset: ' . $aset->nama_aset . ' (' . $validated['jumlah_pinjam'] . ' unit) untuk ' . $validated['keperluan']);

        return redirect()->route('aset.index')->with('success', 'Permohonan pinjam aset berhasil diajukan dan sedang menunggu persetujuan pengurus.');
    }

    public function updateStatusPinjam(Request $request, PeminjamanAset $peminjaman)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,disetujui,dipinjam,dikembalikan,ditolak'],
            'catatan' => ['nullable', 'string'],
        ]);

        $aset = $peminjaman->aset;

        // Kurangi/kembalikan stok jika status berubah
        if ($validated['status'] === 'dipinjam' && $peminjaman->status !== 'dipinjam') {
            $aset->decrement('jumlah_tersedia', $peminjaman->jumlah_pinjam);
        } elseif ($validated['status'] === 'dikembalikan' && $peminjaman->status === 'dipinjam') {
            $aset->increment('jumlah_tersedia', $peminjaman->jumlah_pinjam);
            $peminjaman->tanggal_kembali_aktual = now();
        }

        $peminjaman->status = $validated['status'];
        $peminjaman->catatan = $validated['catatan'];
        $peminjaman->disetujui_oleh = $user->name . ' (' . $user->role_title . ')';
        $peminjaman->save();

        ActivityLog::record('Aset', 'Persetujuan', 'Pengurus (' . $user->name . ') memperbarui status peminjaman aset ' . ($aset->nama_aset ?? '') . ' menjadi ' . strtoupper($validated['status']) . '.');

        return redirect()->back()->with('success', 'Status peminjaman aset berhasil diperbarui.');
    }

    public function destroy(Aset $aset)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $aset->delete();
        return redirect()->route('aset.index')->with('success', 'Aset berhasil dihapus dari inventaris.');
    }
}
