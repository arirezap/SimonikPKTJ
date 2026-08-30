# 📌 Project Context: Evidence Command Center (ECC)

## 1. Project Overview
Aplikasi ini adalah **Evidence Command Center (ECC)** (sebelumnya bernama Simonik), sebuah sistem berbasis web untuk memonitoring kinerja pegawai, mengelola Rencana Kinerja, Log Kegiatan Harian, hingga Penilaian Kinerja. Aplikasi ini menampilkan dasbor analitik tingkat lanjut untuk level pimpinan dan manajemen instansi.

## 2. Technology Stack
- **Backend Framework**: CodeIgniter 4 (PHP)
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript (Vanilla/jQuery)
- **CSS Framework**: Bootstrap 5
- **Data Visualization**: Chart.js (untuk Bar Chart, Doughnut Chart, Polar Area, Line Chart, Radar Chart)
- **Environment**: Berjalan di atas Laragon (Localhost) dengan OS Windows.

## 3. User Roles & Hierarchy
Sistem ini menggunakan *role-based access control* dengan hierarki sebagai berikut:
1. **Pimpinan/Manajemen**: `direktur`, `wadir`, `manajemen`, `spm`, `admin`
   - Memiliki hak akses ke **Dashboard Admin** (Command Center).
   - Dapat melihat analitik agregat seluruh instansi, grafik kinerja per unit, laporan tepat waktu/terlambat, dan fitur ECC.
2. **Kepala Bagian**: `kabag_aak`, `kabag_kuk`
   - Memiliki akses ke Dashboard Admin dengan limitasi tertentu.
3. **Pegawai / User Biasa**: `user`, `aak`, `kuk`
   - Hanya mengakses **Dashboard User**.
   - Fitur utama: Input Rencana Kinerja bulanan, input Log Kegiatan Harian, melihat evaluasi dari atasan, dan grafik kinerja personal.

## 4. UI/UX Design Guidelines (CRITICAL: "UI UX Pro Max")
Semua pengembangan antarmuka (UI) baru **WAJIB** mengikuti panduan gaya "UI UX Pro Max" yang berkelas premium, rapi, dan profesional:
- **Card Design**: Selalu gunakan `class="card border-0 shadow-sm rounded-4"`. Jangan gunakan border bawaan Bootstrap yang kaku.
- **Warna & Gradasi**: Gunakan palet warna yang modern (misal: aksen biru `#0d6efd`, hijau `#198754`). Boleh menggunakan gradien halus untuk *summary cards* pimpinan.
- **Tipografi**: Bersih dan terbaca jelas. Gunakan ketebalan font (`fw-bold`) untuk memberikan hierarki informasi.
- **White Space**: Berikan *padding/margin* yang lega agar elemen tidak saling berdempetan (gunakan kelas `p-4`, `mb-4`, dll).
- **Interaktivitas**: Grafik wajib memiliki fitur klik (terutama *drill-down* menggunakan Modal Bootstrap) dan *hover effects*. Jangan biarkan kursor standar jika elemen bisa diklik.

## 5. Key Features, Architecture & Workflow
- **Dashboard Command Center (Admin/Direktur)**:
  - Berada di `app/Controllers/Admin/Dashboard.php` dan `app/Views/admin/dashboard.php`.
  - Berisi metrik *Top Cards*, *Doughnut Chart* (Ketepatan waktu), *Polar Area Chart* (Distribusi Aspek Penilaian), *Line Chart* (Tren Tahunan), *Leaderboard* (Top 5 & Bottom 5), dan *Bar Chart* (Kinerja Per Unit).
  - Data diagregasi secara *real-time* dari tabel `log_kegiatan_harian`.
- **Dashboard ECC (Radar Chart)**: Menampilkan pencapaian indikator standar dengan bentuk jaring laba-laba.

### Alur Kinerja Staf (3 Modul Utama):
Aplikasi ini memiliki 3 modul yang saling berkesinambungan untuk menilai kinerja staf:
1. **Target Kinerja Bulanan** (Hulu): 
   - *Tabel*: `laporan_harian`. *Controller*: `LaporanHarianController`.
   - *Fungsi*: Staf menetapkan target bulanan (Sasaran Program, Indikator Kinerja, Target Kuantitas) untuk bulan dan tahun tertentu. Data ini menjadi *parent* untuk kegiatan harian.
   - *Refactoring & Validasi*: Menggunakan *single loop* untuk validasi dan penyiapan data, konversi otomatis koma (`,`) ke titik (`.`) untuk angka desimal, dan pengabaian baris kosong secara cerdas.
