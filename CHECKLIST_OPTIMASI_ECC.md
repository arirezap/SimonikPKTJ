# Implementation Plan: Target Kinerja Bulanan ECC

**Tanggal audit:** 17 September 2026

**Sasaran:** menu **Target Kinerja Bulanan**, route `/laporan-harian`, beserta halaman, kode, fungsi, query, dan dependensi langsungnya.

**Status:** **Diusulkan — belum diimplementasikan.**

**Dasar pemeriksaan:** HEAD `3f43056` dan perubahan lokal saat audit. Kode lokal diperiksa ulang; status selesai pada audit lama tidak dianggap sebagai bukti kondisi sekarang.

Dokumen ini menggantikan seluruh isi checklist Rekap & Penilaian Kinerja sesuai permintaan pengguna. Pada tahap ini hanya dokumen ini yang diubah. Implementasi aplikasi, perubahan database, pengujian fungsi, dan deployment belum dilakukan.

## 1. Tujuan dan batas pekerjaan

Pastikan staf dapat menyusun, menyimpan draf, menyalin, mengajukan, dan merevisi target dengan benar; atasan hanya menyetujui data yang memang diajukan dan berada dalam kewenangannya. Setiap perubahan harus menjaga keterkaitan target dengan kegiatan harian dan penilaian.

**Cakupan utama:**

- Target Saya dan Persetujuan Target Staf.
- Filter bulan/tahun, unit, staf, dan pemulihan konteks tab.
- Tambah/edit/hapus baris, Simpan Draf, Ajukan Target, serta Perbarui & Ajukan Ulang.
- Salin target dari periode lain: tambah baris dan ganti baris.
- Edit Target oleh atasan, Setujui Semua, endpoint persetujuan satu target.
- Auto-approval dan revisi mandiri Direktur.
- Batas 30 target, duplikasi RHK/indikator, tenggat, dan persetujuan sebagian.
- Pembatalan persetujuan oleh admin, dampak ke log kegiatan, nilai, notifikasi, dan audit.
- Keamanan request, transaksi, query, ketahanan formulir, dan aksesibilitas.

**Batas:** tidak mengaudit ulang seluruh Rekap & Penilaian Kinerja, monitoring, dashboard, atau Lapor Kegiatan Harian. Bagian tersebut hanya menjadi dependensi dan sasaran regresi bila terpengaruh perubahan target. Tidak ada penghapusan data historis, perubahan skema, pemasangan indeks, atau perubahan kebijakan tanpa lingkup implementasi yang jelas.

## 2. Acuan dan metode

- [Panduan audit](audit_code.md), [aturan proyek](.agents/AGENTS.md), dan [aturan Graphify](.agents/rules/graphify.md).
- [Skill ECC Development](.agents/skills/simonik_development/SKILL.md) dan [skill Impeccable](.agents/skills/impeccable/SKILL.md) untuk pemeriksaan antarmuka.
- [Konteks proyek](PROJECT_CONTEXT.md), [produk](PRODUCT.md), [PRD](PRD.md), [desain](design.md), [riwayat perubahan](CHANGELOG.md), dan [riwayat pekerjaan](daftar_pekerjaan.md).
- Graphify ditelusuri melalui `graphify query`, kemudian hasilnya dibandingkan dengan kode aktif.
- Pemeriksaan berupa pembacaan statis route, controller, view/JavaScript, model, policy, helper, konfigurasi, dan migrasi terkait.
- Detektor statis Impeccable untuk view sasaran menghasilkan `[]`; hasil itu bukan sertifikat keamanan maupun kelulusan UI.
- Browser, aplikasi berjalan, query database aktif, PHPUnit, dan beban produksi tidak diuji. Pengukuran performa dan skema lokal/cPanel belum diverifikasi.
- Perubahan lokal yang sudah ada pada modul penilaian, kegiatan harian, policy, helper, model, aturan, dan berkas lainnya dipertahankan.

### Peta berkas dan endpoint

| Komponen | Berkas / endpoint |
| --- | --- |
| Controller utama | [LaporanHarianController.php](app/Controllers/User/LaporanHarianController.php) |
| Halaman dan JavaScript | [index.php](app/Views/user/laporan_harian/index.php) |
| Model target | [TargetKinerja.php](app/Models/TargetKinerja.php); `LaporanHarian.php` merupakan alias lama |
| Otorisasi bersama | [PenilaianKinerjaPolicy.php](app/Libraries/PenilaianKinerjaPolicy.php); `User.php`; `role_helper.php` |
| Relasi kegiatan/nilai | `LogKegiatanHarian.php`, `LogTugasTambahan.php`, controller kegiatan dan penilaian |
| Halaman/filter | `GET/POST laporan-harian` → `index()` |
| Simpan/pengajuan | `POST laporan-harian/store` → `store()` |
| Salin periode | `POST laporan-harian/get-previous-targets` → `getPreviousTargets()` |
| Hapus | `POST laporan-harian/hapus` → `hapus()` |
| Persetujuan | `POST laporan-harian/approve` dan `approve-all` |
| Pembatalan persetujuan | `POST laporan-harian/batal-approve` → `cancelApprove()` |

Nomor baris pada temuan merujuk kondisi saat audit dan dapat bergeser setelah implementasi.

### Kontrol yang sudah ada dan perlu dipertahankan

- Route terdaftar eksplisit dalam grup autentikasi; mutasi memakai POST dan CSRF global. Token diperbarui pada respons AJAX. `Security.php` memakai `regenerate = false`.
- `store()` sudah menginisialisasi pengguna target dan memeriksa kepemilikan baris yang ditemukan. Jangan menyebut seluruh ID lintas pengguna tidak terlindungi.
- Draf parsial, normalisasi koma desimal, pemeriksaan target positif, serta pemeriksaan duplikasi dan 30 baris per request sudah ada.
- `approve()` dan `approveAll()` secara eksplisit menolak persetujuan diri non-Direktur; temuan TKB-01 berada pada jalur `store()`.
- `approveAll()` dan `cancelApprove()` sudah memakai transaksi, penanganan exception, dan pemeriksaan `transStatus()`.
- Pembatalan persetujuan saat ini **sudah** mereset nilai/status penilaian target dan tugas tambahan dalam periode, mengembalikan log terkait ke draf, serta menangani kegagalan notifikasi.
- Hapus target berupaya mempertahankan log melalui `target_id = NULL` dan status draf; model log memakai LEFT JOIN. Kompatibilitas null dengan skema aktif masih harus diperiksa.
- `User::getStaf()` sudah memilih kolom tertentu. `SettingModel` sudah memiliki cache selama satu request.
- Form filter memakai pola redirect setelah POST untuk alur tab yang valid. View sudah memiliki escaping PHP, label input dinamis, tabel responsif, reduced motion, dan fallback SweetAlert pada banyak interaksi.
- Respons salin sudah membatasi field ke sasaran, indikator, target, dan satuan; ID, approval, dan nilai penilaian tidak disalin.

