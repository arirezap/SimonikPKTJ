# Laporan Audit Kode: Monitoring Target Kinerja Bulanan

**Tanggal:** 11 Oktober 2026  
**Sasaran Modul:** Menu Kepegawaian > Monitoring Target Kinerja Bulanan (`/kepegawaian/target-kinerja`, `/kepegawaian/target-kinerja/detail-pegawai`, `/kepegawaian/target-kinerja/export-excel`, `/kepegawaian/target-kinerja/export-pdf`)  
**Berkas Terkait:**  
- `app/Controllers/Kepegawaian/MonitoringTargetController.php`
- `app/Views/kepegawaian/monitoring_target.php`
- `app/Views/kepegawaian/monitoring_target_pdf.php`
- `app/Models/User.php`
- `app/Models/TargetKinerja.php`
- `app/Models/SettingModel.php`
**Acuan Audit:** `audit_code.md`, `.agents/AGENTS.md`, `design.md`

---

## 1. Ringkasan Eksekutif

Pemeriksaan menyeluruh telah dilakukan terhadap modul **Monitoring Target Kinerja Bulanan** pada menu Kepegawaian berdasarkan 8 pilar evaluasi `audit_code.md`. Modul ini berfungsi sebagai pusat kendali bagi Tim Kepegawaian, Administrator, dan Pimpinan (Direktur, Wadir, Kabag) untuk memantau kepatuhan seluruh pegawai PKTJ dalam menyusun, mengajukan, dan mendapatkan persetujuan Target Kinerja Bulanan (RHK).

Secara arsitektur, modul ini telah dirancang dengan standar kualitas tinggi:
- **Otorisasi Multi-Role**: Seluruh rute terlindungi filter `auth` dan pemeriksaan peran terpusat via `hasAnyRole()`.
- **Bebas Masalah N+1 Query**: Menggunakan teknik *single batch fetching* (`whereIn('user_id', ...)`) untuk memuat target bulanan seluruh pegawai dalam 1 kali kueri.
- **Keamanan Kueri**: Menggunakan seleksi kolom eksplisit tanpa memuat hash kata sandi (`users.password`), validasi parameter numerik ketat, dan proteksi throttling unduhan berkas berat (*rate limiting*).
- **Standar UI/UX 8-Point Grid**: Menerapkan kartu bento, pemilahan status cepat (*quick filter pills*), pencarian instan sisi klien dengan *debounce*, tabel desktop sortable, kartu sentuh seluler, dan modal rincian interaktif dengan *shimmer skeleton loader*.

Namun demikian, audit menemukan **sejumlah celah optimasi dan ketidakkonsistenan data**:
1. **Filter Kategori Jabatan belum memeriksa tabel pivot multi-role (`user_roles`)**, sehingga pegawai dengan tugas rangkap/sekunder (misal tugas belajar atau kepegawaian) berisiko terlewat.
2. **Kueri Atasan memuat seluruh baris tabel pengguna (`findAll()`)**, alih-alih hanya mengambil ID atasan yang relevan dengan pegawai terpilih.
3. **Ketidaksinkronan metrik ringkasan pada berkas PDF**, di mana kategori "Sudah Mengirim" dan "Sudah Disetujui" saling tumpang tindih alih-alih menampilkan kategori antrean "Menunggu Persetujuan".
4. **Penyaringan status pegawai belum menyertakan pegawai `nonaktif` yang memiliki masa tugas aktif (TMT)** pada periode pemantauan lampau.
5. **Duplikasi kode kueri dan hierarki jabatan sebanyak 3 kali** antara tampilan web, ekspor Excel, dan ekspor PDF.

---

## 2. Temuan Audit Berdasarkan 8 Pilar Pemeriksaan

### Pilar 2: Logika Bisnis & Integritas Data
- **MT-01: Filter Kategori Jabatan Mengabaikan Tabel Pivot Multi-Role (`user_roles`)**
  - **Lokasi:** `app/Controllers/Kepegawaian/MonitoringTargetController.php:81-95`, `383-397`, `725-739`
  - **Kondisi:** Filter jabatan hanya mencocokkan kolom string `users.role` secara langsung tanpa memeriksa tabel relasi `user_roles`.
  - **Dampak:** Pegawai yang memiliki peran primer `user` namun mengemban tugas sekunder sebagai `tugas_belajar`, `kepegawaian`, atau `manajemen` (Kanit/Katim) tidak terjaring ketika Kepegawaian menyaring berdasarkan kategori tersebut.
  - **Rekomendasi:** Selaraskan filter peran dengan menyertakan pencocokan ID pengguna dari tabel pivot `user_roles` sebagaimana telah diterapkan pada modul Kelola Pengguna dan Daftar Pegawai.

