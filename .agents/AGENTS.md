# Workspace Rules: Evidence Command Center (ECC)

## 1. Terminology & Branding Rules
- **Strictly use "staf"**: When referring to subordinates or team members in UI labels, documentation, and source code variables, always use **"staf"** (instead of "bawahan" or "staff"). "Staf" is the official polished standard in this corporate environment.
  - UI labels: *"Penilaian Staf"*, *"Daftar Staf"*, *"Staf Saya"*.
  - Code variables: `$stafIdTerpilih`, `getStaf()`, `$daftarStaf`.
- **Strictly use "Evidence Command Center (ECC)"**: The official application name is **Evidence Command Center (ECC)** (or **ECC**). "Simonik" is the legacy application name (even though the root directory name is `SimonikPKTJ`). Always use **Evidence Command Center (ECC)** or **ECC** across all UI labels, page titles, headers, footers, system logs, documentation, and agent responses (never "Simonik").

---

## 2. Core Architecture & Routing Rules
- **Framework & Stack**: CodeIgniter 4 (PHP 8.1+), MySQL, Bootstrap 5.3, Flatpickr, Chart.js, jQuery, SweetAlert2.
- **Explicit Route Registration (`app/Config/Routes.php`)**:
  - All private/authenticated routes **MUST** be explicitly enclosed in `['filter' => 'auth']` groups. Never leave sensitive routes unauthenticated outside filter groups.
  - All AJAX endpoints and state mutation actions (store, update, delete, approve, sync, reset) **MUST** use `POST` (or verified `$routes->match(['get', 'post'], ...)`).
  - Never rely on legacy auto-routing for production endpoints (`$routes->setAutoRoute(false);` is strictly enforced).

---

## 3. Security & Data Integrity Standards
- **CSRF Protection**: All `<form>` elements must include `<?= csrf_field() ?>`. AJAX POST requests must send `<?= csrf_token() ?>` and receive updated `csrf_hash` in the JSON response.
- **XSS Sanitization & URL Schemes**: Never output raw database strings into HTML views without escaping (`esc($var)`). For dynamic links (e.g. `link_bukti`), always sanitize that the URL scheme begins strictly with `http://` or `https://` before rendering into `<a href="...">`.
- **IDOR & Ownership Validation**: Every mutative endpoint updating collections or batch records (e.g. `log_tugas_tambahan`, `target_kinerja_bulanan`) MUST explicitly validate ownership against the target user/staf ID (`(int)$record['user_id'] === (int)$targetUserId`).
- **Password Security**: Always use `password_hash($password, PASSWORD_DEFAULT)` and avoid user enumeration by using generic error messages on authentication failure.
- **Audit Logging**: Use `log_audit()` helper from `app/Helpers/audit_helper.php` for all crucial data modifications (`CREATE`, `UPDATE`, `DELETE`, `LOGIN`, `LOGOUT`, `APPROVE`, `UNLOCK_LAPORAN`, `CANCEL_APPROVE_TARGET`, `RESET_PENILAIAN_KINERJA`, `EXPORT_EXCEL_MONITORING_TARGET`, `EXPORT_PDF_MONITORING_TARGET`, `EXPORT_EXCEL_REKAP_KINERJA`, `EXPORT_PDF_REKAP_KINERJA`).
- **Database Safety, Transactions & Optimization**:
  - Wrap multi-step mutations in Database Transactions (`$db->transStart()` and `$db->transComplete()`) and `try...catch (\Exception $e)`.
  - Every new database table column must be declared in `$allowedFields` of the corresponding Model class.
  - Always sanitize decimal inputs with `str_replace(',', '.', trim((string)$val))` to support Indonesian comma notation.
  - **Selective Column Queries in Directory & Large Datasets**: Always use selective column queries (e.g. `select('users.id, users.nama_lengkap, users.nip, users.username, users.jabatan, users.pangkat, users.unit, users.role, users.foto, users.atasan_id')`) and never expose `users.*` (password hash) to view templates.
  - **Dual-Sync `unit_id` & `unit`**: When updating employee units, always perform dual-sync between the unit name string (`users.unit`) and integer foreign key (`users.unit_id`) mapped to `unit_kerja.id`.
  - **Form State Preservation (`old()`)**: Every form field must be repopulated with `old('field_name', $fallback)` to guarantee user input is preserved across validation errors.
