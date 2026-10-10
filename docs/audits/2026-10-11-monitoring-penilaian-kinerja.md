# Laporan Audit Kode: Monitoring Penilaian Kinerja

**Tanggal:** 11 Oktober 2026  
**Sasaran Modul:** Menu Kepegawaian > Monitoring Penilaian Kinerja (`/kepegawaian`, `/kepegawaian/monitoring-penilaian`, `/kepegawaian/detail-pegawai`, `/kepegawaian/export-excel`, `/kepegawaian/export-pdf`)  
**Berkas Terkait:**  
- `app/Controllers/Kepegawaian/DashboardKepegawaian.php`
- `app/Views/kepegawaian/rekap_kinerja.php`
- `app/Views/kepegawaian/rekap_kinerja_pdf.php`
- `app/Helpers/kinerja_helper.php`
- `app/Libraries/PenilaianKinerjaPolicy.php`
- `app/Models/User.php`
- `app/Models/LaporanHarian.php`
- `app/Models/LogTugasTambahan.php`  
**Acuan Audit:** `audit_code.md`, `.agents/AGENTS.md`, `design.md`

---

## 1. Ringkasan Eksekutif

Pemeriksaan komprehensif telah dilakukan terhadap modul **Monitoring Penilaian Kinerja** pada menu Kepegawaian berdasarkan 8 pilar evaluasi `audit_code.md`. Modul ini berfungsi sebagai dasbor pengawasan instansi bagi Tim Kepegawaian, Administrator, dan Pimpinan (Direktur, Wadir, Kabag) untuk memantau capaian kinerja bulanan dan tahunan seluruh pegawai PKTJ secara menyeluruh.

Modul ini memiliki beberapa keunggulan teknis yang solid:
- **Bebas N+1 Query**: Menggunakan teknik *2-query bulk batch fetching* pada method `getBulkRekapKinerja()` untuk menggabungkan target RHK, realisasi harian, dan tugas tambahan seluruh pegawai dalam satu putaran.
- **Privasi Nilai Draf**: Rekapitulasi institusi hanya mengekspos nilai capaian yang telah berstatus `terbit` (`status_penilaian = 'terbit'`), menjaga kerahasiaan penilaian draf sebelum disahkan atasan.
- **Throttling Ekspor Berkas**: Dilengkapi proteksi *rate limiting* (`checkExportRateLimit`) untuk mencegah beban lonjakan server akibat unduhan dokumen Excel dan PDF berukuran besar.
- **Standar Antarmuka 8-Point Grid**: Menerapkan kartu bento, *filter pills* horizontal scrollable, pencarian instan sisi klien dengan *debounce*, sorting kolom tabel desktop, kartu sentuh seluler, dan modal rincian kegiatan pegawai.

Namun demikian, audit menemukan **7 poin celah optimasi, duplikasi kode, dan inkonsistensi mikro-kopi** yang perlu diperbaiki:
1. **Filter Kategori Jabatan belum memeriksa tabel pivot `user_roles`**, sehingga pegawai dengan tugas tambahan sekunder (tugas belajar, pengelola kepegawaian, atau manajemen) tidak terjaring.
2. **Penyaringan status pegawai belum menyertakan pegawai `nonaktif` yang memiliki TMT selesai** pada periode lampau yang bersangkutan.
3. **Pemuatan berlebih data pengguna (`findAll()`)** hanya untuk mencari nama atasan langsung, menghabiskan memori RAM server.
4. **Duplikasi kueri penarikan pegawai sebanyak 3 kali** antara web, Excel, dan PDF (pelanggaran prinsip DRY).
5. **Inkonsistensi konversi predikat kinerja** yang ditulis ulang secara manual di 5 tempat berbeda dengan ambang batas lokal, alih-alih memanfaatkan `hitung_predikat_kinerja()` dari `kinerja_helper.php`.
6. **Ketiadaan penanda visual status pegawai (*Mutasi*, *Pensiun*, *Nonaktif*)** pada daftar rekapitulasi desktop dan kartu seluler.
7. **Mikro-kopi dialog unduhan SweetAlert2** masih memuat istilah teknis sistem.

---

## 2. Temuan Audit Berdasarkan 8 Pilar Pemeriksaan