- **MT-02: Inkonsistensi Kategori Ringkasan KPI pada Laporan PDF**
  - **Lokasi:** `app/Controllers/Kepegawaian/MonitoringTargetController.php:760-833` & `app/Views/kepegawaian/monitoring_target_pdf.php:206-240`
  - **Kondisi:** Pada antarmuka web, terdapat 6 kotak KPI yang bersifat aditif dan terpisah tegas: *Total*, *Terkirim (Persentase)*, *Disetujui*, *Menunggu Persetujuan*, *Draf*, dan *Belum Mengisi*. Namun pada laporan PDF, kotak "Menunggu Persetujuan" ditiadakan dan digantikan oleh "Sudah Mengirim" bersanding dengan "Sudah Disetujui".
  - **Dampak:** Karena "Sudah Mengirim" mencakup target yang sudah disetujui, penjumlahan angka pada kotak-kotak KPI di PDF menghasilkan total yang melebihi jumlah pegawai sebenarnya (tumpang tindih / *overlapping data*), membingungkan pimpinan saat membaca cetakan fisik.
  - **Rekomendasi:** Selaraskan kotak KPI pada laporan PDF agar menampilkan kategori aditif: *Total*, *Disetujui*, *Menunggu Persetujuan*, *Draf*, dan *Belum Mengisi* (atau 6 kotak persis seperti versi web).

- **MT-03: Pegawai Nonaktif dengan TMT Aktif Terlewat dari Pemantauan Periode Lampau**
  - **Lokasi:** `app/Controllers/Kepegawaian/MonitoringTargetController.php:63, 72, 367, 376, 709, 718`
  - **Kondisi:** Kueri penyaringan status pegawai hanya mencakup:
    `whereIn('status_pegawai', ['mutasi_keluar', 'pensiun'])`.
  - **Dampak:** Jika ada pegawai yang berstatus `nonaktif` namun memiliki tanggal akhir tugas (TMT) di pertengahan tahun berjalan, pegawai tersebut tidak muncul saat Kepegawaian memantau target bulan-bulan lampau masa dinasnya.
  - **Rekomendasi:** Perluas kondisi menjadi `whereIn('status_pegawai', ['mutasi_keluar', 'pensiun', 'nonaktif'])`.

---

### Pilar 5: Performa & Optimasi Kueri
- **MT-04: Pemuatan Berlebih Data Master Pengguna pada Pemetaan Atasan Langsung**
  - **Lokasi:** `app/Controllers/Kepegawaian/MonitoringTargetController.php:102, 400, 742`
  - **Kondisi:** Kode memanggil `$userModel->select('id, nama_lengkap, nip, unit, jabatan')->findAll()` yang mengambil seluruh pegawai di sistem hanya untuk mencari nama atasan.
  - **Dampak:** Memuat ratusan baris data pengguna yang tidak dibutuhkan ke memori server (RAM), terjadi berulang kali setiap halaman dimuat dan setiap ekspor dijalankan.
  - **Rekomendasi:** Ambil daftar `atasan_id` unik dari `$semuaPegawai` saja (`array_filter(array_unique(array_column($semuaPegawai, 'atasan_id')))`), lalu lakukan kueri tertarget dengan `whereIn('id', $atasanIds)`.

- **MT-05: Duplikasi Kueri dan Logika Penyusunan Rekap Target (Pelanggaran DRY)**
  - **Lokasi:** `app/Controllers/Kepegawaian/MonitoringTargetController.php` pada `index()`, `exportExcel()`, dan `exportPdf()`
  - **Kondisi:** Blok kueri penarikan pegawai, filter waktu, pengurutan hierarki, pengambilan target RHK, dan penentuan status pengiriman/persetujuan ditulis ulang secara identik sebanyak 3 kali (sekitar 180 baris kode berulang).
  - **Dampak:** Setiap pembaruan logika bisnis di masa mendatang berisiko desinkronisasi antara tampilan web, hasil unduhan Excel, dan cetakan PDF.
  - **Rekomendasi:** Ekstraksi proses penyiapan data ke dalam method privat `getMonitoringData($bulan, $tahun, $unit, $role, $status)` sebagai satu sumber kebenaran (*single source of truth*).

---

