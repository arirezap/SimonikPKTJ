# Laporan Audit Kode: Menu Kelola Tim

**Tanggal:** 11 Oktober 2026  
**Sasaran Modul:** Menu Kelola Tim (`/tim`, `/tim/add`, `/tim/remove`, `/tim/update_unit`)  
**Berkas Terkait:**  
- `app/Controllers/User/TimController.php`
- `app/Views/user/tim_saya.php`
- `app/Models/User.php`
- `app/Models/UnitKerja.php`
- `app/Helpers/avatar_helper.php`
- `app/Helpers/audit_helper.php`
- `app/Helpers/role_helper.php`  
**Acuan Audit:** `audit_code.md`, `.agents/AGENTS.md`, `design.md`

---

## 1. Ringkasan Eksekutif

Audit komprehensif telah dilaksanakan terhadap modul **Kelola Tim** (`/tim`) mengacu pada 8 pilar evaluasi `audit_code.md` dan standar tata kelola proyek `.agents/AGENTS.md`. Modul ini digunakan oleh pimpinan unit kerja (Kabag, Kanit, Katim, Kapokja, Manajemen, Admin) untuk mengatur komposisi anggota tim kerja, menetapkan atasan langsung staf, serta melakukan pemutakhiran unit kerja staf secara dinamis.

Modul ini telah mengadopsi beberapa aspek desain dan keamanan yang baik:
- **Proteksi Rute Terfilter**: Rute `/tim`, `/tim/add`, `/tim/remove`, dan `/tim/update_unit` telah berada di dalam grup filter `auth` dengan proteksi CSRF token.
- **Kueri Kolom Selektif**: Pengambilan data profil pegawai menggunakan kueri kolom selektif (`select('id, nama_lengkap, nip, unit, unit_id, jabatan, role, atasan_id, foto')`) tanpa mengekspos hash kata sandi ke view template.
- **Tata Letak Bento 8-Point Grid**: Tampilan kartu KPI, pencarian real-time di sisi klien, dan tabel responsif telah selaras dengan sistem grid 8-point ECC.
- **Integrasi Audit Log**: Telah terdapat pemanggilan `log_audit()` pada setiap aksi penambahan, penghapusan, dan pembaruan unit staf.

Meskipun demikian, audit mendeteksi **8 (delapan) kelemahan arsitektur, keamanan akses, integritas hierarki, dan interaksi antarmuka** yang harus ditangani:
1. **Otorisasi Tidak Konsisten pada Endpoint Mutasi (Broken Access Control)**: Method `addStaf()`, `removeStaf()`, dan `updateUnit()` tidak memvalidasi hak peran pimpinan, berpotensi dipanggil oleh pengguna role staf biasa.
2. **Potensi Eskalasi Hak Akses Peran SPM di `updateUnit()`**: Pengubahan unit kerja staf ke "Satuan Penjaminan Mutu" secara otomatis mengubah kolom `role` staf menjadi `'spm'`.
3. **Penyaringan Status Pegawai Aktif Belum Diterapkan**: Pegawai nonaktif (pensiun, mutasi keluar) masih muncul di daftar tim dan dapat dipilih pada formulir penambahan anggota tim.
4. **Ketiadaan Proteksi Lingkaran Hierarki (Cycle / Ancestor Loop)**: Belum ada validasi untuk mencegah pimpinan menambahkan atasan langsungnya sendiri sebagai staf (hierarki melingkar).
5. **Inkonsistensi Nilai Kosong Kolom `atasan_id`**: Mengosongkan `atasan_id` menggunakan integer `0` alih-alih `null` (sesuai skema kolom `users.atasan_id INT NULL`), serta auto-assign atasan yang rapuh saat update unit.
6. **Potensi DOM-based XSS pada Template Select2**: Pilihan Select2 diinterpolasi langsung ke dalam HTML template literal tanpa pembersihan karakter khusus di JavaScript.
7. **Ketiadaan Database Transaction & Data Pembanding Audit Log**: Mutasi data tim belum dilindungi transaksi atomik (`transStart`/`transComplete`), dan `old_data` pada `log_audit()` selalu bernilai `null`.
8. **Penyelarasan UI/UX, Area Sentuh Mobile, & Mikro-Kopi**: Indikator `.spin-icon` tidak memiliki animasi CSS berputar, tombol mobile belum mencapai 44px, dan dialog SweetAlert2 belum mematuhi aturan mikro-kopi padat.

