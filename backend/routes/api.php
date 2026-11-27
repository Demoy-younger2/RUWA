<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\RfidController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes - SiRW Digital
|--------------------------------------------------------------------------
*/

// 1. Publik & Auth
Route::get('/stats', [ApiController::class, 'getPublicStats']);
Route::get('/cek-surat', [ApiController::class, 'checkSurat']);
Route::post('/auth/login', [ApiController::class, 'login']);
Route::post('/auth/quick-login/{role}', [ApiController::class, 'quickLogin']);
Route::get('/dashboard', [ApiController::class, 'getDashboard']);

// 2. Kependudukan & Surat
Route::get('/warga', [ApiController::class, 'getWarga']);
Route::post('/warga', [ApiController::class, 'storeWarga']);
Route::get('/surat', [ApiController::class, 'getSurat']);
Route::post('/surat', [ApiController::class, 'storeSurat']);
Route::post('/surat/{id}/approve-rt', [ApiController::class, 'approveRtSurat']);
Route::post('/surat/{id}/approve-rw', [ApiController::class, 'approveRwSurat']);

// 3. Kas Keuangan (Pemasukan, Pengeluaran, Semua Transaksi)
Route::get('/kas', [ApiController::class, 'getKas']);
Route::post('/kas', [ApiController::class, 'storeKas']);
Route::put('/kas/{id}', [ApiController::class, 'updateKas']);
Route::delete('/kas/{id}', [ApiController::class, 'deleteKas']);

// 4. Kategori Kas
Route::get('/kategori-kas', [ApiController::class, 'getKategoriKas']);
Route::post('/kategori-kas', [ApiController::class, 'storeKategoriKas']);
Route::put('/kategori-kas/{id}', [ApiController::class, 'updateKategoriKas']);
Route::delete('/kategori-kas/{id}', [ApiController::class, 'deleteKategoriKas']);

// 5. Kelola Tagihan Warga
Route::get('/tagihan-warga', [ApiController::class, 'getTagihanWarga']);
Route::post('/tagihan-warga', [ApiController::class, 'storeTagihanWarga']);
Route::post('/tagihan-warga/generate', [ApiController::class, 'generateTagihanMassal']);
Route::post('/tagihan-warga/{id}/bayar', [ApiController::class, 'bayarTagihanWarga']);
Route::post('/tagihan-warga/{id}/verifikasi', [ApiController::class, 'verifikasiTagihanWarga']);
Route::put('/tagihan-warga/{id}', [ApiController::class, 'updateTagihanWarga']);
Route::delete('/tagihan-warga/{id}', [ApiController::class, 'deleteTagihanWarga']);

// 6. Kelola Rekening & QRIS
Route::get('/rekening-kas', [ApiController::class, 'getRekeningKas']);
Route::post('/rekening-kas', [ApiController::class, 'storeRekeningKas']);
Route::put('/rekening-kas/{id}', [ApiController::class, 'updateRekeningKas']);
Route::delete('/rekening-kas/{id}', [ApiController::class, 'deleteRekeningKas']);

// 7. Ronda & Panic Button
Route::get('/ronda', [ApiController::class, 'getRonda']);
Route::post('/ronda/panic-button', [ApiController::class, 'triggerPanic']);

// 8. Bank Sampah & UMKM
Route::get('/bank-sampah', [ApiController::class, 'getBankSampah']);
Route::post('/bank-sampah/setor', [ApiController::class, 'setorSampah']);
Route::get('/umkm', [ApiController::class, 'getUmkm']);
Route::post('/umkm', [ApiController::class, 'storeUmkm']);

// 9. Posyandu & Kegiatan
Route::get('/posyandu', [ApiController::class, 'getPosyandu']);
Route::post('/posyandu', [ApiController::class, 'storePosyandu']);
Route::get('/kegiatan', [ApiController::class, 'getKegiatan']);
Route::post('/kegiatan', [ApiController::class, 'storeKegiatan']);

// 10. RUKEM & Aspirasi
Route::get('/rukem', [ApiController::class, 'getRukem']);
Route::get('/aspirasi', [ApiController::class, 'getAspirasi']);
Route::post('/aspirasi', [ApiController::class, 'storeAspirasi']);
Route::post('/aspirasi/{id}/tanggapi', [ApiController::class, 'tanggapiAspirasi']);

// 11. Aset & Koperasi
Route::get('/aset', [ApiController::class, 'getAset']);
Route::post('/aset/pinjam', [ApiController::class, 'pinjamAset']);
Route::get('/koperasi', [ApiController::class, 'getKoperasi']);

// 12. Log Aktivitas & Pengaturan
Route::get('/log-aktivitas', [ApiController::class, 'getLogAktivitas']);
Route::get('/pengaturan', [ApiController::class, 'getPengaturan']);

// 13. IoT RFID / NFC ESP32 & Manajemen Kartu e-KTP
Route::post('/rfid/tap', [RfidController::class, 'tap']);
Route::post('/rfid/heartbeat', [RfidController::class, 'heartbeat']);
Route::post('/rfid/register-session', [RfidController::class, 'startRegisterSession']);
Route::get('/rfid/status-session', [RfidController::class, 'checkRegisterSession']);
Route::post('/rfid/cancel-session', [RfidController::class, 'cancelRegisterSession']);
Route::post('/rfid/manual-bind', [RfidController::class, 'manualRegister']);
Route::post('/rfid/unbind', [RfidController::class, 'unbindCard']);
Route::get('/rfid/logs', [RfidController::class, 'getLogs']);
Route::match(['get', 'post'], '/iot/rfid/scan', [RfidController::class, 'tap']);
Route::match(['get', 'post'], '/iot/rfid/heartbeat', [RfidController::class, 'heartbeat']);
Route::get('/rfid/device-status', [RfidController::class, 'getDeviceStatus']);
Route::post('/iot/rfid/login', [RfidController::class, 'loginByRfid']);
