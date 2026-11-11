<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Pendaftaran Warga - SiRW Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-full flex flex-col justify-center py-8 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-slate-800">

    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="text-center">
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-xl shadow-emerald-500/30 font-bold text-2xl">
                    <i class="fa-solid fa-users-line"></i>
                </div>
            </a>
            <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-white">
                Pendaftaran Akun Warga
            </h2>
            <p class="mt-1 text-xs text-slate-300">
                Layanan mandiri pengurusan surat & administrasi RW 05 Sukamaju
            </p>
        </div>

        @if($errors->any())
            <div class="mt-4 p-3 rounded-xl bg-rose-500/10 border border-rose-400/30 text-rose-200 text-xs">
                @foreach($errors->all() as $err)
                    <p class="flex items-center gap-2"><i class="fa-solid fa-circle-exclamation"></i> {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <div class="mt-6 bg-white/95 backdrop-blur-md py-6 px-6 sm:px-8 shadow-2xl rounded-3xl border border-white/20">
            <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700">Nama Lengkap (Sesuai KTP)</label>
                    <input name="name" type="text" required value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" class="mt-1 block w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700">Nomor Induk Kependudukan (NIK 16 Digit)</label>
                    <input name="nik" type="text" maxlength="16" required value="{{ old('nik') }}" placeholder="320101xxxxxxxxxx" class="mt-1 block w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700">RT</label>
                        <select name="rt" required class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                            <option value="01">RT 01</option>
                            <option value="02">RT 02</option>
                            <option value="03">RT 03</option>
                            <option value="04">RT 04</option>
                            <option value="05">RT 05</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700">RW</label>
                        <input name="rw" type="text" readonly value="05" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-100 text-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700">Alamat Email Aktif</label>
                    <input name="email" type="email" required value="{{ old('email') }}" placeholder="nama@email.com" class="mt-1 block w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700">Nomor WhatsApp / HP</label>
                    <input name="phone" type="text" required value="{{ old('phone') }}" placeholder="081234567890" class="mt-1 block w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700">Kata Sandi</label>
                        <input name="password" type="password" required placeholder="Min. 6 Karakter" class="mt-1 block w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700">Konfirmasi Sandi</label>
                        <input name="password_confirmation" type="password" required placeholder="Ulangi Sandi" class="mt-1 block w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-md transition">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Daftar Sekarang</span>
                    </button>
                </div>
            </form>

            <div class="mt-4 pt-4 border-t border-slate-100 text-center text-xs">
                <span class="text-slate-500">Sudah punya akun?</span>
                <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700 ml-1">Masuk di sini</a>
            </div>
        </div>
    </div>

</body>
</html>