- **Upload Security & Defense-in-Depth (`public/assets/uploads/`)**:
  - **Dual-Layer 2MB Limit**: Enforce 2MB maximum file size on client-side (JavaScript FileReader `2 * 1024 * 1024` bytes) and server-side (`max_size[foto,2048]`).
  - **Magic Bytes & MIME Validation**: Always inspect image binary structure using `is_image[foto]` and strict MIME types (`mime_in[foto,image/jpg,image/jpeg,image/png]`).
  - **Cryptographic Random Renaming**: Never preserve original client filenames. Always use `$file->getRandomName()` to completely neutralize path traversal, null-byte injection, and filename collision attacks.
  - **Script Execution Barrier (`.htaccess`)**: Ensure `public/assets/uploads/.htaccess` disables PHP execution (`php_flag engine off`), blocks all executable script extensions (`.php`, `.phtml`, `.cgi`, `.sh`), and disables directory indexing (`Options -Indexes -ExecCGI`).
  - **Safe Unlinking**: Use `basename()` and check `file_exists(FCPATH . $oldFile)` when removing old photos, strictly guarding against deleting system assets (e.g. `default.png`).

---

## 4. Key Business Logic & Workflow Rules

### A. Auto-Approval & Self-Revision Direktur
- Khusus akun role `direktur`, pembuatan Target Kinerja Bulanan otomatis disetujui (`status_approval = 'disetujui'`, `status = 'terkirim'`) dan dapat direvisi secara mandiri oleh Direktur kapan saja tanpa memerlukan persetujuan pihak lain.

### B. Prasyarat Penilaian Kinerja oleh Atasan Langsung
- Atasan Langsung **hanya dapat memberikan nilai kinerja bulanan** kepada staf jika seluruh Target Kinerja Bulanan staf pada periode tersebut sudah berstatus `disetujui`. Jika belum disetujui, input nilai di formulir penilaian terkunci dan tampil notifikasi pengingat untuk menyetujui target terlebih dahulu.

### C. Standardisasi Ambang Batas Predikat Kinerja
Seluruh modul perhitungan predikat (Controller, Modal AJAX, Desktop Table, Mobile Card, PDF, Excel) wajib mematuhi formula resmi:
- **Sangat Baik**: `> 100%` s.d. `150%`
- **Baik**: `> 90%` s.d. `100%`
- **Butuh Perbaikan**: `> 75%` s.d. `90%`
- **Kurang**: `> 25%` s.d. `75%`
- **Sangat Kurang**: `<= 25%`
- **Belum Dinilai**: `0%` (atau belum ada penilaian / RHK dinilai = 0 / NULL)

### D. Mekanisme Reset Penilaian Kinerja
- Saat atasan melakukan **Reset Nilai** atau mengosongkan nilai capaian:
  - Nilai capaian di basis data dikosongkan menjadi `NULL`.
  - Flag status penilaian diset ke `NULL` (bukan menjadi `terbit` dengan nilai 0).
  - Status kembali menjadi **"Belum Dinilai"** persis seperti saat belum pernah diisi.

### E. Otorisasi Menu, Modul Kepegawaian & Visibilitas Dashboard
- Menu tree **Kepegawaian** (Monitoring Target Kinerja & Monitoring Penilaian Kinerja) pada sidebar dan endpoint controllernya **HANYA** muncul dan dapat diakses oleh akun dengan role: `direktur`, `wadir`, `kabag` (`kabag_aak`, `kabag_kuk`), `kepegawaian`, dan `admin`.
- Role `wadir` memiliki akses monitoring luas tetapi secara eksplisit **tidak memiliki wewenang menilai atau merevisi target staf** (hanya mengelola "Target Saya").
- **Visibilitas Dashboard Institusi Penuh**: Khusus akun dengan role `direktur`, `wadir`, `kabag` (`kabag_aak`, `kabag_kuk`), dan `kepegawaian` (termasuk Katim Kepegawaian), dasbor eksekutif membuka hak visibilitas penuh (`$canSeeAll = true`) untuk memantau performa seluruh pegawai di lingkungan PKTJ (bukan terbatas hanya pada unit binaannya).