### Pilar 7 & 8: Antarmuka, Aksesibilitas, & Mikro-Kopi
- **MT-06: Ketiadaan Penanda Visual Status Pegawai (Mutasi/Pensiun) pada Baris Pemantauan**
  - **Lokasi:** `app/Views/kepegawaian/monitoring_target.php:693-705` (desktop) & `851-856` (mobile)
  - **Kondisi:** Pegawai mutasi keluar atau pensiun yang muncul pada bulan aktifnya ditampilkan dengan gaya visual yang sama persis seperti pegawai aktif reguler.
  - **Dampak:** Tim Kepegawaian tidak dapat membedakan secara instan apakah pegawai tersebut adalah pegawai aktif reguler atau pegawai yang sudah berpindah tugas.
  - **Rekomendasi:** Tambahkan badge status ringkas (*Mutasi*, *Pensiun*, *Nonaktif*) di samping nama pegawai pada tabel desktop dan kartu seluler.

- **MT-07: Penyederhanaan Teks Dialog Unduhan Dokumen (SweetAlert2)**
  - **Lokasi:** `app/Views/kepegawaian/monitoring_target.php:1338-1340`
  - **Kondisi:** Dialog SweetAlert2 saat menyiapkan unduhan masih memuat kalimat yang cukup panjang dan berbau teknis (*"Streaming data terenkripsi & aman"*).
  - **Rekomendasi:** Sederhanakan teks dialog menjadi singkat, tenang, dan padat sesuai standar mikro-kopi ECC:
    - Judul: *"Menyiapkan Berkas..."*
    - Teks: *"Mohon tunggu, berkas sedang diproses."*

---

## 3. Matriks Status Audit

| Kode | Temuan | Pilar | Tingkat Risiko | Status Implementasi |
|---|---|---|---|---|
| **MT-01** | Filter jabatan mengabaikan tabel pivot `user_roles` | Logika Bisnis (Pilar 2) | Sedang | **Selesai (Resolved)** |
| **MT-02** | Metrik ringkasan KPI pada PDF saling tumpang tindih | Integritas Data (Pilar 2) | Sedang | **Selesai (Resolved)** |
| **MT-03** | Pegawai `nonaktif` bertanggal TMT terlewat di bulan lampau | Logika Bisnis (Pilar 2) | Rendah | **Selesai (Resolved)** |
| **MT-04** | Pemuatan seluruh user (`findAll()`) untuk map atasan | Performa/Kueri (Pilar 5) | Sedang | **Selesai (Resolved)** |
| **MT-05** | Duplikasi kueri di `index`, `exportExcel`, `exportPdf` (DRY) | Struktur Kode (Pilar 1) | Sedang | **Selesai (Resolved)** |
| **MT-06** | Ketiadaan badge status kepegawaian di tabel pemantauan | Antarmuka/UI (Pilar 7) | Rendah | **Selesai (Resolved)** |
| **MT-07** | Mikro-kopi dialog unduhan memuat teks teknis | Bahasa/Mikro-kopi (Pilar 8) | Rendah | **Selesai (Resolved)** |

---

## 4. Rencana Implementasi Perbaikan (Implementation Plan)

### Langkah Kerja:
1. **Pemusatan Data & Optimasi Kueri Controller (`MonitoringTargetController.php`)**:
   - Buat method privat helper `prepareMonitoringDataset($bulan, $tahun, $unit, $role, $status)` untuk menyatukan logika pengambilan pegawai, filter peran multi-role (`user_roles`), penyaringan status pegawai (`aktif`, `mutasi_keluar`, `pensiun`, `nonaktif`), pemuatan kueri target RHK, dan agregasi status.
   - Optimalkan kueri pemetaan atasan langsung agar hanya mengambil ID atasan yang terlibat via `whereIn('id', $atasanIds)`.
   - Gunakan method terpusat ini pada `index()`, `exportExcel()`, dan `exportPdf()`.
2. **Penyelarasan Metrik KPI Laporan PDF (`monitoring_target_pdf.php`)**:
   - Selaraskan kotak statistik ringkasan PDF agar memuat status yang aditif: Total Pegawai, Sudah Disetujui, Menunggu Persetujuan, Draf, dan Belum Mengisi.
3. **Penyelarasan Visual Antarmuka (`monitoring_target.php`)**:
   - Tambahkan badge penanda status pegawai (*Mutasi*, *Pensiun*, *Nonaktif*) pada kolom nama pegawai di desktop dan kartu seluler.
   - Sederhanakan teks penjelasan pada dialog proses unduhan SweetAlert2.
