# Product Requirements Document (PRD)
## Evidence Command Center (ECC)

**Status:** Active Development
**Stack:** CodeIgniter 4, MySQL, Bootstrap 5, jQuery

### 1. Ringkasan Eksekutif (Executive Summary)
Simonik PKTJ adalah sistem informasi internal yang didesain untuk memantau, merekapitulasi, dan menilai kinerja pegawai secara komprehensif. Sistem ini bertindak sebagai **Evidence Command Center (ECC)**, di mana seluruh bukti pekerjaan, pencapaian target, dan rekam jejak aktivitas harian staf bermuara, dievaluasi, dan divisualisasikan dalam bentuk *dashboard* analitik.

### 2. Tujuan Produk (Product Goals)
- **Sentralisasi Data Kinerja:** Menggantikan pencatatan manual dengan sistem pencatatan harian berbasis *cloud/server* yang disertai tautan bukti (*evidence-based*).
- **Transparansi & Akuntabilitas:** Memberikan visibilitas langsung antara target yang dibebankan (Target Bulanan / RHK) dengan realisasi aktual yang dikerjakan pegawai.
- **Evaluasi Objektif:** Memfasilitasi atasan dalam memberikan penilaian yang akurat dengan mengacu langsung pada bukti pekerjaan tanpa perlu *micromanagement*.
- **Visualisasi Data Eksekutif:** Menyediakan *dashboard* berkinerja tinggi untuk memonitor metrik-metrik inti (Top Unit Kerja, Top Pegawai, dan Pegawai yang butuh perhatian).

### 3. Modul Utama & Fitur (Core Modules & Features)

#### 3.1. Dashboard Eksekutif (Admin/Pimpinan)
- **Top 5 Unit Kerja:** *Leaderboard* unit kerja dengan rata-rata capaian tertinggi.
- **Top 5 Pegawai Berkinerja Terbaik:** *Leaderboard* individu yang paling banyak merealisasikan target bulanannya.
- **Perlu Perhatian Khusus (Bottom 5):** Daftar pegawai dengan nilai capaian terendah sebagai bahan evaluasi dan *coaching* pimpinan.
- *UI/UX Pattern:* Menggunakan sistem **Bento Grid** untuk menata widget metrik dengan batas tinggi dan lebar yang seragam agar terlihat simetris.

##### 3.2. Modul Laporan Harian & Log Kegiatan (Daily Activity)
- **Pencatatan Aktivitas:** Pegawai wajib mencatat aktivitas harian, jumlah realisasi (`jumlah_capaian` minimal `0`), dan menyertakan tautan bukti pendukung (*link Google Drive / Dokumen*). Dukungan pecahan desimal menggunakan koma (`,`) dikonversi secara otomatis.
- **Pemetaan Indikator:** Setiap aktivitas harian wajib dikaitkan dengan satu Indikator Kinerja / Rencana Hasil Kerja (RHK) utama.
- **Tugas Tambahan & Penanganan Draf:** Mendukung pencatatan dan penghapusan tugas tambahan secara dinamis via AJAX dengan sinkronisasi ID otomatis (`allTambahanIds`) dan token CSRF dinamis.

#### 3.3. Modul Penilaian Kinerja (Evidence Command Center)
Modul ini merupakan *core engine* untuk evaluasi bulanan yang terdiri dari dua sub-sistem (tab):
- **Tab Target Bulanan Saya (Individu):** 
  - Tempat pegawai melihat rekapitulasi realisasi bulanannya berdasarkan laporan harian.
  - Menampilkan metrik Target vs Realisasi beserta Selisih (Gap) dan Nilai Capaian.
- **Tab Penilaian Staf (Atasan):**
  - *Dropdown* pencarian cerdas (menggunakan Select2) untuk memilih staf.
  - Atasan dapat memvalidasi langsung laporan harian staf dengan mengklik *link* bukti.
  - Atasan memberikan "Predikat" capaian, yang secara otomatis membatasi (min-max) skor penilaian untuk menghindari *human error*.
  - Dilengkapi fitur perhitungan nilai *real-time* (Javascript) dan perlindungan data tidak disengaja terhapus menggunakan konfirmasi SweetAlert2.

#### 3.4. Keamanan, Log & Deployment Safety
- **CCTV Internal Sistem (Audit Trail):** Merekam jejak setiap aktivitas pengguna secara *invisible* (latar belakang) di seluruh titik krusial aplikasi.
- **Data Tersimpan:** Mencatat aksi (`CREATE`, `UPDATE`, `DELETE`, `LOGIN`, dll), aktor (Pengguna), entitas yang diubah, IP Address, sistem operasi/browser (User Agent), serta merekam status data *sebelum* dan *sesudah* perubahan dalam format JSON.
- **Resiliensi Database:** Menggunakan blok `try...catch` di setiap operasi penyimpanan massal untuk mencegah Error 500 dan menjaga aplikasi tetap responsif.
- **Pendaftaran Rute Eksplisit:** Seluruh endpoint AJAX terdaftar pada `Routes.php` dalam grup filter otentikasi.
- **Mekanisme Cache-Control:** Dilengkapi HTTP Cache-Control header di layout utama agar pengguna langsung mendapatkan update terbaru pasca deployment.