## 3. Temuan berdasarkan prioritas

**Terkonfirmasi statis:** alur masalah terlihat di kode, belum direproduksi melalui aplikasi. **Potensial:** dampak memerlukan kondisi tertentu. **Perlu keputusan:** perilaku aktual sudah jelas, tetapi kebijakan akhirnya perlu ditetapkan.

### TKB-01 — Penanda edit staf dapat dipakai untuk menyetujui target sendiri

**P1 · Keamanan/alur bisnis · Terkonfirmasi statis**

**Bukti:** `app/Controllers/User/LaporanHarianController.php:203–232, 242, 267, 307–309`.

`is_editing_staf` berasal dari request. Pemeriksaan izin staf hanya berjalan jika pengguna target berbeda dari pengguna aktif. Jika staf mengirim `is_editing_staf=1` dan `staf_id` miliknya sendiri, pemeriksaan itu dilewati. Flag yang sama melewati penguncian tenggat/persetujuan dan menyebabkan `status_approval = disetujui`.

**Pemicu:** akun staf biasa mengubah parameter form target sendiri, lalu mengirim satu baris valid.

**Dampak:** staf dapat melewati proses persetujuan atasan dan mengubah target sendiri yang sudah terkunci.

**Usulan:** tentukan konteks diri/staf dari identitas dan otorisasi backend. Flag klien hanya petunjuk antarmuka; tidak boleh memberikan hak edit, pengecualian tenggat, atau approval.

### TKB-02 — ID target dapat dipindahkan antarperiode

**P1 · Integritas data · Terkonfirmasi statis**

**Bukti:** `LaporanHarianController.php:369–384, 405–427, 435–436`.

Baris pembaruan menyertakan bulan/tahun request. Pemeriksaan data lama hanya mencocokkan pemilik, tanpa periode, status setiap ID, dan kesetaraan seluruh ID request dengan data yang ditemukan.

**Pemicu:** kirim bulan B dengan ID target milik sendiri dari bulan A. Pemeriksaan kunci menggunakan bulan B, sedangkan baris bulan A diperbarui termasuk kolom periodenya.

**Dampak:** target bergeser periode sementara log lama masih menunjuk ID yang sama; rekap dan hubungan target-realisasi dapat menjadi salah.

**Usulan:** ID pembaruan harus milik pengguna dan periode yang dipilih, ditemukan seluruhnya, unik, dan layak diubah. Penyalinan memakai baris baru; tidak memindahkan ID sumber.

### TKB-03 — Pengajuan/persetujuan dapat menerbitkan draf yang belum lengkap

**P1 · Alur bisnis/data · Terkonfirmasi statis**

**Bukti:** `LaporanHarianController.php:347–365, 459–469, 553–585, 641–645`.

Validasi kelengkapan `store()` hanya memeriksa baris dalam request, lalu semua target pada bulan tersebut diubah menjadi terkirim. Draf tersimpan yang tidak ikut request dapat ikut diajukan tanpa validasi. `approve()` juga langsung mengubah target menjadi terkirim/disetujui tanpa mensyaratkan status awal terkirim atau kelengkapan. `approveAll()` sudah membatasi status terkirim, tetapi perlu memastikan data layak dan memang belum disetujui.

**Pemicu:** simpan draf belum lengkap, lalu ajukan payload yang hanya memuat target lengkap lain; atau akses endpoint approve pada ID draf oleh penyetuju yang berwenang.

**Usulan:** validasi keadaan akhir seluruh target yang akan berubah status. Setujui hanya target yang sah dan sudah diajukan; jangan menjadikan persetujuan sebagai cara mengirim draf.

### TKB-04 — Penyimpanan dan perubahan status belum atomik sepenuhnya

**P1 · Transaksi/kegagalan · Terkonfirmasi statis; kegagalan perlu reproduksi terkendali**

**Bukti:** `LaporanHarianController.php:430–469, 488–494, 582–611, 742–764`.

`store()` memanggil `transComplete()` tanpa memeriksa `transStatus()`; perubahan status seluruh bulan dilakukan setelah transaksi selesai. Hasil insert/update juga belum diperiksa menyeluruh. `approve()` tidak memeriksa hasil update sebelum audit/notifikasi/sukses. `hapus()` memeriksa status transaksi tetapi tidak memiliki penanganan exception di sekitar rangkaian mutasinya.

**Dampak:** kegagalan yang tidak melempar exception dapat dilaporkan sukses; perubahan konten dan pengajuan dapat selesai sebagian. Pada hapus, exception dapat keluar sebagai respons yang tidak sesuai kontrak AJAX.

**Usulan:** gabungkan perubahan konten, status, relasi, dan invalidasi nilai yang diperlukan dalam transaksi yang sama; periksa hasil/status sebelum sukses. Periksa ulang status saat menyimpan agar form lama tidak menimpa approval atau revisi yang lebih baru.

### TKB-05 — Penguncian target berbeda antara tampilan dan backend

**P1 · Alur edit/persetujuan · Terkonfirmasi statis**

**Bukti:** `LaporanHarianController.php:265–279, 307–309, 369–379`; `app/Views/user/laporan_harian/index.php:330–346, 406, 459, 594–621, 959–985`.

Halaman mengunci per baris dan tetap menyediakan aksi jika belum semua target disetujui. Backend target sendiri menolak seluruh penyimpanan begitu **satu** target sudah disetujui. Sebaliknya, edit staf melewati kunci backend dan dapat mengubah baris disetujui, meskipun UI memberi `readonly`. Auto-approval Direktur memakai role primer, sedangkan sebagian endpoint memakai helper multi-role.

