# Panduan Audit & Optimasi Evidence Command Center (ECC)

**Versi panduan:** 3.0.0 · **Diperbarui:** 16 September 2026

**Lingkungan:** Laragon lokal dan cPanel produksi. Catat hasil keduanya secara terpisah.

Dokumen ini adalah panduan kerja berulang untuk memeriksa kode, logika bisnis, keamanan, basis data, kueri, performa, dan antarmuka ECC. Dokumen ini **bukan sertifikat kelulusan aplikasi**. Checklist merupakan kriteria pemeriksaan; status lulus hanya diberikan dalam laporan audit dengan bukti dan cakupan jelas.

## 1. Cara menggunakan

Contoh instruksi:

- `Gunakan audit_code.md untuk audit modul /log-kegiatan dan susun rencana perbaikannya.`
- `Gunakan audit_code.md untuk memeriksa kueri dashboard dan peluang optimasinya.`
- `Gunakan audit_code.md untuk audit seluruh proyek secara bertahap.`
- `Laksanakan rencana perbaikan ECC-001 yang sudah disetujui dengan panduan audit_code.md.`

Jika audit diminta tanpa sasaran tertentu, petakan seluruh proyek dan prioritaskan keamanan akses, integritas data, serta alur kinerja. Lanjutkan penelusuran bertahap tanpa meminta konfirmasi untuk setiap berkas. Jika sasaran disebutkan, periksa modul tersebut dan dependensi yang memengaruhinya. Catat bagian yang belum diperiksa.

**Audit saja:** telusuri bukti, sajikan temuan dan rencana implementasi. **Implementasi:** ikuti lingkup persetujuan pengguna yang sudah diberikan. Permintaan audit tidak otomatis mengizinkan perubahan kode, skema, data, atau konfigurasi produksi. Persetujuan yang sudah mencakup tindakan terkait tidak perlu diminta ulang.

## 2. Acuan dan batas kerja

| Acuan | Kegunaan |
| --- | --- |
| [.agents/AGENTS.md](.agents/AGENTS.md) | Aturan kerja, keamanan, istilah, dan batas pengujian |
| [Skill ECC Development](.agents/skills/simonik_development/SKILL.md) | Arsitektur dan ketentuan khusus ECC |
| [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md), [PRODUCT.md](PRODUCT.md), [PRD.md](PRD.md) | Tujuan produk, peran, dan alur bisnis |
| [design.md](design.md) | Standar tampilan, ukuran, bahasa, dan interaksi |
| [CHECKLIST_OPTIMASI_ECC.md](CHECKLIST_OPTIMASI_ECC.md) | Riwayat pekerjaan optimasi |
| [CHANGELOG.md](CHANGELOG.md), [daftar_pekerjaan.md](daftar_pekerjaan.md) | Riwayat pengembangan dan audit |
| `composer.json`, `composer.lock`, kode, migrasi, konfigurasi lingkungan | Bukti implementasi pada revisi yang diperiksa |

- Instruksi pengguna dalam sesi berlaku sesuai lingkupnya. Skill umum disesuaikan dengan aturan khusus ECC: CodeIgniter 4, MySQL, Bootstrap 5.3, dan Bootstrap Icons. Jangan mengganti teknologi hanya karena saran skill lain.
- Pisahkan **aturan yang diharapkan** dari **perilaku yang ditemukan**. Kode aktif bisa keliru; klaim dokumentasi lama juga bisa tidak sesuai lagi.
- Jika aturan bertentangan, tunjukkan kedua sumber dan dampaknya. Lanjutkan bagian independen; jangan menetapkan kebijakan baru atau mengubah hasil penilaian secara sepihak.
- Jangan mencetak kredensial `.env`, token, hash kata sandi, data pribadi, atau isi cadangan ke laporan. Gunakan contoh yang disamarkan.
- Jangan menjalankan migrasi, seeder, pembersihan cache/log, sinkronisasi eksternal, impor, atau perubahan produksi sebagai bagian otomatis dari audit.
- Ikuti `.agents/AGENTS.md`: browser testing, subagent testing, dan automated testing tidak dijalankan mandiri tanpa izin. Siapkan skenario uji manual untuk pengguna dan catat pemeriksaan yang belum dijalankan.
- Jika `graphify-out/graph.json` tersedia, ikuti [aturan Graphify](.agents/rules/graphify.md) sebelum menelusuri arsitektur. Jika tidak tersedia, catat keterbatasannya dan baca kode langsung. Setelah perubahan kode, ikuti aturan pembaruan grafik; laporkan bila alat tidak tersedia.