---

## 2. Temuan Audit Berdasarkan 8 Pilar Pemeriksaan

### Pilar 1 & 4: Struktur Kode & Keamanan Akses (Access Control)
- **TIM-01: Ketiadaan Verifikasi Peran Pimpinan pada Endpoint Mutasi (`addStaf`, `removeStaf`, `updateUnit`)**
  - **Lokasi:** `app/Controllers/User/TimController.php:78-206`
  - **Kondisi:**
    Pemeriksaan otorisasi peran pimpinan hanya diterapkan di method `index()`:
    ```php
    if (!hasAnyRole(['manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'admin'])) {
        return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki hak akses ke modul Kelola Tim.');
    }
    ```
    Pada method `addStaf()`, `removeStaf()`, dan `updateUnit()`, pengecekan ini **hilang sama sekali**.
  - **Dampak:** Akun pengguna biasa (role `pegawai`/`staf`) dapat mengirimkan request POST langsung ke `/tim/add`, `/tim/remove`, atau `/tim/update_unit` untuk mengubah atasan atau unit kerja pegawai lain tanpa memiliki wewenang manajerial.
  - **Rekomendasi:** Terapkan verifikasi peran pimpinan terpusat atau di awal setiap method aksi mutasi:
    ```php
    if (!hasAnyRole(['manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'admin'])) {
        // Blokir akses dengan redirect error atau JSON response 403
    }
    ```

- **TIM-02: Otomatisasi Perubahan Role Menjadi 'spm' pada Pembaruan Unit Kerja**
  - **Lokasi:** `app/Controllers/User/TimController.php:178-180`
  - **Kondisi:**
    ```php
    if (strtolower(trim($unitNama ?? '')) === 'satuan penjaminan mutu') {
        $updateData['role'] = 'spm';
    }
    ```
  - **Dampak:** Setiap pimpinan tim dapat secara sepihak menaikkan hak akses stafnya menjadi `spm` hanya dengan memilih unit kerja SPM. Peran `spm` membuka hak akses ke modul Audit Mutu Internal, Dasbor Eksekutif, dan monitoring institusi. Pengelolaan role adalah domain eksklusif Administrator pada modul Kelola Pengguna.
  - **Rekomendasi:** Hapus baris pengubahan role otomatis ini agar peran pengguna tidak berubah saat unit kerja disesuaikan.

---

### Pilar 2 & 5: Logika Bisnis, Integritas Data, & Performa Kueri
- **TIM-03: Ketiadaan Filter Status Pegawai Aktif (`status_pegawai = 'aktif'`)**
  - **Lokasi:** `app/Controllers/User/TimController.php:33-61` & `98-101`
  - **Kondisi:**
    - Pengambilan daftar anggota tim di `index()` (`$stafQuery`) tidak menyaring `status_pegawai = 'aktif'`.
    - Pengambilan kandidat dropdown penambahan staf (`$semuaPegawaiDb`) memuat seluruh pengguna tanpa menyaring `status_pegawai = 'aktif'`.
    - Validasi di `addStaf()` tidak memeriksa status aktif pegawai sasaran.
  - **Dampak:** Pegawai yang telah mutasi keluar, pensiun, atau dinonaktifkan tetap muncul di dalam tabel tim dan dapat dipilih kembali ke dalam tim.
  - **Rekomendasi:** Tambahkan filter `where('status_pegawai', 'aktif')` pada seluruh kueri dan validasi status aktif sebelum menambahkan staf ke tim.

- **TIM-04: Ketiadaan Proteksi Rantai Hierarki Melingkar (Circular Hierarchy / Cycle Prevention)**
  - **Lokasi:** `app/Controllers/User/TimController.php:94`
  - **Kondisi:**
    Pengecekan saat ini hanya: `if (empty($stafId) || (int)$stafId === (int)$myId)`.
    Belum ada proteksi jika pimpinan menambahkan atasannya sendiri (atau atasan di tingkat yang lebih tinggi) sebagai stafnya.
  - **Dampak:** Terjadi lingkaran setan hierarki (pimpinan membawahi atasannya sendiri). Hal ini dapat merusak rantai supervisi dan menyebabkan kegagalan logika rekursif pada alur persetujuan atau pelaporan.
  - **Rekomendasi:** Tambahkan pengecekan rantai atasan (ancestor traversal check). Jika pegawai sasaran berada dalam garis atasan pimpinan yang sedang login, batalkan penambahan dengan pesan error.

