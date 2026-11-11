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
        // 1. Kategori Kas (Master Data Kategori Pemasukan & Pengeluaran)
        Schema::create('kategori_kas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['pemasukan', 'pengeluaran'])->default('pemasukan');
            $table->enum('tingkat', ['RW', 'RT'])->default('RW');
            $table->string('deskripsi')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 2. Rekening & QRIS Kas RW / RT
        Schema::create('rekening_kas', function (Blueprint $table) {
            $table->id();
            $table->enum('tingkat', ['RW', 'RT'])->default('RW');
            $table->string('rt', 5)->default('01');
            $table->string('nama_bank'); // BCA, Mandiri, BRI, QRIS RW, GoPay, DANA
            $table->string('nomor_rekening');
            $table->string('atas_nama');
            $table->enum('tipe', ['bank', 'qris', 'ewallet'])->default('bank');
            $table->text('qris_image')->nullable();
            $table->string('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 3. Kelola Tagihan Warga
        Schema::create('tagihan_wargas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tagihan')->unique();
            $table->string('judul');
            $table->enum('tingkat', ['RW', 'RT'])->default('RW');
            $table->string('rt', 5)->default('01');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_warga');
            $table->string('nik')->nullable();
            $table->string('nomor_rumah')->nullable();
            $table->string('periode')->default('November 2026');
            $table->decimal('nominal', 15, 2);
            $table->string('kategori')->default('Iuran Bulanan');
            $table->enum('status', ['belum_bayar', 'menunggu_verifikasi', 'lunas'])->default('belum_bayar');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->foreignId('rekening_id')->nullable()->constrained('rekening_kas')->nullOnDelete();
            $table->string('bukti_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->string('diverifikasi_oleh')->nullable();
            $table->timestamps();
        });

        // 4. Tambah kolom pelengkap ke tabel kas
        Schema::table('kas', function (Blueprint $table) {
            $table->string('pihak_terkait')->nullable()->after('keterangan');
            $table->string('metode')->nullable()->default('Tunai')->after('pihak_terkait');
            $table->foreignId('tagihan_id')->nullable()->after('metode')->constrained('tagihan_wargas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas', function (Blueprint $table) {
            $table->dropForeign(['tagihan_id']);
            $table->dropColumn(['pihak_terkait', 'metode', 'tagihan_id']);
        });
        Schema::dropIfExists('tagihan_wargas');
        Schema::dropIfExists('rekening_kas');
        Schema::dropIfExists('kategori_kas');
    }
};
