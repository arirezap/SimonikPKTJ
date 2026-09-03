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
   - **Direktur (`direktur`)**: Pimpinan tertinggi dengan akses penuh ke Dashboard Command Center, Rekap Kepegawaian, auto-approve target mandiri (dapat merevisi mandiri kapan saja), serta hak persetujuan target dan penilaian kinerja staf institusi.
   - **Wakil Direktur (`wadir`)**: Pimpinan Eksekutif Pengawas/Monitoring dengan akses ke Dashboard Command Center, Rekap Kepegawaian, Monitoring Kinerja, dan Instrumen Akreditasi ECC. **Wadir secara eksplisit tidak memiliki akses/wewenang untuk merevisi target staf, menyetujui target staf, ataupun memberikan penilaian kinerja kepada staf** (hanya mengelola target dan capaian personal "Target Saya").
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
1. **Wajib Istilah "staf"**: Selalu gunakan kata baku **"staf"** (bukan "bawahan" atau "staff") di seluruh label antarmuka, pesan notifikasi, variabel kode (`$stafIdTerpilih`, `getAllStaf()`), dan dokumen teknis (kepatuhan mutlak `.agents/AGENTS.md`).
2. **Wajib Nama "Evidence Command Center (ECC)"**: Seluruh judul halaman, logo topbar, header laporan, footer sistem, dan respons asisten **WAJIB** menyebut **Evidence Command Center (ECC)** atau **ECC** (bukan "Simonik").

---

## 5. UI/UX & Interaction Design Standards ("UI UX Pro Max & 8-Point Grid")
- **8-Point Grid System**: Seluruh spacing (`padding`, `margin`, `gap`), ukuran elemen, dan aset grafis wajib mematuhi kelipatan 8px: `4px` (0.5x micro), `8px` (1x base), `12px` (1.5x), `16px` (2x), `24px` (3x), `32px` (4x), `40px` (5x), `48px` (6x), `64px` (8x), `80px` (10x). Dilarang keras menggunakan angka sembarang tak sejajar grid (`7px`, `13px`, `62px`, `95px`).
- **Bento Card Architecture**: Selalu gunakan kartu elevasi modern `class="card border-0 shadow-sm rounded-4"` dengan `padding: 24px` (desktop) / `16px` (mobile).
- **Standar Ukuran Aset & Ikon**: Swatch legenda `16px × 16px`, tombol compact `height: 32px`, kontrol form `height: 36px`–`40px`, tombol CTA utama `min-height: 40px`, ikon header modal `40px × 40px`, avatar `40px`/`64px`/`80px`, sel kalender desktop `min-height: 64px` (mobile `48px`).
- **Format Numerik Tabular**: Seluruh angka desimal, target, realisasi, dan skor wajib menggunakan `font-variant-numeric: tabular-nums; font-feature-settings: "tnum";`.
- **Aksesibilitas Tinggi**: Wajib menyematkan atribut `aria-label` pada tombol ekspor, filter toolbar, dan input pencarian live.
- **Ergonomi Sentuh Seluler**: Target sentuh tombol minimal 40px–44px, dukungan *iOS Zoom Prevention* (`font-size: 16px !important` pada input select di layar <768px), dan *touch segmented tabs*.
- **SweetAlert2 & Native Fallback**: Setiap tombol aksi dialog wajib memeriksa `typeof Swal !== 'undefined'` dan menyediakan fallback `confirm()` agar UI tetap berfungsi normal tanpa hambatan koneksi CDN.

---

## 6. Arsitektur Modul Utama & Workflow Kinerja

### A. Alur Kinerja Staf (3 Modul Hulu-Hilir):
1. **Target Kinerja Bulanan (Hulu - `/laporan-harian`)**:
   - Model: `TargetKinerjaBulanan` (Tabel: `target_kinerja_bulanan`).
   - Staf menyusun sasaran program, indikator kinerja, dan target bulanan kuantitatif (`DECIMAL(10,4)`).
   - **Fleksibilitas Pengeditan Sebelum Disetujui**: Staf bebas mengubah angka target, mengedit teks, menambah baris, maupun menghapus target selagi status belum `disetujui` oleh atasan langsung (saat masih berstatus `menunggu_persetujuan`).
   - **Tombol Aksi Kontekstual**: Tombol submit beradaptasi dinamis: berlabel *"Ajukan Target"* jika status draf/baru, dan otomatis berubah menjadi *"Perbarui & Ajukan Ulang"* jika target sudah pernah diajukan sebelumnya.
   - **Notifikasi Bertahap ke Atasan**: Membedakan pengajuan baru (*"Persetujuan Target Bulanan"*) vs pembaruan (*"Pembaruan Target Bulanan"*).
   - **Defensive Engineering & Fault-Tolerant**: Inisialisasi eksplisit `$targetUser` di awal `store()` dan penanganan exception pada pengiriman notifikasi (`try...catch`) untuk menjamin zero 500 error di PHP 8.1+ produksi cPanel.
   - Mendukung simpan draf sementara, simpan & kirim, auto-approve Direktur (dapat diedit/direvisi mandiri kapan saja), serta persetujuan berjenjang atasan langsung (`approve` & `approveAll`). Penguncian permanen hanya aktif setelah disetujui atasan.