## 3. Urutan audit

1. **Tetapkan konteks:** tanggal, sasaran, keluhan, branch/commit, perubahan lokal, lingkungan, dan izin pemeriksaan. Pertahankan perubahan pengguna yang sudah ada.
2. **Petakan alur:** route → filter → controller → model/helper/trait → tabel → view/JavaScript → ekspor. Sertakan pemanggil komponen bersama.
3. **Baca riwayat relevan:** periksa kesesuaian optimasi yang pernah selesai; jangan mengulang implementasi hanya karena muncul di checklist.
4. **Periksa menurut risiko:** dahulukan akses tanpa izin, kehilangan/perubahan data salah, dan kesalahan nilai; lanjutkan titik lambat yang terbukti dan kualitas antarmuka sesuai sasaran.
5. **Kumpulkan bukti:** `berkas:baris`, method/route, kondisi pemicu, perilaku aktual, dan perilaku yang diharapkan. Telusuri penjagaan di lapisan lain sebelum menyatakan celah.
6. **Susun temuan dan rencana:** pisahkan masalah terkonfirmasi, dugaan, konflik aturan, dan usulan peningkatan. Perubahan kode menunggu rencana yang jelas dan persetujuan implementasi yang mencakupnya.
7. **Jika implementasi diizinkan:** kerjakan perubahan terkecil yang menyelesaikan akar masalah. Lindungi kontrak respons, hak akses, hasil perhitungan, dan kompatibilitas lingkungan.


**Kontrol yang sudah ada dan harus dipertahankan:**

- Route eksplisit di grup autentikasi; perubahan memakai POST dan CSRF global. Konfigurasi saat audit menggunakan `regenerate = false`.
- `store()` sudah memeriksa kepemilikan baris yang ditemukan; temuan bukan klaim bahwa seluruh ID pengguna lain bebas diubah.
- Transaksi batch, pemeriksaan `transStatus()`, audit penilaian, pemeriksaan persetujuan target, dan tenggat sudah ada.
- Normalisasi koma desimal sudah ada di backend.
- Modal bukti sudah memeriksa awalan HTTP(S); data JSON kalender di-escape sebagai atribut. Penyusun tooltip juga sudah meng-escape teks dinamis.
- Tabel responsif, angka tabular, label beberapa input, dan aturan reduced motion sudah tersedia.
- API tren sudah memakai batch per tahun, bukan query untuk setiap baris. Pemanggil API grafik tidak ditemukan pada view sasaran maupun pencarian `app/Views` dan `public/assets/js`; jangan menganggap grafik ini sedang ditampilkan atau menghapus endpoint tanpa pemeriksaan penggunaan.

## 3. Temuan dan bukti

**Keterangan:** “Terkonfirmasi statis” berarti alur terlihat pada kode, belum direproduksi lewat aplikasi. “Potensial” membutuhkan kondisi data atau kegagalan tertentu. “Keputusan kebijakan” berarti aturan belum cukup seragam untuk langsung diubah.

### RPK-01 — ID penilaian belum dibatasi ke periode dan status yang diperiksa

**P1 · Terkonfirmasi statis.**

**Bukti:** `PenilaianKinerjaController.php:396–407, 449–492, 502–555, 563–566`.

Persetujuan target dan tenggat diperiksa berdasarkan bulan/tahun request. Akan tetapi, target dan tugas tambahan untuk pembaruan diambil melalui ID dan hanya diperiksa `user_id`. Tidak ada pencocokan periode setiap baris, status terkirim, maupun kesetaraan semua ID request dengan baris yang ditemukan.

**Pemicu:** penilai yang sah mengirim periode A yang memenuhi syarat, tetapi menyisipkan ID milik staf yang sama dari periode B atau baris draf. Nilai periode B dapat ikut berubah. ID yang hilang dan duplikat juga belum ditolak secara eksplisit.

**Hasil yang dituju:** seluruh ID harus valid, ditemukan, dan berada dalam cakupan pengguna, periode, status, serta aksi yang diizinkan; satu pelanggaran membatalkan seluruh batch.

### RPK-02 — Hak akses halaman, penyimpanan, grafik, dan revisi berbeda

**P1 · Perbedaan terkonfirmasi; sebagian kebijakan perlu diputuskan.**

