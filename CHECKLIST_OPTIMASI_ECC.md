# 📋 DOKUMEN CHECKLIST OPTIMASI KODE, BASIS DATA & KEAMANAN SISTEM
## Evidence Command Center (ECC) — Politeknik Keselamatan Transportasi Jalan (PKTJ)

Dokumen ini adalah lembar kerja resmi pelacakan roadmap optimasi teknis, peningkatan efisiensi basis data, dan pengerasan keamanan (*security hardening*) aplikasi **Evidence Command Center (ECC)**. Pekerjaan dieksekusi secara bertahap dan terukur mulai dari nomor 1.

---

## 🎯 Ringkasan Roadmap & Prioritas Eksekusi

| No | Modul / Fokus | Kategori | Tingkat Risiko | Dampak Performa | Status |
| :---: | :--- | :---: | :---: | :---: | :---: |
| **1** | **In-Memory Caching `SettingModel`** | Kode / Performa | Sangat Rendah | 🔥 Tinggi (Pangkas 5–8 query/request) | ✅ Selesai (14 Sep 2026) |
| **2** | **Aktivasi Global HTTP Security Headers** | Keamanan (OWASP) | Sangat Rendah | 🛡️ Tinggi (Cegah Clickjacking & Sniffing) | ✅ Selesai (14 Sep 2026) |
| **3** | **Pembersihan Indeks Ganda Basis Data** | Basis Data | Sangat Rendah | ⚡ Sedang (Pangkas write-overhead DB) | ✅ Selesai (14 Sep 2026) |
| **4** | **Smart Polling Notifikasi (Page Visibility)** | Frontend / I/O | Rendah | 📉 Tinggi (Hemat resource cPanel) | ✅ Selesai (14 Sep 2026) |
| **5** | **Proteksi Anti Session Hijacking** | Keamanan | Rendah | 🔒 Tinggi (Validasi User-Agent Fingerprint) | ✅ Selesai (14 Sep 2026) |
| **6** | **Throttling Ekspor Berat (PDF & Excel)** | Keamanan / Beban | Rendah | 🛡️ Sedang (Cegah server starvation) | ✅ Selesai (14 Sep 2026) |
| **7** | **Fitur Backup Basis Data 1-Klik (Superadmin)** | Fitur Baru | Rendah | 💾 Tinggi (Kemudahan disaster recovery) | ✅ Selesai (14 Sep 2026) |
| **8** | **Housekeeping & Retensi Log Aktivitas** | Basis Data | Rendah | 🧹 Sedang (Cegah tabel membengkak) | ✅ Selesai (15 Sep 2026) |

---

## 🚀 RINCIAN CHECKLIST PEKERJAAN (STEP-BY-STEP)

---

### 📌 TUGAS 1: In-Memory Request Caching pada `SettingModel` & Eliminasi Kueri SQL Berulang

* **Latar Belakang & Masalah**:
  * Fungsi `SettingModel::getValue()` saat ini mengeksekusi kueri langsung `SELECT * FROM settings WHERE setting_key = ?` setiap kali dipanggil.
  * `MaintenanceFilter` memanggilnya 2 kali pada **setiap request HTTP**.
  * Controller kinerja (`LaporanHarianController`, `LogKegiatanController`, `NotificationController`) memanggilnya 3–5 kali lagi untuk mengecek konfigurasi batas tanggal.
  * Terjadi pemborosan 5–8 kueri SQL berulang dengan data yang sama di setiap siklus HTTP request.
* **Rencana Solusi**:
  * Terapkan *static request cache* pada `app/Models/SettingModel.php`.
  * Pada pemanggilan pertama dalam satu request, ambil seluruh setting ke dalam array asosiatif in-memory. Pemanggilan berikutnya dijawab instan $O(1)$ tanpa kueri database tambahan.
  * Tambahkan mekanisme *cache invalidation* (reset static cache) saat fungsi `SettingsController::store()` dijalankan oleh Administrator.
* **Berkas Sasaran**:
  * `[MODIFY]` `app/Models/SettingModel.php`
  * `[MODIFY]` `app/Controllers/Admin/SettingsController.php`
* **Kriteria Kelulusan**:
  * 0 error sintaks pada `php -l`.
  * Kueri ke tabel `settings` terpangkas menjadi maksimal 1 kueri per request.
  * Pengaturan yang diperbarui di menu `/settings` langsung terbaca tanpa penundaan (*zero-stale data*).
* **Status**: ✅ **Selesai & Terverifikasi (14 September 2026)**

