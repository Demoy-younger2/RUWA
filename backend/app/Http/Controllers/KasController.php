<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Kas;
use App\Models\KategoriKas;
use App\Models\RekeningKas;
use App\Models\TagihanWarga;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasController extends Controller
{
    /**
     * Semua Transaksi (Buku Kas Umum)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Kas::with(['user', 'tagihan']);

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        } elseif (in_array($user->role, ['rt', 'sekretaris_rt', 'bendahara_rt'])) {
            $query->where('tingkat', 'RT')->where('rt', $user->rt ?? '01');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('keterangan', 'like', "%{$s}%")
                  ->orWhere('pihak_terkait', 'like', "%{$s}%");
            });
        }

        $kasList = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();

        $totalPemasukan = Kas::when($request->filled('tingkat'), fn($q) => $q->where('tingkat', $request->tingkat))
            ->where('jenis', 'pemasukan')->sum('jumlah');

        $totalPengeluaran = Kas::when($request->filled('tingkat'), fn($q) => $q->where('tingkat', $request->tingkat))
            ->where('jenis', 'pengeluaran')->sum('jumlah');

        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        $kategoriList = KategoriKas::where('is_aktif', true)->pluck('nama')->toArray();
        if (empty($kategoriList)) {
            $kategoriList = ['Iuran Warga Bulanan', 'Iuran Kebersihan Lingkungan', 'Iuran Keamanan & Ronda', 'Kas RW', 'Kas RT', 'Dana Sosial', 'Operasional Ronda'];
        }

        $activeSubmenu = 'semua';
        return view('kas.index', compact('kasList', 'totalPemasukan', 'totalPengeluaran', 'saldoAkhir', 'kategoriList', 'activeSubmenu'));
    }

    /**
     * Menu Pemasukan Kas
     */
    public function pemasukan(Request $request)
    {
        $request->merge(['jenis' => 'pemasukan']);
        $user = Auth::user();
        $query = Kas::with(['user', 'tagihan'])->where('jenis', 'pemasukan');

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        } elseif (in_array($user->role, ['rt', 'sekretaris_rt', 'bendahara_rt'])) {
            $query->where('tingkat', 'RT')->where('rt', $user->rt ?? '01');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $kasList = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();
        $totalPemasukan = $query->sum('jumlah');
        $totalPengeluaran = 0;
        $saldoAkhir = $totalPemasukan;

        $kategoriList = KategoriKas::where('jenis', 'pemasukan')->where('is_aktif', true)->pluck('nama')->toArray();
        $activeSubmenu = 'pemasukan';

        return view('kas.index', compact('kasList', 'totalPemasukan', 'totalPengeluaran', 'saldoAkhir', 'kategoriList', 'activeSubmenu'));
    }

    /**
     * Menu Pengeluaran Kas
     */
    public function pengeluaran(Request $request)
    {
        $request->merge(['jenis' => 'pengeluaran']);
        $user = Auth::user();
        $query = Kas::with(['user', 'tagihan'])->where('jenis', 'pengeluaran');

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        } elseif (in_array($user->role, ['rt', 'sekretaris_rt', 'bendahara_rt'])) {
            $query->where('tingkat', 'RT')->where('rt', $user->rt ?? '01');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $kasList = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();
        $totalPengeluaran = $query->sum('jumlah');
        $totalPemasukan = 0;
        $saldoAkhir = -$totalPengeluaran;

        $kategoriList = KategoriKas::where('jenis', 'pengeluaran')->where('is_aktif', true)->pluck('nama')->toArray();
        $activeSubmenu = 'pengeluaran';

        return view('kas.index', compact('kasList', 'totalPemasukan', 'totalPengeluaran', 'saldoAkhir', 'kategoriList', 'activeSubmenu'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt'])) {
            return redirect()->route('kas.index')->with('error', 'Hanya bendahara/pengurus yang dapat mencatat transaksi kas.');
        }

        $kategoriList = KategoriKas::where('is_aktif', true)->get();
        return view('kas.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'tingkat' => ['required', 'in:RW,RT'],
            'rt' => ['nullable', 'string', 'max:5'],
            'kategori' => ['required', 'string'],
            'jenis' => ['required', 'in:pemasukan,pengeluaran'],
            'jumlah' => ['required', 'numeric', 'min:1000'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string'],
            'pihak_terkait' => ['nullable', 'string', 'max:150'],
            'metode' => ['nullable', 'string', 'max:50'],
        ]);

        Kas::create([
            'tingkat' => $validated['tingkat'],
            'rt' => $validated['rt'] ?? ($user->rt ?? '01'),
            'rw' => $user->rw ?? '05',
            'user_id' => $user->id,
            'kategori' => $validated['kategori'],
            'jenis' => $validated['jenis'],
            'jumlah' => $validated['jumlah'],
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'],
            'pihak_terkait' => $validated['pihak_terkait'] ?? ($validated['jenis'] === 'pemasukan' ? 'Warga' : 'Pihak Terkait'),
            'metode' => $validated['metode'] ?? 'Tunai',
        ]);

        ActivityLog::record(
            'Kas',
            'Transaksi',
            'Mencatat ' . strtoupper($validated['jenis']) . ' kas ' . $validated['tingkat'] . ' sebesar Rp ' . number_format($validated['jumlah'], 0, ',', '.') . ' (' . $validated['kategori'] . ' - ' . $validated['keterangan'] . ').'
        );

        $redirectRoute = $validated['jenis'] === 'pemasukan' ? 'kas.pemasukan' : ($validated['jenis'] === 'pengeluaran' ? 'kas.pengeluaran' : 'kas.index');
        return redirect()->route($redirectRoute)->with('success', 'Transaksi kas berhasil dicatat.');
    }

    public function destroy(Kas $ka)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        ActivityLog::record('Kas', 'Hapus', 'Menghapus catatan kas ' . $ka->tingkat . ' senilai Rp ' . number_format($ka->jumlah, 0, ',', '.') . ' (' . $ka->keterangan . ').');
        $ka->delete();
        return redirect()->back()->with('success', 'Catatan kas berhasil dihapus.');
    }

    /**
     * Kelola Kategori Kas
     */
    public function kategori(Request $request)
    {
        $kategoriList = KategoriKas::orderBy('jenis')->orderBy('nama')->get();
        return view('kas.kategori', compact('kategoriList'));
    }

    public function storeKategori(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Hanya bendahara/pengurus yang dapat menambah kategori.');
        }

        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'tingkat' => 'required|in:RW,RT',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        KategoriKas::create($data);
        ActivityLog::record('Kas', 'Kategori', 'Menambahkan kategori kas: ' . $data['nama']);
        return redirect()->route('kas.kategori')->with('success', 'Kategori kas berhasil ditambahkan.');
    }

    public function destroyKategori($id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'bendahara_rw'])) {
            abort(403, 'Hanya bendahara RW/superadmin yang dapat menghapus kategori.');
        }

        $kat = KategoriKas::findOrFail($id);
        ActivityLog::record('Kas', 'Kategori', 'Menghapus kategori kas: ' . $kat->nama);
        $kat->delete();
        return redirect()->route('kas.kategori')->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * Kelola Tagihan Warga
     */
    public function tagihan(Request $request)
    {
        $user = Auth::user();
        $query = TagihanWarga::with(['user', 'rekening'])->latest('id');

        if ($user->role === 'warga') {
            $query->where('user_id', $user->id);
        } elseif (in_array($user->role, ['rt', 'sekretaris_rt', 'bendahara_rt'])) {
            $query->where('rt', $user->rt ?? '01');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tagihans = $query->paginate(15)->withQueryString();
        $wargas = Warga::orderBy('nama_lengkap')->get();
        $rekenings = RekeningKas::where('is_aktif', true)->get();

        return view('kas.tagihan', compact('tagihans', 'wargas', 'rekenings'));
    }

    public function storeTagihan(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $data = $request->validate([
            'judul' => 'required|string|max:200',
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'user_id' => 'nullable|exists:users,id',
            'nama_warga' => 'required|string|max:150',
            'periode' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:5000',
            'kategori' => 'required|string|max:100',
            'tanggal_jatuh_tempo' => 'nullable|date',
        ]);

        $count = TagihanWarga::count() + 1;
        $kode = 'TGH-' . now()->format('Ym') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        TagihanWarga::create(array_merge($data, [
            'kode_tagihan' => $kode,
            'rt' => $data['rt'] ?? ($user->rt ?? '01'),
            'status' => 'belum_bayar',
        ]));

        ActivityLog::record('Kas', 'Tagihan', 'Menerbitkan tagihan ' . $kode . ' untuk ' . $data['nama_warga']);
        return redirect()->route('kas.tagihan')->with('success', 'Tagihan warga berhasil diterbitkan.');
    }

    public function generateTagihan(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $data = $request->validate([
            'judul' => 'required|string|max:200',
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'periode' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:5000',
            'kategori' => 'required|string|max:100',
        ]);

        $wargaQuery = Warga::query();
        if ($data['tingkat'] === 'RT' && !empty($data['rt'])) {
            $wargaQuery->where('rt', $data['rt']);
        }
        $wargaList = $wargaQuery->get();

        $count = 0;
        foreach ($wargaList as $w) {
            $kode = 'TGH-' . now()->format('Ym') . '-' . str_pad(TagihanWarga::count() + 1, 4, '0', STR_PAD_LEFT);
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
                'tanggal_jatuh_tempo' => now()->addDays(20)->toDateString(),
            ]);
            $count++;
        }

        ActivityLog::record('Kas', 'Tagihan Massal', "Generate {$count} tagihan massal untuk warga.");
        return redirect()->route('kas.tagihan')->with('success', "Berhasil menerbitkan {$count} tagihan warga.");
    }

    public function verifikasiTagihan(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $tagihan = TagihanWarga::findOrFail($id);
        $status = $request->input('status', 'lunas');

        if ($status === 'lunas') {
            $tagihan->update([
                'status' => 'lunas',
                'diverifikasi_oleh' => $user->name,
                'tanggal_bayar' => now()->toDateString(),
            ]);

            // Catat otomatis ke Kas
            Kas::create([
                'tingkat' => $tagihan->tingkat,
                'rt' => $tagihan->rt,
                'rw' => '05',
                'user_id' => $tagihan->user_id ?? $user->id,
                'kategori' => $tagihan->kategori,
                'jenis' => 'pemasukan',
                'jumlah' => $tagihan->nominal,
                'tanggal' => now()->toDateString(),
                'keterangan' => 'Pembayaran tagihan ' . $tagihan->kode_tagihan . ' (' . $tagihan->nama_warga . ')',
                'pihak_terkait' => $tagihan->nama_warga,
                'metode' => $tagihan->metode_pembayaran ?? 'Transfer Bank',
                'tagihan_id' => $tagihan->id,
            ]);

            ActivityLog::record('Kas', 'Verifikasi', "Verifikasi Lunas tagihan {$tagihan->kode_tagihan} ({$tagihan->nama_warga}).");
            return redirect()->route('kas.tagihan')->with('success', 'Tagihan diverifikasi LUNAS dan dicatat ke kas masuk.');
        } else {
            $tagihan->update(['status' => 'belum_bayar', 'catatan' => 'Pembayaran ditolak / bukti tidak sesuai.']);
            ActivityLog::record('Kas', 'Tolak Bayar', "Menolak bukti pembayaran tagihan {$tagihan->kode_tagihan}.");
            return redirect()->route('kas.tagihan')->with('info', 'Pembayaran tagihan ditolak.');
        }
    }

    public function destroyTagihan($id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'bendahara_rw'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $t = TagihanWarga::findOrFail($id);
        $kode = $t->kode_tagihan;
        $t->delete();

        ActivityLog::record('Kas', 'Hapus Tagihan', "Menghapus tagihan {$kode}.");
        return redirect()->route('kas.tagihan')->with('success', 'Tagihan berhasil dihapus.');
    }

    /**
     * Kelola Rekening & QRIS
     */
    public function rekening(Request $request)
    {
        $rekeningList = RekeningKas::orderBy('tingkat')->orderBy('id')->get();
        return view('kas.rekening', compact('rekeningList'));
    }

    public function storeRekening(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $data = $request->validate([
            'tingkat' => 'required|in:RW,RT',
            'rt' => 'nullable|string|max:5',
            'nama_bank' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:100',
            'atas_nama' => 'required|string|max:150',
            'tipe' => 'required|in:bank,qris,ewallet',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($data['tipe'] === 'qris') {
            $encodedText = urlencode('QRIS-' . $data['tingkat'] . '-' . $data['nomor_rekening']);
            $data['qris_image'] = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$encodedText}";
        }

        RekeningKas::create($data);
        ActivityLog::record('Kas', 'Rekening & QRIS', 'Menambahkan rekening kas: ' . $data['nama_bank']);
        return redirect()->route('kas.rekening')->with('success', 'Rekening/QRIS berhasil disimpan.');
    }

    public function destroyRekening($id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'bendahara_rw'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $r = RekeningKas::findOrFail($id);
        $nama = $r->nama_bank;
        $r->delete();

        ActivityLog::record('Kas', 'Rekening & QRIS', "Menghapus rekening {$nama}.");
        return redirect()->route('kas.rekening')->with('success', 'Rekening berhasil dihapus.');
    }
}
