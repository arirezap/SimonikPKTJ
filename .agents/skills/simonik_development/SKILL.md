---
name: "ECC (Evidence Command Center) Development"
description: "Panduan arsitektur, modul utama, dan aturan khusus untuk membantu AI Agent mengembangkan aplikasi ECC (Evidence Command Center)."
---

# ECC (Evidence Command Center) Development Guide & Project Blueprint

Panduan ini berisi pedoman lengkap arsitektur sistem, peta modul, basis data, dan standarisasi kode untuk proyek **ECC (Evidence Command Center)**. Gunakan ini untuk memahami alur kerja dan memastikan kode yang Anda buat konsisten dengan struktur yang sudah ada.

---

## 1. Spesifikasi Tech Stack
- **Framework Utama:** CodeIgniter 4 (PHP 8.1+)
- **Basis Data:** MySQL
- **Desain UI:** Bootstrap 5.3 (Vanilla CSS/JS, hindari TailwindCSS kecuali diminta secara eksplisit)
- **Library Frontend:**
  - **jQuery:** Digunakan untuk manipulasi DOM dan request AJAX dasar.
  - **Flatpickr:** Digunakan untuk custom calendar datepicker harian dengan indikator titik status aktivitas kerja.
  - **Select2:** Digunakan untuk *Searchable Dropdown* (pencarian nama pegawai / unit kerja).
  - **Chart.js:** Digunakan untuk merender grafik analisis performa individu dan dashboard unit eksekutif.
  - **SweetAlert2:** Digunakan untuk interaksi dialog konfirmasi (selalu sertakan *native browser fallback*).

---

## 2. Struktur Proyek & Konvensi MVC
- **Controllers (`app/Controllers/`):**
  - Gunakan penamaan file PascalCase.
  - Pisahkan area admin di `app/Controllers/Admin/` (misal: `UserController.php`, `MasterDataController.php`), area kepegawaian di `app/Controllers/Kepegawaian/` (misal: `DashboardKepegawaian.php`, `MonitoringTargetController.php`), dan area pengguna di `app/Controllers/User/` (misal: `PenilaianKinerjaController.php`, `LogKegiatanController.php`, `LaporanHarianController.php`).
- **Models (`app/Models/`):**
  - Pastikan setiap model mendefinisikan `$table`, `$primaryKey`, dan `$allowedFields` agar query builder CI4 berjalan optimal dan aman.
- **Views (`app/Views/`):**
  - Semua file berformat `.php`.
  - Gunakan pemetaan template layout (`$this->extend()`, `$this->section()`).
  - Selalu bersihkan output menggunakan `esc()` untuk mencegah kerentanan XSS.
- **Routing (`app/Config/Routes.php`):**
  - Semua route **WAJIB** terdaftar secara eksplisit di dalam grup filter otentikasi `auth` (seperti `$routes->group('', ['filter' => 'auth'], ...)`).
  - Sertakan rute POST untuk semua endpoint AJAX (misal: `log-kegiatan/storeTugasTambahan`, `log-kegiatan/hapusTugasTambahan`, `laporan-harian/approve`, `penilaian-kinerja/store`). Jangan pernah mengandalkan auto-routing di cPanel.

---

## 3. Peta Modul Utama Aplikasi

### A. Modul Target Kinerja Bulanan & Auto-Approval Direktur
- **Target Kinerja Bulanan (`app/Models/TargetKinerja.php` & `User\LaporanHarianController`):**
  - Tempat pegawai menyusun Rencana Hasil Kerja (RHK) dan target kuantitas bulanan.
  - **Khusus Akun Direktur:** Target yang dibuat otomatis berstatus `disetujui` (`status_approval = 'disetujui'`, `status = 'terkirim'`) dan dapat direvisi secara mandiri kapan saja tanpa memerlukan approval pihak lain.
  - **Pegawai Non-Direktur (Fleksibilitas Revisi Staf):** Target berstatus `menunggu_persetujuan` dan staf berhak mengedit, menambah, atau menghapus target kapan saja selagi belum disetujui Atasan Langsung. Tombol aksi beradaptasi dinamis: *"Ajukan Target"* (draf) dan *"Perbarui & Ajukan Ulang"* (menunggu persetujuan). Notifikasi ke atasan otomatis membedakan pengajuan awal vs pembaruan target.
  - **Prinsip Failsafe Notifikasi:** Variabel `$targetUser` diinisialisasi secara defensif di awal `store()` dan fungsi `send_notification()` dibungkus dalam blok `try...catch` agar kendala notifikasi tidak menggagalkan penyimpanan target utama. Penguncian penuh hanya berlaku setelah target disetujui atasan (`status_approval = 'disetujui'`).

