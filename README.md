# SiRW Digital - Sistem Informasi Manajemen Rukun Warga (RW 05 Sukamaju)

Aplikasi web modern manajemen lingkungan Rukun Warga berbasis **Laravel 12 (Backend)** dan **Vite + Tailwind CSS + Alpine.js (Frontend)** dengan arsitektur terpisah 2 folder (`frontend` & `backend`).

**Repository GitHub:** [https://github.com/Demoy-younger2/RUWA.git](https://github.com/Demoy-younger2/RUWA.git)

---

## 📁 Struktur Direktori (2 Folder Architecture)

```
d:\Rukun_Warga\
├── backend\                     # [BACKEND] Framework Laravel 12 & REST API
│   ├── app\                     # Controllers, Models, Middleware (RBAC)
│   ├── config\                  # Konfigurasi CORS, database, session
│   ├── database\                # Migrations, Seeders, database.sqlite
│   ├── routes\                  # api.php (33 REST endpoints) & web.php
│   └── artisan.bat              # Runner artisan lokal
│
├── frontend\                    # [FRONTEND] Client SPA (Vite + Tailwind + Alpine.js)
│   ├── src\
│   │   ├── main.js              # State manajemen, router SPA, Axios API client
│   │   └── style.css            # Tailwind CSS styling
│   ├── index.html               # Halaman SPA interaktif mobile & desktop
│   ├── package.json             # Dependensi frontend
│   └── vite.config.js           # Konfigurasi proxy /api ke http://127.0.0.1:8000
│
├── jalankan-aplikasi.bat        # 🚀 1-Click Runner: Menjalankan Backend + Frontend sekaligus
├── start-backend.bat            # Menjalankan backend Laravel saja (port 8000)
├── start-frontend.bat           # Menjalankan frontend Vite saja (port 5173)
└── .vscode\                     # Konfigurasi VS Code (PATH php & terminal task)
```

---

## 🚀 Cara Menjalankan Aplikasi

### Cara 1: Sangat Mudah (1-Click Runner)
Cukup **double-click** file:
👉 **`jalankan-aplikasi.bat`**

File ini akan:
1. Menjalankan server backend di `http://127.0.0.1:8000`
2. Menjalankan server frontend di `http://localhost:5173`
3. Membuka Google Chrome langsung ke `http://localhost:5173`

---

### Cara 2: Menjalankan Manual di Terminal / VS Code

**Terminal 1 (Backend):**
```powershell
cd backend
php artisan serve --port=8000
```
*(Atau jalankan: `.\start-backend.bat`)*

**Terminal 2 (Frontend):**
```powershell
cd frontend
npm run dev
```
*(Atau jalankan: `.\start-frontend.bat`)*

Buka browser di: **`http://localhost:5173`**

---

## 👥 Kredensial 8 Role Pengguna

Kata sandi bawaan untuk semua akun demo: **`password`**. Di dalam aplikasi frontend, tersedia **1-Click Quick Role Switcher** di bagian header atas untuk langsung berganti peran kapan saja.

| Role | Nama Akun | Email Login | Hak Akses Utama |
|---|---|---|---|
| **Superadmin** | Administrator Pusat | `superadmin@rukunwarga.id` | Konfigurasi sistem penuh, audit trail, reset log, manajemen akun pengguna. |
| **Ketua RW** | H. Bambang Sutrisno | `rw@rukunwarga.id` | Tanda tangan & terbitkan surat resmi, monitoring kas RW, profil wilayah. |
| **Ketua RT** | Joko Prasetyo | `rt@rukunwarga.id` | Verifikasi permohonan surat warga RT, pembagian jadwal ronda pos kamling. |
| **Sekretaris RW** | Siti Rahmawati | `sekretaris.rw@rukunwarga.id` | Administrasi arsip surat, agenda kegiatan bersama gotong royong. |
| **Bendahara RW** | Hendra Wijaya | `bendahara.rw@rukunwarga.id` | Pengelolaan kas umum RW, laporan keuangan transparan, bank sampah. |
| **Sekretaris RT** | Dewi Lestari | `sekretaris.rt01@rukunwarga.id` | Notulensi, administrasi warga RT 01, pencatatan posyandu balita/lansia. |
| **Bendahara RT** | Agus Salim | `bendahara.rt01@rukunwarga.id` | Pembukuan uang kas bulanan dan iuran kebersihan tingkat RT 01. |
| **Warga** | Budi Santoso | `warga@rukunwarga.id` | Ajukan surat, belanja UMKM, setor bank sampah, simpan pinjam koperasi, SOS. |

---

## 📋 14 Modul Utama

1. **Dashboard Adaptif**: Metrik khusus per role + tombol darurat SOS siskamling.
2. **Data Warga**: Manajemen kependudukan, KK, NIK, status domisili, filter RT.
3. **Surat Menyurat**: Alur RT $\rightarrow$ RW + cetak lembar surat resmi berkop garuda.
4. **Kas Keuangan**: Arus kas transparan tingkat RW & RT (pemasukan/pengeluaran).
5. **Keamanan & Ronda**: Jadwal giliran jaga pos kamling + broadcast alert darurat SOS.
6. **Bank Sampah**: Katalog harga limbah per kg, pencatatan setoran, penarikan saldo tunai.
7. **Pasar UMKM**: Etalase produk warga + tombol pemesanan langsung via WhatsApp.
8. **Posyandu Pintar**: KMS balita (berat/tinggi badan) dan pemeriksaan lansia.
9. **Kegiatan & Agenda**: Kalender gotong royong, kerja bakti, peringatan HUT RI.
10. **RUKEM (Rukun Kematian)**: Santunan duka warga wafat dan inventaris perlengkapan.
11. **Aspirasi & Aduan**: Pelaporan fasilitas rusak dengan opsi anonim dan tanggapan resmi.
12. **Aset & Fasilitas**: Inventaris tenda, kursi, genset, stok ketersediaan, formulir pinjam.
13. **Koperasi Warga Mandiri**: Kartu anggota digital, simpanan pokok/wajib/sukarela, pinjaman modal.
14. **Log Aktivitas & Audit**: Jejak audit mutasi data seluruh pengguna (Timeline & Tabel).
