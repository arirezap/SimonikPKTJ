# Panduan Audit & Optimasi Evidence Command Center (ECC)

**Versi panduan:** 3.2.0 · **Diperbarui:** 13 Oktober 2026

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
8. **Laporkan hasil:** tinjau diff, catat pemeriksaan yang benar-benar dilakukan, risiko tersisa, dan langkah uji pengguna. Pembacaan kode tidak boleh dilaporkan sebagai pengujian runtime.

## 4. Delapan pilar pemeriksaan

| No | Pilar | Yang diperiksa |
| --- | --- | --- |
| 1 | Struktur dan ketepatan kode | MVC, kontrak method, validasi tipe/data, kode berulang/tidak terpakai, kompatibilitas PHP dan CI4 terpasang |
| 2 | Logika bisnis dan integritas data | Alur status, hak penilai, batas periode, desimal, transaksi, pengiriman ganda, perubahan bersamaan |
| 3 | Pemakaian ulang dan dependensi | Helper/trait, konsistensi rumus, pemuatan aset, fallback, versi terkunci dan dependensi yang digunakan |
| 4 | Keamanan | Route privat, CSRF, XSS, IDOR, multi-role, unggahan, sesi, header, pembatasan permintaan |
| 5 | Performa | Jumlah/bentuk kueri, indeks, baris dimuat, memori, cache, ukuran respons, polling, ekspor |
| 6 | Penanganan kegagalan dan jejak aktivitas | Data kosong, pembagian nol, gagal transaksi/notifikasi, galat dan audit tanpa membocorkan rahasia |
| 7 | Antarmuka dan aksesibilitas | Responsivitas, keterbacaan, sentuhan, keyboard/fokus, label, keadaan memuat/kosong/gagal |
| 8 | Bahasa dan operasional | Identitas ECC, istilah staf, pesan sederhana, lokal-produksi, cadangan, pemulihan, retensi |

Gunakan pilar yang relevan; tulis alasan jika tidak berlaku. Pemeriksaan kerentanan dependensi terbaru membutuhkan sumber resmi terkini dan tanggal pemeriksaan. `composer.lock` saja tidak membuktikan dependensi aman. Jangan memperbarui paket massal sebagai efek samping audit.

## 5. Checklist lintas modul

### A. Keamanan akses dan masukan

- [ ] Route privat eksplisit, terlindungi `auth`, dan auto-routing dinonaktifkan. Periksa controller serta pemanggilnya; daftar route saja belum membuktikan endpoint bekerja.
- [ ] Mutasi memakai POST sesuai aturan ECC dan CSRF. AJAX memperbarui token pada alur berhasil/gagal yang relevan; perhatikan permintaan bersamaan.
- [ ] Backend memeriksa akses halaman, detail AJAX, ekspor, dan mutasi. Menu tersembunyi atau `isAJAX()` bukan pengganti otorisasi.
- [ ] ID objek diperiksa bersama pemilik, periode/tanggal, dan hubungan atasan-staf. Pada batch, seluruh ID diperiksa; ID asing tidak boleh ikut tersimpan.
- [ ] Peran primer/sekunder diperhitungkan; perubahan profil/unit tidak memberikan hak tambahan di luar kebijakan yang diizinkan.
- [ ] Kueri memakai parameter binding; kolom dan arah pengurutan memakai daftar yang diizinkan. `$allowedFields` sesuai kebutuhan; jangan menerima seluruh payload mentah.
- [ ] Keluaran di-escape sesuai konteks HTML, atribut, URL, atau JavaScript. Serialisasi data ke JavaScript menangani kutip dan backtick dengan benar.
- [ ] Tautan bukti hanya menerima skema `http://` atau `https://` dan divalidasi di backend serta saat ditampilkan.
- [ ] Foto dibatasi 2 MB di klien/backend, diperiksa sebagai gambar dan MIME yang diizinkan, diberi nama acak, serta dilindungi dari eksekusi skrip. Hapus berkas lama hanya di direktori unggahan dan lindungi aset bawaan.
- [ ] Login meregenerasi sesi, logout mencabut sesi/token, pesan login seragam, pembatasan login sesuai konfigurasi, dan siklus remember-me aman.
- [ ] Data sensitif tidak diteruskan ke view, detail, ekspor umum, atau audit log. Header keamanan dan atribut kuki diperiksa pada lingkungan yang benar.

