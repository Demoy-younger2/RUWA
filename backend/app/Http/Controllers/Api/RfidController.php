<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\RfidScan;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RfidController extends Controller
{
    /**
     * Cache key prefix untuk sesi pendaftaran kartu RFID
     */
    const SESSION_CACHE_KEY = 'rfid_active_registration_session';

    /**
     * Format dan bersihkan string UID (hanya alphanumeric, uppercase)
     */
    protected function cleanUid(?string $uid): string
    {
        if (!$uid) return '';
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $uid));
    }

    /**
     * 1. Mulai Sesi Pendaftaran Kartu e-KTP / RFID untuk Pengguna yang Sedang Login
     * Dipanggil oleh Frontend saat pengguna mengklik "Mulai Scan Kartu"
     */
    public function startRegisterSession(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan. Silakan login terlebih dahulu.',
            ], 404);
        }

        $sessionData = [
            'status' => 'waiting', // waiting, registered, rejected_duplicate, error
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'rfid_uid' => null,
            'error_message' => null,
            'started_at' => now()->toISOString(),
            'expires_at' => now()->addSeconds(90)->toISOString(),
        ];

        // Simpan sesi aktif ke Cache selama 90 detik
        Cache::put(self::SESSION_CACHE_KEY, $sessionData, 90);

        return response()->json([
            'success' => true,
            'message' => 'Sesi scan RFID aktif! Silakan tempelkan e-KTP / Kartu ke alat ESP32.',
            'session' => $sessionData,
        ]);
    }

    /**
     * 2. Periksa Status Sesi Pendaftaran (Polling dari Frontend Web)
     */
    public function checkRegisterSession()
    {
        $session = Cache::get(self::SESSION_CACHE_KEY);

        if (!$session) {
            return response()->json([
                'status' => 'idle',
                'message' => 'Tidak ada sesi scan kartu yang aktif.',
            ]);
        }

        return response()->json([
            'status' => $session['status'],
            'user_id' => $session['user_id'],
            'user_name' => $session['user_name'],
            'rfid_uid' => $session['rfid_uid'] ?? null,
            'error_message' => $session['error_message'] ?? null,
            'message' => match ($session['status']) {
                'waiting' => 'Menunggu kartu e-KTP/RFID ditempelkan ke ESP32...',
                'registered' => 'Kartu berhasil didaftarkan!',
                'rejected_duplicate' => $session['error_message'] ?? 'Kartu sudah terdaftar pada akun lain!',
                'error' => $session['error_message'] ?? 'Terjadi kesalahan pemindaian.',
                default => 'Status tidak diketahui.',
            },
        ]);
    }

    /**
     * 3. Batalkan Sesi Pendaftaran
     */
    public function cancelRegisterSession()
    {
        Cache::forget(self::SESSION_CACHE_KEY);

        return response()->json([
            'success' => true,
            'message' => 'Sesi pemindaian berhasil dibatalkan.',
        ]);
    }

    /**
     * 4. Pendaftaran Kartu Manual / Direct (Bisa via Web Form atau Scanner USB)
     * VALIDASI: UID tidak boleh terdaftar pada akun lain!
     */
    public function manualRegister(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rfid_uid' => 'required|string|min:4|max:50',
        ]);

        $uid = $this->cleanUid($request->input('rfid_uid'));
        $userId = $request->input('user_id');
        $user = User::findOrFail($userId);

        // ATURAN PENTING: Cek apakah UID sudah terdaftar pada akun lain!
        $existing = User::where('rfid_uid', $uid)->where('id', '!=', $userId)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => "Kartu/e-KTP (UID: {$uid}) SUDAH TERDAFTAR pada akun lain: {$existing->name} ({$existing->email}) dan TIDAK BISA digunakan!",
            ], 422);
        }

        // Simpan ke User
        $user->rfid_uid = $uid;
        $user->rfid_registered_at = now();
        $user->save();

        // Sinkronisasi ke data Warga jika terhubung
        Warga::where('user_id', $user->id)->orWhere('nik', $user->nik)->update([
            'rfid_uid' => $uid,
        ]);

        // Catat Scan Log
        RfidScan::create([
            'user_id' => $user->id,
            'rfid_uid' => $uid,
            'device_id' => $request->input('device_id', 'WEB-MANUAL'),
            'status' => 'registered',
            'action' => 'register',
            'message' => "Pendaftaran UID {$uid} berhasil untuk pengguna {$user->name}",
        ]);

        ActivityLog::record(
            'RFID IoT',
            'Registrasi Kartu',
            "Pengguna {$user->name} berhasil mendaftarkan kartu RFID/e-KTP UID: {$uid}.",
            $user
        );

        return response()->json([
            'success' => true,
            'message' => "Kartu e-KTP (UID: {$uid}) berhasil didaftarkan untuk {$user->name}!",
            'rfid_uid' => $uid,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'rfid_uid' => $user->rfid_uid,
            ],
        ]);
    }

    /**
     * 5. IoT ESP32 Endpoint (Menerima Tap Kartu dari ESP32 via HTTP POST / GET)
     * Mendukung pemindaian dari modul RC522 / PN532 pada ESP32
     */
    public function handleEspScan(Request $request)
    {
        $rawUid = $request->input('uid') ?? $request->query('uid');
        $deviceId = $request->input('device_id') ?? $request->query('device_id', 'ESP32-READER-01');

        if (!$rawUid) {
            return response()->json([
                'success' => false,
                'code' => 'EMPTY_UID',
                'message' => 'Parameter UID kartu tidak ditemukan.',
            ], 400);
        }

        $uid = $this->cleanUid($rawUid);

        // Ambil sesi pendaftaran aktif jika ada
        $activeSession = Cache::get(self::SESSION_CACHE_KEY);

        // =========================================================================
        // SKENARIO A: Ada Sesi Pendaftaran Aktif di Web (User sedang scan untuk akunnya)
        // =========================================================================
        if ($activeSession && $activeSession['status'] === 'waiting') {
            $targetUserId = $activeSession['user_id'];
            $targetUser = User::find($targetUserId);

            if (!$targetUser) {
                Cache::forget(self::SESSION_CACHE_KEY);
                return response()->json([
                    'success' => false,
                    'code' => 'TARGET_USER_NOT_FOUND',
                    'message' => 'Pengguna target pendaftaran tidak ditemukan.',
                ], 404);
            }

            // ATURAN KRUSIAL: Cek apakah UID sudah terdaftar pada akun lain!
            $duplicateUser = User::where('rfid_uid', $uid)->where('id', '!=', $targetUserId)->first();

            if ($duplicateUser) {
                $errorMsg = "Kartu (UID: {$uid}) DITOLAK! Sudah terdaftar pada akun: {$duplicateUser->name} ({$duplicateUser->role_title})";

                // Update status di cache agar modal di frontend langsung bereaksi menampilkan pesan merah
                $activeSession['status'] = 'rejected_duplicate';
                $activeSession['rfid_uid'] = $uid;
                $activeSession['error_message'] = $errorMsg;
                Cache::put(self::SESSION_CACHE_KEY, $activeSession, 60);

                // Catat di log
                RfidScan::create([
                    'user_id' => $targetUserId,
                    'rfid_uid' => $uid,
                    'device_id' => $deviceId,
                    'status' => 'rejected_duplicate',
                    'action' => 'register_attempt',
                    'message' => "Percobaan pendaftaran kartu gagal: UID sudah milik {$duplicateUser->name}",
                ]);

                return response()->json([
                    'success' => false,
                    'code' => 'DUPLICATE_CARD',
                    'message' => $errorMsg,
                    'registered_to' => $duplicateUser->name,
                ], 409);
            }

            // Jika kartu sudah terdaftar di akun yang sama
            if ($targetUser->rfid_uid === $uid) {
                $activeSession['status'] = 'registered';
                $activeSession['rfid_uid'] = $uid;
                Cache::put(self::SESSION_CACHE_KEY, $activeSession, 60);

                return response()->json([
                    'success' => true,
                    'code' => 'ALREADY_OWNED',
                    'message' => "Kartu ini sudah terdaftar sebelumnya pada akun {$targetUser->name}.",
                    'user' => $targetUser->name,
                    'uid' => $uid,
                ]);
            }

            // SUKSES: Kartu valid & belum terdaftar di akun manapun -> Daftarkan!
            $targetUser->rfid_uid = $uid;
            $targetUser->rfid_registered_at = now();
            $targetUser->save();

            // Update warga jika ada
            Warga::where('user_id', $targetUser->id)->orWhere('nik', $targetUser->nik)->update([
                'rfid_uid' => $uid,
            ]);

            // Update status sesi agar modal frontend mendeteksi sukses
            $activeSession['status'] = 'registered';
            $activeSession['rfid_uid'] = $uid;
            Cache::put(self::SESSION_CACHE_KEY, $activeSession, 60);

            // Log
            RfidScan::create([
                'user_id' => $targetUser->id,
                'rfid_uid' => $uid,
                'device_id' => $deviceId,
                'status' => 'registered',
                'action' => 'register',
                'message' => "Kartu UID {$uid} sukses didaftarkan untuk {$targetUser->name} via ESP32 ({$deviceId})",
            ]);

            ActivityLog::record(
                'RFID IoT',
                'Registrasi e-KTP ESP32',
                "Kartu e-KTP UID: {$uid} berhasil didaftarkan untuk {$targetUser->name} melalui perangkat IoT {$deviceId}.",
                $targetUser
            );

            return response()->json([
                'success' => true,
                'code' => 'REGISTRATION_SUCCESS',
                'message' => "Kartu berhasil terdaftar untuk {$targetUser->name}!",
                'user' => $targetUser->name,
                'uid' => $uid,
            ]);
        }

        // =========================================================================
        // SKENARIO B: Tidak Ada Sesi Pendaftaran (Mode Tap / Identifikasi / Presensi)
        // =========================================================================
        $registeredUser = User::where('rfid_uid', $uid)->first();

        if ($registeredUser) {
            RfidScan::create([
                'user_id' => $registeredUser->id,
                'rfid_uid' => $uid,
                'device_id' => $deviceId,
                'status' => 'recognized',
                'action' => 'tap_access',
                'message' => "Akses diterima untuk {$registeredUser->name} ({$registeredUser->role_title})",
            ]);

            return response()->json([
                'success' => true,
                'code' => 'ACCESS_GRANTED',
                'message' => "Selamat Datang, {$registeredUser->name}",
                'user' => [
                    'id' => $registeredUser->id,
                    'name' => $registeredUser->name,
                    'role' => $registeredUser->role,
                    'role_title' => $registeredUser->role_title,
                    'rt' => $registeredUser->rt,
                    'rw' => $registeredUser->rw,
                    'nik' => $registeredUser->nik,
                ],
                'uid' => $uid,
            ]);
        }

        // Kartu belum terdaftar
        RfidScan::create([
            'user_id' => null,
            'rfid_uid' => $uid,
            'device_id' => $deviceId,
            'status' => 'unregistered',
            'action' => 'tap_unknown',
            'message' => "Kartu UID {$uid} ditempelkan tetapi belum terdaftar di sistem",
        ]);

        return response()->json([
            'success' => false,
            'code' => 'UNREGISTERED_CARD',
            'message' => "Kartu UID: {$uid} belum terdaftar. Silakan daftarkan melalui akun web SiRW.",
            'uid' => $uid,
        ], 404);
    }

    /**
     * 6. Lepas / Hapus Pendaftaran Kartu (Unbind)
     */
    public function unbindCard(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->input('user_id'));
        $oldUid = $user->rfid_uid;

        $user->rfid_uid = null;
        $user->rfid_registered_at = null;
        $user->save();

        Warga::where('user_id', $user->id)->orWhere('nik', $user->nik)->update([
            'rfid_uid' => null,
        ]);

        ActivityLog::record(
            'RFID IoT',
            'Lepas Kartu',
            "Pengguna {$user->name} melepaskan pendaftaran kartu RFID (UID lama: {$oldUid}).",
            $user
        );

        return response()->json([
            'success' => true,
            'message' => 'Kartu RFID/e-KTP berhasil dilepas dari akun.',
        ]);
    }

    /**
     * 7. Login Cepat Menggunakan Kartu RFID / e-KTP
     */
    public function loginByRfid(Request $request)
    {
        $rawUid = $request->input('uid');
        if (!$rawUid) {
            return response()->json([
                'success' => false,
                'message' => 'UID kartu wajib dikirim.',
            ], 400);
        }

        $uid = $this->cleanUid($rawUid);
        $user = User::where('rfid_uid', $uid)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Kartu e-KTP (UID: {$uid}) belum terdaftar pada akun manapun.",
            ], 404);
        }

        ActivityLog::record(
            'Auth',
            'Login RFID',
            "Pengguna {$user->name} berhasil login menggunakan kartu e-KTP (UID: {$uid}).",
            $user
        );

        return response()->json([
            'success' => true,
            'message' => "Login berhasil! Selamat datang, {$user->name}.",
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

    /**
     * 8. Riwayat Log Pindaian RFID
     */
    public function getLogs()
    {
        $logs = RfidScan::with('user:id,name,role,rt,rw')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'logs' => $logs,
        ]);
    }
}