---

### 📌 TUGAS 2: Aktivasi Global HTTP Security Headers (OWASP Hardening)

* **Latar Belakang & Masalah**:
  * Filter `CodeIgniter\Filters\SecureHeaders::class` sudah terdaftar di `$aliases` pada `app/Config/Filters.php`, namun belum diaktifkan di `$globals['after']`.
  * Akibatnya, respon HTTP belum menyertakan header proteksi keamanan modern dari serangan berbasis peramban.
* **Rencana Solusi**:
  * Daftarkan `'secureheaders'` ke dalam array `$globals['after']` pada `app/Config/Filters.php`.
  * Konfigurasi parameter header pada `app/Config/Security.php` atau filter terkait:
    * `X-Frame-Options: SAMEORIGIN` (Mencegah serangan *Clickjacking / iframe masking*).
    * `X-Content-Type-Options: nosniff` (Mencegah *MIME-Sniffing confusion*).
    * `X-XSS-Protection: 1; mode=block` (Mengaktifkan filter XSS native peramban).
    * `Referrer-Policy: strict-origin-when-cross-origin` (Melindungi kerahasiaan URL internal).
* **Berkas Sasaran**:
  * `[MODIFY]` `app/Config/Filters.php`
* **Kriteria Kelulusan**:
  * Header keamanan terverifikasi muncul pada seluruh response HTTP (dapat diuji via `curl -I` atau Network Tab browser).
  * Seluruh komponen antarmuka, aset visual, dan modal SweetAlert2 tetap berfungsi 100% normal tanpa *blocked content*.
* **Status**: ✅ **Selesai & Terverifikasi (14 September 2026)**

---

### 📌 TUGAS 3: Pembersihan Indeks Ganda (*Duplicate Indexes*) di Basis Data

* **Latar Belakang & Masalah**:
  * Dari audit statistik indeks basis data `ekinerja_kinerja`, ditemukan indeks duplikat yang memiliki daftar kolom identik:
    1. Tabel `target_kinerja_bulanan`: Indeks `idx_lh_user_bulan_tahun` dan `idx_user_bulan_tahun` (keduanya pada `(user_id, bulan, tahun)`).
    2. Tabel `log_tugas_tambahan`: Indeks `idx_ltt_user_tgl` dan `idx_user_tgl` (keduanya pada `(user_id, tanggal_kegiatan)`).
  * Indeks ganda membuang memori buffer pool MySQL dan memperlambat operasi *write* (`INSERT`, `UPDATE`, `DELETE`) karena server harus memperbarui dua pohon indeks B-Tree yang sama.
* **Rencana Solusi**:
  * Menjalankan kueri penghapusan indeks duplikat:
    ```sql
    ALTER TABLE `target_kinerja_bulanan` DROP INDEX `idx_user_bulan_tahun`;
    ALTER TABLE `log_tugas_tambahan` DROP INDEX `idx_user_tgl`;
    ```
  * Menjaga indeks utama (`idx_lh_user_bulan_tahun` dan `idx_ltt_user_tgl`) tetap aktif untuk mempercepat pencarian data.
* **Berkas / Objek Sasaran**:
  * Basis Data MySQL lokal (`ekinerja_kinerja`) dan dokumentasi panduan cPanel.
* **Kriteria Kelulusan**:
  * Kueri SELECT bulanan tetap berjalan cepat dengan indeks utama.
  * Ukuran pohon indeks basis data lebih efisien.
* **Status**: ✅ **Selesai & Terverifikasi (14 September 2026)**

---

### 📌 TUGAS 4: Smart Polling Notifikasi Berbasis Page Visibility API

* **Latar Belakang & Masalah**:
  * Skrip notifikasi di `app/Views/layouts/main.php` melakukan fetch berkala setiap 5 menit (`setInterval(fetchNotifications, 300000)`).
  * Polling ini berjalan terus-menerus tanpa henti meskipun jendela browser diminimalkan atau pengguna sedang membuka tab lain seharian, sehingga membebani koneksi server cPanel secara percuma.
* **Rencana Solusi**:
  * Integrasikan **HTML5 Page Visibility API** (`document.visibilityState` / event `visibilitychange`).
  * Saat tab disembunyikan (*hidden*), jeda interval polling.
  * Saat tab kembali aktif (*visible*), lakukan fetch langsung jika waktu interval sudah terlampaui (*smart immediate resume*).
* **Berkas Sasaran**:
  * `[MODIFY]` `app/Views/layouts/main.php`