- **TIM-05: Inkonsistensi Nilai Kosong Atasan (`0` vs `null`) dan Auto-Assign yang Rapuh**
  - **Lokasi:** `app/Controllers/User/TimController.php:126, 190, 193`
  - **Kondisi:**
    - Saat staf dikeluarkan dari tim (`removeStaf`), kolom `atasan_id` disetel ke `0`. Padahal skema kolom di database adalah `INT NULL` (di `Profile.php` nilai kosong menggunakan `null`). Nilai `0` dapat merusak integritas relasi foreign key `users.atasan_id = users.id`.
    - Di `updateUnit()`, sistem otomatis mencari pimpinan unit baru dan mengalihkan `atasan_id` tanpa konfirmasi pengguna dan tanpa memeriksa status aktif pimpinan tersebut. Jika tidak ditemukan pimpinan, `atasan_id` disetel `0`.
  - **Dampak:** Inkonsistensi data relasi di basis data dan risiko hilangnya supervisi staf secara tak terduga.
  - **Rekomendasi:** Gunakan nilai `null` untuk mengosongkan `atasan_id`. Pastikan pencarian pimpinan unit baru menyaring pimpinan aktif dan tidak mengarahkan ke diri staf sendiri (`id != $userId`).

- **TIM-06: Penyaringan Staf Tim pada `index()` dan Pemisahan Staf Antar Pimpinan**
  - **Lokasi:** `app/Controllers/User/TimController.php:36-43`
  - **Kondisi:**
    Kueri `index()` menggunakan `orWhere('unit', $myUnit)` secara terbuka. Jika ada staf di unit yang sama yang sebenarnya berada di bawah atasan lain, staf tersebut tetap tampil di daftar tim pengguna saat ini.
  - **Dampak:** Terjadi kerancuan supervisi jika dalam satu unit kerja terdapat lebih dari satu pimpinan/koordinator.
  - **Rekomendasi:** Batasi kueri unit hanya untuk staf yang belum memiliki atasan (`atasan_id IS NULL OR atasan_id = 0`), atau prioritaskan staf yang secara eksplisit memiliki `atasan_id = $userId`.

---

### Pilar 4 & 6: Keamanan Masukan, Penanganan Transaksi, & Jejak Audit
- **TIM-07: Ketiadaan Database Transaction & Data Pembanding pada Audit Log**
  - **Lokasi:** `app/Controllers/User/TimController.php:103-108, 125-130, 196-202`
  - **Kondisi:**
    Operasi mutasi data pada `addStaf()`, `removeStaf()`, dan `updateUnit()` tidak dibungkus dalam blok Database Transaction (`$db->transStart()` dan `$db->transComplete()`). Selain itu, pemanggilan `log_audit()` selalu mengirim parameter `old_data` bernilai `null`.
  - **Dampak:** Jika terjadi kegagalan sistem di tengah proses, integritas data tidak terjamin. Jejak audit tidak mencatat data sebelum perubahan sehingga riwayat mutasi unit/atasan tidak terlacak secara akuntabel.
  - **Rekomendasi:** Bungkus setiap operasi mutasi dalam database transaction dan simpan data lama sebelum pembaruan (`['atasan_id' => $old['atasan_id'], 'unit' => $old['unit'], 'unit_id' => $old['unit_id']]`) ke dalam `log_audit()`.

- **TIM-08: Potensi DOM-based XSS pada Penataan Pilihan Select2 Modal Tambah Staf**
  - **Lokasi:** `app/Views/user/tim_saya.php:584-617`
  - **Kondisi:**
    Fungsi JavaScript `formatPegawaiOption(state)` membaca atribut `dataset.nama`, `dataset.nip`, `dataset.jabatan`, dan `dataset.unit`, lalu langsung merangkainya ke dalam string template literal HTML `${nama}`, `${jabatan}`, `${nip}`, `${unit}` yang diproses oleh jQuery `$()`.
  - **Dampak:** Jika terdapat data nama atau jabatan pegawai yang mengandung karakter HTML spesial atau skrip terselubung, data tersebut dieksekusi sebagai elemen DOM aktif.
  - **Rekomendasi:** Terapkan fungsi pembersih teks HTML (sanitizer/escapeHtml) sebelum merangkainya ke dalam template jQuery Select2.