2. **Lapor Kegiatan Harian (Eksekusi - `/log-kegiatan`)**:
   - Model: `LaporanHarian` (Tabel: `log_kegiatan_harian`) & `LogTugasTambahan` (Tabel: `log_tugas_tambahan`).
   - Pencatatan log kegiatan terikat dengan `target_id`. Dilengkapi pencatatan tugas tambahan institusi, tautan bukti digital, dan deteksi hari kerja/hari libur nasional (`is_working_day()`).
   - **Datepicker Flatpickr Terintegrasi**: Tanggal merah/akhir pekan masa depan (`.flatpickr-disabled`) berpenampilan redup pudar (`#fca5a5`, opacity 0.35), sedangkan tanggal yang sudah tiba/aktif berpenampilan merah cerah tegas (`#ef4444`, font-weight 700, opacity 1).
   - Dilengkapi fitur Izin Revisi (`bukaKunci`) oleh Atasan Langsung & Superadmin.
3. **Rekap & Penilaian Kinerja (Evaluasi - `/penilaian-kinerja`)**:
   - Controller: `PenilaianKinerjaController.php`.
   - **Kalender Heatmap Matriks Aktivitas**:
     - 100% bebas emoji, matriks 7 kolom (Senin–Minggu).
     - Dimensi sel: Desktop `min-height: 64px`, `border-radius: 8px`, `padding: 8px 10px`; Mobile `min-height: 48px`, `border-radius: 6px`.
     - Footer Keterangan: Bento Capsule Bar (`height: 32px`), swatches `16px × 16px` (`border-radius: 4px`), Pill interaktif callout `height: 32px`.
   - **Pop-up Modal Rincian Pekerjaan (`#modalDetailLogTanggal`)**:
     - Header icon `40px × 40px`, tombol navigasi tanggal (`<` `>`) `width: 32px; height: 32px;`, banner info tanggal bersih tanpa label redundan "Tanggal Terpilih" dan "Hari Reguler" (badge cerdas hanya muncul untuk Libur Nasional dan Akhir Pekan), tabel `.table-bento` `padding: 12px 16px` dengan batas `max-height: 440px`.
   - **Keamanan & Ownership Validation**:
     - Penutupan celah IDOR kepemilikan tugas tambahan pada `store()`.
   - **Prasyarat Penilaian**: Atasan Langsung HANYA DAPAT memberi nilai jika seluruh target kinerja bulanan staf pada periode terkait sudah berstatus `disetujui`.
   - **Standar Predikat Kinerja**: Sangat Baik (`>100% - 150%`), Baik (`>90% - 100%`), Butuh Perbaikan (`>75% - 90%`), Kurang (`>25% - 75%`), Sangat Kurang (`<=25%`), Belum Dinilai (`0%` atau RHK dinilai = 0 / NULL).
    - **Mekanisme Reset**: Tombol Reset Nilai mengosongkan nilai (`nilai_capaian = NULL`) dan menyetel flag `status_penilaian = NULL` di database (kembali ke status "Belum Dinilai"), serta mencatat audit log `RESET_PENILAIAN_KINERJA`.
4. **Kelola Tim Saya (Manajemen Tim - `/tim`)**:
   - Controller: `app/Controllers/User/TimController.php` | View: `app/Views/user/tim_saya.php`.
   - Otorisasi pimpinan & manajerial: `['manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'admin']`.
   - Penambahan staf dengan pencarian multi-field Select2 (Nama, NIP, Jabatan, Unit) dan pencegahan menambahkan akun admin/direktur/diri sendiri.
   - Proteksi IDOR ketat pada saat mengeluarkan staf atau mengubah unit kerja staf secara real-time via AJAX dengan token CSRF dinamis.

### B. Modul Kepegawaian (`/kepegawaian`):
Menu tree Kepegawaian di sidebar memiliki 2 submodul terpadu untuk Tim Kepegawaian, Pimpinan, dan Manajemen:
1. **Monitoring Target Kinerja Bulanan (`/kepegawaian/target-kinerja`)**:
   - Controller: `app/Controllers/Kepegawaian/MonitoringTargetController.php`.
   - Pemantauan status hulu penyusunan dan persetujuan target seluruh pegawai (Sudah Mengirim, Draf, Belum Mengisi, Sudah Disetujui, Menunggu Persetujuan).
   - Dialog Modal Rincian Target (Zero-Reload AJAX).
   - Ekspor Excel Multi-Sheet numerik murni & PDF Landscape kedinasan dengan penanganan mode "Sepanjang Tahun" (`nama_bulan`).
