import './style.css';
import Alpine from 'alpinejs';
import axios from 'axios';

// Konfigurasi Base API URL Laravel Backend
const API_BASE = '/api';

window.appState = () => ({
    // State Navigasi
    // views: dashboard, pemasukan, pengeluaran, semua_transaksi, kategori_kas, tagihan_warga, rekening_kas, warga, surat, ronda, bank_sampah, umkm, posyandu, kegiatan, rukem, aspirasi, aset, koperasi, log_aktivitas, pengaturan, welcome
    currentView: 'dashboard',
    sidebarOpen: false,
    mobileNav: false,
    backendStatus: 'checking', // checking, connected, offline

    // User Session & Role (Default Ketua RW)
    currentUser: {
        id: 2,
        name: 'H. Bambang Sutrisno, S.E.',
        email: 'rw@rukunwarga.id',
        role: 'rw',
        role_title: 'Ketua RW',
        rt: '01',
        rw: '05',
        rfid_uid: null,
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
    kas: { kas: [], total_pemasukan: 0, total_pengeluaran: 0, saldo: 0, kategori_pemasukan: [], kategori_pengeluaran: [], rekenings: [] },
    kategoriKas: [],
    tagihanWarga: { tagihans: [], stats: {}, wargas: [], rekenings: [] },
    rekeningKas: [],
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

    // UI Modals Keuangan
    modalKas: { show: false, mode: 'create', data: null, defaultJenis: 'pemasukan' },
    modalKategori: { show: false, mode: 'create', data: null },
    modalTagihan: { show: false, mode: 'create', data: null },
    modalGenerateTagihan: false,
    modalBayarTagihan: null,
    modalVerifikasiTagihan: null,
    modalRekening: { show: false, mode: 'create', data: null },
    modalQrPreview: null,

    // Filter & Search State Keuangan
    filterKas: { search: '', kategori: '', tingkat: '', rt: '' },
    filterTagihan: { status: '', search: '', tab: 'all' }, // tab: all / my
    filterKategori: { jenis: '' },

    // Modals Umum
    modalSurat: false,
    modalCetakSurat: null,
    modalPanic: false,
    modalSetorSampah: false,
    modalUmkm: false,
    modalAspirasi: false,
    modalPinjamAset: false,
    modalTanggapiAspirasi: null,
    toastMessage: '',
    toastType: 'success',

    // IoT RFID / NFC ESP32 State
    modalRfid: {
        show: false,
        tab: 'esp32', // esp32, manual, simulate
        status: 'idle', // idle, waiting, registered, rejected_duplicate, error
        message: '',
        uidInput: '',
        simulatedUid: '04A1B2C3',
        pollInterval: null,
        countdown: 90,
    },
    rfidLogs: [],

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

    // Hak Akses Role Helpers
    canManageKas() {
        return ['superadmin', 'rw', 'rt', 'bendahara_rw', 'bendahara_rt'].includes(this.currentUser.role);
    },

    canManageFinance() {
        return ['superadmin', 'bendahara_rw', 'bendahara_rt'].includes(this.currentUser.role);
    },

    canVerifyPayment() {
        return ['superadmin', 'rw', 'bendahara_rw', 'bendahara_rt'].includes(this.currentUser.role);
    },

    isWarga() {
        return this.currentUser.role === 'warga';
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
                id: roleKey === 'superadmin' ? 1 : (roleKey === 'rw' ? 2 : (roleKey === 'rt' ? 3 : (roleKey === 'bendahara_rw' ? 5 : (roleKey === 'bendahara_rt' ? 7 : 8)))),
                name: found.name,
                email: found.email,
                role: found.key,
                role_title: found.title,
                rt: '01',
                rw: '05',
            };
            this.showToast(`Berhasil berganti peran ke ${found.title} (${found.name})`);
            try {
                const res = await axios.post(`${API_BASE}/auth/quick-login/${roleKey}`);
                if (res.data?.user) {
                    this.currentUser = {
                        ...this.currentUser,
                        ...res.data.user
                    };
                }
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
                kategoriRes,
                tagihanRes,
                rekeningRes,
                rondaRes,
                sampahRes,
                umkmRes,
                posyanduRes,
                kegiatanRes,
                rukemRes,
                aspirasiRes,
                asetRes,
                kopRes,
                logRes,
                rfidRes
            ] = await Promise.allSettled([
                axios.get(`${API_BASE}/stats`),
                axios.get(`${API_BASE}/warga`),
                axios.get(`${API_BASE}/surat`),
                axios.get(`${API_BASE}/kas`),
                axios.get(`${API_BASE}/kategori-kas`),
                axios.get(`${API_BASE}/tagihan-warga`),
                axios.get(`${API_BASE}/rekening-kas`),
                axios.get(`${API_BASE}/ronda`),
                axios.get(`${API_BASE}/bank-sampah`),
                axios.get(`${API_BASE}/umkm`),
                axios.get(`${API_BASE}/posyandu`),
                axios.get(`${API_BASE}/kegiatan`),
                axios.get(`${API_BASE}/rukem`),
                axios.get(`${API_BASE}/aspirasi`),
                axios.get(`${API_BASE}/aset`),
                axios.get(`${API_BASE}/koperasi?user_id=${this.currentUser.id}`),
                axios.get(`${API_BASE}/log-aktivitas`),
                axios.get(`${API_BASE}/rfid/logs`)
            ]);

            if (statsRes.status === 'fulfilled') this.stats = statsRes.value.data.stats || {};
            if (wargaRes.status === 'fulfilled') this.wargas = wargaRes.value.data.data || wargaRes.value.data || [];
            if (suratRes.status === 'fulfilled') this.surats = suratRes.value.data || [];
            if (kasRes.status === 'fulfilled') this.kas = kasRes.value.data || { kas: [], total_pemasukan: 0, total_pengeluaran: 0, saldo: 0 };
            if (kategoriRes.status === 'fulfilled') this.kategoriKas = kategoriRes.value.data || [];
            if (tagihanRes.status === 'fulfilled') this.tagihanWarga = tagihanRes.value.data || { tagihans: [], stats: {}, wargas: [], rekenings: [] };
            if (rekeningRes.status === 'fulfilled') this.rekeningKas = rekeningRes.value.data || [];
            if (rondaRes.status === 'fulfilled') {
                this.rondas = rondaRes.value.data.rondas || [];
                this.panicAlerts = rondaRes.value.data.panic_alerts || [];
            }
            if (sampahRes.status === 'fulfilled') this.bankSampah = sampahRes.value.data || { transaksis: [] };
            if (umkmRes.status === 'fulfilled') this.umkms = umkmRes.value.data || [];
            if (posyanduRes.status === 'fulfilled') this.posyandus = posyanduRes.value.data || [];
            if (kegiatanRes.status === 'fulfilled') this.kegiatans = kegiatanRes.value.data || [];
            if (rukemRes.status === 'fulfilled') this.rukems = rukemRes.value.data || [];
            if (aspirasiRes.status === 'fulfilled') this.aspirasis = aspirasiRes.value.data || [];
            if (asetRes.status === 'fulfilled') {
                this.asets = asetRes.value.data.asets || [];
                this.peminjamans = asetRes.value.data.peminjamans || [];
            }
            if (kopRes.status === 'fulfilled') this.koperasi = kopRes.value.data || {};
            if (logRes.status === 'fulfilled') this.logs = logRes.value.data.data || logRes.value.data || [];
            if (rfidRes && rfidRes.status === 'fulfilled') this.rfidLogs = rfidRes.value.data.logs || [];
        } catch (e) {
            console.error('Error loading data:', e);
        }
    },

    // ==========================================
    // ACTIONS: KAS (PEMASUKAN, PENGELUARAN, SEMUA)
    // ==========================================
    openModalKas(jenis = 'pemasukan', item = null) {
        if (item) {
            this.modalKas = {
                show: true,
                mode: 'edit',
                data: { ...item },
                defaultJenis: item.jenis,
            };
        } else {
            this.modalKas = {
                show: true,
                mode: 'create',
                data: {
                    tingkat: 'RW',
                    rt: this.currentUser.rt || '01',
                    kategori: jenis === 'pemasukan' ? (this.kas.kategori_pemasukan?.[0] || 'Iuran Warga Bulanan') : (this.kas.kategori_pengeluaran?.[0] || 'Operasional Pos Ronda & Keamanan'),
                    jenis: jenis,
                    jumlah: '',
                    tanggal: new Date().toISOString().split('T')[0],
                    pihak_terkait: jenis === 'pemasukan' ? 'Warga RT 01' : 'Penyedia Jasa / Toko',
                    metode: 'Tunai',
                    keterangan: '',
                    bukti_transaksi: '',
                },
                defaultJenis: jenis,
            };
        }
    },

    async submitKas(event) {
        const formData = new FormData(event.target);
        const isEdit = this.modalKas.mode === 'edit' && this.modalKas.data?.id;
        const payload = {
            tingkat: formData.get('tingkat'),
            rt: formData.get('rt') || '01',
            kategori: formData.get('kategori'),
            jenis: formData.get('jenis'),
            jumlah: Number(formData.get('jumlah')),
            tanggal: formData.get('tanggal'),
            pihak_terkait: formData.get('pihak_terkait'),
            metode: formData.get('metode') || 'Tunai',
            keterangan: formData.get('keterangan'),
            bukti_transaksi: formData.get('bukti_transaksi') || '',
            user_id: this.currentUser.id,
        };

        try {
            if (isEdit) {
                await axios.put(`${API_BASE}/kas/${this.modalKas.data.id}`, payload);
                this.showToast('Transaksi kas berhasil diperbarui!');
            } else {
                await axios.post(`${API_BASE}/kas`, payload);
                this.showToast('Transaksi kas berhasil dicatat!');
            }
            this.modalKas.show = false;
            await this.loadAllData();
        } catch (err) {
            this.showToast('Gagal menyimpan transaksi kas', 'error');
        }
    },

    async deleteKas(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus catatan kas ini?')) return;
        try {
            await axios.delete(`${API_BASE}/kas/${id}`);
            this.showToast('Catatan kas berhasil dihapus!');
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menghapus transaksi kas', 'error');
        }
    },

    // ==========================================
    // ACTIONS: KATEGORI KAS
    // ==========================================
    openModalKategori(item = null) {
        if (item) {
            this.modalKategori = {
                show: true,
                mode: 'edit',
                data: { ...item },
            };
        } else {
            this.modalKategori = {
                show: true,
                mode: 'create',
                data: {
                    nama: '',
                    jenis: 'pemasukan',
                    tingkat: 'RW',
                    deskripsi: '',
                    is_aktif: true,
                },
            };
        }
    },

    async submitKategori(event) {
        const formData = new FormData(event.target);
        const isEdit = this.modalKategori.mode === 'edit' && this.modalKategori.data?.id;
        const payload = {
            nama: formData.get('nama'),
            jenis: formData.get('jenis'),
            tingkat: formData.get('tingkat'),
            deskripsi: formData.get('deskripsi'),
            is_aktif: formData.get('is_aktif') === '1' || formData.get('is_aktif') === 'on' ? 1 : 0,
        };

        try {
            if (isEdit) {
                await axios.put(`${API_BASE}/kategori-kas/${this.modalKategori.data.id}`, payload);
                this.showToast('Kategori kas berhasil diperbarui!');
            } else {
                await axios.post(`${API_BASE}/kategori-kas`, payload);
                this.showToast('Kategori kas berhasil ditambahkan!');
            }
            this.modalKategori.show = false;
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menyimpan kategori kas', 'error');
        }
    },

    async deleteKategori(id) {
        if (!confirm('Hapus kategori ini? Data kas yang sudah menggunakan kategori ini tidak akan terhapus.')) return;
        try {
            await axios.delete(`${API_BASE}/kategori-kas/${id}`);
            this.showToast('Kategori kas berhasil dihapus!');
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menghapus kategori', 'error');
        }
    },

    // ==========================================
    // ACTIONS: TAGIHAN WARGA
    // ==========================================
    openModalTagihan(item = null) {
        if (item) {
            this.modalTagihan = {
                show: true,
                mode: 'edit',
                data: { ...item },
            };
        } else {
            this.modalTagihan = {
                show: true,
                mode: 'create',
                data: {
                    judul: 'Iuran Warga & Kebersihan',
                    tingkat: 'RT',
                    rt: this.currentUser.rt || '01',
                    nama_warga: '',
                    nomor_rumah: '',
                    periode: 'November 2026',
                    nominal: 50000,
                    kategori: 'Iuran Kebersihan Lingkungan',
                    tanggal_jatuh_tempo: new Date(Date.now() + 15 * 86400000).toISOString().split('T')[0],
                    catatan: '',
                },
            };
        }
    },

    async submitTagihan(event) {
        const formData = new FormData(event.target);
        const isEdit = this.modalTagihan.mode === 'edit' && this.modalTagihan.data?.id;
        const payload = {
            judul: formData.get('judul'),
            tingkat: formData.get('tingkat'),
            rt: formData.get('rt') || '01',
            nama_warga: formData.get('nama_warga'),
            nomor_rumah: formData.get('nomor_rumah'),
            periode: formData.get('periode'),
            nominal: Number(formData.get('nominal')),
            kategori: formData.get('kategori'),
            tanggal_jatuh_tempo: formData.get('tanggal_jatuh_tempo'),
            catatan: formData.get('catatan'),
        };

        if (isEdit) {
            payload.status = formData.get('status');
        }

        try {
            if (isEdit) {
                await axios.put(`${API_BASE}/tagihan-warga/${this.modalTagihan.data.id}`, payload);
                this.showToast('Tagihan berhasil diperbarui!');
            } else {
                await axios.post(`${API_BASE}/tagihan-warga`, payload);
                this.showToast('Tagihan warga berhasil diterbitkan!');
            }
            this.modalTagihan.show = false;
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menyimpan tagihan', 'error');
        }
    },

    async submitGenerateTagihan(event) {
        const formData = new FormData(event.target);
        const payload = {
            judul: formData.get('judul'),
            tingkat: formData.get('tingkat'),
            rt: formData.get('rt'),
            periode: formData.get('periode'),
            nominal: Number(formData.get('nominal')),
            kategori: formData.get('kategori'),
            tanggal_jatuh_tempo: formData.get('tanggal_jatuh_tempo'),
        };

        try {
            const res = await axios.post(`${API_BASE}/tagihan-warga/generate`, payload);
            this.showToast(res.data.message || 'Tagihan massal berhasil di-generate!');
            this.modalGenerateTagihan = false;
            await this.loadAllData();
        } catch (e) {
            this.showToast(e.response?.data?.message || 'Gagal generate tagihan massal', 'error');
        }
    },

    openModalBayar(tagihan) {
        this.modalBayarTagihan = {
            ...tagihan,
            metode_pembayaran: 'QRIS Kas RW 05',
            rekening_id: this.rekeningKas[0]?.id || '',
            tanggal_bayar: new Date().toISOString().split('T')[0],
            bukti_bayar: 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&auto=format&fit=crop&q=60',
            catatan: 'Pembayaran tagihan via transfer.',
        };
    },

    async submitBayarTagihan(event) {
        const formData = new FormData(event.target);
        const id = this.modalBayarTagihan.id;
        const payload = {
            metode_pembayaran: formData.get('metode_pembayaran'),
            rekening_id: formData.get('rekening_id') || null,
            tanggal_bayar: formData.get('tanggal_bayar'),
            bukti_bayar: formData.get('bukti_bayar'),
            catatan: formData.get('catatan'),
        };

        try {
            await axios.post(`${API_BASE}/tagihan-warga/${id}/bayar`, payload);
            this.showToast('Konfirmasi pembayaran terkirim! Menunggu verifikasi bendahara.');
            this.modalBayarTagihan = null;
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal mengirim konfirmasi bayar', 'error');
        }
    },

    openModalVerifikasi(tagihan) {
        this.modalVerifikasiTagihan = { ...tagihan, catatan_verif: 'Bukti transfer valid dan sesuai nominal.' };
    },

    async submitVerifikasiTagihan(id, status, catatan = '') {
        try {
            const payload = {
                status: status,
                petugas: `${this.currentUser.name} (${this.currentUser.role_title})`,
                catatan: catatan || (status === 'lunas' ? 'Diverifikasi Lunas oleh bendahara.' : 'Bukti pembayaran ditolak.'),
            };
            const res = await axios.post(`${API_BASE}/tagihan-warga/${id}/verifikasi`, payload);
            this.showToast(res.data.message || 'Status tagihan berhasil diverifikasi!');
            this.modalVerifikasiTagihan = null;
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal memproses verifikasi tagihan', 'error');
        }
    },

    async deleteTagihan(id) {
        if (!confirm('Hapus tagihan ini?')) return;
        try {
            await axios.delete(`${API_BASE}/tagihan-warga/${id}`);
            this.showToast('Tagihan berhasil dihapus!');
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menghapus tagihan', 'error');
        }
    },

    // ==========================================
    // ACTIONS: REKENING & QRIS KAS
    // ==========================================
    openModalRekening(item = null) {
        if (item) {
            this.modalRekening = {
                show: true,
                mode: 'edit',
                data: { ...item },
            };
        } else {
            this.modalRekening = {
                show: true,
                mode: 'create',
                data: {
                    tingkat: 'RW',
                    rt: this.currentUser.rt || '01',
                    tipe: 'bank',
                    nama_bank: '',
                    nomor_rekening: '',
                    atas_nama: 'KAS RW 05 SUKAMAJU',
                    qris_image: '',
                    keterangan: '',
                    is_aktif: true,
                },
            };
        }
    },

    async submitRekening(event) {
        const formData = new FormData(event.target);
        const isEdit = this.modalRekening.mode === 'edit' && this.modalRekening.data?.id;
        const payload = {
            tingkat: formData.get('tingkat'),
            rt: formData.get('rt') || '01',
            tipe: formData.get('tipe'),
            nama_bank: formData.get('nama_bank'),
            nomor_rekening: formData.get('nomor_rekening'),
            atas_nama: formData.get('atas_nama'),
            qris_image: formData.get('qris_image'),
            keterangan: formData.get('keterangan'),
            is_aktif: formData.get('is_aktif') === '1' || formData.get('is_aktif') === 'on' ? 1 : 0,
        };

        try {
            if (isEdit) {
                await axios.put(`${API_BASE}/rekening-kas/${this.modalRekening.data.id}`, payload);
                this.showToast('Rekening / QRIS berhasil diperbarui!');
            } else {
                await axios.post(`${API_BASE}/rekening-kas`, payload);
                this.showToast('Rekening / QRIS berhasil ditambahkan!');
            }
            this.modalRekening.show = false;
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menyimpan rekening', 'error');
        }
    },

    async deleteRekening(id) {
        if (!confirm('Hapus rekening / QRIS ini?')) return;
        try {
            await axios.delete(`${API_BASE}/rekening-kas/${id}`);
            this.showToast('Rekening / QRIS berhasil dihapus!');
            await this.loadAllData();
        } catch (e) {
            this.showToast('Gagal menghapus rekening', 'error');
        }
    },

    // ==========================================
    // OTHER APP ACTIONS
    // ==========================================
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

    // ==========================================
    // RFID / NFC IoT & ESP32 Management
    // ==========================================
    openRfidModal(targetUserId = null, targetUserName = null) {
        this.modalRfid.show = true;
        this.modalRfid.tab = 'esp32';
        this.modalRfid.status = 'idle';
        this.modalRfid.message = '';
        this.modalRfid.uidInput = '';
        this.modalRfid.countdown = 90;
        this.modalRfid.targetUserId = targetUserId || this.currentUser.id;
        this.modalRfid.targetUserName = targetUserName || this.currentUser.name;
        this.startRfidSession();
    },

    async startRfidSession() {
        this.stopRfidPolling();
        this.modalRfid.status = 'waiting';
        this.modalRfid.message = 'Menunggu kartu e-KTP / RFID ditempelkan ke ESP32...';
        this.modalRfid.countdown = 90;

        try {
            await axios.post(`${API_BASE}/rfid/register-session`, {
                user_id: this.modalRfid.targetUserId || this.currentUser.id
            });

            // Timer hitung mundur
            this.modalRfid.countdownTimer = setInterval(() => {
                if (this.modalRfid.countdown > 0) {
                    this.modalRfid.countdown--;
                } else {
                    this.modalRfid.status = 'error';
                    this.modalRfid.message = 'Waktu sesi habis (timeout). Silakan klik Ulangi Scan.';
                    this.stopRfidPolling();
                }
            }, 1000);

            // Polling status dari ESP32 setiap 1.5 detik
            this.modalRfid.pollInterval = setInterval(async () => {
                try {
                    const res = await axios.get(`${API_BASE}/rfid/status-session`);
                    const data = res.data;

                    if (data.status === 'registered') {
                        this.modalRfid.status = 'registered';
                        this.modalRfid.message = `Kartu e-KTP (UID: ${data.rfid_uid}) BERHASIL terdaftar untuk ${data.user_name || this.modalRfid.targetUserName}!`;
                        if (this.modalRfid.targetUserId === this.currentUser.id) {
                            this.currentUser.rfid_uid = data.rfid_uid;
                        }
                        this.showToast(this.modalRfid.message, 'success');
                        this.stopRfidPolling();
                        await this.loadAllData();
                    } else if (data.status === 'rejected_duplicate') {
                        this.modalRfid.status = 'rejected_duplicate';
                        this.modalRfid.message = data.error_message || 'Kartu sudah terdaftar pada akun lain dan tidak bisa digunakan!';
                        this.showToast(this.modalRfid.message, 'error');
                        this.stopRfidPolling();
                        await this.loadAllData();
                    } else if (data.status === 'error') {
                        this.modalRfid.status = 'error';
                        this.modalRfid.message = data.error_message || 'Terjadi kesalahan saat membaca kartu.';
                        this.stopRfidPolling();
                    }
                } catch (err) {
                    // silent polling error
                }
            }, 1500);

        } catch (e) {
            this.modalRfid.status = 'error';
            this.modalRfid.message = e.response?.data?.message || 'Gagal memulai sesi pendaftaran RFID.';
        }
    },

    stopRfidPolling() {
        if (this.modalRfid.pollInterval) {
            clearInterval(this.modalRfid.pollInterval);
            this.modalRfid.pollInterval = null;
        }
        if (this.modalRfid.countdownTimer) {
            clearInterval(this.modalRfid.countdownTimer);
            this.modalRfid.countdownTimer = null;
        }
    },

    async closeRfidModal() {
        this.stopRfidPolling();
        try {
            await axios.post(`${API_BASE}/rfid/cancel-session`);
        } catch (e) {}
        this.modalRfid.show = false;
    },

    async submitManualRfid() {
        if (!this.modalRfid.uidInput || this.modalRfid.uidInput.trim().length < 4) {
            this.showToast('Masukkan UID kartu minimal 4 karakter!', 'error');
            return;
        }

        try {
            const res = await axios.post(`${API_BASE}/rfid/manual-bind`, {
                user_id: this.modalRfid.targetUserId || this.currentUser.id,
                rfid_uid: this.modalRfid.uidInput.trim(),
                device_id: 'WEB-MANUAL'
            });

            this.modalRfid.status = 'registered';
            this.modalRfid.message = res.data.message;
            if (this.modalRfid.targetUserId === this.currentUser.id) {
                this.currentUser.rfid_uid = res.data.rfid_uid;
            }
            this.showToast(res.data.message, 'success');
            await this.loadAllData();
            setTimeout(() => {
                this.closeRfidModal();
            }, 1500);
        } catch (err) {
            const msg = err.response?.data?.message || 'Gagal mendaftarkan kartu.';
            this.modalRfid.status = 'rejected_duplicate';
            this.modalRfid.message = msg;
            this.showToast(msg, 'error');
        }
    },

    async unbindRfid(userId = null) {
        if (!confirm('Apakah Anda yakin ingin melepas pendaftaran kartu e-KTP / RFID ini?')) return;
        const targetId = userId || this.currentUser.id;
        try {
            const res = await axios.post(`${API_BASE}/rfid/unbind`, { user_id: targetId });
            if (targetId === this.currentUser.id) {
                this.currentUser.rfid_uid = null;
            }
            this.showToast(res.data.message || 'Kartu berhasil dilepas.', 'success');
            await this.loadAllData();
        } catch (err) {
            this.showToast('Gagal melepas kartu.', 'error');
        }
    },

    async simulateEspTap(uid = null) {
        const tapUid = uid || this.modalRfid.simulatedUid || '04A1B2C3';
        try {
            const res = await axios.post(`${API_BASE}/iot/rfid/scan`, {
                uid: tapUid,
                device_id: 'ESP32-DEV-SIMULATOR'
            });
            this.showToast(`[Simulasi ESP32] ${res.data.message}`, 'success');
            await this.loadAllData();
        } catch (err) {
            const msg = err.response?.data?.message || 'Simulasi scan ditolak';
            this.showToast(`[Simulasi ESP32] ${msg}`, 'error');
            await this.loadAllData();
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
