<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Masuk - SiRW Digital (Sistem Rukun Warga)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-full flex flex-col justify-center py-8 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-slate-800">

    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        <!-- Logo & Title -->
        <div class="text-center">
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-xl shadow-emerald-500/30 font-bold text-2xl">
                    <i class="fa-solid fa-users-line"></i>
                </div>
            </a>
            <h2 class="mt-4 text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                SiRW <span class="text-emerald-400">Digital</span>
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-300">
                Portal Pelayanan Warga RW 05 Kelurahan Sukamaju
            </p>
        </div>

        <!-- Notification Alerts -->
        @if(session('info'))
            <div class="mt-4 p-3 rounded-xl bg-blue-500/10 border border-blue-400/30 text-blue-200 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-info"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="mt-4 p-3 rounded-xl bg-amber-500/10 border border-amber-400/30 text-amber-200 text-xs flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mt-4 p-3 rounded-xl bg-rose-500/10 border border-rose-400/30 text-rose-200 text-xs">
                @foreach($errors->all() as $err)
                    <p class="flex items-center gap-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <!-- Card Form Login -->
        <div class="mt-6 bg-white/95 backdrop-blur-md py-6 px-6 sm:px-8 shadow-2xl rounded-3xl border border-white/20">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700">Alamat Email</label>
                    <div class="mt-1 relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-envelope text-xs"></i>
                        </div>
                        <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" placeholder="nama@rukunwarga.id" class="block w-full pl-10 pr-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700">Kata Sandi</label>
                    <div class="mt-1 relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </div>
                        <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••" class="block w-full pl-10 pr-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span>Ingat saya</span>
                    </label>
                    <span class="text-slate-400 text-[11px]">Default pass: <code class="font-bold text-slate-600">password</code></span>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-md shadow-emerald-600/30 transition">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Masuk ke Dashboard</span>
                    </button>
                </div>
            </form>

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Warga baru?</span>
                <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:text-emerald-700">Daftar Akun Warga</a>
            </div>
        </div>

        <!-- Quick 1-Click Role Switcher Demo Box (8 Roles) -->
        <div class="mt-6 bg-slate-800/80 backdrop-blur-md p-4 sm:p-5 rounded-3xl border border-slate-700 text-white shadow-xl">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold tracking-wide uppercase text-emerald-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-bolt"></i>
                    <span>Quick Login (8 Role Uji Coba)</span>
                </h3>
                <span class="text-[10px] text-slate-400">1-Klik Langsung Masuk</span>
            </div>
            <p class="text-[11px] text-slate-300 mb-3">
                Klik salah satu tombol peran di bawah untuk menguji hak akses menu dan tampilan secara instan:
            </p>

            <div class="grid grid-cols-2 gap-2">
                @foreach($demoUsers as $key => $demo)
                    <a href="{{ route('quick.login', $key) }}" class="group relative flex flex-col p-2.5 rounded-2xl bg-slate-900/80 hover:bg-slate-700/80 border border-slate-700 hover:border-emerald-500 transition text-left">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-bold text-white group-hover:text-emerald-300 truncate">{{ $demo['role'] }}</span>
                            <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 group-hover:text-emerald-400 transition transform group-hover:translate-x-0.5"></i>
                        </div>
                        <span class="text-[10px] text-slate-400 truncate">{{ $demo['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Back to Home Link -->
        <div class="text-center mt-6">
            <a href="{{ route('welcome') }}" class="text-xs text-slate-400 hover:text-white transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-house"></i>
                <span>Kembali ke Halaman Depan Publik</span>
            </a>
        </div>
    </div>

</body>
</html>
