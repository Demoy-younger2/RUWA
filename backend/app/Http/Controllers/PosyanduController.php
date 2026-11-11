<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        $query = Posyandu::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_peserta', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $posyandus = $query->latest('tanggal_pemeriksaan')->paginate(10)->withQueryString();

        $totalBalita = Posyandu::where('kategori', 'balita')->distinct('nama_peserta')->count();
        $totalLansia = Posyandu::where('kategori', 'lansia')->distinct('nama_peserta')->count();
        $totalPemeriksaanBulanIni = Posyandu::whereMonth('tanggal_pemeriksaan', now()->month)->count();

        return view('posyandu.index', compact('posyandus', 'totalBalita', 'totalLansia', 'totalPemeriksaanBulanIni'));
    }

    public function create()
    {
        $wargas = Warga::select('id', 'nama_lengkap', 'nik', 'tanggal_lahir')->get();
        return view('posyandu.create', compact('wargas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => ['required', 'in:balita,lansia'],
            'nama_peserta' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:16'],
            'tanggal_pemeriksaan' => ['required', 'date'],
            'usia_bulan_tahun' => ['nullable', 'integer'],
            'berat_badan' => ['nullable', 'numeric'],
            'tinggi_badan' => ['nullable', 'numeric'],
            'tensi_darah' => ['nullable', 'string', 'max:20'],
            'gula_darah' => ['nullable', 'numeric'],
            'status_kesehatan' => ['nullable', 'string', 'max:100'],
            'imunisasi_atau_vitamin' => ['nullable', 'string', 'max:255'],
            'catatan_kader' => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        Posyandu::create(array_merge($validated, [
            'nama_kader' => $user->name,
        ]));

        return redirect()->route('posyandu.index')->with('success', 'Catatan pemeriksaan Posyandu berhasil disimpan.');
    }

    public function show(Posyandu $posyandu)
    {
        return view('posyandu.show', compact('posyandu'));
    }

    public function destroy(Posyandu $posyandu)
    {
        $posyandu->delete();
        return redirect()->route('posyandu.index')->with('success', 'Data pemeriksaan posyandu berhasil dihapus.');
    }
}