2. **Monitoring Penilaian Kinerja (`/kepegawaian` & `/kepegawaian/monitoring-penilaian`)**:
   - Controller: `app/Controllers/Kepegawaian/DashboardKepegawaian.php`.
   - Mengagregasi capaian riil kinerja vs target, skor kualitas/disiplin, dan status terbit SKP.
   - Ultra-Fast 2-Query Batch Fetching (tanpa N+1 query problem).
   - Ekspor Excel & PDF rekapitulasi penilaian.

- **Hierarki Jabatan Resmi Institusi**:
  Pengurutan otomatis berdasarkan struktur organisasi: Direktur $\rightarrow$ Wakil Direktur $\rightarrow$ Kepala Bagian (Kabag AAK/KUK) $\rightarrow$ Ketua Tim (Katim) & Koordinator $\rightarrow$ Kepala Pusat (Kapus) $\rightarrow$ Kepala Unit (Kanit) $\rightarrow$ Ketua/Sekretaris Program Studi (Kaprodi/Sekprodi) $\rightarrow$ Ketua Pokja $\rightarrow$ Tenaga Pendidik / Dosen $\rightarrow$ Jabatan Fungsional Tertentu (JFT) $\rightarrow$ Staf Pelaksana $\rightarrow$ Pegawai Tugas Belajar.
- **Otorisasi Menu Kepegawaian**: Menu tree dan endpoint dibatasi khusus untuk: `['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk']`.
- **Visibilitas Dashboard Institusi**: Khusus akun dengan role `direktur`, `wadir`, `kabag` (`kabag_aak`, `kabag_kuk`), dan `kepegawaian` (termasuk Katim Kepegawaian), dasbor eksekutif membuka hak pantau seluruh pegawai di lingkungan PKTJ (`$canSeeAll = true`).
- **Default Periode Bulan**: Menu awal selalu otomatis memuat **Bulan Sekarang (`date('n')`)**. Tersedia pilihan bulan lampau atau *Sepanjang Tahun* (`'all'`).

### C. Log Keamanan Aktivitas (Audit Trail - `/admin/audit-logs`):
- Helper: `app/Helpers/audit_helper.php` (`log_audit()`).
- Mencatat mutasi data krusial (`CREATE`, `UPDATE`, `DELETE`, `LOGIN`, `LOGOUT`, `APPROVE`, `UNLOCK_LAPORAN`, `CANCEL_APPROVE_TARGET`, `RESET_PENILAIAN_KINERJA`, `EXPORT_EXCEL_KEPEGAWAIAN`, `EXPORT_PDF_KEPEGAWAIAN`) lengkap dengan rekaman JSON *before-after*, IP address, dan User Agent.

### D. Mode Pemeliharaan Mandiri (Maintenance Mode - `/settings`):
- Filter: `app/Filters/MaintenanceFilter.php`.
- Saklar switch 1-klik di menu Pengaturan Sistem mengalihkan seluruh pengguna non-admin ke halaman `public/maintenance.html` (HTTP 503) dengan auto-refresh 30 detik. Administrator tetap memiliki akses 100%.

---

## 7. Arsitektur Perutean (Routing Rules - `app/Config/Routes.php`)
- **Auto-Routing Dimatikan**: `$routes->setAutoRoute(false);` wajib aktif demi keamanan endpoint.
- **Rute Terdaftar Eksplisit**: Seluruh rute sistem terverifikasi 100% memetakan ke Controller dan Method aktif tanpa rute rusak.
- **Filter Guard `auth`**: Seluruh rute internal dibungkus dalam grup `['filter' => 'auth']`.
- **Rute Delete Terkunci ke POST**: Seluruh endpoint hapus data (Master Data, SKP, dan Bukti LED) wajib menggunakan metode `POST` berpelindung token CSRF.

---

## 8. Sinkronisasi Database (Local vs cPanel Production)
- Database: `ekinerja_kinerja` (24 Tabel Basis Data).
- Presisi Tinggi: `target_bulanan` dan `jumlah_capaian` menggunakan `DECIMAL(10,4)`, `nilai_capaian` menggunakan `DECIMAL(5,2)`.
- Status Paritas: Hasil audit forensik pada dump `ekinerja_kinerja (2).sql` membuktikan bahwa database cPanel sudah **100% SINKRON** dengan database lokal (0 missing columns, identical data types & settings keys). **Tidak diperlukan query SQL manual setelah push.**

---

## 9. Status Graphify Knowledge Graph
- Korpus Terindeks: 636 berkas (~1.147.824 kata).
- Simpul & Relasi: 9.611 nodes, 19.933 edges, 554 communities.
- Berkas Artefak: `graphify-out/graph.json`, `graphify-out/graph.html`, dan `graphify-out/GRAPH_REPORT.md`.