---

### Pilar 7 & 8: Antarmuka, Mikro-Kopi, 8-Point Grid, & Aksesibilitas
- **TIM-09: Definisi Animasi `.spin-icon` Tidak Ditemukan**
  - **Lokasi:** `app/Views/user/tim_saya.php:723`
  - **Kondisi:**
    Elemen status AJAX memuat `<i class="bi bi-arrow-repeat spin-icon"></i>`, namun selektor CSS `.spin-icon` tidak memiliki definisi keyframe rotasi di tag `<style>`.
  - **Dampak:** Indikator proses pembaruan unit kerja tampak beku (statis) saat proses AJAX berlangsung.
  - **Rekomendasi:** Tambahkan definisi animasi `@keyframes spinIcon` dan kelas `.spin-icon { animation: spinIcon 1s linear infinite; }` yang menghormati `@media (prefers-reduced-motion: reduce)`.

- **TIM-10: Area Sentuh Tombol Aksi Mobile Belum Memenuhi Standar 44px**
  - **Lokasi:** `app/Views/user/tim_saya.php:484`
  - **Kondisi:**
    Tombol "Keluarkan dari Tim" pada kartu mobile memiliki tinggi `min-height: 40px`. Standar ergonomi seluler ECC dan Checklist Audit C.100 mensyaratkan area sentuh minimum 44px.
  - **Rekomendasi:** Sesuaikan menjadi `min-height: 44px;`.

- **TIM-11: Bahasa Dialog Konfirmasi SweetAlert2 Masih Bertele-tele**
  - **Lokasi:** `app/Views/user/tim_saya.php:778-780`
  - **Kondisi:**
    Teks konfirmasi menggunakan: `"Apakah Anda yakin ingin mengeluarkan ${namaPegawai} dari tim Anda?"`.
  - **Dampak:** Melanggar standar mikro-kopi ECC (AGENTS.md Bagian 6) yang mensyaratkan kalimat singkat, tenang, dan padat (1 kalimat pendek, non-pertanyaan bertele-tele).
  - **Rekomendasi:** Ubah menjadi: `"Pegawai ini akan dikeluarkan dari tim Anda."`.

- **TIM-12: CSRF Token Tidak Diperbarui pada Respon Error AJAX 403**
  - **Lokasi:** `app/Controllers/User/TimController.php:152` & `app/Views/user/tim_saya.php:742-764`
  - **Kondisi:**
    Ketika validasi staf gagal di baris 152, respon JSON 403 tidak menyertakan hash CSRF baru. Akibatnya, form di browser mengalami inkonsistensi token CSRF jika pengguna melakukan aksi berikutnya.
  - **Rekomendasi:** Sertakan `csrf_token() => csrf_hash()` pada seluruh respon JSON, baik sukses maupun gagal.

---

## 3. Matriks Rekapitulasi Temuan

| ID Temuan | Kategori | Tingkat Keparahan | Status |
| --- | --- | --- | --- |
| **TIM-01** | Otorisasi Akses Mutasi | **Tinggi (High)** | Belum Diperbaiki |
| **TIM-02** | Keamanan Hak Akses Role | **Tinggi (High)** | Belum Diperbaiki |
| **TIM-03** | Integritas Status Pegawai | **Sedang (Medium)** | Belum Diperbaiki |
| **TIM-04** | Integritas Hierarki (Cycle) | **Sedang (Medium)** | Belum Diperbaiki |
| **TIM-05** | Skema Database & Relasi Atasan | **Sedang (Medium)** | Belum Diperbaiki |
| **TIM-06** | Keamanan DOM-based XSS Select2 | **Sedang (Medium)** | Belum Diperbaiki |
| **TIM-07** | Database Transaction & Audit Log | **Sedang (Medium)** | Belum Diperbaiki |
| **TIM-08** | UI/UX, Animasi, & Mikro-Kopi | **Rendah (Low)** | Belum Diperbaiki |

---

## 4. Rencana Tindak Lanjut

Perbaikan menyeluruh akan dirangkum dalam dokumen **Rencana Implementasi (*Implementation Plan*)** dan diajukan kepada pengguna untuk mendapatkan persetujuan sebelum eksekusi pengubahan kode.
