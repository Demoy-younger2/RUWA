<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kegiatan::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $kegiatans = $query->orderBy('tanggal_mulai', 'desc')->paginate(9)->withQueryString();

        $kategoriList = [
            'Kerja Bakti',
            'Rapat Warga',
            'Peringatan PHBN',
            'Senam Sehat',
            'Pengajian',
            'Posyandu',
            'Lomba & Olahraga',
            'Lain-lain',
        ];

        return view('kegiatan.index', compact('kegiatans', 'kategoriList'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt'])) {
            return redirect()->route('kegiatan.index')->with('error', 'Hanya pengurus yang dapat membuat agenda kegiatan.');
        }

        $kategoriList = [
            'Kerja Bakti',
            'Rapat Warga',
            'Peringatan PHBN',
            'Senam Sehat',
            'Pengajian',
            'Posyandu',
            'Lomba & Olahraga',
            'Lain-lain',
        ];

        return view('kegiatan.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'sekretaris_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'lokasi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'anggaran' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:akan_datang,berlangsung,selesai,dibatalkan'],
        ]);

        Kegiatan::create($validated);

        return redirect()->route('kegiatan.index')->with('success', 'Agenda kegiatan berhasil dipublikasikan ke warga.');
    }

    public function show(Kegiatan $kegiatan)
    {
        return view('kegiatan.show', compact('kegiatan'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        $kategoriList = [
            'Kerja Bakti',
            'Rapat Warga',
            'Peringatan PHBN',
            'Senam Sehat',
            'Pengajian',
            'Posyandu',
            'Lomba & Olahraga',
            'Lain-lain',
        ];

        return view('kegiatan.edit', compact('kegiatan', 'kategoriList'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'lokasi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'anggaran' => ['nullable', 'numeric'],
            'status' => ['required', 'in:akan_datang,berlangsung,selesai,dibatalkan'],
        ]);

        $kegiatan->update($validated);

        return redirect()->route('kegiatan.index')->with('success', 'Agenda kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $kegiatan->delete();
        return redirect()->route('kegiatan.index')->with('success', 'Agenda kegiatan telah dihapus.');
    }
}
