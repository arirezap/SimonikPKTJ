# 📌 Project Context: Evidence Command Center (ECC)

## 1. Project Overview
Aplikasi ini adalah **Evidence Command Center (ECC)** (sebelumnya bernama Simonik), sebuah platform Enterprise berbasis web untuk memonitoring, mengevaluasi, dan merekapitulasi kinerja pegawai serta akreditasi institusi pada Politeknik Keselamatan Transportasi Jalan (PKTJ) Tegal. Aplikasi ini menyediakan dasbor analitik tingkat lanjut untuk level pimpinan, manajemen, kepegawaian, atasan penilai, hingga staf pelaksana mandiri.

---

## 2. Technology Stack & Environment
- **Backend Framework**: CodeIgniter 4 (PHP 8.1+)
- **Database**: MySQL 5.7 / 8.0 (24 Tabel Utama di `ekinerja_kinerja`)
- **Frontend**: HTML5, Vanilla CSS / CSS Custom Tokens, JavaScript (ES6 / jQuery 3.6+)
- **CSS Framework**: Bootstrap 5.3 + Bootstrap Icons (`bi bi-...`)
- **Data Visualization**: Chart.js (Bar, Horizontal Bar, Doughnut, Polar Area, Line, Radar Chart)
- **Document Generators**: PhpSpreadsheet (Workbook Excel Multi-Sheet) & Dompdf / html2pdf (PDF Standar Kedinasan A4 Landscape/Portrait)
- **Environment**: Laragon (Localhost `http://simonikpktj.test/`) & cPanel Production Server.

---

## 3. User Roles & Hierarchy Matrix
Sistem menggunakan *Role-Based Access Control (RBAC)* dengan 10 varian peran aktif:
1. **Superadmin (`admin`)**:
   - Memiliki kendali penuh ke seluruh modul sistem (`users`, `settings`, `audit-logs`, `master-data/*`, `remunerasi`, `monitoring`, `kepegawaian`).
   - Memiliki hak pembatalan persetujuan target (`cancelApprove`) dan izin revisi laporan harian (`bukaKunci`).
2. **Pimpinan Eksekutif (`direktur`, `wadir`)**:
   - Akses penuh ke Dashboard Command Center, Rekap Kepegawaian, Monitoring Kinerja Seluruh Unit, dan Modul Akreditasi ECC.
   - Auto-approve target mandiri (Direktur) dan evaluasi berjenjang untuk Wadir.
3. **Kepala Bagian & Struktural (`kabag`, `kabag_aak`, `kabag_kuk`, `manajemen`, `spm`)**:
   - Akses ke Dashboard Admin, Rekap Kepegawaian Institusi, Kelola Tim, dan Penilaian Kinerja Staf Bawahan.
   - Verifikasi bukti dan simulasi skor Akreditasi LED ECC.
4. **Tim Kepegawaian (`kepegawaian`)**:
   - Pengawasan menyeluruh modul Rekapitulasi Kinerja Kepegawaian (`/kepegawaian`), evaluasi ketercapaian RHK untuk hak remunerasi, dan ekspor berkas dinas (Excel Multi-Sheet & PDF Landscape).
5. **Pegawai Reguler / Staf Pelaksana (`user`)**:
   - Pengelolaan Target Kinerja Bulanan (`/laporan-harian`), Log Kegiatan Harian (`/log-kegiatan`), Kontrak Kinerja, Pakta Integritas, dan SKP.
6. **Pegawai Tugas Belajar (`tugas_belajar`)**:
   - Akses khusus pelaporan progres studi/tugas belajar mandiri.

---

## 4. Standar Terminologi & Branding ECC (CRITICAL RULES)
1. **Wajib Istilah "staf"**: Selalu gunakan kata baku **"staf"** (bukan "staf") di seluruh label antarmuka, pesan notifikasi, variabel kode (`$stafIdTerpilih`, `getAllStaf()`), dan dokumen teknis (kepatuhan mutlak `.agents/AGENTS.md`).
2. **Wajib Nama "Evidence Command Center (ECC)"**: Seluruh judul halaman, logo topbar, header laporan, footer sistem, dan respons asisten **WAJIB** menyebut **Evidence Command Center (ECC)** atau **ECC** (bukan "Simonik").

---

## 5. UI/UX & Interaction Design Standards ("UI UX Pro Max")
- **Bento Card Architecture**: Selalu gunakan kartu elevasi modern `class="card border-0 shadow-sm rounded-4"`.
- **Aksesibilitas Tinggi**: Wajib menyematkan atribut `aria-label` pada tombol ekspor, filter toolbar, dan input pencarian live.
- **Ergonomi Sentuh Seluler**: Target sentuh tombol minimal 44px, dukungan *iOS Zoom Prevention* (`font-size: 16px !important` pada input select di layar <768px), dan *touch segmented tabs*.
- **SweetAlert2 & Native Fallback**: Setiap tombol aksi dialog wajib memeriksa `typeof Swal !== 'undefined'` dan menyediakan fallback `confirm()` agar UI tetap berfungsi normal tanpa hambatan koneksi CDN.

---

## 6. Arsitektur Modul Utama & Workflow Kinerja

