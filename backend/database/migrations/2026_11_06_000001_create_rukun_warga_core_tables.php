<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Data Warga
        Schema::create('wargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nik', 16)->unique();
            $table->string('no_kk', 16)->nullable();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('agama', 30)->default('Islam');
            $table->string('pendidikan', 50)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('status_perkawinan', 30)->default('Kawin');
            $table->string('status_keluarga', 30)->default('Kepala Keluarga'); // Kepala Keluarga, Istri, Anak, Lainnya
            $table->string('status_tinggal', 30)->default('Tetap'); // Tetap, Kontrak, Kos
            $table->string('rt', 5)->default('01');
            $table->string('rw', 5)->default('05');
            $table->text('alamat')->nullable();
            $table->string('no_hp', 25)->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        // 2. Surat Menyurat
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique()->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->nullOnDelete();
            $table->string('jenis_surat'); // Surat Pengantar SKCK, Keterangan Domisili, Keterangan Usaha, SKTM, Kematian, Pindah
            $table->text('keperluan');
            $table->text('keterangan_tambahan')->nullable();
            $table->string('lampiran')->nullable();
            $table->enum('status', ['diajukan', 'disetujui_rt', 'disetujui_rw', 'ditolak', 'selesai'])->default('diajukan');
            $table->text('catatan_rt')->nullable();
            $table->text('catatan_rw')->nullable();
            $table->string('disetujui_rt_oleh')->nullable();
            $table->string('disetujui_rw_oleh')->nullable();
            $table->timestamp('tanggal_diajukan')->useCurrent();
            $table->timestamp('tanggal_disetujui_rt')->nullable();
            $table->timestamp('tanggal_disetujui_rw')->nullable();
            $table->timestamps();
        });

        // 3. Kas Keuangan (RT / RW)
        Schema::create('kas', function (Blueprint $table) {
            $table->id();
            $table->enum('tingkat', ['RW', 'RT'])->default('RT');
            $table->string('rt', 5)->default('01');
            $table->string('rw', 5)->default('05');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kategori'); // Iuran Bulanan, Keamanan, Kebersihan, Kas RW, Kas RT, Dana Sosial, Donasi, Operasional
            $table->enum('jenis', ['pemasukan', 'pengeluaran'])->default('pemasukan');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->text('keterangan');
            $table->string('bukti_transaksi')->nullable();
            $table->timestamps();
        });

        // 4. Keamanan & Ronda
        Schema::create('rondas', function (Blueprint $table) {
            $table->id();
            $table->string('rt', 5)->default('01');
            $table->string('rw', 5)->default('05');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])->default('Senin');
            $table->date('tanggal')->nullable();
            $table->string('nama_petugas');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pos_ronda')->default('Pos Ronda RT 01');
            $table->string('jam_jaga')->default('22:00 - 04:00');
            $table->enum('status_kehadiran', ['dijadwalkan', 'hadir', 'izin', 'alpa'])->default('dijadwalkan');
            $table->text('catatan_patroli')->nullable();
            $table->text('laporan_kejadian')->nullable();
            $table->timestamps();
        });

        // 5. Bank Sampah & Transaksi
        Schema::create('bank_sampahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori'); // Plastik Botol, Kardus/Kertas, Kaleng/Besi, Kaca, Minyak Jelantah
            $table->decimal('harga_per_kg', 12, 2);
            $table->string('satuan', 20)->default('kg');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('transaksi_bank_sampahs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bank_sampah_id')->nullable()->constrained('bank_sampahs')->nullOnDelete();
            $table->enum('jenis_transaksi', ['setor', 'tarik_saldo'])->default('setor');
            $table->decimal('berat', 8, 2)->default(0); // dalam kg
            $table->decimal('total_rupiah', 12, 2);
            $table->date('tanggal');
            $table->enum('status', ['selesai', 'pending', 'dibatalkan'])->default('selesai');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 6. UMKM Warga
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_usaha');
            $table->string('nama_pemilik');
            $table->string('kategori'); // Makanan & Minuman, Fashion, Kerajinan, Jasa, Sembako, Agrobisnis
            $table->text('deskripsi');
            $table->decimal('harga_mulai', 12, 2)->default(0);
            $table->string('no_whatsapp', 25);
            $table->string('alamat')->nullable();
            $table->string('rt', 5)->default('01');
            $table->string('rw', 5)->default('05');
            $table->string('foto_produk')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // 7. Posyandu (Balita & Lansia)
        Schema::create('posyandus', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['balita', 'lansia'])->default('balita');
            $table->string('nama_peserta');
            $table->string('nik', 16)->nullable();
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->nullOnDelete();
            $table->date('tanggal_pemeriksaan');
            $table->integer('usia_bulan_tahun')->nullable(); // umur
            $table->decimal('berat_badan', 5, 2)->nullable(); // kg
            $table->decimal('tinggi_badan', 5, 2)->nullable(); // cm
            $table->string('tensi_darah', 20)->nullable(); // Lansia
            $table->decimal('gula_darah', 6, 2)->nullable(); // Lansia
            $table->string('status_kesehatan')->nullable(); // Gizi Baik / Normal / Perlu Perhatian
            $table->string('imunisasi_atau_vitamin')->nullable();
            $table->text('catatan_kader')->nullable();
            $table->string('nama_kader')->nullable();
            $table->timestamps();
        });

        // 8. Kegiatan & Agenda Warga
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori'); // Kerja Bakti, Rapat Warga, Peringatan PHBN, Pengajian, Senam Sehat, Lomba
            $table->string('penyelenggara')->default('Pengurus RW 05');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai')->nullable();
            $table->string('lokasi');
            $table->text('deskripsi');
            $table->decimal('anggaran', 15, 2)->default(0);
            $table->string('foto_banner')->nullable();
            $table->enum('status', ['akan_datang', 'berlangsung', 'selesai', 'dibatalkan'])->default('akan_datang');
            $table->timestamps();
        });

        // 9. RUKEM (Rukun Kematian)
        Schema::create('rukems', function (Blueprint $table) {
            $table->id();
            $table->string('nama_almarhum');
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->nullOnDelete();
            $table->date('tanggal_meninggal');
            $table->time('jam_meninggal')->nullable();
            $table->string('nama_ahli_waris');
            $table->string('no_hp_ahli_waris', 25)->nullable();
            $table->text('alamat_duka');
            $table->string('rt', 5)->default('01');
            $table->string('rw', 5)->default('05');
            $table->string('lokasi_pemakaman')->nullable();
            $table->time('jam_pemakaman')->nullable();
            $table->decimal('jumlah_santunan', 12, 2)->default(1000000);
            $table->enum('status_santunan', ['belum_diserahkan', 'sudah_diserahkan'])->default('belum_diserahkan');
            $table->date('tanggal_penyerahan_santunan')->nullable();
            $table->string('petugas_penyerah')->nullable();
            $table->string('fasilitas_disiapkan')->nullable(); // Keranda, Tenda, Sound, Pemandian
            $table->timestamps();
        });

        // 10. Aspirasi & Pengaduan
        Schema::create('aspirasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kategori'); // Infrastruktur, Keamanan, Kebersihan, Pelayanan, Lainnya
            $table->string('judul');
            $table->text('isi');
            $table->string('lokasi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            $table->text('tanggapan')->nullable();
            $table->string('ditanggapi_oleh')->nullable();
            $table->boolean('is_anonim')->default(false);
            $table->timestamps();
        });

        // 11. Aset RW / RT & Peminjaman
        Schema::create('asets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->string('kategori'); // Tenda, Meja & Kursi, Sound System, Elektronik, Kebersihan, Keamanan
            $table->enum('tingkat', ['RW', 'RT'])->default('RW');
            $table->string('rt', 5)->default('01');
            $table->string('rw', 5)->default('05');
            $table->integer('jumlah_total')->default(1);
            $table->integer('jumlah_tersedia')->default(1);
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->string('lokasi_penyimpanan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('peminjaman_asets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_id')->constrained('asets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('jumlah_pinjam')->default(1);
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_rencana');
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->text('keperluan');
            $table->enum('status', ['menunggu', 'disetujui', 'dipinjam', 'dikembalikan', 'ditolak'])->default('menunggu');
            $table->string('disetujui_oleh')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 12. Koperasi Warga
        Schema::create('koperasi_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('no_anggota')->unique();
            $table->date('tanggal_daftar');
            $table->decimal('total_simpanan', 15, 2)->default(0);
            $table->decimal('total_pinjaman', 15, 2)->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('koperasi_transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koperasi_anggota_id')->constrained('koperasi_anggotas')->cascadeOnDelete();
            $table->enum('jenis', ['simpanan_pokok', 'simpanan_wajib', 'simpanan_sukarela', 'pinjaman', 'angsuran', 'penarikan'])->default('simpanan_wajib');
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['lunas', 'proses', 'pending'])->default('lunas');
            $table->timestamps();
        });

        // 13. Pengaturan Sistem RW
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aplikasi')->default('Sistem Informasi Rukun Warga');
            $table->string('nama_rw')->default('RW 05');
            $table->string('nama_kelurahan')->default('Sukamaju');
            $table->string('nama_kecamatan')->default('Cibadak');
            $table->string('nama_kota')->default('Kabupaten Sukabumi');
            $table->string('kode_pos', 10)->default('43351');
            $table->string('nama_ketua_rw')->default('H. Suryana, S.Pd.');
            $table->text('sambutan_rw')->nullable();
            $table->string('kontak_rw', 25)->default('0812-3456-7890');
            $table->string('email_rw')->default('rw05.sukamaju@gmail.com');
            $table->text('alamat_sekretariat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
        Schema::dropIfExists('koperasi_transaksis');
        Schema::dropIfExists('koperasi_anggotas');
        Schema::dropIfExists('peminjaman_asets');
        Schema::dropIfExists('asets');
        Schema::dropIfExists('aspirasis');
        Schema::dropIfExists('rukems');
        Schema::dropIfExists('kegiatans');
        Schema::dropIfExists('posyandus');
        Schema::dropIfExists('umkms');
        Schema::dropIfExists('transaksi_bank_sampahs');
        Schema::dropIfExists('bank_sampahs');
        Schema::dropIfExists('rondas');
        Schema::dropIfExists('kas');
        Schema::dropIfExists('surats');
        Schema::dropIfExists('wargas');
    }
};