### F. Fleksibilitas Pengeditan Target Kinerja Bulanan (Sebelum Disetujui)
- **Staf berhak mengedit, menambah, atau menghapus target kinerja selagi belum disetujui atasan langsung**:
  - Selama `status_approval != 'disetujui'`, baris input tidak dikunci dan tombol submit formulir beradaptasi secara kontekstual:
    - Status Draf/Baru: Berlabel `<i class="bi bi-send"></i> Ajukan Target`.
    - Status Menunggu Persetujuan: Berlabel `<i class="bi bi-arrow-repeat"></i> Perbarui & Ajukan Ulang`.
  - **Notifikasi Bertahap ke Atasan Langsung**:
    - Pengajuan awal: *"Persetujuan Target Bulanan: [Nama Staf] mengirimkan Target Bulanan..."*
    - Pengajuan pembaruan: *"Pembaruan Target Bulanan: [Nama Staf] memperbarui Target Bulanan..."*
  - **Inisialisasi Defensif & Failsafe Notifikasi**:
    - Variabel `$targetUser` wajib diinisialisasi secara eksplisit di awal method `store()` sebelum pengecekan alur (mencegah `Undefined variable $targetUser` / Error 500 pada PHP 8.1+ / production).
    - Seluruh pemanggilan helper notifikasi (`send_notification`) wajib dibungkus dalam blok `try...catch (\Throwable $e)` agar kendala jaringan/database notifikasi tidak pernah menggagalkan penyimpanan target utama.
  - Penguncian permanen formulir target (*readonly*) hanya terjadi setelah disetujui oleh atasan langsung (`status_approval = 'disetujui'`), kecuali akun role `direktur` yang memiliki hak revisi mandiri sewaktu-waktu.

### G. Standar Prosedur Audit & Rencana Implementasi (Implementation Plan)
- Setiap kali audit kode selesai dilakukan dan menemukan celah atau kebutuhan perbaikan, agen **WAJIB menyusun Rencana Implementasi (*Implementation Plan*)** terlebih dahulu sebelum mengeksekusi perubahan kode pada berkas manapun.
- **Larangan Testing Otomatis Mandiri Tanpa Izin**: Agen tidak diizinkan menjalankan *browser testing*, *subagent testing*, atau *automated testing* mandiri. Seluruh verifikasi tampilan dan fungsi diserahkan sepenuhnya kepada pengguna untuk diuji secara langsung.

### H. Proteksi Self-Atasan Loop
- Pengguna tidak diperbolehkan memilih akun dirinya sendiri sebagai Atasan Langsung (`atasan_id !== userId`). Logika pengontrol wajib memvalidasi dan menganulir relasi atasan jika terjadi manipulasi input pemilihan diri sendiri.

### I. Sentralisasi Otorisasi & Kebijakan Rekap Kinerja (`PenilaianKinerjaPolicy`)
- Hak akses melihat rekap kinerja, detail kegiatan staf, pemberian nilai draf/terbit, reset penilaian, pembukaan kunci revisi, dan pembatalan persetujuan target bulanan wajib dikontrol secara sentral melalui `App\Libraries\PenilaianKinerjaPolicy`.
- **Privasi Draf Penilai**: Nilai capaian yang masih berstatus draf hanya boleh ditampilkan kepada pihak penilai yang sah. Seluruh respons antarmuka dan API untuk staf hanya mengekspos nilai yang telah resmi berstatus `terbit`.
- **Penyelarasan Nilai saat Revisi**: Pembukaan izin revisi kegiatan atau pembatalan persetujuan target staf otomatis mereset status penilaian capaian menjadi `NULL` (Belum Dinilai) dalam transaksi atomik, mencegah nilai usang tetap berlaku tanpa proses penilaian ulang yang sah.

