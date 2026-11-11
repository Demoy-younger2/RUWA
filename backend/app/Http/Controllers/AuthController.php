<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $demoUsers = [
            'superadmin' => [
                'role' => 'Superadmin',
                'name' => 'Bambang Pratama',
                'email' => 'superadmin@rukunwarga.id',
                'desc' => 'Akses penuh seluruh sistem & pengaturan',
                'icon' => 'shield-check',
                'color' => 'from-purple-600 to-indigo-700',
            ],
            'rw' => [
                'role' => 'Ketua RW',
                'name' => 'H. Suryana, S.Pd.',
                'email' => 'rw@rukunwarga.id',
                'desc' => 'Persetujuan akhir surat, rekap RT & Kas RW',
                'icon' => 'building-library',
                'color' => 'from-blue-600 to-cyan-700',
            ],
            'rt' => [
                'role' => 'Ketua RT 01',
                'name' => 'Agus Budiman',
                'email' => 'rt01@rukunwarga.id',
                'desc' => 'Verifikasi warga, surat RT 01 & ronda',
                'icon' => 'home-modern',
                'color' => 'from-emerald-600 to-teal-700',
            ],
            'sekretaris_rw' => [
                'role' => 'Sekretaris RW',
                'name' => 'Dedi Iskandar',
                'email' => 'sekretaris.rw@rukunwarga.id',
                'desc' => 'Administrasi arsip surat, agenda & notulen RW',
                'icon' => 'document-text',
                'color' => 'from-amber-600 to-yellow-700',
            ],
            'bendahara_rw' => [
                'role' => 'Bendahara RW',
                'name' => 'Hj. Ratna Sari',
                'email' => 'bendahara.rw@rukunwarga.id',
                'desc' => 'Pencatatan kas RW & laporan keuangan',
                'icon' => 'banknotes',
                'color' => 'from-teal-600 to-emerald-800',
            ],
            'sekretaris_rt' => [
                'role' => 'Sekretaris RT 01',
                'name' => 'Eko Prasetyo',
                'email' => 'sekretaris.rt@rukunwarga.id',
                'desc' => 'Administrasi warga RT & presensi ronda',
                'icon' => 'pencil-square',
                'color' => 'from-orange-600 to-amber-700',
            ],
            'bendahara_rt' => [
                'role' => 'Bendahara RT 01',
                'name' => 'Rina Wulandari',
                'email' => 'bendahara.rt@rukunwarga.id',
                'desc' => 'Pengelolaan iuran RT 01 & kas bulanan',
                'icon' => 'wallet',
                'color' => 'from-green-600 to-lime-700',
            ],
            'warga' => [
                'role' => 'Warga (RT 01)',
                'name' => 'Budi Santoso',
                'email' => 'warga@rukunwarga.id',
                'desc' => 'Permohonan surat, lapor ronda, bank sampah & umkm',
                'icon' => 'user-group',
                'color' => 'from-indigo-600 to-sky-700',
            ],
        ];

        return view('auth.login', compact('demoUsers'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            ActivityLog::record('Auth', 'Login', 'Pengguna ' . $user->name . ' berhasil masuk ke sistem (' . $user->role_title . ').', $user);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . $user->name . ' (' . $user->role_title . ')!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function quickLogin($role)
    {
        $user = User::where('role', $role)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Akun demo untuk role tersebut tidak ditemukan.');
        }

        Auth::login($user);
        request()->session()->regenerate();

        ActivityLog::record('Auth', 'Login', 'Quick Login sebagai ' . $user->role_title . ' (' . $user->name . ').', $user);

        return redirect()->route('dashboard')
            ->with('success', 'Berhasil masuk secara instan sebagai ' . $user->name . ' (' . $user->role_title . ')!');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'size:16', 'unique:users,nik', 'unique:wargas,nik'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'rt' => ['required', 'string', 'max:5'],
            'rw' => ['required', 'string', 'max:5'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'warga',
            'nik' => $validated['nik'],
            'phone' => $validated['phone'],
            'rt' => $validated['rt'],
            'rw' => $validated['rw'],
            'status' => 'aktif',
        ]);

        Warga::create([
            'user_id' => $user->id,
            'nik' => $validated['nik'],
            'nama_lengkap' => $validated['name'],
            'no_hp' => $validated['phone'],
            'rt' => $validated['rt'],
            'rw' => $validated['rw'],
            'status_tinggal' => 'Tetap',
        ]);

        Auth::login($user);

        ActivityLog::record('Auth', 'Register', 'Warga baru melakukan pendaftaran mandiri: ' . $user->name . ' (NIK: ' . $user->nik . ').', $user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Akun warga Anda telah aktif.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::record('Auth', 'Logout', 'Pengguna ' . $user->name . ' keluar dari sesi sistem.', $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
