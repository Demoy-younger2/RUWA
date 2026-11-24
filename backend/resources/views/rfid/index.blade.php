@extends('layouts.app', ['title' => 'IoT e-KTP & RFID Reader ESP32'])

@section('content')
<div class="space-y-6" x-data="rfidView({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->rfid_uid ?? '' }}')">

    <!-- Header Halaman Bersih & Modern -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold shadow-xs">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">IoT e-KTP & RFID Reader</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Aktif
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Layanan pendaftaran dan integrasi kartu identitas e-KTP / RFID warga berbasis mikrokontroler ESP32.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" @click="openModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition active:scale-95">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Hubungkan e-KTP Baru</span>
            </button>
        </div>
    </div>

    <!-- Grid Utama: Kartu e-KTP & Panel Kontrol -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KARTU e-KTP DIGITAL (Kolom Kiri 5-Grid) -->
        <div class="lg:col-span-5 flex flex-col">
            <div class="bg-gradient-to-tr from-slate-900 via-sky-950 to-blue-950 text-white rounded-2xl p-6 shadow-lg border border-sky-500/20 flex flex-col justify-between min-h-[260px] relative overflow-hidden">
                
                <!-- Header Kartu -->
                <div>
                    <div class="flex items-start justify-between border-b border-white/10 pb-3">
                        <div>
                            <span class="text-[9px] font-bold tracking-widest uppercase text-sky-300 block">REPUBLIK INDONESIA</span>
                            <span class="text-xs font-extrabold tracking-wider text-white block">KARTU TANDA PENDUDUK (e-KTP)</span>
                            <span class="text-[10px] text-slate-400 font-medium">RW 05 SUKAMAJU DIGITAL</span>
                        </div>
                        <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-white/10 text-sky-200 text-[10px] font-mono font-bold">
                            <i class="fa-solid fa-wifi rotate-90 text-[9px]"></i>
                            <span>13.56 MHz</span>
                        </div>
                    </div>

                    <!-- Chip & Status -->
                    <div class="mt-4 flex items-center justify-between">
                        <!-- Chip Emas Realistis Mini -->
                        <div class="w-10 h-7 rounded bg-gradient-to-br from-amber-300 to-yellow-500 border border-amber-200 p-0.5 shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between h-2 border-b border-amber-600/50">
                                <span class="w-2 border-r border-amber-600/50"></span>
                                <span class="w-2 border-l border-amber-600/50"></span>
                            </div>
                            <div class="flex justify-between h-2">
                                <span class="w-2 border-r border-amber-600/50"></span>
                                <span class="w-2 border-l border-amber-600/50"></span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <template x-if="userUid">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                TERDAFTAR
                            </span>
                        </template>
                        <template x-if="!userUid">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                BELUM TERHUBUNG
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Info Warga & UID Kartu -->
                <div class="mt-5 space-y-2">
                    <div>
                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-semibold block">Nama Pemilik Akun</span>
                        <p class="text-sm font-bold text-white tracking-wide truncate" x-text="userName"></p>
                        <p class="text-[11px] text-sky-200/80">{{ $user->role_title }} • RT {{ $user->rt ?? '01' }} / RW {{ $user->rw ?? '05' }}</p>
                    </div>

                    <div class="pt-2 border-t border-white/10 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] uppercase tracking-wider text-slate-400 font-semibold block">UID Kartu e-KTP</span>
                            <template x-if="userUid">
                                <span class="font-mono text-xs font-bold text-emerald-300 tracking-wider" x-text="userUid"></span>
                            </template>
                            <template x-if="!userUid">
                                <span class="font-mono text-[11px] text-slate-400 italic">-- Belum terpasang --</span>
                            </template>
                        </div>

                        <div>
                            <template x-if="userUid">
                                <button type="button" @click="unbindCard()" class="text-[10px] font-bold text-rose-300 hover:text-white bg-rose-500/20 hover:bg-rose-500/40 border border-rose-500/30 px-2.5 py-1 rounded-lg transition">
                                    <i class="fa-solid fa-link-slash mr-1"></i> Lepas Kartu
                                </button>
                            </template>
                            <template x-if="!userUid">
                                <button type="button" @click="openModal()" class="text-[10px] font-bold text-emerald-200 hover:text-white bg-emerald-500/20 hover:bg-emerald-500/40 border border-emerald-500/30 px-2.5 py-1 rounded-lg transition">
                                    <i class="fa-solid fa-qrcode mr-1"></i> Hubungkan
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- PANEL STATUS & UJI COBA CEPAT (Kolom Kanan 7-Grid) -->
        <div class="lg:col-span-7 flex flex-col justify-between space-y-4">
            
            <!-- 2 Kartu Status Ringkas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Anti-Duplikasi Aktif</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                            1 kartu e-KTP hanya dapat terikat pada 1 akun. Ditolak jika sudah dipakai user lain.
                        </p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                        <i class="fa-solid fa-stopwatch"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Sesi Pairing 90 Detik</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                            Saat tombol scan ditekan, server membuka jendela pairing 90 detik untuk ESP32.
                        </p>
                    </div>
                </div>
            </div>

            <!-- LAB SIMULASI CEPAT (PENGUJIAN TANPA HARDWARE) -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-flask text-teal-600"></i>
                        <span>Lab Pengujian Server (Simulasi Tap Kartu)</span>
                    </h4>
                    <span class="text-[10px] text-slate-400 font-medium">Uji respon API tanpa alat</span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="simulateTap('new')" :disabled="simulating" 
                            class="px-3 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition disabled:opacity-50">
                        <i class="fa-solid fa-id-card text-emerald-600 mr-1"></i>
                        Simulasi Tap Kartu Baru
                    </button>

                    <button type="button" @click="simulateTap('duplicate')" :disabled="simulating" 
                            class="px-3 py-2 rounded-xl text-xs font-bold text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition disabled:opacity-50">
                        <i class="fa-solid fa-ban text-rose-600 mr-1"></i>
                        Simulasi Kartu Duplikat (Uji Ditolak)
                    </button>
                </div>

                <!-- Simulation Result Log -->
                <div x-show="simulationLog" x-cloak class="p-3 rounded-xl bg-slate-900 text-slate-100 font-mono text-[11px] space-y-1">
                    <div class="flex items-center justify-between text-[10px] text-slate-400 border-b border-slate-800 pb-1">
                        <span>Hasil Respon API:</span>
                        <button type="button" @click="simulationLog = ''" class="hover:text-white">&times;</button>
                    </div>
                    <p class="pt-1" :class="simSuccess ? 'text-emerald-400 font-bold' : 'text-rose-400 font-bold'" x-text="simulationLog"></p>
                </div>
            </div>

        </div>

    </div>

    <!-- TABEL RIWAYAT PEMINDAIAN REALTIME DARI ESP32 -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                    <span>Riwayat Pemindaian Kartu (Live IoT Log)</span>
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Catatan aktivitas tap kartu identitas e-KTP dan RFID yang diterima sistem</p>
            </div>
            <button type="button" @click="location.reload()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                <i class="fa-solid fa-rotate text-slate-400"></i>
                <span>Refresh</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-400 uppercase font-bold text-[10px]">
                    <tr>
                        <th class="py-2.5 px-4">Waktu</th>
                        <th class="py-2.5 px-4">Device</th>
                        <th class="py-2.5 px-4">UID Kartu</th>
                        <th class="py-2.5 px-4">Nama Pemilik</th>
                        <th class="py-2.5 px-4">Status</th>
                        <th class="py-2.5 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentScans as $scan)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-2.5 px-4 text-slate-500 font-mono text-[11px]">{{ $scan->created_at->format('H:i:s • d M Y') }}</td>
                            <td class="py-2.5 px-4 font-mono text-[11px] text-slate-700 font-semibold">{{ $scan->device_id }}</td>
                            <td class="py-2.5 px-4">
                                <span class="font-mono font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 text-[11px] border border-slate-200">{{ $scan->rfid_uid }}</span>
                            </td>
                            <td class="py-2.5 px-4 font-bold text-slate-900">{{ $scan->user ? $scan->user->name : '-' }}</td>
                            <td class="py-2.5 px-4">
                                @if($scan->status === 'registered')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check text-[9px]"></i> Sukses Terdaftar
                                    </span>
                                @elseif($scan->status === 'recognized')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Akses Diterima
                                    </span>
                                @elseif($scan->status === 'rejected_duplicate')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <i class="fa-solid fa-ban text-[9px]"></i> Ditolak (Duplikat)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-question text-[9px]"></i> Belum Terdaftar
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-slate-600 text-[11px]">{{ $scan->message ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                Belum ada riwayat tap kartu RFID. Silakan tempelkan kartu pada scanner ESP32.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL POPUP REGISTRASI e-KTP / RFID INTERAKTIF -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative border border-slate-100" @click.away="closeModal()">
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-base font-bold shadow-xs">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Registrasi Kartu e-KTP</h3>
                        <p class="text-xs text-slate-500">Mendaftarkan untuk: <span class="font-bold text-emerald-700" x-text="userName"></span></p>
                    </div>
                </div>
                <button type="button" @click="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Tab Navigasi Modal -->
            <div class="flex rounded-xl bg-slate-100 p-1 text-xs font-bold text-slate-600">
                <button type="button" @click="tab = 'esp32'; startSession()" 
                        :class="tab === 'esp32' ? 'bg-white text-emerald-800 shadow-xs' : 'hover:text-slate-900'"
                        class="flex-1 py-1.5 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-microchip"></i>
                    <span>Scan ESP32</span>
                </button>
                <button type="button" @click="tab = 'manual'; stopPolling()" 
                        :class="tab === 'manual' ? 'bg-white text-emerald-800 shadow-xs' : 'hover:text-slate-900'"
                        class="flex-1 py-1.5 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-keyboard"></i>
                    <span>Input Manual</span>
                </button>
                <button type="button" @click="tab = 'simulate'" 
                        :class="tab === 'simulate' ? 'bg-white text-emerald-800 shadow-xs' : 'hover:text-slate-900'"
                        class="flex-1 py-1.5 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-flask"></i>
                    <span>Simulasi Cepat</span>
                </button>
            </div>

            <!-- TAB 1: SCAN VIA ESP32 (REALTIME IOT) -->
            <div x-show="tab === 'esp32'" class="space-y-4">
                <!-- Status Menunggu Tap (Waiting) -->
                <div x-show="scanStatus === 'waiting'" class="p-6 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-center space-y-4">
                    <div class="relative w-16 h-16 mx-auto flex items-center justify-center">
                        <span class="absolute inset-0 rounded-full bg-emerald-400/20 animate-ping"></span>
                        <div class="relative w-14 h-14 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xl shadow-lg shadow-emerald-600/30">
                            <i class="fa-solid fa-wifi rotate-90"></i>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-extrabold text-emerald-950">Sensor ESP32 Siap Membaca</h4>
                        <p class="text-xs text-emerald-700 mt-1 max-w-xs mx-auto">
                            Silakan tempelkan kartu e-KTP atau RFID Anda ke reader ESP32...
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-emerald-200 text-xs text-emerald-800 font-mono font-bold shadow-xs">
                        <i class="fa-solid fa-clock text-emerald-600"></i>
                        <span>Sisa Waktu: <span x-text="countdown + 's'"></span></span>
                    </div>
                </div>

                <!-- Status Sukses Terdaftar (Registered) -->
                <div x-show="scanStatus === 'registered'" class="p-5 rounded-2xl bg-emerald-50 border border-emerald-300 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl mx-auto shadow-md shadow-emerald-500/20">
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
                    <div class="w-12 h-12 rounded-full bg-rose-600 text-white flex items-center justify-center text-xl mx-auto shadow-md shadow-rose-600/20">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-rose-950">PENDAFTARAN KARTU DITOLAK!</h4>
                        <p class="text-xs text-rose-700 mt-1 font-semibold" x-text="statusMessage"></p>
                    </div>
                    <p class="text-[11px] text-rose-600 bg-white p-2.5 rounded-xl border border-rose-200 leading-relaxed">
                        Satu kartu e-KTP hanya dapat diikat pada 1 akun warga saja demi keamanan sistem.
                    </p>
                </div>

                <!-- Status Error / Timeout -->
                <div x-show="scanStatus === 'error'" class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center text-xl mx-auto">
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

            <!-- TAB 2: INPUT MANUAL -->
            <div x-show="tab === 'manual'" class="space-y-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <label class="block text-xs font-bold text-slate-700">Masukkan UID Kartu e-KTP / RFID:</label>
                    <input type="text" x-model="manualUid" placeholder="Contoh: 04A1B2C3" maxlength="20"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-mono text-sm uppercase font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <p class="text-[11px] text-slate-500">
                        * Format UID heksadesimal dari kartu Anda.
                    </p>
                </div>

                <div x-show="manualFeedback" class="p-3 rounded-xl text-xs font-semibold"
                     :class="manualSuccess ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                     x-text="manualFeedback"></div>

                <button type="button" @click="submitManual()" :disabled="!manualUid || manualLoading"
                        class="w-full py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 transition shadow-sm">
                    <span x-show="!manualLoading"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Kartu</span>
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
                    Tutup
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
