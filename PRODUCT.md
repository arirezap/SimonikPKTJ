# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Pimpinan & Struktural PKTJ:** Direktur, Wakil Direktur (Wadir), Kepala Bagian (Kabag AAK & KUK), Kepala Unit (Kanit), Ketua Tim (Katim), dan Ketua Program Studi (Kaprodi).
- **Pegawai, Dosen & Staf Operasional ECC Kinerja:** Seluruh pegawai ASN maupun tenaga pendukung yang terlibat dalam perencanaan target bulanan (RHK), pelaporan kegiatan harian, survei pemetaan kompetensi SDM, dan pemantauan akreditasi LED.
- **Pengelola Substansi Lintas Unit:** Staf Subbag Kepegawaian, Tim Satuan Penjaminan Mutu (SPM), Tim Substansi Administrasi Ketarunaan & Alumni (`ketarunaan`, `ketarunaan_validator`, `karir_alumni` / PIC Tracer Study), Pusat Pengembangan Karakter (`pusbangkar`), serta Tim Substansi Bidang Kerjasama (`kerjasama` — mitra industri & distribusi survei pengguna lulusan).
- **Pengguna Layanan Mandiri (Portal Terisolasi):** Taruna aktif (`taruna` — saat ini 427 taruna Prodi RSTJ Tingkat 1–4, disusul TRO & TO) dan Lulusan (`alumni`), ditambah mitra industri/pengguna lulusan secara eksternal.

## Product Purpose

**Evidence Command Center (ECC)** adalah platform manajemen kinerja dan tata kelola ketarunaan terintegrasi milik Politeknik Keselamatan Transportasi Jalan (PKTJ Tegal). Sistem ini menyatukan:
1. **Manajemen Kinerja & Akreditasi Institusi (ECC 1.x):** Perencanaan target bulanan (RHK), log aktivitas harian berbasis tautan bukti (*evidence link*), penilaian berjenjang atasan-staf, monitoring remunerasi, pemantauan instrumen Laporan Evaluasi Diri (LED), dan survei pemetaan potensi/kompetensi SDM PKTJ.
2. **Komando Data Ketarunaan, Karier & Alumni (ECC 2.0):** Pengelolaan satu identitas taruna lintas status pendidikan, rekam jejak kegiatan, prestasi individu/tim, pembinaan karakter & pelanggaran, seleksi empat jenis beasiswa, bursa lowongan kerja terverifikasi, profil dasar alumni, serta pengajuan mandiri taruna dengan tata kelola validasi berjenjang dan jejak audit penuh.

## Positioning

Platform tata kelola institusi dan ketarunaan berbasis bukti digital (*evidence-based*) yang mengonsolidasikan evaluasi kinerja pegawai, pemetaan kompetensi SDM, dan siklus pembinaan taruna hingga alumni dalam satu *command center* visual berkinerja tinggi yang aman, teraudit, dan bebas duplikasi data.

## Operating Context

Aplikasi web internal PKTJ Tegal yang beroperasi setiap hari melalui desktop maupun perangkat seluler:
- **Portal Pegawai & Pimpinan:** Mengelola target bulanan, log harian, persetujuan dan penilaian staf lintas periode mutasi/rotasi, rekap remunerasi, simulasi LED, pemetaan SDM, serta dasbor pengawasan ketarunaan, validasi data, dan ekspor laporan resmi.
- **Portal Mandiri Taruna & Alumni (Terisolasi dari ECC Kinerja):** Taruna masuk memakai Nomor Taruna untuk melengkapi biodata/kontak, mengubah kata sandi, memantau rekam jejak pribadi, serta mengajukan klaim prestasi, capaian kegiatan, dan beasiswa; lulusan kelak memperbarui profil karier dan mengisi *tracer study*.
- **Operasional Saat Ini & Arah Bertahap:** Sistem telah berada pada mode **Data Operasional (`produksi`)** dengan 427 taruna aktif Prodi RSTJ Tingkat 1–4 serta 21 pemegang peran dari 3 unit pengelola, dengan rencana ekspansi bertahap menuju impor data taruna Prodi TRO dan TO, pengoperasian penuh siklus IP/beasiswa/pelanggaran, dan pengembangan modul *Tracer Study* serta Survei Pengguna Lulusan.

## Capabilities and Constraints

