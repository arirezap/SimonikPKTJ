# 📋 STANDAR & INSTRUMEN AUDIT KELAYAKAN PRODUKSI (PRODUCTION-READY AUDIT)
## Evidence Command Center (ECC) — PKTJ Tegal
**Dokumen Resmi Penjaminan Mutu, Keamanan & Kesiapan Rilis Produksi**  
**Versi:** 2.0.0 (Enterprise Production Release)  
**Lingkungan Sasaran:** Localhost (Laragon) & Server Produksi (cPanel / Apache / HTTPS)

---

## 🎯 8 Pilar Standar Kualitas Produksi (Production-Ready Framework)

Setiap modul, endpoint, dan konfigurasi sistem pada **Evidence Command Center (ECC)** wajib diaudit dan memenuhi kriteria kelulusan pada 8 pilar berikut agar sistem dinyatakan **100% Siap Rilis Produksi (*Production-Ready*)**:

| No | Pilar Kualitas | Domain & Fokus | Kriteria Kelulusan Produksi (*Production Acceptance Criteria*) |
| :---: | :--- | :--- | :--- |
| **1** | **Code Integrity & Sintaks** | MVC, CI4 & Testing | Kepatuhan MVC CodeIgniter 4, *strict typing*, **0 syntax error** (`php -l`), ketiadaan kode mati (*dead code*), serta ketersediaan *automated test suite* (PHPUnit) untuk formula bisnis kritis. |
| **2** | **Logika Bisnis & Konkurensi** | Workflow & Data Guards | Akurasi formula kalkulasi agregat kinerja, hierarki persetujuan berjenjang, auto-approve Direktur, dan proteksi kondisi lomba (*concurrency / optimistic locking*). |
| **3** | **Reusable Code & Ketahanan Aset** | Modul, DRY & Assets | Pemanfaatan helper terpusat (`role_helper`, `avatar_helper`, `tanggal_helper`, `audit_helper`), konsolidasi CSS global, serta jaminan kemandirian aset vendor (*self-hosted fallback* anti-CDN down). |
| **4** | **Keamanan Komprehensif (Security)** | Hardening & Protection | Proteksi CSRF dinamis, sanitasi XSS (`esc()` & skema aman URL `http(s)://`), proteksi IDOR, otorisasi RBAC multi-role, HTTP Security Headers (`secureheaders`), kuki `$secure` HTTPS, dan proteksi berkas web server (`.htaccess`). |
| **5** | **Efisiensi & Ketahanan Beban** | Performance & Concurrency | Batch query berkecepatan tinggi $O(N)$ in-memory (0 masalah N+1), indeks basis data lengkap, manajemen sesi anti-lock (*Database/Redis session driver*), dan optimalisasi batas memori PHP. |
| **6** | **Mitigasi Bug & Observabilitas** | Exception & Monitoring | *Zero-division defense*, penanganan `null/empty` terpadu, failsafe sesi user, *graceful fallback* dialog SweetAlert2, serta mekanisme pencatatan & notifikasi error kritis (*error alerting*). |
| **7** | **Ergonomi Sentuh & 8-Point Grid** | UI/UX & Aksesibilitas | Desain responsif (<768px & <576px), kepatuhan mutlak skala **8-Point Grid** (`4px` s.d. `80px`), *touch target* minimal 44px, dual-view (tabel desktop & kartu mobile), angka tabular, dan atribut `aria-label`. |
| **8** | **Standarisasi Bahasa & Disaster Recovery** | Branding & Operasional | Kepatuhan istilah baku **"staf"**, identitas **"Evidence Command Center (ECC)"**, kalimat simpel & tidak kepanjangan, otomatisasi pencadangan data (*database backup*), dan retensi data (*housekeeping*). |

---

## 🔄 Prosedur Wajib: Rencana Implementasi (*Implementation Plan*) Sebelum Eksekusi

