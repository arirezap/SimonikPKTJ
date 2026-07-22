# 📌 Project Context: Simonik PKTJ (Sistem Informasi Monitoring Kinerja) & ECC

## 1. Project Overview
Aplikasi ini adalah **ECC (Evidence Command Center)**, sebuah sistem berbasis web untuk memonitoring kinerja pegawai, mengelola Rencana Kinerja, Log Kegiatan Harian, hingga Penilaian Kinerja. Aplikasi ini juga terintegrasi dengan **ECC (Evidence Command Center)** yang menampilkan dasbor analitik tingkat lanjut untuk level pimpinan.

## 2. Technology Stack
- **Backend Framework**: CodeIgniter 4 (PHP)
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript (Vanilla/jQuery)
- **CSS Framework**: Bootstrap 5
- **Data Visualization**: Chart.js (untuk Bar Chart, Doughnut Chart, Polar Area, Line Chart, Radar Chart)
- **Environment**: Berjalan di atas Laragon (Localhost) dengan OS Windows.

## 3. User Roles & Hierarchy
Sistem ini menggunakan *role-based access control* dengan hierarki sebagai berikut:
1. **Pimpinan/Manajemen**: `direktur`, `wadir`, `manajemen`, `spm`, `admin`
   - Memiliki hak akses ke **Dashboard Admin** (Command Center).
   - Dapat melihat analitik agregat seluruh instansi, grafik kinerja per unit, laporan tepat waktu/terlambat, dan fitur ECC.
2. **Kepala Bagian**: `kabag_aak`, `kabag_kuk`
   - Memiliki akses ke Dashboard Admin dengan limitasi tertentu.
3. **Pegawai / User Biasa**: `user`, `aak`, `kuk`
   - Hanya mengakses **Dashboard User**.
   - Fitur utama: Input Rencana Kinerja bulanan, input Log Kegiatan Harian, melihat evaluasi dari atasan, dan grafik kinerja personal.

## 4. UI/UX Design Guidelines (CRITICAL: "UI UX Pro Max")
Semua pengembangan antarmuka (UI) baru **WAJIB** mengikuti panduan gaya "UI UX Pro Max" yang berkelas premium, rapi, dan profesional:
- **Card Design**: Selalu gunakan `class="card border-0 shadow-sm rounded-4"`. Jangan gunakan border bawaan Bootstrap yang kaku.
- **Warna & Gradasi**: Gunakan palet warna yang modern (misal: aksen biru `#0d6efd`, hijau `#198754`). Boleh menggunakan gradien halus untuk *summary cards* pimpinan.
- **Tipografi**: Bersih dan terbaca jelas. Gunakan ketebalan font (`fw-bold`) untuk memberikan hierarki informasi.
- **White Space**: Berikan *padding/margin* yang lega agar elemen tidak saling berdempetan (gunakan kelas `p-4`, `mb-4`, dll).
- **Interaktivitas**: Grafik wajib memiliki fitur klik (terutama *drill-down* menggunakan Modal Bootstrap) dan *hover effects*. Jangan biarkan kursor standar jika elemen bisa diklik.

## 5. Key Features, Architecture & Workflow
- **Dashboard Command Center (Admin/Direktur)**:
  - Berada di `app/Controllers/Admin/Dashboard.php` dan `app/Views/admin/dashboard.php`.
  - Berisi metrik *Top Cards*, *Doughnut Chart* (Ketepatan waktu), *Polar Area Chart* (Distribusi Aspek Penilaian), *Line Chart* (Tren Tahunan), *Leaderboard* (Top 5 & Bottom 5), dan *Bar Chart* (Kinerja Per Unit).
  - Data diagregasi secara *real-time* dari tabel `log_kegiatan_harian`.
- **Dashboard ECC (Radar Chart)**: Menampilkan pencapaian indikator standar dengan bentuk jaring laba-laba.

### Alur Kinerja Staf (3 Modul Utama):
Aplikasi ini memiliki 3 modul yang saling berkesinambungan untuk menilai kinerja staf:
1. **Target Kinerja Bulanan** (Hulu): 
   - *Tabel*: `laporan_harian`. *Controller*: `LaporanHarianController`.
   - *Fungsi*: Staf menetapkan target bulanan (Sasaran Program, Indikator Kinerja, Target Kuantitas) untuk bulan dan tahun tertentu. Data ini menjadi *parent* untuk kegiatan harian.
2. **Lapor Kegiatan Harian** (Eksekusi): 
   - *Tabel*: `log_kegiatan_harian`. *Controller*: `LogKegiatanController`.
   - *Fungsi*: Staf mencatat aktivitas harian mereka secara spesifik. Setiap *log* wajib ditautkan ke target bulanan melalui `target_id` (berelasi dengan `laporan_harian.id`). Tabel ini juga mencatat `jumlah_capaian` harian.
3. **Rekap & Penilaian Kinerja** (Hilir/Evaluasi):
   - *Controller*: `PenilaianKinerjaController`.
   - *Fungsi*: Mengagregasi data dari `log_kegiatan_harian`. 
   - Bagi **Staf**, modul ini berfungsi untuk melihat rekapitulasi progres mereka.
   - Bagi **Atasan**, modul ini memunculkan antarmuka (form) untuk menilai kinerja bawahan/staf yang tergabung di timnya. Skor penilaian (Kualitas, Disiplin, Kerjasama) disimpan kembali ke dalam baris data di tabel `log_kegiatan_harian`.


## 6. Coding Standards & Agent Instructions
- **Routing**: Perhatikan `app/Config/Routes.php` dan penggunaan `AuthFilter` untuk menjaga keamanan *endpoint* berdasarkan peran (*role*).
- **Controller Logic & Form Handling**: Usahakan logika perhitungan berat diselesaikan di Controller atau menggunakan *Query Builder* di Model (bukan di dalam View). **WAJIB** menerapkan pola **PRG (Post-Redirect-Get)** pada setiap form filter atau form *submission* untuk menghindari peringatan *Confirm Form Resubmission* dan *403 CSRF Security Exception* bawaan CodeIgniter.
- **Database & Model**: Jika ada penambahan kolom pada tabel (misalnya kolom `no_hp` pada `users`), pastikan kolom tersebut juga didaftarkan pada `$allowedFields` di Model terkait (contoh: `app/Models/User.php`) agar data dapat tersimpan.
- **File Modifications**: Dilarang menghapus komentar/kode lama yang tidak terkait langsung dengan perbaikan. Prioritaskan perbaikan *bug* secara spesifik.
- **Chart.js**: Jika data label sumbu X terlalu panjang, gunakan format `indexAxis: 'y'` (Horizontal Bar Chart) agar rapi, dan gunakan tinggi wadah (*container*) yang dinamis.