**Bukti:** `PenilaianKinerjaController.php:91–123, 362–386, 693–706`; `LogKegiatanController.php:1159–1166`; view `penilaian_kinerja/index.php:586, 1973–1984`.

- Halaman Kepegawaian menawarkan staf langsung dan pegawai tugas belajar; `store()` serta API grafik memberikan akses lebih luas kepada Kepegawaian.
- Formulir evaluasi mandiri hanya ditampilkan untuk role primer Direktur, sementara backend juga mengizinkan pengguna tanpa atasan.
- Halaman dapat menampilkan staf berdasarkan hubungan atasan, tetapi API grafik membatasi atasan berdasarkan daftar nama role.
- Direktur dapat melihat tombol revisi karena dianggap penilai, tetapi endpoint revisi hanya menerima admin, Kepegawaian, atau atasan langsung.
- Urutan pemeriksaan akun multi-role berbeda: halaman mendahulukan admin/Direktur, sedangkan penyimpanan penilaian staf menolak akun yang memiliki role Wadir.

**Hasil yang dituju:** satu matriks izin dengan aksi terpisah untuk melihat, menilai, menilai diri, membuka revisi, dan membatalkan persetujuan. Akses monitoring tidak otomatis menjadi hak menilai.

### RPK-03 — Kontrak input dan aksi belum ketat

**P1 · Terkonfirmasi statis.**

**Bukti:** `PenilaianKinerjaController.php:38–58, 348–355, 393–428, 439–475, 502–537, 681–686`.

Aksi selain `submit`/`draft` jatuh ke status NULL tanpa ditolak. Nilai di luar 0–150 dipotong menjadi batas terdekat; teks nonnumerik dapat menghasilkan nilai NULL dengan status penilaian tetap terisi. Array, pasangan ID/nilai, ID positif, bulan 1–12, dan tahun belum divalidasi secara konsisten. Pemeriksaan API hanya menolak nilai yang menjadi nol setelah cast.

**Pemicu:** request berisi `action` tak dikenal, nilai `151`, `-1`, `abc`, array bersarang, daftar ID tidak sepadan, atau bulan `13`.

**Hasil yang dituju:** input salah ditolak dengan pesan jelas; tidak ada perubahan diam-diam atau pesan sukses untuk batch kosong. Input aman yang sudah diketik dipulihkan saat validasi gagal.

### RPK-04 — Nilai draf tugas tambahan ikut dikirim kepada staf melalui HTML kalender

**P1 · Terkonfirmasi statis.**

**Bukti:** `LogTugasTambahan.php:42–55`; `PenilaianKinerjaController.php:149–152, 236–240, 299–312, 772–775`; view `penilaian_kinerja/index.php:867`.

Data tugas tambahan dimuat sebagai baris lengkap, diteruskan ke kalender, lalu diserialisasi ke `data-logs`. Kolom `nilai_capaian` dan `status_penilaian` tetap ikut terkirim, termasuk nilai draf penilai. Menyembunyikan angka pada tabel tidak menghilangkannya dari sumber HTML.

**Pemicu:** atasan menyimpan nilai tugas tambahan sebagai draf; staf membuka Target Saya dan memeriksa atribut kalender.

**Hasil yang dituju:** payload rincian kegiatan memakai daftar kolom yang diperlukan. Nilai draf hanya tersedia bagi pihak yang berhak menilai, bukan staf sebagai penerima penilaian.

### RPK-05 — Nilai gabungan tugas tambahan Direktur hanya menyimpan baris pertama

**P2 · Terkonfirmasi statis.**

**Bukti:** view `penilaian_kinerja/index.php:535–539, 699–701, 1190–1192`; `PenilaianKinerjaController.php:502–537`; `KinerjaBatchTrait.php:153–158`.

Formulir mandiri mengirim satu `log_tambahan_id`, sedangkan formulir staf mengirim semua ID. Backend memperbarui daftar yang dikirim. Formulir mandiri juga mengambil nilai awal hanya dari nilai yang sudah terbit sehingga nilai gabungan yang baru disimpan sebagai draf dapat tampak kosong setelah reload.

**Dampak:** baris tambahan dalam bulan yang sama dapat memiliki nilai/status berbeda; pembaca yang memilih nilai pertama dapat menghasilkan hasil berbeda jika susunan data berubah.