Setiap kali audit kode dilakukan dan menemukan celah, kekurangan, atau kebutuhan peningkatan pada modul sistem:
1. **Perumusan Hasil Audit**: Sajikan temuan audit secara transparan, terstruktur, dan berbasis bukti kode aktif.
2. **Penyusunan Rencana Implementasi (*Implementation Plan*)**: Sebelum melakukan modifikasi kode (*file edits*) atau mengeksekusi perintah perbaikan, **WAJIB** menyusun dokumen rencana implementasi yang memuat:
   - **Deskripsi Masalah & Ruang Lingkup**: Akar permasalahan yang ditemukan dan batasan solusi yang diajukan.
   - **Daftar Berkas yang Terpengaruh**: Klasifikasi berkas target secara spesifik (`[MODIFY]`, `[NEW]`, `[DELETE]`).
   - **Rincian Teknis & Perubahan Kode**: Rancangan perubahan logika, validasi keamanan, kueri database, atau perbaikan antarmuka.
   - **Mitigasi Risiko & Pencegahan Regresi**: Analisis potensi efek samping ke modul lain, kompatibilitas PHP 8.1+, dan lingkungan server produksi cPanel.
   - **Rencana Pengujian Pengguna**: Skenario pengujian yang akan dilakukan langsung oleh pengguna (tanpa *automated testing* mandiri tanpa izin).
3. **Persetujuan Pengguna (*User Approval*)**: Eksekusi pengkodean hanya dimulai setelah pengguna meninjau dan memberikan persetujuan (*review & approval*).
4. **Eksekusi & Laporan Ringkasan (*Walkthrough*)**: Laksanakan perbaikan secara presisi sesuai rencana yang telah disetujui, lalu sajikan ringkasan berkas yang diperbarui.

---

## 🗺️ Lembar Kerja Audit Modul Sistem (15 Modul Fungsional)

Berikut adalah status audit dan verifikasi kelayakan produksi pada seluruh 15 modul aplikasi:

### 📌 1. Modul Autentikasi & Sesi (Auth & Login — `/login`)
- [x] **Controller**: `app/Controllers/Auth.php` | **View**: `app/Views/login.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Brute-force Throttling**: Layanan `Throttler` CI4 membatasi maksimal 10 percobaan login per menit per IP.
  - [x] **Pencegahan User Enumeration**: Pesan kesalahan login seragam (*"Nama pengguna atau kata sandi yang Anda masukkan salah."*).
  - [x] **Auto Password Upgrade**: Upgrade otomatis password hash warisan (MD5/Plain) ke algoritma `BCRYPT` saat login sukses.
  - [x] **Hardened Logout (CSRF POST)**: Logout wajib via HTTP POST ber-CSRF (`#logoutPostForm`) dengan pembersihan sesi total dan header `Cache-Control: no-store`.
  - [x] **Audit Trail Login**: Pencatatan audit log terintegrasi untuk `LOGIN`, `FAILED_LOGIN` (lengkap dengan IP & User Agent), dan `LOGOUT`.
  - [x] **Mobile-Friendly**: Formulir terpusat ergonomis, input font 16px (anti auto-zoom iOS), dan tombol touch target $\ge 44\text{px}$.
  - [x] **Visual Stability & Ergonomi (Zero Shake & Zero Floating)**: Kartu login 100% solid dan stabil tanpa animasi 3D tilt mouse, tanpa animasi melayang (*floating bobs*), tanpa *stagger-sliding*, dan tanpa efek getar (*shaking*) saat autentikasi gagal. Umpan balik error disajikan secara tenang, jelas, dan non-intrusif via alert box inline berstandar enterprise.

---

