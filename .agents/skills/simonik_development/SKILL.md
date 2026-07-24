---
name: "ECC (Evidence Command Center) Development"
description: "Panduan arsitektur, modul utama, dan aturan khusus untuk membantu AI Agent mengembangkan aplikasi ECC (Evidence Command Center)."
---

# ECC (Evidence Command Center) Development Guide & Project Blueprint

Panduan ini berisi pedoman lengkap arsitektur sistem, peta modul, basis data, dan standarisasi kode untuk proyek **ECC (Evidence Command Center)**. Gunakan ini untuk memahami alur kerja dan memastikan kode yang Anda buat konsisten dengan struktur yang sudah ada.

---

## 1. Spesifikasi Tech Stack
- **Framework Utama:** CodeIgniter 4 (PHP 8.0+)
- **Basis Data:** MySQL
- **Desain UI:** Bootstrap 5 (Vanilla CSS/JS, hindari TailwindCSS kecuali diminta secara eksplisit)
- **Library Frontend:**
  - **jQuery:** Digunakan untuk manipulasi DOM dan request AJAX dasar.
  - **Select2:** Digunakan untuk *Searchable Dropdown* (pencarian nama pegawai / unit kerja).
  - **Chart.js:** Digunakan untuk merender grafik analisis performa individu dan dashboard unit eksekutif.

---

## 2. Struktur Proyek & Konvensi MVC
- **Controllers (`app/Controllers/`):**
  - Gunakan penamaan file PascalCase.
  - Pisahkan area admin di subfolder `app/Controllers/Admin/` (misal: `UserController.php`, `MasterDataController.php`) dan area pengguna di `app/Controllers/User/` (misal: `PenilaianKinerjaController.php`, `LogKegiatanController.php`).
- **Models (`app/Models/`):**
  - Pastikan setiap model mendefinisikan `$table`, `$primaryKey`, dan `$allowedFields` agar query builder CI4 berjalan optimal dan aman.
- **Views (`app/Views/`):**
  - Semua file berformat `.php`.
  - Gunakan pemetaan template layout (`$this->extend()`, `$this->section()`).
  - Selalu bersihkan output menggunakan `esc()` untuk mencegah kerentanan XSS.
- **Routing (`app/Config/Routes.php`):**
  - Semua route terdaftar di dalam grup filter otentikasi `auth` (seperti `$routes->group('', ['filter' => 'auth'], ...)`).
  - Jangan mengandalkan auto-routing bawaan untuk modul-modul penting.

---

## 3. Peta Modul Utama Aplikasi
### A. Modul Kinerja Pegawai & Log Kegiatan
- **Rencana Kinerja (`app/Models/RencanaKinerja.php` & `User\InputRencana`):** Digunakan untuk merumuskan indikator target kerja individu.
- **Laporan Harian & Log Kegiatan (`app/Models/LogKegiatanHarian.php` & `User\LogKegiatanController`):** Tempat pegawai mencatat aktivitas harian mereka secara real-time.
- **SKP & Target Kerja (`app/Models/SkpModel.php` & `User\Skp`):** Pengelolaan Sasaran Kerja Pegawai tahunan/semesteran.

### B. Modul Penilaian & Dashboard Analisis
- **Penilaian Kinerja (`User\PenilaianKinerjaController`):**
  - Atasan memberikan penilaian harian terhadap input kegiatan harian stafnya berdasarkan indikator **Disiplin** dan **Kerjasama** yang otomatis dihitung nilai rata-rata hariannya.
  - **Tab Analisis Kinerja (Individu):** Grafik tren 6 bulan terakhir, kualitas/ketepatan waktu, dan produktivitas pegawai bersangkutan.
  - **Tab Analisis Keseluruhan (Agregat/Eksekutif):** Menampilkan perbandingan performa antar unit kerja, tingkat kedisiplinan per unit, status penilaian unit (Dinilai vs Belum Dinilai), serta menampilkan daftar Top 5 Performers.
  - Data analitik dimuat secara dinamis via AJAX lewat rute API `penilaian-kinerja/api-chart`.