2. **Lapor Kegiatan Harian** (Eksekusi): 
   - *Tabel*: `log_kegiatan_harian` & `log_tugas_tambahan`. *Controller*: `LogKegiatanController`.
   - *Fungsi*: Staf mencatat aktivitas harian mereka secara spesifik. Setiap *log* wajib ditautkan ke target bulanan melalui `target_id` (berelasi dengan `laporan_harian.id`). Tabel ini juga mencatat `jumlah_capaian` harian.
   - *Aturan Ketat*: Kolom `jumlah_capaian` wajib diisi angka (minimal `0`). Fitur hapus tugas tambahan disinkronkan via AJAX dengan penanganan CSRF token dinamis dan pelacakan ID (`$allTambahanIds`).
3. **Rekap & Penilaian Kinerja** (Hilir/Evaluasi):
   - *Controller*: `PenilaianKinerjaController`.
   - *Fungsi*: Mengagregasi data dari `log_kegiatan_harian`. 
   - Bagi **Staf**, modul ini berfungsi untuk melihat rekapitulasi progres mereka.
   - Bagi **Atasan**, modul ini memunculkan antarmuka (form) untuk menilai kinerja staf/staf yang tergabung di timnya. Skor penilaian (Kualitas, Disiplin, Kerjasama) disimpan kembali ke dalam baris data di tabel `log_kegiatan_harian`.6. **Log Keamanan (Audit Trail)** (Background Service):
   - Menggunakan tabel `audit_logs` dan `app/Helpers/audit_helper.php`.
   - Merekam seluruh aktivitas `CREATE`, `UPDATE`, `DELETE`, `LOGIN/LOGOUT`, `APPROVE`, `UNLOCK_LAPORAN`, dan `CANCEL_APPROVE_TARGET` dari seluruh modul secara otomatis dan *non-blocking*. Mampu menangkap data *before-after* dalam bentuk JSON.
   - Halaman `/admin/audit-logs` dilengkapi Live Search, Filter Periode Tanggal, Bootstrap Icons, dan Interactive JSON Viewer Modal.
5. **Fitur Pengendalian Superadmin (Unlock & Cancel Approval)**:
   - **Buka Kunci Laporan Harian Staf (`log-kegiatan/buka-kunci`)**: Superadmin (`hasRole('admin')`) dan Atasan Langsung dapat memberikan izin revisi laporan harian & tugas tambahan staf yang sudah berstatus `terkirim` pada tanggal tertentu agar staf dapat merevisi laporannya. Status di-reset ke `draft`, mencatat audit log `UNLOCK_LAPORAN`, dan mengirim notifikasi *in-app*. Saat staf menyimpan ulang ("Simpan & Kirim"), laporan terkunci kembali secara otomatis.
   - **Pembatalan Persetujuan Target Bulanan (`laporan-harian/batal-approve`)**: Superadmin dapat membatalkan persetujuan Target Bulanan yang sudah disetujui (`status_approval = 'disetujui'`). Status di-reset ke `draft` (`status_approval = 'menunggu_persetujuan'`), mencatat audit log `CANCEL_APPROVE_TARGET`, dan mengirim notifikasi *in-app*. Riwayat laporan harian sebelumnya **TETAP UTUH & AMAN**, dan otomatis terhubung kembali begitu target disetujui ulang oleh atasan.
   - **Diferensiasi Status Badges & Banner Revisi**: Status badge `Disetujui` (hijau), `Menunggu Persetujuan` (kuning - hanya saat `status === 'terkirim'`), dan `Draf (Perlu Revisi)` (kuning perbaikan - saat `status === 'draft'`) ditampilkan secara akurat lengkap dengan alert banner petunjuk revisi pada halaman staf.
6. **Modul Rekap Kepegawaian & Remunerasi Monitoring**:
   - Berada di `/kepegawaian` (`DashboardKepegawaian.php` & `rekap_kinerja.php`). Khusus untuk peran `kepegawaian` dan `admin` untuk evaluasi hak pencairan remunerasi bulanan pegawai.
   - Menghitung rasio kelengkapan penilaian RHK (`dinilai / total`), rata-rata nilai kinerja, dan predikat kinerja (*'Baik'* untuk skor >= 90). Dilengkapi ekspor data CSV UTF-8 BOM ramah Excel.
7. **Normalisasi Relasi Unit Kerja & Dual-Sync Architecture**:
   - Berada di `users.unit_id` (berelasi ke `unit_kerja.id`) dengan `$allowedFields` terdaftar pada `User.php`.
   - Menggunakan mekanisme *Dual-Sync* yang mempertahankan kolom string `users.unit` untuk menjaga *backward compatibility* total pada seluruh query laporan dan rekapitulasi lama.
   - Mendukung *Cascading Update* otomatis dari `MasterDataController::updateUnitKerja()` ke seluruh pengguna yang terdaftar di unit tersebut.
   - Memiliki proteksi *Deletion Barrier* pada `MasterDataController::deleteUnitKerja()` dan antarmuka JavaScript yang menolak penghapusan unit kerja jika masih terdapat pegawai aktif.
   - Tabel Master Data Unit Kerja menyajikan indikator badge **`Pegawai Terdaftar`** yang dihitung via agregasi `UnitKerja::getUnitsWithUserCount()`.