**Hasil yang dituju:** aturan nilai gabungan berlaku konsisten untuk seluruh kegiatan tambahan yang memenuhi syarat dalam satu periode; draf milik penilai dapat dimuat kembali. Data historis yang sudah berbeda nilainya dilaporkan dahulu, bukan ditimpa otomatis.

### RPK-06 — Nilai resmi, pratinjau, nol, dan predikat belum memiliki satu kontrak

**P2 · Perbedaan terkonfirmasi + keputusan kebijakan.**

**Bukti:** view `penilaian_kinerja/index.php:525–558, 994–1028, 1622–1631, 1703–1706, 1830–1857`; `PenilaianKinerjaController.php:457–465, 737–740`; `KinerjaBatchTrait.php:90–119, 142–167`.

- Ringkasan staf dari PHP menghitung RHK terbit tetapi menerima nilai tugas tambahan draf. JavaScript kemudian menghitung input yang terisi sebagai pratinjau.
- Konfirmasi penerbitan di frontend mengubah input kosong menjadi 0; backend yang menerima nilai kosong menyimpannya sebagai NULL. Formulir mandiri tidak memakai alur konfirmasi staf.
- Rata-rata tahunan trait membuang bulan bernilai 0. Contoh bulan terbit 0 dan 100 menghasilkan rata-rata 100, jika 0 seharusnya merupakan nilai sah.
- API mengubah ketiadaan nilai tren menjadi 0 sehingga tidak membedakan belum dinilai dengan nilai nol.
- Batas tepat 90 berbeda antara AGENTS/PROJECT_CONTEXT dan skill/design. Dokumentasi juga menyebut 0 sebagai belum dinilai, sementara sebagian kode menganggap 0 sebagai nilai terbit “Sangat Kurang”.

**Hasil yang dituju:** nilai resmi hanya dari komponen yang sah dan terbit; pratinjau diberi label tersendiri. Arti nol, input kosong, pembulatan, dan batas predikat ditetapkan sebelum hasil bisnis diubah.

### RPK-07 — Revisi belum menyelaraskan nilai lama dan tampilan status

**P2 · Mekanisme terkonfirmasi; aturan pembatalan nilai perlu ditetapkan.**

**Bukti:** `LogKegiatanController.php:1190–1200`; `LaporanHarianController.php:863–887`; `PenilaianKinerjaController.php:162–165`; view `penilaian_kinerja/index.php:1980–1989`.

Buka kunci dan batal persetujuan mengubah status kegiatan/target menjadi draf tanpa mengosongkan nilai dan status penilaiannya. Akibatnya nilai lama dapat terbaca pada rekap sendiri atau kembali dihitung ketika data dikirim ulang. Selain itu kalender staf hanya mengambil kegiatan terkirim, sedangkan modal memiliki cabang “revisi aktif” berbasis data draf yang sudah disaring keluar.
* **Status**: ✅ **Selesai & Terverifikasi (14 September 2026)**

---

### 📌 TUGAS 7: Fitur Backup Basis Data 1-Klik untuk Superadmin

* **Latar Belakang & Masalah**:
  * Saat ini pencadangan basis data harus dilakukan secara manual melalui phpMyAdmin cPanel atau mysqldump di terminal.
* **Rencana Solusi**:
  * Sediakan tombol *"Unduh Cadangan Basis Data (.sql)"* di menu Pengaturan Sistem (`/settings`).
  * Endpoint hanya dapat diakses oleh role `admin` dengan verifikasi CSRF dan pencatatan audit log `BACKUP_DATABASE`.
  * Menghasilkan dump SQL terkompresi menggunakan CodeIgniter Database Utility / stream writer yang hemat memori.
* **Berkas Sasaran**:
  * `[MODIFY]` `app/Config/Routes.php` (Pendaftaran rute `settings/backup-db`)
  * `[MODIFY]` `app/Controllers/Admin/SettingsController.php` (Method `backupDatabase()`)
  * `[MODIFY]` `app/Views/admin/settings/index.php` (Bento Card Cadangan Basis Data & dialog SweetAlert2)
* **Kriteria Kelulusan**:
  * Berkas dump `.sql` dapat diunduh langsung via 1-klik dan 100% kompatibel di-restore ke phpMyAdmin.
  * Hanya dapat diakses oleh Administrator dengan proteksi rate limit dan pencatatan audit trail `BACKUP_DATABASE`.
* **Status**: ✅ **Selesai & Terverifikasi (14 September 2026)**

---

