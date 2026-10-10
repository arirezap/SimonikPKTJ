# Laporan Audit Kode: Fitur Status Pegawai pada Menu Kelola Pengguna

**Tanggal:** 11 Oktober 2026  
**Sasaran Modul:** Fitur Status Pegawai (Aktif, Mutasi Keluar, Pensiun, Nonaktif), TMT Selesai, dan Dispensasi Masa Lampau pada Menu Kelola Pengguna (`/users`, `/users/create`, `/users/edit/(:num)`)  
**Berkas Terkait:**  
- `app/Controllers/Admin/UserController.php`
- `app/Controllers/Auth.php`
- `app/Models/User.php`
- `app/Views/admin/users.php`
- `app/Views/admin/user_create.php`
- `app/Views/admin/user_edit.php`
- `app/Database/Migrations/2026-10-11-020000_AddStatusPegawaiAndDispensasiToUsers.php`
**Acuan Audit:** `audit_code.md`, `.agents/AGENTS.md`, `design.md`

---

## 1. Ringkasan Eksekutif

Pemeriksaan komprehensif telah dilakukan terhadap **Fitur Status Pegawai dan Dispensasi Masa Lampau** pada modul Kelola Pengguna berdasarkan 8 pilar evaluasi `audit_code.md`. Fitur ini dirancang sebagai solusi jangka panjang siklus kepegawaian untuk menangani pegawai mutasi keluar, pensiun, maupun nonaktif, serta pemberian tiket dispensasi pengisian masa lampau secara perorangan tanpa membuka saklar batas waktu global instansi.

Arsitektur dasar telah dilengkapi skema migrasi terindeks (`idx_u_status_pegawai`), penanganan mode arsip (*read-only*), penapisan otomatis pada daftar penilaian atasan langsung, dan perhitungan proporsional (prorata) pada rekap tahunan kepegawaian.

Namun demikian, audit menemukan **1 celah keamanan autentikasi penting (akun nonaktif masih dapat login)**, **ketiadaan validasi tanggal pada validator form**, **asimetri tampilan badge dispensasi pada kartu seluler**, serta peluang peningkatan integritas hubungan atasan-staf ketika seorang pimpinan berstatus mutasi atau pensiun.

---

## 2. Temuan Audit Berdasarkan 8 Pilar Pemeriksaan

### Pilar 4: Keamanan (Security)
- **SP-01: Akun Berstatus "Nonaktif" Masih Dapat Masuk (Login) ke Sistem**
  - **Lokasi:** `app/Controllers/Auth.php:70-137`
  - **Kondisi:** Pada proses autentikasi `Auth::loginProcess()`, setelah verifikasi kata sandi cocok, sistem hanya meregenerasi sesi tanpa memeriksa kolom `status_pegawai`.
  - **Dampak:** Pegawai yang telah dinonaktifkan (karena pemberhentian, mutasi keluar tanpa akses, atau sanksi disiplin) masih dapat login dan menjelajahi dasbor aplikasi.
  - **Rekomendasi:** Tambahkan blokade autentikasi di `Auth.php` khusus untuk status `'nonaktif'`:
    ```php
    if (($data['status_pegawai'] ?? 'aktif') === 'nonaktif') {
        log_audit('FAILED_LOGIN_DISABLED_ACCOUNT', 'users', $data['id']);
        return redirect()->back()->withInput()->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi bagian Kepegawaian.');
    }
    ```
    *(Catatan: Akun `mutasi_keluar` dan `pensiun` tetap diperbolehkan masuk untuk mengakses arsip atau tiket dispensasi).*

- **SP-02: Ketiadaan Proteksi Penguncian Status untuk Akun Superadmin Utama**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:418`
  - **Kondisi:** Pada method `update()`, admin dapat mengubah status pengguna apa pun tanpa mengecualikan akun `admin` utama.
  - **Dampak:** Manipulasi atau kekeliruan pengubahan status pada akun `admin` utama menjadi `nonaktif` berpotensi mengunci akses administratif sistem secara permanen.
  - **Rekomendasi:** Kunci nilai `status_pegawai` untuk akun `admin` utama agar selalu tetap `'aktif'`.

---

### Pilar 1: Struktur & Ketepatan Kode
- **SP-03: Ketiadaan Aturan Validasi CI4 untuk Atribut Status Baru di `store()` dan `update()`**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:175-218` & `345-384`
  - **Kondisi:** Atribut `status_pegawai`, `tmt_selesai`, dan `dispensasi_lampau_sampai` belum didaftarkan pada aturan validasi `$this->validate()` CI4, melainkan hanya mengandalkan sanitasi fallback manual.
  - **Dampak:** Format string tanggal yang tidak valid (misal: bukan `YYYY-MM-DD`) tidak ditolak oleh validasi awal formulir.
  - **Rekomendasi:** Tambahkan aturan validasi:
    - `'status_pegawai' => 'permit_empty|in_list[aktif,mutasi_keluar,pensiun,nonaktif]'`
    - `'tmt_selesai' => 'permit_empty|valid_date[Y-m-d]'`
    - `'dispensasi_lampau_sampai' => 'permit_empty|valid_date[Y-m-d]'`

