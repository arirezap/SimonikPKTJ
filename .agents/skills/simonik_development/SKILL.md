---
name: "ECC (Evidence Command Center) Development"
description: "Panduan arsitektur, modul utama, dan aturan khusus untuk membantu AI Agent mengembangkan aplikasi ECC (Evidence Command Center)."
---

# ECC (Evidence Command Center) Development Guide & Project Blueprint

Panduan ini berisi pedoman lengkap arsitektur sistem, peta modul, basis data, dan standarisasi kode untuk proyek **ECC (Evidence Command Center)**. Gunakan ini untuk memahami alur kerja dan memastikan kode yang Anda buat konsisten dengan struktur yang sudah ada.

---

## 1. Spesifikasi Tech Stack
- **Framework Utama:** CodeIgniter 4 (PHP 8.1+)
- **Basis Data:** MySQL
- **Desain UI:** Bootstrap 5.3 (Vanilla CSS/JS, hindari TailwindCSS kecuali diminta secara eksplisit)
- **Library Frontend:**
  - **jQuery:** Digunakan untuk manipulasi DOM dan request AJAX dasar.
  - **Flatpickr:** Digunakan untuk custom calendar datepicker harian dengan indikator titik status aktivitas kerja.
  - **Select2:** Digunakan untuk *Searchable Dropdown* (pencarian nama pegawai / unit kerja).
  - **Chart.js:** Digunakan untuk merender grafik analisis performa individu dan dashboard unit eksekutif.
  - **SweetAlert2:** Digunakan untuk interaksi dialog konfirmasi (selalu sertakan *native browser fallback*).

---

## 2. Struktur Proyek & Konvensi MVC
- **Controllers (`app/Controllers/`):**
  - Gunakan penamaan file PascalCase.
  - Pisahkan area admin di `app/Controllers/Admin/` (misal: `UserController.php`, `MasterDataController.php`), area kepegawaian di `app/Controllers/Kepegawaian/` (misal: `DashboardKepegawaian.php`, `MonitoringTargetController.php`), dan area pengguna di `app/Controllers/User/` (misal: `PenilaianKinerjaController.php`, `LogKegiatanController.php`, `LaporanHarianController.php`).
- **Models (`app/Models/`):**
  - Pastikan setiap model mendefinisikan `$table`, `$primaryKey`, dan `$allowedFields` agar query builder CI4 berjalan optimal dan aman.
- **Views (`app/Views/`):**
  - Semua file berformat `.php`.
  - Gunakan pemetaan template layout (`$this->extend()`, `$this->section()`).
  - Selalu bersihkan output menggunakan `esc()` untuk mencegah kerentanan XSS.
- **Routing (`app/Config/Routes.php`):**
  - Semua route **WAJIB** terdaftar secara eksplisit di dalam grup filter otentikasi `auth` (seperti `$routes->group('', ['filter' => 'auth'], ...)`).
  - Sertakan rute POST untuk semua endpoint AJAX (misal: `log-kegiatan/storeTugasTambahan`, `log-kegiatan/hapusTugasTambahan`, `laporan-harian/approve`, `penilaian-kinerja/store`). Jangan pernah mengandalkan auto-routing di cPanel.

---

## 3. Peta Modul Utama Aplikasi

### A. Modul Target Kinerja Bulanan & Auto-Approval Direktur
- **Target Kinerja Bulanan (`app/Models/TargetKinerja.php` & `User\LaporanHarianController`):**
  - Tempat pegawai menyusun Rencana Hasil Kerja (RHK) dan target kuantitas bulanan.
  - **Khusus Akun Direktur:** Target yang dibuat otomatis berstatus `disetujui` (`status_approval = 'disetujui'`, `status = 'terkirim'`) dan dapat direvisi secara mandiri kapan saja tanpa memerlukan approval pihak lain.
  - **Pegawai Non-Direktur:** Target berstatus `menunggu_persetujuan` dan harus disetujui Atasan Langsung sebelum dapat dinilai.

### B. Modul Laporan Harian & Log Kegiatan (`/log-kegiatan`)
- **Pencatatan Aktivitas Harian (`app/Models/LogKegiatanHarian.php`, `LogTugasTambahan` & `User\LogKegiatanController`):**
  - Mencatat realisasi harian tugas pokok & tugas tambahan beserta link bukti.
  - **Datepicker Flatpickr Terintegrasi:** Menampilkan titik status (Hijau = Terkirim, Kuning = Draf, Merah = Belum Diisi).
  - **Styling Tanggal Merah & Weekend:** Tanggal merah/akhir pekan yang berstatus masa depan (`.flatpickr-disabled`) berpenampilan redup pudar (`#fca5a5`, opacity 0.35, normal weight), sedangkan tanggal yang sudah tiba/aktif berpenampilan merah cerah tegas (`#ef4444`, font-weight 700, opacity 1).