### Pilar 1 & 2: Arsitektur, Logika Bisnis & Integritas Data
- **MP-01: Filter Kategori Jabatan Mengabaikan Tabel Pivot Multi-Role (`user_roles`)**
  - **Lokasi:** `app/Controllers/Kepegawaian/DashboardKepegawaian.php:72-86`, `592-606`, `1053-1067`
  - **Kondisi:** Filter jabatan hanya memeriksa kolom tunggal `users.role`. Pegawai yang memiliki peran primer `user` namun mengemban tugas sekunder di tabel pivot `user_roles` (seperti `tugas_belajar`, `kepegawaian`, atau `manajemen`) tidak terjaring saat Kepegawaian menyaring berdasarkan kategori tersebut. Selain itu, pengecualian administrator hanya menyaring kolom `users.role != 'admin'`.
  - **Rekomendasi:** Perluas filter peran dengan menyertakan pencocokan ID pengguna dari tabel pivot `user_roles` dan kecualikan ID pengguna yang memiliki peran `admin` pada pivot `user_roles`.

- **MP-02: Pegawai Nonaktif dengan TMT Aktif Terlewat dari Pemantauan Periode Lampau**
  - **Lokasi:** `app/Controllers/Kepegawaian/DashboardKepegawaian.php:54, 63, 574, 583, 1035, 1044`
  - **Kondisi:** Kueri penyaringan status pegawai hanya mencakup `whereIn('status_pegawai', ['mutasi_keluar', 'pensiun'])`.
  - **Dampak:** Pegawai yang berstatus `nonaktif` namun memiliki tanggal akhir tugas (TMT) di bulan-bulan lampau tahun berjalan tidak muncul saat Kepegawaian memantau capaian bulan aktif masa dinasnya.
  - **Rekomendasi:** Perluas kondisi menjadi `whereIn('status_pegawai', ['mutasi_keluar', 'pensiun', 'nonaktif'])`.

- **MP-05: Inkonsistensi Konversi Predikat Kinerja (Duplikasi Ambang Batas Lokal)**
  - **Lokasi:** `DashboardKepegawaian.php:140-157` & `1253-1272`, `rekap_kinerja.php:640-658`, `818-836`, `rekap_kinerja_pdf.php:362-380`
  - **Kondisi:** Logika penentuan predikat (Sangat Baik, Baik, Butuh Perbaikan, Kurang, Sangat Kurang, Belum Dinilai) ditulis ulang secara manual dengan if-else di controller, modal AJAX, tabel desktop, kartu seluler, dan PDF.
  - **Dampak:** Melanggar aturan sentralisasi `AGENTS.md` Bagian 4.J yang mewajibkan konversi predikat dipusatkan pada `app/Helpers/kinerja_helper.php` (`hitung_predikat_kinerja()`).
  - **Rekomendasi:** Panggil helper `kinerja` (`helper(['avatar', 'kinerja'])`) dan gunakan fungsi `hitung_predikat_kinerja()` secara seragam di seluruh titik.

---

### Pilar 5: Performa & Optimasi Kueri
- **MP-03: Pemuatan Berlebih Data Master Pengguna pada Pemetaan Atasan Langsung**
  - **Lokasi:** `app/Controllers/Kepegawaian/DashboardKepegawaian.php:91, 559, 1020`
  - **Kondisi:** Memanggil `$userModel->select('id, nama_lengkap, nip, unit, jabatan')->findAll()` yang memuat seluruh tabel pengguna ke memori RAM server, meskipun yang dibutuhkan hanyalah segelintir atasan langsung dari pegawai yang sedang difilter.
  - **Rekomendasi:** Kumpulkan daftar `atasan_id` unik dari `$semuaPegawai` saja (`array_unique(array_filter(array_column($semuaPegawai, 'atasan_id')))`), lalu lakukan kueri tertarget via `whereIn('id', $atasanIds)`.