### B. Data, transaksi, dan kondisi batas

- [ ] Normalisasi koma desimal memakai `str_replace(',', '.', trim((string) $val))`, lalu validasi angka/rentangnya. Jangan mengubah input tidak valid menjadi nol diam-diam.
- [ ] Bedakan `NULL`, kosong, nol, draf, terkirim, disetujui, dan terbit. Nol yang sudah dinilai tidak otomatis sama dengan belum dinilai.
- [ ] Presisi target/realisasi dan nilai sesuai skema serta kebijakan ECC. Periksa pembulatan pada penyimpanan, agregat, layar, Excel, dan PDF.
- [ ] Mutasi beberapa langkah memakai transaksi dan memeriksa hasil/statusnya. `try...catch` saja tidak cukup jika driver melaporkan kegagalan tanpa exception.
- [ ] Status dan otorisasi diperiksa kembali saat menyimpan. Tombol nonaktif tidak mencegah pengiriman ganda, perubahan bersamaan, atau permintaan yang dimanipulasi.
- [ ] Gagal notifikasi tidak membatalkan penyimpanan utama yang berhasil; transaksi gagal tidak menghasilkan pesan sukses atau notifikasi persetujuan.
- [ ] Perubahan unit menyinkronkan `unit_id` dan `unit`; relasi atasan mencegah diri sendiri serta siklus hierarki. Periksa nasib catatan terkait ketika data induk dihapus.
- [ ] Preservasi data pegawai pensiun, mutasi, dan nonaktif: Pegawai dengan status `pensiun`, `mutasi_keluar`, atau `nonaktif` tetap muncul pada dashboard dan monitoring periode lampau selama periode tersebut sebelum/pada `tmt_selesai`. Data riwayat kinerja historis tidak boleh terhapus atau hilang saat status pegawai berubah.
- [ ] Riwayat penugasan multi-periode (rotasi jabatan, pindah unit, dan pergantian pimpinan): Perubahan atasan langsung, unit kerja, atau jabatan dicatat secara temporal (`riwayat_atasan`). Kueri rekap, cetak dokumen SKP, dan filtering dashboard mengacu pada penugasan periode bersangkutan (`getPenugasanPeriode()`), bukan menimpa data historis secara destruktif.
- [ ] Periode, zona waktu, pergantian bulan/tahun, tahun kabisat, hari libur, dan tenggat memakai acuan konsisten.

### C. Antarmuka dan bahasa

- [ ] Gunakan Bootstrap 5.3, Bootstrap Icons seperlunya, angka tabular, dan ukuran menurut `design.md`. Hindari perubahan desain di luar sasaran.
- [ ] Periksa desktop/seluler, teks panjang, tabel lebar, kontrol tetap, keadaan kosong/memuat/gagal, dan pemulihan input setelah validasi gagal (`old()` untuk field yang aman; jangan mengisi ulang kata sandi).
- [ ] Kontrol dapat digunakan lewat keyboard, fokus terlihat, label terbaca, status tidak hanya dibedakan dengan warna, dan gerakan menghormati `prefers-reduced-motion`.
- [ ] Bedakan ukuran visual tombol kompak dari area sentuhnya; periksa area sentuh seluler minimal 44px menurut pedoman proyek.
- [ ] SweetAlert2 memiliki pemeriksaan ketersediaan dan fallback `confirm()`/`alert()`. Unduhan menunggu hasil sebenarnya, bukan waktu tunggu buatan.
- [ ] Gunakan **Evidence Command Center (ECC)**, **staf**, **Kata Sandi**, **Alamat Email**, **Keluar**, dan **Simpan Profil**.
- [ ] Teks pengguna singkat, tenang, dan memberi langkah berikutnya tanpa galat internal. Contoh: “Gagal menyimpan laporan. Silakan coba lagi.”
- [ ] Dialog memakai judul 2–4 kata, satu kalimat penjelasan, dan aksi jelas: “Hapus Kegiatan?”, “Kegiatan ini akan dihapus.”, “Ya, Hapus”, “Batal”.
- [ ] Boleh menambahkan atau mengganti animasi sederhana saja pada halaman hanya jika dibutuhkan. Periksa terlebih dahulu animasi yang sudah ada dan sesuaikan secara hemat sesuai kebutuhan (hindari animasi berlebihan dan jaga kesederhanaan visual).

