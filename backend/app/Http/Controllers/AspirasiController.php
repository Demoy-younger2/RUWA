<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Aspirasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AspirasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Aspirasi::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $aspirasis = $query->latest()->paginate(10)->withQueryString();

        $kategoriList = [
            'Infrastruktur & Jalan',
            'Keamanan & Ketertiban',
            'Kebersihan & Sampah',
            'Sosial & Kemasyarakatan',
            'Pelayanan Administrasi RT/RW',
            'Lain-lain',
        ];

        return view('aspirasi.index', compact('aspirasis', 'kategoriList'));
    }

    public function create()
    {
        $kategoriList = [
            'Infrastruktur & Jalan',
            'Keamanan & Ketertiban',
            'Kebersihan & Sampah',
            'Sosial & Kemasyarakatan',
            'Pelayanan Administrasi RT/RW',
            'Lain-lain',
        ];

        return view('aspirasi.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => ['required', 'string'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'is_anonim' => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();

        Aspirasi::create([
            'user_id' => $user->id,
            'kategori' => $validated['kategori'],
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'lokasi' => $validated['lokasi'],
            'is_anonim' => $request->boolean('is_anonim'),
            'status' => 'menunggu',
        ]);

        ActivityLog::record('Aspirasi', 'Pengajuan', 'Menyampaikan aspirasi/aduan: "' . $validated['judul'] . '" (' . $validated['kategori'] . ').' . ($request->boolean('is_anonim') ? ' [Pengirim Anonim]' : ''));

        return redirect()->route('aspirasi.index')->with('success', 'Aspirasi/Aduan Anda berhasil dikirim ke pengurus RT & RW.');
    }

    public function tanggapi(Request $request, Aspirasi $aspirasi)
    {
        $user = Auth::user();
        if ($user->isWarga()) {
            abort(403, 'Hanya pengurus yang dapat memberikan tanggapan resmi.');
        }

        $validated = $request->validate([
            'tanggapan' => ['required', 'string'],
            'status' => ['required', 'in:menunggu,diproses,selesai,ditolak'],
        ]);

        $aspirasi->update([
            'tanggapan' => $validated['tanggapan'],
            'status' => $validated['status'],
            'ditanggapi_oleh' => $user->name . ' (' . $user->role_title . ')',
        ]);

        ActivityLog::record('Aspirasi', 'Tanggapan', 'Pengurus (' . $user->name . ') menanggapi aduan "' . $aspirasi->judul . '" (Status: ' . strtoupper($validated['status']) . ').');

        return redirect()->back()->with('success', 'Tanggapan dan status aduan berhasil diperbarui.');
    }

    public function destroy(Aspirasi $aspirasi)
    {
        $user = Auth::user();
        if ($aspirasi->user_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $aspirasi->delete();
        return redirect()->route('aspirasi.index')->with('success', 'Aspirasi berhasil dihapus.');
    }
}
