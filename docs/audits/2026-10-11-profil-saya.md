# Laporan Audit Kode: Menu Profil Saya

**Tanggal:** 11 Oktober 2026  
**Sasaran Modul:** Menu Profil Saya (`/profile`, `/profile/update`)  
**Berkas Terkait:**  
- `app/Controllers/Profile.php`
- `app/Views/profile.php`
- `app/Models/User.php`
- `app/Models/UnitKerja.php`
- `app/Helpers/avatar_helper.php`
- `app/Helpers/audit_helper.php`  
**Acuan Audit:** `audit_code.md`, `.agents/AGENTS.md`, `design.md`

---

## 1. Ringkasan Eksekutif

Pemeriksaan komprehensif telah dilakukan terhadap modul **Profil Saya** (`/profile`) berdasarkan 8 pilar evaluasi `audit_code.md`. Modul ini berfungsi sebagai pusat pengelolaan identitas mandiri pegawai, data kepegawaian (NIP, jabatan, pangkat, unit kerja, atasan langsung), nomor kontak, pembaruan kata sandi, dan foto profil.

Modul ini telah mengimplementasikan sejumlah fitur keamanan dan standar arsitektur yang baik:
- **Proteksi Akses Terfilter**: Seluruh rute profil (`profile` dan `profile/update`) terdaftar eksplisit di dalam grup filter `auth` dengan proteksi CSRF token.
- **Pencegahan Mass Assignment**: Payload penyimpanan basis data disusun secara eksplisit (whitelisted array), dan kolom `username` dikunci secara ketat agar tidak dapat dimanipulasi dari form.
- **Proteksi Self-Atasan Loop**: Terdapat proteksi awal yang menganulir nilai jika pengguna memilih ID akunnya sendiri sebagai atasan langsung (`(int)$atasanIdPost !== (int)$userId`).
- **Validasi Unggahan Foto Berlapis**: Validasi ukuran maksimal 2MB (2,097,152 bytes) dan MIME type (JPG/PNG) telah diterapkan di sisi klien (FileReader) dan sisi server (`is_image[foto]|mime_in|max_size[foto,2048]`), penamaan acak kriptografis (`getRandomName()`), serta folder terlindungi `.htaccess` anti-eksekusi skrip PHP.
- **Pencegahan Tabrakan Identitas**: Memiliki validasi keunikan email dan NIP/NIK terhadap akun lain (`where('id !=', $userId)`).

Namun demikian, audit menemukan **7 poin celah keamanan, integritas data, dan inkonsistensi logika** yang perlu diperbaiki:
1. **Potensi Eskalasi Hak Akses Mandiri (Privilege Escalation)** melalui manipulasi perubahan unit kerja ke *"Satuan Penjaminan Mutu"*.
2. **Hash Kata Sandi Terekspos ke Log Audit Sistem**, melanggar standar privasi dan perlindungan data sensitif.
3. **Desinkronisasi Sesi Pengguna** untuk kolom `nip`, `jabatan`, dan `pangkat` setelah profil diperbarui.
4. **Daftar Kandidat Atasan Langsung Memuat Pegawai Nonaktif** (mutasi keluar, pensiun, nonaktif) karena kueri belum memfilter status kepegawaian aktif.
5. **Ketiadaan Penanganan Khusus untuk Akun Direktur** yang merupakan pimpinan tertinggi institusi (tidak memiliki atasan langsung).
6. **Pemindahan Berkas Foto Menggunakan Path Relatif**, berisiko meleset pada environment server tertentu.
7. **Penyelarasan UI/UX Form**: Belum memanfaatkan helper resmi avatar dan ketiadaan *feedback validation* terarah.

---

## 2. Temuan Audit Berdasarkan 8 Pilar Pemeriksaan

### Pilar 1 & 2: Logika Bisnis & Integritas Data
- **PRF-01: Potensi Eskalasi Peran Mandiri (Privilege Escalation) Melalui Pilihan Unit SPM**
  - **Lokasi:** `app/Controllers/Profile.php:195-200`
  - **Kondisi:**
    ```php
    $role_aplikasi = $user['role'];
    if (strtolower(trim($data['unit'] ?? '')) === 'satuan penjaminan mutu' && in_array($user['role'], ['staf', 'pegawai', 'user'])) {
        $role_aplikasi = 'spm';
        $data['role'] = 'spm';
    }
    ```
  - **Dampak:** Pengguna biasa (staf) dapat menaikkan hak akses perannya sendiri menjadi `spm` hanya dengan memilih Unit Kerja "Satuan Penjaminan Mutu" di formulir profil. Peran `spm` membuka akses ke menu Audit Mutu Internal, Dasbor Eksekutif, dan monitoring luas. Manajemen peran pegawai seharusnya menjadi hak eksklusif Administrator di menu Kelola Pengguna.
  - **Rekomendasi:** Hapus logika otomatisasi perubahan role ini dari controller Profil pengguna. Peran pengguna harus tetap konsisten (`$data['role'] = $user['role']` atau jangan sertakan `$data['role']` pada update profil mandiri).

