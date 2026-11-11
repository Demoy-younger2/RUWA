<?php

use App\Http\Controllers\Api\ApiController;
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

// 3. Kas & Ronda
Route::get('/kas', [ApiController::class, 'getKas']);
Route::post('/kas', [ApiController::class, 'storeKas']);
Route::get('/ronda', [ApiController::class, 'getRonda']);
Route::post('/ronda/panic-button', [ApiController::class, 'triggerPanic']);

// 4. Bank Sampah & UMKM
Route::get('/bank-sampah', [ApiController::class, 'getBankSampah']);
Route::post('/bank-sampah/setor', [ApiController::class, 'setorSampah']);
Route::get('/umkm', [ApiController::class, 'getUmkm']);
Route::post('/umkm', [ApiController::class, 'storeUmkm']);

// 5. Posyandu & Kegiatan
Route::get('/posyandu', [ApiController::class, 'getPosyandu']);
Route::post('/posyandu', [ApiController::class, 'storePosyandu']);
Route::get('/kegiatan', [ApiController::class, 'getKegiatan']);
Route::post('/kegiatan', [ApiController::class, 'storeKegiatan']);

// 6. RUKEM & Aspirasi
Route::get('/rukem', [ApiController::class, 'getRukem']);
Route::get('/aspirasi', [ApiController::class, 'getAspirasi']);
Route::post('/aspirasi', [ApiController::class, 'storeAspirasi']);
Route::post('/aspirasi/{id}/tanggapi', [ApiController::class, 'tanggapiAspirasi']);

// 7. Aset & Koperasi
Route::get('/aset', [ApiController::class, 'getAset']);
Route::post('/aset/pinjam', [ApiController::class, 'pinjamAset']);
Route::get('/koperasi', [ApiController::class, 'getKoperasi']);

// 8. Log Aktivitas & Pengaturan
Route::get('/log-aktivitas', [ApiController::class, 'getLogAktivitas']);
Route::get('/pengaturan', [ApiController::class, 'getPengaturan']);
