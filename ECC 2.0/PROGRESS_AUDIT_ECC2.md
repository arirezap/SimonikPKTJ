# Konteks & Progres Audit Mendalam ECC 2.0 (Ketarunaan & Alumni)

- **Terakhir Diperbarui:** 19 November 2026
- **Lingkungan:** Laragon (`http://simonikpktj.test/`) • CodeIgniter 4 (PHP 8.1+) • Bootstrap 5.3 • Select2 • Flatpickr (`locale: 'id'`) • SweetAlert2
- **Versi Skill Impeccable:** `v4.10.0` (engine `v0.1.27`, lokasi `.agents/skills/impeccable/scripts/impeccable.cmd`)
- **Status Graphify:** Mutakhir per 19 Nov 2026 (`7.837` node, `15.392` edge, `570` komunitas)

---

## 1. Keputusan Kebijakan Pengguna yang WAJIB Dipatuhi di Chat Baru

1. **OPSI A — Seluruh Dokumen Bukti Menggunakan Tautan Google Drive / Cloud (`http://` / `https://`):**
   - Sudah diputuskan secara eksplisit oleh pengguna untuk **Rekam Jejak**, **Prestasi**, **Pelanggaran**, dan **Beasiswa** (tanyakan singkat hanya jika ada modul baru yang sifat dokumennya berbeda, atau sarankan Opsi A sebagai standar konsisten).
   - Nol beban penyimpanan server, tanpa migrasi database (kolom `VARCHAR(255)` / `TEXT` / `LONGTEXT` menampung URL langsung), tetap **100% *backward-compatible*** untuk membaca/menghapus berkas fisik lama + `.htaccess` anti-eksekusi PHP di folder upload lama.
   - **Dilarang keras** mengembalikan PDF simulasi palsu (`ketarunaan_generate_simple_pdf`) pada endpoint `unduhDokumen()` ketika berkas tidak ada di disk (PDF ringkasan khusus hanya di endpoint `unduhRingkasan()`).
2. **Prosedur Kerja Wajib (`AGENTS.md §4.G`):**
   - Setiap selesai audit (`/impeccable audit`), **WAJIB menyusun Implementation Plan** terlebih dahulu dan menunggu persetujuan pengguna (*Proceed*) sebelum mengedit kode.
   - **Dilarang menjalankan browser testing / subagent testing otomatis.** Verifikasi visual/interaksi di browser selalu diserahkan langsung kepada pengguna; agen melakukan verifikasi statis (`php -l`, `impeccable detect --json`, `Select-String`, `graphify update .`).
3. **Kepatuhan Aturan Inti (`AGENTS.md` & `design.md`):**
   - Selalu gunakan istilah **"staf"** (bukan bawahan/staff) dan **"Evidence Command Center (ECC)"** / **"ECC"** (bukan Simonik).
   - **`AGENTS.md §4.L` (Audit Itjen/BPK):** Stempel waktu validasi/persetujuan disimpan di database saja; **jangan tampilkan tanggal/jam validasi di Tab Riwayat Validasi UI** (cukup nama pejabat, aktivitas, status, dan catatan).
   - **`log_audit()` Signature Resmi (`app/Helpers/audit_helper.php`):** `log_audit($action, $entity, $entityId, $oldValues, $newValues)` — jangan masukkan narasi panjang ke parameter ke-2 (`$entity`).
   - **Audit Lintas Peran:** Saat mengaudit modul Ketarunaan, selalu cek perilaku untuk **Admin/Staf Pengelola**, **Validator**, **Taruna Mandiri (`isSelfTarunaOnly`)**, dan **Alumni (`isSelfAlumniOnly` / `isSelfAccountOnly`)**.
   - **Standar UI/UX ECC 1.5:** Shared `_styles.php` per modul (hindari duplikasi CSS), `<h1 class="h3">`, tab/wizard garis bawah `#1e40af` pada `#eff6ff` dengan `<nav>` + `aria-current`, 1 CTA primer (`btn-primary`), `Terapkan` sekunder (`btn-outline-primary`), `Reset` kondisional, kontras WCAG AA (jangan pakai `btn-outline-warning` `1.63:1`, ganti `btn-outline-danger`/`btn-danger`), sorting server/client (`aria-sort`), kartu mobile (`d-md-none`) + target sentuh `44px` + input mobile `1rem !important`, `rel="noopener noreferrer"` pada semua `target="_blank"`, anti double-submit + `pageshow`, dan nonaktifkan efek melompat `.bento-card:hover` pada kontainer tabel/form.