### C. Modul Rekap & Penilaian Kinerja (`/penilaian-kinerja`)
- **Penilaian Kinerja Staf (`User\PenilaianKinerjaController`):**
  - Atasan Langsung HANYA DAPAT memberi nilai jika seluruh target kinerja bulanan staf pada periode terkait sudah disetujui.
  - **Formula Standar Predikat Kinerja:**
    - Sangat Baik: `> 100%` s.d. `150%`
    - Baik: `>= 90%` s.d. `100%`
    - Butuh Perbaikan: `> 75%` s.d. `< 90%`
    - Kurang: `> 25%` s.d. `75%`
    - Sangat Kurang: `<= 25%`
    - Belum Dinilai: `0%` (atau belum ada penilaian / RHK dinilai = 0)
  - **Fitur Reset Penilaian Kinerja:**
    - Tombol Reset Nilai langsung mengosongkan nilai (`nilai_capaian = NULL`) dan menyetel flag `status_penilaian = NULL` di database, mengembalikan status menjadi "Belum Dinilai" murni (bukan berstatus `terbit` dengan nilai 0).

### D. Modul Monitoring Kepegawaian (`/kepegawaian/target-kinerja` & `/kepegawaian`)
- **Akses Terbatas (Role-Restricted):**
  - Menu tree dan endpoint modul ini HANYA diizinkan untuk role: `direktur`, `wadir`, `kabag` (`kabag_aak`, `kabag_kuk`), `kepegawaian`, dan `admin`.
  - **Monitoring Target Kinerja:** Pemantauan status penyusunan target seluruh unit kerja instansi.
  - **Monitoring Penilaian Kinerja:** Rekapitulasi nilai dan capaian kinerja seluruh pegawai institusi dengan ekspor Excel Multi-Sheet dan PDF A4 Landscape berstandar resmi.
  - Menggunakan *selective column querying* (`select('id, nama_lengkap, nip, unit, jabatan, role, atasan_id, foto')`) untuk efisiensi memori tingkat tinggi.

### E. Modul Autentikasi & Keamanan Sesi (OWASP Compliant)
- **Pencegahan User Enumeration (`app/Controllers/Auth.php`):** Pesan kesalahan login seragam (*"Nama pengguna atau kata sandi yang Anda masukkan salah."*).
- **Pencatatan Audit Trail `FAILED_LOGIN`:** Merekam kegagalan login dengan IP Address dan alasan.
- **Hardened Logout:** Logout via form POST terlindungi CSRF (`#logoutPostForm`), audit log `LOGOUT`, dan header `Cache-Control: no-store`.

### F. Modul Pengendalian Superadmin
- **Buka Kunci Laporan Harian Staf (`POST log-kegiatan/buka-kunci`):** Superadmin dapat membuka kunci laporan harian yang terkunci, mencatat audit log `UNLOCK_LAPORAN`.
- **Pembatalan Persetujuan Target Bulanan (`POST laporan-harian/batal-approve`):** Superadmin dapat membatalkan persetujuan target bulanan staf untuk revisi, mencatat audit log `CANCEL_APPROVE_TARGET`.

---

## 4. Standarisasi UI/UX, 8-Point Grid System & Kualitas Visual
- **8-Point Grid System (Strict Spacing & Asset Scale):**
  - Seluruh layout, jarak elemen (`margin`, `padding`, `gap`), tinggi tombol, dan wadah aset wajib mematuhi kelipatan 8px: `4px` (0.5x micro), `8px` (1x base), `12px` (1.5x), `16px` (2x), `24px` (3x), `32px` (4x), `40px` (5x), `48px` (6x), `64px` (8x), `80px` (10x).
  - Standar ukuran aset: Swatch `16px × 16px`, tombol compact `height: 32px`, kontrol form `height: 36px`–`40px`, tombol aksi CTA `min-height: 40px`, box icon header modal `40px × 40px`, sel kalender desktop `min-height: 64px` (mobile `48px`), avatar profil `40px`/`64px`/`80px`.
- **Tabular Numbers:** Selalu gunakan `font-variant-numeric: tabular-nums; font-feature-settings: "tnum";` pada angka capaian, nilai persen, tanggal, dan NIP.
- **Sanitasi URL Bukti XSS:** Selalu validasi bahwa link bukti berawalan skema `http://` atau `https://` sebelum dirender ke tag `<a>`.

---

## 5. Aturan Penulisan Kode & Keamanan
- **CSRF Protection:** Semua elemen `<form>` wajib menyertakan `<?= csrf_field() ?>`. Request AJAX POST wajib mengirim token CSRF dan memperbarui `csrf_hash`.
- **Database Transactions:** Semua mutasi batch wajib dibungkus dalam blok `try...catch (\Exception $e)` dan `$db->transStart()` / `$db->transComplete()`.
- **Sanitasi Desimal:** Selalu gunakan `str_replace(',', '.', trim((string)$val))` sebelum parsing numerik.
- **SweetAlert2 Fallback:** Selalu sediakan *native browser fallback* (`confirm()`) jika library SweetAlert2 belum selesai termuat.
- **XSS Prevention:** Selalu gunakan `esc($var)` saat mencetak variabel ke View HTML.
- **Standardized Audit Logging:** Selalu gunakan `log_audit()` untuk merekam mutasi data penting.