### B. Modul Laporan Harian & Log Kegiatan (`/log-kegiatan`)
- **Pencatatan Aktivitas Harian (`app/Models/LogKegiatanHarian.php`, `LogTugasTambahan` & `User\LogKegiatanController`):**
  - Mencatat realisasi harian tugas pokok & tugas tambahan beserta link bukti.
  - **Datepicker Flatpickr Terintegrasi:** Menampilkan titik status (Hijau = Terkirim, Kuning = Draf, Merah = Belum Diisi).
  - **Styling Tanggal Merah & Weekend:** Tanggal merah/akhir pekan yang berstatus masa depan (`.flatpickr-disabled`) berpenampilan redup pudar (`#fca5a5`, opacity 0.35, normal weight), sedangkan tanggal yang sudah tiba/aktif berpenampilan merah cerah tegas (`#ef4444`, font-weight 700, opacity 1).

### C. Modul Rekap & Penilaian Kinerja (`/penilaian-kinerja`)
- **Penilaian Kinerja Staf (`User\PenilaianKinerjaController`):**
  - Atasan Langsung HANYA DAPAT memberi nilai jika seluruh target kinerja bulanan staf pada periode terkait sudah disetujui.
  - **Formula Standar Predikat Kinerja:**
    - Sangat Baik: `> 100%` s.d. `150%`
    - Baik: `>= 90%` s.d. `100%`
    - Butuh Perbaikan: `> 75%` s.d. `< 90%`
    - Kurang: `> 25%` s.d. `75%`
    - Sangat Kurang: `<= 25%`
    - Belum Dinilai: `0%` (atau belum ada penilaian / RHK dinilai = 0)
  - **Fitur Reset Penilaian Kinerja:**
    - Tombol Reset Nilai langsung mengosongkan nilai (`nilai_capaian = NULL`) dan menyetel flag `status_penilaian = NULL` di database, mengembalikan status menjadi "Belum Dinilai" murni (bukan berstatus `terbit` dengan nilai 0).

### D. Modul Monitoring Kepegawaian (`/kepegawaian/target-kinerja` & `/kepegawaian`)
- **Akses Terbatas (Role-Restricted):**
  - Menu tree dan endpoint modul ini HANYA diizinkan untuk role: `direktur`, `wadir`, `kabag` (`kabag_aak`, `kabag_kuk`), `kepegawaian`, dan `admin`.
  - **Monitoring Target Kinerja:** Pemantauan status penyusunan target seluruh unit kerja instansi.
  - **Monitoring Penilaian Kinerja:** Rekapitulasi nilai dan capaian kinerja seluruh pegawai institusi dengan ekspor Excel Multi-Sheet dan PDF A4 Landscape berstandar resmi.
  - Menggunakan *selective column querying* (`select('id, nama_lengkap, nip, unit, jabatan, role, atasan_id, foto')`) untuk efisiensi memori tingkat tinggi.

### E. Modul Autentikasi & Keamanan Sesi (OWASP Compliant)
- **Pencegahan User Enumeration (`app/Controllers/Auth.php`):** Pesan kesalahan login seragam (*"Nama pengguna atau kata sandi yang Anda masukkan salah."*).
- **Pencatatan Audit Trail `FAILED_LOGIN`:** Merekam kegagalan login dengan IP Address dan alasan.
- **Hardened Logout:** Logout via form POST terlindungi CSRF (`#logoutPostForm`), audit log `LOGOUT`, dan header `Cache-Control: no-store`.

### F. Modul Pengendalian Superadmin
- **Buka Kunci Laporan Harian Staf (`POST log-kegiatan/buka-kunci`):** Superadmin dapat membuka kunci laporan harian yang terkunci, mencatat audit log `UNLOCK_LAPORAN`.
- **Pembatalan Persetujuan Target Bulanan (`POST laporan-harian/batal-approve`):** Superadmin dapat membatalkan persetujuan target bulanan staf untuk revisi, mencatat audit log `CANCEL_APPROVE_TARGET`.