- **Arsitektur Multi-Role Pivot & Pemisahan Portal Mandiri:** Mendukung peran primer struktural dan peran sekunder fungsional (`user_roles`). `EmployeeOnlyFilter` mengisolasi 100% rute kinerja pegawai dari akun mandiri (`taruna`/`alumni`), sementara akun mandiri diarahkan ke portal pribadinya tanpa akses dasbor internal atau data taruna lain.
- **Dual-Sync Unit Kerja & Hierarki Multi-Periode:** Sinkronisasi `users.unit_id` dan `users.unit`, serta pengelolaan riwayat penugasan dinas (`riwayat_atasan`) agar penilaian SKP tetap akurat saat terjadi mutasi, rotasi, atau pensiun sesuai PermenPAN-RB No. 6 Tahun 2022.
- **Satu Identitas Taruna & Pemisahan Status vs Validasi:** `nomor_taruna` (teks unik) menjadi kunci tunggal lintas modul pendidikan, prestasi, capaian, pelanggaran, beasiswa, dan alumni; memisahkan 6 status pendidikan (`aktif`, `skorsing`, `cuti`, `mengundurkan_diri`, `drop_out`, `lulus`) dari status tata kelola (`draf`, `diajukan`, `perlu_perbaikan`, `tervalidasi`, `dibatalkan`).
- **Kendali Anti-IDOR & Perlindungan Data Sensitif:** Isolasi kueri berbasis sesi, penguncian pengajuan setelah dikirim/tervalidasi, pembatasan dokumen sensitif (NIK penuh, berita acara pelanggaran, dokumen ekonomi beasiswa), serta proteksi akses URL langsung (HTTP 403).
- **Invisible Audit Trail & Transparansi Netral:** Perekaman menyeluruh (`CREATE`, `UPDATE`, `DELETE`, `APPROVE`, `VALIDASI`, `LOGIN`, ekspor, dan penolakan akses) berformat JSON di `audit_logs`, dengan tampilan UI persetujuan kinerja yang menjaga netralitas waktu untuk mitigasi temuan auditor Itjen/BPK.
- **Impor Fleksibel & Lintas Prodi Tanpa Data Fiktif:** Mendukung pembacaan ekspor Excel (`.xls`/`.xlsx`) maupun template CSV dengan pembentukan `ref_kelas` otomatis mengikuti kelas riil masing-masing program studi (`RSTJ`, `TRO`, `TO`).
- **Dual-View Mobile First & Upload Hardening:** Tampilan desktop adaptif beralih ke Touch Card di seluler (`<768px`, area sentuh min. 44px); pengamanan berkas unggahan (batas ukuran, validasi MIME/ekstensi, penamaan acak kriptografis, `.htaccess` anti-eksekusi PHP).
- **Standar Istilah Mutlak:** Menggunakan istilah resmi **"staf"** (bukan "bawahan" atau "staff"), nama aplikasi **"Evidence Command Center (ECC)"**, serta bahasa antarmuka singkat, padat, dan non-teknis.

## Brand Commitments

- **Official Name:** Evidence Command Center (ECC).
- **Visual Identity:** Modern Enterprise Bento Grid (permukaan putih bersih, radius `16px`, elevasi bayangan halus, latar Slate kontras tinggi `#f8fafc`), konsisten mengikuti shell ECC 1.5 di seluruh subsistem ECC 1.x dan ECC 2.0.
- **8-Point Grid Scale:** Ritme spasial kelipatan `8px` (`4px`, `8px`, `12px`, `16px`, `24px`, `32px`, `40px`, `48px`, `64px`, `80px`) pada margin, padding, tinggi kontrol, dan wadah aset ikon.
- **Brand Colors:** Deep Navy `#0f172a`, Royal Blue `#0d6efd`, Argentina/Sky Blue `#0284c7`, Success Emerald `#198754` / `#10b981`, Amber Warning `#d97706`, Crimson Danger `#dc2626`, Purple `#7c3aed`.
- **Single Icon Family:** Bootstrap Icons (`bi bi-...`) secara eksklusif dan selektif sesuai prinsip minimalis ikon.

## Evidence on Hand

- **Basis Data Terpadu:** 56 tabel MySQL ternormalisasi (mencakup modul Kinerja & LED, Survei Pemetaan Kompetensi SDM, serta subsistem Ketarunaan, Karier & Alumni).
- **Data Operasional Nyata (Tahap Awal RSTJ):** `427` data taruna aktif Prodi RSTJ Tingkat 1–4 beserta `18` kelas riil (`ref_kelas`), `427` akun portal `taruna`, dan `21` pegawai PKTJ pemegang peran resmi ECC 2.0 pada mode `produksi`.
- **Ekspor & Dokumen Resmi:** Cetak A4 Kontrak Kinerja, Pakta Integritas, Rekap SKP Multi-Periode PDF, Rekap Remunerasi Excel/CSV BOM UTF-8, pratinjau/ekspor laporan ketarunaan lintas modul, serta template impor CSV/Excel.
- **Dokumentasi Proyek Aktif:** [`PRD.md`](file:///c:/laragon/www/SimonikPKTJ/PRD.md), [`ECC 2.0/PRD.md`](file:///c:/laragon/www/SimonikPKTJ/ECC%202.0/PRD.md), [`design.md`](file:///c:/laragon/www/SimonikPKTJ/design.md), [`PROJECT_CONTEXT.md`](file:///c:/laragon/www/SimonikPKTJ/PROJECT_CONTEXT.md), [`survei_pemetaan_kompetensi.md`](file:///c:/laragon/www/SimonikPKTJ/survei_pemetaan_kompetensi.md), [`CHANGELOG.md`](file:///c:/laragon/www/SimonikPKTJ/CHANGELOG.md), dan [`audit_code.md`](file:///c:/laragon/www/SimonikPKTJ/audit_code.md).

## Product Principles

1. **Transparansi Berbasis Bukti Nyata (*Evidence-Based*):** Setiap capaian kinerja pegawai, prestasi taruna, perubahan status, dan penetapan beasiswa wajib bertumpu pada dokumen atau tautan bukti yang dapat diverifikasi.
2. **Satu Sumber Kebenaran (*Single Source of Truth*):** Data pegawai (`users`), unit kerja (`unit_kerja`), nomor taruna (`master_taruna`), dan jejak audit (`audit_logs`) dipakai bersama antar-modul tanpa pencatatan ganda atau angka fiktif.
3. **Pemisahan Pengajuan dan Pengesahan:** Data draf, pengajuan, verifikasi, penetapan formal, dan validasi dipisahkan secara tegas untuk mencegah angka belum sah masuk ke rekap resmi atau dasbor pimpinan.
4. **Resiliensi Transaksi & Ergonomi Entri Data:** Seluruh operasi multi-tabel dibungkus transaksi atomik (`transStart`/`transComplete`), blok `try-catch` terpusat, penulisan cepat angka desimal koma Indonesia, serta penguncian input yang cerdas dan aman.