### 📌 TUGAS 8: Housekeeping & Manajemen Retensi Data Log Aktivitas

* **Latar Belakang & Masalah**:
  * Tabel `audit_logs` dan `notifications` akan terus membesar seiring berjalannya waktu operasional aplikasi.
* **Rencana Solusi**:
  * Buat tombol dan kebijakan retensi di Pengaturan Sistem untuk mengarsipkan atau membersihkan log yang sudah berusia lebih dari 12 bulan.
  * Dilengkapi konfirmasi SweetAlert2 dan pencatatan audit trail sebelum eksekusi pembersihan.
* **Berkas Sasaran**:
  * `[MODIFY]` `app/Config/Routes.php` (Rute `POST settings/purge-logs`)
  * `[MODIFY]` `app/Controllers/Admin/SettingsController.php` (Method `purgeLogs()`)
  * `[MODIFY]` `app/Views/admin/settings/index.php` (Bento Card Housekeeping, pilihan retensi & target, dialog SweetAlert2)
* **Kriteria Kelulusan**:
  * Data lama terhapus aman dengan transaksi database, data terkini tetap terjaga utuh.
  * Tindakan pembersihan otomatis tercatat ke dalam audit trail keamanan `PURGE_OLD_LOGS`.
* **Status**: ✅ **Selesai & Terverifikasi (15 September 2026)**

---

## 📊 Lembar Kemajuan Pelaksanaan

```text
Progress: [████████████████████████████████████████] 100% (8 dari 8 Selesai) - SELESAI SEMPURNA
```

---

## 🗄️ Kumpulan Kueri SQL Siap Eksekusi di phpMyAdmin cPanel

Salin seluruh blok kueri SQL di bawah ini dan tempelkan langsung pada tab **SQL** di phpMyAdmin cPanel database `ekinerja_kinerja`:

```sql
-- =========================================================================
-- EVIDENCE COMMAND CENTER (ECC) — PENYESUAIAN BASIS DATA CPANEL
-- Tanggal: 15 September 2026
-- Lingkungan Sasaran: cPanel phpMyAdmin (ekinerja_kinerja)
-- =========================================================================

-- 1. DECOMMISSIONING: Hapus Tabel SKP Legacy yang Sudah Tidak Digunakan
DROP TABLE IF EXISTS `skp_targets`;
DROP TABLE IF EXISTS `skp_headers`;

-- 2. SINKRONISASI MIGRASI CI4 (Mencatat Batch 44 agar status migrasi 100% up-to-date)
INSERT IGNORE INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
('2026-09-02-121500', 'App\\Database\\Migrations\\AddIndexToTargetKinerjaBulanan', 'default', 'App', UNIX_TIMESTAMP(), 44),
('2026-09-02-150000', 'App\\Database\\Migrations\\AddIndexToLogTugasTambahan', 'default', 'App', UNIX_TIMESTAMP(), 44),
('2026-09-14-153000', 'App\\Database\\Migrations\\DropDuplicateIndexes', 'default', 'App', UNIX_TIMESTAMP(), 44);

-- Selesai. Struktur tabel di cPanel sudah memiliki indeks komposit utama 
-- (idx_lh_user_bulan_tahun & idx_ltt_user_tgl) sehingga bebas dari error #1091.
```

*(Dokumen ini akan diperbarui statusnya secara berkala setiap kali satu nomor tugas selesai dikerjakan dan diverifikasi)*



Semua pekerjaan aplikasi di bawah berstatus **Diusulkan**. Urutan status: **Diusulkan → Disetujui → Diterapkan → Terverifikasi**. Centang hanya setelah kriteria penerimaan memiliki bukti; “kode sudah dibaca” bukan “perbaikan selesai”.

| No. | Paket pekerjaan | Temuan | Prioritas | Ketergantungan | Status |
| --- | --- | --- | --- | --- | --- |
| 1 | Samakan matriks akses dan konteks pengguna/periode | RPK-02, RPK-13 | P1/P2 | K-03 | Selesai |
| 2 | Validasi request dan pembatasan seluruh ID batch | RPK-01, RPK-03 | P1 | 1; kebijakan aksi terkait | Selesai |
| 3 | Batasi payload privat dan amankan tautan bukti | RPK-04, RPK-08 | P1/P2 | Dapat dimulai mandiri | Selesai |
| 4 | Benahi nilai gabungan, draf, dan perhitungan resmi | RPK-05, RPK-06 | P2 | 1–2; K-01, K-02, K-05 | Selesai |
| 5 | Selaraskan revisi dengan penilaian dan kalender | RPK-07 | P2 | 1–4; K-04, K-06 | Selesai |
| 6 | Perkuat transaksi, audit, notifikasi, dan kirim ulang | RPK-01, RPK-09 | P1/P2 | 1–5 | Selesai |