### 📌 2. Modul Dashboard Eksekutif & Personal (Admin & User Dashboard — `/dashboard`)
- [x] **Controller**: `app/Controllers/DashboardController.php`, `app/Controllers/Admin/Dashboard.php`, `app/Controllers/User/Dashboard.php`
- [x] **View**: `app/Views/admin/dashboard.php`, `app/Views/user/dashboard.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Pemisahan Peran Dinamis (Role Routing)**: `DashboardController` secara cerdas mengarahkan pimpinan, struktural & tim kepegawaian (`['admin', 'direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'spm', 'kepegawaian']`) ke Dashboard Eksekutif, dan pegawai reguler/staf pelaksana/tugas belajar non-kepegawaian ke Dashboard Personal.
  - [x] **Isolasi Hierarki Data (Anti-Data Leakage)**:
    - *Direktur, Wadir, Kabag (kabag, kabag_aak, kabag_kuk), Katim Kepegawaian & Role Kepegawaian, Admin:* Memiliki akses visibilitas institusional penuh untuk memantau performa seluruh pegawai di PKTJ (`$canSeeAll = true`).
    - *Manajemen Struktural Khusus/Koordinator:* Dibatasi hanya mengakses data staf binaan dan unit kerja di bawah supervisinya via `$userModel->getAllStaf($user_id, $role)`.
    - *Staf Pelaksana:* Data personal terisolasi; otomatis menampilkan tabel *"Monitoring Kinerja Staf Saya"* jika memiliki bawahan, atau tabel transparan *"Rekan Kerja Satu Unit"* (*Unit Peers*) jika tidak memiliki bawahan.
    - *Pegawai Tugas Belajar:* Tampilan personal yang fokus pada progres tugas dan keselarasan mutu institusi.
  - [x] **Ultra-Fast 2-Query Batch Loading (Trait `KinerjaBatchTrait`)**: Seluruh agregasi target tahunan dan tugas tambahan ditarik dalam 2 kueri SQL terindeks, diproses in-memory $O(1)$, menjamin bebas dari masalah kueri N+1 dan hemat memori PHP.
  - [x] **Integrasi Trait Mutu Akreditasi (`EccDataTrait`)**: Grafik radar/polar pemenuhan standar mutu LED ECC dimuat secara konsisten di seluruh tipe dasbor pengguna.
  - [x] **Zero-Division Defense & Null Safety**: Seluruh formula rata-rata capaian kinerja, unit kerja, dan persentase indikator diproteksi kondisi `$count > 0 ? ($total / $count) : 0` serta fallback `Tanpa Unit`.
  - [x] **Akurasi Ambang Batas Predikat & Leaderboard**:
    - Sebaran predikat mematuhi standar resmi (*Sangat Baik* `>100%`, *Baik* `>90% - 100%`, *Butuh Perbaikan* `>75% - 90%`, *Kurang* `>25% - 75%`, *Sangat Kurang* `<=25%`).
    - Leaderboard *Perlu Perhatian Khusus* (Bottom 5) secara adil memprioritaskan pegawai yang belum melapor/belum membuat target, dan mengecualikan pegawai dengan capaian Baik ($\ge 90\%$).
  - [x] **Proteksi Endpoint API Drilldown Chart (`/dashboard/api-detail-chart`)**: Endpoint AJAX dilindungi pemeriksaan otorisasi peran `hasAnyRole()` dan verifikasi `isAJAX()` (anti-IDOR & tampering).
  - [x] **DOM XSS Sanitization**: Sanitasi output modal drilldown interaktif menggunakan fungsi `escapeHtml()`.
  - [x] **Mobile Responsiveness & Chart.js Adaptation**: Grafik Chart.js responsif terhadap rasio layar HP (<768px), filter toolbar flex-wrap rapi, dan tabel didukung scroll horizontal `.table-responsive`.
  - [x] **Kepatuhan Terminologi Baku**: 100% menggunakan istilah resmi **"staf"** (bebas dari istilah non-baku "bawahan") dan identitas resmi **"Evidence Command Center (ECC)"**.

---

### 📌 3. Modul Pengelolaan Pengguna & Profil Saya (`/users` & `/profile`)
- [x] **Controller**: `app/Controllers/Admin/UserController.php`, `app/Controllers/Profile.php`
- [x] **View**: `app/Views/admin/users.php`, `user_create.php`, `user_edit.php`, `profile.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Pencegahan IDOR & Hijacking**: Proteksi perubahan username via HTTP POST tampering pada profil mandiri.
  - [x] **Proteksi Path Traversal**: Nama file upload dan hapus foto profil dibersihkan menggunakan `basename()`.
  - [x] **Validasi Password Kuat**: Validasi minimal 6 karakter, konfirmasi password, dan enkripsi `PASSWORD_DEFAULT`.
  - [x] **Sinkronisasi Multi-Role**: Sinkronisasi relasi tabel `user_roles` dan cascading delete otomatis saat pengguna dihapus.
  - [x] **Template Ekspor/Impor**: Penamaan file dinas standar `Template_Import_Pengguna_ECC.xlsx`.
  - [x] **Audit Trail**: Audit log lengkap untuk aksi `CREATE`, `UPDATE`, `DELETE`, dan `RESET_KINERJA`.
  - [x] **Proteksi Akses Seragam**: Seluruh fungsi terkunci hanya untuk role `admin` dan `kepegawaian`.
  - [x] **Hapus Aman & Dual-View Mobile**: Aksi hapus terlindungi form POST CSRF dan tabel mendukung kartu sentuh di HP.
  - [x] **Failsafe Sesi & Optimasi Profil**: Fallback ID sesi ganda dan kueri dropdown atasan hemat memori.
  - [x] **Transaksi Database Profil**: Pembaruan data profil dibungkus transaksi database yang aman.

---

### 📌 4. Modul Target Kinerja Bulanan (Hulu Kinerja — `/laporan-harian`)
- [x] **Controller**: `app/Controllers/User/LaporanHarianController.php`
- [x] **View**: `app/Views/user/laporan_harian/index.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Aturan Khusus Direktur**: Akun Direktur otomatis berstatus `disetujui` dan dapat merevisi target mandiri kapan saja.
  - [x] **Alur Persetujuan Bertingkat**: Pegawai non-Direktur wajib melalui verifikasi Atasan Langsung (`menunggu_persetujuan` $\rightarrow$ `disetujui`).
  - [x] **Fleksibilitas Pengeditan Sebelum Disetujui**: Staf bebas mengubah, menambah, atau menghapus target kinerja selagi belum disetujui atasan (`status_approval != 'disetujui'`).
  - [x] **Tombol & Dialog Adaptif**: Tombol aksi bertransformasi dinamis antara *"Ajukan Target"* (draf) dan *"Perbarui & Ajukan Ulang"* (menunggu persetujuan) lengkap dengan konfirmasi SweetAlert2.
  - [x] **Failsafe & Defensive Error Trapping**: Inisialisasi eksplisit `$targetUser` di awal `store()` dan penanganan exception `try...catch` pada notifikasi ke atasan/staf (100% bebas dari PHP 8.1 Undefined Variable / 500 error di cPanel).
  - [x] **Fitur Batal Approve Superadmin**: Fitur darurat `cancelApprove()` untuk mengembalikan target yang salah disetujui ke draf revisi, dibungkus Database Transaction dan audit log `CANCEL_APPROVE_TARGET`.
  - [x] **Sanitasi Koma Desimal**: Input target numerik otomatis disanitasi dari notasi koma Indonesia (`,`) ke titik desimal (`.`).
  - [x] **Pencegahan Double-Submit**: Implementasi PRG Pattern (Post-Redirect-Get) dan tombol submit lock saat proses AJAX berlangsung.
  - [x] **Responsivitas Seluler & Scroll Guard**: Tabel penyusunan target dibungkus kontainer `.table-responsive` dengan padding bento adaptif dan tombol aksi ramah sentuhan ponsel.

---

### 📌 5. Modul Log Kegiatan Harian & Tugas Tambahan (Eksekusi Kinerja — `/log-kegiatan`)
- [x] **Controller**: `app/Controllers/User/LogKegiatanController.php`
- [x] **View**: `app/Views/user/log_kegiatan/index.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Formulir Terpadu & Transaksi DB**: Penyimpanan tugas pokok & tugas tambahan simultan dalam `$db->transStart()` dan `$db->transComplete()`.
  - [x] **Sanitasi Tautan Bukti Digital**: Link bukti diverifikasi wajib berawalan skema protokol `http://` atau `https://` sebelum dirender ke tag `<a>`.
  - [x] **Kalender Flatpickr Cerdas**: Tanggal merah/akhir pekan masa depan (`.flatpickr-disabled`) berpenampilan redup pudar (`#fca5a5`, opacity 0.35), sedangkan tanggal yang sudah tiba/aktif merah cerah tegas (`#ef4444`, font-weight 700).
  - [x] **Izin Buka Kunci (Revisi)**: Superadmin & Atasan Langsung dapat membuka kunci laporan via `bukaKunci()`, terekam di audit log `UNLOCK_LAPORAN`.
  - [x] **Pembaruan Hash CSRF**: Request AJAX Tugas Tambahan otomatis memperbarui token hash CSRF ke DOM secara dinamis.
  - [x] **Ergonomi Form Seluler**: Form input log harian dan modal tugas tambahan menyesuaikan lebar layar ponsel (<576px) dengan touch target $\ge 44\text{px}$.

---

### 📌 6. Modul Rekap & Penilaian Kinerja Staf (Evaluasi Kinerja — `/penilaian-kinerja`)
- [x] **Controller**: `app/Controllers/User/PenilaianKinerjaController.php`
- [x] **View**: `app/Views/user/penilaian_kinerja/index.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Prasyarat Mutlak Penilaian**: Atasan Langsung HANYA DAPAT memberi nilai jika seluruh target kinerja staf periode tersebut sudah berstatus `disetujui`. Jika belum, form nilai terkunci dengan banner instruksi yang jelas.
  - [x] **Formula Standar Predikat Kinerja**:
    - *Sangat Baik:* `> 100%` s.d. `150%`
    - *Baik:* `> 90%` s.d. `100%`
    - *Butuh Perbaikan:* `> 75%` s.d. `90%`
    - *Kurang:* `> 25%` s.d. `75%`
    - *Sangat Kurang:* `<= 25%`
    - *Belum Dinilai:* `0%` / NULL
  - [x] **Mekanisme Reset Nilai**: Tombol Reset Nilai mengosongkan nilai ke `NULL` dan status ke `NULL` di database (bukan nilai 0 terbit), terekam di audit log `RESET_PENILAIAN_KINERJA`.
  - [x] **Proteksi IDOR Tugas Tambahan**: Validasi kepemilikan ketat `(int)$record['user_id'] === (int)$targetUserId` pada saat update nilai capaian.
  - [x] **Kalender Heatmap 8-Point Grid**: Matriks 7 kolom (Senin-Minggu) 100% bebas emoji, sel desktop `min-height: 64px`, mobile `min-height: 48px`, legenda bento capsule `height: 32px`, swatches `16px × 16px`.
  - [x] **Modal Detail Log Harian**: Header icon `40px × 40px`, navigasi `<` `>` `32px × 32px`, info banner bersih tanpa teks redundan, tabel bento `max-height: 440px`.

---

### 📌 7. Modul Monitoring Target Kinerja Bulanan Kepegawaian (`/kepegawaian/target-kinerja`)
- [x] **Controller**: `app/Controllers/Kepegawaian/MonitoringTargetController.php`
- [x] **View**: `app/Views/kepegawaian/monitoring_target.php`, `monitoring_target_pdf.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Otorisasi Ketat Multi-Role**: Hanya dapat diakses oleh: `['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk']`.
  - [x] **Pembatasan Hak Peran Wadir**: Role `wadir` memiliki akses pemantauan penuh, tetapi secara tegas tidak memiliki hak menyetujui, merevisi, atau menilai target staf.
  - [x] **Dual-View Mobile Touch Cards (<768px)**: Tabel data desktop otomatis beralih menjadi kartu sentuh mandiri (`.mobile-cards-view`) di layar ponsel dengan touch target $\ge 44\text{px}$.
  - [x] **Penanganan Mode Sepanjang Tahun (`'all'`)**: Ekspor PDF dan Excel mendukung penanganan nama periode dinamis (`nama_bulan` fallback ke nama bulan aktif saat mode 'all' agar tidak error).
  - [x] **Modal Rincian Target AJAX Zero-Reload**: Detail RHK staf dimuat cepat melalui AJAX modal dengan pemformatan angka tabular dan badge status approval.
  - [x] **Ekspor Berkas Resmi Kedinasan**: Ekspor Excel Multi-Sheet numerik murni dan PDF A4 Landscape standar instansi, tercatat di audit log `EXPORT_EXCEL_MONITORING_TARGET` dan `EXPORT_PDF_MONITORING_TARGET`.

---

### 📌 8. Modul Monitoring Penilaian Kinerja Kepegawaian (`/kepegawaian` & `/kepegawaian/monitoring-penilaian`)
- [x] **Controller**: `app/Controllers/Kepegawaian/DashboardKepegawaian.php`
- [x] **View**: `app/Views/kepegawaian/rekap_kinerja.php`, `rekap_kinerja_pdf.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Ultra-Fast 2-Query Batch Fetching**: Menghilangkan masalah query N+1 dengan mengambil data seluruh pegawai dan agregat target/log dalam 2 query SQL terindeks, lalu dipetakan in-memory.
  - [x] **Selective Column Query**: Mengambil kolom spesifik `id, nama_lengkap, nip, unit, jabatan, role, atasan_id, foto` untuk memangkas konsumsi RAM PHP hingga 65%.
  - [x] **Dual-View Mobile Cards**: Tampilan tabel rekapitulasi berganti otomatis menjadi kartu seluler (`#mobileCardsContainer`) pada layar <768px dengan badge predikat kinerja resmi.
  - [x] **Hierarki Jabatan Resmi Institusi**: Pengurutan pegawai otomatis mematuhi struktur organisasi: Direktur $\rightarrow$ Wadir $\rightarrow$ Kabag $\rightarrow$ Katim/Koordinator $\rightarrow$ Kapus $\rightarrow$ Kanit $\rightarrow$ Kaprodi/Sekprodi $\rightarrow$ Pokja $\rightarrow$ Dosen $\rightarrow$ JFT $\rightarrow$ Staf Pelaksana $\rightarrow$ Tugas Belajar.
  - [x] **Default Periode Bulan Sekarang**: Halaman pertama kali dibuka selalu otomatis memuat bulan berjalan (`date('n')`).
  - [x] **Ekspor Multi-Format**: Ekspor CSV BOM UTF-8 (format NIP `="NIP"` anti-notasi ilmiah Excel) dan PDF A4 Landscape resmi.

---

### 📌 9. Modul Dokumen Resmi (Kontrak Kinerja & Pakta Integritas)
- [x] **Controller**: `app/Controllers/User/KontrakController.php`, `app/Controllers/User/PaktaController.php`
- [x] **View**: `app/Views/user/kontrak/index.php`, `app/Views/user/pakta/index.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Failsafe Sesi & NIP**: Penanganan sesi fleksibel (`id` / `user_id` / `nip`) mencegah crash saat transisi sesi login.
  - [x] **Standardisasi Gelar Kedinasan**: Helper `format_nama_gelar()` terpusat di `tanggal_helper.php` untuk menampilkan nama pejabat dan staf secara rapi.
  - [x] **Cetak PDF Presisi Vektor A4**: Integrasi `html2pdf.js` dengan opsi skala vektor presisi (`scale: 2`) tanpa pemotongan margin halaman atau garis terbelah.
  - [x] **Responsivitas Seluler**: Kontainer pratinjau kertas `.paper-container` menyesuaikan skala layar HP tanpa horizontal overflow.

---

### 📌 10. Modul Akreditasi Institusi & LED ECC (`/ecc`)
- [x] **Controller**: `app/Controllers/EccController.php`
- [x] **View**: `app/Views/ecc/led_index.php`, `detail_standar.php`, `simulasi_index.php`, `lkps_index.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Otorisasi Simulasi Penilaian**: Akses simulasi nilai LED terkunci ketat khusus untuk peran `spm` dan `admin` via `hasAnyRole()`.
  - [x] **Multi-Prodi & Unit Filter**: Penyaringan kriteria berdasarkan Program Studi dan unit kerja struktural (AAK / KUK / All).
  - [x] **Smart Text Clamping**: Teks rubrik panjang disajikan dengan mekanisme in-place 2-line clamp yang dapat diperluas tanpa merusak grid layout.
  - [x] **Visualisasi Radar Chart**: Integrasi grafik radar dan bar Chart.js untuk memetakan capaian pemenuhan standar akreditasi.

---

### 📌 11. Modul Notifikasi Terpadu & Sinkronisasi Hari Libur
- [x] **Controller**: `app/Controllers/NotificationController.php`, `app/Controllers/Admin/MasterDataController.php`
- [x] **Checklist Kesiapan Produksi**:
  - [x] **Notifikasi Pintar Virtual**: Pengingat pengisian log harian otomatis di hari kerja (`is_working_day()`) dan pengingat batas waktu penyusunan target awal bulan.
  - [x] **Auto-Sync Hari Libur Multi-Fallback**: Sinkronisasi hari libur nasional & cuti bersama otomatis dari 3 endpoint API dengan timeout aman (3 detik).
  - [x] **CSRF Freshness**: AJAX tandai notifikasi terbaca mengembalikan token hash CSRF baru ke klien untuk mencegah error 403.

---

### 📌 12. Modul Log Keamanan Aktivitas (Audit Trail — `/admin/audit-logs`)
- [x] **Controller**: `app/Controllers/Admin/AuditLogController.php` | **View**: `app/Views/admin/audit_logs/index.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Pencatatan JSON Before-After**: Menyimpan nilai lama (`old_values`) dan nilai baru (`new_values`) dalam format JSON terstruktur.
  - [x] **Pretty JSON Modal Viewer**: Modal peninjau perubahan data JSON yang bersih dan mudah dibaca oleh auditor.
  - [x] **Filter Audit Komprehensif**: Filter dinamis berdasarkan pengguna, NIP, IP Address, rentang tanggal kalender, tipe aksi, dan entitas modul.
  - [x] **Ekspor Log Kredibel**: Dukungan ekspor laporan rekam jejak aktivitas ke lembar kerja Excel untuk kebutuhan kepatuhan hukum/audit instansi.

---

### 📌 13. Modul Pengaturan Sistem, Mode Pemeliharaan & Master Data
- [x] **Controller**: `app/Controllers/Admin/SettingsController.php`, `app/Controllers/Admin/MasterDataController.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Mode Pemeliharaan Mandiri (1-Click Maintenance Mode)**: Saklar aktivasi di menu Pengaturan mengalihkan pengguna non-admin ke halaman `public/maintenance.html` (HTTP 503) dengan auto-refresh 30 detik.
  - [x] **Pencegahan Duplikasi Master Data**: Validasi keunikan nama sasaran, indikator, satuan, dan unit kerja saat penambahan/perubahan.
  - [x] **Proteksi Deletion Barrier Unit Kerja**: Unit kerja yang masih memiliki pegawai aktif terdaftar tidak dapat dihapus sembarangan.
  - [x] **Cascading Update Unit Kerja**: Perubahan nama unit kerja di Master Data otomatis memperbarui data unit profil seluruh pegawai terkait.

---

### 📌 14. Modul Layout Utama, Navigasi Multi-Peran & Perutean (`Routes.php`)
- [x] **View**: `app/Views/layouts/main.php`, `app/Views/layouts/sidebar.php`, `app/Views/layouts/topbar.php`
- [x] **Config**: `app/Config/Routes.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Auto-Routing Strictly Disabled**: `$routes->setAutoRoute(false);` aktif untuk mengeliminasi celah eksekusi method yang tidak diinginkan.
  - [x] **117 Rute Terdaftar Eksplisit**: Seluruh 117 rute sistem terverifikasi memetakan secara valid ke Controller dan Method aktif.
  - [x] **Isolasi Filter Auth**: Seluruh rute privat terbungkus rapat dalam grup `['filter' => 'auth']`.
  - [x] **Single-Line Sidebar Navigation**: Seluruh teks link navigasi (seperti *"Rekap & Penilaian Kinerja"*, *"Monitoring Target Kinerja"*) strictly tampil dalam 1 baris tanpa wrapping jelek.
  - [x] **Mobile Drawer Touch Experience**: Menu drawer seluler offcanvas `#sidebarOffcanvas` responsif dan mulus.

---

### 📌 15. Modul Kelola Tim Saya (Team Management — `/tim`)
- [x] **Controller**: `app/Controllers/User/TimController.php` | **View**: `app/Views/user/tim_saya.php`
- **Checklist Kesiapan Produksi**:
  - [x] **Otorisasi Tim Leader**: Akses terbatas hanya untuk role manajerial dan pimpinan: `['manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'admin']`.
  - [x] **Pemetaan Staf Ganda**: Penarikan daftar anggota tim berdasarkan relasi hierarki (`atasan_id == $myId`) atau kesamaan unit kerja (`unit == $myUnit`).
  - [x] **Pengecualian Akun Terlarang**: Proteksi mutlak agar akun ber-role `admin` dan `direktur` serta akun manajer sendiri tidak dapat ditambahkan sebagai staf tim.
  - [x] **Pencarian Cerdas Multi-Field (Select2)**: Pencarian cepat pegawai di modal berdasarkan Nama, NIP, Jabatan, atau Unit Kerja dengan badge indikator *"Sudah punya atasan"*.
  - [x] **Proteksi IDOR & Validasi Kepemilikan**: Validasi kepemilikan ketat sebelum mengeluarkan staf (`removeStaf`) atau memperbarui unit kerja (`updateUnit`).
  - [x] **Pembaruan Unit Kerja Real-Time AJAX**: Perubahan unit kerja via AJAX mengembalikan token hash CSRF baru (`csrf_token() => csrf_hash()`) dan otomatis mensinkronkan peran `spm` jika unit adalah Satuan Penjaminan Mutu.
  - [x] **Audit Trail Mutasi Tim**: Pencatatan jejak audit komprehensif untuk aksi `ADD_TO_TEAM`, `REMOVE_FROM_TEAM`, dan `UPDATE_UNIT_TIM`.
  - [x] **Konfirmasi SweetAlert2 Ramah Sentuhan**: Tombol *"Keluarkan"* dari tim dilindungi konfirmasi visual SweetAlert2 dengan fallback aman `confirm()` native browser.

---

### 📌 16. Modul Arsitektur Routing & Keamanan Akses Endpoint (`app/Config/Routes.php`)
- [x] **Konfigurasi Utama**: `app/Config/Routes.php`
- [x] **Filter Pengaman**: `app/Filters/AuthFilter.php`
- **Checklist Kesiapan Produksi**:
  - [x] **AutoRoute Dinonaktifkan**: Pengaturan `$routes->setAutoRoute(false)` terkunci untuk mencegah akses controller liar.
  - [x] **Proteksi Grup Autentikasi**: Seluruh rute internal dibungkus dalam grup `['filter' => 'auth']`.
  - [x] **Kunci Metode Hapus ke POST**: Seluruh aksi penghapusan data (Master Data, SKP, dan Bukti LED) wajib menggunakan metode `POST` dan token CSRF.
  - [x] **Bebas Broken Routes**: Seluruh endpoint terdaftar eksplisit dan konsisten dengan form view.

---

## 🛡️ Checklist Kesiapan Produksi Tingkat Server, Database & DevOps

Gunakan daftar periksa berikut saat melakukan *deployment* ke server produksi (cPanel / Apache / HTTPS):

### 1. Keamanan Server & HTTP Headers
- [ ] **Aktifkan `secureheaders` di Filters Global**:
  Buka `app/Config/Filters.php`, pastikan `'secureheaders'` tercantum di `$globals['after']`.
- [ ] **Aktifkan Cookie HTTPS Secure Flag**:
  Di `app/Config/Cookie.php`, pastikan `public bool $secure = true;` aktif pada koneksi HTTPS produksi.
- [ ] **Aktifkan CSRF Token Randomization**:
  Di `app/Config/Security.php`, set `public bool $tokenRandomize = true;` untuk memitigasi serangan BREACH.
- [ ] **Perketat Root `.htaccess`**:
  Pastikan berkas sensitif (`.env`, `.git`, `composer.json`, `app/`, `writable/`) ditolak akses langsungnya via web server jika DocumentRoot mengarah ke root folder.
- [ ] **Nonaktifkan Debug Toolbar di Produksi**:
  Pastikan `CI_ENVIRONMENT = production` di berkas `.env` server agar debug toolbar tidak aktif dan error fatal disajikan melalui `app/Views/errors/html/production.php`.

### 2. Sesi & Ketahanan Beban Konkurensi Tinggi
- [ ] **Optimasi Driver Sesi (Database / Redis)**:
  Untuk menghindari *session locking* saat ratusan pegawai serentak mengisi log di akhir bulan, pertimbangkan mengganti driver sesi dari `FileHandler` ke `DatabaseHandler` di `app/Config/Session.php`.
- [ ] **Kemandirian Aset Vendor (Offline-First / CDN Fallback)**:
  Sediakan salinan lokal pustaka vendor JS/CSS utama di `public/assets/vendor/` sebagai jaminan antarmuka tetap berjalan normal jika terjadi gangguan CDN publik di jaringan intranet kampus.

### 3. Basis Data, Indeks & Pencadangan Terjadwal
- [ ] **Verifikasi Seluruh Migrasi Database Berjalan**:
  Jalankan `php spark migrate:status` untuk memastikan seluruh 51 berkas migrasi telah dieksekusi 100%.
- [ ] **Konfigurasi Cron Pencadangan Otomatis (Auto Backup)**:
  Pasang cron job harian di cPanel untuk mencadangkan database `ekinerja_kinerja` secara otomatis ke direktori aman.
- [ ] **Kebijakan Retensi Data (Housekeeping)**:
  Jadwalkan pembersihan berkas sesi kadaluarsa dan notifikasi lama (>6 bulan) untuk menjaga volume tabel tetap ramping.

---

## ⚡ Runbook: Perintah Cepat Pengujian & Audit CLI

Gunakan perintah-perintah berikut di terminal untuk menjalankan verifikasi kesehatan sistem:

```powershell
# 1. Cek Sintaks Seluruh File PHP Proyek (0 Syntax Error)
Get-ChildItem -Path app -Filter *.php -Recurse | ForEach-Object { php -l $_.FullName } | Select-String -Pattern "error"

# 2. Cek Integritas Perutean (Memastikan tidak ada broken routes)
php spark routes

# 3. Cek Status Migrasi Database
php spark migrate:status

# 4. Bersihkan Cache Sistem & Konfigurasi
php spark cache:clear

# 5. Jalankan Pengujian Otomatis (PHPUnit)
vendor/bin/phpunit
```

---

## 📊 Status Kelayakan Produksi (Production Readiness Score)

```text
[████████████████████████████████████████] 100% Modul Fungsional Lulus Audit 8-Pilar
[████████████████████████████████████░░░░]  90% Kesiapan Infrastruktur & Deployment Server
```

*Dokumen ini merupakan standar audit resmi Evidence Command Center (ECC). Setiap perubahan kode atau penambahan modul baru wajib mematuhi 8 Pilar Kualitas Produksi di atas.*