### C. Modul Evaluasi Standar & Simulasi (ECC & LED)
- **Kriteria LED (`app/Models/LedCriteria.php` & `Admin\MasterDataController::led`):** Pengelolaan kriteria Evaluasi Diri.
- **ECC & Simulasi (`app/Controllers/EccController`):** Modul simulasi Evaluasi Capaian Kinerja (LKPS dan Standar LED) untuk keperluan akreditasi kampus politeknik.

### D. Modul Administrasi & Master Data
- **Kelola Pengguna (`Admin\UserController`):** Mendukung pencarian, export/import via Excel, batch edit unit, dan update data unit kerja secara AJAX.
- **Master Data (`Admin\MasterDataController`):** Manajemen Sasaran, Indikator Kinerja, Satuan, Unit Kerja, Standar LED, dan Kriteria LED.

---

## 4. Logika Wewenang & Otorisasi Pengguna (Roles)
Aplikasi membagi wewenang berdasarkan peran jabatan:
1. **Pegawai Biasa (Staf):** Hanya dapat menginput Rencana, Realisasi, Laporan Harian, SKP, serta melihat visualisasi grafik performa pribadinya sendiri di Tab *Analisis Kinerja*.
2. **Atasan Menengah (Kabag/Kepala Unit):** Memiliki wewenang untuk melihat, menilai, dan memantau rekap data staf yang berada *di dalam unit kerjanya saja*.
3. **Direksi (Direktur/Wakil Direktur) & Superadmin:** Memiliki otoritas penuh untuk memantau performa *seluruh unit kerja* dan *seluruh pegawai* di kampus Politeknik, serta mengakses tab agregat eksekutif.

---

## 5. Standarisasi UI/UX & Kualitas Grafik
- **UI/UX Pro Max:** Seluruh pengembangan UI wajib mematuhi standar desain dari skill `ui-ux-pro-max` (estetika kaya, tipografi modern, micro-animations, dan komponen premium di atas MVP standar Bootstrap).
- **Layout Rapi & Rata:** Pastikan tombol aksi (seperti tombol Reset Filter) berukuran proporsional, sejajar tinggi elemennya dengan Select2 dropdown, dan menggunakan variasi outline/icon yang intuitif bagi pengguna senior.
- **Grafik Bebas Tumpukan (Anti-Overlap):**
  - Jangan gunakan *Radar Chart* jika data label sumbu berjumlah banyak (misal menampilkan nama pegawai individu). Radar chart sebaiknya digunakan maksimal untuk 6-8 label (misalnya memetakan rata-rata unit kerja).
  - Untuk data berskala besar (seperti ranking seluruh pegawai), gunakan **Horizontal Bar Chart** yang dibungkus di dalam wadah div scrollable (`max-height: 450px; overflow-y: auto;`) agar tidak merusak layout halaman.
  - Untuk grafik status (seperti laporan Dinilai vs Belum), gunakan **Stacked Horizontal Bar Chart** dengan warna kontras yang ramah mata.

---

## 6. Aturan Penulisan Kode & Keamanan
- **CSRF Protection:** Semua elemen `<form>` wajib menyertakan token CSRF (`<?= csrf_field() ?>`). Request POST AJAX wajib mengirimkan token CSRF di header atau datanya.
- **XSS Prevention:** Hindari pencetakan data mentah database langsung ke View. Selalu gunakan `esc($var)` untuk menjaga sanitasi output HTML.
- **Error Handling & API JSON:** Endpoint AJAX/API harus selalu mengembalikan format JSON yang valid (`return $this->response->setJSON(...)`) beserta status code HTTP yang sesuai jika terjadi error.
- **Refactoring & Modifikasi:** Sebelum mengubah alur data, selalu periksa parameter filter yang terkirim dari form pencarian (`bulan`, `tahun`, `unit_kerja`, `user_id`) agar data yang ditampilkan selalu sinkron dengan filter terpilih.