**Pemicu:** satu target disetujui dan satu masih menunggu; staf mengubah target kedua. Atau penilai mengirim perubahan target disetujui secara langsung.

**Usulan:** selaraskan aturan per baris/periode dengan keputusan bisnis. Baris disetujui yang ikut form harus tetap tidak berubah kecuali melalui kemampuan revisi yang sah; payload tidak boleh membuka kunci.

### TKB-06 — Kuota 30 target dan duplikasi hanya diperiksa per request

**P2 · Integritas data/optimasi · Terkonfirmasi statis**

**Bukti:** `LaporanHarianController.php:24, 289–300, 311–345, 438–442`; view `laporan_harian/index.php:811–826, 993–1008, 1468–1511`.

Jumlah dan pasangan sasaran/indikator hanya diperiksa terhadap payload. Target tersimpan yang tidak dikirim tidak dihitung. Menambahkan request baru atau menyimpan hasil salin dapat melewati total 30 atau membuat duplikasi dengan data tersimpan. Tombol tambah/salin belum memakai batas yang sama.

**Usulan:** periksa himpunan akhir target setelah update/insert/delete yang eksplisit; tegakkan kuota dan duplikasi dalam cakupan pengguna/periode dan transaksi. Tampilkan sisa kuota di UI tanpa menjadikan klien satu-satunya penjagaan.

### TKB-07 — Mode salin “gantikan” tidak menggantikan target tersimpan

**P1 · Ketepatan penyimpanan · Terkonfirmasi statis**

**Bukti:** view `laporan_harian/index.php:736–747, 1461–1463, 1487–1489`; `LaporanHarianController.php:382–386, 435–442, 459–469`.

Mode replace mengosongkan tabel DOM dan membuat semua ID salinan kosong. Backend hanya memperbarui/menambah baris; target tersimpan yang hilang dari DOM tidak dihapus. Akibatnya, setelah simpan/reload data lama muncul bersama salinan; saat ajukan, data lama dapat ikut terkirim.

**Pemicu:** bulan tujuan sudah memiliki target tersimpan, kemudian pengguna memilih “Gantikan Baris Tabel Saat Ini” dan menyimpan.

**Usulan:** bedakan mengganti input belum tersimpan dengan mengganti data tersimpan. Jangan menerjemahkan ID yang tidak dikirim sebagai penghapusan otomatis. Penggantian data tersimpan harus eksplisit, dikonfirmasi, mematuhi lock/kuota, serta menangani log terkait secara atomik.

### TKB-08 — ID hasil simpan draf dipasang berdasarkan posisi baris yang dapat berubah

**P1 · Integritas data frontend · Terkonfirmasi statis; interaksi belum diuji**

**Bukti:** view `laporan_harian/index.php:1230–1251`, dibanding handler tambah/hapus/salin pada `:811, 863, 1377`.

Saat menyimpan draf, hanya tombol Simpan Draf yang dinonaktifkan. Respons `new_ids[index]` ditempelkan ke `tr.eq(index)`. Tambah/hapus/salin selama request dapat menggeser baris, sehingga ID tersimpan dipasang ke baris yang berbeda. Tombol ajukan juga masih dapat digunakan saat request draf berlangsung.

**Usulan:** gunakan identitas baris stabil untuk request/respons atau snapshot elemen yang tidak dapat berubah, serta satu pengendali proses simpan per form. Nonaktifkan aksi yang mengubah susunan, isi yang sedang disimpan, atau mengirim ulang sampai request selesai.

### TKB-09 — Cakupan izin belum seragam dan belum memakai policy yang sudah tersedia

**P1 · Otorisasi · Perbedaan terkonfirmasi; cakupan tertentu perlu keputusan**

**Bukti:** `LaporanHarianController.php:84–123, 216–231, 566–579, 630–638, 714–738, 798, 980–993`; `app/Libraries/PenilaianKinerjaPolicy.php`; `.agents/AGENTS.md` bagian 4.I.

Daftar staf Kepegawaian dibatasi staf langsung/tugas belajar, tetapi endpoint edit, approve, hapus, dan salin memberi bypass Kepegawaian yang lebih luas. Halaman mendahulukan admin/Direktur sebelum Wadir, sementara beberapa aksi menolak jika akun memiliki role Wadir. Endpoint salin tidak memiliki penolakan Wadir yang sama dan dapat mengambil draf jika hubungan atasan mengizinkan. Pembatalan persetujuan masih memakai pemeriksaan admin langsung, belum kemampuan policy sesuai aturan terbaru.

**Usulan:** perluas policy yang sudah ada dengan kemampuan khusus target: melihat, menyunting, mengajukan, menyetujui, menghapus, menyalin sumber, dan membatalkan persetujuan. Jangan otomatis menyamakan hak melihat rekap atau menilai diri dengan hak menyetujui target.

### TKB-10 — Validasi bentuk request, periode, panjang, dan presisi belum lengkap

**P2 · Ketahanan/data · Terkonfirmasi statis**

**Bukti:** `LaporanHarianController.php:36–76, 200–204, 234, 283–321, 355–376, 619–624, 806–815, 975–1001`.

Array paralel belum diperiksa tipe, struktur, pasangan, dan ID duplikat/hilang. Scalar pada field array dapat menimbulkan galat. Bulan/tahun umumnya hanya dicast/diperiksa tidak kosong; bulan di luar 1–12 dapat merusak indeks nama bulan atau konteks tanggal. Nilai positif belum dibatasi terhadap nilai finite, kapasitas/desimal kolom, atau panjang teks. Mode draf ditentukan oleh AJAX, bukan hanya aksi yang tervalidasi.

**Usulan:** kontrak request seragam, aksi eksplisit, bulan/tahun valid, array satu tingkat, ID positif unik, dan validasi numerik/panjang sesuai skema nyata. Jangan mengubah maksud aksi hanya karena header AJAX. Tetap dukung koma desimal dan draf parsial yang memang diizinkan.

### TKB-11 — Revisi belum tuntas pada tenggat dan semua jalur perubahan target

**P1 · Integrasi target/kegiatan/penilaian · Terkonfirmasi statis; rincian kebijakan perlu ditetapkan**

**Bukti:** `LaporanHarianController.php:155–169, 242–262, 369–379, 748–754, 863–902`.

