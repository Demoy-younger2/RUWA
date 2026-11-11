<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ActivityLog::with('user');

        // Filter pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('deskripsi', 'like', "%{$q}%")
                    ->orWhere('user_name', 'like', "%{$q}%")
                    ->orWhere('modul', 'like', "%{$q}%")
                    ->orWhere('tindakan', 'like', "%{$q}%");
            });
        }

        // Filter Modul
        if ($request->filled('modul')) {
            $query->where('modul', $request->modul);
        }

        // Filter Tindakan
        if ($request->filled('tindakan')) {
            $query->where('tindakan', $request->tindakan);
        }

        // Filter Role
        if ($request->filled('role')) {
            $query->where('user_role', $request->role);
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        // Metrik Statistik
        $totalLogs = ActivityLog::count();
        $logsHariIni = ActivityLog::whereDate('created_at', today())->count();
        $aksiPengurus = ActivityLog::whereIn('user_role', ['superadmin', 'rw', 'rt', 'sekretaris_rw', 'bendahara_rw', 'sekretaris_rt', 'bendahara_rt'])->count();
        $alarmDarurat = ActivityLog::where('tindakan', 'Darurat SOS')->count();

        $modulList = [
            'Auth',
            'Surat',
            'Warga',
            'Kas',
            'Ronda',
            'Bank Sampah',
            'UMKM',
            'Posyandu',
            'Kegiatan',
            'RUKEM',
            'Aspirasi',
            'Aset',
            'Koperasi',
            'Pengaturan',
        ];

        $tindakanList = [
            'Login',
            'Pengajuan',
            'Verifikasi',
            'Persetujuan',
            'Penolakan',
            'Tambah',
            'Ubah',
            'Hapus',
            'Transaksi',
            'Presensi',
            'Darurat SOS',
            'Setor',
            'Peminjaman',
        ];

        return view('log_aktivitas.index', compact(
            'logs',
            'totalLogs',
            'logsHariIni',
            'aksiPengurus',
            'alarmDarurat',
            'modulList',
            'tindakanList'
        ));
    }

    public function destroy(ActivityLog $activity_log)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang memiliki izin menghapus log audit.');
        }

        $activity_log->delete();

        return redirect()->route('log-aktivitas.index')->with('success', 'Catatan log aktivitas berhasil dihapus.');
    }

    public function clearAll(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang memiliki wewenang mengosongkan riwayat log.');
        }

        ActivityLog::truncate();

        ActivityLog::record('Pengaturan', 'Hapus', 'Superadmin ' . $user->name . ' mereset seluruh riwayat log aktivitas audit sistem.');

        return redirect()->route('log-aktivitas.index')->with('success', 'Seluruh riwayat log aktivitas telah dibersihkan.');
    }
}