## 6. Kueri, basis data, dan performa

**Prinsip:** optimasi harus mempertahankan hasil dan hak akses. Dugaan kode lambat belum menjadi bukti peningkatan performa.

### Langkah pengukuran

1. Tentukan kasus setara: route/aksi, peran, filter, periode, volume data, kondisi cache, lingkungan, dan jumlah sampel.
2. Jika pengukuran runtime diizinkan, catat waktu respons, jumlah/waktu kueri, baris dimuat, memori puncak, dan ukuran respons sesuai kebutuhan. Pengujian beban produksi membutuhkan izin khusus.
3. Jika akses basis data tersedia dalam lingkup pekerjaan, baca skema/indeks aktual. Gunakan `EXPLAIN` pada SELECT yang mewakili kasus lambat; nilai rencana bersama ukuran tabel dan selektivitasnya.
4. Jangan memakai `EXPLAIN ANALYZE` sebagai pemeriksaan ringan: perintah tersebut mengeksekusi kueri dan dapat membebani sistem. Evaluasi izin dan versi mesin terlebih dahulu.
5. Sesudah perbaikan, bandingkan hasil bisnis dan ukuran performa dengan kondisi setara. Tanpa pengukuran, tulis “perkiraan dari pembacaan kode; belum diukur”.

### Daftar periksa

- [ ] **N+1 dan pemuatan berlebih:** telusuri kueri dalam loop, helper berulang, kolom tidak terpakai, serta data setahun untuk tampilan satu bulan. Dua kueri besar belum tentu efisien.
- [ ] **Agregasi:** pertimbangkan `SUM`/`COUNT`/`GROUP BY` atau batch seperlunya. Periksa penggandaan baris akibat join dan perubahan hasil LEFT JOIN akibat filter tabel kanan.
- [ ] **Filter waktu:** tinjau `YEAR()`/`MONTH()` pada kolom filter dan peluang rentang `>= awal` serta `< awal_periode_berikutnya`. Buktikan lewat rencana kueri dan kesetaraan hasil.
- [ ] **Indeks:** cocokkan susunan kolom dengan WHERE/JOIN/ORDER BY dan volume data. Sebelum menghapus indeks, bandingkan urutan, prefix, keunikan, serta kebutuhan foreign key; nama mirip tidak cukup.
- [ ] **Pagination:** batasi daftar besar dan ekspor. Gunakan urutan stabil; pastikan total, filter, dan halaman benar.
- [ ] **Batas akses kosong:** bedakan array ID kosong yang berarti tidak ada data diizinkan dari `null` yang berarti semua data. Jangan melepas filter akses ketika array kosong.
- [ ] **Cache:** periksa cakupan request/pengguna/periode, invalidasi semua mutasi termasuk batch, dan fallback saat pembacaan pengaturan gagal. Jangan membagikan data privat lintas pengguna.
- [ ] **Sesi:** ukur apakah permintaan dari sesi yang sama saling menunggu. File, Database, dan Redis handler CI4 memiliki mekanisme penguncian; penggantian driver bukan jaminan hilangnya antrean. Evaluasi durasi pekerjaan dan kapan sesi terakhir ditulis.
- [ ] **Ekspor/cadangan:** periksa batas memori/waktu, pembatasan unduhan, pemrosesan bertahap, penanganan gagal, dan berkas sementara. Unduhan berhasil belum membuktikan cadangan dapat dipulihkan.
- [ ] **Frontend:** periksa polling saat tab tersembunyi, permintaan ganda, pencarian terlalu sering, ukuran DOM/aset, dan cache aset. Jangan menyimpan respons privat dalam cache publik.
- [ ] **Migrasi:** perubahan skema memakai migrasi dengan prasyarat, dampak data lama, waktu penguncian, dan pemulihan. Jangan menandai migrasi berjalan tanpa bukti perubahan skemanya.

