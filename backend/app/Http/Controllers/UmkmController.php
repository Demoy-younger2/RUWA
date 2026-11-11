<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $query = Umkm::with('user')->where('status', 'aktif');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('rt')) {
            $query->where('rt', $request->rt);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $umkms = $query->latest()->paginate(9)->withQueryString();

        $kategoriList = [
            'Makanan & Minuman',
            'Sembako & Kelontong',
            'Jasa & Servis',
            'Fashion & Pakaian',
            'Kerajinan & Seni',
            'Tanaman & Pertanian',
            'Lain-lain',
        ];

        return view('umkm.index', compact('umkms', 'kategoriList'));
    }

    public function create()
    {
        $kategoriList = [
            'Makanan & Minuman',
            'Sembako & Kelontong',
            'Jasa & Servis',
            'Fashion & Pakaian',
            'Kerajinan & Seni',
            'Tanaman & Pertanian',
            'Lain-lain',
        ];

        return view('umkm.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_usaha' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string'],
            'deskripsi' => ['required', 'string'],
            'harga_mulai' => ['required', 'numeric', 'min:0'],
            'no_whatsapp' => ['required', 'string', 'max:25'],
            'alamat' => ['nullable', 'string'],
            'rt' => ['nullable', 'string', 'max:5'],
        ]);

        $user = Auth::user();

        // Normalisasi nomor whatsapp ke format 62xxx jika diawali 08
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['no_whatsapp']);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '628' . substr($cleanPhone, 2);
        }

        Umkm::create([
            'user_id' => $user->id,
            'nama_usaha' => $validated['nama_usaha'],
            'nama_pemilik' => $user->name,
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'harga_mulai' => $validated['harga_mulai'],
            'no_whatsapp' => $cleanPhone,
            'alamat' => $validated['alamat'] ?? ('RT ' . ($user->rt ?? '01') . ' RW ' . ($user->rw ?? '05')),
            'rt' => $validated['rt'] ?? ($user->rt ?? '01'),
            'rw' => $user->rw ?? '05',
            'status' => 'aktif',
        ]);

        return redirect()->route('umkm.index')->with('success', 'Lapak usaha warga Anda berhasil dipublikasikan!');
    }

    public function show(Umkm $umkm)
    {
        $umkm->load('user');
        return view('umkm.show', compact('umkm'));
    }

    public function edit(Umkm $umkm)
    {
        $user = Auth::user();
        if ($umkm->user_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses mengubah produk ini.');
        }

        $kategoriList = [
            'Makanan & Minuman',
            'Sembako & Kelontong',
            'Jasa & Servis',
            'Fashion & Pakaian',
            'Kerajinan & Seni',
            'Tanaman & Pertanian',
            'Lain-lain',
        ];

        return view('umkm.edit', compact('umkm', 'kategoriList'));
    }

    public function update(Request $request, Umkm $umkm)
    {
        $user = Auth::user();
        if ($umkm->user_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'nama_usaha' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string'],
            'deskripsi' => ['required', 'string'],
            'harga_mulai' => ['required', 'numeric', 'min:0'],
            'no_whatsapp' => ['required', 'string', 'max:25'],
            'alamat' => ['nullable', 'string'],
            'rt' => ['nullable', 'string', 'max:5'],
        ]);

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['no_whatsapp']);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '628' . substr($cleanPhone, 2);
        }

        $umkm->update([
            'nama_usaha' => $validated['nama_usaha'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'harga_mulai' => $validated['harga_mulai'],
            'no_whatsapp' => $cleanPhone,
            'alamat' => $validated['alamat'],
            'rt' => $validated['rt'] ?? $umkm->rt,
        ]);

        return redirect()->route('umkm.index')->with('success', 'Data usaha berhasil diperbarui.');
    }

    public function destroy(Umkm $umkm)
    {
        $user = Auth::user();
        if ($umkm->user_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $umkm->delete();
        return redirect()->route('umkm.index')->with('success', 'Produk UMKM berhasil dihapus.');
    }
}