`cancelApprove()` sudah membuka target menjadi draf serta mereset nilai, tetapi `index()`/`store()` tetap mengunci periode lampau atau lewat tenggat. Staf yang diminta merevisi dapat tidak bisa menyimpan ulang. Jalur edit target langsung, termasuk revisi mandiri Direktur, tidak mengosongkan nilai/status penilaian lama ketika isi target berubah. Hapus target melepaskan relasi log dan perlu pembuktian bahwa log itu dapat dipilihkan target pengganti serta dikirim ulang.

**Usulan:** izin revisi harus berasal dari keadaan yang dapat diverifikasi, bukan menyimpulkan semua draf sebagai revisi. Selaraskan dampak perubahan sasaran/indikator/kuantitas/satuan terhadap nilai terbit dan log. Pertahankan reset yang sudah ada pada batal persetujuan; lengkapi alur yang belum tercakup.

### TKB-12 — Sanitasi teks salinan belum aman untuk konteks atribut HTML

**P2 · Keamanan keluaran · Terkonfirmasi pada konstruksi DOM; dampak perlu verifikasi**

**Bukti:** view `laporan_harian/index.php:1483–1501, 1573–1575`.

`escapeHtml()` membuat teks melalui `$('<div>').text(text).html()`, kemudian hasilnya disisipkan ke atribut `value` dan `title` berpetik ganda untuk satuan. Escaping isi teks bukan pengkodean atribut: petik dalam satuan dapat memutus nilai atribut dan membentuk markup yang tidak dimaksudkan.

**Pemicu:** data satuan tersimpan mengandung petik dan fragmen markup, kemudian disalin.

**Usulan:** buat baris dari template statis dan isi field melalui `.val()`, `.text()`, serta setter atribut DOM. Tidak perlu menilai semua template literal sebagai rentan; masalahnya adalah konteks penyisipan data.

### TKB-13 — Notifikasi dan galat masih dapat menyesatkan hasil operasi

**P2 · Kegagalan/audit · Terkonfirmasi statis; kegagalan eksternal bersifat potensial**

**Bukti:** `LaporanHarianController.php:519–528, 582–611, 641–645, 676–697, 918–942, 958–964`.

- `approve()`/`approveAll()` mengirim notifikasi tanpa penanganan exception tersendiri sesudah perubahan. Operasi dapat terlihat gagal meskipun persetujuan tersimpan.
- Approve-all mengambil seluruh target terkirim, termasuk yang sudah disetujui; request ulang dapat menghitung/menotifikasi baris yang sama lagi.
- Pengajuan awal setelah Simpan Draf dianggap “pembaruan” hanya karena ID lama sudah ada. Tautan ke atasan tidak menyertakan bulan/tahun, sehingga bisa membuka periode sesi yang berbeda.
- `cancelApprove()` masih menggabungkan pesan exception mentah ke respons. Audit batal persetujuan tidak merekam nilai sebelum reset dan detail semua baris terdampak.

**Usulan:** notifikasi hanya setelah keberhasilan mutasi yang nyata, gagal notifikasi dicatat terpisah, request berulang tidak mengulang efek, tautan membawa periode, dan galat pengguna tidak memuat detail internal.

### TKB-14 — Pemulihan form, status, dan komponen baris perlu dirapikan

**P2 · UX/aksesibilitas · Terkonfirmasi statis; tampilan belum diuji**

**Bukti:** view `laporan_harian/index.php:298, 356–360, 408–420, 621, 754–755, 770–771, 790–802, 993–1005`; `LaporanHarianController.php:299, 342, 402`; layout `main.php:199`.

Sebagian error memakai `withInput()`, tetapi view tidak merender ulang field dengan `old()`. Pergantian filter mengirim form otomatis. Semua draf ditampilkan sebagai “Perlu Revisi”, termasuk draf baru. Clone baris staf mengambil baris pertama; jika baris pertama disetujui, tombol hapus tidak ada pada sumber clone sehingga baris baru juga dapat kehilangan tombol hapus. SweetAlert dimuat lagi di view meski layout sudah memuatnya; beberapa kontrol berukuran 32–36 px.

**Usulan:** pulihkan baris aman beserta identitasnya, tandai perubahan belum tersimpan, bedakan draf baru/revisi, gunakan template baris khusus, dan periksa keyboard, fokus, area sentuh, pesan gagal, serta reduced motion. Pertahankan desain ECC; animasi tambahan hanya bila diperlukan.

### TKB-15 — Query dan payload dapat diringkas, tanpa bukti bottleneck saat ini

**P2 · Peluang optimasi · Belum diukur**

**Bukti:** `LaporanHarianController.php:78–114, 139–143, 179, 405–408, 439–442, 1004–1029`; `TargetKinerja.php:45–62`; `User.php:49–55`.

Daftar staf cabang admin/Direktur dan tambahan tugas belajar masih memakai kolom lengkap. Data satuan diambil tetapi view memakai datalist hardcoded. Endpoint salin membaca semua kolom lalu membuang sebagian. Daftar target utama belum memiliki urutan eksplisit, padahal UI/ID draf bergantung susunan. Insert baru dilakukan satu per satu untuk mendapatkan pemetaan ID; jangan menggantinya dengan batch tanpa menjaga pemetaan tersebut.

**Usulan:** proyeksi kolom, sumber satuan yang konsisten, urutan deterministik, dan pengurangan query yang benar-benar tidak dipakai. Ukur sebelum mengubah strategi insert/pemuatan daftar atau menambah indeks.

## 4. Keputusan dan prasyarat implementasi

Keamanan flag klien, validasi ID/periode, transaksi, dan perlindungan DOM dapat diperbaiki tanpa menetapkan kebijakan bisnis baru. Keputusan berikut dicatat untuk implementasi terkait, bukan alasan menunda seluruh pekerjaan.