### Catatan sebelum–sesudah

| Ukuran | Sebelum | Sesudah | Kondisi dan bukti |
| --- | --- | --- | --- |
| Ketepatan hasil dan akses | Belum diperiksa | Belum diperiksa | Skenario, peran, periode, baris, nilai total |
| Waktu respons/kueri | Belum diukur | Belum diukur | Satuan, jumlah sampel, kondisi cache |
| Jumlah kueri/baris dimuat | Belum diukur | Belum diukur | Route/aksi dan volume data |
| Memori/ukuran respons | Belum diukur | Belum diukur | Lingkungan dan metode pengukuran |

Isi hanya ukuran yang relevan. Jangan mengarang persentase penghematan atau menjanjikan O(1) untuk pemrosesan seluruh kumpulan data.

## 7. Peta cakupan modul

Konfirmasi keberadaan berkas/route pada revisi yang diperiksa. Semua modul mengikuti checklist lintas modul. Hitung route, tabel, dan migrasi dari bukti saat audit, bukan angka tetap dalam dokumen.

| Sasaran | Titik masuk utama | Pemeriksaan khusus |
| --- | --- | --- |
| Autentikasi/sesi | `app/Controllers/Auth.php`, `app/Filters/AuthFilter.php` | Login/logout, remember-me, regenerasi, fingerprint, pembatasan login |
| Dashboard | `app/Controllers/DashboardController.php`, `app/Controllers/Admin/Dashboard.php`, `app/Controllers/User/Dashboard.php` | Cakupan institusi/unit/staf, pegawai mutasi/pensiun dengan TMT aktif, riwayat penugasan unit/jabatan periode terkait, filter tahun multi-periode (2025/2026), nilai terbit, predikat ≤ 25% (Perhatian Khusus) dan Belum Dinilai |
| Agregasi bersama | `app/Controllers/Traits/KinerjaBatchTrait.php`, `app/Controllers/Traits/EccDataTrait.php` | Seluruh pemanggil, data tahunan, tugas tambahan, rata-rata dan bulan bernilai nol |
| Target bulanan | `app/Controllers/User/LaporanHarianController.php`, `app/Models/TargetKinerja.php`, `app/Models/LaporanHarian.php` | Draf/pengajuan/persetujuan, salin periode, batas baris, Direktur, pembatalan dan dampak ke log |
| Kegiatan harian | `app/Controllers/User/LogKegiatanController.php`, `app/Models/LogKegiatanHarian.php`, `app/Models/LogTugasTambahan.php` | Target sah, pemilik/tanggal, draf masa depan, tenggat, revisi, bukti, kalender |
| Penilaian | `app/Controllers/User/PenilaianKinerjaController.php` | Target disetujui, hak penilai, draf/terbit/reset, kosong/nol, rentang 0–150 |
| Monitoring target | `app/Controllers/Kepegawaian/MonitoringTargetController.php` | Peran, pegawai pensiun/mutasi dengan TMT aktif, riwayat penugasan/atasan periode bersangkutan, belum mengisi, status campuran, bulanan/tahunan, layar dan ekspor |
| Monitoring penilaian | `app/Controllers/Kepegawaian/DashboardKepegawaian.php` | Kelengkapan, pegawai pensiun/mutasi dengan TMT aktif, riwayat penugasan/atasan penilai multi-periode, nilai terbit, predikat, hierarki, detail/Excel/PDF |
| Monitoring/remunerasi | `app/Controllers/Admin/MonitoringController.php`, `app/Controllers/Admin/RemunerasiController.php` | Sumber nilai, akses, mutasi pegawai, periode, pembatasan ekspor |
| Kelola pengguna | `app/Controllers/Admin/UserController.php`, `app/Models/User.php` | Peran, status_pegawai (aktif, mutasi, pensiun, nonaktif), tmt_selesai, riwayat penugasan periode (`riwayat_atasan`), impor/batch, duplikasi, reset, penghapusan, unggahan |
| Profil/direktori | `app/Controllers/Profile.php`, `app/Controllers/User/DaftarPegawaiController.php` | Field diubah/dilihat, smart lock atasan/unit bagi staf biasa, status pegawai (mutasi/pensiun), self-atasan, unit ganda, pemberian peran, foto, pencarian |
| Kelola tim | `app/Controllers/User/TimController.php` | Hak tambah/keluarkan staf, sinkronisasi riwayat atasan periode aktif, perubahan unit/jabatan pimpinan, siklus hierarki |
| Kontrak/pakta | `app/Controllers/User/KontrakController.php`, `app/Controllers/User/PaktaController.php` | Identitas, periode, penandatangan, data kosong, cetak A4 |
| LED/simulasi | `app/Controllers/EccController.php`, `app/Controllers/Admin/MasterDataController.php`, `app/Models/LedScore.php` | Prodi/unit, bukti, persetujuan, simulasi SPM/admin, skor/grafik |
| Master/hari libur | `app/Controllers/Admin/MasterDataController.php`, `app/Models/UnitKerja.php`, `app/Models/HolidayModel.php` | Duplikasi, hapus data berelasi, nama unit, API gagal/timeout/sinkronisasi berulang |
| Notifikasi/audit log | `app/Controllers/NotificationController.php`, `app/Controllers/Admin/AuditLogController.php`, `app/Helpers/audit_helper.php` | Pemilik, polling, CSRF, filter/pagination, data sensitif, biaya pencatatan |
| Pengaturan/pemeliharaan | `app/Controllers/Admin/SettingsController.php`, `app/Models/SettingModel.php`, `app/Filters/MaintenanceFilter.php` | Cache, nilai sah, akses admin, 503, cadangan, retensi |
| Shell/route/fitur lain | `app/Config/Routes.php`, `app/Config/Filters.php`, `app/Views/layouts/main.php`, `app/Views/layouts/sidebar.php` | Menu dan otorisasi, token, aset, route fitur tersembunyi, modul tambahan hasil pemetaan |

