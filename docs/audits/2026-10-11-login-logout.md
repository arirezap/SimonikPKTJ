# Laporan Audit Kode: Fitur dan Tampilan Halaman Login & Logout

**Tanggal:** 11 Oktober 2026  
**Sasaran Modul:** Autentikasi Pengguna (`/login`, `/logout`, Sesi, Remember-Me, Rate Limiting)  
**Berkas Terkait:**  
- `app/Controllers/Auth.php`
- `app/Controllers/BaseController.php`
- `app/Filters/AuthFilter.php`
- `app/Views/login.php`
- `app/Views/layouts/main.php`  
**Acuan Audit:** `audit_code.md`, `.agents/AGENTS.md`, `design.md`

---

## 1. Ringkasan Eksekutif

Audit komprehensif telah dilaksanakan terhadap alur dan antarmuka **Login dan Logout** Evidence Command Center (ECC) berdasarkan 8 pilar evaluasi `audit_code.md` dan standar tata kelola proyek `.agents/AGENTS.md`. Modul ini mengontrol pintu gerbang utama autentikasi, integritas sesi, proteksi brute-force, persistensi login (*Remember Me*), serta pembersihan jejak kredensial saat pengguna keluar.

Sistem telah memiliki fondasi keamanan modern:
- **Proteksi Penyerangan Brute Force**: Terdapat integrasi *Rate Limiting / Throttler* bawaan CodeIgniter 4.
- **Pencegahan Session Hijacking & Fixation**: Dilengkapi regenerasi sesi otomatis (`$session->regenerate(true)`) dan verifikasi sidik jari peramban (*User-Agent Fingerprint SHA-256*).
- **Pencegahan User Enumeration**: Pesan kegagalan login dibuat seragam (*"Nama pengguna atau kata sandi yang Anda masukkan salah."*) baik untuk akun tidak terdaftar maupun kata sandi salah.
- **Auto Hash Upgrade**: Memperbarui hash sandi lama (plaintext/MD5 legacy) secara otomatis ke BCRYPT standar saat pengguna berhasil login.
- **Pembersihan Sesi Total pada Logout**: Logout menghancurkan sesi (`session()->destroy()`), menghapus cookie token `remember_me`, dan menyetel header protektif anti-cache.

Namun demikian, audit menemukan **8 (delapan) kelemahan kritis keamanan, integritas data sesi, dan ergonomi antarmuka**:
1. **Akun Nonaktif / Mutasi / Pensiun Masih Dapat Login Bebas (Kritis)**: Sistem tidak memeriksa `status_pegawai = 'aktif'` saat login maupun saat auto-login remember-me.
2. **Sisa Logika Eskalasi Peran SPM di `BaseController` (Kritis)**: Logika otomatisasi role menjadi `'spm'` masih aktif pada mekanisme auto-login remember-me di `BaseController.php`.
3. **Desinkronisasi Sesi Kolom `unit_id`**: Nilai foreign key `unit_id` tidak disimpan ke sesi saat login normal maupun auto-login, menyebabkan kegagalan fitur terkait unit hingga profil disimpan ulang.
4. **Resiko Pemblokiran Massal Jaringan Kampus (*False Positive Lockout*)**: Throttler membatasi seluruh request per IP tanpa memisahkan percobaan gagal atau username.
5. **Kelemahan Kriptografi Token Remember-Me**: Pembuatan token masih menggunakan `md5()` sederhana tanpa HMAC-SHA256, dan tidak meregenerasi sesi saat auto-login.
6. **Area Sentuh Tombol Toggle Password di Bawah 44px**: Ukuran tombol mata (*show/hide password*) belum memenuhi standar ergonomi layar sentuh mobile.
7. **Potensi Pembatalan Form Submit Akibat `btn.disabled = true` Instan**: Penonaktifan tombol submit dilakukan tepat sebelum event bubbling selesai.
8. **Standarisasi Bahasa & Aksesibilitas**: Judul halaman masih menggunakan kata *"Login"* (bukan *"Masuk ke Akun"*), dan spinner belum mendukung `prefers-reduced-motion`.

---

## 2. Temuan Audit Berdasarkan 8 Pilar Pemeriksaan