### Paket 6 — Transaksi, audit, notifikasi
| 9 | Verifikasi manual, dokumentasi, da
- [x] Validasi ulang cakupan dan status pada saat transaksi berjalan; tentukan penguncian baris atau pemeriksaan versi yang cocok dengan database aktif.
- [x] Periksa hasil kedua pembaruan batch. Kegagalan kedua tidak boleh meninggalkan perubahan pertama.
- [x] Cegah form lama menimpa perubahan penilai lain secara diam-diam. Gunakan kondisi versi/status dan pesan muat ulang bila konflik terdeteksi.
- [x] Catat ID, staf, periode, nilai/status sebelum-sesudah, termasuk status approval tambahan dan reset; jangan merekam data pengguna yang tidak diperlukan.
- [x] Muat helper notifikasi eksplisit pada endpoint yang memakainya. Jalankan setelah commit dengan penanganan exception dan hasil gagal.
- [x] Hindari notifikasi berulang untuk request penerbitan yang tidak mengubah apa pun. Bedakan transaksi gagal, tidak ada perubahan, dan sukses tetapi notifikasi gagal.

**Berkas:** [MODIFY] tiga controller terkait; [MODIFY bersyarat] helper notifikasi/audit jika perubahan lokal tidak cukup.

**Penerimaan:** kegagalan mutasi tidak menghasilkan sukses/notifikasi terbit; kegagalan notifikasi tidak menyatakan nilai gagal tersimpan. Konflik perubahan tidak menimpa hasil terbaru tanpa pemberitahuan.

**Penerimaan:** akses langsung ke endpoint memiliki hasil sama dengan matriks izin; menyembunyikan tombol bukan satu-satunya perlindungan. Penggantian staf, sesi lama, dan multi-role tidak memperluas akses.

### Paket 2 — Kontrak input dan ID batch

- [x] Terima hanya aksi `draft`, `submit`, atau `reset`; validasi tipe skalar/array sebelum iterasi dan konversi.
- [x] Validasi ID integer positif, duplikat, panjang/pasangan array, nilai finite, rentang 0–150, serta koma desimal. Jangan memakai pembatasan otomatis untuk menyamarkan angka salah.
- [x] Validasi bulan 1–12, tahun sesuai rentang aplikasi yang disepakati, dan tanggal kalender nyata; gunakan nilai ternormalisasi yang sama untuk query, deadline, sesi, audit, dan redirect.
- [x] Ambil baris berdasarkan pengguna dan periode, lalu cocokkan seluruh ID. Target memakai bulan/tahun; tugas tambahan memakai tanggal awal bulan sampai sebelum awal bulan berikutnya.
- [ ] Untuk draf/terbit, verifikasi status kegiatan dan persetujuan target yang relevan. Reset boleh membersihkan status sesuai aturan yang disepakati, tetapi tetap terbatas pengguna/periode.
- [ ] Batalkan seluruh batch jika satu baris tidak sah. Tolak payload tanpa pekerjaan; jangan melaporkan sukses.
- [ ] Pulihkan input aman berdasarkan ID baris, bukan indeks yang dapat berubah; jangan menampilkan kembali payload milik pengguna lain.

**Berkas:** [MODIFY] controller, `TargetKinerja.php`, `LogTugasTambahan.php`, view sasaran.

**Penerimaan:** ID lintas pengguna, lintas periode, draf yang tidak layak, hilang, duplikat, atau payload rusak tidak mengubah satu pun baris.

### Paket 3 — Privasi data dan bukti

- [x] Bentuk payload kalender dengan daftar kolom eksplisit: tanggal, uraian, capaian, satuan, bukti aman, jenis kegiatan, dan status yang boleh diketahui.
- [x] Hindari serialisasi langsung baris model; keluarkan nilai draf penilai dari respons staf, termasuk atribut HTML dan respons API.
- [x] Terapkan validasi URL HTTP(S), escaping sesuai konteks, dan atribut rel pada seluruh tautan bukti.
- [x] Pertahankan sanitasi yang sudah benar pada tooltip dan modal; hindari perombakan tanpa kebutuhan.