### Aturan bisnis yang harus dijaga

- Direktur dapat menyetujui otomatis dan merevisi targetnya sendiri. Staf dapat memperbarui target sebelum disetujui; sesudahnya berlaku penguncian/revisi.
- Wadir dapat memantau institusi, tanpa hak menyetujui/merevisi target staf atau menilai staf. Periksa kombinasi peran bila haknya bertentangan.
- Penilaian staf mensyaratkan seluruh target periode terkait disetujui. Reset mengembalikan `nilai_capaian` dan `status_penilaian` ke `NULL`.
- Monitoring kepegawaian mengikuti peran: `kepegawaian`, `admin`, `direktur`, `wadir`, `kabag`, `kabag_aak`, `kabag_kuk`. Hak melihat tidak otomatis memberi hak mengubah.
- Nilai belum terbit tidak muncul sebagai nilai resmi. Periksa rumus di layar/grafik/detail/Excel/PDF dan kontribusi tugas tambahan.
- **Pegawai pensiun, mutasi keluar, dan nonaktif:** Tetap diikutsertakan dalam rekapitulasi, dashboard, dan monitoring periode lampau berbasis tanggal batas TMT (`tmt_selesai >= periodMonth` atau `YEAR(tmt_selesai) >= tahun_kinerja`). Tampilkan lencana (*badge*) status yang jelas pada antarmuka pengguna tanpa mengaburkan data capaian yang sah.
- **Rotasi jabatan, pindah unit kerja, dan pergantian pimpinan (multi-periode):** Hubungan atasan langsung, unit, dan jabatan pegawai mengacu pada riwayat penugasan periode aktif (`riwayat_atasan` via `User::getPenugasanPeriode()`). Penilaian kinerja lampau, lembar cetak SKP, dan agregasi unit kerja masa lalu tetap terkunci sesuai struktur organisasi pada periode bersangkutan.
- **Persetujuan target dan penilaian kinerja saat pergantian pimpinan:** Pejabat yang berhak menyetujui target dan memberikan nilai capaian staf pada suatu periode adalah atasan yang aktif menjabat pada periode bulan/tahun bersangkutan. Pimpinan baru hanya berwenang memproses periode berjalan sejak awal masa penugasannya. Lembar cetak laporan PDF/Excel menyelaraskan pejabat penilai dan atasan penilai sesuai riwayat jabatan masa tersebut (PermenPAN-RB No. 6 Tahun 2022).
- **Proteksi integritas profil staf (*Smart Lock*):** Input pilihan atasan langsung dan unit kerja pada menu profil (`/profile`) dikunci bagi staf biasa. Penyesuaian hierarki penugasan dan rotasi mutasi dikelola secara terpusat oleh Admin atau Kepegawaian untuk menjamin konsistensi audit trail institusi.
- **Filter tahun dan penanganan nilai kosong/0 di dashboard:** Dropdown filter tahun wajib menghimpun seluruh riwayat tahun (misal: 2025 dan 2026). Capaian dengan nilai 0 atau belum berstatus terbit dilabeli **"Belum Dinilai"**, sedangkan daftar evaluasi perhatian khusus dibatasi secara ketat hanya untuk nilai ≤ 25% (tidak mencampur pegawai yang belum dinilai).
- **Konflik teridentifikasi saat penyusunan panduan:** `.agents/AGENTS.md` dan `PROJECT_CONTEXT.md` menetapkan “Baik” pada `> 90`, sedangkan skill ECC dan `design.md` memakai `>= 90`. Dokumen juga perlu membedakan nol yang sudah dinilai dari belum dinilai. Periksa apakah konflik masih ada; jangan mengubah ambang tanpa keputusan kebijakan pengguna.
- Skenario batas nilai: `NULL`, kosong, `0`, `25`, `25.01`, `75`, `75.01`, `89.99`, `90`, `90.01`, `100`, `100.01`, `150`, dan nilai di luar rentang. Tambahkan tanpa target, hanya tugas tambahan, sebagian belum dinilai, serta rata-rata tahunan dengan bulan bernilai nol.