- **PRF-03: Desinkronisasi Data Sesi (`nip`, `jabatan`, `pangkat`) Pasca-Penyimpanan Profil**
  - **Lokasi:** `app/Controllers/Profile.php:220-230`
  - **Kondisi:** Array pembaruan sesi hanya mencakup `nama`, `nama_lengkap`, `unit`, `unit_id`, dan `role`. Kolom `nip`, `jabatan`, dan `pangkat` tidak diperbarui dalam sesi `session()->set()`.
  - **Dampak:** Komponen antarmuka yang membaca `session('jabatan')`, `session('nip')`, atau `session('pangkat')` tetap menampilkan data lama hingga pengguna keluar dan masuk kembali.
  - **Rekomendasi:** Sertakan seluruh data kepegawaian yang diperbarui ke dalam sesi: `nip`, `jabatan`, `pangkat`, `unit`, `unit_id`, `nama`, `nama_lengkap`, dan `foto`.

- **PRF-04: Kandidat Atasan Langsung Memuat Pegawai Nonaktif (Mutasi Keluar / Pensiun)**
  - **Lokasi:** `app/Controllers/Profile.php:31-36` & `134-136`
  - **Kondisi:** Kueri `$potential_bosses` hanya memfilter `id != $userId` dan `whereIn('role', $bossRoles)` tanpa memeriksa `status_pegawai = 'aktif'`.
  - **Dampak:** Pegawai yang sudah mutasi keluar, pensiun, atau berstatus nonaktif tetap muncul pada dropdown pilihan Atasan Langsung. Selain itu, di method `update()`, belum ada validasi backend bahwa `atasan_id` yang dikirim benar-benar valid dan berstatus aktif.
  - **Rekomendasi:**
    - Tambahkan filter `where('status_pegawai', 'aktif')` pada kueri `$potential_bosses`.
    - Jika pegawai yang sedang login sudah memiliki atasan (meskipun atasan tersebut nonaktif), pastikan data atasan lama tetap dimuat agar relasi tidak terputus secara tidak terduga.
    - Validasi `atasan_id` di method `update()` agar memastikan ID atasan valid atau null.

- **PRF-05: Akun Role Direktur Disajikan Input Pilihan Atasan Langsung**
  - **Lokasi:** `app/Views/profile.php:476-489`, `app/Controllers/Profile.php:31-36`
  - **Kondisi:** Dropdown Atasan Langsung ditampilkan ke seluruh pengguna tanpa pengecualian role Direktur. Sesuai tata kelola ECC (AGENTS.md Bagian 4.A), Direktur adalah pimpinan tertinggi institusi (Tier 1) dan melakukan penilaian mandiri tanpa atasan.
  - **Dampak:** Membingungkan Direktur dan berpotensi menetapkan atasan fiktif bagi Direktur.
  - **Rekomendasi:** Pada akun role `direktur`, sembunyikan dropdown atasan langsung dan tampilkan badge penanda *"Pimpinan Tertinggi Institusi"*, serta pastikan di controller `Profile::update()` nilai `atasan_id` untuk Direktur selalu disetel `null`.

---

### Pilar 4 & 6: Keamanan & Jejak Aktivitas (Audit Log)
- **PRF-02: Hash Kata Sandi Lama dan Baru Terekspos ke Log Audit Sistem**
  - **Lokasi:** `app/Controllers/Profile.php:207`
  - **Kondisi:**
    ```php
    $this->userModel->update($userId, $data);
    log_audit('UPDATE', 'users', $userId, $user, $data);
    ```
    Jika pengguna mengganti kata sandi, `$data['password']` memuat hash kata sandi baru, dan `$user['password']` memuat hash kata sandi lama. Seluruh array ini dikirimkan ke tabel `audit_logs`.
  - **Dampak:** Pelanggaran standar privasi dan keamanan data (Checklist Audit Kode A.82 & B.89). Hash kata sandi tersimpan di tabel log audit yang dapat diakses oleh pihak dengan hak audit.
  - **Rekomendasi:** Lakukan sanitasi data sebelum memanggil `log_audit()`:
    ```php
    $auditOld = $user;
    unset($auditOld['password']);
    $auditNew = $data;
    if (isset($auditNew['password'])) {
        $auditNew['password'] = '[DIUBAH]';
    }
    log_audit('UPDATE', 'users', $userId, $auditOld, $auditNew);
    ```