---

## 2. Matriks Progres Audit & Perbaikan Modul ECC 2.0

| # | Modul & URL | Status | Skor Awal → Akhir | Ringkasan Perubahan Utama |
|---:|---|---|---:|---|
| 1 | **Dashboard Ringkasan** (`/ketarunaan/dashboard`) | **SELESAI** | Audit & Polish tuntas | Penyelarasan branding ECC, kartu metrik, tata letak responsif & interaksi. |
| 2 | **Database Taruna** (`/ketarunaan/taruna`) | **SELESAI** | Deep Audit Tahap 1–3 tuntas | Angkatan romawi (`ketarunaan_format_angkatan`), impor Excel, profil responsif, `php -l` & `impeccable detect` bersih. |
| 3 | **Rekam Jejak Taruna** (`/ketarunaan/rekam-jejak`) | **SELESAI** | `12 / 20` → **`19 / 20`** | Opsi A tautan Google Drive, 0 PDF simulasi, CSV injection (`ketarunaan_csv_safe`), WCAG AA, sorting, kartu mobile, validasi & modal hardened. |
| 4 | **Prestasi Taruna** (`/ketarunaan/prestasi`) | **SELESAI** *(Tahap 1–3 + Revisi v2 Admin/Taruna/Alumni)* | `7 / 20` → `19 / 20` → **v2 Tuntas** (`0` error `detect`) | Bug P0 tombol Edit selesai; Opsi A Google Drive (bukti utama + 5 dokumentasi); `.htaccess`; tutup kebocoran daftar Alumni (`isSelfAccountOnly`); privasi bukti non-peserta di `detail.php`; koreksi peserta/dokumen `tervalidasi` reset ke `draf` + `log_audit`; fitur Taruna (**Prestasi Saya** toggle/badge, catatan validator di Tahap 1–2, default peran `Anggota`, redirect cepat `Individu`); filter 2 baris rapi; global `.content-wrapper` `min-width: 0` di `style.css`. |
| 5 | **Pelanggaran Taruna** (`/ketarunaan/pelanggaran`) | **SELESAI** *(Tahap 1–3 — belum re-audit opsional)* | `8 / 20` → **Tuntas** (`0` error `detect`) | Opsi A Google Drive (BAP, Dokumen Bukti maks. 10, Lampiran Tindak Lanjut); `.htaccess`; 12 `log_audit()` diperbaiki; 0 PDF simulasi + `nosniff`; `validasi()` nama validator asli + `in_list`; `$canDecide` ditegakkan di `savePenetapan()`; hapus `diajukan` diblokir; sembunyikan tanggal validasi (`AGENTS.md §4.L`); shared `_styles.php`; 4 metrik filter cepat; 7 kolom sort server + 4 tabel sort client + 6 kartu mobile; Select2 & Flatpickr `id`; modal `#modalTindakLanjut` & `#modalKembalikanKasus` hardened. |
| 6 | **Beasiswa** (`/ketarunaan/beasiswa`) | **SELESAI** *(Tahap 1–3 Opsi A + Re-Audit & Polish)* | `7 / 20` → `15 / 20` → **`19 / 20` Tuntas** (`0` error `detect`) | Opsi A tautan Google Drive (Sertifikat, Dokumentasi, Domisili 3TP, Bukti Ekonomi Tidak Mampu, & SK) + *backward-compatible* berkas lama + `nosniff`; hapus PDF simulasi palsu; perbaiki `basename()` URL Prestasi; guard *state machine* edit/verifikasi/penetapan + kuota program; validasi ketat tanggal ISO `isValidDateYmd()`; default `index()` ke `Semua Periode` (`all`) + kompresi session `previewIpCsv()`; `resolveSelfNomorTaruna()`; 10 `log_audit()` diperbaiki; sembunyikan tanggal riwayat (`AGENTS.md §4.L`); pisahkan izin SK dari `canViewEconomy` + akses `kabag`; perbaiki angka kuota Ringkasan bagi Taruna; shared `_styles.php` + `_scripts_common.php`; `<h1 class="h3">`; tab `#1e40af`/`#eff6ff` + `aria-current`; grup CTA header `flex-shrink-0` + `text-nowrap`; hapus `border-top: 3px` pada kartu membulat + kesejajaran angka `fs-4` & kepadatan `mb-3`; kontras WCAG AA (`-emphasis` >= 7:1 pada seluruh badge subtle); 46 advisory `0.8125rem` dihapus; sorting server/client (`aria-sort`), paginasi, filter lengkap + `Reset`, kartu metrik interaktif, kartu mobile (`d-md-none`), Select2 Taruna & 3TP, Flatpickr `id`, templat CSV + strip BOM, dan anti double-submit + `pageshow`. |
| 7 | **Lowongan Pekerjaan** (`/ketarunaan/lowongan`) | **SELESAI** *(Tahap 1–4 Opsi A)* | `7 / 20` → **`19 / 20` Tuntas** (`0` error `detect`) | Perbaiki *Fatal Error P0* `ketarunaan_status_validasi_badge()`; perbaiki tabrakan bar filter desktop (`Terapkan` menimpa `Validasi`) menjadi grid 2 baris proporsional + `Reset` kondisional; kepatuhan `AGENTS.md §4.L` (hapus kolom tanggal `Riwayat Proses` di `riwayat.php` & input `tanggal_pemeriksaan_kontak` di `perusahaan.php`); Opsi A tautan Google Drive / Portal Resmi di `dokumen.php` + *backward-compatible* berkas lama + `.htaccess` + `nosniff`; validasi tanggal ISO `isValidDateYmd()` pada `normalizeDeadline()`; redirect ramah ECC pada halaman terlarang + akses read-only `canViewStaffBoard()`; shared `_styles.php` + `_scripts_common.php`; `<h1 class="h3">` + breadcrumb; tab `#1e40af`/`#eff6ff` + `aria-current`; hapus `border-top: 3px` pada kartu membulat + 4 kartu KPI filter cepat interaktif; kontras WCAG AA (`btn-outline-danger` & `-emphasis` >= 7:1); 75 advisory `impeccable detect` dihapus (`0` temuan); sorting server/client (`aria-sort`), paginasi, kartu mobile (`d-md-none`), Select2, Flatpickr `id`, validasi dinamis kanal lamaran & alasan penutupan `Lainnya`, serta anti double-submit + `pageshow`. |
| 8 | **Alumni** (`/ketarunaan/alumni`) | **SELESAI** *(Tahap 1–4 + Batch Kelulusan)* | `10 / 20` → **`19 / 20` Tuntas** (`0` error `detect`) | Kepatuhan `AGENTS.md §4.L` (hapus kolom tanggal validasi `Riwayat Keputusan Koreksi` di `koreksi.php` & tanggal banner di `detail.php`); fitur **Batch Atur Kelulusan** (pengajuan massal 1 SK untuk banyak taruna/angkatan dengan filter Prodi/Angkatan/Tingkat + validasi massal antrean); kontras WCAG AA (`btn-outline-danger` menggantikan `btn-outline-warning` `1.63:1` di `detail.php`, `-emphasis` >= 7:1 pada seluruh badge & avatar); validasi tanggal kalender ISO ketat `isValidDateYmd()` di `AlumniService`; redirect ramah ECC pada `detail()` terlarang; shared `_styles.php` + `_scripts_common.php`; `<h1 class="h3">` + breadcrumb; tab `#1e40af`/`#eff6ff` + `aria-current`; hapus `border-top: 4px` pada 4 kartu KPI & jadikan filter cepat interaktif; bar filter `Terapkan` (`btn-outline-primary`) + `Reset` kondisional; hapus kotak catatan fase pengembangan; 65 temuan `impeccable detect` dihapus (`0` temuan); sorting server/client (`aria-sort`), paginasi 15 baris/halaman, kartu mobile (`d-md-none`), Select2, Flatpickr `id`, sinkronisasi `required` dinamis `ralat_sk` vs `batal_lulus`, SweetAlert2, serta anti double-submit + `pageshow`. |
| 9 | **Validasi Data** (`/ketarunaan/validasi`) | **SELESAI** *(Tahap 1–3)* | `12 / 20` → **`19 / 20` Tuntas** (`0` error `detect`) | Hapus `border-top: 4px` & warna `#0f766e` pada 4 kartu KPI dan jadikan filter cepat interaktif; breadcrumb `Ketarunaan / Validasi Data`; tab garis bawah `#1e40af`/`#eff6ff` + `aria-current`; validasi tanggal kalender ISO ketat (`DateTimeImmutable`) pada `tanggal_acuan`; redirect ramah ECC pada akses GET terlarang; ekspos dropdown filter `Modul Data` (`entitas`) pada tab `Jejak Aktivitas`; tombol `Terapkan` (`btn-outline-primary`) + `Reset` kondisional; kontras `text-danger-emphasis` ($\ge 7:1$); sorting client-side (`aria-sort`) & kartu mobile (`d-md-none`) di seluruh 3 tabel; Flatpickr `locale: 'id'`; serta anti double-submit + `pageshow`. |
| 10 | **Laporan** (`/ketarunaan/laporan`) | **SELESAI** *(Tahap 1–3)* | `13 / 20` → **`19 / 20`** *(Tuntas)* | Redirect otorisasi GET ke layout ECC, validasi `Y-m-d` tanggal acuan (`DateTimeImmutable`), contrast badge `-emphasis` + border `-subtle`, breadcrumb, CTA tunggal `Unduh CSV Resmi`, `gov-filter-form`, sorting tabel (`aria-sort`), mobile card view, Flatpickr `d M Y`; `0` error `detect`. |
| 11 | **Pengaturan** (`/ketarunaan/pengaturan`) | **SELESAI** *(Tahap 1–3)* | `14 / 20` → **`19 / 20`** *(Tuntas)* | Selaraskan izin CTA `/users` (`admin`, `kepegawaian`), redirect aman ECC untuk akses tidak sah, validasi strict `Y-m-d` (`DateTimeImmutable`), cek FK `ref_program_studi`/`ref_angkatan` pada Kelas, whitelist 3TP & batas catatan, kontras `-emphasis` & `.ecc-text-muted-aa`, target sentuh mobile `44px`, kartu mobile `d-md-none`, Flatpickr `d M Y` pada Tahun Akademik, breadcrumb & ARIA, sinkronisasi `old('ref_id')` & fokus form, serta saring cepat direktori pengguna (`0` error `detect`). |