### J. Standarisasi Perhitungan Kinerja & Parameter Predikat (`kinerja_helper.php`)
- Seluruh logika kalkulasi rata-rata capaian kinerja pegawai dan konversi predikat (Sangat Baik, Baik, Butuh Perbaikan, Kurang, Sangat Kurang) wajib dipusatkan pada `app/Helpers/kinerja_helper.php` (`hitung_capaian_kinerja()` dan `hitung_predikat_kinerja()`).
- Ambang batas predikat resmi dioperkan langsung dari backend ke antarmuka klien (JavaScript) sebagai satu sumber kebenaran (*single source of truth*), mencegah duplikasi atau desinkronisasi logika kalkulasi di peramban.
- Formulir mandiri Direktur (`#formPenilaianMandiri`) dan formulir staf (`#formPenilaian`) wajib diisolasi secara ketat dalam penamaan selector, validasi rentang 0–150%, dan reset nilai.

### K. Modul Riwayat Penugasan, Rotasi Jabatan & Hierarki Multi-Periode
- Penugasan staf dan atasan bersifat temporal/multi-periode berbasis tabel `riwayat_atasan` (total 25 tabel sistem). Evaluasi kinerja dan cetak dokumen SKP mengacu pada relasi penugasan yang aktif pada bulan/tahun terkait via `User::getPenugasanPeriode()` dan `User::getStaf()`.
- **Smart Lock Profil Staf (`/profile`)**: Field atasan langsung dan unit kerja pada menu profil dikunci (*disabled*) bagi staf biasa. Perubahan penugasan dikelola secara terpusat oleh Admin atau Kepegawaian untuk menjamin konsistensi rekam jejak SKP institusi.
- **Penyelarasan Cetak PDF Tahunan**: Lembar cetak PDF laporan bulanan dan rekapitulasi tahunan (`rekap_kinerja_pdf.php`, `DashboardKepegawaian.php`, `MonitoringTargetController.php`) wajib menyelaraskan pejabat penilai dan atasan penilai sesuai riwayat jabatan periode bersangkutan (PermenPAN-RB No. 6 Tahun 2022).

### L. Audit Trail Digital Persetujuan & Penilaian (Mitigasi Temuan Itjen / BPK)
- Kolom audit persetujuan dan penilaian (`approved_by`, `approved_at`, `penilai_id`, `penilaian_at`) pada `target_kinerja_bulanan` dan `log_tugas_tambahan` wajib tercatat lengkap di basis data.
- **Stempel Waktu Database-Only**: Tanggal dan jam persetujuan serta penilaian (`approved_at`, `penilaian_at`) **hanya disimpan di basis data** sebagai jejak audit digital internal.
- **Larangan Menampilkan Tanggal & Jam di UI**: Antarmuka pengguna (UI/UX) pada menu target dan rekapitulasi penilaian kinerja **hanya menampilkan nama pejabat yang menyetujui / menilai** tanpa mengekspos tanggal dan jam, guna memitigasi risiko temuan diskrepansi waktu administrasi pada audit pengawasan Itjen / BPK.

---

## 5. UI/UX, 8-Point Grid & Interaction Design Standards
- **Baseline UI/UX ECC 1.5 (Wajib Dipertahankan)**:
  - Tampilan ECC 1.5 yang berjalan saat ini adalah acuan tunggal UI/UX: 8-point grid, token warna, bento card, shell `.sidebar` / `.header-promax` / `.footer-promax`, serta komponen Select2, Flatpickr, dan SweetAlert2.
  - Mock-up di `ECC 2.0/Mock-up tampilan website/` hanya panduan susunan informasi (data apa, urutan, pengelompokan). **Dilarang mengimplementasikan mock-up secara mentah** (ukuran, warna, ikon, header branding, atau jarak yang tidak sesuai token ECC 1.5).
  - Jika mock-up bertentangan dengan `design.md`, maka `design.md` yang berlaku. Prosedur penerjemahan mock-up ada di `design.md` §21.0.