### G. Modul Direktori Pegawai (`/daftar-pegawai`)
- **Katalog Kontak Pegawai (`User\DaftarPegawaiController` & `app/Views/user/daftar_pegawai.php`):**
  - Menyediakan direktori seluruh pegawai terdaftar di lingkungan PKTJ dengan pencarian instan nama/NIP/jabatan dan filter unit kerja dinamis.
  - Menggunakan *selective column querying* (`select('id, nama_lengkap, nip, unit, jabatan, role, atasan_id, foto')`) tanpa mengambil hash kata sandi untuk perlindungan privasi dan efisiensi memori.
  - Penataan antarmuka kartu profil responsif berbasis 8-Point Grid, modal dialog rincian profil tanpa reload (AJAX), dan tombol filter `min-height: 32px`.

### H. Modul Profil Saya (`/profile`)
- **Pengelolaan Data Diri Mandiri (`Profile.php` & `app/Views/profile.php`):**
  - Pegawai dapat memperbarui nomor handphone, memilih unit kerja, mengatur atasan langsung, dan mengganti Kata Sandi mandiri.
  - **Mekanisme Dual-Sync Unit Kerja:** Saat pegawai memilih unit kerja, sistem otomatis menyinkronkan `unit_id` sekaligus teks nama `unit` untuk menjamin konsistensi data institusional.
  - **Failsafe Identitas Sesi & Proteksi Self-Atasan Loop:** Sistem memvalidasi ganda sesi (`id` dan `user_id`) serta secara defensif memblokir pemilihan diri sendiri sebagai atasan langsung (`$atasanId !== $userId`).
  - **Preservasi Formulir (*Form State Preservation*):** Menggunakan `old()` pada seluruh field input agar data yang baru diketik pengguna tidak hilang saat terjadi galat validasi kata sandi.

---

## 4. Standarisasi UI/UX, 8-Point Grid System & Kualitas Visual
- **8-Point Grid System (Strict Spacing & Asset Scale):**
  - Seluruh layout, jarak elemen (`margin`, `padding`, `gap`), tinggi tombol, dan wadah aset wajib mematuhi kelipatan 8px: `4px` (0.5x micro), `8px` (1x base), `12px` (1.5x), `16px` (2x), `24px` (3x), `32px` (4x), `40px` (5x), `48px` (6x), `64px` (8x), `80px` (10x).
  - Standar ukuran aset:
    - Swatch legenda: `16px × 16px` (`border-radius: 4px`).
    - Compact Table Action Buttons / Filter Buttons: `min-height: 32px; padding: 4px 12px; border-radius: 50rem;`.
    - Password Toggle Action Buttons: `min-height: 36px; padding: 4px 12px; border-radius: 8px;`.
    - Kontrol form & dropdown Select2: `height: 36px`–`40px; border-radius: 8px;`.
    - Tombol aksi CTA utama: `min-height: 40px; border-radius: 8px;`.
    - Box icon header modal / Card header icon container: `40px × 40px` (`border-radius: 12px` / `rounded-3`).
    - Prominent Feature Icons: `48px × 48px` (`border-radius: 16px`).
    - Avatar profil: `40px`/`64px`/`80px`.
    - Sel kalender desktop: `min-height: 64px` (mobile `48px`).
    - Status badges: `padding: 4px 12px; border-radius: 50rem;` (`px-3 py-1`).
- **Tabular Numbers:** Selalu gunakan `font-variant-numeric: tabular-nums; font-feature-settings: "tnum";` pada angka capaian, nilai persen, tanggal, dan NIP.
- **Sanitasi URL Bukti XSS:** Selalu validasi bahwa link bukti berawalan skema `http://` atau `https://` sebelum dirender ke tag `<a>`.

---

## 5. Aturan Penulisan Kode & Keamanan
- **CSRF Protection:** Semua elemen `<form>` wajib menyertakan `<?= csrf_field() ?>`. Request AJAX POST wajib mengirim token CSRF dan memperbarui `csrf_hash`.
- **Pengamanan Unggah Berkas (Upload Hardening & Defense in Depth):**
  - **Validasi Ukuran Maksimal 2MB:** Wajib menerapkan aturan CI4 `uploaded[foto]|max_size[foto,2048]` di sisi server dan atribut `accept="image/*"` di form HTML.
  - **Pemeriksaan MIME Type & Magic Bytes:** Wajib menggunakan aturan `is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]` untuk memastikan file yang diunggah benar-benar berkas gambar asli, bukan skrip berbahaya dengan ekstensi palsu.
  - **Cryptographic Random Renaming:** Seluruh berkas yang diunggah wajib diubah namanya secara acak menggunakan `$file->getRandomName()` sebelum disimpan ke direktori publik (`public/assets/uploads/users/`) untuk mencegah serangan *path traversal* dan penimpaan file.
  - **Script Execution Barrier (`.htaccess`):** Direktori penyimpanan unggahan (`public/assets/uploads/`) wajib diproteksi dengan file `.htaccess` yang mematikan mesin eksekusi PHP (`php_flag engine off`) dan menolak seluruh akses ke file berkas skrip yang dapat dieksekusi (`.php`, `.phtml`, `.cgi`, `.sh`, `.exe`, dll.).
  - **Penghapusan Berkas Lama yang Aman:** Saat mengganti atau menghapus foto profil, nama berkas wajib dibersihkan menggunakan `basename()` dan diverifikasi keberadaannya melalui `file_exists(FCPATH . ...)` sebelum dieksekusi dengan `unlink()`.