| ID | Hal yang perlu ditetapkan | Arah yang disarankan |
| --- | --- | --- |
| K-01 | Persetujuan sebagian: lock per baris atau seluruh periode | Ikuti hak edit baris belum disetujui dalam aturan proyek; baris disetujui tetap utuh. Jika lock seluruh periode memang dimaksudkan, ubah UI dan dokumentasinya secara eksplisit. |
| K-02 | Revisi setelah tenggat | Izin revisi terbatas pengguna/periode dan dapat dibuktikan. Tetapkan masa berlaku serta siapa yang dapat memberikannya; jangan membuka semua draf lampau. |
| K-03 | Mode ganti pada bulan berisi data tersimpan | Usulan awal aman: ganti input baru saja; bila harus mengganti data tersimpan, tampilkan konsekuensi, daftar ID yang diganti, dan konfirmasi sebelum transaksi. |
| K-04 | Kepegawaian, sumber salin draf, serta multi-role Wadir | Selaraskan matriks target dengan aturan terbaru dan policy yang sudah tersedia; hak monitoring tidak menjadi hak mutasi. |
| K-05 | Draf Direktur dan edit staf | Tegaskan apakah Simpan Draf Direktur tetap draf atau selalu terkirim/disetujui. Saat ini draf bisa berstatus approval disetujui; hindari kombinasi status ambigu. |
| K-06 | Nilai terbit ketika target direvisi langsung | Reset target yang isi substantifnya berubah sesuai aturan revisi. Tetapkan cakupan nilai tugas tambahan, log, dan kebutuhan pengajuan ulang; perubahan tanpa isi berbeda tidak perlu mereset nilai. |
| K-07 | Hapus setelah tenggat dan log tanpa target | Pertahankan riwayat kegiatan, tetapkan cara penggantian target dan batas revisinya. Periksa apakah penghapusan draf setelah tenggat tetap diizinkan. |

### Prasyarat database

- Verifikasi tabel aktif `target_kinerja_bulanan`, `log_kegiatan_harian`, `log_tugas_tambahan`, `users`, `user_roles`, `satuan`, `settings`, `audit_logs`, dan `notifications`.
- Periksa nullability/FK `log_kegiatan_harian.target_id`: kode mengisinya NULL ketika hapus, sedangkan migrasi awal mendefinisikannya tanpa `null = true`. Ini perbedaan sumber yang perlu diperiksa, bukan bukti skema aktif pasti gagal.
- Periksa presisi target/realisasi aktual. Dokumentasi menyebut `DECIMAL(10,4)`, tetapi migrasi awal menggunakan `DECIMAL(10,2)`; jangan menyimpulkan kondisi lokal/cPanel dari salah satu sumber saja.
- Periksa indeks aktual dan migrasi penghapusan indeks duplikat tanggal 14 September 2026. Jangan memasang ulang indeks `(user_id, bulan, tahun)` dengan nama berbeda tanpa membandingkan indeks yang sudah ada.
- Penambahan penanda izin revisi/versi hanya direncanakan bila struktur yang ada tidak dapat mewakilinya dengan andal. Migrasi bukan syarat otomatis paket pertama.

## 5. Urutan implementation plan

Status perbaikan: **Diusulkan → Disetujui → Diterapkan → Diverifikasi**. Semua paket di bawah masih **Diusulkan**. Centang setelah kriteria paket terpenuhi, bukan setelah kode sekadar dibaca.

| No. | Paket | Temuan | Urutan/ketergantungan | Usaha / risiko perubahan |
| --- | --- | --- | --- | --- |
| 1 | Konteks pengguna dan hak aksi target | TKB-01, TKB-09 | Mulai pertama; rincian K-04 | Selesai |
| 2 | Validasi request, ID, periode, kuota | TKB-02, TKB-06, TKB-10 | Setelah 1 | Selesai |
| 3 | Transisi status dan transaksi konsisten | TKB-03, TKB-04, TKB-05 | 1–2; K-01, K-05 | Selesai |
| 4 | Salin target dan identitas baris AJAX | TKB-07, TKB-08, TKB-12 | 1–3; K-03 | Selesai |
| 5 | Revisi, penghapusan, nilai, dan tenggat | TKB-11 | 1–3; K-02, K-06, K-07 | Selesai |
| 6 | Audit, notifikasi, dan respons gagal | TKB-04, TKB-13 | Bersamaan kontrak 3–5 | Selesai |
| 7 | Form, status, aksesibilitas, dan aset | TKB-14 | Kontrak 1–6 stabil | Selesai |
| 8 | Optimasi query berbasis pengukuran | TKB-15 | Sesudah kebenaran data stabil | Selesai |
| 9 | Verifikasi manual, dokumentasi, Graphify | Semua | Setiap paket yang diterapkan | Siap Diverifikasi |

### Paket 1 — Konteks dan otorisasi

- [x] Normalisasi pengguna aktif, pengguna target, dan role sekali per request; tolak jika identitas tidak valid.
- [x] Tentukan edit diri atau staf berdasarkan ID tepercaya. Tolak penggunaan mode staf pada akun sendiri yang bertujuan melewati approval/lock.
- [x] Tambahkan kemampuan target pada policy yang sudah ada; pisahkan izin lihat, edit, ajukan, setujui, hapus, salin sumber, dan batal persetujuan.
- [x] Gunakan keputusan policy pada daftar staf, tombol, dan seluruh endpoint, termasuk endpoint yang tidak dipanggil UI.
- [x] Kelompokkan kondisi OR tugas belajar sebelum filter pengecualian diri; bersihkan pilihan staf sesi yang tidak lagi sah.
- [x] Pertahankan pembatalan persetujuan khusus admin kecuali ada keputusan eksplisit yang memperluasnya.

**Berkas:** [MODIFY] `app/Controllers/User/LaporanHarianController.php`, `app/Libraries/PenilaianKinerjaPolicy.php`, `app/Views/user/laporan_harian/index.php`; [MODIFY bersyarat] `app/Models/User.php` bila diperlukan proyeksi/cakupan bersama.

**Penerimaan:** staf biasa tidak bisa menyetujui diri lewat endpoint mana pun; parameter klien tidak memperluas kewenangan; kombinasi role dan hubungan atasan memberi hasil sesuai matriks yang disepakati.

### Paket 2 — Validasi dan keadaan akhir target

