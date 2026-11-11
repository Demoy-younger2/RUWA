<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Aset;
use App\Models\Aspirasi;
use App\Models\BankSampah;
use App\Models\Kas;
use App\Models\Kegiatan;
use App\Models\KoperasiAnggota;
use App\Models\KoperasiTransaksi;
use App\Models\PeminjamanAset;
use App\Models\Pengaturan;
use App\Models\Posyandu;
use App\Models\Ronda;
use App\Models\Rukem;
use App\Models\Surat;
use App\Models\TransaksiBankSampah;
use App\Models\Umkm;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    // ==========================================
    // 1. PUBLIC STATS & CHECK SURAT
    // ==========================================
    public function getPublicStats()
    {
        $pengaturan = Pengaturan::first() ?? new Pengaturan();
        $totalWarga = Warga::count();
        $totalUmkm = Umkm::where('status', 'aktif')->count();
        $totalSuratSelesai = Surat::where('status', 'selesai')->count();
        $kasMasuk = Kas::where('jenis', 'pemasukan')->sum('jumlah');
        $kasKeluar = Kas::where('jenis', 'pengeluaran')->sum('jumlah');
        $saldoKas = $kasMasuk - $kasKeluar;

        $umkms = Umkm::where('status', 'aktif')->latest()->take(6)->get();
        $kegiatans = Kegiatan::latest('tanggal_mulai')->take(4)->get();

        return response()->json([
            'pengaturan' => $pengaturan,
            'stats' => [
                'total_warga' => $totalWarga,
                'total_rt' => 6,
                'total_umkm' => $totalUmkm,
                'total_surat_selesai' => $totalSuratSelesai,
                'saldo_kas' => $saldoKas,
            ],
            'umkm_terkini' => $umkms,
            'kegiatan_terkini' => $kegiatans,
        ]);
    }

    public function checkSurat(Request $request)
    {
        $nomor = $request->query('nomor') ?? $request->nomor;
        if (!$nomor) {
            return response()->json(['message' => 'Nomor surat diperlukan.'], 400);
        }

        $surat = Surat::with(['user', 'warga'])
            ->where('nomor_surat', $nomor)
            ->orWhere('id', $nomor)
            ->first();

        if (!$surat) {
            return response()->json(['message' => 'Surat tidak ditemukan dalam sistem.'], 404);
        }

        return response()->json(['surat' => $surat]);
    }

    // ==========================================
    // 2. AUTHENTICATION (8 ROLES)
    // ==========================================
    public function quickLogin($role)
    {
        $user = User::where('role', $role)->first();
        if (!$user) {
            return response()->json(['message' => 'Akun untuk role ini tidak ditemukan.'], 404);
        }

        ActivityLog::record('Auth', 'Login', 'Quick Login sebagai ' . $user->role_title . ' (' . $user->name . ').', $user);

        return response()->json([
            'message' => 'Login berhasil!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_title' => $user->role_title,
                'rt' => $user->rt,
                'rw' => $user->rw,
                'nik' => $user->nik,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Email atau kata sandi tidak cocok.'], 401);
        }

        ActivityLog::record('Auth', 'Login', 'Pengguna ' . $user->name . ' berhasil masuk ke sistem (' . $user->role_title . ').', $user);

        return response()->json([
            'message' => 'Login berhasil!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_title' => $user->role_title,
                'rt' => $user->rt,
                'rw' => $user->rw,
                'nik' => $user->nik,
                'phone' => $user->phone,
            ],
        ]);
    }

    // ==========================================
    // 3. DASHBOARD SUMMARY
    // ==========================================
    public function getDashboard(Request $request)
    {
        $role = $request->query('role', 'warga');
        $userId = $request->query('user_id');

        $totalWarga = Warga::count();
        $totalSuratMenunggu = Surat::whereIn('status', ['diajukan', 'disetujui_rt'])->count();
        $totalKas = Kas::where('jenis', 'pemasukan')->sum('jumlah') - Kas::where('jenis', 'pengeluaran')->sum('jumlah');
        $totalAspirasiMenunggu = Aspirasi::where('status', 'menunggu')->count();
        $totalAset = Aset::count();
        $totalPinjamanKoperasi = KoperasiAnggota::sum('total_pinjaman');
        $totalSimpananKoperasi = KoperasiAnggota::sum('total_simpanan');

        $suratTerkini = Surat::with('user')->latest()->take(5)->get();
        $logTerkini = ActivityLog::latest()->take(6)->get();
        $rondaHariIni = Ronda::where('hari', now()->locale('id')->isoFormat('dddd'))->get();

        return response()->json([
            'metrics' => [
                'total_warga' => $totalWarga,
                'surat_menunggu' => $totalSuratMenunggu,
                'saldo_kas' => $totalKas,
                'aspirasi_menunggu' => $totalAspirasiMenunggu,
                'total_aset' => $totalAset,
                'total_simpanan' => $totalSimpananKoperasi,
                'total_pinjaman' => $totalPinjamanKoperasi,
            ],
            'surat_terkini' => $suratTerkini,
            'log_terkini' => $logTerkini,
            'ronda_hari_ini' => $rondaHariIni,
        ]);
    }

    // ==========================================
    // 4. DATA WARGA
    // ==========================================
    public function getWarga(Request $request)
    {
        $query = Warga::query();
        if ($request->filled('rt')) {
            $query->where('rt', $request->rt);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_lengkap', 'like', "%{$s}%")
                    ->orWhere('nik', 'like', "%{$s}%")
                    ->orWhere('no_kk', 'like', "%{$s}%");
            });
        }
        return response()->json($query->latest()->paginate(15));
    }

    public function storeWarga(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|unique:wargas,nik',
            'no_kk' => 'nullable',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required',
            'status_perkawinan' => 'required',
            'status_keluarga' => 'required',
            'status_tinggal' => 'required',
            'rt' => 'required',
            'rw' => 'required',
            'alamat' => 'nullable',
            'no_hp' => 'nullable',
        ]);

        $warga = Warga::create($data);
        ActivityLog::record('Warga', 'Tambah', 'Pencatatan data warga baru: ' . $warga->nama_lengkap . ' (RT ' . $warga->rt . ').');
        return response()->json(['message' => 'Data warga berhasil ditambahkan!', 'warga' => $warga]);
    }

    // ==========================================
    // 5. SURAT MENYURAT
    // ==========================================
    public function getSurat(Request $request)
    {
        $query = Surat::with(['user', 'warga']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        return response()->json($query->latest()->get());
    }

    public function storeSurat(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'jenis_surat' => 'required|string',
            'keperluan' => 'required|string',
            'keterangan_tambahan' => 'nullable|string',
        ]);

        $user = User::find($data['user_id']);
        $warga = Warga::where('user_id', $user->id)->first();

        $surat = Surat::create([
            'user_id' => $user->id,
            'warga_id' => $warga?->id,
            'jenis_surat' => $data['jenis_surat'],
            'keperluan' => $data['keperluan'],
            'keterangan_tambahan' => $data['keterangan_tambahan'] ?? null,
            'status' => 'diajukan',
            'tanggal_diajukan' => now(),
        ]);

        ActivityLog::record('Surat', 'Pengajuan', 'Warga ' . $user->name . ' mengajukan ' . $data['jenis_surat'] . ' (' . $data['keperluan'] . ').', $user);
        return response()->json(['message' => 'Permohonan surat berhasil diajukan!', 'surat' => $surat]);
    }

    public function approveRtSurat(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);
        $approverName = $request->input('approver_name', 'Ketua RT');
        $surat->update([
            'status' => 'disetujui_rt',
            'catatan_rt' => $request->input('catatan', 'Telah diverifikasi dan disetujui RT.'),
            'disetujui_rt_oleh' => $approverName,
            'tanggal_disetujui_rt' => now(),
        ]);

        ActivityLog::record('Surat', 'Verifikasi', $approverName . ' memverifikasi permohonan ' . $surat->jenis_surat . '.');
        return response()->json(['message' => 'Surat berhasil diverifikasi RT!', 'surat' => $surat]);
    }

    public function approveRwSurat(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);
        $approverName = $request->input('approver_name', 'Ketua RW');

        $urut = Surat::whereNotNull('nomor_surat')->count() + 1;
        $noSurat = sprintf('470/%03d/SRW/%s', $urut, now()->format('Y'));

        $surat->update([
            'nomor_surat' => $noSurat,
            'status' => 'selesai',
            'catatan_rw' => $request->input('catatan', 'Surat telah ditandatangani dan diterbitkan.'),
            'disetujui_rw_oleh' => $approverName,
            'tanggal_disetujui_rw' => now(),
        ]);

        ActivityLog::record('Surat', 'Persetujuan', $approverName . ' menerbitkan nomor surat resmi ' . $noSurat . '.');
        return response()->json(['message' => 'Surat berhasil disetujui RW!', 'surat' => $surat]);
    }

    // ==========================================
    // 6. KAS KEUANGAN
    // ==========================================
    public function getKas()
    {
        $kas = Kas::with('user')->latest('tanggal')->get();
        $pemasukan = Kas::where('jenis', 'pemasukan')->sum('jumlah');
        $pengeluaran = Kas::where('jenis', 'pengeluaran')->sum('jumlah');

        return response()->json([
            'kas' => $kas,
            'total_pemasukan' => $pemasukan,
            'total_pengeluaran' => $pengeluaran,
            'saldo' => $pemasukan - $pengeluaran,
        ]);
    }

    public function storeKas(Request $request)
    {
        $data = $request->validate([
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable',
            'kategori' => 'required',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'jumlah' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
            'keterangan' => 'required',
        ]);

        $kas = Kas::create(array_merge($data, ['user_id' => 1, 'rw' => '05']));
        ActivityLog::record('Kas', 'Transaksi', 'Pencatatan kas ' . $data['jenis'] . ' ' . $data['tingkat'] . ' sebesar Rp ' . number_format($data['jumlah']) . ' (' . $data['kategori'] . ').');
        return response()->json(['message' => 'Transaksi kas berhasil dicatat!', 'kas' => $kas]);
    }

    // ==========================================
    // 7. RONDA & PANIC BUTTON
    // ==========================================
    public function getRonda()
    {
        $rondas = Ronda::orderBy('hari')->get();
        $panicAlerts = Ronda::whereNotNull('laporan_kejadian')->latest()->take(5)->get();
        return response()->json(['rondas' => $rondas, 'panic_alerts' => $panicAlerts]);
    }

    public function triggerPanic(Request $request)
    {
        $data = $request->validate([
            'lokasi_kejadian' => 'required',
            'jenis_darurat' => 'required',
            'keterangan' => 'required',
            'pelapor' => 'nullable',
        ]);

        $ronda = Ronda::create([
            'rt' => '01',
            'rw' => '05',
            'hari' => now()->locale('id')->isoFormat('dddd'),
            'tanggal' => now(),
            'nama_petugas' => 'LAPORAN DARURAT: ' . ($data['pelapor'] ?? 'Warga'),
            'pos_ronda' => 'Lokasi: ' . $data['lokasi_kejadian'],
            'jam_jaga' => now()->format('H:i') . ' WIB',
            'status_kehadiran' => 'hadir',
            'laporan_kejadian' => '[' . strtoupper($data['jenis_darurat']) . '] ' . $data['keterangan'],
        ]);

        ActivityLog::record('Ronda', 'Darurat SOS', 'TOMBOL DARURAT SOS DITEKAN! [' . $data['jenis_darurat'] . '] di ' . $data['lokasi_kejadian'] . ' - "' . $data['keterangan'] . '"');
        return response()->json(['message' => '🚨 ALARM DARURAT BERHASIL DISIARKAN KE SELURUH WARGA & PENGURUS!', 'alert' => $ronda]);
    }

    // ==========================================
    // 8. BANK SAMPAH
    // ==========================================
    public function getBankSampah()
    {
        $kategoris = BankSampah::all();
        $transaksis = TransaksiBankSampah::with('user', 'bankSampah')->latest()->take(20)->get();
        return response()->json(['kategoris' => $kategoris, 'transaksis' => $transaksis]);
    }

    public function setorSampah(Request $request)
    {
        $data = $request->validate([
            'bank_sampah_id' => 'required|exists:bank_sampahs,id',
            'berat' => 'required|numeric|min:0.1',
            'user_id' => 'required|exists:users,id',
        ]);

        $kat = BankSampah::findOrFail($data['bank_sampah_id']);
        $totalRupiah = $data['berat'] * $kat->harga_per_kg;

        $trx = TransaksiBankSampah::create([
            'kode_transaksi' => 'TRX-BS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'user_id' => $data['user_id'],
            'bank_sampah_id' => $kat->id,
            'jenis_transaksi' => 'setor',
            'berat' => $data['berat'],
            'total_rupiah' => $totalRupiah,
            'tanggal' => now(),
            'status' => 'selesai',
            'catatan' => 'Penyetoran sampah: ' . $kat->nama_kategori,
        ]);

        ActivityLog::record('Bank Sampah', 'Setor', 'Setor sampah daur ulang ' . $kat->nama_kategori . ' ' . $data['berat'] . ' Kg (Rp ' . number_format($totalRupiah) . ').');
        return response()->json(['message' => 'Setor sampah berhasil dicatat!', 'transaksi' => $trx]);
    }

    // ==========================================
    // 9. UMKM
    // ==========================================
    public function getUmkm()
    {
        return response()->json(Umkm::latest()->get());
    }

    public function storeUmkm(Request $request)
    {
        $data = $request->validate([
            'nama_usaha' => 'required',
            'kategori' => 'required',
            'nama_pemilik' => 'required',
            'no_wa' => 'required',
            'alamat_usaha' => 'required',
            'deskripsi' => 'nullable',
            'rentang_harga' => 'nullable',
        ]);

        $umkm = Umkm::create(array_merge($data, ['user_id' => 1, 'status' => 'aktif']));
        ActivityLog::record('UMKM', 'Tambah', 'Pendaftaran lapak UMKM warga: ' . $umkm->nama_usaha . ' (' . $umkm->kategori . ').');
        return response()->json(['message' => 'Produk UMKM berhasil didaftarkan!', 'umkm' => $umkm]);
    }

    // ==========================================
    // 10. POSYANDU
    // ==========================================
    public function getPosyandu()
    {
        return response()->json(Posyandu::latest('tanggal_periksa')->get());
    }

    public function storePosyandu(Request $request)
    {
        $data = $request->validate([
            'kategori' => 'required|in:balita,lansia',
            'nama_peserta' => 'required',
            'tanggal_periksa' => 'required|date',
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'catatan_kesehatan' => 'nullable',
        ]);

        $posyandu = Posyandu::create(array_merge($data, ['rt' => '01', 'rw' => '05', 'petugas' => 'Kader Posyandu']));
        ActivityLog::record('Posyandu', 'Tambah', 'Pemeriksaan Posyandu (' . $data['kategori'] . ') peserta: ' . $data['nama_peserta'] . '.');
        return response()->json(['message' => 'Data pemeriksaan posyandu berhasil dicatat!', 'posyandu' => $posyandu]);
    }

    // ==========================================
    // 11. KEGIATAN & RUKEM & ASPIRASI
    // ==========================================
    public function getKegiatan()
    {
        return response()->json(Kegiatan::latest('tanggal_mulai')->get());
    }

    public function storeKegiatan(Request $request)
    {
        $data = $request->validate([
            'nama_kegiatan' => 'required',
            'kategori' => 'required',
            'tanggal_mulai' => 'required|date',
            'lokasi' => 'required',
            'deskripsi' => 'nullable',
        ]);

        $kegiatan = Kegiatan::create(array_merge($data, ['user_id' => 1, 'status' => 'akan_datang']));
        ActivityLog::record('Kegiatan', 'Tambah', 'Agenda kegiatan baru: ' . $kegiatan->nama_kegiatan . ' (' . $kegiatan->tanggal_mulai . ').');
        return response()->json(['message' => 'Kegiatan warga berhasil diagendakan!', 'kegiatan' => $kegiatan]);
    }

    public function getRukem()
    {
        return response()->json(Rukem::latest('tanggal_meninggal')->get());
    }

    public function getAspirasi()
    {
        return response()->json(Aspirasi::with('user')->latest()->get());
    }

    public function storeAspirasi(Request $request)
    {
        $data = $request->validate([
            'kategori' => 'required',
            'judul' => 'required',
            'isi' => 'required',
            'lokasi' => 'nullable',
            'is_anonim' => 'nullable|boolean',
            'user_id' => 'required|exists:users,id',
        ]);

        $aspirasi = Aspirasi::create(array_merge($data, ['status' => 'menunggu']));
        ActivityLog::record('Aspirasi', 'Pengajuan', 'Aspirasi baru disampaikan: "' . $data['judul'] . '" (' . $data['kategori'] . ').');
        return response()->json(['message' => 'Aspirasi Anda berhasil terkirim ke pengurus!', 'aspirasi' => $aspirasi]);
    }

    public function tanggapiAspirasi(Request $request, $id)
    {
        $aspirasi = Aspirasi::findOrFail($id);
        $aspirasi->update([
            'tanggapan' => $request->input('tanggapan'),
            'status' => $request->input('status', 'selesai'),
            'ditanggapi_oleh' => $request->input('petugas', 'Pengurus RT/RW'),
        ]);

        ActivityLog::record('Aspirasi', 'Tanggapan', 'Tanggapan diberikan untuk aduan "' . $aspirasi->judul . '".');
        return response()->json(['message' => 'Tanggapan aspirasi berhasil disimpan!', 'aspirasi' => $aspirasi]);
    }

    // ==========================================
    // 12. ASET & INVENTARIS
    // ==========================================
    public function getAset()
    {
        $asets = Aset::all();
        $peminjamans = PeminjamanAset::with('aset', 'user')->latest()->take(15)->get();
        return response()->json(['asets' => $asets, 'peminjamans' => $peminjamans]);
    }

    public function pinjamAset(Request $request)
    {
        $data = $request->validate([
            'aset_id' => 'required|exists:asets,id',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date',
            'keperluan' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);

        $pinjam = PeminjamanAset::create(array_merge($data, ['status' => 'menunggu']));
        $aset = Aset::find($data['aset_id']);
        ActivityLog::record('Aset', 'Peminjaman', 'Permohonan pinjam ' . $aset->nama_aset . ' (' . $data['jumlah_pinjam'] . ' unit).');
        return response()->json(['message' => 'Permohonan pinjam aset berhasil dikirim!', 'peminjaman' => $pinjam]);
    }

    // ==========================================
    // 13. KOPERASI WARGA
    // ==========================================
    public function getKoperasi(Request $request)
    {
        $userId = $request->query('user_id');
        $myAnggota = $userId ? KoperasiAnggota::where('user_id', $userId)->first() : null;
        $totalSimpanan = KoperasiAnggota::sum('total_simpanan');
        $totalPinjaman = KoperasiAnggota::sum('total_pinjaman');
        $transaksis = KoperasiTransaksi::with('anggota.user')->latest('tanggal')->take(15)->get();

        return response()->json([
            'my_anggota' => $myAnggota,
            'total_simpanan' => $totalSimpanan,
            'total_pinjaman' => $totalPinjaman,
            'total_anggota' => KoperasiAnggota::count(),
            'transaksis' => $transaksis,
        ]);
    }

    // ==========================================
    // 14. LOG AKTIVITAS & AUDIT
    // ==========================================
    public function getLogAktivitas(Request $request)
    {
        $query = ActivityLog::query();
        if ($request->filled('modul')) {
            $query->where('modul', $request->modul);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('deskripsi', 'like', "%{$q}%")
                    ->orWhere('user_name', 'like', "%{$q}%");
            });
        }
        return response()->json($query->latest()->paginate(20));
    }

    // ==========================================
    // 15. PENGATURAN & USER RBAC
    // ==========================================
    public function getPengaturan()
    {
        return response()->json([
            'pengaturan' => Pengaturan::first(),
            'users' => User::all(),
        ]);
    }
}
