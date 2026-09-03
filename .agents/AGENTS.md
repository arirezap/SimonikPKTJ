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
  - Use selective column queries (e.g. `select('id, nama_lengkap, nip, unit, jabatan, role, atasan_id, foto')`) on large User queries to optimize PHP memory usage.

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

---

## 5. UI/UX, 8-Point Grid & Interaction Design Standards
- **8-Point Grid System (Strict Spacing, Sizing & Asset Scale)**:
  - All layout spacing (`padding`, `margin`, `gap`), component dimensions, and asset containers **MUST** strictly adhere to the **8-Point Grid Scale** (or `4px` half-step for micro badges/tags):
    - `4px` (0.5x), `8px` (1x base), `12px` (1.5x), `16px` (2x), `24px` (3x), `32px` (4x), `40px` (5x), `48px` (6x), `56px` (7x), `64px` (8x), `80px` (10x).
  - Never use arbitrary unaligned pixel values (e.g. `7px`, `13px`, `62px`, `95px`).
  - **Asset & Icon Container Sizing**:
    - Micro Swatches & Indicators: `16px × 16px` (`border-radius: 4px`).
    - Compact Table Action Buttons / Bukti: `height: 32px; padding: 4px 12px; border-radius: 50rem;`.
    - Form Controls & Select2 Dropdowns: `height: 36px`–`40px; border-radius: 8px;`.
    - Standard Avatars & Modal Header Icons: `40px × 40px` (`border-radius: 12px`).
    - Prominent Feature Icons & Badges: `48px × 48px` (`border-radius: 16px`).
    - Profile Photos & Large Avatars: `64px × 64px` or `80px × 80px`.
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
- **SweetAlert2 & JS Fallback**:
  - Always verify `typeof Swal !== 'undefined'` before invoking `Swal.fire()` and provide native browser dialog fallback (`confirm()`) so the UI functions seamlessly even if CDN assets fail to load.

---

## 6. Gaya Bahasa & Komunikasi (Tone & Simplicity)
- **Kalimat Sederhana & Ringkas**: Dalam berkas dokumentasi (`AGENTS.md`, `design.md`, `audit_code.md`), label UI antarmuka, dan komunikasi respons ke pengguna:
  - Gunakan kalimat yang singkat, padat, jelas, dan langsung pada intinya.
  - Hindari kalimat yang terlalu teknis, bertele-tele, atau kepanjangan.
  - Prioritaskan bahasa yang ramah, mudah dipahami pengguna, dan tidak berbelit-belit.

