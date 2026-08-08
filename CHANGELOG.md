# CHANGELOG: Pembaruan Sistem Simonik PKTJ (Sesi Terakhir)

Dokumen ini berisi riwayat fitur, perbaikan (*bug fixes*), dan peningkatan antarmuka (UI/UX) yang telah diselesaikan pada sesi ini. Dokumen ini sangat berguna sebagai konteks apabila Anda memulai sesi (obrolan) baru dengan AI.

## 1. Fitur Baru: Log Keamanan Aktivitas (Audit Trail)
- **Pembuatan Infrastruktur Audit:** 
  - Membuat tabel `audit_logs` dan `AuditLog` model untuk merekam jejak aktivitas (siapa, kapan, aksi apa, entitas, IP Address, *User Agent*, serta nilai *before* dan *after* dalam format JSON).
  - Mengimplementasikan `audit_helper.php` dengan fungsi global `log_audit()` yang ringan dan tidak memblokir (*non-blocking*).
- **Titik Perekaman (Tracking Points) Komprehensif:**
  - **Otentikasi:** Merekam aktivitas `LOGIN` dan `LOGOUT`.
  - **Kelola Pengguna:** Merekam `CREATE`, `UPDATE` (termasuk *batch update* & hapus foto), dan `DELETE` pada pengguna.
  - **Profil Diri:** Merekam `UPDATE` ketika pegawai memperbarui kata sandi atau profil mereka sendiri.
  - **Penilaian Kinerja:** Merekam *approval* (pemberian nilai / persetujuan log) harian baik satuan maupun *batch*.
  - **Evidence Command Center (ECC):** Merekam unggah tautan bukti LED (`UPDATE`), hapus/reset tautan (`DELETE`), serta penilaian simulasi akreditasi (`SIMULASI`).
  - **Master Data:** Merekam perubahan pada Sasaran, Indikator, Satuan, Unit Kerja, Kriteria LED, dan Standar LED.
  - **Pengaturan Sistem:** Merekam saat pengaturan sistem seperti batas waktu pelaporan diubah.
- **Antarmuka (UI) Admin - Log Aktivitas Sistem:**
  - Mengimplementasikan *UI Bento Grid* yang elegan untuk menampilkan daftar log aktivitas.
  - Filter dinamis berdasarkan `Action` (CREATE, UPDATE, DELETE, dll) dan `Entity` (Modul/Tabel).
  - Menambahkan *badge* status yang rapi, serta menampilkan *IP Address* dan *User Agent* untuk melacak perangkat yang digunakan.


## 1. Peningkatan UI/UX (Antarmuka Pengguna)
- **Halaman Login (Bento & Modernisasi):** 
  - Desain ulang halaman login menjadi lebih ringkas dan modern (ala Bento).
  - Skala halaman dikecilkan agar pas di layar laptop tanpa *scroll* bawah (*fit-to-screen*).
  - Membuang spinner Bootstrap bawaan dan menggantinya dengan spinner CSS murni yang mulus (efek putaran cincin dan efek tombol memudar) saat memproses login.
- **Sidebar (Tema Terang):**
  - Mengubah latar belakang *sidebar* menjadi putih bersih (`bg-white` & `border-end`).
  - Menyesuaikan warna font agar cocok dengan tema putih.
  - Menambahkan animasi *zoom* ringan (hover) pada Logo PKTJ di *sidebar* melalui CSS (`.sidebar-logo:hover`).
- **Top Bar (Smart Scroll):**
  - Merapikan teks sapaan dari "Selamat datang kembali," menjadi sekadar "Selamat datang,".
  - Mengimplementasikan fitur **Smart Topbar**: Saat area konten di-*scroll* ke bawah lebih dari 60px, *top bar* akan otomatis menyembunyikan diri (*slide-up*). Saat di-*scroll* ke atas, ia muncul kembali.
- **Halaman Profil (Bento Grid Pro Max):**
  - Mengubah tampilan *form* profil linier menjadi **Bento Grid** (kartu-kartu terpisah untuk Identitas, Kredensial, dan Info Pegawai).
  - **Inisial Cerdas:** Menghapus gambar `default.png`. Jika *user* tidak memiliki foto atau menghapus foto profil, sistem otomatis mencetak Inisial Nama besar (misal: "AR").
  - Transisi Inisial Profil melalui *Javascript* berjalan mulus tanpa perlu memuat ulang halaman (*client-side*).