- **PRF-06: Pemindahan Berkas Foto Menggunakan Path Relatif Ambigu**
  - **Lokasi:** `app/Controllers/Profile.php:170, 183, 191`
  - **Kondisi:** Menggunakan path relatif `'assets/uploads/profile'` pada pemanggilan `$fileFoto->move('assets/uploads/profile', $namaFoto)`.
  - **Dampak:** Jika working directory PHP (CWD) berbeda dengan public root (misal di cPanel / environment tertentu), berkas dapat terunggah ke direktori yang tidak sesuai.
  - **Rekomendasi:** Gunakan path absolut eksplisit `FCPATH . 'assets/uploads/profile'` untuk operasi `move()` dan penghapusan `unlink()`.

---

### Pilar 7 & 8: Antarmuka, Aksesibilitas, & Konsistensi
- **PRF-07: Penyelarasan Inisial Avatar, Mikro-Kopi, dan Feedback Form**
  - **Lokasi:** `app/Views/profile.php:291-298`, `app/Controllers/Profile.php`
  - **Kondisi:**
    - Pembuatan inisial avatar di view `profile.php` ditulis manual secara ad-hoc alih-alih memanfaatkan fungsi standar dari `avatar_helper.php`.
    - Dropdown Unit Kerja dan Atasan belum memiliki teks bantuan saat data tidak ditemukan.
  - **Rekomendasi:**
    - Muat `helper(['avatar'])` dan gunakan fungsi terpusat untuk visual avatar yang konsisten dengan navbar dan modul monitoring.
    - Sederhanakan mikro-kopi dialog sesuai aturan ringkas, padat, dan non-teknis.

---

## 3. Matriks Status Temuan Audit

| Kode | Temuan | Pilar | Tingkat Risiko | Status Rekomendasi | Catatan Perbaikan |
|---|---|---|---|---|---|
| **PRF-01** | Potensi eskalasi peran mandiri ke `spm` via unit kerja | Keamanan/Otorisasi (Pilar 4) | **Tinggi** | **Selesai (Resolved)** | Logika mutasi peran SPM otomatis dihapus; peran pengguna tetap konsisten |
| **PRF-02** | Hash kata sandi terekspos ke tabel audit log | Keamanan/Audit (Pilar 4/6) | **Tinggi** | **Selesai (Resolved)** | Hash kata sandi disanitasi (`[DIUBAH]` / di-unset) sebelum dipassing ke `log_audit()` |
| **PRF-03** | Desinkronisasi sesi `nip`, `jabatan`, `pangkat` pasca update | Integritas Sesi (Pilar 2) | Sedang | **Selesai (Resolved)** | Seluruh data kepegawaian (`nip`, `jabatan`, `pangkat`, `unit`, `unit_id`, `no_hp`) disinkronkan ke sesi |
| **PRF-04** | Dropdown atasan memuat pegawai nonaktif/mutasi/pensiun | Integritas Data (Pilar 2) | Sedang | **Selesai (Resolved)** | Kueri kandidat atasan difilter `status_pegawai = 'aktif'` dan divalidasi ketat di method `update()` |
| **PRF-05** | Role Direktur tidak memiliki penanganan khusus atasan | Logika Bisnis (Pilar 2) | Rendah | **Selesai (Resolved)** | Dropdown atasan disembunyikan untuk akun Direktur dan digantikan banner *"Pimpinan Tertinggi Institusi"*, serta `atasan_id` disetel `null` |
| **PRF-06** | Upload foto memakai path relatif ambigu | Keamanan/Struktur (Pilar 1/4) | Sedang | **Selesai (Resolved)** | Operasi `move()` dan `unlink()` distandarisasi ke path absolut `FCPATH . 'assets/uploads/profile'` |
| **PRF-07** | Penyelarasan inisial avatar & mikro-kopi form | UI/UX / DRY (Pilar 3/7/8) | Rendah | **Selesai (Resolved)** | Helper `avatar` dimuat dan formulir profil diselaraskan |