* **Kriteria Kelulusan**:
  * Tidak ada kueri polling jaringan saat tab browser berada di latar belakang (*backgrounded*).
  * Notifikasi terbarui seketika begitu tab kembali dibuka pengguna.
* **Status**: ✅ **Selesai & Terverifikasi (14 September 2026)**

---

### 📌 TUGAS 5: Proteksi Pembajakan Sesi (*Session Hijacking Defense*)

* **Latar Belakang & Masalah**:
  * Jika kuki sesi pengguna berhasil dicuri via jaringan publik tanpa enkripsi atau malware di sisi klien, kuki tersebut secara teoritis dapat dipakai oleh perangkat lain selama sesi belum kedaluwarsa.
* **Rencana Solusi**:
  * Pada saat login berhasil di `app/Controllers/Auth.php`, buat *browser signature fingerprint* berdasarkan kombinasi `User-Agent` dan subnet IP:
    ```php
    session()->set('user_agent_fingerprint', hash('sha256', $this->request->getUserAgent()->getAgentString()));
    ```
  * Di `app/Filters/AuthFilter.php`, verifikasi kecocokan signature tersebut. Jika terjadi perbedaan drastis (kuki dipakai di peramban yang berbeda sama sekali), hancurkan sesi dan alihkan ke login dengan pesan peringatan keamanan yang tenang.
* **Berkas Sasaran**:
  * `[MODIFY]` `app/Controllers/Auth.php`
  * `[MODIFY]` `app/Filters/AuthFilter.php`
* **Kriteria Kelulusan**:
  * Pengguna normal dapat bernavigasi tanpa kendala.
  * Sesi otomatis terputus jika terdeteksi manipulasi User-Agent.
* **Status**: ✅ **Selesai & Terverifikasi (14 September 2026)**

---

### 📌 TUGAS 6: Rate Limiting / Throttling pada Ekspor Dokumen Berat

* **Latar Belakang & Masalah**:
  * Fitur ekspor Excel Multi-Sheet dan PDF A4 Landscape (`/kepegawaian/export-excel`, `/kepegawaian/export-pdf`, dll.) membutuhkan resource CPU dan alokasi memori PHP (PhpSpreadsheet & Dompdf) yang signifikan.
  * Klik beruntun atau penarikan massal tanpa jeda berpotensi menyebabkan *resource exhaustion* pada server cPanel.
* **Rencana Solusi**:
  * Terapkan pembatasan rate limit berbasis Throttler CI4 pada method ekspor (misal: maksimal 5 unduhan per menit per akun pengguna).
  * Tampilkan pesan ramah pengguna jika batas terlampaui: *"Pengunduhan dokumen dibatasi. Silakan tunggu beberapa detik sebelum mengunduh kembali."*
* **Berkas Sasaran**:
  * `[MODIFY]` `app/Controllers/BaseController.php` (Method pembantu `checkExportRateLimit()`)
  * `[MODIFY]` `app/Controllers/Kepegawaian/DashboardKepegawaian.php` (`exportExcel()`, `exportPdf()`)
  * `[MODIFY]` `app/Controllers/Kepegawaian/MonitoringTargetController.php` (`exportExcel()`, `exportPdf()`)
  * `[MODIFY]` `app/Controllers/Admin/MonitoringController.php` (`exportExcel()`, `exportPdf()`)
  * `[MODIFY]` `app/Views/kepegawaian/rekap_kinerja.php` (Alert banner flashdata error/success)
  * `[MODIFY]` `app/Views/kepegawaian/monitoring_target.php` (Alert banner flashdata error/success)
* **Kriteria Kelulusan**:
  * Unduhan wajar berjalan mulus.
  * Spam klik berulang tertahan dengan respons aman tanpa membebani server.
  * Audit log mencatat aktivitas ekspor dan penolakan rate limit (`RATE_LIMIT_EXPORT`).
* **Status**: ✅ **Selesai & Terverifikasi (14 September 2026)**

---

### 📌 TUGAS 7: Fitur Backup Basis Data 1-Klik untuk Superadmin

* **Latar Belakang & Masalah**:
  * Saat ini pencadangan basis data harus dilakukan secara manual melalui phpMyAdmin cPanel atau mysqldump di terminal.
