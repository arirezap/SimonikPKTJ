# Laporan Audit Kode & Evaluasi UI/UX: Menu Pengaturan Sistem

- **Modul**: Pengaturan Sistem (`/settings`)
- **Tanggal Audit**: 10 Oktober 2026
- **Acuan Standar**: [audit_code.md](file:///c:/laragon/www/SimonikPKTJ/audit_code.md), [.agents/AGENTS.md](file:///c:/laragon/www/SimonikPKTJ/.agents/AGENTS.md), dan [design.md](file:///c:/laragon/www/SimonikPKTJ/design.md).
- **Titik Masuk Utama**:
  - View: [app/Views/admin/settings/index.php](file:///c:/laragon/www/SimonikPKTJ/app/Views/admin/settings/index.php)
  - Controller: [app/Controllers/Admin/SettingsController.php](file:///c:/laragon/www/SimonikPKTJ/app/Controllers/Admin/SettingsController.php)
  - Model: [app/Models/SettingModel.php](file:///c:/laragon/www/SimonikPKTJ/app/Models/SettingModel.php)
  - Filter: [app/Filters/MaintenanceFilter.php](file:///c:/laragon/www/SimonikPKTJ/app/Filters/MaintenanceFilter.php)

---

## 1. Ringkasan Hasil Audit
Menu Pengaturan Sistem telah berfungsi secara mendasar untuk mengelola saklar batas waktu kinerja, mode pemeliharaan, pencadangan basis data, dan retensi log. Namun dari audit mendalam terhadap aspek **UI/UX, arsitektur tata letak (grid), aksesibilitas, dan mikro-kopi**, ditemukan sejumlah catatan penting dan cacat struktural yang perlu segera dioptimalkan.

---

## 2. Temuan Audit Berdasarkan 8 Pilar

### A. Pilar 7: Antarmuka & Aksesibilitas (UI/UX)

#### [TEMUAN 1 - KRITIKAL] Tag Penutup Grid Kolom Kartu Nomor 4 Hilang (Nesting Error)
- **Bukti**: [app/Views/admin/settings/index.php:336-340](file:///c:/laragon/www/SimonikPKTJ/app/Views/admin/settings/index.php#L336-L340)
- **Kondisi**: Tag penutup kartu `</div>` dan kolom `</div>` (`col-12 col-lg-6`) pada kartu batas waktu nomor 4 (Penilaian Kinerja Staf) tidak lengkap sebelum kartu nomor 5 (Tampilan Heatmap) dimulai.
- **Dampak**: Kartu nomor 5 bersarang (*nested*) di dalam kolom kartu nomor 4, merusak hierarki tata letak flexbox/grid Bootstrap 5 dan dapat menyebabkan kerusakan tata letak pada layar tablet dan seluler.

#### [TEMUAN 2 - TINGGI] Desinkronisasi State UI Saklar dengan Kotak Input Angka
- **Bukti**: [app/Views/admin/settings/index.php:164-167, 218-221, 272-275, 326-329, 503-523](file:///c:/laragon/www/SimonikPKTJ/app/Views/admin/settings/index.php)
- **Kondisi**: Ketika saklar batas waktu dimatikan (status *"Mode Bebas"*), skrip hanya mengubah badge status dan transparansi kartu, namun kotak input angka tetap aktif (`disabled = false`) dan berstatus `required`.
- **Dampak UX**: Membingungkan pengguna karena batas waktu dinyatakan nonaktif, namun kolom angka tetap wajib diisi dan dapat diketik.
- **Rekomendasi**: Ketika saklar nonaktif (OFF), kotak input angka otomatis dinonaktifkan (`prop('disabled', true)`), atribut `required` dihilangkan sementara, dan kotak parameter dibuat redup. Saat saklar diaktifkan kembali (ON), kotak diaktifkan kembali.

#### [TEMUAN 3 - SEDANG] Pelanggaran 8-Point Grid Scale pada Badge Angka Kartu
- **Bukti**: [app/Views/admin/settings/index.php:130, 184, 238, 292](file:///c:/laragon/www/SimonikPKTJ/app/Views/admin/settings/index.php)
- **Kondisi**: Badge nomor urut kartu menggunakan dimensi arbitrary `width: 28px; height: 28px;`.
- **Dampak**: Angka `28px` melanggar aturan baku 8-Point Grid Scale ECC (`4px, 8px, 12px, 16px, 24px, 32px, 40px, 48px`).
- **Rekomendasi**: Sesuaikan ukuran badge menjadi `32px × 32px` (kelipatan 4x base 8pt) dengan sudut membulat proporsional.

#### [TEMUAN 4 - SEDANG] Pelanggaran Minimalist Icon Policy (Visual Clutter)
- **Bukti**: [app/Views/admin/settings/index.php:114, 159, 213, 267, 321, 575, 617](file:///c:/laragon/www/SimonikPKTJ/app/Views/admin/settings/index.php)
- **Kondisi**: Terdapat ikon dekoratif yang berlebihan pada label form dan di dalam tombol dialog pop-up konfirmasi.
- **Dampak**: Berlawanan dengan aturan kesederhanaan visual (*anti-visual clutter*): ikon hanya digunakan jika sangat dibutuhkan untuk aksi fungsional.
- **Rekomendasi**: Bersihkan ikon dekoratif dari label form yang teksnya sudah jelas dengan sendirinya, serta sederhanakan tombol pop-up konfirmasi SweetAlert.

#### [TEMUAN 5 - SEDANG] Aksesibilitas Target Sentuh Mobile (< 44px)
- **Bukti**: [app/Views/admin/settings/index.php:164, 218, 272, 326](file:///c:/laragon/www/SimonikPKTJ/app/Views/admin/settings/index.php)
- **Kondisi**: Kotak input angka menggunakan `input-group-sm` dengan tinggi elemen sekitar `31px`. Pada layar sentuh seluler, ukuran ini di bawah standar target sentuh minimal 44px.
- **Rekomendasi**: Pada viewport seluler (`@media (max-width: 575.98px)`), berikan tinggi minimal sentuh `38px–44px` agar mudah ditekan jari pengguna.

---

### B. Pilar 8: Bahasa, Istilah & Mikro-Kopi

#### [TEMUAN 6 - RENDAH] Penggunaan Istilah Teknis pada Teks Pengguna
- **Bukti**: [app/Views/admin/settings/index.php:108, 399, 421](file:///c:/laragon/www/SimonikPKTJ/app/Views/admin/settings/index.php)
- **Kondisi**: Ditemukan frasa berorientasi teknis server yang dilarang pada Aturan 6:
  - *"tanpa perlu menyunting file cPanel"* (baris 108)
  - *"disaster recovery"* (baris 399)
  - *"restore melalui menu Import"* (baris 421)
- **Rekomendasi**: Ganti dengan bahasa Indonesia yang wajar dan berorientasi pengguna:
  - *"Pengguna otomatis dialihkan ke halaman pemeliharaan sementara."*
  - *"Pencadangan data untuk pemulihan sewaktu-waktu."*
  - *"Berkas cadangan dapat dipulihkan kembali kapan saja."*

---

## 3. Status Evaluasi Keamanan & Data (Pilar 1, 2, 4, 6)
- **Route Privat & Autentikasi**: Lulus. Endpoint `/settings` dilindungi filter `auth` dan pemeriksaan role `admin`.
- **CSRF Protection**: Lulus. Form settings dan purge logs menyertakan `csrf_field()`.
- **Validasi Nilai Clamping**: Lulus. Batas input tanggal dan toleransi hari telah diproteksi di backend (`min`/`max` clamping).
- **Audit Logging**: Lulus. Setiap pembaruan settings, pencadangan basis data, dan pembersihan log tercatat lengkap di tabel `audit_logs`.
- **Cache Invalidation**: Lulus. `SettingModel::clearCache()` dipanggil seketika saat data disimpan.