- **8-Point Grid System (Strict Spacing, Sizing & Asset Scale)**:
  - All layout spacing (`padding`, `margin`, `gap`), component dimensions, and asset containers **MUST** strictly adhere to the **8-Point Grid Scale** (or `4px` half-step for micro badges/tags):
    - `4px` (0.5x), `8px` (1x base), `12px` (1.5x), `16px` (2x), `24px` (3x), `32px` (4x), `40px` (5x), `48px` (6x), `56px` (7x), `64px` (8x), `80px` (10x).
  - Never use arbitrary unaligned pixel values (e.g. `7px`, `13px`, `62px`, `95px`).
  - **Asset & Icon Container Sizing**:
    - Micro Swatches & Indicators: `16px × 16px` (`border-radius: 4px`).
    - Compact Table Action Buttons / Bukti: `height: 32px; padding: 4px 12px; border-radius: 50rem;`.
    - Compact Filter Buttons: `min-height: 32px; padding: 4px 12px; border-radius: 6px;`.
    - Form Controls & Select2 Dropdowns: `height: 36px`–`40px; border-radius: 8px;`.
    - Password Toggle Action Buttons: `min-height: 36px; min-width: 44px;`.
    - Standard Avatars & Modal/Card Header Icons: `40px × 40px` (`border-radius: 12px` / `rounded-3`).
    - Prominent Feature Icons & Badges: `48px × 48px` (`border-radius: 16px`).
    - Profile Photos & Large Avatars: `64px × 64px` or `80px × 80px`.
    - Standard Status Badges & Pills: `padding: 4px 12px;` (`px-3 py-1`).
  - **Calendar Heatmap Matrix & Datepicker**:
    - Desktop Day Cells: `min-height: 64px` ($8 \times 8\text{px}$), `padding: 8px 10px`, `border-radius: 8px`, grid `gap: 8px`.
    - Mobile Day Cells: `min-height: 48px` ($6 \times 8\text{px}$), `padding: 4px 6px`, `border-radius: 6px`, grid `gap: 4px`.
    - Swatch Legend Bar: Bento Capsule Strip (`height: 32px; padding: 4px 12px; border-radius: 50rem; gap: 10px;`).
    - Interactive Hint Callout: `height: 32px; padding: 4px 14px; border-radius: 50rem; font-size: 0.75rem; font-weight: 600;`.
    - KPI Summary Boxes: `padding: 8px 16px; border-radius: 8px; min-width: 104px;`.
    - Flatpickr Datepicker: Tanggal merah/akhir pekan masa depan (`.flatpickr-disabled`) berpenampilan redup pudar (`#fca5a5`, opacity 0.35, font normal, cursor not-allowed), sedangkan yang sudah tiba/aktif berwarna merah terang tegas (`#ef4444`, font-weight 700, opacity 1).
  - **Modal Pop-Up Rincian Pekerjaan (`#modalDetailLogTanggal`)**:
    - Header icon `40px × 40px`, date navigation `<` `>` `32px × 32px; border-radius: 8px;`.
    - Info banner tanggal bersih tanpa label redundan *"Tanggal Terpilih"* dan *"Hari Reguler"* (hanya tampilkan badge cerdas kontekstual untuk Hari Libur Nasional & Akhir Pekan).
    - Bento Table: `padding: 12px 16px; max-height: 440px;` ($55 \times 8\text{px}$).
    - Footer action buttons: Pill buttons (`height: 36px`).
- **ECC Design Tokens**:
  - Bento card elevation: `.card-bento-ecc` / `.card.border-0.shadow-sm.rounded-4` with `padding: 24px` (desktop) / `16px` (mobile).
  - Bento Table Paddings: `thead th` and `tbody td` `padding: 12px 16px;` with `max-height: 480px` ($60 \times 8\text{px}$) for scrollable tables.
  - Tabular Numerics: Always use `font-variant-numeric: tabular-nums; font-feature-settings: "tnum";` for numbers, dates, and currency.
  - Accessible focus rings: `box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15)`.
  - Tactile feedback: `.btn-tactile` with active scale response `transform: scale(0.97)`.
  - Motion: Natural deceleration `cubic-bezier(0.16, 1, 0.3, 1)` with full `@media (prefers-reduced-motion: reduce)` support.