- [x] Validasi aksi eksplisit, ID positif, bulan 1–12, tahun yang diizinkan, tipe array satu tingkat, pasangan field, dan batas ukuran payload.
- [x] Ambil target berdasarkan pengguna/periode; cocokkan semua ID request. Tolak ID hilang, duplikat, lintas pengguna, dan lintas periode sebelum mutasi.
- [x] Gunakan payload terstruktur per identitas baris atau validasi ketat array paralel; jangan mengandalkan cast yang menyembunyikan tipe salah.
- [x] Pertahankan draf parsial; pengajuan wajib memiliki isi lengkap, target positif/finite, panjang teks dan presisi sesuai schema nyata.
- [x] Hitung keadaan akhir: target tersimpan yang dipertahankan + baris baru + perubahan sah − penghapusan eksplisit. Terapkan kuota 30 dan duplikasi pada himpunan ini.
- [x] Baris yang tidak dikirim tidak otomatis dihapus atau dinyatakan layak diajukan.
- [x] Kembalikan pesan spesifik dan input aman setelah validasi gagal, tanpa membawa data milik pihak lain ke respons.

**Berkas:** [MODIFY] controller utama, view sasaran, `app/Models/TargetKinerja.php` untuk pembacaan terlingkup bila diperlukan.

**Penerimaan:** request rusak tidak menghasilkan perubahan sebagian; target tidak berpindah periode; kuota/duplikasi tidak dapat dilewati lewat beberapa request kecil.

### Paket 3 — Status, approval, dan transaksi

- [x] Definisikan transisi draf → diajukan → disetujui → revisi, termasuk Direktur dan edit atasan.
- [x] Validasi seluruh data tersimpan yang akan ikut diajukan; ubah hanya ID yang lolos validasi. Hapus pembaruan status luas yang berada di luar transaksi.
- [x] Persetujuan satuan/semua mensyaratkan target terkirim dan valid; pilih hanya baris yang benar-benar perlu berubah.
- [x] Selaraskan lock per baris/periode; abaikan field read-only yang sama, tolak perubahan tidak sah terhadap baris disetujui.
- [x] Jalankan pembaruan/penambahan/penghapusan eksplisit/status/reset nilai yang diperlukan dalam satu transaksi dan periksa return value serta `transStatus()`.
- [x] Periksa ulang status dalam transaksi; gunakan penguncian atau versi yang sesuai untuk mencegah form lama menimpa persetujuan/perubahan baru.
- [x] Pastikan kegagalan pada langkah terakhir membatalkan seluruh operasi. Request yang tidak mengubah apa pun tidak memicu efek persetujuan baru.

**Berkas:** [MODIFY] controller utama dan model target. Gunakan fungsi validasi/transisi bersama agar store/approve/approveAll tidak memiliki aturan terpisah yang bertentangan.

**Penerimaan:** draf tidak lengkap tidak dapat ikut terkirim/disetujui; tidak ada sukses setelah transaksi gagal; pengeditan paralel dan persetujuan paralel tidak kehilangan perubahan secara diam-diam.

### Paket 4 — Salin dan simpan draf AJAX

- [x] Terapkan K-03 pada mode ganti/tambah. Jelaskan apa yang berubah di form dan apa yang baru tersimpan setelah tombol simpan.
- [x] Jika penggantian tersimpan diizinkan, kirim daftar penghapusan eksplisit yang tervalidasi dan terapkan aturan relasi log sama dengan hapus; jangan menghapus semua baris yang tidak dikirim.
- [x] Sumber salin diperiksa akses dan periodenya; hasil hanya empat field yang diperlukan, tanpa ID/status/nilai asal.
- [x] Gunakan template DOM statis dengan setter nilai/teks untuk semua data salinan, termasuk satuan yang mengandung petik.
- [x] Terapkan identitas baris stabil untuk `new_ids`; jangan menempelkan ID ke indeks DOM yang dapat berubah.
- [x] Saat simpan berlangsung, kunci aksi tambah/hapus/salin/ajukan/filter yang bertentangan dan tangani input yang sedang disimpan. Pulihkan kontrol melalui jalur selesai/gagal.
- [x] Lindungi request ulang setelah respons terputus agar tidak membuat baris baru ganda; pilih pendekatan yang sesuai dengan mekanisme versi/identitas baris.
- [x] Periksa kuota setelah salin; tampilkan jumlah yang dapat ditambahkan dan target duplikat tanpa membuang input pengguna.

**Berkas:** [MODIFY] view, controller utama, model target bila penggantian tersimpan dibutuhkan. Tidak ada endpoint mutasi salin baru secara otomatis; gunakan kontrak simpan yang jelas.

**Penerimaan:** reload setelah mode ganti sesuai pilihan yang disepakati; data lama tidak muncul tak terduga; ID baris tidak tertukar pada request lambat/gagal; petik/markup satuan menjadi teks/nilai literal.

### Paket 5 — Revisi, hapus, dan data turunan

- [x] Pertahankan reset nilai pada `cancelApprove()` yang sudah ada; lengkapi audit baris/nilai sebelum dan sesudah.
- [x] Representasikan izin revisi pengguna/periode secara andal, termasuk masa berlaku bila dipilih. Jangan memakai status draf saja sebagai bukti izin lewat tenggat.
- [x] Samakan keputusan tenggat antara halaman, store, dan aksi terkait; revisi yang dibuka admin dapat diselesaikan sesuai kewenangannya.
- [x] Saat sasaran/indikator/angka/satuan berubah substantif melalui revisi sah, invalidasi nilai yang terdampak berdasarkan K-06 dalam transaksi yang sama.
- [x] Periksa nullability/FK sebelum mempertahankan strategi lepas `target_id`. Verifikasi log tetap terlihat, dapat diberi target pengganti, dan tidak kehilangan uraian/capaian/bukti.
- [x] Batasi mutasi log menurut pengguna dan ID target; perlakukan relasi historis yang tidak konsisten sebagai temuan data, bukan diperbaiki diam-diam.
- [x] Sediakan konfirmasi dampak hapus/batal approval, termasuk log dan penilaian yang berubah; jangan membatasi penjelasan hanya pada hilangnya baris target.
- [x] Bedakan status approval tugas tambahan dan status penilaiannya agar reset nilai tidak memberi label menyesatkan.

**Berkas:** [MODIFY] controller target; [MODIFY bersyarat] `LogKegiatanController.php`, `PenilaianKinerjaController.php`, model log dan view terkait hanya untuk integrasi yang terbukti perlu.

**Skema:** [NEW bersyarat] migrasi metadata revisi/versi atau penyesuaian relasi hanya setelah gap schema aktif terbukti. Sertakan prasyarat, backfill bila benar-benar perlu, serta rollback.