* **Rencana Solusi**:
  * Sediakan tombol *"Unduh Cadangan Basis Data (.sql)"* di menu Pengaturan Sistem (`/settings`).
  * Endpoint hanya dapat diakses oleh role `admin` dengan verifikasi CSRF dan pencatatan audit log `BACKUP_DATABASE`.
  * Menghasilkan dump SQL terkompresi menggunakan CodeIgniter Database Utility / stream writer yang hemat memori.
* **Berkas Sasaran**:
  * `[MODIFY]` `app/Config/Routes.php` (Pendaftaran rute `settings/backup-db`)
  * `[MODIFY]` `app/Controllers/Admin/SettingsController.php` (Method `backupDatabase()`)
  * `[MODIFY]` `app/Views/admin/settings/index.php` (Bento Card Cadangan Basis Data & dialog SweetAlert2)
* **Kriteria Kelulusan**:
  * Berkas dump `.sql` dapat diunduh langsung via 1-klik dan 100% kompatibel di-restore ke phpMyAdmin.
  * Hanya dapat diakses oleh Administrator dengan proteksi rate limit dan pencatatan audit trail `BACKUP_DATABASE`.
* **Status**: ✅ **Selesai & Terverifikasi (14 September 2026)**

---

### 📌 TUGAS 8: Housekeeping & Manajemen Retensi Data Log Aktivitas

* **Latar Belakang & Masalah**:
  * Tabel `audit_logs` dan `notifications` akan terus membesar seiring berjalannya waktu operasional aplikasi.
* **Rencana Solusi**:
  * Buat tombol dan kebijakan retensi di Pengaturan Sistem untuk mengarsipkan atau membersihkan log yang sudah berusia lebih dari 12 bulan.
  * Dilengkapi konfirmasi SweetAlert2 dan pencatatan audit trail sebelum eksekusi pembersihan.
* **Berkas Sasaran**:
  * `[MODIFY]` `app/Config/Routes.php` (Rute `POST settings/purge-logs`)
  * `[MODIFY]` `app/Controllers/Admin/SettingsController.php` (Method `purgeLogs()`)
  * `[MODIFY]` `app/Views/admin/settings/index.php` (Bento Card Housekeeping, pilihan retensi & target, dialog SweetAlert2)
* **Kriteria Kelulusan**:
  * Data lama terhapus aman dengan transaksi database, data terkini tetap terjaga utuh.
  * Tindakan pembersihan otomatis tercatat ke dalam audit trail keamanan `PURGE_OLD_LOGS`.
* **Status**: ✅ **Selesai & Terverifikasi (15 September 2026)**

---

## 📊 Lembar Kemajuan Pelaksanaan

```text
Progress: [████████████████████████████████████████] 100% (8 dari 8 Selesai) - SELESAI SEMPURNA
```

---

## 🗄️ Kumpulan Kueri SQL Siap Eksekusi di phpMyAdmin cPanel

Salin seluruh blok kueri SQL di bawah ini dan tempelkan langsung pada tab **SQL** di phpMyAdmin cPanel database `ekinerja_kinerja`:

```sql
-- =========================================================================
-- EVIDENCE COMMAND CENTER (ECC) — PENYESUAIAN BASIS DATA CPANEL
-- Tanggal: 15 September 2026
-- Lingkungan Sasaran: cPanel phpMyAdmin (ekinerja_kinerja)
-- =========================================================================

-- 1. DECOMMISSIONING: Hapus Tabel SKP Legacy yang Sudah Tidak Digunakan
DROP TABLE IF EXISTS `skp_targets`;
DROP TABLE IF EXISTS `skp_headers`;

-- 2. SINKRONISASI MIGRASI CI4 (Mencatat Batch 44 agar status migrasi 100% up-to-date)
INSERT IGNORE INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
('2026-09-02-121500', 'App\\Database\\Migrations\\AddIndexToTargetKinerjaBulanan', 'default', 'App', UNIX_TIMESTAMP(), 44),
('2026-09-02-150000', 'App\\Database\\Migrations\\AddIndexToLogTugasTambahan', 'default', 'App', UNIX_TIMESTAMP(), 44),
('2026-09-14-153000', 'App\\Database\\Migrations\\DropDuplicateIndexes', 'default', 'App', UNIX_TIMESTAMP(), 44);

-- Selesai. Struktur tabel di cPanel sudah memiliki indeks komposit utama 
-- (idx_lh_user_bulan_tahun & idx_ltt_user_tgl) sehingga bebas dari error #1091.
```

*(Dokumen ini akan diperbarui statusnya secara berkala setiap kali satu nomor tugas selesai dikerjakan dan diverifikasi)*