- **Clean UI & Pembatasan Ikon (Minimalist Icon Policy)**:
  - **Dilarang menggunakan banyak ikon pada tampilan antarmuka**: Terapkan prinsip kesederhanaan visual (*visual simplicity*). Ikon **hanya digunakan jika memang sangat dibutuhkan** untuk memperjelas aksi fungsional atau navigasi penting (misal: menu sidebar, tombol aksi edit/hapus/tambah, indikator status centang disetujui/silang ditolak/pensil draf, dan avatar).
  - Hindari menaruh ikon dekoratif pada setiap judul, kolom tabel, kartu, label form, atau teks penjelasan jika teks sudah cukup jelas dengan sendirinya (*anti-visual clutter*), agar tampilan aplikasi selalu berpenampilan bersih (*clean*), rapi, elegan, dan profesional.
- **Animasi Sederhana & Selektif (Simple & Purposeful Motion Policy)**:
  - **Boleh menambahkan atau mengganti animasi sederhana saja pada halaman hanya jika dibutuhkan**: Periksa terlebih dahulu animasi yang sudah ada di halaman bersangkutan dan sesuaikan secara hemat sesuai kebutuhan.
  - Hindari animasi berlebihan, lambat, atau mengganggu fokus kerja pengguna. Pastikan transisi halus, performa tetap ringan, dan selalu menghormati `@media (prefers-reduced-motion: reduce)`.
- **SweetAlert2 & JS Fallback**:
  - Always verify `typeof Swal !== 'undefined'` before invoking `Swal.fire()` and provide native browser dialog fallback (`confirm()`) so the UI functions seamlessly even if CDN assets fail to load.

---

## 6. Gaya Bahasa, Notifikasi & Mikro-Kopi (Tone, Simplicity & Microcopy)
- **Aturan Baku Teks Antarmuka (Non-Teknis, Singkat, Padat & Jelas)**:
  - **Kalimat untuk notifikasi, penjelasan, pop up modal, dan teks lainnya di aplikasi jangan menggunakan bahasa yang teknis. Gunakan kalimat/kata yang singkat, padat, dan jelas saja.**
  - Seluruh teks pada antarmuka pengguna (UI label, tombol, tooltip, alert banner, hint form, modal pop-up, SweetAlert2, notifikasi sistem, pesan error) dan berkas dokumentasi (`AGENTS.md`, `design.md`, `audit_code.md`) **WAJIB** mematuhi prinsip ini tanpa pengecualian.
- **Larangan Istilah Teknis pada Teks Pengguna**:
  - Jangan pernah menampilkan istilah teknis sistem/server/database kepada pengguna, seperti: *"database"*, *"gangguan basis data"*, *"server/jaringan"*, *"SQL"*, *"query"*, *"+ toleransi X hari"*, *"exception"*, *"permanen"*, *"full table scan"*, atau kode error teknis.
  - Gunakan kalimat yang berorientasi pada aksi solutif pengguna. Contoh:
    - ❌ *"Gagal mengirim laporan harian karena gangguan basis data."*
    - ✅ *"Gagal mengirim laporan. Silakan coba lagi."*
    - ❌ *"Pengisian laporan kegiatan untuk periode Agustus 2026 telah ditutup sejak tanggal 05 September 2026 (Batas akhir bulan + toleransi 5 hari)."*
    - ✅ *"Pengisian laporan periode Agustus 2026 telah ditutup sejak 05 September 2026."*
    - ❌ *"Silakan isi minimal satu kegiatan pada Tugas Pokok atau Tugas Tambahan sebelum mengirim ke atasan langsung."*
    - ✅ *"Isi minimal 1 kegiatan pokok atau tugas tambahan."*
- **Standar Dialog Konfirmasi & Modal Pop-Up (SweetAlert2)**:
  - **Judul Dialog**: Cukup 2–4 kata (*"Hapus Kegiatan?"*, *"Hapus Tugas Tambahan?"*, *"Kirim Laporan?"*).
  - **Teks Dialog**: 1 kalimat pendek dan tenang (*"Kegiatan ini akan dihapus."*, bukan *"Apakah Anda yakin ingin menghapus catatan kegiatan ini?"*).
  - **Tombol Aksi**: Kata kerja singkat dan tegas (*"Ya, Hapus"*, *"Kirim"*, *"Batal"*).
  - **Umpan Balik Sukses**: Judul *"Terhapus"* / *"Tersimpan"*, teks *"Kegiatan berhasil dihapus."* / *"Draf berhasil disimpan."*
