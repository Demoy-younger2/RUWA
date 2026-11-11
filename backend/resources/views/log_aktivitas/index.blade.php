@extends('layouts.app', ['title' => 'Log Aktivitas & Audit Trail'])

@section('content')
<div class="space-y-6" x-data="{ viewMode: 'timeline', clearModal: false }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                <span>Log Aktivitas & Jejak Audit Sistem</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Catatan transparan seluruh mutasi data, verifikasi surat, transaksi keuangan kas/koperasi, dan sinyal darurat lingkungan.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- View Mode Switcher -->
            <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-bold">
                <button type="button" @click="viewMode = 'timeline'" :class="viewMode === 'timeline' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <i class="fa-solid fa-timeline"></i>
                    <span>Timeline</span>
                </button>
                <button type="button" @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <i class="fa-solid fa-table-list"></i>
                    <span>Tabel</span>
                </button>
            </div>

            @if(Auth::user()->isSuperAdmin())
                <button type="button" @click="clearModal = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                    <i class="fa-solid fa-trash-can"></i>
                    <span class="hidden sm:inline">Bersihkan Log</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Log Tercatat</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($totalLogs) }}</span>
                <span class="text-xs text-slate-400 font-semibold">kejadian</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">Aktivitas Hari Ini</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-emerald-600">{{ number_format($logsHariIni) }}</span>
                <span class="text-xs text-emerald-500 font-semibold">aksi baru</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider block">Aksi Pengurus RT/RW</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-blue-600">{{ number_format($aksiPengurus) }}</span>
                <span class="text-xs text-blue-500 font-semibold">verifikasi</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider block">Sinyal Darurat SOS</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-rose-600">{{ number_format($alarmDarurat) }}</span>
                <span class="text-xs text-rose-500 font-semibold">peringatan</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar Card -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('log-aktivitas.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama pelaku, isi deskripsi, atau kata kunci..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>

            <!-- Filter Modul -->
            <div>
                <select name="modul" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="">Semua Modul</option>
                    @foreach($modulList as $m)
                        <option value="{{ $m }}" {{ request('modul') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tindakan -->
            <div>
                <select name="tindakan" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="">Semua Tindakan</option>
                    @foreach($tindakanList as $t)
                        <option value="{{ $t }}" {{ request('tindakan') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tanggal & Reset -->
            <div class="flex items-center gap-2">
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white outline-none">
                @if(request()->anyFilled(['q', 'modul', 'tindakan', 'role', 'tanggal']))
                    <a href="{{ route('log-aktivitas.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TIMELINE / FEED VIEW -->
    <div x-show="viewMode === 'timeline'" class="space-y-4">
        <div class="relative pl-6 sm:pl-8 before:absolute before:left-3 sm:before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 space-y-4">
            @forelse($logs as $log)
                @php
                    $actionBadge = $log->action_badge;
                @endphp
                <div class="relative group">
                    <!-- Timeline Node Icon -->
                    <div class="absolute -left-6 sm:-left-8 top-3.5 w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-white border-2 border-slate-300 group-hover:border-emerald-500 flex items-center justify-center text-xs shadow-xs transition z-10">
                        <i class="{{ $log->icon }}"></i>
                    </div>

                    <!-- Log Item Card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-xs hover:border-emerald-300 hover:shadow-md transition space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-extrabold text-sm text-slate-900">{{ $log->user_name }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ ucfirst(str_replace('_', ' ', $log->user_role)) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $actionBadge['bg'] }} {{ $actionBadge['text'] }} flex items-center gap-1">
                                    <i class="fa-solid {{ $actionBadge['icon'] }} text-[9px]"></i>
                                    <span>{{ $log->tindakan }}</span>
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 font-mono uppercase bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                                    {{ $log->modul }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 text-slate-400 text-[11px]">
                                <span title="{{ $log->created_at->format('d M Y, H:i:s') }}" class="font-medium">
                                    <i class="fa-regular fa-clock mr-1"></i>{{ $log->created_at->diffForHumans() }}
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="font-mono text-[10px]">{{ $log->created_at->format('H:i') }} WIB</span>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-xs text-slate-700 leading-relaxed font-medium">
                            {{ $log->deskripsi }}
                        </p>

                        <!-- Footer Meta (IP, User Agent, Delete) -->
                        <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-400">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="flex items-center gap-1 font-mono text-[10px]">
                                    <i class="fa-solid fa-network-wired text-slate-300"></i>
                                    <span>IP: {{ $log->ip_address ?? '127.0.0.1' }}</span>
                                </span>
                                @if($log->user_agent)
                                    <span class="flex items-center gap-1 truncate max-w-xs text-[10px]" title="{{ $log->user_agent }}">
                                        <i class="fa-solid fa-laptop text-slate-300"></i>
                                        <span class="truncate">{{ Str::limit($log->user_agent, 40) }}</span>
                                    </span>
                                @endif
                            </div>

                            @if(Auth::user()->isSuperAdmin())
                                <form method="POST" action="{{ route('log-aktivitas.destroy', $log->id) }}" onsubmit="return confirm('Hapus baris log ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 transition p-1" title="Hapus log">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center">
                    <i class="fa-solid fa-clock-rotate-left text-4xl text-slate-300 mb-3"></i>
                    <h4 class="text-base font-bold text-slate-800">Tidak ada riwayat aktivitas</h4>
                    <p class="text-xs text-slate-500 mt-1">Belum ada aktivitas yang tercatat atau filter pencarian Anda tidak menemukan hasil.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider bg-slate-50/50">
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Modul</th>
                        <th class="py-3.5 px-4">Tindakan</th>
                        <th class="py-3.5 px-4">Deskripsi Kejadian</th>
                        <th class="py-3.5 px-4">Alamat IP</th>
                        @if(Auth::user()->isSuperAdmin())
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        @php
                            $actionBadge = $log->action_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 block">{{ $log->user_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ ucfirst(str_replace('_', ' ', $log->user_role)) }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-700">
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="{{ $log->icon }} text-xs"></i>
                                    <span>{{ $log->modul }}</span>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $actionBadge['bg'] }} {{ $actionBadge['text'] }}">
                                    {{ $log->tindakan }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700 max-w-md truncate" title="{{ $log->deskripsi }}">
                                {{ $log->deskripsi }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-400 text-[11px]">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                            @if(Auth::user()->isSuperAdmin())
                                <td class="py-3 px-4 text-right">
                                    <form method="POST" action="{{ route('log-aktivitas.destroy', $log->id) }}" onsubmit="return confirm('Hapus baris log ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-rose-600 p-1 transition" title="Hapus log">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isSuperAdmin() ? 7 : 6 }}" class="py-8 text-center text-slate-400">
                                Tidak ada log aktivitas yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Links -->
    <div class="pt-2">
        {{ $logs->links() }}
    </div>

    <!-- Modal Konfirmasi Bersihkan Semua Log (Superadmin) -->
    @if(Auth::user()->isSuperAdmin())
        <div x-show="clearModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl" @click.away="clearModal = false">
                <div class="flex items-center gap-3 text-rose-600">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Kosongkan Log Audit?</h3>
                        <p class="text-xs text-slate-400">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus seluruh riwayat aktivitas sistem? Seluruh jejak audit audit trail akan dibersihkan dan sistem akan mencatat kejadian pembersihan ini sebagai entri log awal yang baru.
                </p>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="clearModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('log-aktivitas.clear_all') }}">
                        @csrf
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/20">
                            Ya, Bersihkan Seluruh Log
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
