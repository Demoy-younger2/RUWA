<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Ronda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RondaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ronda::query();

        if (in_array($user->role, ['rt', 'sekretaris_rt', 'bendahara_rt'])) {
            $query->where('rt', $user->rt ?? '01');
        } elseif ($request->filled('rt')) {
            $query->where('rt', $request->rt);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $rondas = $query->orderByRaw("CASE 
            WHEN hari = 'Senin' THEN 1 
            WHEN hari = 'Selasa' THEN 2 
            WHEN hari = 'Rabu' THEN 3 
            WHEN hari = 'Kamis' THEN 4 
            WHEN hari = 'Jumat' THEN 5 
            WHEN hari = 'Sabtu' THEN 6 
            WHEN hari = 'Minggu' THEN 7 
            ELSE 8 END")
            ->paginate(12)->withQueryString();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $rtList = Ronda::distinct()->pluck('rt')->filter()->sort()->values();

        return view('ronda.index', compact('rondas', 'hariList', 'rtList'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'sekretaris_rt'])) {
            return redirect()->route('ronda.index')->with('error', 'Hanya pengurus yang dapat mengatur jadwal ronda.');
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $wargaUsers = User::where('role', 'warga')->get();

        return view('ronda.create', compact('hariList', 'wargaUsers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'hari' => ['required', 'string'],
            'nama_petugas' => ['required', 'string', 'max:255'],
            'pos_ronda' => ['required', 'string'],
            'jam_jaga' => ['required', 'string'],
            'rt' => ['required', 'string'],
        ]);

        Ronda::create([
            'rt' => $validated['rt'],
            'rw' => $user->rw ?? '05',
            'hari' => $validated['hari'],
            'nama_petugas' => $validated['nama_petugas'],
            'pos_ronda' => $validated['pos_ronda'],
            'jam_jaga' => $validated['jam_jaga'],
            'status_kehadiran' => 'dijadwalkan',
        ]);

        return redirect()->route('ronda.index')->with('success', 'Petugas ronda berhasil ditambahkan ke jadwal.');
    }

    public function presensi(Request $request, Ronda $ronda)
    {
        $validated = $request->validate([
            'status_kehadiran' => ['required', 'in:hadir,izin,alpa'],
            'catatan_patroli' => ['nullable', 'string'],
        ]);

        $ronda->update([
            'status_kehadiran' => $validated['status_kehadiran'],
            'catatan_patroli' => $validated['catatan_patroli'],
            'tanggal' => now(),
        ]);

        ActivityLog::record('Ronda', 'Presensi', 'Petugas ' . $ronda->nama_petugas . ' mencatatkan kehadiran ronda malam (' . strtoupper($validated['status_kehadiran']) . ').');

        return redirect()->back()->with('success', 'Status kehadiran ronda berhasil diperbarui.');
    }

    public function panicButton(Request $request)
    {
        $validated = $request->validate([
            'lokasi_kejadian' => ['required', 'string'],
            'jenis_darurat' => ['required', 'string'],
            'keterangan' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Rekam ke catatan ronda terkini sebagai laporan darurat
        Ronda::create([
            'rt' => $user->rt ?? '01',
            'rw' => $user->rw ?? '05',
            'hari' => now()->locale('id')->isoFormat('dddd'),
            'tanggal' => now(),
            'nama_petugas' => 'LAPORAN DARURAT: ' . $user->name,
            'pos_ronda' => 'Lokasi: ' . $validated['lokasi_kejadian'],
            'jam_jaga' => now()->format('H:i') . ' WIB',
            'status_kehadiran' => 'hadir',
            'laporan_kejadian' => '[' . strtoupper($validated['jenis_darurat']) . '] ' . $validated['keterangan'] . ' - Dilaporkan oleh: ' . $user->name . ' (' . $user->phone . ')',
        ]);

        ActivityLog::record('Ronda', 'Darurat SOS', 'TOMBOL DARURAT SOS DITEKAN oleh ' . $user->name . ' (RT ' . ($user->rt ?? '01') . ')! Jenis: ' . $validated['jenis_darurat'] . ' di ' . $validated['lokasi_kejadian'] . ' - "' . $validated['keterangan'] . '"');

        return redirect()->route('ronda.index')->with('success', '🚨 SIAGA! Laporan darurat telah dibroadcast ke seluruh regu jaga ronda dan pengurus RW.');
    }

    public function destroy(Ronda $ronda)
    {
        $ronda->delete();
        return redirect()->route('ronda.index')->with('success', 'Jadwal ronda berhasil dihapus.');
    }
}