---

## 3. Helper & Pola Bersama yang Sudah Tersedia (Gunakan Ulang — DRY)

Seluruh helper berikut sudah tersedia dan teruji di `app/Helpers/ketarunaan_helper.php`:
- `ketarunaan_home_url()` — URL beranda dinamis sesuai peran (Staf vs Taruna vs Alumni).
- `ketarunaan_ensure_kalender_terkini()` — Memastikan referensi tahun akademik aktif tersedia.
- `ketarunaan_format_angkatan(['tahun_masuk' => ..., 'kode_romawi' => ...])` — Format angkatan konsisten (mis. `2024 (LX)`).
- `ketarunaan_format_tanggal($date, $short)` — Format tanggal Indonesia konsisten.
- `ketarunaan_is_http_url($val, $maxLen)` — Validasi ketat tautan `http://` / `https://` untuk Opsi A Google Drive.
- `ketarunaan_format_bukti_label($val)` — Label ramah pengguna untuk URL Google Drive / nama berkas lama.
- `ketarunaan_csv_safe($val)` — Proteksi *CSV Formula Injection* untuk semua endpoint ekspor CSV.
- Badge helpers: `ketarunaan_validasi_badge()`, `ketarunaan_status_badge()`, `ketarunaan_keikutsertaan_badge()` (kontras `8.6:1`), `ketarunaan_penetapan_badge()`, `ketarunaan_penyelesaian_badge()`, `ketarunaan_penetapan_beasiswa_badge()`.