- **MP-04: Duplikasi Logika Kueri Pegawai dan Hierarki (Pelanggaran DRY)**
  - **Lokasi:** `app/Controllers/Kepegawaian/DashboardKepegawaian.php` pada `index()`, `exportExcel()`, dan `exportPdf()`
  - **Kondisi:** Blok kueri seleksi pegawai, filter waktu, pengurutan hierarki, dan pemetaan atasan ditulis ulang secara identik sebanyak 3 kali (sekitar 180 baris kode berulang).
  - **Rekomendasi:** Ekstraksi proses seleksi dan persiapan daftar pegawai ke dalam method privat `getPegawaiDataset($bulan, $tahun, $unit, $role)`.

---

### Pilar 7 & 8: Antarmuka, Aksesibilitas, & Mikro-Kopi
- **MP-06: Ketiadaan Penanda Visual Status Pegawai (Mutasi/Pensiun/Nonaktif) pada Baris Rekapitulasi**
  - **Lokasi:** `app/Views/kepegawaian/rekap_kinerja.php:700-702` (desktop) & `883-884` (mobile)
  - **Kondisi:** Pegawai mutasi keluar, pensiun, atau nonaktif yang tampil pada periode aktifnya tidak memiliki penanda pembeda, sehingga menyulitkan Tim Kepegawaian dan Pimpinan untuk membedakan pegawai aktif reguler dengan pegawai berpindah tugas.
  - **Rekomendasi:** Tambahkan badge status pegawai ringkas (*Mutasi*, *Pensiun*, *Nonaktif*) di samping nama pegawai pada tabel desktop dan kartu seluler.

- **MP-07: Penyederhanaan Teks Dialog Unduhan Dokumen (SweetAlert2)**
  - **Lokasi:** `app/Views/kepegawaian/rekap_kinerja.php:1514-1516`
  - **Kondisi:** Dialog unduhan SweetAlert2 masih memuat teks teknis (*"Streaming terenkripsi & aman"*) dan kalimat yang panjang.
  - **Rekomendasi:** Sederhanakan teks dialog menjadi singkat, tenang, dan padat sesuai standar mikro-kopi ECC:
    - Judul: *"Menyiapkan Berkas..."*
    - Subjudul: *"Sedang menyiapkan data kinerja..."*
    - Keterangan: *"Mohon tunggu sebentar, unduhan akan dimulai otomatis."*

---

## 3. Matriks Status Audit

| Kode | Temuan | Pilar | Tingkat Risiko | Status Rekomendasi | Catatan Perbaikan |
|---|---|---|---|---|---|
| **MP-01** | Filter jabatan mengabaikan tabel pivot `user_roles` | Logika Bisnis (Pilar 2) | Sedang | **Selesai (Resolved)** | Diselaraskan di `preparePegawaiDataset()` memeriksa `user_roles` dan mengecualikan admin pivot |
| **MP-02** | Pegawai `nonaktif` bertanggal TMT terlewat di bulan lampau | Logika Bisnis (Pilar 2) | Rendah | **Selesai (Resolved)** | Kondisi diperluas menyertakan `nonaktif` dengan syarat `tmt_selesai` |
| **MP-03** | Pemuatan seluruh user (`findAll()`) untuk map atasan | Performa/Kueri (Pilar 5) | Sedang | **Selesai (Resolved)** | Dioptimasi dengan selective query `whereIn('id', $atasanIds)` |
| **MP-04** | Duplikasi kueri di `index`, `exportExcel`, `exportPdf` (DRY) | Struktur Kode (Pilar 1) | Sedang | **Selesai (Resolved)** | Diekstraksi ke method privat `preparePegawaiDataset()` |
| **MP-05** | Inkonsistensi perhitungan predikat kinerja & duplikasi kode | Logika Bisnis / DRY (Pilar 2/6) | Sedang | **Selesai (Resolved)** | Dipusatkan 100% ke `hitung_predikat_kinerja()` dari `kinerja_helper.php` |
| **MP-06** | Ketiadaan badge status kepegawaian di tabel pemantauan | Antarmuka/UI (Pilar 7) | Rendah | **Selesai (Resolved)** | Ditambahkan badge visual *Mutasi*, *Pensiun*, *Nonaktif* di desktop table & mobile card |
| **MP-07** | Mikro-kopi dialog unduhan memuat teks teknis | Bahasa/Mikro-kopi (Pilar 8) | Rendah | **Selesai (Resolved)** | Teks teknis dibersihkan dan disederhanakan menjadi singkat, padat, dan jelas |