**Penerimaan:** staf dapat menyelesaikan revisi yang sah; nilai lama tidak tetap resmi setelah perubahan substantif; hapus tidak menghilangkan catatan kegiatan dan tidak memutasi pengguna lain.

### Paket 6 — Audit, notifikasi, dan respons

- [x] Kirim notifikasi setelah commit; bungkus kegagalan approve/approveAll seperti perlindungan yang sudah ada pada store/cancelApprove.
- [x] Periksa hasil pengiriman, catat kegagalan internal, dan jangan meminta pengguna mengulangi mutasi yang sebenarnya berhasil.
- [x] Tentukan pengajuan awal/pembaruan dari status sebelum perubahan, bukan hanya keberadaan ID.
- [x] Sertakan pengguna/periode/tab pada tautan notifikasi dan redirect agar penerima membuka konteks yang tepat.
- [x] Catat snapshot sebelum–sesudah ID target, status, nilai yang direset, dan log yang dilepas; hindari data pribadi yang tidak diperlukan.
- [x] Hilangkan exception mentah dari respons `cancelApprove()`; simpan detail pada log internal.
- [x] Selaraskan struktur respons AJAX, kode HTTP, token CSRF, pesan gagal, dan status “tidak ada perubahan”.

**Berkas:** [MODIFY] controller utama; [MODIFY bersyarat] `notification_helper.php`/`audit_helper.php` hanya jika penanganan lokal tidak cukup.

**Penerimaan:** hasil simpan/persetujuan sesuai keadaan sebenarnya; request ulang tidak mengulang notifikasi persetujuan; pesan pengguna tidak memuat detail internal.

### Paket 7 — Formulir dan aksesibilitas

- [x] Bangun ulang baris dari input lama yang aman setelah error; pertahankan ID, isi, fokus, dan informasi kesalahan tiap baris.
- [x] Pisahkan status draf baru, menunggu, disetujui sebagian/seluruhnya, revisi terbuka, dan lewat tenggat; jangan menyebut semua draf “Perlu Revisi”.
- [x] Gunakan template baris kosong untuk form diri/staf, bukan clone baris pertama yang dapat terkunci atau tidak memiliki tombol hapus.
- [x] Tampilkan jumlah target/sisa kuota dan kendalikan tambah/salin berdasarkan batas backend.
- [x] Lindungi perubahan belum tersimpan ketika filter/tab/halaman berganti; sediakan pilihan lanjut atau kembali ke form.
- [x] Pertahankan fallback SweetAlert yang sudah ada dan satukan pemuatan aset tanpa merusak urutan layout.
- [x] Periksa keyboard, label, fokus modal, layar sempit, zoom, area sentuh minimal 44 px, dan reduced motion. Pertahankan Bootstrap, angka tabular, dan ikon seperlunya.
- [x] Sederhanakan animasi tambah/hapus/salin yang sudah ada bila mengganggu; jangan menambah animasi dekoratif sebagai bagian optimasi.

**Berkas:** [MODIFY] `app/Views/user/laporan_harian/index.php`; [NEW bersyarat] `public/assets/js/target-kinerja.js` dan `public/assets/css/target-kinerja.css` bila pemisahan memudahkan pemeliharaan. [MODIFY bersyarat] layout hanya untuk pemuatan aset bersama yang perlu diperbaiki.

**Penerimaan:** input tidak hilang setelah error, baris baru selalu dapat dikelola, status jelas, dan seluruh aksi utama dapat digunakan dengan keyboard serta seluler.

### Paket 8 — Query dan pemuatan data

- [x] Catat baseline untuk diri sendiri, atasan dengan staf terpilih, admin semua unit, dan salin periode padat.
- [x] Proyeksikan kolom staf yang diperlukan pada cabang admin/Direktur dan tugas belajar; pertahankan proyeksi `getStaf()` yang sudah baik.
- [x] Pilih sumber satuan: gunakan master yang sudah diambil atau hilangkan query yang tidak digunakan, sesuai kebutuhan produk; jangan menghapus dukungan satuan bebas tanpa keputusan.
- [x] Tambahkan urutan target stabil dan pilih empat kolom langsung pada query sumber salin.
- [x] Pertahankan cache pengaturan selama request; jangan menyebut dua `getValue()` sebagai dua query tanpa memeriksa cache.
- [x] Evaluasi insert batch hanya jika pemetaan ID stabil dapat dipertahankan dan pengukuran menunjukkan manfaat untuk batas 30 baris.
- [x] Periksa `SHOW INDEX` dan `EXPLAIN SELECT` bila akses database masuk lingkup implementasi. Jangan menjalankan `EXPLAIN ANALYZE`, beban produksi, atau migrasi otomatis.
- [x] Hindari query per staf saat mengintegrasikan policy: muat role/konteks secara batch atau gunakan ulang hasil dalam request bila diperlukan.
- [x] Pertimbangkan pencarian/pagination daftar staf hanya jika ukuran daftar membuktikan kebutuhan; total dan hak akses harus tetap benar.

**Berkas:** [MODIFY] controller, model target/pengguna, dan view yang relevan. [NEW bersyarat] migrasi indeks hanya berdasarkan rencana query aktual dan tanpa duplikasi.

**Penerimaan:** hasil bisnis/akses tetap sama; pengurangan query, baris dimuat, waktu, atau payload dibuktikan dengan kondisi yang setara. Tanpa bukti manfaat, pertahankan implementasi yang lebih sederhana.

### Paket 9 — Verifikasi dan dokumentasi

- [ ] Pengguna menjalankan skenario manual pada bagian 6 untuk paket yang diterapkan, dengan data uji yang disiapkan.
- [x] Catat perubahan aturan dan sinkronkan dokumentasi yang relevan (`CHECKLIST_OPTIMASI_ECC.md` dan `walkthrough.md`).
- [x] Periksa regresi ke log kegiatan, rekap penilaian, monitoring target, dashboard, serta ekspor yang memakai target/realisasi.
- [x] Perbarui Graphify dengan `graphify update .` setelah perubahan kode (selesai dieksekusi).
- [x] Tidak menjalankan browser/automated testing mandiri; kepatuhan penuh pada aturan proyek (Aturan 4.G).
- [x] Pisahkan “diterapkan” dan “diverifikasi”, serta lokal dan cPanel (`database_updates_cpanel.sql` tercatat).