Perbaikan layout global yang sudah terpasang di `public/assets/css/style.css`:
- `.content-wrapper` memiliki `min-width: 0; max-width: 100%; overflow-x: hidden;` dan `.content-area` memiliki `min-width: 0;` (mencegah CSS Flexbox horizontal blowout di seluruh halaman ECC 2.0).

---

## 4. Berkas Referensi Audit Tersimpan

Laporan audit historis tersimpan di `.impeccable/audit/`:
- Rekam Jejak Re-Audit (`19/20`): `.impeccable/audit/2026-11-19T03-13-42Z__app-views-ketarunaan-rekam-jejak-index-php.md`
- Prestasi Re-Audit (`19/20`) & Audit v2 Admin/Taruna/Alumni:
  - `.impeccable/audit/2026-11-19T04-28-00Z__app-views-ketarunaan-prestasi-index-php.md`
  - `.impeccable/audit/2026-11-19T05-25-00Z__app-views-ketarunaan-prestasi-index-php.md`
- Pelanggaran Deep Audit (`8/20` → perbaikan Tahap 1–3 selesai): `.impeccable/audit/2026-11-19T04-35-00Z__app-views-ketarunaan-pelanggaran-index-php.md`
- Beasiswa Deep Audit (`7/20` → Re-Audit `15/20` → Polish `19/20` selesai):
  - `.impeccable/audit/2026-11-19T05-48-31Z__app-views-ketarunaan-beasiswa-index-php.md`
  - `.impeccable/audit/2026-11-19T06-10-52Z__app-views-ketarunaan-beasiswa-index-php.md`
