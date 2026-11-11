<?php

use App\Http\Controllers\AsetController;
use App\Http\Controllers\AspirasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankSampahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KoperasiController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PosyanduController;
use App\Http\Controllers\RondaController;
use App\Http\Controllers\RukemController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::post('/cek-surat', [WelcomeController::class, 'checkSurat'])->name('welcome.check_surat');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated System Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Data Warga (CRUD & Hak Akses)
    Route::resource('warga', WargaController::class);

    // 3. Surat Menyurat
    Route::resource('surat', SuratController::class);
    Route::post('/surat/{surat}/approve-rt', [SuratController::class, 'approveRT'])->name('surat.approve_rt');
    Route::post('/surat/{surat}/approve-rw', [SuratController::class, 'approveRW'])->name('surat.approve_rw');
    Route::post('/surat/{surat}/reject', [SuratController::class, 'reject'])->name('surat.reject');
    Route::get('/surat/{surat}/cetak', [SuratController::class, 'cetak'])->name('surat.cetak');

    // 4. Kas Keuangan
    Route::resource('kas', KasController::class)->except(['edit', 'update']);

    // 5. Keamanan & Ronda
    Route::resource('ronda', RondaController::class)->except(['edit', 'update', 'show']);
    Route::post('/ronda/{ronda}/presensi', [RondaController::class, 'presensi'])->name('ronda.presensi');
    Route::post('/ronda/panic-button', [RondaController::class, 'panicButton'])->name('ronda.panic_button');

    // 6. Bank Sampah
    Route::get('/bank-sampah', [BankSampahController::class, 'index'])->name('bank-sampah.index');
    Route::post('/bank-sampah/setor', [BankSampahController::class, 'setor'])->name('bank-sampah.setor');
    Route::post('/bank-sampah/tarik-saldo', [BankSampahController::class, 'tarikSaldo'])->name('bank-sampah.tarik_saldo');
    Route::post('/bank-sampah/kategori', [BankSampahController::class, 'storeKategori'])->name('bank-sampah.kategori.store');

    // 7. UMKM
    Route::resource('umkm', UmkmController::class);

    // 8. Posyandu
    Route::resource('posyandu', PosyanduController::class)->except(['edit', 'update']);

    // 9. Kegiatan
    Route::resource('kegiatan', KegiatanController::class);

    // 10. RUKEM (Rukun Kematian)
    Route::resource('rukem', RukemController::class)->except(['edit', 'update', 'show']);
    Route::post('/rukem/{rukem}/serahkan', [RukemController::class, 'serahkanSantunan'])->name('rukem.serahkan');

    // 11. Aspirasi & Pengaduan
    Route::resource('aspirasi', AspirasiController::class)->except(['edit', 'update', 'show']);
    Route::post('/aspirasi/{aspirasi}/tanggapi', [AspirasiController::class, 'tanggapi'])->name('aspirasi.tanggapi');

    // 12. Aset & Peminjaman
    Route::resource('aset', AsetController::class)->except(['edit', 'update', 'show']);
    Route::post('/aset/pinjam', [AsetController::class, 'pinjam'])->name('aset.pinjam');
    Route::match(['post', 'patch'], '/aset/peminjaman/{peminjaman}/update-status', [AsetController::class, 'updateStatusPinjam'])->name('aset.update_pinjam');

    // 13. Koperasi
    Route::get('/koperasi', [KoperasiController::class, 'index'])->name('koperasi.index');
    Route::post('/koperasi/daftar', [KoperasiController::class, 'daftarAnggota'])->name('koperasi.daftar');
    Route::post('/koperasi/transaksi', [KoperasiController::class, 'transaksi'])->name('koperasi.transaksi');

    // 14. Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/profil', [PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
    Route::post('/pengaturan/rw', [PengaturanController::class, 'updateRw'])->name('pengaturan.rw');
    Route::post('/pengaturan/user', [PengaturanController::class, 'createUser'])->name('pengaturan.user');
    Route::match(['post', 'patch'], '/pengaturan/user/{targetUser}/role', [PengaturanController::class, 'updateRoleUser'])->name('pengaturan.user.role');

    // 15. Log Aktivitas & Audit Trail
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log-aktivitas.index');
    Route::delete('/log-aktivitas/{activity_log}', [LogAktivitasController::class, 'destroy'])->name('log-aktivitas.destroy');
    Route::post('/log-aktivitas/clear-all', [LogAktivitasController::class, 'clearAll'])->name('log-aktivitas.clear_all');
});
