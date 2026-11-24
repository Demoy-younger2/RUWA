@extends('layouts.app', ['title' => 'IoT e-KTP & RFID Reader ESP32'])

@section('content')
<div class="space-y-6" x-data="rfidView({{ $user->id }}, '{{ $user->name }}', '{{ $user->rfid_uid }}')">

    <!-- Hero Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 text-white p-6 sm:p-8 shadow-xl">
        <div class="absolute -right-6 -bottom-6 text-white/5 text-9xl pointer-events-none">
            <i class="fa-solid fa-id-card"></i>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        <i class="fa-solid fa-microchip"></i>
                        <span>ESP32 IoT Integration</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Anti-Duplikasi Aktif</span>
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight">
                    IoT e-KTP & Kartu RFID Reader (ESP32)
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Sistem pemindaian kartu identitas e-KTP dan kartu RFID Mifare 13.56 MHz terintegrasi. Scan kartu lewat akun yang login untuk menghubungkan UID secara otomatis.
                </p>
            </div>

            <!-- Quick Action Button -->
            <div class="pt-2 md:pt-0">
                <button type="button" @click="openModal()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-600/30 transition">
                    <i class="fa-solid fa-id-card"></i>
                    <span>Daftarkan e-KTP Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Status Kartu & Proteksi Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kartu Visual e-KTP Akun Login -->
        <div class="lg:col-span-1 rounded-3xl p-6 bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 text-white shadow-xl relative overflow-hidden flex flex-col justify-between min-h-[240px]">
            <div class="absolute -right-4 -bottom-4 text-white/5 text-8xl pointer-events-none">
                <i class="fa-solid fa-fingerprint"></i>
            </div>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-mono tracking-widest uppercase bg-white/10 px-2.5 py-1 rounded-lg">KARTU e-KTP DIGITAL</span>
                    <i class="fa-solid fa-wifi text-indigo-400 text-sm"></i>
                </div>

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-7 rounded-md bg-amber-400/80 border border-amber-300 flex items-center justify-center shrink-0">
                        <div class="w-6 h-4 border border-amber-600/60 rounded-[3px] grid grid-cols-2 gap-0.5 p-0.5">
                            <div class="bg-amber-600/30"></div>
                            <div class="bg-amber-600/30"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold leading-tight" x-text="userName"></p>
                        <p class="text-[10px] text-indigo-200 mt-0.5">{{ $user->role_title }} • NIK: {{ $user->nik ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-white/10">
                <p class="text-[10px] text-indigo-200 uppercase font-semibold mb-1">Status Kartu e-KTP / RFID</p>
                <template x-if="userUid">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-mono text-base font-extrabold tracking-wider text-emerald-400" x-text="'UID: ' + userUid"></span>
                            <span class="block text-[10px] text-emerald-300">✓ Terverifikasi & Aktif</span>
                        </div>
                        <button type="button" @click="unbindCard()" class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/40 text-rose-200 text-[10px] font-bold transition">
                            Lepas
                        </button>
                    </div>
                </template>
                <template x-if="!userUid">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-amber-300 font-semibold italic">Belum Ada Kartu Terdaftar</span>
                        <button type="button" @click="openModal()" class="px-3 py-1.5 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-bold transition">
                            Scan Sekarang
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Proteksi Anti-Duplikasi & Fitur Keamanan -->
        <div class="lg:col-span-2 rounded-3xl bg-white border border-slate-200 p-6 shadow-xs flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-indigo-600"></i>
                    <span>Proteksi Anti-Duplikasi Kartu & Protokol IoT</span>
                </h3>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700">Strict Validation</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-100 space-y-1">
                    <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs mb-1">
                        <i class="fa-solid fa-fingerprint"></i>
                    </div>
                    <h4 class="font-bold text-indigo-900">Anti-Duplikasi Kartu</h4>
                    <p class="text-[11px] text-indigo-700 leading-relaxed">UID kartu yang telah terdaftar pada satu akun secara otomatis <strong>DITOLAK</strong> jika dicoba didaftarkan ke akun lain.</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-1">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs mb-1">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h4 class="font-bold text-emerald-900">Sesi Pairing 90 Detik</h4>
                    <p class="text-[11px] text-emerald-700 leading-relaxed">Klik tombol scan di web, lalu tempelkan kartu ke ESP32. Kartu langsung terikat ke akun Anda secara instan.</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-purple-50/60 border border-purple-100 space-y-1">
                    <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs mb-1">
                        <i class="fa-solid fa-microchip"></i>
                    </div>
                    <h4 class="font-bold text-purple-900">Multi-Board ESP32</h4>
                    <p class="text-[11px] text-purple-700 leading-relaxed">Mendukung Arduino Nano ESP32 maupun ESP32 DevKit Standar dengan modul RC522 (13.56 MHz SPI).</p>
                </div>
            </div>

            <!-- Testing Simulation Box -->
            <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2 text-slate-500">
                    <span class="font-bold text-slate-700">Lab Simulasi:</span>
                    <span>Uji respon server tanpa hardware:</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="simulateTap('04NEW' + Math.floor(1000 + Math.random() * 9000))" class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-[11px] transition">
                        Simulasi Tap Kartu Baru
                    </button>
                    <button type="button" @click="simulateTap(userUid || '04TESTDUPLICATE')" class="px-2.5 py-1 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold text-[11px] transition">
                        Simulasi Tap Kartu Terdaftar (Uji Ditolak)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Skema Wiring Pinout Hardware -->
    <div class="rounded-3xl bg-slate-900 text-white p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-code text-indigo-400"></i>
                <h3 class="text-sm font-extrabold text-white">Panduan Hardware: Skema Wiring Pinout RC522 ke ESP32</h3>
            </div>
            <span class="text-[11px] font-mono text-indigo-300">SPI Interface (13.56 MHz)</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <!-- Pinout Arduino Nano ESP32 -->
            <div class="space-y-2">
                <h4 class="font-bold text-amber-300 uppercase text-[11px] flex items-center gap-1.5">
                    <i class="fa-solid fa-microchip"></i>
                    <span>1. Arduino Nano ESP32:</span>
                </h4>
                <div class="bg-slate-800/80 rounded-2xl p-3.5 font-mono text-[11px] space-y-1 text-slate-300 border border-slate-700/60">
                    <p><span class="text-amber-400">SDA (SS)</span> ---> <span class="text-emerald-400">Pin D10</span> (GPIO 21)</p>
                    <p><span class="text-amber-400">SCK</span>      ---> <span class="text-emerald-400">Pin D13</span> (GPIO 48)</p>
                    <p><span class="text-amber-400">MOSI</span>     ---> <span class="text-emerald-400">Pin D11</span> (GPIO 38)</p>
                    <p><span class="text-amber-400">MISO</span>     ---> <span class="text-emerald-400">Pin D12</span> (GPIO 47)</p>
                    <p><span class="text-amber-400">RST</span>      ---> <span class="text-emerald-400">Pin D9</span>  (GPIO 18)</p>
                    <p><span class="text-amber-400">GND</span>      ---> <span class="text-emerald-400">GND</span></p>
                    <p><span class="text-amber-400">3.3V</span>     ---> <span class="text-emerald-400">3V3</span> <span class="text-rose-400 text-[9px] font-sans font-bold">(Jangan ke 5V!)</span></p>
                </div>
            </div>

            <!-- Pinout ESP32 DevKit Standar -->
            <div class="space-y-2">
                <h4 class="font-bold text-indigo-300 uppercase text-[11px] flex items-center gap-1.5">
                    <i class="fa-solid fa-microchip"></i>
                    <span>2. ESP32 DevKit (30/38 Pin):</span>
                </h4>
                <div class="bg-slate-800/80 rounded-2xl p-3.5 font-mono text-[11px] space-y-1 text-slate-300 border border-slate-700/60">
                    <p><span class="text-amber-400">SDA (SS)</span> ---> <span class="text-emerald-400">GPIO 5</span></p>
                    <p><span class="text-amber-400">SCK</span>      ---> <span class="text-emerald-400">GPIO 18</span></p>
                    <p><span class="text-amber-400">MOSI</span>     ---> <span class="text-emerald-400">GPIO 23</span></p>
                    <p><span class="text-amber-400">MISO</span>     ---> <span class="text-emerald-400">GPIO 19</span></p>
                    <p><span class="text-amber-400">RST</span>      ---> <span class="text-emerald-400">GPIO 22</span></p>
                    <p><span class="text-amber-400">GND</span>      ---> <span class="text-emerald-400">GND</span></p>
                    <p><span class="text-amber-400">3.3V</span>     ---> <span class="text-emerald-400">3.3V</span></p>
                </div>
            </div>

            <!-- Endpoint API Informasi -->
            <div class="space-y-2">
                <h4 class="font-bold text-emerald-300 uppercase text-[11px] flex items-center gap-1.5">
                    <i class="fa-solid fa-network-wired"></i>
                    <span>3. Endpoint Target ESP32:</span>
                </h4>
                <div class="bg-slate-800/80 rounded-2xl p-3.5 font-mono text-[11px] space-y-2 text-slate-300 border border-slate-700/60">
                    <p class="text-emerald-400 font-bold text-[10px] break-all">POST {{ url('/api/iot/rfid/scan') }}</p>
                    <p class="text-slate-400 text-[10px]">Content-Type: application/json</p>
                    <pre class="text-amber-300 text-[10px] bg-slate-900/80 p-2 rounded-lg">{
  "uid": "04A1B2C3",
  "device_id": "ESP32-READER"
}</pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Pemindaian Kartu Realtime -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Riwayat Tap & Pemindaian Kartu (Live IoT Log)</h3>
                <p class="text-xs text-slate-500">Log pemindaian kartu identitas e-KTP dan kartu RFID dari mikrokontroler ESP32</p>
            </div>
            <button type="button" @click="location.reload()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                <i class="fa-solid fa-rotate mr-1"></i> Refresh Log
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Device ID</th>
                        <th class="py-3 px-4">UID Kartu</th>
                        <th class="py-3 px-4">Pengguna Terkait</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentScans as $scan)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">{{ $scan->created_at->format('H:i:s d M Y') }}</td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-700 font-semibold">{{ $scan->device_id }}</td>
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 text-[11px]">{{ $scan->rfid_uid }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $scan->user ? $scan->user->name : '-' }}</td>
                            <td class="py-3 px-4">
                                @if($scan->status === 'registered')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Registrasi Sukses</span>
                                @elseif($scan->status === 'recognized')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">Akses Diterima</span>
                                @elseif($scan->status === 'rejected_duplicate')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Ditolak (Duplikat)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Belum Terdaftar</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-[11px]">{{ $scan->message ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 italic">Belum ada riwayat tap kartu RFID. Silakan tempelkan kartu pada scanner ESP32.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL POPUP REGISTRASI e-KTP / RFID INTERAKTIF -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="closeModal()">
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-base">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Registrasi e-KTP / Kartu RFID</h3>
                        <p class="text-xs text-slate-500">Mendaftarkan untuk: <span class="font-bold text-indigo-700" x-text="userName"></span></p>
                    </div>
                </div>
                <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Tab Navigasi Modal -->
            <div class="flex rounded-xl bg-slate-100 p-1 text-xs font-bold text-slate-600">
                <button type="button" @click="tab = 'esp32'; startSession()" 
                        :class="tab === 'esp32' ? 'bg-white text-indigo-700 shadow-xs' : 'hover:text-slate-900'"
                        class="flex-1 py-1.5 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-microchip"></i>
                    <span>Scan via ESP32</span>
                </button>
                <button type="button" @click="tab = 'manual'; stopPolling()" 
                        :class="tab === 'manual' ? 'bg-white text-indigo-700 shadow-xs' : 'hover:text-slate-900'"
                        class="flex-1 py-1.5 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-keyboard"></i>
                    <span>Input Manual / USB</span>
                </button>
                <button type="button" @click="tab = 'simulate'" 
                        :class="tab === 'simulate' ? 'bg-white text-indigo-700 shadow-xs' : 'hover:text-slate-900'"
                        class="flex-1 py-1.5 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-flask"></i>
                    <span>Simulasi Test</span>
                </button>
            </div>

            <!-- TAB 1: SCAN VIA ESP32 (REALTIME IOT) -->
            <div x-show="tab === 'esp32'" class="space-y-4">
                <!-- Status Menunggu Tap (Waiting) -->
                <div x-show="scanStatus === 'waiting'" class="p-6 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-center space-y-4">
                    <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                        <span class="absolute inset-0 rounded-full bg-indigo-400/20 animate-ping"></span>
                        <div class="relative w-16 h-16 rounded-full bg-indigo-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-indigo-600/30">
                            <i class="fa-solid fa-wifi rotate-90"></i>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-extrabold text-indigo-950">Sensor ESP32 Aktif & Siap Membaca</h4>
                        <p class="text-xs text-indigo-700 mt-1 max-w-xs mx-auto">
                            Silakan tempelkan kartu e-KTP atau RFID Anda ke reader ESP32...
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-indigo-200 text-xs text-indigo-700 font-mono font-bold">
                        <i class="fa-solid fa-clock"></i>
                        <span>Sisa Waktu Sesi: <span x-text="countdown + 's'"></span></span>
                    </div>
                </div>

                <!-- Status Sukses Terdaftar (Registered) -->
                <div x-show="scanStatus === 'registered'" class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-emerald-500 text-white flex items-center justify-center text-2xl mx-auto shadow-md shadow-emerald-500/20">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-emerald-950">Kartu e-KTP Berhasil Didaftarkan!</h4>
                        <p class="text-xs text-emerald-700 mt-1" x-text="statusMessage"></p>
                    </div>
                    <div class="inline-block px-4 py-1.5 rounded-xl bg-white border border-emerald-300 font-mono text-sm font-extrabold text-emerald-800" x-text="'UID: ' + userUid"></div>
                </div>

                <!-- Status Ditolak Karena Duplikat (Rejected Duplicate) -->
                <div x-show="scanStatus === 'rejected_duplicate'" class="p-5 rounded-2xl bg-rose-50 border border-rose-300 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-rose-600 text-white flex items-center justify-center text-2xl mx-auto shadow-md shadow-rose-600/20 animate-bounce">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-rose-950">PENDAFTARAN KARTU DITOLAK!</h4>
                        <p class="text-xs text-rose-700 mt-1 font-semibold" x-text="statusMessage"></p>
                    </div>
                    <p class="text-[11px] text-rose-600 bg-white/80 p-2.5 rounded-xl border border-rose-200">
                        Satu kartu e-KTP/RFID hanya dapat diikat pada 1 akun warga saja demi keamanan dan pencegahan penyalahgunaan.
                    </p>
                </div>

                <!-- Status Error / Timeout -->
                <div x-show="scanStatus === 'error'" class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center text-xl mx-auto">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-amber-950">Waktu Sesi Habis</h4>
                        <p class="text-xs text-amber-700 mt-1" x-text="statusMessage"></p>
                    </div>
                </div>

                <!-- Action Buttons Tab ESP32 -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" @click="startSession()" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-right"></i>
                        <span>Ulangi Scan</span>
                    </button>
                    <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                        Selesai / Tutup
                    </button>
                </div>
            </div>

            <!-- TAB 2: INPUT MANUAL / SCANNER USB -->
            <div x-show="tab === 'manual'" class="space-y-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <label class="block text-xs font-bold text-slate-800 uppercase">
                        Nomor UID Kartu e-KTP / RFID
                    </label>
                    <div class="relative">
                        <input type="text" x-model="manualUidInput" placeholder="Contoh: 04A1B2C3 atau E2000019" 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase tracking-wider outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Masukkan UID kartu (biasanya 8 - 14 karakter heksadesimal). Bisa menggunakan RFID USB Reader Plug & Play atau input manual.
                    </p>
                </div>

                <div x-show="manualError" class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 mt-0.5"></i>
                    <span x-text="manualError"></span>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl border border-slate-200 font-bold text-slate-600 text-xs">
                        Batal
                    </button>
                    <button type="button" @click="submitManual()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20">
                        Simpan & Daftarkan
                    </button>
                </div>
            </div>

            <!-- TAB 3: SIMULASI PENGUJIAN CEPAT -->
            <div x-show="tab === 'simulate'" class="space-y-4">
                <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 space-y-2">
                    <h4 class="text-xs font-bold text-indigo-950 flex items-center gap-1.5">
                        <i class="fa-solid fa-flask text-indigo-600"></i>
                        <span>Uji Coba Validasi IoT Langsung dari Browser</span>
                    </h4>
                    <p class="text-[11px] text-indigo-800 leading-relaxed">
                        Fitur ini mensimulasikan tap dari hardware ESP32 ke server secara langsung untuk menguji skenario pendaftaran sukses dan penolakan kartu yang sudah dipakai akun lain.
                    </p>
                </div>

                <div class="space-y-2 text-xs">
                    <button type="button" @click="simulateTap('04NEW' + Math.floor(1000 + Math.random() * 9000))" class="w-full text-left p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100/70 transition flex items-center justify-between">
                        <div>
                            <span class="font-bold text-emerald-900 block">1. Simulasi Tap Kartu Baru (Unik)</span>
                            <span class="text-[10px] text-emerald-700">Akan berhasil mendaftarkan kartu jika sesi scan sedang aktif</span>
                        </div>
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                    </button>

                    <button type="button" @click="simulateTap(userUid || '04TESTDUPLICATE')" class="w-full text-left p-3 rounded-xl border border-rose-200 bg-rose-50/50 hover:bg-rose-100/70 transition flex items-center justify-between">
                        <div>
                            <span class="font-bold text-rose-900 block">2. Simulasi Tap Kartu Terdaftar / Duplikat</span>
                            <span class="text-[10px] text-rose-700">Menguji proteksi penolakan duplikat kartu</span>
                        </div>
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                    </button>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function rfidView(userId, userName, currentUid) {
    return {
        userId: userId,
        userName: userName,
        userUid: currentUid || null,
        showModal: false,
        tab: 'esp32',
        scanStatus: 'idle',
        statusMessage: '',
        manualUidInput: '',
        manualError: '',
        countdown: 90,
        pollTimer: null,
        countTimer: null,

        openModal() {
            this.showModal = true;
            this.tab = 'esp32';
            this.manualUidInput = '';
            this.manualError = '';
            this.startSession();
        },

        closeModal() {
            this.stopPolling();
            fetch('/api/rfid/cancel-session', { method: 'POST' }).catch(() => {});
            this.showModal = false;
        },

        stopPolling() {
            if (this.pollTimer) clearInterval(this.pollTimer);
            if (this.countTimer) clearInterval(this.countTimer);
            this.pollTimer = null;
            this.countTimer = null;
        },

        async startSession() {
            this.stopPolling();
            this.scanStatus = 'waiting';
            this.statusMessage = 'Menunggu kartu e-KTP / RFID ditempelkan ke ESP32...';
            this.countdown = 90;

            try {
                await fetch('/api/rfid/register-session', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: this.userId })
                });

                this.countTimer = setInterval(() => {
                    if (this.countdown > 0) {
                        this.countdown--;
                    } else {
                        this.scanStatus = 'error';
                        this.statusMessage = 'Waktu sesi habis. Silakan klik Ulangi Scan.';
                        this.stopPolling();
                    }
                }, 1000);

                this.pollTimer = setInterval(async () => {
                    try {
                        const res = await fetch('/api/rfid/status-session');
                        const data = await res.json();

                        if (data.status === 'registered') {
                            this.scanStatus = 'registered';
                            this.userUid = data.rfid_uid;
                            this.statusMessage = `Kartu e-KTP (UID: ${data.rfid_uid}) BERHASIL terdaftar untuk ${this.userName}!`;
                            this.stopPolling();
                        } else if (data.status === 'rejected_duplicate') {
                            this.scanStatus = 'rejected_duplicate';
                            this.statusMessage = data.error_message || 'Kartu sudah terdaftar pada akun lain dan tidak bisa digunakan!';
                            this.stopPolling();
                        } else if (data.status === 'error') {
                            this.scanStatus = 'error';
                            this.statusMessage = data.error_message || 'Terjadi kesalahan pemindaian.';
                            this.stopPolling();
                        }
                    } catch (err) {}
                }, 1500);

            } catch (err) {
                this.scanStatus = 'error';
                this.statusMessage = 'Gagal memulai sesi pendaftaran RFID.';
            }
        },

        async submitManual() {
            if (!this.manualUidInput || this.manualUidInput.trim().length < 4) {
                this.manualError = 'Masukkan UID kartu minimal 4 karakter!';
                return;
            }

            this.manualError = '';
            try {
                const res = await fetch('/api/rfid/manual-bind', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        user_id: this.userId,
                        rfid_uid: this.manualUidInput.trim(),
                        device_id: 'WEB-DASHBOARD'
                    })
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    this.manualError = data.message || 'Kartu gagal didaftarkan.';
                    return;
                }

                this.userUid = data.rfid_uid;
                this.scanStatus = 'registered';
                this.statusMessage = data.message;
                this.tab = 'esp32';
                setTimeout(() => { this.closeModal(); location.reload(); }, 1200);
            } catch (err) {
                this.manualError = 'Terjadi kesalahan saat menyimpan kartu.';
            }
        },

        async unbindCard() {
            if (!confirm('Apakah Anda yakin ingin melepas pendaftaran kartu e-KTP ini?')) return;
            try {
                const res = await fetch('/api/rfid/unbind', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: this.userId })
                });
                const data = await res.json();
                if (data.success) {
                    this.userUid = null;
                    location.reload();
                }
            } catch (e) {
                alert('Gagal melepas kartu.');
            }
        },

        async simulateTap(uid) {
            try {
                const res = await fetch('/api/iot/rfid/scan', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        uid: uid,
                        device_id: 'ESP32-DEV-SIMULATOR'
                    })
                });
                const data = await res.json();
                if (data.success) {
                    alert('[Simulasi ESP32 Berhasil] ' + data.message);
                } else {
                    alert('[Simulasi ESP32 Ditolak] ' + (data.message || 'Kartu tidak diterima'));
                }
            } catch (e) {
                alert('Gagal mengirim simulasi tap.');
            }
        }
    }
}
</script>
@endsection
