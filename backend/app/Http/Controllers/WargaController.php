<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Warga::with('user');

        // Scope filter by RT if RT official
        if (in_array($user->role, ['rt', 'sekretaris_rt', 'bendahara_rt'])) {
            $query->where('rt', $user->rt ?? '01');
        } elseif ($request->filled('rt')) {
            $query->where('rt', $request->rt);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_kk', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_keluarga')) {
            $query->where('status_keluarga', $request->status_keluarga);
        }

        $wargas = $query->latest()->paginate(10)->withQueryString();
        $rtList = Warga::distinct()->pluck('rt')->filter()->sort()->values();

        return view('warga.index', compact('wargas', 'rtList'));
    }

    public function create()
    {
        return view('warga.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', 'unique:wargas,nik'],
            'no_kk' => ['nullable', 'string', 'max:16'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'agama' => ['required', 'string', 'max:30'],
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'status_perkawinan' => ['required', 'string', 'max:30'],
            'status_keluarga' => ['required', 'string', 'max:30'],
            'status_tinggal' => ['required', 'string', 'max:30'],
            'rt' => ['required', 'string', 'max:5'],
            'rw' => ['required', 'string', 'max:5'],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:25'],
        ]);

        Warga::create($validated);

        ActivityLog::record('Warga', 'Tambah', 'Menambahkan data warga baru: ' . $validated['nama_lengkap'] . ' (NIK: ' . $validated['nik'] . ', RT ' . $validated['rt'] . ').');

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function show(Warga $warga)
    {
        $warga->load(['user', 'surats']);
        return view('warga.show', compact('warga'));
    }

    public function edit(Warga $warga)
    {
        return view('warga.edit', compact('warga'));
    }

    public function update(Request $request, Warga $warga)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', 'unique:wargas,nik,' . $warga->id],
            'no_kk' => ['nullable', 'string', 'max:16'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'agama' => ['required', 'string', 'max:30'],
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'status_perkawinan' => ['required', 'string', 'max:30'],
            'status_keluarga' => ['required', 'string', 'max:30'],
            'status_tinggal' => ['required', 'string', 'max:30'],
            'rt' => ['required', 'string', 'max:5'],
            'rw' => ['required', 'string', 'max:5'],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:25'],
        ]);

        $warga->update($validated);

        ActivityLog::record('Warga', 'Ubah', 'Memperbarui data profil kependudukan warga: ' . $warga->nama_lengkap . ' (NIK: ' . $warga->nik . ').');

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(Warga $warga)
    {
        ActivityLog::record('Warga', 'Hapus', 'Menghapus data kependudukan warga: ' . $warga->nama_lengkap . ' (NIK: ' . $warga->nik . ').');

        $warga->delete();
        return redirect()->route('warga.index')->with('success', 'Data warga berhasil dihapus.');
    }
}