- **SP-04: Inkonsistensi Nilai `tmt_selesai` Saat Pegawai Diaktifkan Kembali**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:418-420`
  - **Kondisi:** Ketika status pengguna diubah kembali menjadi `'aktif'`, jika input `tmt_selesai` lama masih terkirim, nilai tersebut tetap disimpan ke basis data.
  - **Dampak:** Terjadi anomali data di mana pegawai aktif memiliki tanggal akhir tugas lampau yang dapat membingungkan saat audit data master.
  - **Rekomendasi:** Kosongkan otomatis `tmt_selesai = null` jika status pegawai yang dipilih adalah `'aktif'`.

---

### Pilar 2: Logika Bisnis & Integritas Data
- **SP-05: Hubungan Staf Terbengkalai Saat Pimpinan Berstatus Mutasi/Pensiun**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:405-440`
  - **Kondisi:** Ketika akun seorang atasan langsung (pimpinan unit/katim) diubah statusnya menjadi `mutasi_keluar`, `pensiun`, atau `nonaktif`, relasi `atasan_id` pada staf-staf yang dipimpinnya tidak diperbarui atau diberi peringatan.
  - **Dampak:** Staf yang bersangkutan tetap mengajukan laporan dan target kepada atasan yang sudah tidak aktif berdinas.
  - **Rekomendasi:** Tampilkan peringatan pada antarmuka jika akun yang diubah statusnya masih tercatat sebagai atasan dari pegawai lain, dan sarankan pembaruan penugasan atasan baru.

