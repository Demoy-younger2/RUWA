<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pengaturan;
use App\Models\Surat;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Surat::with(['user', 'warga']);

        if ($user->isWarga()) {
            $query->where('user_id', $user->id);
        } elseif (in_array($user->role, ['rt', 'sekretaris_rt', 'bendahara_rt'])) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('rt', $user->rt ?? '01');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_surat')) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        $surats = $query->latest()->paginate(10)->withQueryString();

        return view('surat.index', compact('surats'));
    }

    public function create()
    {
        $jenisSuratList = [
            'Surat Pengantar SKCK',
            'Surat Keterangan Domisili',
            'Surat Keterangan Usaha (SKU)',
            'Surat Keterangan Tidak Mampu (SKTM)',
            'Surat Keterangan Kematian',
            'Surat Keterangan Pindah',
            'Surat Izin Keramaian Lingkungan',
        ];

        return view('surat.create', compact('jenisSuratList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_surat' => ['required', 'string'],
            'keperluan' => ['required', 'string'],
            'keterangan_tambahan' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $warga = Warga::where('user_id', $user->id)->first();

        $suratBaru = Surat::create([
            'user_id' => $user->id,
            'warga_id' => $warga?->id,
            'jenis_surat' => $validated['jenis_surat'],
            'keperluan' => $validated['keperluan'],
            'keterangan_tambahan' => $validated['keterangan_tambahan'],
            'status' => 'diajukan',
            'tanggal_diajukan' => now(),
        ]);

        ActivityLog::record('Surat', 'Pengajuan', 'Warga ' . $user->name . ' mengajukan ' . $validated['jenis_surat'] . ' untuk keperluan: ' . $validated['keperluan']);

        return redirect()->route('surat.index')->with('success', 'Permohonan surat berhasil diajukan dan sedang menunggu verifikasi RT.');
    }

    public function show(Surat $surat)
    {
        $surat->load(['user', 'warga']);
        $pengaturan = Pengaturan::first() ?? new Pengaturan();

        return view('surat.show', compact('surat', 'pengaturan'));
    }

    public function approveRT(Request $request, Surat $surat)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rt', 'sekretaris_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'catatan_rt' => ['nullable', 'string'],
        ]);

        $surat->update([
            'status' => 'disetujui_rt',
            'catatan_rt' => $validated['catatan_rt'] ?? 'Data telah diverifikasi dan disetujui tingkat RT.',
            'disetujui_rt_oleh' => $user->name . ' (' . $user->role_title . ')',
            'tanggal_disetujui_rt' => now(),
        ]);

        ActivityLog::record('Surat', 'Verifikasi', 'Ketua RT memverifikasi permohonan ' . $surat->jenis_surat . ' milik ' . ($surat->user->name ?? 'Warga') . '.');

        return redirect()->back()->with('success', 'Surat berhasil disetujui di tingkat RT dan diteruskan ke RW.');
    }

    public function approveRW(Request $request, Surat $surat)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'sekretaris_rw'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'catatan_rw' => ['nullable', 'string'],
        ]);

        // Generate Nomor Surat Otomatis jika belum ada
        $noSurat = $surat->nomor_surat;
        if (!$noSurat) {
            $urut = Surat::whereNotNull('nomor_surat')->count() + 1;
            $noSurat = sprintf('470/%03d/SRW/%s', $urut, now()->format('Y'));
        }

        $surat->update([
            'nomor_surat' => $noSurat,
            'status' => 'selesai',
            'catatan_rw' => $validated['catatan_rw'] ?? 'Surat telah ditandatangani dan siap dicetak.',
            'disetujui_rw_oleh' => $user->name . ' (' . $user->role_title . ')',
            'tanggal_disetujui_rw' => now(),
        ]);

        ActivityLog::record('Surat', 'Persetujuan', 'Ketua RW menyetujui dan menerbitkan nomor surat resmi ' . $noSurat . ' untuk ' . ($surat->user->name ?? 'Warga') . '.');

        return redirect()->back()->with('success', 'Surat telah disetujui oleh RW dan nomor surat resmi telah diterbitkan: ' . $noSurat);
    }

    public function reject(Request $request, Surat $surat)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'alasan_penolakan' => ['required', 'string'],
        ]);

        $surat->update([
            'status' => 'ditolak',
            'catatan_rw' => 'Ditolak: ' . $validated['alasan_penolakan'] . ' (oleh ' . $user->name . ')',
        ]);

        ActivityLog::record('Surat', 'Penolakan', 'Pengurus menolak permohonan surat milik ' . ($surat->user->name ?? 'Warga') . '. Alasan: ' . $validated['alasan_penolakan']);

        return redirect()->back()->with('warning', 'Permohonan surat telah ditolak.');
    }

    public function cetak(Surat $surat)
    {
        $surat->load(['user', 'warga']);
        $pengaturan = Pengaturan::first() ?? new Pengaturan();

        return view('surat.cetak', compact('surat', 'pengaturan'));
    }
}
