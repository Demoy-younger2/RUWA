<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::where('role', 'superadmin')->first();
        $rw = User::where('role', 'rw')->first();
        $rt = User::where('role', 'rt')->first();
        $sekretarisRw = User::where('role', 'sekretaris_rw')->first();
        $bendaharaRw = User::where('role', 'bendahara_rw')->first();
        $warga = User::where('role', 'warga')->first();

        $sampleLogs = [
            [
                'user' => $warga,
                'modul' => 'Surat',
                'tindakan' => 'Pengajuan',
                'deskripsi' => 'Mengajukan permohonan Surat Pengantar SKCK untuk keperluan melamar pekerjaan.',
                'minutes_ago' => 5,
            ],
            [
                'user' => $rt,
                'modul' => 'Surat',
                'tindakan' => 'Verifikasi',
                'deskripsi' => 'Ketua RT 01 memverifikasi permohonan surat pengantar warga (Budi Santoso) dan meneruskan ke RW.',
                'minutes_ago' => 12,
            ],
            [
                'user' => $rw,
                'modul' => 'Surat',
                'tindakan' => 'Persetujuan',
                'deskripsi' => 'Ketua RW menandatangani dan menerbitkan Surat Pengantar Resmi No. SRT/01/2026/001.',
                'minutes_ago' => 20,
            ],
            [
                'user' => $bendaharaRw,
                'modul' => 'Kas',
                'tindakan' => 'Tambah',
                'deskripsi' => 'Mencatat pemasukan Iuran Warga Bulanan RT 01 s/d RT 06 sebesar Rp 3.500.000.',
                'minutes_ago' => 45,
            ],
            [
                'user' => $warga,
                'modul' => 'Bank Sampah',
                'tindakan' => 'Setor',
                'deskripsi' => 'Menyetor 6.5 Kg sampah kardus dan botol plastik ke Bank Sampah Berkah RW 05 (Nilai: Rp 26.000).',
                'minutes_ago' => 75,
            ],
            [
                'user' => $warga,
                'modul' => 'Koperasi',
                'tindakan' => 'Transaksi',
                'deskripsi' => 'Menyetorkan simpanan wajib bulanan anggota koperasi sebesar Rp 20.000.',
                'minutes_ago' => 120,
            ],
            [
                'user' => $rt,
                'modul' => 'Ronda',
                'tindakan' => 'Tambah',
                'deskripsi' => 'Menyusun dan mempublikasikan jadwal giliran siskamling pos ronda malam minggu ini.',
                'minutes_ago' => 180,
            ],
            [
                'user' => $warga,
                'modul' => 'Aspirasi',
                'tindakan' => 'Pengajuan',
                'deskripsi' => 'Mengirimkan aduan fasilitas umum: Lampu penerangan jalan di Gang Melati RT 02 padam.',
                'minutes_ago' => 240,
            ],
            [
                'user' => $sekretarisRw,
                'modul' => 'Kegiatan',
                'tindakan' => 'Tambah',
                'deskripsi' => 'Mempublikasikan agenda: Kerja Bakti Massal Pembersihan Saluran Air Menghadapi Musim Hujan.',
                'minutes_ago' => 360,
            ],
            [
                'user' => $warga,
                'modul' => 'Aset',
                'tindakan' => 'Peminjaman',
                'deskripsi' => 'Mengajukan peminjaman 2 unit Tenda Kerucut dan 50 Kursi Lipat untuk syukuran keluarga.',
                'minutes_ago' => 480,
            ],
            [
                'user' => $superadmin,
                'modul' => 'Pengaturan',
                'tindakan' => 'Ubah',
                'deskripsi' => 'Memperbarui konfigurasi alamat sekretariat balai warga dan nomor hotline darurat RW 05.',
                'minutes_ago' => 600,
            ],
            [
                'user' => $warga,
                'modul' => 'Auth',
                'tindakan' => 'Login',
                'deskripsi' => 'Pengguna Budi Santoso berhasil masuk ke dalam sistem portal SiRW Digital.',
                'minutes_ago' => 720,
            ],
        ];

        foreach ($sampleLogs as $log) {
            $u = $log['user'];
            ActivityLog::create([
                'user_id' => $u ? $u->id : null,
                'user_name' => $u ? $u->name : 'Sistem / Anonim',
                'user_role' => $u ? $u->role : 'warga',
                'modul' => $log['modul'],
                'tindakan' => $log['tindakan'],
                'deskripsi' => $log['deskripsi'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130.0.0.0',
                'created_at' => Carbon::now()->subMinutes($log['minutes_ago']),
                'updated_at' => Carbon::now()->subMinutes($log['minutes_ago']),
            ]);
        }
    }
}
