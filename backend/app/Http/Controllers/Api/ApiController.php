<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Aset;
use App\Models\Aspirasi;
use App\Models\BankSampah;
use App\Models\Kas;
use App\Models\KategoriKas;
use App\Models\Kegiatan;
use App\Models\KoperasiAnggota;
use App\Models\KoperasiTransaksi;
use App\Models\PeminjamanAset;
use App\Models\Pengaturan;
use App\Models\Posyandu;
use App\Models\RekeningKas;
use App\Models\Ronda;
use App\Models\Rukem;
use App\Models\Surat;
use App\Models\TagihanWarga;
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
                'rfid_uid' => $user->rfid_uid,
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
                'rfid_uid' => $user->rfid_uid,
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

    // ========================================================
    // 6. KAS KEUANGAN (PEMASUKAN, PENGELUARAN, SEMUA TRANSAKSI)
    // ========================================================
    public function getKas(Request $request)
    {
        $query = Kas::with(['user', 'tagihan'])->latest('tanggal')->latest('id');

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }
        if ($request->filled('rt')) {
            $query->where('rt', $request->rt);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhere('pihak_terkait', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }

        $kasList = $query->get();

        $totalPemasukan = Kas::where('jenis', 'pemasukan')
            ->when($request->filled('tingkat'), fn($q) => $q->where('tingkat', $request->tingkat))
            ->when($request->filled('rt'), fn($q) => $q->where('rt', $request->rt))
            ->sum('jumlah');

        $totalPengeluaran = Kas::where('jenis', 'pengeluaran')
            ->when($request->filled('tingkat'), fn($q) => $q->where('tingkat', $request->tingkat))
            ->when($request->filled('rt'), fn($q) => $q->where('rt', $request->rt))
            ->sum('jumlah');

        $kategoriPemasukan = KategoriKas::where('jenis', 'pemasukan')->where('is_aktif', true)->pluck('nama');
        $kategoriPengeluaran = KategoriKas::where('jenis', 'pengeluaran')->where('is_aktif', true)->pluck('nama');
        $rekenings = RekeningKas::where('is_aktif', true)->get();

        return response()->json([
            'kas' => $kasList,
            'total_pemasukan' => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'saldo' => $totalPemasukan - $totalPengeluaran,
            'kategori_pemasukan' => $kategoriPemasukan,
            'kategori_pengeluaran' => $kategoriPengeluaran,
            'rekenings' => $rekenings,
        ]);
    }

    public function storeKas(Request $request)
    {
        $data = $request->validate([
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'kategori' => 'required|string',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'jumlah' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'pihak_terkait' => 'nullable|string|max:255',
            'metode' => 'nullable|string|max:50',
            'bukti_transaksi' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $userId = $data['user_id'] ?? (Auth::id() ?? 1);
        $kas = Kas::create(array_merge($data, [
            'user_id' => $userId,
            'rt' => $data['rt'] ?? '01',
            'rw' => '05',
            'pihak_terkait' => $data['pihak_terkait'] ?? ($data['jenis'] === 'pemasukan' ? 'Warga' : 'Pihak Terkait'),
            'metode' => $data['metode'] ?? 'Tunai',
        ]));

        ActivityLog::record(
            'Kas',
            'Transaksi',
            'Pencatatan kas ' . strtoupper($data['jenis']) . ' ' . $data['tingkat'] . ' sebesar Rp ' . number_format($data['jumlah'], 0, ',', '.') . ' (' . $data['kategori'] . ' - ' . $data['keterangan'] . ').'
        );

        return response()->json(['message' => 'Transaksi ' . $data['jenis'] . ' berhasil dicatat!', 'kas' => $kas]);
    }

    public function updateKas(Request $request, $id)
    {
        $kas = Kas::findOrFail($id);

        $data = $request->validate([
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'kategori' => 'required|string',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'jumlah' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'pihak_terkait' => 'nullable|string|max:255',
            'metode' => 'nullable|string|max:50',
            'bukti_transaksi' => 'nullable|string',
        ]);

        $kas->update($data);

        ActivityLog::record(
            'Kas',
            'Edit',
            'Memperbarui transaksi kas ' . strtoupper($kas->jenis) . ' #' . $kas->id . ' senilai Rp ' . number_format($kas->jumlah, 0, ',', '.') . ' (' . $kas->kategori . ').'
        );

        return response()->json(['message' => 'Transaksi kas berhasil diperbarui!', 'kas' => $kas]);
    }

    public function deleteKas(Request $request, $id)
    {
        $kas = Kas::findOrFail($id);
        $ket = $kas->keterangan;
        $jumlah = $kas->jumlah;
        $jenis = $kas->jenis;
        $kas->delete();

        ActivityLog::record(
            'Kas',
            'Hapus',
            'Menghapus transaksi ' . strtoupper($jenis) . ' senilai Rp ' . number_format($jumlah, 0, ',', '.') . ' (' . $ket . ').'
        );

        return response()->json(['message' => 'Transaksi kas berhasil dihapus!']);
    }

    // ========================================================
    // 6.1 MASTER KATEGORI KAS
    // ========================================================
    public function getKategoriKas(Request $request)
    {
        $query = KategoriKas::orderBy('jenis')->orderBy('nama');
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        $kategoris = $query->get()->map(function ($kat) {
            $kat->total_transaksi = Kas::where('kategori', $kat->nama)->count();
            $kat->total_nominal = Kas::where('kategori', $kat->nama)->sum('jumlah');
            return $kat;
        });

        return response()->json($kategoris);
    }

    public function storeKategoriKas(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'tingkat' => 'required|in:RW,RT',
            'deskripsi' => 'nullable|string|max:255',
            'is_aktif' => 'nullable|boolean',
        ]);

        $data['is_aktif'] = $request->has('is_aktif') ? (bool) $request->is_aktif : true;
        $kategori = KategoriKas::create($data);

        ActivityLog::record('Kas', 'Kategori', 'Menambahkan kategori kas baru: ' . $kategori->nama . ' (' . strtoupper($kategori->jenis) . ').');
        return response()->json(['message' => 'Kategori kas berhasil ditambahkan!', 'kategori' => $kategori]);
    }

    public function updateKategoriKas(Request $request, $id)
    {
        $kategori = KategoriKas::findOrFail($id);
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'tingkat' => 'required|in:RW,RT',
            'deskripsi' => 'nullable|string|max:255',
            'is_aktif' => 'nullable|boolean',
        ]);

        $kategori->update($data);
        ActivityLog::record('Kas', 'Kategori', 'Memperbarui kategori kas: ' . $kategori->nama . '.');
        return response()->json(['message' => 'Kategori kas berhasil diperbarui!', 'kategori' => $kategori]);
    }

    public function deleteKategoriKas(Request $request, $id)
    {
        $kategori = KategoriKas::findOrFail($id);
        $nama = $kategori->nama;
        $kategori->delete();

        ActivityLog::record('Kas', 'Kategori', 'Menghapus kategori kas: ' . $nama . '.');
        return response()->json(['message' => 'Kategori kas berhasil dihapus!']);
    }

    // ========================================================
    // 6.2 KELOLA TAGIHAN WARGA
    // ========================================================
    public function getTagihanWarga(Request $request)
    {
        $query = TagihanWarga::with(['user', 'rekening'])->latest('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        if ($request->filled('rt')) {
            $query->where('rt', $request->rt);
        }
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_warga', 'like', "%{$search}%")
                  ->orWhere('kode_tagihan', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%");
            });
        }

        $tagihans = $query->get();

        $stats = [
            'total_tagihan' => TagihanWarga::count(),
            'total_lunas' => TagihanWarga::where('status', 'lunas')->count(),
            'total_menunggu_verifikasi' => TagihanWarga::where('status', 'menunggu_verifikasi')->count(),
            'total_belum_bayar' => TagihanWarga::where('status', 'belum_bayar')->count(),
            'nominal_terkumpul' => TagihanWarga::where('status', 'lunas')->sum('nominal'),
            'nominal_tertunggak' => TagihanWarga::where('status', '!=', 'lunas')->sum('nominal'),
        ];

        $wargas = Warga::select('id', 'user_id', 'nama_lengkap', 'nik', 'no_kk', 'rt', 'rw', 'alamat')->get();
        $rekenings = RekeningKas::where('is_aktif', true)->get();

        return response()->json([
            'tagihans' => $tagihans,
            'stats' => $stats,
            'wargas' => $wargas,
            'rekenings' => $rekenings,
        ]);
    }

    public function storeTagihanWarga(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:200',
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'user_id' => 'nullable|exists:users,id',
            'nama_warga' => 'required|string|max:150',
            'nik' => 'nullable|string|max:20',
            'nomor_rumah' => 'nullable|string|max:50',
            'periode' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:5000',
            'kategori' => 'required|string|max:100',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $countThisMonth = TagihanWarga::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        $kodeTagihan = 'TGH-' . now()->format('Ym') . '-' . str_pad($countThisMonth, 3, '0', STR_PAD_LEFT);

        $tagihan = TagihanWarga::create(array_merge($data, [
            'kode_tagihan' => $kodeTagihan,
            'rt' => $data['rt'] ?? '01',
            'status' => 'belum_bayar',
        ]));

        ActivityLog::record(
            'Kas',
            'Tagihan',
            'Menerbitkan tagihan ' . $tagihan->kode_tagihan . ' untuk ' . $tagihan->nama_warga . ' sebesar Rp ' . number_format($tagihan->nominal, 0, ',', '.') . ' (' . $tagihan->judul . ').'
        );

        return response()->json(['message' => 'Tagihan warga berhasil dibuat!', 'tagihan' => $tagihan]);
    }

    public function generateTagihanMassal(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:200',
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'periode' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:5000',
            'kategori' => 'required|string|max:100',
            'tanggal_jatuh_tempo' => 'nullable|date',
        ]);

        $wargaQuery = Warga::query();
        if ($data['tingkat'] === 'RT' && !empty($data['rt']) && $data['rt'] !== 'all') {
            $wargaQuery->where('rt', $data['rt']);
        }

        $wargaList = $wargaQuery->get();
        if ($wargaList->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data warga ditemukan untuk kriteria RT tersebut.'], 422);
        }

        $createdCount = 0;
        $prefix = 'TGH-' . now()->format('Ym') . '-';
        $baseCount = TagihanWarga::count();

        foreach ($wargaList as $index => $w) {
            $existing = TagihanWarga::where('periode', $data['periode'])
                ->where('kategori', $data['kategori'])
                ->where(function ($q) use ($w) {
                    if ($w->user_id) {
                        $q->where('user_id', $w->user_id);
                    } else {
                        $q->where('nama_warga', $w->nama_lengkap);
                    }
                })->first();

            if (!$existing) {
                $baseCount++;
                $kode = $prefix . str_pad($baseCount, 4, '0', STR_PAD_LEFT);

                TagihanWarga::create([
                    'kode_tagihan' => $kode,
                    'judul' => $data['judul'],
                    'tingkat' => $data['tingkat'],
                    'rt' => $w->rt ?? ($data['rt'] ?? '01'),
                    'user_id' => $w->user_id,
                    'nama_warga' => $w->nama_lengkap,
                    'nik' => $w->nik,
                    'nomor_rumah' => $w->alamat ?? 'RT ' . ($w->rt ?? '01'),
                    'periode' => $data['periode'],
                    'nominal' => $data['nominal'],
                    'kategori' => $data['kategori'],
                    'status' => 'belum_bayar',
                    'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'] ?? now()->addDays(20)->toDateString(),
                ]);
                $createdCount++;
            }
        }

        ActivityLog::record(
            'Kas',
            'Tagihan Massal',
            'Men-generate ' . $createdCount . ' tagihan massal (' . $data['judul'] . ') periode ' . $data['periode'] . ' nominal Rp ' . number_format($data['nominal'], 0, ',', '.') . '.'
        );

        return response()->json([
            'message' => "Berhasil men-generate {$createdCount} tagihan warga secara massal!",
            'created_count' => $createdCount,
        ]);
    }

    public function bayarTagihanWarga(Request $request, $id)
    {
        $tagihan = TagihanWarga::findOrFail($id);

        $data = $request->validate([
            'metode_pembayaran' => 'required|string',
            'rekening_id' => 'nullable|exists:rekening_kas,id',
            'tanggal_bayar' => 'required|date',
            'bukti_bayar' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $tagihan->update([
            'status' => 'menunggu_verifikasi',
            'metode_pembayaran' => $data['metode_pembayaran'],
            'rekening_id' => $data['rekening_id'] ?? null,
            'tanggal_bayar' => $data['tanggal_bayar'],
            'bukti_bayar' => $data['bukti_bayar'] ?? 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&auto=format&fit=crop&q=60',
            'catatan' => $data['catatan'] ?? 'Warga telah melakukan pembayaran dan melampirkan konfirmasi.',
        ]);

        ActivityLog::record(
            'Kas',
            'Konfirmasi Bayar',
            'Warga ' . $tagihan->nama_warga . ' mengonfirmasi pembayaran tagihan ' . $tagihan->kode_tagihan . ' (' . $tagihan->judul . ') via ' . $data['metode_pembayaran'] . '.'
        );

        return response()->json(['message' => 'Konfirmasi pembayaran berhasil dikirim! Menunggu verifikasi bendahara.', 'tagihan' => $tagihan]);
    }

    public function verifikasiTagihanWarga(Request $request, $id)
    {
        $tagihan = TagihanWarga::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:lunas,belum_bayar',
            'petugas' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        if ($data['status'] === 'lunas') {
            $tagihan->update([
                'status' => 'lunas',
                'diverifikasi_oleh' => $data['petugas'],
                'tanggal_bayar' => $tagihan->tanggal_bayar ?? now()->toDateString(),
                'catatan' => $data['catatan'] ?? 'Pembayaran telah diverifikasi valid oleh bendahara.',
            ]);

            // Otomatis catat ke kas pemasukan
            Kas::create([
                'tingkat' => $tagihan->tingkat,
                'rt' => $tagihan->rt,
                'rw' => '05',
                'user_id' => $tagihan->user_id ?? 1,
                'kategori' => $tagihan->kategori,
                'jenis' => 'pemasukan',
                'jumlah' => $tagihan->nominal,
                'tanggal' => $tagihan->tanggal_bayar ?? now()->toDateString(),
                'keterangan' => 'Pembayaran ' . $tagihan->judul . ' oleh ' . $tagihan->nama_warga . ' (' . $tagihan->kode_tagihan . ')',
                'pihak_terkait' => $tagihan->nama_warga,
                'metode' => $tagihan->metode_pembayaran ?? 'Transfer Bank',
                'tagihan_id' => $tagihan->id,
                'bukti_transaksi' => $tagihan->bukti_bayar,
            ]);

            ActivityLog::record(
                'Kas',
                'Verifikasi Tagihan',
                $data['petugas'] . ' memverifikasi LUNAS tagihan ' . $tagihan->kode_tagihan . ' (' . $tagihan->nama_warga . ') sebesar Rp ' . number_format($tagihan->nominal, 0, ',', '.') . '.'
            );

            return response()->json(['message' => 'Tagihan berhasil diverifikasi LUNAS dan dicatat ke arus kas!', 'tagihan' => $tagihan]);
        } else {
            $tagihan->update([
                'status' => 'belum_bayar',
                'catatan' => $data['catatan'] ?? 'Pembayaran ditolak / bukti transfer tidak valid.',
            ]);

            ActivityLog::record(
                'Kas',
                'Tolak Tagihan',
                $data['petugas'] . ' menolak bukti pembayaran tagihan ' . $tagihan->kode_tagihan . ' (' . $tagihan->nama_warga . ').'
            );

            return response()->json(['message' => 'Pembayaran tagihan ditolak.', 'tagihan' => $tagihan]);
        }
    }

    public function updateTagihanWarga(Request $request, $id)
    {
        $tagihan = TagihanWarga::findOrFail($id);
        $data = $request->validate([
            'judul' => 'required|string|max:200',
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'nama_warga' => 'required|string|max:150',
            'nomor_rumah' => 'nullable|string|max:50',
            'periode' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:5000',
            'kategori' => 'required|string|max:100',
            'status' => 'required|in:belum_bayar,menunggu_verifikasi,lunas',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $tagihan->update($data);
        ActivityLog::record('Kas', 'Edit Tagihan', 'Memperbarui tagihan ' . $tagihan->kode_tagihan . ' (' . $tagihan->nama_warga . ').');
        return response()->json(['message' => 'Tagihan berhasil diperbarui!', 'tagihan' => $tagihan]);
    }

    public function deleteTagihanWarga(Request $request, $id)
    {
        $tagihan = TagihanWarga::findOrFail($id);
        $kode = $tagihan->kode_tagihan;
        $tagihan->delete();

        ActivityLog::record('Kas', 'Hapus Tagihan', 'Menghapus tagihan ' . $kode . '.');
        return response()->json(['message' => 'Tagihan berhasil dihapus!']);
    }

    // ========================================================
    // 6.3 KELOLA REKENING & QRIS
    // ========================================================
    public function getRekeningKas(Request $request)
    {
        $query = RekeningKas::orderBy('tingkat')->orderBy('id');
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }
        if ($request->filled('rt')) {
            $query->where('rt', $request->rt);
        }
        if ($request->filled('is_aktif')) {
            $query->where('is_aktif', (bool) $request->is_aktif);
        }

        $rekenings = $query->get();
        return response()->json($rekenings);
    }

    public function storeRekeningKas(Request $request)
    {
        $data = $request->validate([
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'nama_bank' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:100',
            'atas_nama' => 'required|string|max:150',
            'tipe' => 'required|in:bank,qris,ewallet',
            'qris_image' => 'nullable|string',
            'keterangan' => 'nullable|string|max:255',
            'is_aktif' => 'nullable|boolean',
        ]);

        if ($data['tipe'] === 'qris' && empty($data['qris_image'])) {
            $encodedText = urlencode('QRIS-' . $data['tingkat'] . '-' . $data['nomor_rekening']);
            $data['qris_image'] = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$encodedText}";
        }

        $data['is_aktif'] = $request->has('is_aktif') ? (bool) $request->is_aktif : true;
        $rekening = RekeningKas::create($data);

        ActivityLog::record(
            'Kas',
            'Rekening & QRIS',
            'Menambahkan ' . strtoupper($rekening->tipe) . ' baru: ' . $rekening->nama_bank . ' (' . $rekening->nomor_rekening . ' a/n ' . $rekening->atas_nama . ').'
        );

        return response()->json(['message' => 'Rekening / QRIS berhasil disimpan!', 'rekening' => $rekening]);
    }

    public function updateRekeningKas(Request $request, $id)
    {
        $rekening = RekeningKas::findOrFail($id);

        $data = $request->validate([
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'nama_bank' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:100',
            'atas_nama' => 'required|string|max:150',
            'tipe' => 'required|in:bank,qris,ewallet',
            'qris_image' => 'nullable|string',
            'keterangan' => 'nullable|string|max:255',
            'is_aktif' => 'nullable|boolean',
        ]);

        if ($data['tipe'] === 'qris' && empty($data['qris_image']) && empty($rekening->qris_image)) {
            $encodedText = urlencode('QRIS-' . $data['tingkat'] . '-' . $data['nomor_rekening']);
            $data['qris_image'] = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$encodedText}";
        }

        $rekening->update($data);

        ActivityLog::record(
            'Kas',
            'Rekening & QRIS',
            'Memperbarui rekening / QRIS: ' . $rekening->nama_bank . ' (' . $rekening->nomor_rekening . ').'
        );

        return response()->json(['message' => 'Rekening / QRIS berhasil diperbarui!', 'rekening' => $rekening]);
    }

    public function deleteRekeningKas(Request $request, $id)
    {
        $rekening = RekeningKas::findOrFail($id);
        $info = $rekening->nama_bank . ' (' . $rekening->nomor_rekening . ')';
        $rekening->delete();

        ActivityLog::record('Kas', 'Rekening & QRIS', 'Menghapus rekening / QRIS: ' . $info . '.');
        return response()->json(['message' => 'Rekening / QRIS berhasil dihapus!']);
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
