@extends('layouts.app', ['title' => 'IoT e-KTP & RFID Reader ESP32'])

@section('content')
<div class="space-y-6" x-data="rfidView({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->rfid_uid ?? '' }}')">

    <!-- Hero Header Banner (Tema Emerald-Teal Modern SiRW Digital) -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-800 via-teal-800 to-slate-900 text-white p-6 sm:p-8 shadow-xl">
        <!-- Glow Ornaments (Halus & Rapi, Tidak Menutupi Teks) -->
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-0 right-1/3 w-60 h-60 bg-emerald-400/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-white/10 backdrop-blur-md text-emerald-200 border border-white/15">
                        <i class="fa-solid fa-microchip text-emerald-400"></i>
                        <span>ESP32 & RC522 (13.56 MHz)</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-400/20 text-emerald-200 border border-emerald-400/30">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Proteksi Anti-Duplikasi Aktif</span>
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    IoT e-KTP & Kartu RFID Reader
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 max-w-2xl leading-relaxed">
                    Integrasi mikrokontroler ESP32 untuk memindai kartu e-KTP atau RFID Mifare. Scan kartu secara langsung lewat akun warga yang login untuk menghubungkan UID secara instan dan aman.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <button type="button" @click="openModal()" class="flex items-center gap-2 px-5 py-3 rounded-2xl text-xs font-extrabold text-emerald-950 bg-white hover:bg-emerald-50 shadow-lg shadow-black/15 transition transform active:scale-95">
                    <i class="fa-solid fa-id-card text-emerald-600 text-sm"></i>
                    <span>Daftarkan e-KTP Baru</span>
                </button>
                <a href="#panduan-wiring" class="flex items-center gap-2 px-4 py-3 rounded-2xl text-xs font-bold text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-xs transition">
                    <i class="fa-solid fa-diagram-project text-xs"></i>
                    <span>Skematik Wiring</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid: Kartu e-KTP Visual & Panel Keamanan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KARTU FISIK e-KTP DIGITAL (Kolom 1-5) -->
        <div class="lg:col-span-5 flex flex-col">
            <div class="relative rounded-3xl p-6 bg-gradient-to-br from-slate-900 via-teal-950 to-slate-900 text-white shadow-2xl border border-teal-500/20 overflow-hidden flex flex-col justify-between min-h-[280px]">
                <!-- Watermark Background -->
                <div class="absolute -right-8 -bottom-8 text-white/[0.04] text-9xl pointer-events-none select-none">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>

                <!-- Bagian Atas Kartu: Header & Wave Icon -->
                <div class="relative z-10">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[9px] font-bold tracking-[0.2em] uppercase text-teal-300 block">REPUBLIK INDONESIA</span>
                            <span class="text-xs font-extrabold tracking-wider text-white block">KARTU TANDA PENDUDUK (e-KTP)</span>
                            <span class="text-[10px] text-slate-400 font-medium">Sistem Integrasi SiRWDigital</span>
                        </div>
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 border border-white/10 text-teal-300 text-xs">
                            <i class="fa-solid fa-wifi rotate-90 text-[11px]"></i>
                            <span class="text-[10px] font-mono font-bold">13.56 MHz</span>
                        </div>
                    </div>

                    <!-- Microchip EMV Realistis Emas -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="w-12 h-9 rounded-lg bg-gradient-to-br from-amber-300 via-amber-400 to-yellow-500 border border-amber-200/80 p-1 shadow-md shadow-amber-500/20 flex flex-col justify-between">
                            <div class="flex justify-between h-2.5 border-b border-amber-600/40">
                                <span class="w-2.5 border-r border-amber-600/40"></span>
                                <span class="w-2.5 border-l border-amber-600/40"></span>
                            </div>
                            <div class="flex justify-between h-2.5">
                                <span class="w-2.5 border-r border-amber-600/40"></span>
                                <span class="w-2.5 border-l border-amber-600/40"></span>
                            </div>
                        </div>

                        <!-- Badge Status Terdaftar -->
                        <template x-if="userUid">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                KARTU AKTIF
                            </span>
                        </template>
                        <template x-if="!userUid">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                BELUM TERDAFTAR
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Bagian Bawah Kartu: Info Warga & UID -->
                <div class="relative z-10 mt-6 space-y-3">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">Nama Pemilik Akun</p>
                        <p class="text-sm sm:text-base font-extrabold text-white tracking-wide truncate" x-text="userName"></p>
                        <p class="text-[11px] text-teal-200/80 font-medium">{{ $user->role_title }} • RT {{ $user->rt ?? '01' }} / RW {{ $user->rw ?? '05' }}</p>
                    </div>

                    <div class="pt-2 border-t border-white/10 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] uppercase tracking-wider text-slate-400 font-semibold block">UID Kartu e-KTP / RFID</span>
                            <template x-if="userUid">
                                <span class="font-mono text-sm sm:text-base font-extrabold text-emerald-300 tracking-wider" x-text="userUid"></span>
                            </template>
                            <template x-if="!userUid">
                                <span class="font-mono text-xs text-slate-400 italic">-- Belum terhubung --</span>
                            </template>
                        </div>

                        <div>
                            <template x-if="userUid">
                                <button type="button" @click="unbindCard()" class="text-[10px] font-bold text-rose-300 hover:text-rose-100 bg-rose-500/20 hover:bg-rose-500/40 border border-rose-500/30 px-3 py-1 rounded-lg transition">
                                    <i class="fa-solid fa-link-slash mr-1"></i> Lepas Kartu
                                </button>
                            </template>
                            <template x-if="!userUid">
                                <button type="button" @click="openModal()" class="text-[10px] font-bold text-teal-200 hover:text-white bg-teal-500/20 hover:bg-teal-500/40 border border-teal-500/30 px-3 py-1.5 rounded-lg transition">
                                    <i class="fa-solid fa-qrcode mr-1"></i> Hubungkan
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL PROTOKOL & KEAMANAN SISTEM (Kolom 6-12) -->
        <div class="lg:col-span-7 flex flex-col justify-between space-y-5">
            <!-- 3 Pilar Fitur Proteksi -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Fitur 1: Anti Duplikasi -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold mb-3">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 leading-tight">Anti-Duplikasi Akun</h3>
                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                        1 UID e-KTP eksklusif untuk 1 akun. Ditolak otomatis jika sudah terdaftar di user lain.
                    </p>
                </div>

                <!-- Fitur 2: Pairing 90 Detik -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
                    <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold mb-3">
                        <i class="fa-solid fa-stopwatch"></i>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 leading-tight">Sesi Pairing 90 Detik</h3>
                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                        Sistem pairing berbatas waktu aman via cache server untuk registrasi tanpa tabrakan antrean.
                    </p>
                </div>

                <!-- Fitur 3: ESP32 Multi-Board -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold mb-3">
                        <i class="fa-solid fa-microchip"></i>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 leading-tight">Mendukung Multi-Board</h3>
                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                        Siap untuk Arduino Nano ESP32 maupun ESP32 DevKit standar dengan modul RC522 SPI.
                    </p>
                </div>
            </div>

            <!-- LAB SIMULASI & UJI RESPON REST API -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Lab Pengujian REST API (Tanpa Alat)</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">Developer Mode</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Uji alur verifikasi kartu secara instan langsung ke endpoint backend <code class="font-mono text-[11px] bg-slate-100 px-1.5 py-0.5 rounded text-emerald-700">/api/iot/rfid/scan</code>:
                </p>

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <button type="button" @click="simulateTap('new')" :disabled="simulating" class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition disabled:opacity-50">
                        <i class="fa-solid fa-credit-card text-emerald-600"></i>
                        <span>1. Simulasi Tap Kartu Baru</span>
                    </button>

                    <button type="button" @click="simulateTap('duplicate')" :disabled="simulating" class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition disabled:opacity-50">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>2. Simulasi Tap Kartu Terdaftar (Uji Ditolak)</span>
                    </button>
                </div>

                <!-- Simulation Output Box -->
                <div x-show="simulationLog" x-cloak class="p-3.5 rounded-2xl bg-slate-900 text-slate-100 font-mono text-[11px] space-y-1 transition">
                    <div class="flex items-center justify-between text-[10px] text-slate-400 border-b border-slate-800 pb-1">
                        <span>Response Backend:</span>
                        <button type="button" @click="simulationLog = ''" class="hover:text-white">&times; Tutup</button>
                    </div>
                    <p class="pt-1" :class="simSuccess ? 'text-emerald-400 font-bold' : 'text-rose-400 font-bold'" x-text="simulationLog"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- PANDUAN SKEMATIK WIRING HARDWARE ESP32 & RC522 -->
    <div id="panduan-wiring" class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-diagram-project text-emerald-600"></i>
                    <span>Panduan Pengkabelan (Wiring Pinout) Sensor RC522 ke ESP32</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Firmware tersedia di folder repositori <code class="font-mono text-[11px] bg-slate-100 px-1 py-0.5 rounded">iot_esp32_firmware/esp32_rfid_reader/</code></p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-3 py-1 rounded-full flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Tegangan Wajib 3.3V (Bukan 5V!)</span>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Pinout 1: Arduino Nano ESP32 -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-microchip text-teal-600"></i>
                        <span>1. Arduino Nano ESP32</span>
                    </span>
                    <span class="text-[10px] font-mono bg-teal-100 text-teal-800 font-bold px-2 py-0.5 rounded">SPI Port</span>
                </div>
                <div class="space-y-1 font-mono text-[11px] text-slate-700 divide-y divide-slate-200/60">
                    <div class="flex justify-between py-1"><span>SDA (SS)</span><span class="font-bold text-emerald-700">Pin D10 (GPIO 21)</span></div>
                    <div class="flex justify-between py-1"><span>SCK</span><span class="font-bold text-emerald-700">Pin D13 (GPIO 48)</span></div>
                    <div class="flex justify-between py-1"><span>MOSI</span><span class="font-bold text-emerald-700">Pin D11 (GPIO 38)</span></div>
                    <div class="flex justify-between py-1"><span>MISO</span><span class="font-bold text-emerald-700">Pin D12 (GPIO 47)</span></div>
                    <div class="flex justify-between py-1"><span>RST</span><span class="font-bold text-emerald-700">Pin D9 (GPIO 18)</span></div>
                    <div class="flex justify-between py-1"><span>GND</span><span class="font-bold text-slate-900">GND</span></div>
                    <div class="flex justify-between py-1"><span>VCC (3.3V)</span><span class="font-bold text-rose-600">3V3</span></div>
                </div>
            </div>

            <!-- Pinout 2: ESP32 DevKit Standar -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-microchip text-indigo-600"></i>
                        <span>2. ESP32 DevKit (30/38 Pin)</span>
                    </span>
                    <span class="text-[10px] font-mono bg-indigo-100 text-indigo-800 font-bold px-2 py-0.5 rounded">VSPI Port</span>
                </div>
                <div class="space-y-1 font-mono text-[11px] text-slate-700 divide-y divide-slate-200/60">
                    <div class="flex justify-between py-1"><span>SDA (SS)</span><span class="font-bold text-indigo-700">GPIO 5</span></div>
                    <div class="flex justify-between py-1"><span>SCK</span><span class="font-bold text-indigo-700">GPIO 18</span></div>
                    <div class="flex justify-between py-1"><span>MOSI</span><span class="font-bold text-indigo-700">GPIO 23</span></div>
                    <div class="flex justify-between py-1"><span>MISO</span><span class="font-bold text-indigo-700">GPIO 19</span></div>
                    <div class="flex justify-between py-1"><span>RST</span><span class="font-bold text-indigo-700">GPIO 22</span></div>
                    <div class="flex justify-between py-1"><span>GND</span><span class="font-bold text-slate-900">GND</span></div>
                    <div class="flex justify-between py-1"><span>VCC (3.3V)</span><span class="font-bold text-rose-600">3.3V</span></div>
                </div>
            </div>

            <!-- Endpoint & Format Payload -->
            <div class="p-4 rounded-2xl bg-slate-900 text-slate-100 space-y-3 font-mono text-[11px]">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-teal-300 font-sans flex items-center gap-1.5">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>3. Target HTTP Request</span>
                    </span>
                    <span class="text-[9px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 px-2 py-0.5 rounded">POST</span>
                </div>
                <p class="text-emerald-400 font-bold break-all text-[11px]">{{ url('/api/iot/rfid/scan') }}</p>
                <div class="bg-slate-800 p-2.5 rounded-xl space-y-1 border border-slate-700/60 text-[10px]">
                    <p class="text-slate-400">// Body Payload JSON:</p>
                    <p class="text-amber-300">{</p>
                    <p class="text-amber-300 pl-3">"uid": <span class="text-emerald-400">"04A1B2C3"</span>,</p>
                    <p class="text-amber-300 pl-3">"device_id": <span class="text-emerald-400">"ESP32-GATE-01"</span></p>
                    <p class="text-amber-300">}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL LOG PEMINDAIAN REALTIME DARI ESP32 -->
    <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                    <span>Riwayat Pemindaian Kartu (Live IoT Log)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Catatan aktivitas pemindaian e-KTP dan kartu RFID dari reader ESP32</p>
            </div>
            <button type="button" @click="location.reload()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs self-start sm:self-auto">
                <i class="fa-solid fa-rotate text-slate-400"></i>
                <span>Segarkan Data</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-400 uppercase font-bold text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Device ID</th>
                        <th class="py-3 px-4">UID Kartu</th>
                        <th class="py-3 px-4">Warga / Pemilik Akun</th>
                        <th class="py-3 px-4">Status Verifikasi</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentScans as $scan)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">{{ $scan->created_at->format('H:i:s • d M Y') }}</td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-700 font-semibold">{{ $scan->device_id }}</td>
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 text-[11px] border border-slate-200">{{ $scan->rfid_uid }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $scan->user ? $scan->user->name : '-' }}</td>
                            <td class="py-3 px-4">
                                @if($scan->status === 'registered')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check text-[9px]"></i> Registrasi Sukses
                                    </span>
                                @elseif($scan->status === 'recognized')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Akses Diterima
                                    </span>
                                @elseif($scan->status === 'rejected_duplicate')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <i class="fa-solid fa-ban text-[9px]"></i> Ditolak (Duplikat)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-question text-[9px]"></i> Belum Terdaftar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-[11px]">{{ $scan->message ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 italic">
                                <i class="fa-solid fa-satellite-dish text-2xl text-slate-300 block mb-2"></i>
                                Belum ada riwayat tap kartu RFID. Silakan tempelkan kartu pada scanner ESP32.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL POPUP REGISTRASI e-KTP / RFID INTERAKTIF -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative border border-slate-100" @click.away="closeModal()">
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-base font-bold shadow-xs">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Registrasi Kartu e-KTP</h3>
                        <p class="text-xs text-slate-500">Mendaftarkan untuk akun: <span class="font-bold text-emerald-700" x-text="userName"></span></p>
                    </div>
                </div>
                <button type="button" @click="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Tab Navigasi Modal -->
            <div class="flex rounded-2xl bg-slate-100 p-1 text-xs font-bold text-slate-600">
                <button type="button" @click="tab = 'esp32'; startSession()" 
                        :class="tab === 'esp32' ? 'bg-white text-emerald-800 shadow-xs' : 'hover:text-slate-900'"
                        class="flex-1 py-2 rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-microchip"></i>
                    <span>Scan ESP32</span>
                </button>
                <button type="button" @click="tab = 'manual'; stopPolling()" 
                        :class="tab === 'manual' ? 'bg-white text-emerald-800 shadow-xs' : 'hover:text-slate-900'"
                        class="flex-1 py-2 rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-keyboard"></i>
                    <span>Input Manual</span>
                </button>
                <button type="button" @click="tab = 'simulate'" 
                        :class="tab === 'simulate' ? 'bg-white text-emerald-800 shadow-xs' : 'hover:text-slate-900'"
                        class="flex-1 py-2 rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-flask"></i>
                    <span>Simulasi Cepat</span>
                </button>
            </div>

            <!-- TAB 1: SCAN VIA ESP32 (REALTIME IOT) -->
            <div x-show="tab === 'esp32'" class="space-y-4">
                <!-- Status Menunggu Tap (Waiting) -->
                <div x-show="scanStatus === 'waiting'" class="p-6 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-center space-y-4">
                    <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                        <span class="absolute inset-0 rounded-full bg-emerald-400/20 animate-ping"></span>
                        <div class="relative w-16 h-16 rounded-full bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-600/30">
                            <i class="fa-solid fa-wifi rotate-90"></i>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-extrabold text-emerald-950">Sensor ESP32 Siap Membaca</h4>
                        <p class="text-xs text-emerald-700 mt-1 max-w-xs mx-auto">
                            Silakan tempelkan kartu e-KTP atau RFID Anda ke sensor RC522...
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-emerald-200 text-xs text-emerald-800 font-mono font-bold shadow-xs">
                        <i class="fa-solid fa-clock text-emerald-600"></i>
                        <span>Sisa Waktu Sesi: <span x-text="countdown + 's'"></span></span>
                    </div>
                </div>

                <!-- Status Sukses Terdaftar (Registered) -->
                <div x-show="scanStatus === 'registered'" class="p-5 rounded-2xl bg-emerald-50 border border-emerald-300 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-emerald-500 text-white flex items-center justify-center text-2xl mx-auto shadow-md shadow-emerald-500/20">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-emerald-950">Kartu e-KTP Berhasil Terdaftar!</h4>
                        <p class="text-xs text-emerald-700 mt-1" x-text="statusMessage"></p>
                    </div>
                    <div class="inline-block px-4 py-1.5 rounded-xl bg-white border border-emerald-300 font-mono text-sm font-extrabold text-emerald-800 shadow-xs" x-text="'UID: ' + userUid"></div>
                </div>

                <!-- Status Ditolak Karena Duplikat (Rejected Duplicate) -->
                <div x-show="scanStatus === 'rejected_duplicate'" class="p-5 rounded-2xl bg-rose-50 border border-rose-300 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-rose-600 text-white flex items-center justify-center text-2xl mx-auto shadow-md shadow-rose-600/20">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-rose-950">PENDAFTARAN KARTU DITOLAK!</h4>
                        <p class="text-xs text-rose-700 mt-1 font-semibold" x-text="statusMessage"></p>
                    </div>
                    <p class="text-[11px] text-rose-600 bg-white p-2.5 rounded-xl border border-rose-200 leading-relaxed">
                        Satu kartu e-KTP hanya dapat diikat pada 1 akun warga saja demi keamanan dan pencegahan pemalsuan identitas.
                    </p>
                </div>

                <!-- Status Error / Timeout -->
                <div x-show="scanStatus === 'error'" class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-amber-500 text-white flex items-center justify-center text-2xl mx-auto">
                        <i class="fa-solid fa-hourglass-end"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-amber-950">Sesi Telah Berakhir</h4>
                        <p class="text-xs text-amber-800 mt-1" x-text="statusMessage"></p>
                    </div>
                    <button type="button" @click="startSession()" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                        <i class="fa-solid fa-rotate-right mr-1"></i> Mulai Ulang Sesi
                    </button>
                </div>
            </div>

            <!-- TAB 2: INPUT MANUAL / USB READER -->
            <div x-show="tab === 'manual'" class="space-y-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <label class="block text-xs font-bold text-slate-700">Masukkan UID Kartu e-KTP / RFID:</label>
                    <div class="relative">
                        <input type="text" x-model="manualUid" placeholder="Contoh: 04A1B2C3" maxlength="20"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-mono text-sm uppercase font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <p class="text-[11px] text-slate-500">
                        * Gunakan format heksadesimal 8 atau 14 karakter UID kartu Anda.
                    </p>
                </div>

                <div x-show="manualFeedback" class="p-3 rounded-xl text-xs font-semibold"
                     :class="manualSuccess ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                     x-text="manualFeedback"></div>

                <button type="button" @click="submitManual()" :disabled="!manualUid || manualLoading"
                        class="w-full py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 transition shadow-sm">
                    <span x-show="!manualLoading"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan & Pasang Kartu</span>
                    <span x-show="manualLoading"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...</span>
                </button>
            </div>

            <!-- TAB 3: SIMULASI LANGSUNG DARI MODAL -->
            <div x-show="tab === 'simulate'" class="space-y-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <h4 class="text-xs font-bold text-slate-700">Simulasikan Kartu Menempel ke Sensor:</h4>
                    <p class="text-xs text-slate-500">Uji otomatisasi pendaftaran kartu seolah-olah kartu ditempelkan ke mikrokontroler ESP32.</p>

                    <div class="space-y-2 pt-2">
                        <button type="button" @click="runModalSimulation('new')" class="w-full py-2.5 rounded-xl text-xs font-bold text-emerald-900 bg-emerald-100 hover:bg-emerald-200 transition text-left px-4 flex items-center justify-between">
                            <span>1. Simulasi Kartu Baru (Belum Dipakai)</span>
                            <i class="fa-solid fa-arrow-right text-emerald-600"></i>
                        </button>
                        <button type="button" @click="runModalSimulation('duplicate')" class="w-full py-2.5 rounded-xl text-xs font-bold text-rose-900 bg-rose-100 hover:bg-rose-200 transition text-left px-4 flex items-center justify-between">
                            <span>2. Simulasi Kartu Duplikat (Milik Akun Lain)</span>
                            <i class="fa-solid fa-arrow-right text-rose-600"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Tutup Jendela
                </button>
            </div>
        </div>
    </div>

</div>

<!-- JAVASCRIPT LOGIC REALTIME MENGGUNAKAN ALPINE.JS -->
<script>
function rfidView(userId, userName, currentUid) {
    return {
        userId: userId,
        userName: userName,
        userUid: currentUid,
        showModal: false,
        tab: 'esp32',
        scanStatus: 'waiting', // waiting, registered, rejected_duplicate, error
        statusMessage: '',
        countdown: 90,
        pollingInterval: null,
        countdownInterval: null,
        manualUid: '',
        manualLoading: false,
        manualFeedback: '',
        manualSuccess: false,
        simulating: false,
        simulationLog: '',
        simSuccess: false,

        openModal() {
            this.showModal = true;
            this.tab = 'esp32';
            this.startSession();
        },

        closeModal() {
            this.showModal = false;
            this.stopPolling();
            if (this.scanStatus === 'registered') {
                location.reload();
            }
        },

        async startSession() {
            this.scanStatus = 'waiting';
            this.statusMessage = '';
            this.countdown = 90;
            this.stopPolling();

            try {
                const res = await fetch('/api/rfid/register-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ user_id: this.userId })
                });
                const data = await res.json();
                if (data.status) {
                    this.pollStatus();
                } else {
                    this.scanStatus = 'error';
                    this.statusMessage = data.message || 'Gagal memulai sesi pendaftaran.';
                }
            } catch (err) {
                this.scanStatus = 'error';
                this.statusMessage = 'Gagal menghubungi server.';
            }
        },

        pollStatus() {
            this.countdownInterval = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                } else {
                    this.scanStatus = 'error';
                    this.statusMessage = 'Sesi pairing 90 detik telah kedaluwarsa. Silakan mulai ulang.';
                    this.stopPolling();
                }
            }, 1000);

            this.pollingInterval = setInterval(async () => {
                try {
                    const res = await fetch('/api/rfid/status-session');
                    const data = await res.json();

                    if (data.status === 'registered') {
                        this.scanStatus = 'registered';
                        this.userUid = data.uid;
                        this.statusMessage = data.message;
                        this.stopPolling();
                    } else if (data.status === 'rejected_duplicate') {
                        this.scanStatus = 'rejected_duplicate';
                        this.statusMessage = data.message;
                        this.stopPolling();
                    }
                } catch (e) {
                    console.error('Polling error:', e);
                }
            }, 1500);
        },

        stopPolling() {
            if (this.pollingInterval) clearInterval(this.pollingInterval);
            if (this.countdownInterval) clearInterval(this.countdownInterval);
        },

        async submitManual() {
            this.manualLoading = true;
            this.manualFeedback = '';
            try {
                const res = await fetch('/api/rfid/manual-bind', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        user_id: this.userId,
                        rfid_uid: this.manualUid
                    })
                });
                const data = await res.json();
                this.manualSuccess = data.status;
                this.manualFeedback = data.message;

                if (data.status) {
                    this.userUid = data.uid;
                    setTimeout(() => {
                        this.closeModal();
                        location.reload();
                    }, 1200);
                }
            } catch (err) {
                this.manualSuccess = false;
                this.manualFeedback = 'Terjadi kesalahan saat memproses data.';
            } finally {
                this.manualLoading = false;
            }
        },

        async unbindCard() {
            if (!confirm('Apakah Anda yakin ingin melepas tautan kartu e-KTP dari akun ini?')) return;

            try {
                const res = await fetch('/api/rfid/unbind', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ user_id: this.userId })
                });
                const data = await res.json();
                if (data.status) {
                    alert('Kartu e-KTP berhasil dilepas.');
                    location.reload();
                } else {
                    alert(data.message || 'Gagal melepas kartu.');
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            }
        },

        async simulateTap(type) {
            this.simulating = true;
            this.simulationLog = 'Mengirim tap kartu simulasi ke /api/iot/rfid/scan...';

            let testUid = '';
            if (type === 'new') {
                testUid = 'SIM' + Math.random().toString(16).substr(2, 6).toUpperCase();
            } else {
                testUid = this.userUid || 'DEMO123456';
            }

            try {
                const res = await fetch('/api/iot/rfid/scan', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        uid: testUid,
                        device_id: 'SIMULATOR-WEB'
                    })
                });
                const data = await res.json();
                this.simSuccess = data.status === 'success';
                this.simulationLog = `[${data.action_taken || data.status}] ${data.message} (UID: ${testUid})`;
            } catch (err) {
                this.simSuccess = false;
                this.simulationLog = 'Gagal memanggil endpoint simulasi.';
            } finally {
                this.simulating = false;
            }
        },

        async runModalSimulation(type) {
            this.tab = 'esp32';
            let testUid = '';
            if (type === 'new') {
                testUid = 'EKT' + Math.random().toString(16).substr(2, 6).toUpperCase();
            } else {
                testUid = 'DUPLICATE_CARD_99';
            }

            // Jika type duplicate, daftarkan dulu ke user lain jika belum ada
            if (type === 'duplicate') {
                await fetch('/api/rfid/manual-bind', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        user_id: 1, // Superadmin
                        rfid_uid: testUid
                    })
                });
            }

            await fetch('/api/iot/rfid/scan', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    uid: testUid,
                    device_id: 'MODAL-SIMULATOR'
                })
            });
        }
    };
}
</script>
@endsection