8. **Modul Autentikasi & Keamanan Sesi (OWASP ASVS Standard)**:
   - **Pencegahan User Enumeration**: Pesan login diseragamkan (*"Nama pengguna atau kata sandi yang Anda masukkan salah."*) baik untuk akun yang tidak ditemukan maupun password salah.
   - **Audit Trail `FAILED_LOGIN`**: Merekam kegagalan login dengan alasan `user_not_found` atau `invalid_password` beserta alamat IP pengguna.
   - **Hardening Logout**: Mendukung metode POST terlindungi CSRF via `#logoutPostForm` guna mencegah serangan *Forced Logout CSRF*, mencatat audit log `LOGOUT` sebelum sesi dihancurkan, menghapus cookie otentikasi `remember_me`, dan menyuntikkan header `Cache-Control: no-store` untuk mencegah eksploitasi tombol *Back* browser pada komputer bersama.
9. **Modul Profil Pengguna & Kredensial Akun (`/profile`)**:
   - **Penyederhanaan Unggah Avatar**: Interaksi 1-klik langsung pada foto avatar / tombol kamera melayang (menghapus kotak file input ganda yang redundan).
   - **Pratinjau Asinkron Lokal**: Memuat pratinjau gambar instan di browser via `FileReader`, validasi ukuran `< 2MB` dan tipe MIME sebelum upload, serta inisial cerdas 2-huruf otomatis sebagai fallback.
   - **Hardening Kredensial**: Validasi keunikan Email dan NIP/NIK terhadap akun lain (`where('id !=', $userId)`), tombol intip password (`.btn-toggle-pw`), indikator kecocokan password real-time (`match-pop-anim`), *Dirty Form Guard* (`beforeunload`), *Double-Submit Lock*, *Mobile Floating Action Bar*, dan sinkronisasi otomatis role `'spm'` jika unit kerja diubah ke Satuan Penjaminan Mutu.

## 6. Coding Standards & Agent Instructions
- **Routing & Filter**: Seluruh *endpoint* AJAX dan form submission **WAJIB** terdaftar secara eksplisit di `app/Config/Routes.php` dalam grup filter otentikasi `auth`. Rute privat tidak boleh berada di luar filter `auth`.
- **Terminology Rule**: Wajib menggunakan kata baku **"staf"** (bukan "staf") di seluruh label UI, variabel kode, dan dokumentasi.
- **Application Naming**: Nama resmi aplikasi adalah **Evidence Command Center (ECC)** (atau **ECC**).
- **Controller Logic & Form Handling**: Usahakan logika perhitungan berat diselesaikan di Controller atau menggunakan *Query Builder* di Model. **WAJIB** menerapkan pola **PRG (Post-Redirect-Get)** pada setiap form filter atau form *submission* non-AJAX. Untuk request AJAX, selalu kembalikan respons JSON lengkap dengan `csrf_hash`.
- **Database & Try-Catch Safety**: Semua eksekusi `insert()`, `update()`, `delete()`, dan `updateBatch()` pada *controller* wajib dibungkus dalam blok `try...catch (\Exception $e)` dan Database Transaction (`$db->transStart()` & `$db->transComplete()`) untuk mencegah kesalahan data sementara di server *live*.
- **Handling Desimal & Sanitasi**: Nilai angka bertipe desimal harus selalu melalui sanitasi `str_replace(',', '.', trim((string)$val))` sebelum dimasukkan ke basis data agar mendukung input berbasis bahasa Indonesia (koma).
- **Cache Control Deployment v1.2**: Halaman utama dilengkapi dengan meta tag HTTP `Cache-Control` (`no-cache, no-store, must-revalidate`) serta cache-busting `?v=1.2.filemtime(...)` pada file CSS/JS untuk memastikan pengguna mendapatkan versi v1.2 aplikasi terbaru secara otomatis tanpa perlu *clear cache* manual.
- **Database & Model**: Jika ada penambahan kolom pada tabel, pastikan kolom tersebut juga didaftarkan pada `$allowedFields` di Model terkait agar data dapat tersimpan.
- **File Modifications**: Dilarang menghapus komentar/kode lama yang tidak terkait langsung me-perbaikan. Prioritaskan perbaikan *bug* secara spesifik.
- **Chart.js**: Jika data label sumbu X terlalu panjang, gunakan format `indexAxis: 'y'` (Horizontal Bar Chart) agar rapi, dan gunakan tinggi wadah (*container*) yang dinamis.
- **JavaScript UI & Fallback**: Setiap tombol aksi berbasis AJAX yang memanfaatkan SweetAlert2 **WAJIB** mengecek `typeof Swal !== 'undefined'` dan menyediakan fallback dialog `confirm()` agar tetap dapat mengeksekusi aksi AJAX jika library belum selesai dimuat atau terhalang koneksi CDN.