### A. Alur Kinerja Staf (3 Modul Hulu-Hilir):
1. **Target Kinerja Bulanan (Hulu - `/laporan-harian`)**:
   - Model: `TargetKinerjaBulanan` (Tabel: `target_kinerja_bulanan`).
   - Staf menyusun sasaran program, indikator kinerja, dan target bulanan kuantitatif (`DECIMAL(10,4)`).
   - Mendukung simpan draf sementara, simpan & kirim, auto-approve Direktur, serta persetujuan berjenjang atasan langsung (`approve` & `approveAll`).
2. **Lapor Kegiatan Harian (Eksekusi - `/log-kegiatan`)**:
   - Model: `LaporanHarian` (Tabel: `log_kegiatan_harian`) & `LogTugasTambahan` (Tabel: `log_tugas_tambahan`).
   - Pencatatan log kegiatan terikat dengan `target_id`. Dilengkapi pencatatan tugas tambahan institusi, tautan bukti digital, dan deteksi hari kerja/hari libur nasional (`is_working_day()`).
   - Dilengkapi fitur Izin Revisi (`bukaKunci`) oleh Atasan Langsung & Superadmin.
3. **Rekap & Penilaian Kinerja (Evaluasi - `/penilaian-kinerja`)**:
   - Controller: `PenilaianKinerjaController.php`.
   - Mengagregasi data capaian riil vs target. Atasan memberikan skor kualitas, disiplin, dan capaian pada rentang skala **0 - 150%** untuk diterbitkan (`status_penilaian = 'terbit'`).

### B. Modul Rekap Kinerja Kepegawaian (`/kepegawaian`):
- **Controller**: `app/Controllers/Kepegawaian/DashboardKepegawaian.php`.
- **Otorisasi Multi-Peran**: Terbuka untuk `['kepegawaian', 'admin', 'direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'spm']`.
- **Default Periode Bulan**: Menu awal yang terbuka selalu otomatis memuat **Bulan Sekarang (`date('n')`)**. Pengguna dapat memilih bulan lampau (misal: *Agustus*) atau *Sepanjang Tahun* (`'all'`) melalui filter dropdown.
- **Ultra-Fast 2-Query Batch Fetching**: Memuat ratusan data target RHK dan tugas tambahan institusi dalam 2 query SQL terindeks tanpa beban N+1 query loop.
- **Hierarki Jabatan 13-Tier**: Pengurutan otomatis dari Direktur, Wadir, Kabag, Kapus, Kanit, Kaprodi, Dosen, hingga Staf Pelaksana dan Tugas Belajar.
- **Ekspor Dokumen**: Excel Multi-Sheet numerik murni & PDF Landscape berstandar dinas resmi.

### C. Log Keamanan Aktivitas (Audit Trail - `/admin/audit-logs`):
- Helper: `app/Helpers/audit_helper.php` (`log_audit()`).
- Mencatat mutasi data krusial (`CREATE`, `UPDATE`, `DELETE`, `LOGIN`, `LOGOUT`, `APPROVE`, `UNLOCK_LAPORAN`, `CANCEL_APPROVE_TARGET`, `EXPORT_EXCEL_KEPEGAWAIAN`, `EXPORT_PDF_KEPEGAWAIAN`) lengkap dengan rekaman JSON *before-after*, IP address, dan User Agent.

### D. Mode Pemeliharaan Mandiri (Maintenance Mode - `/settings`):
- Filter: `app/Filters/MaintenanceFilter.php`.
- Saklar switch 1-klik di menu Pengaturan Sistem mengalihkan seluruh pengguna non-admin ke halaman `public/maintenance.html` (HTTP 503) dengan auto-refresh 30 detik. Administrator tetap memiliki akses 100%.

---

## 7. Arsitektur Perutean (Routing Rules - `app/Config/Routes.php`)
- **Auto-Routing Dimatikan**: `$routes->setAutoRoute(false);` wajib aktif demi keamanan endpoint.
- **117 Rute Terdaftar Eksplisit**: Seluruh 117 rute sistem terverifikasi 100% memetakan ke Controller dan Method aktif.
- **Filter Guard `auth`**: Seluruh rute internal dibungkus dalam grup `['filter' => 'auth']`.
- **Rute Delete Konsisten**: Seluruh endpoint hapus master data menggunakan `$routes->match(['get', 'post'], ...)` untuk mendukung form CSRF dialog modal SweetAlert2.

---

## 8. Sinkronisasi Database (Local vs cPanel Production)
- Database: `ekinerja_kinerja` (24 Tabel Basis Data).
- Presisi Tinggi: `target_bulanan` dan `jumlah_capaian` menggunakan `DECIMAL(10,4)`, `nilai_capaian` menggunakan `DECIMAL(5,2)`.
- Status Paritas: Hasil audit forensik pada dump `ekinerja_kinerja (2).sql` membuktikan bahwa database cPanel sudah **100% SINKRON** dengan database lokal (0 missing columns, identical data types & settings keys). **Tidak diperlukan query SQL manual setelah push.**

---

## 9. Status Graphify Knowledge Graph
- Korpus Terindeks: 662 berkas (~1.119.885 kata).
- Simpul & Relasi: 7.334 nodes, 17.633 edges, 393 communities.
- Berkas Artefak: `graphify-out/graph.json`, `graphify-out/graph.html`, dan `graphify-out/GRAPH_REPORT.md`.