- **Kepatuhan Mutlak Terminologi & Branding**:
  - Tetap konsisten 100% menggunakan istilah resmi **"staf"** (tidak boleh menggunakan "bawahan" atau "staff").
  - Tetap konsisten 100% menggunakan identitas resmi **"Evidence Command Center (ECC)"** (tidak boleh menggunakan "Simonik").
  - **Pembakuan Istilah Bahasa Indonesia Resmi**:
    - Selalu gunakan **"Kata Sandi"** (dilarang menggunakan "Password").
    - Selalu gunakan **"Lupa Kata Sandi?"** (bukan "Forgot Password").
    - Selalu gunakan **"Alamat Email"** (dilarang menggunakan "Email Address").
    - Selalu gunakan **"Keluar"** pada navigasi/dropdown profil (dilarang menggunakan "Logout").
    - Selalu gunakan **"Simpan Profil"** pada formulir profil (bukan "Simpan Perubahan").

---

## 7. Prinsip Rekayasa Perangkat Lunak & Kualitas Kode (Clean Code Standards)
Berlaku untuk seluruh kode **baru dan kode yang diubah** (ECC 1.x dan ECC 2.0). Kode lama yang tidak disentuh tidak perlu di-refactor massal; rapikan hanya area yang sedang dikerjakan.

### A. Prinsip SOLID
- **Single Responsibility (SRP)**: Satu kelas atau fungsi hanya punya satu alasan untuk diubah.
  - Controller hanya mengatur alur: terima request → validasi → panggil service/model → kirim response.
  - Logika bisnis (kalkulasi, aturan transisi status, agregasi dashboard) ditempatkan di `app/Libraries/` (Service/Policy) atau `app/Helpers/`. Contoh yang sudah ada: `PenilaianKinerjaPolicy`, `kinerja_helper.php`.
  - Model hanya untuk akses data. View tidak boleh menjalankan query basis data.
- **Open/Closed (OCP)**: Tambah perilaku lewat kelas, konfigurasi, atau data referensi baru, bukan dengan menambah cabang `if/elseif` pada kode yang sudah stabil. Daftar status, jenis beasiswa, dan sejenisnya diambil dari tabel `ref_*`, bukan di-hardcode.
- **Liskov Substitution (LSP)**: Subkelas `BaseController` / `Model` tidak boleh mengubah kontrak induk (signature, tipe return, perilaku callback).
- **Interface Segregation (ISP)**: Buat interface kecil per kemampuan (mis. `ExportableInterface`), bukan satu interface besar yang memaksa kelas mengimplementasikan metode yang tidak dipakai.
- **Dependency Inversion (DIP)**: Service menerima dependensi lewat constructor (dengan default dari `model()` / `service()`), bukan `new` yang tersebar di dalam method. Service bersama didaftarkan di `app/Config/Services.php`.
- **Pragmatis**: Buat interface/abstraksi hanya jika ada minimal dua implementasi nyata atau dibutuhkan untuk pengujian (selaras dengan KISS & YAGNI).

### B. Efisiensi & Kesederhanaan Kode
- **KISS**: Pilih solusi paling sederhana yang benar. Gunakan fitur bawaan CodeIgniter 4 (Query Builder, Validation, Model callbacks) sebelum menulis abstraksi sendiri.
- **YAGNI**: Jangan menambah kolom, endpoint, parameter, atau opsi konfigurasi "untuk jaga-jaga". Modul ECC 2.0 dikerjakan satu per satu sesuai PRD dan rencana implementasi yang disetujui.
- **DRY**: Logika yang berulang wajib berada di satu tempat:
  - Predikat, label/warna status, format tanggal Indonesia, sanitasi desimal, dan pengecekan role → helper/library/Policy.
  - Label dan kelompok status taruna diambil dari `ref_status_taruna`, tidak diketik ulang di view atau JavaScript.
  - Nilai ambang batas dikirim dari backend ke JavaScript (*single source of truth*).
  - Potongan view yang berulang → partial `$this->include()` atau View Cells.
- **Query Efisien**: Hindari N+1 query. Gunakan join/agregasi dan kolom selektif (lihat §3).

