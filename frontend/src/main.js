import './style.css';
import Alpine from 'alpinejs';
import axios from 'axios';

// Konfigurasi Base API URL Laravel Backend
const API_BASE = '/api';

window.appState = () => ({
    // State Navigasi
    currentView: 'dashboard', // dashboard, warga, surat, kas, ronda, bank_sampah, umkm, posyandu, kegiatan, rukem, aspirasi, aset, koperasi, log_aktivitas, pengaturan, welcome
    sidebarOpen: false,
    mobileNav: false,
    backendStatus: 'checking', // checking, connected, offline

    // User Session & Role (Default Ketua RW atau Warga)
    currentUser: {
        id: 2,
        name: 'H. Bambang Sutrisno, S.E.',
        email: 'rw@rukunwarga.id',
        role: 'rw',
        role_title: 'Ketua RW',
        rt: '01',
        rw: '05',
    },

    // 8 Roles Configuration
    rolesList: [
        { key: 'superadmin', title: 'Super Admin', name: 'Administrator Pusat', email: 'superadmin@rukunwarga.id', icon: 'fa-user-shield', color: 'text-purple-600', badge: 'bg-purple-100 text-purple-800' },
        { key: 'rw', title: 'Ketua RW', name: 'H. Bambang Sutrisno, S.E.', email: 'rw@rukunwarga.id', icon: 'fa-building-columns', color: 'text-indigo-600', badge: 'bg-indigo-100 text-indigo-800' },
        { key: 'rt', title: 'Ketua RT 01', name: 'Joko Prasetyo', email: 'rt01@rukunwarga.id', icon: 'fa-house-user', color: 'text-blue-600', badge: 'bg-blue-100 text-blue-800' },
        { key: 'sekretaris_rw', title: 'Sekretaris RW', name: 'Siti Rahmawati, S.Sos.', email: 'sekretaris.rw@rukunwarga.id', icon: 'fa-file-signature', color: 'text-amber-600', badge: 'bg-amber-100 text-amber-800' },
        { key: 'bendahara_rw', title: 'Bendahara RW', name: 'Hendra Wijaya, S.E.', email: 'bendahara.rw@rukunwarga.id', icon: 'fa-money-bill-transfer', color: 'text-teal-600', badge: 'bg-teal-100 text-teal-800' },
        { key: 'sekretaris_rt', title: 'Sekretaris RT 01', name: 'Dewi Lestari', email: 'sekretaris.rt01@rukunwarga.id', icon: 'fa-pen-to-square', color: 'text-orange-600', badge: 'bg-orange-100 text-orange-800' },
        { key: 'bendahara_rt', title: 'Bendahara RT 01', name: 'Agus Salim', email: 'bendahara.rt01@rukunwarga.id', icon: 'fa-wallet', color: 'text-lime-600', badge: 'bg-lime-100 text-lime-800' },
        { key: 'warga', title: 'Warga RT 01', name: 'Budi Santoso', email: 'warga@rukunwarga.id', icon: 'fa-user', color: 'text-sky-600', badge: 'bg-slate-100 text-slate-800' },
    ],

    // Data Cache per Modul
    stats: {},
    wargas: [],
    surats: [],
    kas: { kas: [], total_pemasukan: 0, total_pengeluaran: 0, saldo: 0 },
    rondas: [],
    panicAlerts: [],
    bankSampah: { kategoris: [], transaksis: [] },
    umkms: [],
    posyandus: [],
    kegiatans: [],
    rukems: [],
    aspirasis: [],
    asets: [],
    peminjamans: [],
    koperasi: { my_anggota: null, total_simpanan: 0, total_pinjaman: 0, transaksis: [] },
    logs: [],
    pengaturan: {},

    // UI Modals
    modalSurat: false,
    modalCetakSurat: null,
    modalKas: false,
    modalPanic: false,
    modalSetorSampah: false,
    modalUmkm: false,
    modalAspirasi: false,
    modalPinjamAset: false,
    modalTanggapiAspirasi: null,
    toastMessage: '',
    toastType: 'success',

    // Cek Surat Mandiri
    cekSuratNomor: '',
    cekSuratResult: null,
    cekSuratError: '',

    async init() {
        await this.checkBackend();
        await this.loadAllData();
    },

    showToast(message, type = 'success') {
        this.toastMessage = message;
        this.toastType = type;
        setTimeout(() => {
            this.toastMessage = '';
        }, 4000);
    },

    async checkBackend() {
        try {
            const res = await axios.get(`${API_BASE}/stats`);
            this.backendStatus = 'connected';
            this.stats = res.data.stats || {};
            this.pengaturan = res.data.pengaturan || {};
        } catch (err) {
            this.backendStatus = 'offline';
            console.warn('Backend API belum menyala di port 8000. Memakai demo data.');
        }
    },

    async switchRole(roleKey) {
        const found = this.rolesList.find(r => r.key === roleKey);
        if (found) {
            this.currentUser = {
                id: roleKey === 'superadmin' ? 1 : (roleKey === 'rw' ? 2 : (roleKey === 'rt' ? 3 : 8)),
                name: found.name,
                email: found.email,
                role: found.key,
                role_title: found.title,
                rt: '01',
                rw: '05',
            };
            this.showToast(`Berhasil berganti peran ke ${found.title} (${found.name})`);
            try {
                await axios.post(`${API_BASE}/auth/quick-login/${roleKey}`);
            } catch (e) {
                // silent
            }
            this.loadAllData();
        }
    },

    async loadAllData() {
        try {
            const [
                statsRes,
                wargaRes,
                suratRes,
                kasRes,
                rondaRes,
                sampahRes,
                umkmRes,
                posyanduRes,
                kegiatanRes,
                rukemRes,
                aspirasiRes,
                asetRes,
                kopRes,
                logRes
            ] = await Promise.allSettled([
                axios.get(`${API_BASE}/stats`),
                axios.get(`${API_BASE}/warga`),
                axios.get(`${API_BASE}/surat`),
                axios.get(`${API_BASE}/kas`),
                axios.get(`${API_BASE}/ronda`),
                axios.get(`${API_BASE}/bank-sampah`),
                axios.get(`${API_BASE}/umkm`),
                axios.get(`${API_BASE}/posyandu`),
                axios.get(`${API_BASE}/kegiatan`),
                axios.get(`${API_BASE}/rukem`),
                axios.get(`${API_BASE}/aspirasi`),
                axios.get(`${API_BASE}/aset`),
                axios.get(`${API_BASE}/koperasi?user_id=${this.currentUser.id}`),
                axios.get(`${API_BASE}/log-aktivitas`)
            ]);

            if (statsRes.status === 'fulfilled') this.stats = statsRes.value.data.stats;
            if (wargaRes.status === 'fulfilled') this.wargas = wargaRes.value.data.data || wargaRes.value.data;
            if (suratRes.status === 'fulfilled') this.surats = suratRes.value.data;
            if (kasRes.status === 'fulfilled') this.kas = kasRes.value.data;
            if (rondaRes.status === 'fulfilled') {
                this.rondas = rondaRes.value.data.rondas || [];
                this.panicAlerts = rondaRes.value.data.panic_alerts || [];
            }
            if (sampahRes.status === 'fulfilled') this.bankSampah = sampahRes.value.data;
            if (umkmRes.status === 'fulfilled') this.umkms = umkmRes.value.data;
            if (posyanduRes.status === 'fulfilled') this.posyandus = posyanduRes.value.data;
            if (kegiatanRes.status === 'fulfilled') this.kegiatans = kegiatanRes.value.data;
            if (rukemRes.status === 'fulfilled') this.rukems = rukemRes.value.data;
            if (aspirasiRes.status === 'fulfilled') this.aspirasis = aspirasiRes.value.data;
            if (asetRes.status === 'fulfilled') {
                this.asets = asetRes.value.data.asets || [];
                this.peminjamans = asetRes.value.data.peminjamans || [];
            }
            if (kopRes.status === 'fulfilled') this.koperasi = kopRes.value.data;
            if (logRes.status === 'fulfilled') this.logs = logRes.value.data.data || logRes.value.data;
        } catch (e) {
            console.error('Error loading data:', e);
        }
    },

    // Actions API
    async submitSurat(event) {
        const formData = new FormData(event.target);
        const payload = {
            user_id: this.currentUser.id,
            jenis_surat: formData.get('jenis_surat'),
            keperluan: formData.get('keperluan'),
            keterangan_tambahan: formData.get('keterangan_tambahan'),
        };

        try {
            await axios.post(`${API_BASE}/surat`, payload);
            this.showToast('Permohonan surat berhasil diajukan ke RT!');
            this.modalSurat = false;
            await this.loadAllData();
        } catch (err) {
            this.showToast('Gagal mengajukan surat', 'error');
        }
    },

    async approveRt(id) {
        try {
            await axios.post(`${API_BASE}/surat/${id}/approve-rt`, { approver_name: this.currentUser.name });
            this.showToast('Surat berhasil diverifikasi oleh RT!');
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal verifikasi surat', 'error');
        }
    },

    async approveRw(id) {
        try {
            await axios.post(`${API_BASE}/surat/${id}/approve-rw`, { approver_name: this.currentUser.name });
            this.showToast('Surat resmi berhasil disetujui & nomor surat diterbitkan!');
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menyetujui surat', 'error');
        }
    },

    async triggerPanicSOS(event) {
        const formData = new FormData(event.target);
        const payload = {
            lokasi_kejadian: formData.get('lokasi_kejadian'),
            jenis_darurat: formData.get('jenis_darurat'),
            keterangan: formData.get('keterangan'),
            pelapor: this.currentUser.name,
        };

        try {
            await axios.post(`${API_BASE}/ronda/panic-button`, payload);
            this.showToast('🚨 ALARM DARURAT BERHASIL DISIARKAN!', 'error');
            this.modalPanic = false;
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menyiarkan alarm', 'error');
        }
    },

    async checkSuratMandiri() {
        this.cekSuratResult = null;
        this.cekSuratError = '';
        if (!this.cekSuratNomor) {
            this.cekSuratError = 'Masukkan nomor surat atau ID surat.';
            return;
        }

        try {
            const res = await axios.get(`${API_BASE}/cek-surat?nomor=${encodeURIComponent(this.cekSuratNomor)}`);
            this.cekSuratResult = res.data.surat;
        } catch (err) {
            this.cekSuratError = 'Surat tidak ditemukan. Pastikan nomor surat sesuai yang tertera pada lembar dokumen.';
        }
    },

    async submitKas(event) {
        const formData = new FormData(event.target);
        const payload = {
            tingkat: formData.get('tingkat'),
            kategori: formData.get('kategori'),
            jenis: formData.get('jenis'),
            jumlah: Number(formData.get('jumlah')),
            tanggal: formData.get('tanggal'),
            keterangan: formData.get('keterangan'),
        };

        try {
            await axios.post(`${API_BASE}/kas`, payload);
            this.showToast('Transaksi kas berhasil dicatat!');
            this.modalKas = false;
            await this.loadAllData();
        } catch (err) {
            this.showToast('Gagal mencatat kas', 'error');
        }
    },

    async submitAspirasi(event) {
        const formData = new FormData(event.target);
        const payload = {
            user_id: this.currentUser.id,
            kategori: formData.get('kategori'),
            judul: formData.get('judul'),
            isi: formData.get('isi'),
            lokasi: formData.get('lokasi'),
            is_anonim: formData.get('is_anonim') ? 1 : 0,
        };

        try {
            await axios.post(`${API_BASE}/aspirasi`, payload);
            this.showToast('Aspirasi Anda berhasil dikirim ke pengurus!');
            this.modalAspirasi = false;
            await this.loadAllData();
        } catch (err) {
            this.showToast('Gagal mengirim aspirasi', 'error');
        }
    },

    async submitTanggapanAspirasi(event) {
        const formData = new FormData(event.target);
        const id = this.modalTanggapiAspirasi.id;
        const payload = {
            tanggapan: formData.get('tanggapan'),
            status: formData.get('status'),
            petugas: this.currentUser.name + ' (' + this.currentUser.role_title + ')',
        };

        try {
            await axios.post(`${API_BASE}/aspirasi/${id}/tanggapi`, payload);
            this.showToast('Tanggapan resmi pengurus berhasil disimpan!');
            this.modalTanggapiAspirasi = null;
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menyimpan tanggapan', 'error');
        }
    },

    async submitPinjamAset(event) {
        const formData = new FormData(event.target);
        const payload = {
            aset_id: formData.get('aset_id'),
            jumlah_pinjam: Number(formData.get('jumlah_pinjam')),
            tanggal_pinjam: formData.get('tanggal_pinjam'),
            tanggal_kembali_rencana: formData.get('tanggal_kembali_rencana'),
            keperluan: formData.get('keperluan'),
            user_id: this.currentUser.id,
        };

        try {
            await axios.post(`${API_BASE}/aset/pinjam`, payload);
            this.showToast('Permohonan pinjam fasilitas berhasil diajukan!');
            this.modalPinjamAset = false;
            await this.loadAllData();
        } catch (err) {
            this.showToast('Gagal meminjam aset', 'error');
        }
    },

    formatRupiah(num) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num || 0);
    },

    formatDate(dateStr) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }
});

Alpine.start();