- **Database Transactions:** Semua mutasi batch wajib dibungkus dalam blok `try...catch (\Exception $e)` dan `$db->transStart()` / `$db->transComplete()`.
- **Sanitasi Desimal:** Selalu gunakan `str_replace(',', '.', trim((string)$val))` sebelum parsing numerik.
- **SweetAlert2 Fallback:** Selalu sediakan *native browser fallback* (`confirm()`) jika library SweetAlert2 belum selesai termuat.
- **XSS Prevention:** Selalu gunakan `esc($var)` saat mencetak variabel ke View HTML.
- **Standardized Audit Logging:** Selalu gunakan `log_audit()` untuk merekam mutasi data penting.

---

## 6. Standar Gaya Bahasa, Notifikasi & Mikro-Kopi (Tone, Simplicity & Microcopy)
- **Prinsip Utama: Singkat, Padat, Jelas & Ramah Pengguna**:
  - Seluruh teks antarmuka (label form, tombol aksi, tooltip, alert banner, modal pop-up, SweetAlert2, toast notifikasi, pesan validasi/error) wajib menggunakan kalimat ringkas dan langsung pada intinya.
- **Larangan Istilah Teknis pada Teks Pengguna**:
  - Dilarang keras menampilkan istilah teknis sistem/server/database kepada pengguna, seperti: *"database"*, *"basis data"*, *"server/jaringan"*, *"SQL"*, *"query"*, *"+ toleransi X hari"*, *"exception"*, *"permanen"*, dsb.
  - Berikan pesan yang tenang dan berorientasi solusi, contoh:
    - ❌ *"Gagal mengirim laporan harian karena gangguan basis data."* $\rightarrow$ ✅ *"Gagal mengirim laporan. Silakan coba lagi."*
    - ❌ *"Pengisian laporan periode Agustus 2026 telah ditutup sejak 05 September (Batas akhir + toleransi 5 hari)."* $\rightarrow$ ✅ *"Pengisian laporan periode Agustus 2026 telah ditutup sejak 05 September 2026."*
    - ❌ *"Silakan isi minimal satu kegiatan pada Tugas Pokok atau Tugas Tambahan sebelum mengirim ke atasan."* $\rightarrow$ ✅ *"Isi minimal 1 kegiatan pokok atau tugas tambahan."*
- **Standar Dialog Konfirmasi SweetAlert2**:
  - **Judul Dialog**: 2–4 kata (*"Hapus Kegiatan?"*, *"Hapus Tugas Tambahan?"*, *"Kirim Laporan?"*).
  - **Teks Dialog**: 1 kalimat pendek dan tenang (*"Kegiatan ini akan dihapus."*).
  - **Tombol Aksi**: Kata kerja singkat dan tegas (*"Ya, Hapus"*, *"Kirim"*, *"Batal"*).
  - **Umpan Balik Sukses**: Judul *"Terhapus"* / *"Tersimpan"*, teks *"Kegiatan berhasil dihapus."* / *"Draf berhasil disimpan."*
- **Pembakuan Istilah Resmi ECC**:
  - *"Kata Sandi"* (bukan Password)
  - *"Alamat Email"* (bukan Email Address)
  - *"Keluar"* (bukan Logout)
  - *"Simpan Profil"* (bukan Simpan Perubahan / Update)
  - *"Perbarui Kata Sandi"* (bukan Ubah Password)
- **Kepatuhan Mutlak Terminologi & Branding**:
  - Wajib 100% menggunakan istilah resmi **"staf"** (tidak boleh menggunakan "bawahan" atau "staff").
  - Wajib 100% menggunakan identitas resmi **"Evidence Command Center (ECC)"** (tidak boleh menggunakan "Simonik").
