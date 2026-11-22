# Product Requirements Document (PRD)
## Evidence Command Center (ECC)

**Institusi:** Politeknik Keselamatan Transportasi Jalan (PKTJ Tegal)
**Versi Platform:** ECC 1.6 (Kinerja, LED & Pemetaan SDM) + ECC 2.0 (Ketarunaan, Karier & Alumni — Mode Operasional Tahap Awal RSTJ)
**Status:** Operasional Bertahap & Pengembangan Lanjutan
**Stack:** CodeIgniter 4 (PHP 8.1+), MySQL (56 tabel), Bootstrap 5.3, Chart.js, Flatpickr, SweetAlert2, jQuery
**Terakhir Diperbarui:** 17 November 2026

> Untuk rincian kriteria penerimaan fungsional level-butir (`GOV`, `TAR`, `RJK`, `PRE`, `PEL`, `BEA`, `LOW`, `ALM`, `DSH`, dan `UAT-01..20`) pada subsistem Ketarunaan & Alumni, lihat dokumen pendamping [`ECC 2.0/PRD.md`](file:///c:/laragon/www/SimonikPKTJ/ECC%202.0/PRD.md).

---

### 1. Ringkasan Eksekutif (Executive Summary)
**Evidence Command Center (ECC)** adalah sistem informasi internal terpadu PKTJ Tegal yang menyatukan tata kelola **kinerja dan kompetensi pegawai** bersama tata kelola **data ketarunaan, karier, dan alumni** dalam satu pangkalan data (*single source of truth*).
Sistem memastikan setiap target kerja pegawai, capaian akreditasi program studi, hingga riwayat pendidikan, prestasi, pembinaan, beasiswa, dan penempatan kerja lulusan memiliki bukti terverifikasi, jejak audit lengkap, serta visualisasi dasbor berbasis data sumber nyata.

---

### 2. Tujuan Produk (Product Goals)
- **Sentralisasi Berbasis Bukti (*Evidence-Based*):** Menghilangkan pencatatan terpisah dengan mewajibkan tautan atau berkas bukti tervalidasi pada setiap target kinerja pegawai maupun rekam jejak taruna.
- **Integritas & Akuntabilitas Berjenjang:** Memisahkan peran pelaksana/pengaju dari validator/pejabat penilai secara tegas pada seluruh proses bisnis kepegawaian maupun ketarunaan.
- **Visibilitas Eksekutif Real-Time:** Menyajikan dasbor ringkasan kinerja unit/pegawai serta posisi 6 status pendidikan taruna berdasarkan tanggal acuan (*as-of date*) yang dapat ditelusuri hingga sumbernya.
- **Kesiapan Ekosistem SDM & Lulusan PKTJ:** Menghubungkan profil potensi/kompetensi pegawai dengan pembinaan taruna hingga kesiapan karier, lowongan industri, dan pelacakan lulusan (*tracer study*).

---

### 3. Arsitektur Pengguna, Peran & Isolasi Portal
Sistem menerapkan otorisasi multi-peran melalui tabel `users` dan `user_roles`:
1. **Peran Struktural & Fungsional ECC 1.x (Kinerja, Kepegawaian, LED, Survei SDM):**
   - `admin`, `direktur`, `wadir` (pengawas eksekutif tanpa hak menyetujui/menilai target staf), `kabag_aak`, `kabag_kuk`, `manajemen` (Kanit/Katim/Kaprodi), `user`, `tugas_belajar`, serta peran sekunder `kepegawaian` dan `spm`.
2. **Pemetaan Peran Operasional ECC 2.0 (21 Pegawai Nyata pada 3 Unit Kerja):**
   - **Tim Substansi Bidang Administrasi Ketarunaan & Alumni:** `ketarunaan`, `karir_alumni` (termasuk **PIC Tracer Study**), dan `ketarunaan_validator` (Katim).
   - **Pusat Pengembangan Karakter:** `pusbangkar` (pembinaan disiplin/pelanggaran, prestasi karakter, dan validasi terkait).
   - **Tim Substansi Bidang Kerjasama:** `kerjasama` (pengelola perusahaan mitra, lowongan industri, dan **share survei/informasi ke mitra pengguna lulusan**, terisolasi dari data pelanggaran, beasiswa, dan koreksi kelulusan alumni).
3. **Akun Layanan Mandiri Terisolasi (`taruna` & `alumni`):**
   - Dilindungi oleh `EmployeeOnlyFilter` agar 100% terisolasi dari seluruh rute ECC Kinerja, serta dikecualikan dari kueri pegawai (`User::SELF_SERVICE_ROLES`).
   - Pada subsistem ECC 2.0, `KetarunaanPolicy::canAccessDashboard()` menutup akses Dashboard Ringkasan internal kampus dan mengarahkan akun mandiri langsung ke portal pribadinya.

---

### 4. Modul yang Telah Dibangun & Beroperasi

#### 4.1. Subsistem Kinerja Pegawai, Remunerasi & Akreditasi (ECC 1.0 – 1.6)
- **Dashboard Eksekutif & Pengguna:** Grafik historis kinerja, Top 5 Unit Kerja, Top 5 Pegawai, dan bottom leaderboard berbasis tanggal/periode.
- **Target Kinerja Bulanan & Log Harian:** Dukungan pecahan desimal presisi 4 digit (`DECIMAL(10,4)`), notasi koma Indonesia, tugas tambahan skala `0–150%`, batas kunci akhir bulan, serta alur pengajuan revisi.
- **Penilaian Kinerja & Hierarki Multi-Periode:** Penilaian staf oleh atasan langsung tervalidasi melalui `PenilaianKinerjaPolicy`, tabel penugasan historis `riwayat_atasan` (rotasi/mutasi/pensiun), Smart Lock form profil pegawai, serta cetak SKP tahunan PDF sesuai PermenPAN-RB No. 6 Tahun 2022 dengan digital audit trail yang netral di UI.
- **Kepegawaian, Remunerasi & LED:** Monitoring target dan penilaian lintas unit, ekspor rekap Excel multi-sheet & PDF landscape A4, manajemen hak akun (`/users`), direktori pegawai selektif (`/daftar-pegawai`), master data berjenjang (`Dual-Sync unit_id`), mode pemeliharaan mandiri (`/settings`), notifikasi auto-sync hari libur, serta modul LED akreditasi prodi (SPM).

#### 4.2. Modul Survei Pemetaan Potensi, Kompetensi & Kapasitas SDM
- **Instrumen 10 Bagian (83 Butir):** Halaman pengisian mandiri berbasis tautan bagikan (*Share-Link Only*) tanpa perlu login di depan, melayani ASN (NIP 18 digit dengan autofill & verifikasi kata sandi bila pindah perangkat) maupun Non-ASN (NIK 16 digit dengan *HttpOnly Signed Cookie* & verifikasi anti-*deadlock*).
- **Protected Smart Profile Sync & Rekap Kabag KUK:** Pembaruan profil aman (`pending_profile_sync`), pemisahan KPI keterisian ASN vs partisipasi Non-ASN, delegasi staf pemeriksa, moderasi arsip data tidak valid, dan tombol saklar buka/tutup periode survei.

#### 4.3. Subsistem ECC 2.0 — Ketarunaan, Karier & Alumni
Sistem kini beroperasi pada mode **`produksi` (*Data Operasional*)** dengan cakupan awal **427 taruna aktif Prodi RSTJ Tingkat 1–4** (`18` kelas riil `ref_kelas` dari ekspor `sim.pktj.ac.id` beserta `427` akun portal `taruna`), sementara Prodi `TRO` dan `TO` tetap aktif di aplikasi menunggu tahap impor data berikutnya:
- **Dashboard Ringkasan Ketarunaan (`/ketarunaan/dashboard`):** Filter Prodi (`RSTJ`, `TRO`, `TO`), Angkatan, dan Tanggal Acuan (*as-of date*) secara dinamis via AJAX; metrik 6 status pendidikan (`Aktif`, `Skorsing`, `Cuti Akademik`, `Mengundurkan Diri`, `Drop Out`, `Lulus`), 4 metrik modul operasional, matriks sebaran prodi, indikator kualitas data, antrean tindak lanjut, dan akses cepat (eksklusif untuk staf & pimpinan).
- **Database Taruna & Portal Profil Mandiri (`/ketarunaan/taruna`):** Identitas utama, data pendidikan, kontak & wali (NIK/kontak sensitif dibatasi sesuai peran), riwayat pendidikan, riwayat status berbukti SK, pengajuan perubahan status tanpa nilai dummy, serta impor multi-format (`.xls`/`.xlsx` ekspor 39 kolom SIM PKTJ maupun template CSV) dengan pratinjau validasi dan pembuatan `ref_kelas` otomatis mengikuti kelas riil masing-masing prodi. Taruna login dapat memperbarui biodata kontak aman & mengganti kata sandi di `/ketarunaan/keamanan-akun`.
- **Rekam Jejak Taruna & Capaian Mandiri (`/ketarunaan/rekam-jejak`):** Kronologi terpadu lintas modul sumber plus pengajuan kegiatan/capaian tambahan oleh staf maupun langsung oleh taruna (`draf`/`diajukan`/`perlu_perbaikan`/`tervalidasi`) dengan kewajiban bukti saat pengajuan.
- **Prestasi Taruna (`/ketarunaan/prestasi`):** Pencatatan prestasi individu maupun tim berbasis peristiwa (`rekap dua satuan`), lampiran sertifikat/foto dokumentasi, serta dukungan pengajuan mandiri taruna (otomatis tertaut sebagai peserta, anti-hapus diri sendiri, dan terkunci dari suntingan selama menunggu/sudah validasi).
- **Pembinaan Karakter & Pelanggaran (`/ketarunaan/pelanggaran`):** Pemisahan tegas antara `Dugaan`, `Terbukti`, `Tidak Terbukti`, dan `Dibatalkan` serta penyelesaian (`Proses`, `Selesai`, `Banding`), tindak lanjut pembinaan Pusbangkar, dan perlindungan dokumen/berita acara terbatas.
- **Beasiswa 4 Jenis (`/ketarunaan/beasiswa`):** Pengelolaan program untuk `Apresiasi` (seleksi 5 IP semester tertinggi per kelas beserta penyimpanan versi pemeringkatan), `Prestasi`, `Daerah 3TP`, dan `Tidak Mampu`; mendukung pengajuan klaim mandiri oleh `taruna`, pemeriksaan dokumen ekonomi berizin khusus, serta pemisahan status verifikasi dan penetapan SK penerima.
- **Lowongan Pekerjaan & Perusahaan Mitra (`/ketarunaan/lowongan`):** Direktori perusahaan mitra terverifikasi, sasaran program studi, kontrol publikasi setelah validasi, penutupan manual beralasan, serta pemeriksaan tenggat otomatis terhadap waktu acuan.
- **Alumni — Profil Dasar (`/ketarunaan/alumni`):** Pembentukan profil alumni hanya dari status `Lulus` tervalidasi (menolak `Mengundurkan Diri` & `Drop Out`), antrean kelulusan tanpa batas potongan daftar, serta koreksi SK kelulusan non-destruktif (`ralat_sk` / `batal_lulus`).
- **Tata Kelola Lintas Modul:** Pusat antrean **Validasi Data** (`/ketarunaan/validasi`), **Laporan & Ekspor** resmi konsisten definisi satuan (`/ketarunaan/laporan`), dan **Pengaturan** data rujukan non-destruktif, matriks kewenangan, serta penanda mode data (`/ketarunaan/pengaturan`).

---

### 5. Pedoman Desain, Mikro-Kopi & Keamanan Sistem
- **Baseline UI/UX:** Bento Grid ECC 1.5, palet warna resmi, tipografi Inter + `tabular-nums`, ritme **8-Point Grid**, responsif desktop-mobile, Bootstrap Icons selektif, serta `SweetAlert2` dengan fallback dialog native.
- **Kebijakan Bahasa & Terminologi:** Menggunakan **Evidence Command Center (ECC)**, istilah baku **"staf"**, pembakuan *"Kata Sandi"*, *"Alamat Email"*, *"Keluar"*, *"Simpan Profil"*, serta teks non-teknis yang singkat, padat, dan jelas.
- **Keamanan & Integritas:** `setAutoRoute(false)` eksplisit di `Routes.php`, CSRF token ganda pada form & AJAX, proteksi IDOR & kepemilikan data, sanitasi URL (`http://`/`https://`), pengamanan upload (`getRandomName()`, `.htaccess` anti-eksekusi skrip), transaksi atomik, dan `audit_logs`.

---

### 6. Riwayat Pencapaian & Rencana Masa Depan (Project Roadmap)

#### A. Pencapaian Selesai (ECC 1.0 – ECC 2.0 Tahap Awal)
- [x] **ECC 1.0 – 1.5 (Agustus – Oktober 2026):** Multi-Role Pivot (`user_roles`), Target & Log Kegiatan Harian desimal 4-digit, Penilaian Kinerja `0–150%`, Kepegawaian & Ekspor Excel/PDF, Audit Trail JSON, LED Prodi, Web-Based Maintenance Mode, Normalisasi Unit (`Dual-Sync`), Upload Hardening `.htaccess`, serta Audit 8-Pilar & 8-Point Grid.
- [x] **ECC 1.6 (13 Oktober 2026):** Modul Riwayat Penugasan & Rotasi Multi-Periode Atasan-Staf (`riwayat_atasan`), Smart Lock Profil, Penyelarasan SKP PDF Multi-Periode, dan Digital Audit Trail Persetujuan/Penilaian netral auditor.
- [x] **Survei Pemetaan SDM (30 Oktober 2026):** Instrumen 10 Bagian (83 butir) berbasis tautan bagikan untuk ASN & Non-ASN beserta rekap & *Protected Smart Profile Sync*.
- [x] **ECC 2.0 Fondasi & 8 Modul Inti (4 – 14 November 2026):** Migrasi lengkap (total 56 tabel DB), shell ECC 2.0 selaras ECC 1.5, Dashboard as-of date, Database Taruna, Rekam Jejak, Prestasi, Pelanggaran, Beasiswa 4 jenis, Lowongan & Mitra, Alumni profil dasar, serta Tata Kelola (Validasi, Laporan, Pengaturan).
- [x] **ECC 2.0 Operasional Tahap Awal & Portal Taruna (15 – 16 November 2026):** Impor penuh **427 taruna aktif Prodi RSTJ Tingkat 1–4** beserta 427 akun login `taruna`, isolasi penuh akun mandiri dari ECC Kinerja (`EmployeeOnlyFilter`) & Dashboard internal (`DashboardController`), pemetaan **21 pegawai nyata PKTJ** (Unit Ketarunaan & Alumni, Pusbangkar, Kerjasama), pembukaan pengajuan mandiri taruna pada **Prestasi**, **Capaian Rekam Jejak**, dan **Klaim Beasiswa**, serta pembersihan sisa tampilan simulasi pada UI.

#### B. Rencana Pengembangan & Operasional Ke Depan (*Future Roadmap*)
- [ ] **Tahap 1 — Uji Penerimaan Pengguna (*Browser UAT* Lintas Peran RSTJ):** Pengujian alur penuh di peramban oleh staf operasional, validator (`ketarunaan_validator`, `pusbangkar`), tim `kerjasama`, pimpinan, dan perwakilan taruna RSTJ untuk memverifikasi alur pengajuan, pengembalian revisi, validasi, laporan, dan batasan akses sebelum operasional penuh.
- [ ] **Tahap 2 — Impor Data Taruna Prodi `TRO` & `TO`:** Melengkapi populasi taruna aktif Prodi **D-IV TRO** dan **D-III TO** dari ekspor resmi `sim.pktj.ac.id` lengkap dengan pembentukan kelas riil otomatis pada `ref_kelas` dan pembukaan akun portal taruna masing-masing.
- [ ] **Tahap 3 — Operasionalisasi Siklus Transaksi & Kebijakan Institusi:**
  - Mengaktifkan pencatatan **Nilai IP per semester** (`taruna_nilai_ip`) untuk menjalankan seleksi **Beasiswa Apresiasi (5 IP tertinggi per kelas)**, serta membuka periode program beasiswa nyata (`Apresiasi`, `Prestasi`, `Daerah 3TP`, `Tidak Mampu`), pelaporan pembinaan Pusbangkar, dan lowongan mitra oleh Tim Kerjasama.
  - Memfinalkan daftar **Keputusan Terbuka Institusi** (aturan pemecah seri peringkat kelima Apresiasi, acuan resmi wilayah 3TP, persyaratan dokumen ekonomi Tidak Mampu, kelayakan status Cuti/Skorsing terhadap beasiswa, batas jam tenggat lowongan, serta pengesahan SOP penggunaan ECC 2.0).
- [ ] **Tahap 4 — Aktivasi Basis Data Alumni & Modul *Tracer Study* + Survei Pengguna Lulusan:**
  - Pengisian data lulusan resmi (`Lulus` tervalidasi -> `alumni_profil`) dan pembukaan akses portal mandiri `alumni` tanpa mencampur taruna yang mengundurkan diri atau *drop out*.
  - Pembangunan fitur penuh **Tracer Study** bagi lulusan (dikelola **Tim Ketarunaan & Alumni — Unit 6**) dan **Survei Kepuasan Pengguna Lulusan** untuk mitra instansi/industri (didistribusikan bersama **Tim Kerjasama — Unit 4**).
- [ ] **Tahap 5 — Integrasi Sistem & Keandalan Jangka Panjang:**
  - Kajian integrasi otomatis (sinkronisasi data taruna, nilai IP, dan kelulusan dengan sistem akademik `sim.pktj.ac.id` serta opsi SSO institusi).
  - Penambahan analitik tren kohort antar-angkatan, otomatisasi jadwal pencadangan dan pemulihan (*RPO/RTO*), serta dukungan ekspansi fitur pasca-MVP lainnya (kesehatan/semapta bila diperlukan oleh Pusbangkar).
