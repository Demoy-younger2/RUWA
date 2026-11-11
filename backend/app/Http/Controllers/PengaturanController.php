<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pengaturan = Pengaturan::first() ?? new Pengaturan();
        $users = User::paginate(10);

        $roles = [
            'superadmin' => 'Superadmin',
            'rw' => 'Ketua RW',
            'rt' => 'Ketua RT',
            'sekretaris_rw' => 'Sekretaris RW',
            'bendahara_rw' => 'Bendahara RW',
            'sekretaris_rt' => 'Sekretaris RT',
            'bendahara_rt' => 'Bendahara RT',
            'warga' => 'Warga',
        ];

        return view('pengaturan.index', compact('user', 'pengaturan', 'users', 'roles'));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'min:6', 'confirmed'],
        ]);

        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.']);
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->save();

        ActivityLog::record('Pengaturan', 'Ubah', 'Pengguna ' . $user->name . ' memperbarui profil akun atau kata sandi.');

        return redirect()->route('pengaturan.index')->with('success', 'Profil akun Anda berhasil diperbarui.');
    }

    public function updateRw(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['superadmin', 'rw'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'nama_aplikasi' => ['required', 'string', 'max:255'],
            'nama_rw' => ['required', 'string', 'max:50'],
            'nama_kelurahan' => ['required', 'string', 'max:100'],
            'nama_kecamatan' => ['required', 'string', 'max:100'],
            'nama_kota' => ['required', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'nama_ketua_rw' => ['required', 'string', 'max:255'],
            'sambutan_rw' => ['nullable', 'string'],
            'kontak_rw' => ['nullable', 'string', 'max:25'],
            'email_rw' => ['nullable', 'email', 'max:100'],
            'alamat_sekretariat' => ['nullable', 'string'],
        ]);

        $pengaturan = Pengaturan::first();
        if ($pengaturan) {
            $pengaturan->update($validated);
        } else {
            Pengaturan::create($validated);
        }

        ActivityLog::record('Pengaturan', 'Ubah', 'Pengurus (' . $user->name . ') memperbarui konfigurasi data wilayah dan sekretariat ' . $validated['nama_rw'] . '.');

        return redirect()->route('pengaturan.index')->with('success', 'Informasi lingkungan RW dan instansi berhasil disimpan.');
    }

    public function createUser(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Hanya superadmin yang dapat membuat user baru secara langsung.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:superadmin,rw,rt,sekretaris_rw,bendahara_rw,sekretaris_rt,bendahara_rt,warga'],
            'rt' => ['nullable', 'string', 'max:5'],
            'password' => ['required', 'min:6'],
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'rt' => $validated['rt'] ?? '01',
            'rw' => '05',
            'password' => Hash::make($validated['password']),
            'status' => 'aktif',
        ]);

        ActivityLog::record('Pengaturan', 'Tambah', 'Superadmin menambahkan akun pengguna baru: ' . $validated['name'] . ' (' . $validated['email'] . ') dengan role ' . $validated['role'] . '.');

        return redirect()->route('pengaturan.index')->with('success', 'Pengguna baru berhasil ditambahkan dengan role ' . $validated['role']);
    }

    public function updateRoleUser(Request $request, User $targetUser)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Akses dibatasi untuk Superadmin.');
        }

        $validated = $request->validate([
            'role' => ['required', 'in:superadmin,rw,rt,sekretaris_rw,bendahara_rw,sekretaris_rt,bendahara_rt,warga'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $targetUser->update($validated);

        ActivityLog::record('Pengaturan', 'Ubah', 'Superadmin mengubah role akun ' . $targetUser->name . ' menjadi ' . $validated['role'] . ' (Status: ' . $validated['status'] . ').');

        return redirect()->back()->with('success', 'Hak akses & status pengguna ' . $targetUser->name . ' berhasil diperbarui.');
    }
}