## 8. Lingkungan dan operasional

- [ ] Catat versi PHP, CI4/dependensi terpasang, mesin basis data, zona waktu, dan perbedaan lokal-produksi. Versi terkunci bukan bukti kondisi server produksi.
- [ ] Periksa DocumentRoot, perlindungan `.env`/`.git`/`app`/`writable`, dan unggahan. Konfirmasi aturan Apache berlaku pada konfigurasi cPanel yang digunakan.
- [ ] Periksa mode produksi, detail galat/debug toolbar, HTTPS, header, dan kuki. Jangan menerapkan pengaturan produksi secara membabi buta pada lokal HTTP.
- [ ] Bandingkan migrasi, kolom, tipe, indeks, dan pengaturan relevan. Paritas lokal-cPanel pada audit lama bukan bukti kesamaan saat ini.
- [ ] Cadangan memiliki kontrol akses, lokasi aman, retensi, serta hasil uji pemulihan di lingkungan terpisah. Tombol unduh bukan bukti cron pencadangan aktif.
- [ ] Retensi log/notifikasi sesuai pengaturan, batas tanggal, transaksi, dan jejak audit. Jangan menjalankan pembersihan hanya untuk membuktikan tombol berfungsi.
- [ ] Rencana deployment menjelaskan urutan kode/skema, prasyarat, gangguan, pemeriksaan sesudahnya, dan pemulihan. Rollback skema tidak selalu mengembalikan data yang hilang.

