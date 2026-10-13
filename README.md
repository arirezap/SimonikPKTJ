# 🚀 Evidence Command Center (ECC)

<div align="center">
  <p><strong>Aplikasi berbasis web modern untuk memonitor, mengevaluasi, dan merekapitulasi kinerja institusi.</strong></p>
</div>

---

## 📌 Deskripsi Proyek
**Evidence Command Center (ECC)** (sebelumnya dikenal sebagai Simonik) adalah platform terintegrasi yang dirancang untuk mengelola rekam jejak dan evaluasi kinerja pegawai. Sistem ini memfasilitasi alur kerja birokrasi secara digital dan dilengkapi dengan ekosistem pelaporan yang dinamis. 

Dibangun dengan fokus pada kecepatan dan kenyamanan pengguna, sistem ini memastikan adopsi yang tinggi melalui antarmuka yang intuitif dan bersih.

---

## ✨ Fitur Utama

- **🔐 Arsitektur Multi-Role Pivot:** Memungkinkan sistem untuk menangani berbagai jenis peran pengguna secara dinamis dan berlapis tanpa konflik hak akses.
- **🛡️ Log Keamanan & Audit Trail Digital:** Perekaman otomatis terhadap setiap perubahan krusial di dalam sistem lengkap dengan IP Address, User Agent, riwayat data JSON, serta stempel waktu digital approval & penilaian kinerja di tingkat basis data.
- **👥 Hierarki Multi-Periode & Rotasi Jabatan:** Pencatatan penugasan temporal pegawai berbasis riwayat rotasi jabatan (`riwayat_atasan`) dengan penyelarasan otomatis penilai SKP tahunan dan lembar ekspor PDF resmi (PermenPAN-RB No. 6 Tahun 2022).
- **📊 Penilaian Kinerja Berjenjang:** Alur validasi dan *approval* otomatis dari atasan secara hierarkis untuk memastikan transparansi dan integritas data.
- **🏢 Manajemen Bukti (Evidence Management):** Modul pengumpulan dokumen dan target kinerja yang tersinkronisasi antar-divisi.
- **💼 Modul Pelaporan & Rekapitulasi:** Dasbor khusus untuk melacak tren fluktuasi kinerja dengan dukungan ekspor data (Excel Multi-Sheet & PDF A4) yang akurat.
- **🎨 UI/UX Modern:** Mengadopsi tata letak responsif berbasis 8-Point Grid System dan interaksi dinamis untuk pengalaman penggunaan yang mulus (*seamless*).

---

## 🛠️ Tech Stack
- **Framework Core:** CodeIgniter 4 (PHP 8.1+)
- **Database:** MySQL (25 Tabel Utama, Batch 46)
- **Frontend / UI:** HTML5, CSS 3, Bootstrap 5.3
- **Javascript:** jQuery, SweetAlert2, Flatpickr, Chart.js

---

## ⚙️ Panduan Instalasi (Development)

1. **Clone repositori ini:**
   ```bash
   git clone https://github.com/arirezap/SimonikPKTJ.git
   ```

2. **Atur Environment Variables:**
   Salin *file* `env` menjadi `.env` lalu sesuaikan kredensial koneksi server dan database Anda. Pastikan `CI_ENVIRONMENT` diset ke `development` atau `production` sesuai kebutuhan.

3. **Install Dependensi:**
   ```bash
   composer install
   ```

4. **Migrasi Database:**
   Jalankan perintah berikut untuk membangun struktur tabel pada database Anda:
   ```bash
   php spark migrate
   ```

5. **Jalankan Aplikasi:**
   Buka melalui *virtual host* di *local environment* Anda atau gunakan server bawaan CI4:
   ```bash
   php spark serve
   ```

---

## 📌 Versi Terbaru & Dokumentasi
- **Versi Rilis:** `v1.6` (13 Oktober 2026)
- **Ringkasan Perubahan:** Lihat [CHANGELOG.md](CHANGELOG.md), [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md), [audit_code.md](audit_code.md), dan [daftar_pekerjaan.md](daftar_pekerjaan.md) untuk detail audit 100% komprehensif seluruh modul sistem.

---

> **Dikelola secara internal sebagai solusi platform produktivitas dan monitoring kinerja.**
