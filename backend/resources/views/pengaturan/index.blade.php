@extends('layouts.app', ['title' => 'Pengaturan & Manajemen Sistem'])

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'profil' }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-gears text-slate-700"></i>
                <span>Pengaturan Sistem & Profil</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola akun personal Anda, identitas lingkungan kepengurusan RW, dan hak akses pengguna.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-900 text-white flex items-center gap-1.5">
                <i class="fa-solid fa-user-shield text-amber-400"></i>
                <span>Role Aktif: {{ Auth::user()->role_title }}</span>
            </span>
        </div>
    </div>

    <!-- Tab Navigation Pills -->
    <div class="flex flex-wrap items-center gap-2 p-1.5 bg-slate-100 rounded-2xl max-w-xl">
        <button type="button" @click="activeTab = 'profil'" :class="activeTab === 'profil' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="flex-1 py-2 px-4 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-user"></i>
            <span>Profil Akun</span>
        </button>

        @if(in_array(Auth::user()->role, ['superadmin', 'rw']))
            <button type="button" @click="activeTab = 'rw'" :class="activeTab === 'rw' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="flex-1 py-2 px-4 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-landmark"></i>
                <span>Identitas RW</span>
            </button>
        @endif

        @if(Auth::user()->isSuperAdmin())
            <button type="button" @click="activeTab = 'users'" :class="activeTab === 'users' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="flex-1 py-2 px-4 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-users-gear"></i>
                <span>Manajemen User</span>
            </button>
        @endif
    </div>

    <!-- TAB 1: PROFIL & KEAMANAN AKUN -->
    <div x-show="activeTab === 'profil'" class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
            <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-2xl font-extrabold shadow-md">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-500 font-mono">{{ $user->email }} • NIK: {{ $user->nik ?? 'Belum Diisi' }}</p>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            {{ $user->role_title }}
                        </span>
                        <span class="text-[11px] text-slate-400">RT {{ $user->rt ?? '01' }} / RW {{ $user->rw ?? '05' }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('pengaturan.profil') }}" class="mt-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition">
                        @error('email')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Nomor WhatsApp / HP
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition">
                        @error('phone')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Role Akses (Ditetapkan Superadmin)
                        </label>
                        <div class="px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm font-bold text-slate-700">
                            {{ $user->role_title }}
                        </div>
                    </div>
                </div>

                <!-- Ganti Password Section -->
                <div class="pt-6 border-t border-slate-100 space-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-lock text-slate-500 text-sm"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Ubah Kata Sandi (Kosongkan bila tidak ingin diganti)</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label for="current_password" class="block text-xs font-bold text-slate-600">Kata Sandi Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            @error('current_password')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="new_password" class="block text-xs font-bold text-slate-600">Kata Sandi Baru</label>
                            <input type="password" id="new_password" name="new_password" minlength="6" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            @error('new_password')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="new_password_confirmation" class="block text-xs font-bold text-slate-600">Konfirmasi Sandi Baru</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan Akun</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 2: PENGATURAN IDENTITAS RW (SUPERADMIN & RW) -->
    @if(in_array(Auth::user()->role, ['superadmin', 'rw']))
        <div x-show="activeTab === 'rw'" class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
                <div class="pb-6 border-b border-slate-100">
                    <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-landmark text-emerald-600"></i>
                        <span>Konfigurasi Lembaga & Wilayah RW</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Data identitas ini digunakan pada kop surat resmi, halaman depan, dan informasi instansi rukun warga.</p>
                </div>

                <form method="POST" action="{{ route('pengaturan.rw') }}" class="mt-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="nama_aplikasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nama Aplikasi Portal <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama_aplikasi" name="nama_aplikasi" value="{{ old('nama_aplikasi', $pengaturan->nama_aplikasi ?? 'Sistem Informasi Rukun Warga RW 05') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label for="nama_rw" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nomor / Nama RW <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama_rw" name="nama_rw" value="{{ old('nama_rw', $pengaturan->nama_rw ?? 'RW 05') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div class="space-y-1.5">
                            <label for="nama_kelurahan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Kelurahan / Desa <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama_kelurahan" name="nama_kelurahan" value="{{ old('nama_kelurahan', $pengaturan->nama_kelurahan ?? 'Sukamaju') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label for="nama_kecamatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Kecamatan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama_kecamatan" name="nama_kecamatan" value="{{ old('nama_kecamatan', $pengaturan->nama_kecamatan ?? 'Cilodong') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label for="nama_kota" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Kota / Kabupaten <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama_kota" name="nama_kota" value="{{ old('nama_kota', $pengaturan->nama_kota ?? 'Kota Depok') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label for="kode_pos" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Kode Pos
                            </label>
                            <input type="text" id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $pengaturan->kode_pos ?? '16415') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label for="nama_ketua_rw" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nama Ketua RW <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama_ketua_rw" name="nama_ketua_rw" value="{{ old('nama_ketua_rw', $pengaturan->nama_ketua_rw ?? 'H. Bambang Sutrisno, S.E.') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label for="kontak_rw" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Hotline WhatsApp RW
                            </label>
                            <input type="text" id="kontak_rw" name="kontak_rw" value="{{ old('kontak_rw', $pengaturan->kontak_rw ?? '081298765432') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label for="email_rw" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Email Resmi Sekretariat
                            </label>
                            <input type="email" id="email_rw" name="email_rw" value="{{ old('email_rw', $pengaturan->email_rw ?? 'sekretariat@rw05sukamaju.id') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="alamat_sekretariat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Alamat Balai Warga / Posko Sekretariat RW
                        </label>
                        <input type="text" id="alamat_sekretariat" name="alamat_sekretariat" value="{{ old('alamat_sekretariat', $pengaturan->alamat_sekretariat ?? 'Balai Warga RW 05, Jl. Melati Raya No. 1, Kel. Sukamaju') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label for="sambutan_rw" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pesan & Sambutan Ketua RW (Tampil di Landing Page)
                        </label>
                        <textarea id="sambutan_rw" name="sambutan_rw" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">{{ old('sambutan_rw', $pengaturan->sambutan_rw ?? 'Selamat datang di Portal Digital Rukun Warga RW 05. Mari bersama kita ciptakan lingkungan hunian yang guyub, aman, sehat, dan modern.') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end pt-4">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Identitas RW</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- TAB 3: MANAJEMEN USER & RBAC (SUPERADMIN ONLY) -->
    @if(Auth::user()->isSuperAdmin())
        <div x-show="activeTab === 'users'" class="space-y-6">

            <!-- Card Tambah Akun Pengguna Baru -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8" x-data="{ openForm: false }">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-user-plus text-emerald-600"></i>
                            <span>Tambah Akun Pengurus / Warga</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Buat kredensial akun baru langsung dengan role yang ditentukan.</p>
                    </div>
                    <button type="button" @click="openForm = !openForm" class="px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white text-xs font-bold transition">
                        <span x-text="openForm ? 'Tutup Form' : '+ Tambah User'"></span>
                    </button>
                </div>

                <div x-show="openForm" class="mt-6 pt-6 border-t border-slate-100">
                    <form method="POST" action="{{ route('pengaturan.user') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" required placeholder="Contoh: Ahmad Fadli" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Email Login <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" required placeholder="contoh@warga.id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Kata Sandi <span class="text-rose-500">*</span></label>
                                <input type="password" name="password" minlength="6" required placeholder="Minimal 6 karakter" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pilih Role Akses <span class="text-rose-500">*</span></label>
                                <select name="role" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none bg-white">
                                    @foreach($roles as $key => $title)
                                        <option value="{{ $key }}">{{ $title }} ({{ $key }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Wilayah RT</label>
                                <select name="rt" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none bg-white">
                                    <option value="01">RT 01</option>
                                    <option value="02">RT 02</option>
                                    <option value="03">RT 03</option>
                                    <option value="04">RT 04</option>
                                    <option value="05">RT 05</option>
                                    <option value="06">RT 06</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20">
                                Buat Akun Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Daftar Seluruh Pengguna Sistem -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-users text-slate-700"></i>
                            <span>Daftar Seluruh Akun Pengguna (RBAC)</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Atur hak akses role (8 roles) dan status aktif tiap pengguna</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                <th class="py-3 px-3">Nama & Email</th>
                                <th class="py-3 px-3">Wilayah</th>
                                <th class="py-3 px-3">Role Saat Ini</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3 text-right">Ubah Role & Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($users as $u)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3">
                                        <span class="font-bold text-slate-900 block">{{ $u->name }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-semibold text-slate-700">RT {{ $u->rt ?? '-' }} / RW {{ $u->rw ?? '-' }}</span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $u->isSuperAdmin() ? 'bg-purple-100 text-purple-800' : ($u->isRw() ? 'bg-indigo-100 text-indigo-800' : ($u->isRt() ? 'bg-blue-100 text-blue-800' : ($u->isBendahara() ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700'))) }}">
                                            {{ $u->role_title }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $u->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ ucfirst($u->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <form method="POST" action="{{ route('pengaturan.user.role', $u->id) }}" class="inline-flex items-center gap-1.5">
                                            @csrf
                                            <select name="role" class="px-2 py-1 rounded-lg border border-slate-200 text-[11px] font-bold bg-white outline-none">
                                                @foreach($roles as $rKey => $rTitle)
                                                    <option value="{{ $rKey }}" {{ $u->role === $rKey ? 'selected' : '' }}>{{ $rTitle }}</option>
                                                @endforeach
                                            </select>
                                            <select name="status" class="px-2 py-1 rounded-lg border border-slate-200 text-[11px] font-bold bg-white outline-none">
                                                <option value="aktif" {{ $u->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                <option value="nonaktif" {{ $u->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                            </select>
                                            <button type="submit" class="px-2 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-bold hover:bg-slate-800 transition">
                                                Simpan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    @endif

</div>
@endsection