## 9. Status, prioritas, dan bukti

### Status pemeriksaan

| Status | Makna |
| --- | --- |
| Belum diperiksa | Belum ada bukti sesi ini |
| Sesuai dari pembacaan kode | Implementasi yang ditelusuri sesuai; runtime belum dibuktikan |
| Terverifikasi | Pemeriksaan tercatat telah dijalankan dan sesuai; sebutkan metode/lingkungan |
| Perlu perbaikan | Masalah terkonfirmasi dengan pemicu dan bukti |
| Perlu konfirmasi | Bukti kurang atau aturan bertentangan |
| Tidak berlaku | Tidak relevan dengan sasaran, disertai alasan |

Status perbaikan dicatat terpisah: **Diusulkan → Disetujui → Diterapkan → Diverifikasi**. Kode berubah tetapi belum diuji tidak otomatis terverifikasi.

### Prioritas temuan

| Prioritas | Pedoman |
| --- | --- |
| P0 — Kritis | Akses tanpa izin atau kerusakan/kehilangan data serius dengan jalur terbukti; perlu penanganan segera |
| P1 — Tinggi | Alur inti gagal, nilai salah, batas akses terlewati, atau beban yang mengganggu operasional |
| P2 — Sedang | Masalah terbatas, pemborosan kueri/memori, atau hambatan penggunaan dengan dampak jelas |
| P3 — Rendah | Perapian, konsistensi, atau kemudahan pemeliharaan dengan dampak terbatas |

Prioritas, kepastian, usaha, dan risiko perubahan adalah hal berbeda. Jangan memberi P0/P1 hanya karena kode mencurigakan. Peluang optimasi tanpa pengukuran diberi label usulan.

### Format temuan

```text
ID: ECC-001
Judul dan prioritas:
Kategori: bug / keamanan / data / performa / antarmuka / dokumentasi
Status pemeriksaan dan tingkat kepastian:
Lokasi: berkas:baris, method/route
Pemicu: peran, periode, masukan, volume data
Bukti aktual dan aturan yang diharapkan:
Dampak bagi pengguna/data:
Akar masalah atau hal yang masih perlu dibuktikan:
Usulan, manfaat, usaha, dan risiko:
Cara verifikasi serta hasil yang diharapkan:
```

Gabungkan gejala dengan akar masalah sama. Jika tidak menemukan masalah, sebutkan cakupan/keterbatasan; jangan membuat temuan hanya untuk mengisi laporan.

## 10. Rencana implementasi dan laporan

Sebelum mengubah kode atau skema, susun rencana yang dapat ditinjau pengguna:

1. ID temuan, akar masalah, dan batas pekerjaan.
2. Berkas `[MODIFY]`, `[NEW]`, `[DELETE]`, serta tabel/indeks terpengaruh.
3. Perubahan logika/kueri/tampilan dan perilaku yang harus tetap sama.
4. Dampak pada pemanggil lain, akses, data lama, dan lokal-cPanel.
5. Urutan pengerjaan, prasyarat, serta pemulihan jika gagal.
6. Ukuran keberhasilan, verifikasi yang diizinkan, dan skenario uji pengguna.
7. Persetujuan yang sudah ada; minta hanya keputusan tambahan yang diperlukan. Jika diminta audit saja, serahkan rencana dan tunggu persetujuan implementasi.