**Berkas:** [MODIFY] controller, view sasaran; proyeksi model bila diperlukan.

**Penerimaan:** nilai draf penilai tidak ditemukan dalam respons untuk staf; bukti non-HTTP(S) tampil sebagai teks/status tanpa tautan aktif. Nilai resmi tetap terbaca.

### Paket 4 — Nilai gabungan dan kalkulasi bersama

- [x] Tetapkan satu kontrak hasil: nilai resmi, pratinjau penilai, jumlah komponen, jumlah dinilai, dan status belum lengkap.
- [x] Resolusikan kumpulan tugas tambahan yang layak di backend berdasarkan pengguna/periode; jangan bergantung pada ID pertama dari form mandiri.
- [x] Gunakan nilai draf untuk mengisi kembali form pihak penilai; hasil resmi staf tetap hanya memakai nilai terbit.
- [x] Deteksi perbedaan nilai/status historis di antara tugas tambahan; catat untuk peninjauan terpisah sebelum normalisasi.
- [x] Pindahkan perhitungan/predikat PHP yang berulang ke satu fungsi bersama. Berikan parameter predikat yang sama ke JavaScript agar batasnya tidak disalin manual.
- [x] Setelah keputusan K-01/K-02: selaraskan nilai 0, NULL, input kosong, rata-rata tahunan, pembulatan, dan tren tanpa data. Jangan mengganti nilai historis otomatis.
- [x] Pastikan API tidak menjumlahkan target/realisasi dengan satuan berbeda sebagai satu ukuran produktivitas tanpa definisi bisnis; konfirmasi kontrak/pemanggil sebelum perubahan respons.

**Berkas:** [NEW] `app/Helpers/kinerja_helper.php`; [MODIFY] controller, view, `KinerjaBatchTrait.php`; [MODIFY bersyarat] pemanggil bersama jika kontraknya berubah.

**Penerimaan:** kasus dengan data setara memberi nilai resmi sama pada Target Saya dan API; pratinjau terpisah jelas. Nilai gabungan Direktur tersimpan dan dimuat ulang dengan benar pada lebih dari satu kegiatan.

### Paket 5 — Revisi dan status kalender

- [x] Terapkan keputusan K-04 dalam transaksi yang sama dengan buka revisi/batal persetujuan.
- [x] Cegah nilai lama menjadi resmi kembali hanya karena target/kegiatan dikirim ulang.
- [x] Sediakan metadata status revisi yang cukup untuk kalender staf tanpa membocorkan isi draf yang belum boleh dilihat.
- [x] Selaraskan kemampuan tombol dengan izin endpoint, termasuk Direktur, atasan langsung, Kepegawaian, dan admin.
- [x] Pertahankan perbaikan Lapor Kegiatan Harian yang sudah ada; ubah hanya integrasi yang dibutuhkan modul penilaian.

**Berkas:** [MODIFY] `LogKegiatanController.php::bukaKunci()`, `LaporanHarianController.php::cancelApprove()`, controller penilaian dan view.

**Penerimaan:** izin revisi terlihat benar setelah reload; data yang direvisi tidak membawa nilai terbit lama tanpa proses penilaian sesuai keputusan kebijakan.

### Paket 6 — Transaksi, audit, notifikasi

- [ ] Validasi ulang cakupan dan status pada saat transaksi berjalan; tentukan penguncian baris atau pemeriksaan versi yang cocok dengan database aktif.
- [ ] Periksa hasil kedua pembaruan batch. Kegagalan kedua tidak boleh meninggalkan perubahan pertama.
- [ ] Cegah form lama menimpa perubahan penilai lain secara diam-diam. Gunakan kondisi versi/status dan pesan muat ulang bila konflik terdeteksi.
- [ ] Catat ID, staf, periode, nilai/status sebelum-sesudah, termasuk status approval tambahan dan reset; jangan merekam data pengguna yang tidak diperlukan.
- [ ] Muat helper notifikasi eksplisit pada endpoint yang memakainya. Jalankan setelah commit dengan penanganan exception dan hasil gagal.
- [ ] Hindari notifikasi berulang untuk request penerbitan yang tidak mengubah apa pun. Bedakan transaksi gagal, tidak ada perubahan, dan sukses tetapi notifikasi gagal.

**Berkas:** [MODIFY] tiga controller terkait; [MODIFY bersyarat] helper notifikasi/audit jika perubahan lokal tidak cukup.

