# Laporan Audit Kode Fungsional: Menu Pengaturan Sistem

- **Modul**: Pengaturan Sistem (`/settings`)
- **Tanggal Audit**: 10 Oktober 2026
- **Acuan Standar**: [audit_code.md](file:///c:/laragon/www/SimonikPKTJ/audit_code.md), [.agents/AGENTS.md](file:///c:/laragon/www/SimonikPKTJ/.agents/AGENTS.md), dan [PRD.md](file:///c:/laragon/www/SimonikPKTJ/PRD.md).
- **Titik Masuk Utama**:
  - Controller: [app/Controllers/Admin/SettingsController.php](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/Admin/SettingsController.php)
  - Model: [app/Models/SettingModel.php](file:///c:/laragon/www/SimonikPKTJ/app/Models/SettingModel.php)
  - Filter: [app/Filters/MaintenanceFilter.php](file:///c:/laragon/www/SimonikPKTJ/app/Filters/MaintenanceFilter.php)
  - Konsumen Pengaturan:
    - Target Bulanan: [app/Controllers/User/LaporanHarianController.php](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/User/LaporanHarianController.php)
    - Log Harian: [app/Controllers/User/LogKegiatanController.php](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/User/LogKegiatanController.php)
    - Penilaian Kinerja: [app/Controllers/User/PenilaianKinerjaController.php](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/User/PenilaianKinerjaController.php)

---

## 1. Ringkasan Eksekutif
Audit fungsional komprehensif dilakukan terhadap 6 fitur utama pada menu Pengaturan Sistem:
1. **Mode Pemeliharaan (Maintenance Mode)**: Saklar aktivasi global dan pesan kustom untuk pengguna non-admin.
2. **Batas Waktu Target Kinerja Bulanan**: Saklar batas waktu dan tanggal maksimal pengajuan target.
3. **Kunci Laporan Bulan Lalu**: Saklar kunci akhir bulan (cutoff) dan toleransi hari tambahan.
4. **Toleransi Harian Pelaporan Log**: Saklar batas bergulir harian (rolling daily limit).
5. **Batas Waktu Penilaian Kinerja Staf**: Saklar batas waktu penilaian atasan di bulan berikutnya.
6. **Penonaktifan Kalender Heatmap Berbasis Tahun**: Multi-select tahun untuk menyembunyikan heatmap dan menggantikannya dengan tabel kegiatan harian.
7. **Cadangan Basis Data (Database Backup)**: Ekspor dump SQL 1-klik.
8. **Pemeliharaan & Retensi Log Sistem (Housekeeping)**: Pembersihan data log audit dan notifikasi lama.

Secara umum seluruh fitur inti telah berjalan, namun ditemukan **5 celah fungsional dan performa** yang membutuhkan perbaikan.

---

## 2. Temuan Audit Berdasarkan 8 Pilar `audit_code.md`

### A. Pilar 2: Logika Bisnis & Integritas Data
#### [TEMUAN F-01 - TINGGI] Ketiadaan Transaksi Basis Data Atomik pada `SettingsController::store()`
- **Lokasi**: [app/Controllers/Admin/SettingsController.php:140-200](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/Admin/SettingsController.php#L140-L200)
- **Kondisi**: Mutasi pengaturan sistem mengeksekusi hingga 7 kueri `update()` secara berurutan tanpa dibungkus dalam Database Transaction.
- **Dampak**: Jika koneksi terputus di tengah pembaruan, sistem akan berada dalam status parsial (sebagian saklar aktif, sebagian belum), melanggar aturan atomisitas transaksi pada [.agents/AGENTS.md](file:///c:/laragon/www/SimonikPKTJ/.agents/AGENTS.md) Aturan 3.
- **Rekomendasi**: Bungkus proses mutasi dengan `$db->transStart()`, `$db->transComplete()`, dan blok `try...catch`.

#### [TEMUAN F-02 - SEDANG] Hardcoded Clamp Tanggal 28 pada Batas Penilaian Kinerja Bulan Non-Februari
- **Lokasi**: [app/Controllers/User/PenilaianKinerjaController.php:508](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/User/PenilaianKinerjaController.php#L508)
- **Kondisi**: Pada logika validasi batas waktu penilaian atasan, terdapat baris:
  `$deadlineDay = min(28, max(1, $batasPenilaian));`
- **Dampak**: Jika admin menyetel batas tanggal penilaian pada tanggal 30 atau 31, sistem secara sepihak memotongnya menjadi tanggal 28 bahkan untuk bulan-bulan yang memiliki 30 atau 31 hari (seperti Januari, Maret, Mei, dst.).
- **Rekomendasi**: Ganti pemotongan hardcoded 28 dengan jumlah hari nyata pada bulan evaluasi terkait: `min((int)$nextMonthOfEval->format('t'), max(1, $batasPenilaian))`.

---

### B. Pilar 1 & 4: Struktur Kode & Keamanan Masukan
#### [TEMUAN F-03 - SEDANG] Ketiadaan Whitelist Kunci pada Array `$settings` POST
- **Lokasi**: [app/Controllers/Admin/SettingsController.php:157-180](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/Admin/SettingsController.php#L157-L180)
- **Kondisi**: Controller melakukan iterasi pada seluruh kunci array `$this->request->getPost('settings')` tanpa memeriksa apakah kunci tersebut termasuk dalam daftar pengaturan sistem yang sah.
- **Dampak**: Injeksi kunci sembarang pada payload POST dapat memicu kueri update yang tidak perlu ke database.
- **Rekomendasi**: Terapkan whitelist kunci resmi: `['batas_input_target', 'toleransi_hari_bulan_lalu', 'batas_input_log', 'batas_penilaian_kinerja', 'maintenance_message']`.

---

### C. Pilar 5: Performa & Skalabilitas
#### [TEMUAN F-04 - TINGGI] Risiko Out of Memory (OOM) & Timeout saat Pencadangan Basis Data
- **Lokasi**: [app/Controllers/Admin/SettingsController.php:298-303](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/Admin/SettingsController.php#L298-L303)
- **Kondisi**:
  1. `backupDatabase()` memuat seluruh baris tabel sekaligus ke memori PHP menggunakan `$query->getResultArray()`.
  2. Tidak ada penambahan batas waktu eksekusi (`set_time_limit(0)`).
- **Dampak**: Pada lingkungan produksi dengan data log kegiatan atau audit trail mencapai puluhan ribu baris, proses unduh cadangan berisiko gagal karena kehabisan alokasi memori PHP (`memory_limit`) atau batas waktu eksekusi skrip (30 detik).
- **Rekomendasi**:
  1. Tambahkan `set_time_limit(0);` di awal method.
  2. Gunakan pembacaan baris unbuffered streaming: `$result->getUnbufferedRow('array')` sehingga konsumsi RAM tetap ringan O(1) berapa pun jumlah data.

#### [TEMUAN F-05 - SEDANG] Kueri Ganda Redundan pada `purgeLogs()`
- **Lokasi**: [app/Controllers/Admin/SettingsController.php:369-387](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/Admin/SettingsController.php#L369-L387)
- **Kondisi**: Method menjalankan `countAllResults(false)` terlebih dahulu, lalu menjalankan kueri `delete()` terpisah.
- **Dampak**: Beban kueri menjadi dua kali lipat dan rentan terhadap ketidakakuratan jika terjadi perubahan data bersamaan (*race condition*).
- **Rekomendasi**: Langsung eksekusi `delete()` dan periksa baris terhapus menggunakan `$db->affectedRows()`.

---

## 3. Fitur yang Telah Bekerja dengan Sangat Baik (Lulus Audit)
- **Filter Maintenance Mode (`MaintenanceFilter.php`)**: Berjalan sempurna, mengecualikan role `admin`, mendukung pesan kustom, merespons kode 503 dengan benar baik untuk request web HTML maupun request AJAX JSON.
- **Kontrol Hak Akses**: Seluruh method dilindungi pemeriksaan `hasRole('admin')` dan filter `auth`.
- **In-Memory Caching (`SettingModel.php`)**: Cache statis request-scoped menghemat hingga 5–8 kueri redundan per request dan otomatis ter-invalidation saat data disimpan (`clearCache()`).
- **Audit Logging**: Setiap aksi mutasi, backup, dan purge tercatat lengkap ke tabel `audit_logs` dengan format terstruktur.
- **Rate Limiting**: Unduhan basis data dibatasi maksimal 3 kali per menit untuk mencegah penyalahgunaan resource server.