### Pilar 1, 2, & 4: Keamanan Akses, Integritas Data, & Autentikasi
- **AUTH-01: Akun Pegawai Nonaktif / Pensiun / Mutasi Masih Dapat Login ke Sistem (Tingkat: Kritis)**
  - **Lokasi:** `app/Controllers/Auth.php:51-70` & `app/Controllers/BaseController.php:74-78`
  - **Kondisi:**
    Saat pengguna memasukkan kredensial yang valid, sistem langsung membuat sesi login tanpa memeriksa kolom `status_pegawai`:
    ```php
    $data = $model->where('username', $username)->first();
    // Tidak ada pengecekan $data['status_pegawai'] === 'aktif'
    ```
    Hal yang sama terjadi pada alur auto-login di `BaseController.php`.
  - **Dampak:** Pegawai yang telah dinonaktifkan, mutasi keluar, atau pensiun tetap memiliki akses penuh ke sistem ECC, melihat laporan, target kinerja, dan dokumen internal institusi.
  - **Rekomendasi:** Tambahkan verifikasi ketat: jika `($data['status_pegawai'] ?? 'aktif') !== 'aktif'`, tolak proses login, catat audit kegagalan dengan alasan `account_inactive`, dan tampilkan pesan: *"Akun Anda saat ini tidak aktif. Silakan hubungi Administrator atau Unit Kepegawaian."*. Pada auto-login `BaseController`, hapus kuki remember-me jika akun tidak aktif.

- **AUTH-02: Eskalasi Peran Mandiri SPM pada Auto-Login Remember Me (Tingkat: Kritis)**
  - **Lokasi:** `app/Controllers/BaseController.php:78-81`
  - **Kondisi:**
    ```php
    $role_aplikasi = $user['role'];
    if (strtolower(trim($user['unit'] ?? '')) === 'satuan penjaminan mutu') {
        $role_aplikasi = 'spm';
    }
    ```
  - **Dampak:** Pengguna biasa yang unit kerjanya Satuan Penjaminan Mutu akan dinaikkan perannya menjadi `spm` secara otomatis saat auto-login via remember-me, membuka akses menu Audit Mutu Internal dan Dasbor Eksekutif tanpa wewenang Administrator.
  - **Rekomendasi:** Hapus blok otomatisasi penentuan role ini dari `BaseController.php`.

- **AUTH-03: Ketiadaan Penyimpanan `unit_id` pada Data Sesi Login**
  - **Lokasi:** `app/Controllers/Auth.php:100-115` & `app/Controllers/BaseController.php:96-110`
  - **Kondisi:**
    Array data sesi `$ses_data` memuat `unit`, `jabatan`, `pangkat`, tetapi tidak menyimpan `unit_id`.
  - **Dampak:** Modul-modul aplikasi yang membaca `session('unit_id')` akan menerima nilai `null` sehingga sinkronisasi unit kerja dapat terganggu sebelum pengguna membuka dan menyimpan profilnya.
  - **Rekomendasi:** Sertakan `'unit_id' => $data['unit_id'] ?? null` ke dalam data sesi di `Auth.php` dan `BaseController.php`.

- **AUTH-04: Throttling Brute-Force Berpotensi Menyebabkan False Positive pada Jaringan Kampus**
  - **Lokasi:** `app/Controllers/Auth.php:41`
  - **Kondisi:**
    Kunci throttling hanya berupa hash IP: `$throttler->check(md5($ipAddress . '_login'), 10, MINUTE)`. Throttler ini dijalankan pada setiap request submit form login (bahkan sebelum kredensial diperiksa).
  - **Dampak:** Di lingkungan kampus PKTJ di mana ratusan pegawai menggunakan jaringan WiFi / NAT publik yang sama, 10 orang yang login dalam 1 menit akan menyebabkan seluruh pegawai lainnya terblokir selama 1 menit meskipun kredensial mereka benar.
  - **Rekomendasi:**
    - Periksa throttling berdasarkan kombinasi IP dan username: `md5($ipAddress . '_' . strtolower($username) . '_login')`.
    - Konsumsi jatah kuota throttling terutama saat percobaan login mengalami kegagalan.