## 2. Struktur Pangkalan Data & Model (Database)
- **Penambahan Nomor HP:**
  - Telah dibuat berkas Migrasi Database CodeIgniter (`AddNoHpToUsers`) untuk menambah kolom `no_hp` (VARCHAR 20) ke tabel `users`. Migrasi sukses dieksekusi.
  - Model `app/Models/User.php` telah diperbarui dengan menambahkan `'no_hp'` ke dalam `$allowedFields`.
  - UI pada *Halaman Profil* telah disesuaikan dari "Nomor WhatsApp (Aktif)" dan `no_wa` menjadi **No. HP** (`no_hp`).

## 3. Perbaikan Kutu (*Bug Fixes* & Logika)
- **Perbaikan Kinerja Bulanan (Laporan Harian):**
  - *Bug Tab Melompat:* Memperbaiki *bug* di mana saat *user* mengganti filter bulan di tab "Target Kinerja Saya", UI malah melompat ke tab "Persetujuan Target Staf". Diselesaikan dengan menyisipkan input tersembunyi `source_tab` untuk melacak asal form.
  - *Bug Form Resubmission (403 Security Exception):* Memperbaiki error keamanan bawaan browser/CodeIgniter (CSRF Token) saat pengguna me-*refresh* (F5) halaman Target Kinerja. Diatasi dengan merombak kode pada `LaporanHarianController::index` menjadi pola keamanan **PRG (Post-Redirect-Get)**. Kini me-refresh halaman usai *filter* bulan/tahun 100% aman dan tidak melempar galat.

## 4. Pengembangan Evidence Command Center (ECC) & Kinerja
- **Dashboard Admin (Top 5 Leaderboard):**
  - Standardisasi tinggi *header* dan *row* di ketiga kartu *Bento* agar sejajar sempurna secara horizontal.
  - Menerapkan fitur paksa potong teks (`text-truncate`) dipadukan dengan HTML native *tooltip* (`title="..."`) untuk nama pegawai yang terlalu panjang agar tidak merusak layout ke bawah.
- **Penilaian Kinerja (Tab Staf / Atasan):**
  - **Penyelarasan UI/UX:** Memindahkan ringkasan total nilai ke dalam *footer* tabel (sebelah kanan bawah) agar *flow* membacanya lebih natural.
  - **Koreksi Data:** Menghapus label *sum* yang menyesatkan pada kolom Realisasi/Selisih karena perbedaan satuan ukur antar pegawai.
  - **Fitur Baru (Action Buttons):** Menambahkan tombol **Kosongkan** (reset paksa) dan **Simpan Sementara** (draft).
  - **Konfirmasi Modern:** Mengganti *popup browser default* (yang memblokir *thread*) dengan dialog konfirmasi berbasis **SweetAlert2** untuk UX yang jauh lebih halus.
- **Target Bulanan Saya (Tab Individu):**
  - Desain ulang menyeluruh menggunakan prinsip *UI/UX Pro Max* (Bento Grid, modern, hierarki tegas).
  - Mengintegrasikan blok "NILAI KINERJA" raksasa ke dalam *footer* tabel RHK untuk menyeragamkan desain dengan tab Staf.
  - Menyempurnakan tombol *link* laporan harian menjadi ikon bundar yang ramping.
  
## 5. Perombakan Arsitektur Multi-Role (Tabel Pivot)
- **Tabel `user_roles`:** Mengganti arsitektur akses dari *single-role* menjadi *multi-role* menggunakan tabel pivot. Semua pengguna kini memiliki **1 Role Primer** (Jabatan Struktural) dan bisa memiliki banyak **Role Sekunder** (Peran Fungsional seperti Kepegawaian, SPM, dll).
- **Pembuatan `role_helper.php`:** Menyediakan fungsi global `hasRole()`, `hasAnyRole()`, dan `getUserRoles()` untuk menangani pengecekan keamanan di seluruh *controller* dan *view*.
- **Migrasi Database:** Melakukan migrasi dan otomatisasi data lama (khususnya untuk akun yang terjebak pada role primer `spm`, diubah menjadi `user`/`manajemen` dengan *role* tambahan `spm`).
- **Kelola Pengguna (Admin):** Menambahkan opsi centang (checkbox) pada formulir *Tambah/Edit Pengguna* agar Admin dapat memberikan hak akses khusus seperti `Kepegawaian` dan `SPM` tanpa mengorbankan role struktural mereka.

