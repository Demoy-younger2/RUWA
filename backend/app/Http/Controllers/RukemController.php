<?php

namespace App\Http\Controllers;

use App\Models\Rukem;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RukemController extends Controller
{
    public function index(Request $request)
    {
        $query = Rukem::query();

        if ($request->filled('rt')) {
            $query->where('rt', $request->rt);
        }

        $rukems = $query->latest('tanggal_meninggal')->paginate(10)->withQueryString();

        $totalSantunanDiserahkan = Rukem::where('status_santunan', 'sudah_diserahkan')->sum('jumlah_santunan');
        $totalSantunanTertunda = Rukem::where('status_santunan', 'belum_diserahkan')->sum('jumlah_santunan');

        return view('rukem.index', compact('rukems', 'totalSantunanDiserahkan', 'totalSantunanTertunda'));
    }

    public function create()
    {
        $wargas = Warga::select('id', 'nama_lengkap', 'nik', 'alamat', 'rt', 'rw')->get();
        return view('rukem.create', compact('wargas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_almarhum' => ['required', 'string', 'max:255'],
            'tanggal_meninggal' => ['required', 'date'],
            'jam_meninggal' => ['nullable'],
            'nama_ahli_waris' => ['required', 'string', 'max:255'],
            'no_hp_ahli_waris' => ['nullable', 'string', 'max:25'],
            'alamat_duka' => ['required', 'string'],
            'rt' => ['required', 'string', 'max:5'],
            'rw' => ['required', 'string', 'max:5'],
            'lokasi_pemakaman' => ['nullable', 'string', 'max:255'],
            'jam_pemakaman' => ['nullable'],
            'jumlah_santunan' => ['required', 'numeric', 'min:0'],
            'fasilitas_disiapkan' => ['nullable', 'string'],
        ]);

        Rukem::create(array_merge($validated, [
            'status_santunan' => 'belum_diserahkan',
        ]));

        return redirect()->route('rukem.index')->with('success', 'Laporan duka RUKEM berhasil dicatat. Pengurus dan tim siaga rukun kematian telah diberi notifikasi.');
    }

    public function serahkanSantunan(Request $request, Rukem $rukem)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $rukem->update([
            'status_santunan' => 'sudah_diserahkan',
            'tanggal_penyerahan_santunan' => now(),
            'petugas_penyerah' => $user->name . ' (' . $user->role_title . ')',
        ]);

        return redirect()->back()->with('success', 'Santunan RUKEM telah dikonfirmasi sudah diserahkan ke keluarga ahli waris.');
    }

    public function destroy(Rukem $rukem)
    {
        $rukem->delete();
        return redirect()->route('rukem.index')->with('success', 'Data RUKEM berhasil dihapus.');
    }
}