- **SP-06: Validasi Keharusan TMT Selesai pada Status Mutasi & Pensiun**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:241-244` & `406-409`
  - **Kondisi:** Form mengizinkan pemilihan status `mutasi_keluar` atau `pensiun` tanpa mengisi `tmt_selesai`.
  - **Dampak:** Logika pembatasan tanggal lampau dan penapisan staf atasan mengandalkan `tmt_selesai`. Jika kosong, batasan masa tugas pegawai tidak terdefinisi secara pasti di sistem.
  - **Rekomendasi:** Wajibkan pengisian `tmt_selesai` jika status pegawai yang dipilih adalah `mutasi_keluar` atau `pensiun`.

---

### Pilar 5: Performa & Optimasi Kueri
- **SP-07: Ketiadaan Kolom `status_pegawai` pada Daftar Pengurutan (`$allowedSorts`)**
  - **Lokasi:** `app/Controllers/Admin/UserController.php:91-105`
  - **Kondisi:** Kolom `users.status_pegawai` belum didaftarkan dalam array `$allowedSorts`.
  - **Dampak:** Upaya pengurutan daftar pengguna berdasarkan status diabaikan dan kembali ke pengurutan default nama lengkap.
  - **Rekomendasi:** Daftarkan `'users.status_pegawai' => 'users.status_pegawai'` dan `'status_pegawai' => 'users.status_pegawai'` pada `$allowedSorts`.

---

### Pilar 7: Antarmuka & Aksesibilitas (UI/UX & 8-Point Grid)
- **SP-08: Ketiadaan Badge Dispensasi pada Tampilan Kartu Seluler (*Mobile Card*)**
  - **Lokasi:** `app/Views/admin/users.php:618-628`
  - **Kondisi:** Badge penanda "Dispensasi" telah terpasang pada baris tabel desktop, namun belum ditampilkan pada kartu pengguna versi seluler.
  - **Dampak:** Pengguna yang mengakses melalui ponsel tidak mendapatkan informasi visual mengenai kepemilikan tiket dispensasi aktif.
  - **Rekomendasi:** Tambahkan rendering badge dispensasi yang harmonis pada bagian header kartu seluler di `users.php`.

- **SP-09: Interaktivitas Form Dinamis pada Pemilihan Status Pegawai**
  - **Lokasi:** `app/Views/admin/user_create.php:150-174` & `app/Views/admin/user_edit.php:162-187`
  - **Kondisi:** Kolom `Tanggal Akhir Tugas (TMT)` selalu tampil terbuka meskipun status yang dipilih adalah `Aktif`.
  - **Dampak:** Antarmuka kurang intuitif dan berpotensi memicu pengisian tanggal yang tidak relevan bagi pegawai aktif.
  - **Rekomendasi:** Tambahkan kendali JavaScript sederhana untuk menampilkan kolom TMT hanya saat status dipilih `Mutasi Keluar` atau `Pensiun`.

---

### Pilar 8: Bahasa, Istilah & Mikro-Kopi
- **SP-10: Keselarasan Label Tombol Simpan pada Formulir Pengguna**
  - **Lokasi:** `app/Views/admin/user_edit.php:195`
  - **Kondisi:** Tombol formulir edit berlabel `Simpan Perubahan`.
  - **Rekomendasi:** Sesuaikan menjadi `Simpan Pengguna` agar seragam dengan formulir tambah pengguna dan mematuhi prinsip mikro-kopi singkat, padat, dan jelas.

---

## 3. Matriks Status Audit

| Kode | Temuan | Pilar | Tingkat Risiko | Status Rekomendasi |
|---|---|---|---|---|
| **SP-01** | Akun berstatus `nonaktif` masih dapat login | Keamanan (Pilar 4) | **Tinggi** | Perlu perbaikan segera |
| **SP-02** | Status akun Superadmin utama dapat diubah | Keamanan (Pilar 4) | Sedang | Perlu perbaikan |
| **SP-03** | Ketiadaan validasi CI4 untuk status & tanggal | Struktur Kode (Pilar 1) | Sedang | Perlu perbaikan |
| **SP-04** | TMT lama tidak direset saat status kembali aktif | Integritas Data (Pilar 1) | Rendah | Perlu perbaikan |
| **SP-05** | Hubungan staf binaan saat atasan mutasi/pensiun | Logika Bisnis (Pilar 2) | Sedang | Perlu mitigasi antarmuka |
| **SP-06** | Keharusan mengisi TMT saat mutasi/pensiun | Logika Bisnis (Pilar 2) | Sedang | Perlu perbaikan |
| **SP-07** | Kolom `status_pegawai` belum ada di `$allowedSorts` | Performa/Kueri (Pilar 5) | Rendah | Perlu perbaikan |
| **SP-08** | Badge dispensasi belum muncul di kartu seluler | Antarmuka/UI (Pilar 7) | Rendah | Perlu perbaikan |
| **SP-09** | Interaktivitas dinamis show/hide kolom TMT | Antarmuka/UI (Pilar 7) | Rendah | Usulan peningkatan |
| **SP-10** | Label tombol edit belum seragam "Simpan Pengguna" | Bahasa/Mikro-kopi (Pilar 8) | Rendah | Usulan penyelarasan |

---

## 4. Rencana Implementasi Perbaikan (Implementation Plan)

### Rencana Tindakan:
1. **Keamanan Autentikasi (`Auth.php`)**:
   - Menambahkan pengecekan status di `loginProcess()`: jika `status_pegawai === 'nonaktif'`, batalkan login, catat audit log percobaan login akun nonaktif, dan kembalikan pesan ramah pengguna.
2. **Pengamanan & Validasi Controller (`UserController.php`)**:
   - Menambahkan rule validasi untuk `status_pegawai`, `tmt_selesai`, dan `dispensasi_lampau_sampai`.
   - Mengunci status akun superadmin (`username === 'admin'`) agar selalu `'aktif'`.
   - Mengosongkan otomatis `tmt_selesai` jika status dipilih `'aktif'`.
   - Mendaftarkan `users.status_pegawai` ke dalam `$allowedSorts`.
3. **Penyelarasan Antarmuka (`users.php`, `user_create.php`, `user_edit.php`)**:
   - Menambahkan badge status dispensasi pada kartu seluler di `users.php`.
   - Menambahkan skrip JavaScript untuk toggle tampilan kolom TMT Selesai saat status diubah (hanya muncul saat Mutasi atau Pensiun).
   - Menyelaraskan teks tombol menjadi `Simpan Pengguna`.