### C. Keterbacaan & Struktur (Readability)
- **Penamaan Deskriptif**:
  - Kelas `PascalCase` (`TarunaController`, `StatusTarunaService`).
  - Method dan variabel `camelCase` (`$totalPrestasi`, `hitungStatusPerTanggal()`).
  - Tabel dan kolom basis data `snake_case` (`nomor_taruna`, `status_validasi`).
  - Konstanta `UPPER_SNAKE_CASE`.
  - Boolean diawali `is` / `has` / `can` (`$isAktif`, `$canSeeAll`).
  - Gunakan istilah domain yang konsisten (taruna, staf, prestasi). Dilarang nama kabur seperti `$x`, `$tp`, `$d`, `$data2`.
- **Komentar yang Tepat**: Jelaskan **mengapa** (aturan bisnis, rujukan PRD/regulasi, trik khusus), bukan **apa** yang dilakukan kode. Tulis docblock untuk method publik service/library (`@param`, `@return`, `@throws`). Jangan menghapus komentar lama yang tidak terkait perubahan.
- **Konsistensi Format**:
  - PHP mengikuti **PSR-12** dan autoload **PSR-4** (indentasi 4 spasi, satu kelas per file, namespace sesuai folder), serta deklarasi tipe parameter dan return (PHP 8.1+).
  - JavaScript: gunakan `const` / `let` (tanpa `var`), `camelCase`.
  - CSS: nama kelas `kebab-case`, nilai mengikuti token ECC dan 8-point grid.
- **Ukuran Wajar**: Method fokus dan pendek. View yang terlalu panjang dipecah menjadi partial.

### D. Keamanan & Ketahanan (Robustness)
- **Error Handling yang Baik**:
  - Operasi mutasi dibungkus `try...catch (\Throwable $e)` beserta transaksi (§3).
  - Detail teknis dicatat dengan `log_message('error', ...)`; pengguna hanya menerima pesan singkat non-teknis (§6).
  - Dilarang menampilkan stack trace atau pesan exception mentah. Produksi wajib `CI_ENVIRONMENT = production`.
  - Respons AJAX konsisten: `{ status, message, csrf_hash }` dengan kode HTTP yang sesuai.
  - Efek samping non-kritis (notifikasi, log tambahan) bersifat *fail-safe* dan tidak boleh menggagalkan proses utama.
- **Validasi Input**:
  - Semua input dari luar (form, query string, AJAX, impor Excel, API) divalidasi di server dengan CodeIgniter Validation, meskipun sudah divalidasi di klien. Gunakan aturan *whitelist* (`in_list`, `is_natural_no_zero`, `valid_date`, `max_length`).
  - Gunakan Query Builder / parameter binding. **Dilarang** menyusun SQL dengan menyambung input pengguna.
  - Escape output dengan `esc()`, cek otorisasi dan kepemilikan data (IDOR) di server (§3).
- **Mudah Diuji (Testability)**:
  - Logika bisnis ditulis di service/helper yang tidak bergantung langsung pada `$this->request` atau session, sehingga bisa diuji dengan PHPUnit (`tests/`).
  - Hindari state statis dan dependensi tersembunyi. Nilai "hari ini" atau tanggal acuan diterima sebagai parameter, bukan di-hardcode di dalam fungsi.
  - Kode wajib *mudah diuji*, tetapi **menjalankan** pengujian otomatis tetap tunduk pada §4.G (perlu izin pengguna).

### E. Checklist Sebelum Menyelesaikan Perubahan
- [ ] Tidak ada logika bisnis atau query di view; controller tetap ringkas.
- [ ] Tidak ada duplikasi logika yang sudah tersedia di helper/library/Policy.
- [ ] Tidak ada fitur atau parameter di luar kebutuhan yang disetujui.
- [ ] Penamaan jelas, format PSR-12, tipe parameter dan return dideklarasikan.
- [ ] Input divalidasi di server, output di-escape, otorisasi dicek.
- [ ] Error ditangani dengan pesan ramah pengguna dan dicatat di log.
- [ ] UI mengikuti baseline ECC 1.5 dan `design.md`, bukan salinan mentah mock-up.
- [ ] `php -l` lulus untuk file PHP yang diubah, lalu jalankan `graphify update .`.
