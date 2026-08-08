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
   - *Refactoring & Validasi*: Menggunakan *single loop* untuk validasi dan penyiapan data, konversi otomatis koma (`,`) ke titik (`.`) untuk angka desimal, dan pengabaian baris kosong secara cerdas.
2. **Lapor Kegiatan Harian** (Eksekusi): 
   - *Tabel*: `log_kegiatan_harian` & `log_tugas_tambahan`. *Controller*: `LogKegiatanController`.
   - *Fungsi*: Staf mencatat aktivitas harian mereka secara spesifik. Setiap *log* wajib ditautkan ke target bulanan melalui `target_id` (berelasi dengan `laporan_harian.id`). Tabel ini juga mencatat `jumlah_capaian` harian.
   - *Aturan Ketat*: Kolom `jumlah_capaian` wajib diisi angka (minimal `0`). Fitur hapus tugas tambahan disinkronkan via AJAX dengan penanganan CSRF token dinamis dan pelacakan ID (`$allTambahanIds`).
3. **Rekap & Penilaian Kinerja** (Hilir/Evaluasi):
   - *Controller*: `PenilaianKinerjaController`.
   - *Fungsi*: Mengagregasi data dari `log_kegiatan_harian`. 
   - Bagi **Staf**, modul ini berfungsi untuk melihat rekapitulasi progres mereka.
   - Bagi **Atasan**, modul ini memunculkan antarmuka (form) untuk menilai kinerja staf/staf yang tergabung di timnya. Skor penilaian (Kualitas, Disiplin, Kerjasama) disimpan kembali ke dalam baris data di tabel `log_kegiatan_harian`.
4. **Log Keamanan (Audit Trail)** (Background Service):
   - Menggunakan tabel `audit_logs` dan `app/Helpers/audit_helper.php`.
   - Merekam seluruh aktivitas `CREATE`, `UPDATE`, `DELETE`, `LOGIN/LOGOUT`, dan `APPROVE` dari seluruh modul secara otomatis dan *non-blocking*. Mampu menangkap data *before-after* dalam bentuk JSON.


## 6. Coding Standards & Agent Instructions
- **Routing & Filter**: Seluruh *endpoint* AJAX dan form submission **WAJIB** terdaftar secara eksplisit di `app/Config/Routes.php` dalam grup filter otentikasi `auth`. Jangan pernah berasumsi auto-routing berjalan di server *live cPanel*.
- **Controller Logic & Form Handling**: Usahakan logika perhitungan berat diselesaikan di Controller atau menggunakan *Query Builder* di Model. **WAJIB** menerapkan pola **PRG (Post-Redirect-Get)** pada setiap form filter atau form *submission* non-AJAX. Untuk request AJAX, selalu kembalikan respons JSON lengkap dengan `csrf_hash`.
- **Database & Try-Catch Safety**: Semua eksekusi `insert()`, `update()`, dan `updateBatch()` pada *controller* wajib dibungkus dalam blok `try...catch (\Exception $e)` untuk mencegah munculnya halaman Error 500 (*white screen*) di server *live*.
- **Handling Desimal & Sanitasi**: Nilai angka bertipe desimal harus selalu melalui sanitasi `str_replace(',', '.', trim((string)$val))` sebelum dimasukkan ke basis data agar mendukung input berbasis bahasa Indonesia (koma).
- **Cache Control Deployment**: Halaman utama dilengkapi dengan meta tag HTTP `Cache-Control` (`no-cache, no-store, must-revalidate`) serta cache-busting `?v=filemtime(...)` pada file CSS/JS untuk memastikan pengguna mendapatkan versi aplikasi terbaru tanpa perlu *clear cache* manual.
- **Database & Model**: Jika ada penambahan kolom pada tabel, pastikan kolom tersebut juga didaftarkan pada `$allowedFields` di Model terkait agar data dapat tersimpan.
- **File Modifications**: Dilarang menghapus komentar/kode lama yang tidak terkait langsung dengan perbaikan. Prioritaskan perbaikan *bug* secara spesifik.
- **Chart.js**: Jika data label sumbu X terlalu panjang, gunakan format `indexAxis: 'y'` (Horizontal Bar Chart) agar rapi, dan gunakan tinggi wadah (*container*) yang dinamis.