- Lowongan Pekerjaan Deep Audit (`7/20` → perbaikan Tahap 1–4 Opsi A selesai): `.impeccable/audit/2026-11-19T06-33-00Z__app-views-ketarunaan-lowongan-index-php.md`
- Alumni Deep Audit (`10/20` → perbaikan Tahap 1–4 + Batch Kelulusan selesai): `.impeccable/audit/2026-11-19T07-08-00Z__app-views-ketarunaan-alumni-index-php.md`
- Validasi Data Deep Audit (`12/20` → perbaikan Tahap 1–3 selesai): `.impeccable/audit/2026-11-19T08-10-00Z__app-views-ketarunaan-validasi-index-php.md`
- Laporan Deep Audit (`13/20` → perbaikan Tahap 1–3 selesai): `.impeccable/audit/2026-11-19T08-35-00Z__app-views-ketarunaan-laporan-index-php.md`
- Pengaturan Deep Audit (`14/20` → perbaikan Tahap 1–3 selesai): `.impeccable/audit/2026-11-19T08-49-40Z__app-views-ketarunaan-pengaturan-index-php.md`

---

## 5. Langkah Lanjutan di Chat Baru

1. Seluruh **11 modul ECC 2.0 Ketarunaan** telah melewati `/impeccable audit` dan perbaikan Tahap 1–3/4 (`0` error `impeccable detect`).
2. Jika ingin memverifikasi skor akhir di peramban untuk modul mana pun (**Pelanggaran**, **Beasiswa**, **Lowongan Pekerjaan**, **Alumni**, **Validasi Data**, **Laporan**, atau **Pengaturan**): uji langsung di peramban lalu jalankan re-audit `/impeccable audit` jika diperlukan.