Laporan setiap audit memuat:

- Tanggal, sasaran, revisi/perubahan lokal, lingkungan, dan metode.
- Temuan menurut prioritas dengan tautan `berkas:baris`.
- Cakupan diperiksa, belum diperiksa, dan perlu konfirmasi.
- Rencana; jika dilaksanakan, perubahan dan status tiap temuan.
- Ukuran sebelum–sesudah jika tersedia, pemeriksaan yang dijalankan beserta hasil, dan skenario uji manual tersisa.

Sajikan laporan dalam percakapan. Jika diminta menyimpan laporan, gunakan berkas sesi terpisah seperti `docs/audits/YYYY-MM-DD-sasaran.md` agar checklist ini tetap dapat dipakai ulang. Status lama tidak dibawa otomatis ke audit berikutnya.

## 11. Referensi perintah pemeriksaan

Ini referensi, **bukan instruksi menjalankan semuanya**. Pilih sesuai lingkup dan izin. Gunakan PowerShell pada workspace Windows ini.

### Pembacaan statis

```powershell
git status --short
git rev-parse --short HEAD
git diff --stat
rg --files app tests
rg -n 'setAutoRoute|filter|group' app/Config/Routes.php
```

Jika pemeriksaan sintaks termasuk lingkup verifikasi, jalankan `php -l` pada berkas PHP yang berubah dan periksa exit code. Tanpa kesalahan sintaks belum membuktikan logika benar. Jangan menyaring hanya kata `error`: pesan sukses PHP juga mengandung kata tersebut.

```powershell
php -l app/Controllers/User/LaporanHarianController.php
$LASTEXITCODE
```

### Pemeriksaan yang memuat aplikasi

Jika sesuai lingkup pemeriksaan lingkungan, `php spark routes` menampilkan pendaftaran route dan `php spark migrate:status` membaca status migrasi. Keduanya memuat aplikasi; tinjau bootstrap/custom commands serta lingkungan sebelum menjalankan. Daftar route bukan pengujian seluruh endpoint; status migrasi bukan bukti kesamaan seluruh kolom/indeks.

### Pengujian dan perubahan

PHPUnit, browser testing, dan pengujian beban hanya dijalankan setelah diizinkan sesuai aturan proyek. Periksa koneksi pengujian, bootstrap, dan fixtures agar tidak menyentuh data operasional. Pilih pengujian relevan; jangan mengulang seluruh suite tanpa alasan.

`php spark migrate`, seeder, `php spark cache:clear`, perubahan skema/data, restore, dan pembersihan log adalah tindakan perubahan. Tempatkan dalam rencana implementasi yang diizinkan, bukan paket perintah audit.

## 12. Catatan revisi

Versi 3.2.0 (13 Oktober 2026) menambahkan panduan audit dan aturan bisnis penanganan pegawai dinamis (pensiun, mutasi keluar, rotasi jabatan, pindah unit kerja, dan pergantian atasan langsung) berbasis riwayat penugasan multi-periode (`riwayat_atasan`), preservasi data kinerja lampau berbasis `tmt_selesai`, standardisasi label "Belum Dinilai", pembatasan daftar perhatian khusus (≤ 25%), serta perluasan cakupan filter tahun lintas periode (2025/2026).

Versi 3.0.0 mengubah daftar klaim kelulusan menjadi panduan pemeriksaan berulang. Checklist lama bertanda selesai dan angka “100% modul”/“90% infrastruktur” tidak dipakai sebagai status saat ini karena tidak menyertakan bukti per sesi. Riwayat sebelumnya tetap tersedia di Git dan dokumen riwayat terkait.

Pembaruan panduan ini **tidak menyatakan audit aplikasi selesai**, tidak mengubah kebijakan penilaian, dan tidak membuktikan kondisi produksi. Audit berikutnya dimulai dari bukti pada revisi serta lingkungan yang diperiksa.