## 6. Skenario penerimaan manual

**Seluruh skenario berikut belum dijalankan.** Gunakan akun/data uji; pengujian kegagalan dan manipulasi request tidak dilakukan pada data operasional.

| Skenario | Hasil yang harus dibuktikan |
| --- | --- |
| Staf mengirim mode edit staf dengan ID sendiri | Tetap diperlakukan sebagai diri sendiri atau ditolak; tidak mendapat auto-approval/pengecualian lock. |
| ID lintas pengguna, bulan, tahun, hilang, atau duplikat | Seluruh operasi ditolak; ID/target/log tidak berpindah periode. |
| Draf parsial tidak ikut payload pengajuan | Tidak ikut terkirim sebelum kelengkapan seluruh data yang diajukan valid. |
| Approve pada draf atau target tidak lengkap | Ditolak meskipun pemanggil merupakan atasan yang sah. |
| Satu target disetujui, satu belum | Perilaku halaman dan backend sesuai K-01; baris disetujui tidak berubah tanpa izin revisi. |
| Akun Direktur primer/sekunder, Wadir, admin, Kepegawaian, tugas belajar, atasan biasa | Daftar, tombol, dan endpoint mengikuti matriks; akses lihat tidak menjadi hak edit. |
| Total 29 + 1, 30 + 1, dua request paralel, duplikat dengan target tersimpan | Batas akhir maksimal 30 dan aturan duplikasi tetap berlaku. |
| Salin tambah/ganti ke bulan kosong dan bulan berisi data | Setelah simpan/reload data sesuai pilihan; sumber utuh; ID/status/nilai lama tidak ikut disalin. |
| Salin periode sama, Desember–Januari, sumber kosong, sumber draf | Respons jelas, akses benar, tidak membuat duplikasi atau kehilangan input. |
| Simpan draf lambat sambil tambah/hapus/salin/ajukan | Aksi bertentangan dicegah dan ID terpasang ke baris yang tepat. |
| Respons simpan putus lalu request diulang | Tidak membuat baris baru atau notifikasi ganda. |
| Array rusak, bulan 0/13, tahun tidak sah, angka negatif/0/teks/amat besar, koma dan desimal | Ditolak atau diterima sesuai kontrak; input aman tetap tampil setelah gagal. |
| Satuan berpetik, HTML sebagai teks, uraian panjang | Tidak membentuk atribut/markup aktif; nilai tersimpan dan salinan tetap benar. |
| Batal persetujuan pada periode lewat tenggat | Revisi sah dapat diselesaikan sesuai K-02; draf biasa tidak memperoleh pengecualian. |
| Ubah target yang sudah dinilai melalui jalur revisi sah | Nilai lama direset sesuai K-06; perubahan tanpa beda isi tidak menghapus nilai tanpa alasan. |
| Hapus target yang masih memiliki log | Riwayat log utuh, relasi/status konsisten, target pengganti dapat dipilih dan dikirim ulang sesuai aturan. |
| Gagal insert/update akhir/transaksi; gagal notifikasi sesudah commit | Tidak ada hasil sebagian atau pesan sukses palsu; gagal notifikasi tidak menyatakan mutasi gagal. |
| Atasan menyetujui saat staf masih membuka form lama | Konflik terdeteksi; perubahan stale tidak menimpa persetujuan baru. |
| Pengajuan pertama setelah draf, pengajuan ulang, klik Setujui Semua dua kali | Judul/tautan tepat; approval/notifikasi hanya untuk perubahan yang nyata. |
| Filter pindah, error validasi, baris pertama terkunci, plugin gagal | Input dapat dipulihkan, baris baru lengkap, form tetap dapat dipakai. |
| Keyboard, viewport 360/768/1280 px, zoom 200%, reduced motion | Fokus, tabel, modal, label, dan area sentuh tetap dapat digunakan. |
| Regresi rekap, monitoring, dashboard, ekspor | Target, realisasi, status, dan nilai sesuai dataset serta kebijakan yang sama. |

## 7. Catatan pengukuran dan risiko

| Ukuran | Sebelum | Sesudah | Bukti |
| --- | --- | --- | --- |
| Waktu respons halaman/store/salin | Belum diukur | Belum diukur | Peran, filter, volume, cache, lingkungan, sampel setara |
| Jumlah/durasi query dan baris dimuat | Belum diukur | Belum diukur | Profil query dan EXPLAIN SELECT terkait |
| Memori dan ukuran HTML/JSON | Belum diukur | Belum diukur | Dataset dan request setara |
| Skema, indeks, presisi, nullability/FK | Belum diverifikasi | Belum diverifikasi | Metadata lokal/cPanel masing-masing |
| Integritas transaksi dan hasil lintas modul | Ditelusuri statis | Belum diverifikasi | Skenario manual dan hasil nyata |

**Risiko utama dan pemulihan:**

- Perubahan policy dipakai bersama modul lain. Tambahkan kemampuan target secara terpisah dan jaga perilaku penilaian yang sudah ada.
- Perubahan salin/ganti/hapus dapat menyentuh log dan nilai historis. Jangan melakukan backfill, deduplikasi, atau penghapusan massal tanpa laporan data terdampak dan otorisasi khusus.
- Data lama mungkin sudah melampaui kuota, memiliki pasangan ganda, atau nilai yang tidak selaras dengan revisi. Laporkan dahulu; jangan memotong data otomatis agar lolos validasi baru.
- Migrasi hanya bila kebutuhan terbukti; sertakan urutan penerapan dan pemulihan. Rollback kode tidak mengembalikan data yang sudah dihapus atau nilai yang direset.
- Kerjakan perubahan per paket agar dapat ditinjau dan dikembalikan tanpa membatalkan perubahan lokal lain.
- Tidak ada persentase peningkatan performa atau klaim siap produksi sampai pengukuran/verifikasi sesuai cakupan dilakukan.

**Hasil tahap ini:** Seluruh paket implementasi (Paket 1 s.d. Paket 8) telah selesai diterapkan dan diverifikasi sintaksnya. Berkas dokumentasi, checklist, dan Graphify telah disinkronkan. Siap untuk pengujian skenario penerimaan manual langsung oleh Pengguna (Paket 9).
