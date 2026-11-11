<?php

namespace Database\Seeders;

use App\Models\Aset;
use App\Models\Aspirasi;
use App\Models\BankSampah;
use App\Models\Kas;
use App\Models\Kegiatan;
use App\Models\KoperasiAnggota;
use App\Models\KoperasiTransaksi;
use App\Models\PeminjamanAset;
use App\Models\Pengaturan;
use App\Models\Posyandu;
use App\Models\Ronda;
use App\Models\Rukem;
use App\Models\Surat;
use App\Models\TransaksiBankSampah;
use App\Models\Umkm;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Buat User untuk 8 Role
        $superadmin = User::create([
            'name' => 'Bambang Pratama (Superadmin)',
            'email' => 'superadmin@rukunwarga.id',
            'password' => $password,
            'role' => 'superadmin',
            'nik' => '3201010000000001',
            'phone' => '081234567890',
            'rt' => '01',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        $rw = User::create([
            'name' => 'H. Suryana, S.Pd. (Ketua RW)',
            'email' => 'rw@rukunwarga.id',
            'password' => $password,
            'role' => 'rw',
            'nik' => '3201010000000002',
            'phone' => '081234567891',
            'rt' => '01',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        $rt = User::create([
            'name' => 'Agus Budiman (Ketua RT 01)',
            'email' => 'rt01@rukunwarga.id',
            'password' => $password,
            'role' => 'rt',
            'nik' => '3201010000000003',
            'phone' => '081234567892',
            'rt' => '01',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        $sekretaris_rw = User::create([
            'name' => 'Dedi Iskandar (Sekretaris RW)',
            'email' => 'sekretaris.rw@rukunwarga.id',
            'password' => $password,
            'role' => 'sekretaris_rw',
            'nik' => '3201010000000004',
            'phone' => '081234567893',
            'rt' => '02',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        $bendahara_rw = User::create([
            'name' => 'Hj. Ratna Sari (Bendahara RW)',
            'email' => 'bendahara.rw@rukunwarga.id',
            'password' => $password,
            'role' => 'bendahara_rw',
            'nik' => '3201010000000005',
            'phone' => '081234567894',
            'rt' => '01',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        $sekretaris_rt = User::create([
            'name' => 'Eko Prasetyo (Sekretaris RT 01)',
            'email' => 'sekretaris.rt@rukunwarga.id',
            'password' => $password,
            'role' => 'sekretaris_rt',
            'nik' => '3201010000000006',
            'phone' => '081234567895',
            'rt' => '01',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        $bendahara_rt = User::create([
            'name' => 'Rina Wulandari (Bendahara RT 01)',
            'email' => 'bendahara.rt@rukunwarga.id',
            'password' => $password,
            'role' => 'bendahara_rt',
            'nik' => '3201010000000007',
            'phone' => '081234567896',
            'rt' => '01',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        $wargaUser = User::create([
            'name' => 'Budi Santoso (Warga RT 01)',
            'email' => 'warga@rukunwarga.id',
            'password' => $password,
            'role' => 'warga',
            'nik' => '3201012304850001',
            'phone' => '081298765432',
            'rt' => '01',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        $wargaUser2 = User::create([
            'name' => 'Siti Aminah (Warga RT 02)',
            'email' => 'siti@rukunwarga.id',
            'password' => $password,
            'role' => 'warga',
            'nik' => '3201014506890002',
            'phone' => '081377889900',
            'rt' => '02',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        // 2. Data Pengaturan Lingkungan RW
        Pengaturan::create([
            'nama_aplikasi' => 'SiRW-Digital: Sistem Rukun Warga Terpadu',
            'nama_rw' => 'RW 05 Sukamaju',
            'nama_kelurahan' => 'Kelurahan Sukamaju',
            'nama_kecamatan' => 'Kecamatan Cibadak',
            'nama_kota' => 'Kabupaten Sukabumi',
            'kode_pos' => '43351',
            'nama_ketua_rw' => 'H. Suryana, S.Pd.',
            'sambutan_rw' => 'Selamat datang di Portal Digital RW 05 Sukamaju. Bersama mewujudkan lingkungan rukun, aman, transparan, dan berdaya saing melalui teknologi cerdas.',
            'kontak_rw' => '0812-3456-7890',
            'email_rw' => 'sekretariat.rw05@sukamaju.desa.id',
            'alamat_sekretariat' => 'Balai Warga RW 05, Jl. Melati No. 12, Sukamaju',
        ]);

        // 3. Data Warga Lengkap
        $wargaBudi = Warga::create([
            'user_id' => $wargaUser->id,
            'nik' => '3201012304850001',
            'no_kk' => '3201011002050012',
            'nama_lengkap' => 'Budi Santoso',
            'tempat_lahir' => 'Sukabumi',
            'tanggal_lahir' => '1985-04-23',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'pendidikan' => 'S1 Teknik Informatika',
            'pekerjaan' => 'Karyawan Swasta',
            'status_perkawinan' => 'Kawin',
            'status_keluarga' => 'Kepala Keluarga',
            'status_tinggal' => 'Tetap',
            'rt' => '01',
            'rw' => '05',
            'alamat' => 'Jl. Kenanga No. 4, RT 01 / RW 05',
            'no_hp' => '081298765432',
        ]);

        $wargaSiti = Warga::create([
            'user_id' => $wargaUser2->id,
            'nik' => '3201014506890002',
            'no_kk' => '3201011002050013',
            'nama_lengkap' => 'Siti Aminah',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1989-06-15',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'pendidikan' => 'D3 Manajemen',
            'pekerjaan' => 'Wiraswasta / Pemilik UMKM',
            'status_perkawinan' => 'Kawin',
            'status_keluarga' => 'Istri',
            'status_tinggal' => 'Tetap',
            'rt' => '02',
            'rw' => '05',
            'alamat' => 'Jl. Mawar No. 8, RT 02 / RW 05',
            'no_hp' => '081377889900',
        ]);

        Warga::create([
            'nik' => '3201015609950003',
            'no_kk' => '3201011002050012',
            'nama_lengkap' => 'Dewi Lestari',
            'tempat_lahir' => 'Sukabumi',
            'tanggal_lahir' => '1995-09-16',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'status_perkawinan' => 'Kawin',
            'status_keluarga' => 'Istri',
            'status_tinggal' => 'Tetap',
            'rt' => '01',
            'rw' => '05',
            'alamat' => 'Jl. Kenanga No. 4, RT 01 / RW 05',
            'no_hp' => '081298765433',
        ]);

        Warga::create([
            'nik' => '3201012111150004',
            'no_kk' => '3201011002050012',
            'nama_lengkap' => 'Rizky Ramadhan',
            'tempat_lahir' => 'Sukabumi',
            'tanggal_lahir' => '2015-11-21',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'pendidikan' => 'SD',
            'pekerjaan' => 'Pelajar',
            'status_perkawinan' => 'Belum Kawin',
            'status_keluarga' => 'Anak',
            'status_tinggal' => 'Tetap',
            'rt' => '01',
            'rw' => '05',
            'alamat' => 'Jl. Kenanga No. 4, RT 01 / RW 05',
            'no_hp' => '-',
        ]);

        Warga::create([
            'nik' => '3201011202600005',
            'no_kk' => '3201011002050014',
            'nama_lengkap' => 'H. Mulyadi',
            'tempat_lahir' => 'Cianjur',
            'tanggal_lahir' => '1960-02-12',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'pendidikan' => 'Pensiunan PNS',
            'pekerjaan' => 'Pensiunan',
            'status_perkawinan' => 'Kawin',
            'status_keluarga' => 'Kepala Keluarga',
            'status_tinggal' => 'Tetap',
            'rt' => '03',
            'rw' => '05',
            'alamat' => 'Jl. Anggrek No. 15, RT 03 / RW 05',
            'no_hp' => '085712349988',
        ]);

        // 4. Data Surat Menyurat
        Surat::create([
            'nomor_surat' => '470/012/SRW/2026',
            'user_id' => $wargaUser->id,
            'warga_id' => $wargaBudi->id,
            'jenis_surat' => 'Surat Pengantar SKCK',
            'keperluan' => 'Persyaratan melamar pekerjaan di Badan Usaha Milik Negara (BUMN)',
            'keterangan_tambahan' => 'Pemohon berkelakuan baik dan tercatat aktif sebagai warga RT 01.',
            'status' => 'selesai',
            'catatan_rt' => 'Data sesuai dan warga aktif iuran.',
            'catatan_rw' => 'Disetujui. Surat pengantar telah ditandatangani digital.',
            'disetujui_rt_oleh' => 'Agus Budiman (Ketua RT 01)',
            'disetujui_rw_oleh' => 'H. Suryana, S.Pd. (Ketua RW 05)',
            'tanggal_diajukan' => now()->subDays(5),
            'tanggal_disetujui_rt' => now()->subDays(4),
            'tanggal_disetujui_rw' => now()->subDays(3),
        ]);

        Surat::create([
            'nomor_surat' => '470/018/SRW/2026',
            'user_id' => $wargaUser2->id,
            'warga_id' => $wargaSiti->id,
            'jenis_surat' => 'Surat Keterangan Usaha (SKU)',
            'keperluan' => 'Pengajuan bantuan modal usaha mikro KUR BRI',
            'keterangan_tambahan' => 'Memiliki usaha catering rumahan "Dapur Bu Siti" sejak tahun 2021.',
            'status' => 'disetujui_rt',
            'catatan_rt' => 'Usaha aktif beroperasi di rumah RT 02.',
            'disetujui_rt_oleh' => 'Agus Budiman (Ketua RT 01)',
            'tanggal_diajukan' => now()->subDays(2),
            'tanggal_disetujui_rt' => now()->subDay(),
        ]);

        Surat::create([
            'nomor_surat' => '470/021/SRW/2026',
            'user_id' => $wargaUser->id,
            'warga_id' => $wargaBudi->id,
            'jenis_surat' => 'Surat Keterangan Domisili',
            'keperluan' => 'Pendaftaran sekolah anak di SMP Negeri 1',
            'keterangan_tambahan' => 'Tinggal menetap di wilayah RT 01 RW 05 sejak 2012.',
            'status' => 'diajukan',
            'tanggal_diajukan' => now()->subHours(6),
        ]);

        // 5. Data Kas Keuangan (RW & RT)
        Kas::create([
            'tingkat' => 'RW',
            'rt' => '01',
            'rw' => '05',
            'user_id' => $bendahara_rw->id,
            'kategori' => 'Iuran Warga Tingkat RW',
            'jenis' => 'pemasukan',
            'jumlah' => 2500000,
            'tanggal' => now()->subDays(10),
            'keterangan' => 'Penerimaan iuran pemeliharaan lingkungan RW dari RT 01, 02, 03 bulan Oktober',
        ]);

        Kas::create([
            'tingkat' => 'RW',
            'rt' => '01',
            'rw' => '05',
            'user_id' => $bendahara_rw->id,
            'kategori' => 'Kas RW',
            'jenis' => 'pengeluaran',
            'jumlah' => 450000,
            'tanggal' => now()->subDays(7),
            'keterangan' => 'Penggantian lampu penerangan jalan utama RW 05 (5 titik LED)',
        ]);

        Kas::create([
            'tingkat' => 'RT',
            'rt' => '01',
            'rw' => '05',
            'user_id' => $bendahara_rt->id,
            'kategori' => 'Iuran Keamanan & Kebersihan',
            'jenis' => 'pemasukan',
            'jumlah' => 1200000,
            'tanggal' => now()->subDays(8),
            'keterangan' => 'Setoran iuran warga RT 01 (24 KK x Rp 50.000)',
        ]);

        Kas::create([
            'tingkat' => 'RT',
            'rt' => '01',
            'rw' => '05',
            'user_id' => $bendahara_rt->id,
            'kategori' => 'Operasional RT',
            'jenis' => 'pengeluaran',
            'jumlah' => 300000,
            'tanggal' => now()->subDays(4),
            'keterangan' => 'Kopi, konsumsi, dan baterai senter Pos Ronda RT 01',
        ]);

        // 6. Data Keamanan & Ronda
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $petugasJaga = [
            'Senin' => ['Budi Santoso', 'Hendro Wijaya', 'Pak Joko'],
            'Selasa' => ['Agus Budiman', 'Yayan Hendra', 'Dani Kurniawan'],
            'Rabu' => ['Dedi Iskandar', 'Ahmad Fauzi', 'Rudi Tabuti'],
            'Kamis' => ['Eko Prasetyo', 'Bambang Supeno', 'Ferry Irawan'],
            'Jumat' => ['H. Mulyadi', 'Iwan Falsafah', 'Teguh Prakoso'],
            'Sabtu' => ['Warga Regu Muda', 'Karang Taruna RT 01', 'Budi Santoso'],
            'Minggu' => ['Koordinator Keamanan', 'Satpam Lingkungan', 'Sukardi'],
        ];

        foreach ($hariList as $hari) {
            foreach ($petugasJaga[$hari] as $nama) {
                Ronda::create([
                    'rt' => '01',
                    'rw' => '05',
                    'hari' => $hari,
                    'tanggal' => now()->startOfWeek()->addDays(array_search($hari, $hariList)),
                    'nama_petugas' => $nama,
                    'user_id' => ($nama === 'Budi Santoso') ? $wargaUser->id : null,
                    'pos_ronda' => 'Pos Ronda Utama RT 01',
                    'jam_jaga' => '22:00 - 04:00',
                    'status_kehadiran' => ($hari === 'Senin' || $hari === 'Selasa') ? 'hadir' : 'dijadwalkan',
                    'catatan_patroli' => ($hari === 'Senin') ? 'Patroli keliling 3 putaran jam 23:30, 01:30, dan 03:00. Situasi aman terkendali.' : null,
                ]);
            }
        }

        // 7. Data Bank Sampah
        $bsPlastik = BankSampah::create([
            'nama_kategori' => 'Plastik Botol & Gelas Bersih',
            'harga_per_kg' => 3500,
            'satuan' => 'kg',
            'deskripsi' => 'Botol air mineral, botol pembersih, gelas cup bening dalam keadaan bersih dan dipipihkan.',
        ]);

        $bsKardus = BankSampah::create([
            'nama_kategori' => 'Kardus & Kertas Bekas',
            'harga_per_kg' => 2200,
            'satuan' => 'kg',
            'deskripsi' => 'Kardus coklat packing, kertas HVS putih, buku tulis dan koran bekas.',
        ]);

        $bsLogam = BankSampah::create([
            'nama_kategori' => 'Kaleng, Besi & Logam',
            'harga_per_kg' => 6000,
            'satuan' => 'kg',
            'deskripsi' => 'Kaleng minuman, potongan besi sisa bangunan, seng, tembaga, dan aluminium.',
        ]);

        $bsJelantah = BankSampah::create([
            'nama_kategori' => 'Minyak Jelantah (UCO)',
            'harga_per_kg' => 7000,
            'satuan' => 'liter',
            'deskripsi' => 'Minyak goreng sisa pakai yang telah disaring dan disimpan dalam jerigen tertutup.',
        ]);

        TransaksiBankSampah::create([
            'kode_transaksi' => 'TRX-BS-202611-001',
            'user_id' => $wargaUser->id,
            'bank_sampah_id' => $bsPlastik->id,
            'jenis_transaksi' => 'setor',
            'berat' => 12.5,
            'total_rupiah' => 12.5 * 3500,
            'tanggal' => now()->subDays(6),
            'status' => 'selesai',
            'catatan' => 'Penyetoran 2 kantong botol plastik pilahan rumah tangga',
        ]);

        TransaksiBankSampah::create([
            'kode_transaksi' => 'TRX-BS-202611-002',
            'user_id' => $wargaUser->id,
            'bank_sampah_id' => $bsKardus->id,
            'jenis_transaksi' => 'setor',
            'berat' => 20.0,
            'total_rupiah' => 20.0 * 2200,
            'tanggal' => now()->subDays(3),
            'status' => 'selesai',
            'catatan' => 'Kardus paket belanja online',
        ]);

        // 8. Data UMKM Warga
        Umkm::create([
            'user_id' => $wargaUser2->id,
            'nama_usaha' => 'Dapur Bu Siti - Katering & Nasi Kotak',
            'nama_pemilik' => 'Siti Aminah',
            'kategori' => 'Makanan & Minuman',
            'deskripsi' => 'Menyediakan aneka nasi bento, tumpeng mini, snack box arisan, dan lauk harian khas Sunda siap santap.',
            'harga_mulai' => 15000,
            'no_whatsapp' => '081377889900',
            'alamat' => 'Jl. Mawar No. 8, RT 02 / RW 05',
            'rt' => '02',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        Umkm::create([
            'user_id' => $wargaUser->id,
            'nama_usaha' => 'Berkah Laundry Kiloan & Satuan',
            'nama_pemilik' => 'Budi Santoso & Keluarga',
            'kategori' => 'Jasa',
            'deskripsi' => 'Layanan cuci komplit, setrika uap, cuci bed cover, sepatu, dan karpet dengan deterjen ramah lingkungan. Gratis jemput antar area RW 05!',
            'harga_mulai' => 7000,
            'no_whatsapp' => '081298765432',
            'alamat' => 'Jl. Kenanga No. 4, RT 01 / RW 05',
            'rt' => '01',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        Umkm::create([
            'user_id' => $superadmin->id,
            'nama_usaha' => 'Keripik Singkong & Pisang Renyah "Mak Nyus"',
            'nama_pemilik' => 'Ibu Ningsih',
            'kategori' => 'Makanan & Minuman',
            'deskripsi' => 'Camilan olahan singkong dan pisang asli perkebunan lokal, gurih renyah tanpa bahan pengawet kimia.',
            'harga_mulai' => 12000,
            'no_whatsapp' => '085811223344',
            'alamat' => 'Jl. Cempaka No. 2, RT 03 / RW 05',
            'rt' => '03',
            'rw' => '05',
            'status' => 'aktif',
        ]);

        // 9. Data Posyandu
        Posyandu::create([
            'kategori' => 'balita',
            'nama_peserta' => 'Rizky Ramadhan',
            'nik' => '3201012111150004',
            'tanggal_pemeriksaan' => now()->subDays(12),
            'usia_bulan_tahun' => 24, // 24 bulan
            'berat_badan' => 12.8,
            'tinggi_badan' => 88.5,
            'status_kesehatan' => 'Gizi Baik (Hijau)',
            'imunisasi_atau_vitamin' => 'Vitamin A & Vaksin Campak Booster',
            'catatan_kader' => 'Pertumbuhan sangat baik, motorik aktif.',
            'nama_kader' => 'Bidan Nina & Kader Posyandu Melati',
        ]);

        Posyandu::create([
            'kategori' => 'lansia',
            'nama_peserta' => 'H. Mulyadi',
            'nik' => '3201011202600005',
            'tanggal_pemeriksaan' => now()->subDays(12),
            'usia_bulan_tahun' => 66, // 66 tahun
            'berat_badan' => 64.0,
            'tinggi_badan' => 165.0,
            'tensi_darah' => '125/80 mmHg',
            'gula_darah' => 110,
            'status_kesehatan' => 'Normal & Sehat',
            'imunisasi_atau_vitamin' => 'Suplemen Kalsium & Vitamin B Kompleks',
            'catatan_kader' => 'Tetap rutin jalan pagi 30 menit per hari.',
            'nama_kader' => 'Kader Posyandu Lansia Sejahtera',
        ]);

        // 10. Data Kegiatan Warga
        Kegiatan::create([
            'judul' => 'Kerja Bakti Bersih Lingkungan & Fogging Nyamuk DBD',
            'kategori' => 'Kerja Bakti',
            'penyelenggara' => 'Pengurus RW 05 & Seluruh RT 01-03',
            'tanggal_mulai' => now()->addDays(3)->setHour(7)->setMinute(0),
            'tanggal_selesai' => now()->addDays(3)->setHour(11)->setMinute(30),
            'lokasi' => 'Sepanjang Saluran Drainase Utama RW 05 dan Balai Warga',
            'deskripsi' => 'Kerja bakti pembersihan selokan, pemangkasan dahan pohon rawan tumbang, dan pengasapan fogging antisipasi musim penghujan.',
            'anggaran' => 750000,
            'status' => 'akan_datang',
        ]);

        Kegiatan::create([
            'judul' => 'Senam Kebugaran Lansia & Cek Kesehatan Gratis',
            'kategori' => 'Senam Sehat',
            'penyelenggara' => 'PKK RW 05 & Puskesmas Pembantu',
            'tanggal_mulai' => now()->addDays(7)->setHour(6)->setMinute(30),
            'tanggal_selesai' => now()->addDays(7)->setHour(9)->setMinute(0),
            'lokasi' => 'Lapangan Olahraga RW 05',
            'deskripsi' => 'Senam jantung sehat bersama instruktur berlisensi, dilanjutkan cek tensi dan konsultasi nutrisi keluarga.',
            'anggaran' => 300000,
            'status' => 'akan_datang',
        ]);

        Kegiatan::create([
            'judul' => 'Rapat Pleno Pengurus RT & RW Triwulan',
            'kategori' => 'Rapat Warga',
            'penyelenggara' => 'Pengurus RW 05',
            'tanggal_mulai' => now()->subDays(14)->setHour(19)->setMinute(30),
            'tanggal_selesai' => now()->subDays(14)->setHour(22)->setMinute(0),
            'lokasi' => 'Aula Balai RW 05',
            'deskripsi' => 'Evaluasi keuangan kas RW, pembahasan program portal digital rukun warga, dan rencana perbaikan jalan paving blok RT 02.',
            'anggaran' => 250000,
            'status' => 'selesai',
        ]);

        // 11. Data RUKEM (Rukun Kematian)
        Rukem::create([
            'nama_almarhum' => 'Alm. Bapak Suwandi bin Kartono',
            'tanggal_meninggal' => now()->subDays(20),
            'jam_meninggal' => '04:15',
            'nama_ahli_waris' => 'Ibu Endang Suryati',
            'no_hp_ahli_waris' => '081233445566',
            'alamat_duka' => 'Jl. Kenanga No. 10, RT 01 / RW 05',
            'rt' => '01',
            'rw' => '05',
            'lokasi_pemakaman' => 'TPU Kampung Baru Sukamaju',
            'jam_pemakaman' => '10:00',
            'jumlah_santunan' => 1500000,
            'status_santunan' => 'sudah_diserahkan',
            'tanggal_penyerahan_santunan' => now()->subDays(19),
            'petugas_penyerah' => 'H. Suryana (Ketua RW) & Agus Budiman (Ketua RT 01)',
            'fasilitas_disiapkan' => 'Keranda Jenazah, Tenda Duka 2 Unit, Sound Pengajian, Perlengkapan Memandikan Jenazah',
        ]);

        // 12. Data Aspirasi & Pengaduan
        Aspirasi::create([
            'user_id' => $wargaUser->id,
            'kategori' => 'Infrastruktur',
            'judul' => 'Lampu Penerangan Jalan Gang 3 RT 01 Padam',
            'isi' => 'Mohon bantuan pengurus RT untuk perbaikan bohlam lampu penerangan jalan di ujung Gang 3 RT 01, jalan sangat gelap saat malam dan rawan untuk pejalan kaki.',
            'lokasi' => 'Tiang Listrik Gang 3 depan rumah No. 14',
            'status' => 'selesai',
            'tanggapan' => 'Sudah diganti dengan lampu LED 30 Watt yang baru pada tanggal 4 November oleh tim ronda malam RT 01. Terima kasih atas laporannya.',
            'ditanggapi_oleh' => 'Agus Budiman (Ketua RT 01)',
            'is_anonim' => false,
        ]);

        Aspirasi::create([
            'user_id' => $wargaUser2->id,
            'kategori' => 'Kebersihan',
            'judul' => 'Usulan Penambahan Jadwal Pengangkutan Sampah',
            'isi' => 'Mengingat volume sampah meningkat saat akhir pekan, apakah memungkinkan pengangkutan sampah dari armada kebersihan ditambah pada hari Minggu sore?',
            'lokasi' => 'Tempat Penampungan Sementara RT 02',
            'status' => 'diproses',
            'tanggapan' => 'Usulan sedang dikoordinasikan dengan petugas Dinas Lingkungan Hidup kecamatan.',
            'ditanggapi_oleh' => 'Dedi Iskandar (Sekretaris RW)',
            'is_anonim' => false,
        ]);

        // 13. Data Aset RW & Peminjaman
        $asetTenda = Aset::create([
            'kode_aset' => 'AST-RW-001',
            'nama_aset' => 'Tenda Terop Besi Pipa 4x6 Meter',
            'kategori' => 'Tenda',
            'tingkat' => 'RW',
            'rt' => '01',
            'rw' => '05',
            'jumlah_total' => 4,
            'jumlah_tersedia' => 3,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Gudang Balai RW 05',
            'keterangan' => 'Lengkap dengan terpal putih tebal dan kawat pengikat.',
        ]);

        $asetKursi = Aset::create([
            'kode_aset' => 'AST-RW-002',
            'nama_aset' => 'Kursi Plastik Napolly Hijau',
            'kategori' => 'Meja & Kursi',
            'tingkat' => 'RW',
            'rt' => '01',
            'rw' => '05',
            'jumlah_total' => 150,
            'jumlah_tersedia' => 100,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Gudang Balai RW 05',
            'keterangan' => 'Tersusun rapi dalam rak khusus.',
        ]);

        Aset::create([
            'kode_aset' => 'AST-RW-003',
            'nama_aset' => 'Sound System Portable Wireless + 2 Mic',
            'kategori' => 'Sound System',
            'tingkat' => 'RW',
            'rt' => '01',
            'rw' => '05',
            'jumlah_total' => 2,
            'jumlah_tersedia' => 2,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Sekretariat RW 05',
            'keterangan' => 'Dapat digunakan dengan baterai aki untuk rapat outdoor/senam.',
        ]);

        PeminjamanAset::create([
            'aset_id' => $asetKursi->id,
            'user_id' => $wargaUser->id,
            'jumlah_pinjam' => 50,
            'tanggal_pinjam' => now()->addDays(2),
            'tanggal_kembali_rencana' => now()->addDays(4),
            'keperluan' => 'Acara syukuran khitanan keluarga di rumah',
            'status' => 'disetujui',
            'disetujui_oleh' => 'Sekretaris RW 05',
            'catatan' => 'Harap dikembalikan dalam keadaan bersih dan lengkap.',
        ]);

        // 14. Data Koperasi Warga
        $anggotaKoperasi = KoperasiAnggota::create([
            'user_id' => $wargaUser->id,
            'no_anggota' => 'KOP-RW05-001',
            'tanggal_daftar' => now()->subMonths(6),
            'total_simpanan' => 1250000,
            'total_pinjaman' => 0,
            'status' => 'aktif',
        ]);

        KoperasiTransaksi::create([
            'koperasi_anggota_id' => $anggotaKoperasi->id,
            'jenis' => 'simpanan_pokok',
            'nominal' => 250000,
            'tanggal' => now()->subMonths(6),
            'keterangan' => 'Setoran Simpanan Pokok awal pendaftaran',
            'status' => 'lunas',
        ]);

        KoperasiTransaksi::create([
            'koperasi_anggota_id' => $anggotaKoperasi->id,
            'jenis' => 'simpanan_wajib',
            'nominal' => 200000,
            'tanggal' => now()->subMonths(1),
            'keterangan' => 'Simpanan Wajib bulan Oktober',
            'status' => 'lunas',
        ]);
    }
}
