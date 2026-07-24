# 🚀 Simonik PKTJ (Sistem Monitoring Kinerja)

<div align="center">
  <p><strong>Aplikasi berbasis web modern untuk memonitor, mengevaluasi, dan merekapitulasi kinerja institusi.</strong></p>
</div>

---

## 📌 Deskripsi Proyek
**Simonik PKTJ** adalah platform terintegrasi yang dirancang khusus untuk mengelola rekam jejak kinerja pegawai di lingkungan **Politeknik Keselamatan Transportasi Jalan (PKTJ)**. Sistem ini memfasilitasi alur kerja birokrasi berjenjang secara digital (dari Staf hingga Direktur) dan dilengkapi dengan ekosistem **Evidence Command Center (ECC)** untuk otomasi pemenuhan dokumen akreditasi institusi.

Dibangun dengan filosofi desain **UI/UX Pro Max (Bento Grid)**, sistem ini memastikan adopsi pengguna yang tinggi melalui antarmuka yang intuitif, bersih, dan memanjakan mata.

---

## ✨ Fitur Utama

### 1. 🔐 Arsitektur Multi-Role Pivot
Berbeda dengan sistem tradisional, Simonik menggunakan arsitektur peran ganda (*Multi-role Pivot Table*). Seorang pengguna memiliki **Peran Struktural** (Staf, Manajemen, dll.) yang menentukan alur *approval* hierarkisnya, namun di saat bersamaan bisa disematkan **Peran Fungsional** (Kepegawaian, SPM) tanpa saling tindih (konflik hak akses).

### 2. 📊 Sistem Penilaian Kinerja Berjenjang
Pencatatan Rencana Hasil Kerja (RHK) bulanan dan log harian terhubung langsung ke atasan. Sistem mengunci RHK yang sudah berstatus 'Terkirim' agar tidak dapat dimanipulasi, memaksa interaksi validasi *real-time* dari pimpinan unit.

### 3. 🏢 ECC (Evidence Command Center) Cerdas
* Fitur Laporan Evaluasi Diri (LED) yang langsung memfilter tugas dan indikator berdasarkan asal Unit Kerja pengguna (misal: Subbagian AAK atau KUK).
* Menyediakan hak akses eksklusif fitur **Simulasi Penilaian Institusi** yang hanya dapat disentuh oleh **Satuan Penjaminan Mutu (SPM)**.

### 4. 💼 Dashboard Rekap Kepegawaian (12-Month Matrix)
Ruang kontrol khusus bagi divisi SDM (Kepegawaian) untuk melacak tren fluktuasi nilai rata-rata tiap pegawai selama 1 tahun penuh. Dilengkapi fitur **Export Excel** presisi yang mengunci *formatting* NIP agar tidak berubah menjadi angka matematis (notasi ilmiah) pada Microsoft Excel.

### 5. 🎨 UI/UX Pro Max & Kinerja Ekstrem
Mengadopsi tata letak modern **Bento Grid**, sistem dibekali dengan **Skeleton Loading** dinamis pada setiap transisi *filter* data (menghilangkan layar putih *loading* jadul), serta umpan balik visual interaktif dengan *SweetAlert2*.

---

## 🛠️ Tech Stack & Ekosistem
- **Framework Core:** CodeIgniter 4 (PHP 8.1+)
- **Database:** MySQL (Relational & Pivot mapping)
- **Frontend / UI:** HTML5, Vanilla CSS 3 (Bento Styles), Bootstrap 5.3
- **Javascript:** jQuery, SweetAlert2
- **Arsitektur Development:** Dibantu dan direkayasa bersama AI (Google DeepMind - Antigravity Agent)

---

## ⚙️ Panduan Instalasi (Development)

1. **Clone repositori ini:**
   ```bash
   git clone https://github.com/arirezap/SimonikPKTJ.git
   ```

2. **Atur Environment Variables:**
   Salin `env` menjadi `.env` lalu sesuaikan kredensial server dan database Anda.
   ```env
   CI_ENVIRONMENT = development
   app.baseURL = 'http://simonikpktj.test/'
   
   database.default.hostname = localhost
   database.default.database = simonik_db
   database.default.username = root
   database.default.password = 
   ```

3. **Install Dependensi:**
   ```bash
   composer install
   ```

4. **Migrasi Database:**
   Pastikan tabel terbentuk sempurna dengan menjalankan skrip *migration*.
   ```bash
   php spark migrate
   ```

5. **Jalankan Aplikasi:**
   Buka lewat *virtual host* di browser Anda atau gunakan server bawaan CI4:
   ```bash
   php spark serve
   ```

---

## 🔒 Matriks Hak Akses (Role Access Matrix)

Sistem dirancang tertutup dengan hierarki ketat:
* **`admin`**: Dewa (God-mode). Mengatur pengguna, master data RHK institusi, dan unit kerja.
* **`direktur` / `wadir`**: *Top management view*. Menyetujui dan menilai kinerja manajer/kabag di bawahnya.
* **`manajemen`**: Pimpinan unit (Kanit/Kabag). Mengelola staf di divisinya.
* **`user`**: Pegawai reguler/Staf (hanya bisa melaporkan aktivitas).
* **`kepegawaian`**: Akses sisipan untuk memantau nilai gaji/remunerasi lintas divisi.
* **`spm`**: Akses sisipan untuk mengeksekusi penilaian final akreditasi kampus.

---

> **Dikelola dengan ❤️ untuk Politeknik Keselamatan Transportasi Jalan (PKTJ)**
