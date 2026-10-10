# Laporan Audit Kode: Modul Daftar Pegawai

- **Modul**: Daftar Pegawai (`/daftar-pegawai`)
- **Tanggal Audit**: 10 Oktober 2026
- **Acuan Standar**: [audit_code.md](file:///c:/laragon/www/SimonikPKTJ/audit_code.md), [.agents/AGENTS.md](file:///c:/laragon/www/SimonikPKTJ/.agents/AGENTS.md), dan [design.md](file:///c:/laragon/www/SimonikPKTJ/design.md).
- **Titik Masuk Utama**:
  - Route: [app/Config/Routes.php:65](file:///c:/laragon/www/SimonikPKTJ/app/Config/Routes.php#L65)
  - Controller: [app/Controllers/User/DaftarPegawaiController.php](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/User/DaftarPegawaiController.php)
  - View: [app/Views/user/daftar_pegawai.php](file:///c:/laragon/www/SimonikPKTJ/app/Views/user/daftar_pegawai.php)
  - Helper Terkait: [app/Helpers/avatar_helper.php](file:///c:/laragon/www/SimonikPKTJ/app/Helpers/avatar_helper.php), [app/Helpers/role_helper.php](file:///c:/laragon/www/SimonikPKTJ/app/Helpers/role_helper.php)

---

## 1. Ringkasan Hasil Audit
Modul Daftar Pegawai berfungsi sebagai direktori internal seluruh pegawai di lingkungan Politeknik Keselamatan Transportasi Jalan (PKTJ). Direktori ini menampilkan identitas pegawai, NIP, jabatan, unit kerja, dan atasan langsung dengan fitur pencarian dan penyortiran kolom.

Secara umum arsitektur dasar telah aman (melindungi akun admin dan menggunakan kueri selektif tanpa membocorkan hash password), namun ditemukan **sejumlah kelemahan fungsional pada filter role/unit kerja serta pelanggaran standar UI/UX (visual clutter ikon dekoratif dan ukuran avatar non-grid)**.

---

## 2. Temuan Audit Berdasarkan 8 Pilar `audit_code.md`

### A. Pilar 2: Logika Bisnis & Integritas Data
#### [TEMUAN DP-01 - TINGGI] Filter Role "kabag" Gagal Menampilkan Kepala Bagian (KUK & AAK)
- **Lokasi**: [app/Controllers/User/DaftarPegawaiController.php:59-61](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/User/DaftarPegawaiController.php#L59-L61) dan [app/Views/user/daftar_pegawai.php:85](file:///c:/laragon/www/SimonikPKTJ/app/Views/user/daftar_pegawai.php#L85)
- **Kondisi**: Dropdown filter mengirim nilai `role = 'kabag'`. Controller mengeksekusi `$query->where('users.role', $role);`. Sementara di basis data ECC, akun Kepala Bagian tersimpan dengan role `kabag_kuk` dan `kabag_aak`.
- **Dampak**: Ketika pengguna memfilter "Kepala Bagian (Kabag)", sistem menampilkan 0 hasil (kosong), sehingga pejabat Kabag KUK dan Kabag AAK tidak dapat ditemukan melalui filter ini.
- **Rekomendasi**: Tambahkan penanganan khusus jika `$role === 'kabag'`, kueri harus mencocokkan `whereIn('users.role', ['kabag', 'kabag_aak', 'kabag_kuk'])`.

#### [TEMUAN DP-02 - SEDANG] Filter Role Mengabaikan Tabel Pivot `user_roles` (Multi-Role Support)
- **Lokasi**: [app/Controllers/User/DaftarPegawaiController.php:59-61](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/User/DaftarPegawaiController.php#L59-L61)
- **Kondisi**: Filter role hanya memeriksa kolom primer `users.role`. Di ECC, peran seperti Katim, Kanit, Kepegawaian, SPM, dan Tugas Belajar sering kali diberikan sebagai peran sekunder di tabel `user_roles`.
- **Dampak**: Pegawai yang bertugas sebagai Katim atau Kanit dengan role primer `user` tidak muncul saat difilter berdasarkan role Katim/Kanit.
- **Rekomendasi**: Sertakan pencocokan ID dari tabel `user_roles` saat memfilter role tertentu.

#### [TEMUAN DP-03 - SEDANG] Sinkronisasi Dual-Check pada Filter Unit Kerja
- **Lokasi**: [app/Controllers/User/DaftarPegawaiController.php:63-65](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/User/DaftarPegawaiController.php#L63-L65)
- **Kondisi**: Filter hanya memeriksa `where('users.unit', $unit)` berdasarkan string teks unit.
- **Dampak**: Jika terdapat pegawai lama yang nama unitnya memiliki sedikit variasi ejaan tetapi `users.unit_id` telah terhubung ke `unit_kerja.id`, pegawai tersebut terlewat dari hasil filter.
- **Rekomendasi**: Lakukan pencocokan ganda (`users.unit = $unit OR users.unit_id = $unitId`) sesuai standar Aturan 3 [.agents/AGENTS.md](file:///c:/laragon/www/SimonikPKTJ/.agents/AGENTS.md).

---

### B. Pilar 7: Antarmuka & Aksesibilitas (UI/UX)
#### [TEMUAN DP-04 - TINGGI] Pelanggaran Minimalist Icon Policy (Visual Clutter Ikon Dekoratif Berulang)
- **Lokasi**: [app/Views/user/daftar_pegawai.php:166, 176, 181](file:///c:/laragon/www/SimonikPKTJ/app/Views/user/daftar_pegawai.php#L166)
- **Kondisi**: Di setiap baris tabel direktori terdapat 3 ikon dekoratif:
  - `<i class="bi bi-card-text me-1"></i>` sebelum NIP.
  - `<i class="bi bi-building me-1"></i>` di dalam badge Unit Kerja.
  - `<i class="bi bi-person-badge text-success me-1"></i>` sebelum Nama Atasan.
- **Dampak**: Untuk daftar berisi ratusan pegawai, ratusan ikon dekoratif ini menciptakan kebisingan visual (*visual clutter*) yang berlebihan dan bertentangan dengan Aturan 5 [.agents/AGENTS.md](file:///c:/laragon/www/SimonikPKTJ/.agents/AGENTS.md) (*Minimalist Icon Policy*).
- **Rekomendasi**: Bersihkan ikon dekoratif dari sel tabel. Format teks NIP, badge unit, dan nama atasan sudah cukup jelas dan bersih tanpa ikon redundan.

#### [TEMUAN DP-05 - SEDANG] Pelanggaran 8-Point Grid Scale pada Dimensi Avatar
- **Lokasi**: [app/Views/user/daftar_pegawai.php:15](file:///c:/laragon/www/SimonikPKTJ/app/Views/user/daftar_pegawai.php#L15)
- **Kondisi**: CSS `.avatar-circle-sm` didefinisikan dengan ukuran arbitrary `width: 30px; height: 30px;`, sedangkan fungsi helper `render_user_avatar(..., 32)` mengirim ukuran 32px.
- **Dampak**: Dimensi `30px` melanggar skala grid 8pt (`4px, 8px, 12px, 16px, 24px, 32px, 40px, 48px`).
- **Rekomendasi**: Standarkan CSS `.avatar-circle-sm` menjadi `width: 32px; height: 32px;` (kelipatan 4x base 8pt).

#### [TEMUAN DP-06 - SEDANG] Ketiadaan Tombol Reset Filter Aktif
- **Lokasi**: [app/Views/user/daftar_pegawai.php:51-99](file:///c:/laragon/www/SimonikPKTJ/app/Views/user/daftar_pegawai.php#L51-L99)
- **Kondisi**: Toolbar filter hanya memiliki tombol "Filter". Saat filter pencarian, unit, atau role sedang aktif, tidak ada tombol cepat untuk mengembalikan (*reset*) filter ke kondisi awal.
- **Dampak**: Pengguna harus menghapus teks input dan memilih kembali opsi "Semua" secara manual satu per satu.
- **Rekomendasi**: Tampilkan tombol "Reset" (kembali ke `/daftar-pegawai`) jika ada filter aktif.

#### [TEMUAN DP-07 - SEDANG] Ketiadaan Indikator Role pada Tabel
- **Lokasi**: [app/Views/user/daftar_pegawai.php:170-173](file:///c:/laragon/www/SimonikPKTJ/app/Views/user/daftar_pegawai.php#L170-L173)
- **Kondisi**: Kolom tabel hanya menampilkan Jabatan dan Pangkat, tanpa menampilkan badge role fungsional pegawai.
- **Rekomendasi**: Tampilkan badge role rapi menggunakan helper bawaan `render_role_badge($u['role'])` agar pengguna dapat membedakan tingkatan peran secara visual.

---

## 3. Aspek yang Telah Memenuhi Standar (Lulus Audit)
- **Route Privat**: Endpoint `/daftar-pegawai` dilindungi filter `auth` dalam grup privat.
- **Pencegahan Kebocoran Kredensial**: Query secara ketat memilih kolom spesifik (`select('users.id, users.nama_lengkap, ...')`) tanpa pernah mengekspos hash kata sandi ke view.
- **Penyembunyian Akun Admin**: Akun superadmin (`role = admin`, `username = admin`, dan ID di `user_roles`) disaring keluar dari direktori publik.
- **Sanitasi XSS**: Semua keluaran data string diescape menggunakan fungsi `esc()`.
- **Sorting Whitelist**: Kolom pengurutan divalidasi ketat terhadap array `$validSortColumns` untuk mencegah SQL injection.
