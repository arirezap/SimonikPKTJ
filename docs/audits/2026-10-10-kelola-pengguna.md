# Laporan Audit Kode: Menu Kelola Pengguna (User Management)

**Tanggal:** 10 Oktober 2026  
**Sasaran Modul:** Menu Kelola Pengguna (`/users`, `/users/create`, `/users/edit/(:num)`, `/users/import`, `/users/export`, `/users/batch_update`, `/users/ajax_update_unit`, `/users/reset-kinerja`)  
**Berkas Terkait:**  
- `app/Controllers/Admin/UserController.php`
- `app/Views/admin/users.php`
- `app/Views/admin/user_create.php`
- `app/Views/admin/user_edit.php`
- `app/Models/User.php`
**Acuan Audit:** `audit_code.md`, `.agents/AGENTS.md`, `design.md`

---

## 1. Ringkasan Eksekutif

Pemeriksaan komprehensif telah dilakukan terhadap modul **Kelola Pengguna** berdasarkan 8 pilar evaluasi `audit_code.md`. Modul ini mengelola data seluruh pegawai, hierarki atasan-staf, penetapan peran primer dan sekunder (multi-role pivot `user_roles`), sinkronisasi unit kerja, serta impor/ekspor data.

Secara umum, arsitektur dasar telah memiliki pengamanan otorisasi terpusat via `checkAdminOrKepegawaian()`, CSRF protection, dan pencatatan audit trail (`log_audit()`). Namun demikian, ditemukan sejumlah celah keamanan, integritas data transaksi, inefisiensi kueri, dan pelanggaran standar UI/UX (8-Point Grid, Minimalist Icon Policy, dan pembakuan istilah).

---

## 2. Temuan Audit Berdasarkan 8 Pilar Pemeriksaan

### Pilar 4: Keamanan (Security)
- **KP-01: Paparan Data Sensitif via Kueri `users.*`**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:48`
  - **Kondisi:** Kueri menggunakan `$this->userModel->select('users.*, atasan.nama_lengkap as nama_atasan')`.
  - **Dampak:** Seluruh kolom sensitif termasuk hash kata sandi (`users.password`) dan token dimuat ke RAM serta diteruskan ke view template.
  - **Rekomendasi:** Gunakan seleksi kolom eksplisit: `select('users.id, users.nama_lengkap, users.nip, users.username, users.email, users.jabatan, users.pangkat, users.unit, users.unit_id, users.role, users.foto, users.atasan_id, atasan.nama_lengkap as nama_atasan')`.

- **KP-02: Ketiadaan Proteksi Self-Atasan Loop pada Method `update()`**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:385-396`
  - **Kondisi:** Input `atasan_id` diterima langsung tanpa memverifikasi apakah `(int)$atasan_id === (int)$id`.
  - **Dampak:** Manipulasi input dapat menyebabkan pengguna menjadi atasan bagi dirinya sendiri, memicu rekursi tak terbatas (infinite loop) pada penelusuran hierarki staf di masa mendatang.
  - **Rekomendasi:** Terapkan pengaman: `if (!empty($atasan_id) && (int)$atasan_id === (int)$id) { $atasan_id = 0; }`.

- **KP-07: Jalur Penghapusan Berkas Foto Kurang Deterministik**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:594`
  - **Kondisi:** Menggunakan `file_exists('assets/uploads/profile/' . basename($user['foto']))`.
  - **Dampak:** Pada konfigurasi DocumentRoot server produksi/cPanel, pemeriksaan tanpa `FCPATH` berisiko gagal menemukan berkas sehingga berkas lama tidak terhapus.
  - **Rekomendasi:** Gunakan `FCPATH . 'assets/uploads/profile/' . basename($user['foto'])`.

---

### Pilar 2: Logika Bisnis & Integritas Data
- **KP-03: Ketiadaan Database Transaction pada Seluruh Operasi Mutasi Data**
  - **Lokasi:** `app/Controllers/Admin/UserController.php` pada method `store()`, `update()`, `delete()`, `importExcel()`, dan `resetKinerja()`.
  - **Kondisi:** Seluruh operasi multi-tabel (`users`, `user_roles`, `log_kegiatan_harian`, dll.) dieksekusi tanpa `$db->transStart()` dan `$db->transComplete()`.
  - **Dampak:** Kegagalan pada langkah kedua menyebabkan data korup/inkonsisten (misal: user tersimpan tapi role di `user_roles` gagal, atau impor Excel berhenti di tengah jalan).
  - **Rekomendasi:** Bungkus setiap operasi dalam Database Transaction atomik dengan `try...catch (\Throwable $e)`.

- **KP-05: Pelanggaran Dual-Sync `unit_id` pada Impor Excel**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:758-774`
  - **Kondisi:** Data impor hanya menyimpan string `unit`, sedangkan integer `unit_id` dibiarkan null/kosong.
  - **Dampak:** Terjadi desinkronisasi antara nama unit dan relasi master `unit_kerja`.
  - **Rekomendasi:** Lakukan pencarian `id` ke master `unit_kerja` berdasarkan nama unit saat impor.