#### 3.5. Modul Master Data & Relasi Unit Kerja Normalisasi
- **Normalisasi Relasi (`users.unit_id`):** Tabel `users` dinormalkan dengan relasi foreign key `unit_id` ke tabel `unit_kerja` dengan mempertahankan sinkronisasi otomatis kolom teks `unit` (*Dual-Sync*).
- **Cascading Update:** Pembaruan nama unit pada Master Data otomatis memperbarui seluruh profil pegawai terdaftar.
#### 3.6. Modul Pengaturan Sistem Terpusat & Mode Pemeliharaan (Maintenance Mode)
- **Mode Pemeliharaan Mandiri (Web-Based Maintenance Mode):** Administrator dapat mengaktifkan mode pemeliharaan dengan 1 klik melalui menu `/settings` tanpa perlu mengedit file `.htaccess` atau membuka cPanel.
- **Perilaku Sistem:** Pengguna non-admin dialihkan ke `public/maintenance.html` (Bento layout, auto-refresh 30s, status 503), sedangkan Administrator tetap memiliki akses 100% dengan banner indikator peringatan di header.
- **Kebijakan Batas Waktu Kinerja:** Mengatur batas input target bulanan, batas penilaian kinerja oleh atasan, batas kunci laporan bulan lalu (End-of-Month Cutoff), dan toleransi hari laporan harian.

#### 3.7. Presisi Desimal Tinggi & Skala Penilaian 0 - 150%
- **Presisi Desimal 4 Digit (`DECIMAL(10,4)`):** Mendukung perhitungan target dan realisasi pecahan halus (contoh: `0.3333`, `0.1250`) tanpa pemotongan atau pembulatan paksa.
- **Standardisasi Skala Tugas Tambahan:** Skala penilaian tugas tambahan diselaraskan menjadi `0 - 150%` setara dengan Target RHK bulanan.

### 4. Pedoman Desain (UI/UX Guidelines)
Aplikasi ini dikembangkan dengan berpegang teguh pada prinsip **ui-ux-pro-max**:
- **Pendekatan Bento / Card:** Semua tabel, *form*, dan *widget* dibungkus ke dalam *card* membulat (`rounded`, `shadow-sm`) dengan latar belakang putih.
- **Tipografi Bersih & Hierarki Tegas:** Penggunaan warna yang bermakna (`text-primary`, `text-success` untuk hal positif, `text-danger` untuk perhatian khusus/peringatan) tanpa menggunakan kotak warna *solid* berukuran raksasa yang menyakiti mata.
- **Interaksi Mikro & Halus:** Penghindaran *popup browser default* (menggunakan SweetAlert2), penambahan transisi saat memuat *tab*, *smart scroll topbar*, dan tombol aksi berbasis ikon bulat alih-alih tombol teks yang panjang.
- **Konsistensi Arsitektur Informasi:** Penempatan indikator akhir (seperti "Nilai Kinerja") selalu di posisi pamungkas alur baca, yakni di *footer* kanan bawah tabel.

### 5. Arsitektur Keamanan & Multi-Role
Sistem menerapkan konsep pemisahan tugas menggunakan arsitektur **Tabel Pivot (Multi-Role)**:
- **Role Primer (Struktural):** Menentukan posisi jabatan dan hak akses dasar (misal: `user`, `manajemen`, `direktur`, `admin`). Role primer mendasari fungsi pencatatan kinerja dan validasi atasan-bawahan.
- **Role Sekunder (Fungsional):** Peran tambahan yang bisa disematkan lebih dari satu ke pegawai tanpa menghapus peran strukturalnya.
  - **Kepegawaian:** Akses *read-only* ke rekapitulasi nilai kinerja semua unit (untuk remunerasi) beserta fitur export Excel.
  - **SPM (Satuan Penjaminan Mutu):** Akses khusus untuk mengendalikan **Simulasi Penilaian LED** di modul ECC.
  - *Extensibility:* Sistem siap menampung peran masa depan seperti Auditor atau Reviewer dokumen khusus.

### 6. Rencana Masa Depan (Future Roadmap)
- [x] Implementasi arsitektur Multi-Role (Tabel Pivot).
- [x] Modul Kepegawaian (Rekap Nilai Remunerasi) & Export Excel.
- [x] Log Aktivitas Sistem (Audit Trail) dengan Live Search, Date Range Filter, & JSON Viewer Modal.
- [x] Normalisasi Relasi Unit Kerja (`users.unit_id`) & Cascading Sync.
- [x] Smart Indexing & Optimasi Database v1.4.
- [x] Refactoring & Optimalisasi Modul Laporan Kinerja & Log Kegiatan Harian.
- [x] Modul Evidence Command Center (ECC LED & Simulasi Penilaian).
- [x] Modul Notifikasi Auto-Sync Hari Libur Nasional API & History Retention.
- [x] Sistem Layout Utama, Pembatasan Role Sidebar, & Dynamic CSRF Protection.
- [x] Mekanisme Automatic Anti-Cache Busting v1.4 (`style.css?v=1.4.TIMESTAMP`).
- [x] Grafik Historis (Chart.js) pada *dashboard* user untuk melihat tren kinerja pribadi selama setahun.
- [x] Rilis Resmi Versi 1.3: Penyelesaian Audit 8-Pilar Menyeluruh Seluruh Modul Sistem (31 Agustus 2026).
- [x] Fitur Mode Pemeliharaan Mandiri Administrator (Web-Based Maintenance Mode) via `/settings`.
- [x] Presisi Desimal 4 Digit (`DECIMAL(10,4)`) & Standardisasi Penilaian Tugas Tambahan 0 - 150%.
- [x] Engine Ekspor Rekap Kepegawaian Multi-Sheet Excel & PDF Landscape A4 dengan Pengurutan Hierarki 13-Tier Jabatan.
- [x] Rilis Resmi Versi 1.4: Penyelesaian Audit 8-Pilar Modul Kelola Tim, Kelola Pengguna, Profil Saya, dan Pengerasan Rute POST (3 September 2026).