- **AUTH-05: Penggunaan Algoritma Hash Lemah MD5 pada Token Remember-Me**
  - **Lokasi:** `app/Controllers/Auth.php:122` & `app/Controllers/BaseController.php:76`
  - **Kondisi:**
    Token di-generate menggunakan format `$data['id'] . '::' . md5($data['id'] . $data['username'] . $data['password'])`.
    Saat auto-login berhasil di `BaseController.php`, sesi tidak di-regenerate (`$this->session->regenerate(true)`) dan tidak ada pencatatan `log_audit()`.
  - **Dampak:** Rentan terhadap benturan hash MD5 (*hash collision*), dan auto-login tidak terlacak dalam jejak audit login sistem.
  - **Rekomendasi:**
    - Perbarui pembuatan token menggunakan HMAC-SHA256 dengan secret key aplikasi:
      `hash_hmac('sha256', $data['id'] . $data['username'] . $data['password'], config('App')->appTimezone ?? 'ecc_secret')`.
    - Panggil `$this->session->regenerate(true)` dan `log_audit('LOGIN_REMEMBER_ME', 'users', $user['id'])` saat auto-login berhasil.

---

### Pilar 7 & 8: Antarmuka, Ergonomi Layar Sentuh, Bahasa, & Aksesibilitas
- **AUTH-06: Area Sentuh Tombol Toggle Password di Bawah Standar 44px**
  - **Lokasi:** `app/Views/login.php:96-101`
  - **Kondisi:**
    Tombol `#togglePassword` hanya memiliki padding `0.35rem 0.6rem` tanpa dimensi minimum.
  - **Dampak:** Sulit ditekan di perangkat ponsel layar sentuh dan sering meleset ke area input password.
  - **Rekomendasi:** Terapkan standar tombol toggle sesuai `AGENTS.md` Bagian 5:
    `min-height: 36px; min-width: 44px; display: inline-flex; align-items: center; justify-content: center;`.

- **AUTH-07: Potensi Pembatalan Submit Akibat `btn.disabled = true` Instan**
  - **Lokasi:** `app/Views/login.php:345`
  - **Kondisi:**
    Pada event `submit`, tombol login langsung disetel `btn.disabled = true;` di dalam alur sinkron sebelum peramban menyelesaikan pengiriman request HTTP.
  - **Dampak:** Pada sejumlah peramban seluler (WebKit/iOS Safari dan Chrome mobile), menonaktifkan submitter element tepat di event submit dapat menggugurkan payload pengiriman form.
  - **Rekomendasi:** Gunakan penundaan mikro `setTimeout(() => { btn.disabled = true; }, 10);` atau flag `isSubmitting` agar form terkirim sempurna sambil tetap mencegah klik ganda.

- **AUTH-08: Penyelarasan Bahasa Title Bar & Dukungan Reduksi Gerak (Reduced Motion)**
  - **Lokasi:** `app/Views/login.php:6` & `app/Views/login.php:179-194`
  - **Kondisi:**
    - Judul peramban memuat teks: `<title>Login - Evidence Command Center (ECC)</title>`.
    - Animasi `@keyframes spin` pada indikator loading belum memiliki aturan pengecualian untuk pengguna dengan preferensi reduced motion.
  - **Rekomendasi:**
    - Selaraskan judul menjadi: `<title>Masuk ke Akun - Evidence Command Center (ECC)</title>`.
    - Tambahkan media query `@media (prefers-reduced-motion: reduce) { .fit-spinner { animation: none !important; } }`.

---

## 3. Matriks Rekapitulasi Temuan

| ID Temuan | Kategori | Tingkat Keparahan | Status |
| --- | --- | --- | --- |
| **AUTH-01** | Autentikasi Pegawai Aktif | **Kritis (Critical)** | Belum Diperbaiki |
| **AUTH-02** | Keamanan Hak Akses Role | **Kritis (Critical)** | Belum Diperbaiki |
| **AUTH-03** | Integritas Sesi (`unit_id`) | **Sedang (Medium)** | Belum Diperbaiki |
| **AUTH-04** | Rate Limiting Jaringan Kampus | **Sedang (Medium)** | Belum Diperbaiki |
| **AUTH-05** | Kriptografi Remember-Me | **Sedang (Medium)** | Belum Diperbaiki |
| **AUTH-06** | Ergonomi Mobile (Area Sentuh) | **Rendah (Low)** | Belum Diperbaiki |
| **AUTH-07** | Stabilitas Form Submit | **Rendah (Low)** | Belum Diperbaiki |
| **AUTH-08** | Bahasa & Aksesibilitas | **Rendah (Low)** | Belum Diperbaiki |

---

## 4. Rencana Tindak Lanjut

Perbaikan menyeluruh akan dirangkum dalam dokumen **Rencana Implementasi (*Implementation Plan*)** dan diajukan kepada pengguna untuk mendapatkan persetujuan sebelum eksekusi pengubahan kode.
