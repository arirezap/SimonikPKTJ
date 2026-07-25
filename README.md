# 🚀 Simonik (Sistem Monitoring Kinerja)

<div align="center">
  <p><strong>Aplikasi berbasis web modern untuk memonitor, mengevaluasi, dan merekapitulasi kinerja institusi.</strong></p>
</div>

---

## 📌 Deskripsi Proyek
**Simonik** adalah platform terintegrasi yang dirancang untuk mengelola rekam jejak dan evaluasi kinerja pegawai. Sistem ini memfasilitasi alur kerja birokrasi secara digital dan dilengkapi dengan ekosistem pelaporan yang dinamis. 

Dibangun dengan fokus pada kecepatan dan kenyamanan pengguna, sistem ini memastikan adopsi yang tinggi melalui antarmuka yang intuitif dan bersih.

---

## ✨ Fitur Utama

- **🔐 Arsitektur Multi-Role Pivot:** Memungkinkan sistem untuk menangani berbagai jenis peran pengguna secara dinamis dan berlapis tanpa konflik hak akses.
- **🛡️ Log Keamanan (Audit Trail):** Perekaman otomatis (*invisible*) terhadap setiap perubahan krusial di dalam sistem (Login, Kelola Pengguna, Penilaian, Master Data) lengkap dengan *IP Address*, *User Agent*, dan riwayat data JSON untuk menjamin integritas.
- **📊 Penilaian Kinerja Berjenjang:** Alur validasi dan *approval* otomatis dari atasan secara hierarkis untuk memastikan transparansi dan integritas data.
- **🏢 Manajemen Bukti (Evidence Management):** Modul pengumpulan dokumen dan target kinerja yang tersinkronisasi antar-divisi.
- **💼 Modul Pelaporan & Rekapitulasi:** Dasbor khusus untuk melacak tren fluktuasi kinerja dengan dukungan ekspor data (Excel/CSV) yang akurat.
- **🎨 UI/UX Modern:** Mengadopsi tata letak responsif, sistem dibekali dengan interaksi dinamis untuk pengalaman penggunaan yang mulus (*seamless*).

---

## 🛠️ Tech Stack
- **Framework Core:** CodeIgniter 4 (PHP 8.1+)
- **Database:** MySQL
- **Frontend / UI:** HTML5, CSS 3, Bootstrap 5.3
- **Javascript:** jQuery, SweetAlert2

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

> **Dikelola secara internal sebagai solusi platform produktivitas dan monitoring kinerja.**
