# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Pegawai, Dosen, Staf, Kepala Bagian (Kabag AAK & KUK), Kepala Unit (Kanit), Ketua Tim (Katim), Ketua Program Studi (Kaprodi), Staf Subbag Kepegawaian, Tim Satuan Penjaminan Mutu (SPM), serta Pimpinan Tinggi (Direktur & Wakil Direktur) Politeknik Keselamatan Transportasi Jalan (PKTJ Tegal).

## Product Purpose

Evidence Command Center (ECC) (sebelumnya Simonik) adalah sistem informasi internal terintegrasi untuk mengelola perencanaan target kinerja bulanan (RHK), mencatat log aktivitas harian berbasis tautan bukti (*evidence link*), memfasilitasi penilaian kinerja berjenjang oleh atasan langsung, memonitor kelayakan pencairan remunerasi pegawai, dan mengevaluasi capaian instrumen Laporan Evaluasi Diri (LED) program studi secara real-time.

## Positioning

Platform evaluasi kinerja institusi berbasis bukti digital (*evidence-based*) yang menggabungkan hierarki struktural birokrasi, penegakan integritas penilaian atasan-bawahan, monitoring remunerasi objektif, dan pemantauan akreditasi program studi dalam satu *command center* visual berkinerja tinggi.

## Operating Context

Aplikasi berbasis web yang diakses setiap hari kerja melalui laptop, komputer kantor, dan perangkat seluler oleh seluruh civitas PKTJ. Meliputi pengisian log kegiatan harian, pengajuan izin perbaikan/revisi laporan, approval target bulanan, penilaian kinerja berkala sebelum batas tanggal per bulan, hingga ekspor data evaluasi kinerja untuk keperluan remunerasi.

## Capabilities and Constraints

- **Multi-Role Pivot Table**: Mendukung peran primer struktural (Direktur, Wadir, Kabag, Kanit, Katim, Kaprodi, User, Tugas Belajar) dan peran sekunder fungsional (Kepegawaian, SPM).
- **Dual-Sync Unit Kerja**: Menghubungkan `users.unit_id` ke `unit_kerja.id` dengan mempertahankan sinkronisasi string `users.unit` untuk backward-compatibility 100%.
- **Data Partitioning & Ownership Guard**: Isolasi query ketat berbasis sesi login pengguna agar tidak terjadi manipulasi data antar-pegawai (mitigasi IDOR).
- **Invisible Audit Trail**: Perekaman otomatis setiap aksi `CREATE`, `UPDATE`, `DELETE`, `APPROVE`, `UNLOCK_LAPORAN`, `CANCEL_APPROVE_TARGET` dengan data *before-after* format JSON.
- **Dynamic CSRF & Throttling**: Regenerasi token CSRF pada setiap interaksi AJAX dan pembatasan brute-force login 10x/menit.
- **Dual-View Mobile First**: Tampilan tabel desktop dengan sticky header yang otomatis beralih ke *Touch Card List* pada layar smartphone (<768px) dengan touch target minimal 44px.
- **Standar Istilah Mutlak**: Menggunakan istilah baku **"staf"** (tidak menggunakan "staf") dan nama resmi **"Evidence Command Center (ECC)"**.

## Brand Commitments

- **Official Name**: Evidence Command Center (ECC).
- **Visual Identity**: Modern Enterprise Bento Grid (Clean White surfaces, 16px border-radius, soft elevation shadow, high-contrast Slate backgrounds `#f8fafc`).
- **Brand Colors**: Deep Navy `#0f172a`, Royal Blue `#0d6efd`, Success Emerald `#198754`, Amber Warning `#d97706`, Crimson Danger `#dc2626`.
- **Single Icon Family**: Bootstrap Icons (`bi bi-...`) eksklusif pada 100% tampilan antarmuka.

## Evidence on Hand

- Basis data operasional dengan 24 tabel MySQL termigrasi dan ternormalisasi.
- Template dokumen cetak standar A4 untuk Kontrak Kinerja dan Pakta Integritas.
- Ekspor rekapitulasi data remunerasi berformat CSV BOM UTF-8 ramah Microsoft Excel.
- Dokumentasi teknis lengkap di `PRD.md`, `DESIGN.md`, `PROJECT_CONTEXT.md`, `CHANGELOG.md`, dan `daftar_pekerjaan.md`.

## Product Principles

1. **Transparansi Berbasis Bukti Nyata (*Evidence-Based*)**: Setiap klaim capaian dan kuantitas hasil kerja wajib menyertakan bukti tautan dokumen yang dapat diverifikasi langsung oleh atasan penilai.
2. **Penegakan Hierarki & Integritas Penilaian**: Hubungan penilai dan yang dinilai ditentukan secara hierarkis melalui sistem atasan langsung dengan validasi batas rentang predikat kinerja.
3. **Ergonomi & Kecepatan Entri Data**: Antarmuka dirancang untuk input cepat tanpa friksi, mendukung angka desimal berbasis koma bahasa Indonesia, dan pencarian instan live search.
4. **Resiliensi & Keandalan Transaksi**: Seluruh operasi multi-baris dibungkus dalam transaksi database aman (`transStart` / `transComplete`) dan blok `try-catch` terpusat.