- **KP-06: Ketidaklengkapan Pembersihan pada Reset Kinerja Bulanan**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:840-856`
  - **Kondisi:** `resetKinerja()` menghapus `log_kegiatan_harian`, `target_kinerja_bulanan`, dan `remunerasi`, namun melewatkan tabel `log_tugas_tambahan`.
  - **Dampak:** Catatan tugas tambahan staf pada periode tersebut tertinggal di basis data.
  - **Rekomendasi:** Tambahkan penghapusan `log_tugas_tambahan` pada rentang bulan dan tahun yang bersangkutan.

---

### Pilar 5: Performa & Optimasi Kueri
- **KP-04: Kueri Redundan Unit Kerja & Beban Berlebih pada `potential_bosses`**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:119, 123-124`
  - **Kondisi:**
    1. `$unitKerjaModel->findAll()` dipanggil dua kali berturut-turut untuk memetakan data.
    2. `$data['potential_bosses']` mengambil seluruh pengguna di sistem dengan `users.*` tanpa filter peran pimpinan.
  - **Dampak:** Pemborosan kueri dan alokasi memori yang tidak perlu.
  - **Rekomendasi:**
    1. Panggil `$unitKerjaModel->findAll()` satu kali dan gunakan kembali hasilnya.
    2. Batasi `potential_bosses` hanya pada peran pimpinan (`bossRoles`) dan pilih kolom yang dibutuhkan saja.

- **KP-08: Keterbatasan Filter Role & Pengabaian Tabel Pivot `user_roles`**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:58-64` dan `app/Views/admin/users.php:305-310`
  - **Kondisi:** Filter hanya memiliki 5 role dan hanya mencocokkan `users.role` tanpa membaca tabel pivot `user_roles`.
  - **Dampak:** Pegawai dengan peran sekunder (misal: Staf yang merangkap Kepegawaian, SPM, atau Katim) tidak terjaring filter.
  - **Rekomendasi:** Selaraskan logika filter role seperti pada Daftar Pegawai yang telah mendukung multi-role pivot.

---

### Pilar 7 & 8: UI/UX, 8-Point Grid Scale, & Bahasa
- **KP-09: Dimensi Tombol Baris Tabel Non-Grid 30px**
  - **Lokasi:** `app/Views/admin/users.php:529, 536, 540`
  - **Kondisi:** Tombol aksi Edit dan Hapus menggunakan `width: 30px; height: 30px;`.
  - **Rekomendasi:** Ubah menjadi `32px × 32px` ($4 \times 8\text{px}$) sesuai 8-Point Grid Scale.

- **KP-10: Visual Clutter Ikon Dekoratif, Dialog SweetAlert2, & Pembakuan Istilah**
  - **Lokasi:** `app/Views/admin/users.php`, `user_create.php`, `user_edit.php`
  - **Kondisi:**
    1. Ikon dekoratif redundan pada NIP dan Atasan di setiap baris data tabel.
    2. SweetAlert2 menggunakan kata teknis *"permanen"* dan ikon dalam tombol konfirmasi.
    3. Form masih menggunakan istilah *"Password"* alih-alih baku *"Kata Sandi"*.
  - **Rekomendasi:** Bersihkan ikon dekoratif berulang, sederhanakan pesan SweetAlert2, dan ubah seluruh label menjadi *"Kata Sandi"*.

---

## 3. Matriks Status Audit

| Kode | Temuan | Pilar | Tingkat Risiko | Status |
|---|---|---|---|---|
| KP-01 | Paparan hash kata sandi via `users.*` | Keamanan & Performa | Sedang | Perlu perbaikan |
| KP-02 | Ketiadaan proteksi Self-Atasan Loop di `update()` | Integritas & Keamanan | Sedang | Perlu perbaikan |
| KP-03 | Ketiadaan Database Transaction pada mutasi | Integritas Data | Tinggi | Perlu perbaikan |
| KP-04 | Kueri ganda `unitKerjaModel` & pemuatan seluruh user di atasan | Performa & Kueri | Rendah | Perlu perbaikan |
| KP-05 | Ketiadaan dual-sync `unit_id` pada impor Excel | Integritas Data | Sedang | Perlu perbaikan |
| KP-06 | Tugas tambahan tertinggal pada Reset Kinerja | Logika Bisnis | Sedang | Perlu perbaikan |
| KP-07 | Penghapusan foto profil tanpa FCPATH | Keamanan/Operasional | Rendah | Perlu perbaikan |
| KP-08 | Filter role mengabaikan tabel pivot `user_roles` | Fungsional | Sedang | Perlu perbaikan |
| KP-09 | Tombol tabel non-grid 30px | UI/UX & Grid | Rendah | Perlu perbaikan |
| KP-10 | Visual clutter, bahasa teknis di Swal, & istilah Password | Bahasa & UI/UX | Rendah | Perlu perbaikan |