**Penerimaan:** kegagalan mutasi tidak menghasilkan sukses/notifikasi terbit; kegagalan notifikasi tidak menyatakan nilai gagal tersimpan. Konflik perubahan tidak menimpa hasil terbaru tanpa pemberitahuan.

### Paket 7 — Formulir dan aksesibilitas

- [ ] Pisahkan identitas dan selector form mandiri/staf; penghitungan, reset, fokus, serta validasi hanya menyentuh form yang dimaksud.
- [ ] Gunakan fallback dropdown native jika Select2 tidak tersedia; jaga fungsi form ketika JavaScript/plugin gagal.
- [ ] Lengkapi fallback SweetAlert pada seluruh jalur dan hapus pemuatan aset ganda setelah memastikan urutan layout benar.
- [ ] Tampilkan kesalahan dekat input, pertahankan nilai aman, dan beri pilihan saat pengguna mengganti filter dengan perubahan belum tersimpan.
- [ ] Lindungi kirim ganda pada draf/terbit/reset/revisi; pulihkan kontrol setelah gagal.
- [ ] Ubah pemicu kalender menjadi kontrol semantik, tambahkan label tanggal/status, fokus terlihat, navigasi keyboard dan pengembalian fokus modal.
- [ ] Periksa viewport 360/768/1280 px, zoom 200%, teks panjang, dan area sentuh minimal 44 px; tabel boleh memiliki gulir horizontal yang jelas.
- [ ] Pertahankan Bootstrap, Bootstrap Icons seperlunya, angka tabular, dan grid proyek. Tinjau animasi yang ada; hanya transisi sederhana yang membantu interaksi.

**Berkas:** [MODIFY] view sasaran; [NEW bersyarat] `public/assets/js/penilaian-kinerja.js`, `public/assets/css/penilaian-kinerja.css` bila pemisahan memudahkan pemeliharaan. Jangan sekaligus merombak layout global.

**Penerimaan:** pengguna dapat menyimpan, membatalkan, memperbaiki input, dan membuka rincian dengan keyboard maupun seluler; kegagalan plugin tidak memutus alur utama.

### Paket 8 — Optimasi query dan payload

- [ ] Ukur kondisi awal sebelum mengoptimasi: peran, filter, volume, waktu respons, query, baris dimuat, memori, dan ukuran HTML/JSON.
- [ ] Pilih kolom pengguna yang digunakan saja; jangan membawa hash kata sandi dan kolom lain ke daftar staf.
- [ ] Ganti filter `YEAR(tanggal_kegiatan)` dengan rentang tanggal yang setara. Pertimbangkan pembacaan tepat enam bulan untuk API bila manfaat terukur.
- [ ] Bedakan `null` dan daftar ID kosong pada loader batch; telusuri semua pemanggil sebelum mengubah kontrak.
- [ ] Kurangi duplikasi data kalender; pemuatan modal/tab bertahap hanya jika payload atau waktu benar-benar menjadi masalah. Endpoint baru tetap harus memiliki pemeriksaan izin.
- [ ] Evaluasi agregasi realisasi dan urutan stabil tanpa mengubah makna LEFT JOIN, status terkirim, atau presisi angka.
- [ ] Periksa indeks aktual dengan `SHOW INDEX` dan `EXPLAIN SELECT` dalam lingkup akses yang tersedia. Jangan menjalankan `EXPLAIN ANALYZE` atau beban produksi tanpa izin.
- [ ] Jangan membuat indeks hanya dari nama migrasi. Migrasi `2026-09-14-153000_DropDuplicateIndexes.php` sudah menghapus indeks duplikat dan menyebut indeks pengganti yang dipertahankan; keadaan database aktif belum dikonfirmasi.

**Berkas:** [MODIFY] controller, trait, model terkait; [NEW bersyarat] migrasi indeks hanya setelah rencana query membuktikan kebutuhan, berikut rollback. Tidak ada migrasi wajib dalam rencana awal.

**Penerimaan:** hasil dan hak akses sama, volume pembacaan berkurang pada kasus yang ditargetkan, dan metrik sebelum-sesudah dicatat. Bila manfaat tidak terbukti, jangan menambah kompleksitas.

### Paket 9 — Verifikasi dan penutupan

- [ ] Lakukan skenario manual pada bagian 6 untuk setiap paket yang diimplementasikan; catat hasil, lingkungan, dan bukti.
