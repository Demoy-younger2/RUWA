<?php

namespace Database\Seeders;

use App\Models\KategoriKas;
use App\Models\RekeningKas;
use App\Models\TagihanWarga;
use App\Models\User;
use App\Models\Kas;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;

class FinanceAdvancedSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori Kas
        $kategoriList = [
            // Pemasukan
            ['nama' => 'Iuran Warga Bulanan', 'jenis' => 'pemasukan', 'tingkat' => 'RW', 'deskripsi' => 'Iuran rutin seluruh KK setiap bulan untuk kas umum RW'],
            ['nama' => 'Iuran Kebersihan Lingkungan', 'jenis' => 'pemasukan', 'tingkat' => 'RT', 'deskripsi' => 'Retribusi kebersihan dan pengangkutan sampah tingkat RT'],
            ['nama' => 'Iuran Keamanan & Ronda', 'jenis' => 'pemasukan', 'tingkat' => 'RT', 'deskripsi' => 'Iuran keamanan pos kamling dan operasional siskamling'],
            ['nama' => 'Donasi Swadaya & Dana Sosial', 'jenis' => 'pemasukan', 'tingkat' => 'RW', 'deskripsi' => 'Sumbangan sukarela donatur warga atau simpatisan RW'],
            ['nama' => 'Hasil Penjualan Bank Sampah', 'jenis' => 'pemasukan', 'tingkat' => 'RW', 'deskripsi' => 'Bagi hasil penjualan limbah anorganik bank sampah'],
            ['nama' => 'Bagi Hasil Usaha Koperasi', 'jenis' => 'pemasukan', 'tingkat' => 'RW', 'deskripsi' => 'Sisa Hasil Usaha (SHU) dan jasa simpan pinjam koperasi'],
            // Pengeluaran
            ['nama' => 'Operasional Pos Ronda & Keamanan', 'jenis' => 'pengeluaran', 'tingkat' => 'RT', 'deskripsi' => 'Konsumsi ronda, pengadaan baterai senter, jas hujan'],
            ['nama' => 'Biaya Angkut Sampah & TPS', 'jenis' => 'pengeluaran', 'tingkat' => 'RT', 'deskripsi' => 'Honor petugas kebersihan gerobak sampah RT'],
            ['nama' => 'Perbaikan Fasilitas & Lampu Jalan', 'jenis' => 'pengeluaran', 'tingkat' => 'RW', 'deskripsi' => 'Pembelian lampu LED penerangan jalan umum & semen'],
            ['nama' => 'Santunan Duka & RUKEM', 'jenis' => 'pengeluaran', 'tingkat' => 'RW', 'deskripsi' => 'Santunan duka warga wafat dan perlengkapan pemakaman'],
            ['nama' => 'Pengadaan Aset & Tenda RW', 'jenis' => 'pengeluaran', 'tingkat' => 'RW', 'deskripsi' => 'Perawatan tenda, sound system, kursi warga'],
            ['nama' => 'Konsumsi Gotong Royong', 'jenis' => 'pengeluaran', 'tingkat' => 'RT', 'deskripsi' => 'Snack dan makan minum kerja bakti pembersihan selokan'],
            ['nama' => 'Administrasi & ATK Surat', 'jenis' => 'pengeluaran', 'tingkat' => 'RW', 'deskripsi' => 'Kertas formulir, tinta printer, amplop dinas kop RW'],
        ];

        foreach ($kategoriList as $k) {
            KategoriKas::firstOrCreate(
                ['nama' => $k['nama'], 'jenis' => $k['jenis']],
                $k
            );
        }

        // 2. Rekening & QRIS Kas
        $rekeningList = [
            [
                'tingkat' => 'RW',
                'rt' => '01',
                'nama_bank' => 'Bank Central Asia (BCA)',
                'nomor_rekening' => '7120-8899-01',
                'atas_nama' => 'KAS RW 05 SUKAMAJU',
                'tipe' => 'bank',
                'qris_image' => 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=00020101021126570014ID.LINKAJA.WWW01189360091800000000010208123456780303UMI51440014ID.GO.BI.QRIS0102125204481453033605802ID5916KAS%20RW%2005%20SUKAMAJU6007BANDUNG61054011562070703A016304C2D1',
                'keterangan' => 'Rekening utama penampungan iuran warga dan donasi tingkat RW',
                'is_aktif' => true,
            ],
            [
                'tingkat' => 'RW',
                'rt' => '01',
                'nama_bank' => 'Bank Mandiri',
                'nomor_rekening' => '137-00-98213-4',
                'atas_nama' => 'KAS BERSAMA RW 05',
                'tipe' => 'bank',
                'qris_image' => null,
                'keterangan' => 'Rekening cadangan pembangunan dan dana sosial RW',
                'is_aktif' => true,
            ],
            [
                'tingkat' => 'RW',
                'rt' => '01',
                'nama_bank' => 'QRIS Kas RW 05 (Semua Bank / E-Wallet)',
                'nomor_rekening' => 'NMID: ID1020261100588',
                'atas_nama' => 'QRIS RESMI RW 05',
                'tipe' => 'qris',
                'qris_image' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=00020101021226590014ID.CO.QRIS.WWW01189360050300000505010215ID10202611005880303UMI51440014ID.GO.BI.QRIS0102125204599953033605802ID5916KAS%20RW%2005%20RESMI6007JAKARTA61051234062070703A016304B9E4',
                'keterangan' => 'Scan QRIS via BCA Mobile, Livin, BRImo, GoPay, OVO, ShopeePay, DANA',
                'is_aktif' => true,
            ],
            [
                'tingkat' => 'RT',
                'rt' => '01',
                'nama_bank' => 'Bank Rakyat Indonesia (BRI)',
                'nomor_rekening' => '0341-01-002341-50-8',
                'atas_nama' => 'KAS KEUANGAN RT 01',
                'tipe' => 'bank',
                'qris_image' => null,
                'keterangan' => 'Rekening iuran bulanan warga dan operasional ronda RT 01',
                'is_aktif' => true,
            ],
            [
                'tingkat' => 'RT',
                'rt' => '01',
                'nama_bank' => 'GoPay / DANA Kas RT 01',
                'nomor_rekening' => '0812-9988-7711',
                'atas_nama' => 'Bendahara RT 01 (Agus Salim)',
                'tipe' => 'ewallet',
                'qris_image' => 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=https://gopay.co.id/transfer/kas-rt01',
                'keterangan' => 'Penerimaan non-tunai dompet digital warga RT 01',
                'is_aktif' => true,
            ],
        ];

        $rekeningMap = [];
        foreach ($rekeningList as $r) {
            $rek = RekeningKas::firstOrCreate(
                ['nama_bank' => $r['nama_bank'], 'nomor_rekening' => $r['nomor_rekening']],
                $r
            );
            $rekeningMap[$rek->nama_bank] = $rek->id;
        }

        // 3. Tagihan Warga
        $wargaUser = User::where('role', 'warga')->first();
        $bcaRekId = RekeningKas::where('nama_bank', 'like', '%BCA%')->value('id');
        $qrisRekId = RekeningKas::where('tipe', 'qris')->value('id');

        $tagihanList = [
            [
                'kode_tagihan' => 'TGH-202611-001',
                'judul' => 'Iuran Warga & Kebersihan RT 01 - November 2026',
                'tingkat' => 'RT',
                'rt' => '01',
                'user_id' => $wargaUser?->id,
                'nama_warga' => $wargaUser?->name ?? 'Budi Santoso',
                'nik' => '3271011504880001',
                'nomor_rumah' => 'Blok B3 No. 12',
                'periode' => 'November 2026',
                'nominal' => 50000,
                'kategori' => 'Iuran Kebersihan Lingkungan',
                'status' => 'menunggu_verifikasi',
                'tanggal_jatuh_tempo' => '2026-11-20',
                'tanggal_bayar' => '2026-11-05',
                'metode_pembayaran' => 'QRIS Resmi Kas RW',
                'rekening_id' => $qrisRekId,
                'bukti_bayar' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&auto=format&fit=crop&q=60',
                'catatan' => 'Sudah bayar via scan QRIS aplikasi BRImo sebesar Rp 50.000.',
                'diverifikasi_oleh' => null,
            ],
            [
                'kode_tagihan' => 'TGH-202611-002',
                'judul' => 'Iuran Pembangunan Balai Warga RW 05 - Periode Q4',
                'tingkat' => 'RW',
                'rt' => '01',
                'user_id' => $wargaUser?->id,
                'nama_warga' => $wargaUser?->name ?? 'Budi Santoso',
                'nik' => '3271011504880001',
                'nomor_rumah' => 'Blok B3 No. 12',
                'periode' => 'November 2026',
                'nominal' => 100000,
                'kategori' => 'Donasi Swadaya & Dana Sosial',
                'status' => 'belum_bayar',
                'tanggal_jatuh_tempo' => '2026-11-25',
                'tanggal_bayar' => null,
                'metode_pembayaran' => null,
                'rekening_id' => $bcaRekId,
                'bukti_bayar' => null,
                'catatan' => 'Sumbangan sukarela renovasi kanopi balai warga.',
                'diverifikasi_oleh' => null,
            ],
            [
                'kode_tagihan' => 'TGH-202610-003',
                'judul' => 'Iuran Warga & Kebersihan RT 01 - Oktober 2026',
                'tingkat' => 'RT',
                'rt' => '01',
                'user_id' => $wargaUser?->id,
                'nama_warga' => $wargaUser?->name ?? 'Budi Santoso',
                'nik' => '3271011504880001',
                'nomor_rumah' => 'Blok B3 No. 12',
                'periode' => 'Oktober 2026',
                'nominal' => 50000,
                'kategori' => 'Iuran Kebersihan Lingkungan',
                'status' => 'lunas',
                'tanggal_jatuh_tempo' => '2026-10-20',
                'tanggal_bayar' => '2026-10-10',
                'metode_pembayaran' => 'Transfer Bank BCA',
                'rekening_id' => $bcaRekId,
                'bukti_bayar' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&auto=format&fit=crop&q=60',
                'catatan' => 'Lunas diverifikasi oleh Bendahara RT Agus Salim',
                'diverifikasi_oleh' => 'Agus Salim (Bendahara RT 01)',
            ],
            [
                'kode_tagihan' => 'TGH-202611-004',
                'judul' => 'Iuran Ronda & Siskamling RT 01 - November 2026',
                'tingkat' => 'RT',
                'rt' => '01',
                'user_id' => null,
                'nama_warga' => 'H. Suryadi',
                'nik' => '3271011202750003',
                'nomor_rumah' => 'Blok A1 No. 04',
                'periode' => 'November 2026',
                'nominal' => 35000,
                'kategori' => 'Iuran Keamanan & Ronda',
                'status' => 'belum_bayar',
                'tanggal_jatuh_tempo' => '2026-11-20',
                'tanggal_bayar' => null,
                'metode_pembayaran' => null,
                'rekening_id' => null,
                'bukti_bayar' => null,
                'catatan' => 'Iuran pos ronda malam.',
                'diverifikasi_oleh' => null,
            ],
            [
                'kode_tagihan' => 'TGH-202611-005',
                'judul' => 'Iuran Sampah RT 02 - November 2026',
                'tingkat' => 'RT',
                'rt' => '02',
                'user_id' => null,
                'nama_warga' => 'Wahyu Hidayat',
                'nik' => '3271012005820005',
                'nomor_rumah' => 'Blok C2 No. 08',
                'periode' => 'November 2026',
                'nominal' => 40000,
                'kategori' => 'Iuran Kebersihan Lingkungan',
                'status' => 'lunas',
                'tanggal_jatuh_tempo' => '2026-11-20',
                'tanggal_bayar' => '2026-11-02',
                'metode_pembayaran' => 'Tunai',
                'rekening_id' => null,
                'bukti_bayar' => null,
                'catatan' => 'Diterima tunai oleh Bendahara RT.',
                'diverifikasi_oleh' => 'Bendahara RT 02',
            ],
        ];

        foreach ($tagihanList as $t) {
            TagihanWarga::firstOrCreate(
                ['kode_tagihan' => $t['kode_tagihan']],
                $t
            );
        }

        // 4. Update transaksi kas dengan pihak_terkait & metode
        Kas::where('jenis', 'pemasukan')->whereNull('pihak_terkait')->update([
            'pihak_terkait' => 'Warga Lingkungan RW 05',
            'metode' => 'Transfer Bank',
        ]);
        Kas::where('jenis', 'pengeluaran')->whereNull('pihak_terkait')->update([
            'pihak_terkait' => 'Penyedia Jasa / Toko Terkait',
            'metode' => 'Tunai',
        ]);
    }
}