## 6. Modul Baru: Rekap Kepegawaian & ECC Multi-Role
- **Modul Rekap Kepegawaian:**
  - Membuat *controller* dan *view* baru yang khusus menampilkan tabel seluruh pengguna dari berbagai unit kerja.
  - Fitur ini khusus untuk tim SDM (role sekunder `kepegawaian`) guna keperluan evaluasi remunerasi/gaji.
  - Menambahkan fitur tombol **Export Excel (.csv)** yang kompatibel secara universal.
- **Penyelarasan Hak Akses ECC (Evidence Command Center):**
  - **Menu ECC > LED:** Kini dibuka bebas (terlihat) untuk **seluruh pegawai**. Filter di sisi server telah diperbaiki agar sistem cerdas melacak hierarki (hingga 2 level ke atas) untuk mengetahui *parent unit* (AAK/KUK) dan hanya memunculkan tugas LED sesuai bagian mereka.
  - **Menu ECC > Simulasi Penilaian:** Dikunci secara eksklusif hanya untuk pengguna yang memiliki *role* sekunder `spm` atau `admin`. Proteksi ganda diterapkan pada *Sidebar UI* maupun level *Controller*.

## 7. Refactoring & Perbaikan Stabilitas Live cPanel (Sesi Terbaru)
- **Refactoring Target Kinerja Bulanan (`LaporanHarianController.php`):**
  - Penyatuan *looping* validasi dan *data preparation* menjadi satu iterasi tunggal untuk efisiensi komputasi.
  - Penanganan desimal fleksibel: Otomatis mengonversi tanda koma (`,`) menjadi titik (`.`) sehingga aman untuk tipe data `DECIMAL` MySQL.
  - Pengabaian baris kosong secara cerdas pada opsi "Simpan Sementara" mau pun "Simpan & Kirim".
  - Penambahan proteksi `try...catch` di sekitar operasi `updateBatch` dan `insert` untuk mencegah *Error 500 (White Screen)* jika database mengalami gangguan sementara.
- **Penguatan Validasi & Perbaikan Bug Log Kegiatan Harian (`LogKegiatanController.php`):**
  - **Validasi Capaian Wajib:** Menolak formulir jika kolom `jumlah_capaian` kosong, dan mewajibkan angka (minimal `0`) dengan pesan peringatan yang informatif.
  - **Perbaikan Method `storeTugasTambahan()`:** Menyelaraskan pengolahan `jumlah_capaian`, sanitasi desimal koma, serta proteksi `try...catch` pada endpoint khusus tugas tambahan.
  - **Perbaikan Bug Hapus Draf Tugas Tambahan:**
    - Memperbaiki pengembalian `$allTambahanIds` pada respons AJAX `store()` agar mencakup seluruh ID (baik draf eksisting maupun baris baru yang baru saja tersimpan).
    - Memperbaiki *handler* `hapus-baris-tmb` di JavaScript agar menggunakan nama token CSRF dinamis (`<?= csrf_token() ?>`) dan memperbarui token di DOM secara *real-time* setelah setiap *request*.
- **Pendaftaran Rute Eksplisit (`Routes.php`):**
  - Mendaftarkan rute-rute AJAX yang sebelumnya belum terdaftar di grup `auth` (`log-kegiatan/storeTugasTambahan`, `log-kegiatan/hapusTugasTambahan`, dan `laporan-harian/approve`) untuk mencegah kegagalan HTTP `404/405` di server *live cPanel*.
- **Mekanisme Anti-Cache untuk Deployment (`main.php`):**
  - Menambahkan meta tag HTTP `Cache-Control` (`no-cache, no-store, must-revalidate`), `Pragma`, dan `Expires: 0` pada *layout* utama untuk memaksa *browser* mengunduh tampilan terbaru setelah update tanpa perlu *clear cache* manual.

