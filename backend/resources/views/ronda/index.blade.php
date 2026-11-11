@extends('layouts.app', ['title' => 'Keamanan & Jadwal Ronda'])

@section('content')
<div class="space-y-6" x-data="{ panicModal: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-shield-halved text-rose-600"></i>
                <span>Keamanan Lingkungan & Jadwal Ronda</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Jadwal siskamling malam, presensi regu jaga pos, buku tamu dan sistem darurat siaga lingkungan.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Panic Button SOS Trigger -->
            <button @click="panicModal = true" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 shadow-lg shadow-rose-600/30 transition animate-pulse">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Tombol Darurat (SOS)</span>
            </button>

            @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'sekretaris_rt']))
                <a href="{{ route('ronda.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Atur Petugas Ronda</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Hari Tab -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap gap-2 text-xs font-semibold">
        <a href="{{ route('ronda.index') }}" class="px-3.5 py-1.5 rounded-xl transition {{ !request('hari') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua Hari
        </a>
        @foreach($hariList as $h)
            <a href="{{ route('ronda.index', ['hari' => $h]) }}" class="px-3.5 py-1.5 rounded-xl transition {{ request('hari') === $h ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                {{ $h }}
            </a>
        @endforeach
    </div>

    <!-- Cards Grid of Ronda Patrol Squads -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($rondas as $ronda)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800">
                            {{ $ronda->hari }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ronda->status_kehadiran === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ ucfirst($ronda->status_kehadiran) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 text-white flex items-center justify-center font-bold text-sm shrink-0">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $ronda->nama_petugas }}</h3>
                            <p class="text-[11px] text-slate-400">Wilayah RT {{ $ronda->rt }} &bull; RW {{ $ronda->rw }}</p>
                        </div>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-2xl mb-3">
                        <p><strong class="text-slate-700">Pos Ronda:</strong> {{ $ronda->pos_ronda }}</p>
                        <p><strong class="text-slate-700">Jam Jaga:</strong> {{ $ronda->jam_jaga }}</p>
                        @if($ronda->catatan_patroli)
                            <p class="text-emerald-700 font-medium italic mt-1 border-t border-slate-200/50 pt-1">
                                <i class="fa-solid fa-clipboard-check mr-1"></i> {{ $ronda->catatan_patroli }}
                            </p>
                        @endif
                        @if($ronda->laporan_kejadian)
                            <p class="text-rose-700 font-bold bg-rose-50 p-2 rounded-xl mt-1 border border-rose-200">
                                🚨 {{ $ronda->laporan_kejadian }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Presensi Buttons for Squad -->
                @if(in_array(Auth::user()->role, ['superadmin', 'rw', 'rt', 'sekretaris_rt']) || Auth::user()->name === $ronda->nama_petugas)
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                        <form method="POST" action="{{ route('ronda.presensi', $ronda->id) }}" class="flex-1 flex gap-1.5">
                            @csrf
                            <input type="hidden" name="status_kehadiran" value="hadir">
                            <input type="hidden" name="catatan_patroli" value="Hadir di pos tepat waktu dan patroli keliling lingkungan.">
                            <button type="submit" class="flex-1 py-1.5 px-2 rounded-xl font-bold text-[11px] text-white bg-emerald-600 hover:bg-emerald-700 transition">
                                <i class="fa-solid fa-check mr-1"></i> Absen Hadir
                            </button>
                        </form>

                        @if(in_array(Auth::user()->role, ['superadmin', 'rt']))
                            <form method="POST" action="{{ route('ronda.destroy', $ronda->id) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-3 py-12 text-center text-xs text-slate-400 bg-white rounded-3xl border border-slate-200">
                Tidak ada data jadwal ronda untuk hari yang dipilih.
            </div>
        @endforelse
    </div>

    @if($rondas->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $rondas->links() }}
        </div>
    @endif

    <!-- Panic Button Modal -->
    <div x-show="panicModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-transition>
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-rose-200" @click.away="panicModal = false">
            <div class="flex items-center gap-3 text-rose-600 mb-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Tombol Darurat Lingkungan (SOS)</h3>
                    <p class="text-xs text-slate-500">Kirim laporan kejadian kritis secara langsung</p>
                </div>
            </div>

            <form method="POST" action="{{ route('ronda.panic_button') }}" class="space-y-3 mt-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700">Jenis Kondisi Darurat</label>
                    <select name="jenis_darurat" required class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                        <option value="Pencurian / Orang Mencurigakan">Pencurian / Orang Mencurigakan</option>
                        <option value="Kebakaran">Kebakaran</option>
                        <option value="Medis / Butuh Ambulans">Darurat Medis / Ambulans</option>
                        <option value="Bencana Alam / Pohon Tumbang">Bencana Alam / Pohon Tumbang</option>
                        <option value="Keributan / Gangguan Keamanan">Keributan / Gangguan Keamanan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Lokasi Tepat Kejadian</label>
                    <input type="text" name="lokasi_kejadian" required placeholder="Contoh: Depan Pos Ronda RT 01 / Gang 4 No. 12" class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700">Keterangan Singkat</label>
                    <textarea name="keterangan" required rows="2" placeholder="Uraikan kondisi darurat yang sedang terjadi..." class="mt-1 block w-full px-3 py-2 text-xs rounded-xl border border-slate-300"></textarea>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button type="button" @click="panicModal = false" class="flex-1 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/30">
                        Kirim Sinyal Darurat
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
