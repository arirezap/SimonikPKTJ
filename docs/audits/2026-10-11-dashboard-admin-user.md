# Laporan Audit Kode: Fitur Dashboard (Admin & User)
**Tanggal Audit:** 11 Oktober 2026  
**Modul/Fitur:** Dashboard Admin (`/dashboard` - Admin, Pimpinan & Manajemen) & Dashboard User (`/dashboard` - Staf & Atasan Langsung)  
**Berkas yang Diaudit:**
1. Router Dispatcher: `app/Controllers/DashboardController.php`
2. Admin Dashboard Controller: `app/Controllers/Admin/Dashboard.php`
3. Admin Dashboard View: `app/Views/admin/dashboard.php`
4. User Dashboard Controller: `app/Controllers/User/Dashboard.php`
5. User Dashboard View: `app/Views/user/dashboard.php`
6. Trait Perhitungan Batch Kinerja: `app/Controllers/Traits/KinerjaBatchTrait.php`
7. Trait Mutu ECC: `app/Controllers/Traits/EccDataTrait.php`
8. Konfigurasi Rute Terkait: `app/Config/Routes.php`

---

## 1. Ringkasan Eksekutif
Audit komprehensif telah dilaksanakan terhadap seluruh komponen backend dan frontend modul **Dashboard**, baik untuk antarmuka Admin (mencakup Direktur, Wadir, Kabag, Kepegawaian, SPM, Manajemen) maupun antarmuka User (mencakup Staf dan Atasan Langsung). Audit dilakukan berdasarkan 8 pilar evaluasi arsitektur perangkat lunak, keamanan, efisiensi data, kebersihan kode, dan kepatuhan aturan kerja Evidence Command Center (ECC).

Hasil audit menemukan **1 temuan bug fungsional & keamanan (DSH-01)** yang menyebabkan modal rincian grafik sebaran dan tren selalu ditolak (*403 Forbidden*), **2 temuan efisiensi data dan performa tinggi (DSH-02, DSH-03)** akibat pemrosesan residu data legacy dan kueri non-selektif tanpa filter status pegawai aktif, **1 temuan arsitektur redundansi kode mati (DSH-04)**, **1 temuan inkonsistensi predikat kinerja (DSH-05)**, **1 temuan optimasi loop (DSH-06)**, serta **1 temuan mikro-kopi (DSH-07)**.

---

## 2. Tabel Ringkasan Temuan Audit

| ID Temuan | Pilar Evaluasi | Tingkat Keparahan | Status | Deskripsi Singkat |
| :--- | :--- | :--- | :--- | :--- |
| **DSH-01** | Keamanan & Interaktivitas | **Tinggi (High)** | Open | Modal drilldown grafik analitik donat dan tren selalu gagal (*403 Forbidden*) karena request fetch tidak menyertakan header `X-Requested-With`. |
| **DSH-02** | Performa & Efisiensi Data | **Tinggi (High)** | Open | Eksekusi kueri legacy `rencana_kinerja` dan loop parsing JSON realisasi tahunan di Admin Dashboard yang tidak digunakan sama sekali di view. |
| **DSH-03** | Integritas Data & Keamanan | **Tinggi (High)** | Open | Kueri pengguna `findAll()` menarik semua kolom sensitif (password hash) dan tidak memfilter `status_pegawai = 'aktif'`, mencemari metrik organisasi. |
| **DSH-04** | Arsitektur & Clean Code | **Sedang (Medium)** | Open | Dead code blok "Kinerja Seluruh Pegawai (Per Unit)" pada User Dashboard Controller dan View yang tidak pernah diakses oleh staf biasa. |
| **DSH-05** | Integritas Data & UI/UX | **Sedang (Medium)** | Open | Inkonsistensi penentuan kelas badge predikat kinerja di JS view dan residu array key `'cukup'` pada controller. |
| **DSH-06** | Efisiensi & Kueri | **Rendah (Low)** | Open | Duplikasi perulangan pegawai (`$allUsersForUnit`) di Admin Dashboard untuk menghitung agregasi unit kerja. |
| **DSH-07** | Mikro-Kopi & Gaya Bahasa | **Rendah (Low)** | Open | Pesan error pada dialog AJAX masih bernuansa teknis jaringan ("Gagal menghubungi server"). |

---

## 3. Rincian Temuan Audit

### DSH-01: Modal Drilldown Grafik Analitik Donat dan Tren Bulanan Ditolak (403 Forbidden)
- **Kategori:** Pilar 2 (Keamanan) & Pilar 6 (UI/UX Interactivity)
- **Tingkat Keparahan:** Tinggi (High)
- **Lokasi Berkas:**
  - `app/Views/admin/dashboard.php:957`
  - `app/Controllers/Admin/Dashboard.php:488`
- **Analisis Masalah:**
  Pada method `Admin\Dashboard::apiDetailChart()`, diterapkan pengecekan `$this->request->isAJAX()`. Namun pada JavaScript view `admin/dashboard.php` di fungsi `fetchChartDetail()`:
  ```javascript
  fetch(url)
      .then(res => res.json())
  ```
  Pemanggilan `fetch(url)` tidak menyertakan header `X-Requested-With: XMLHttpRequest`. Akibatnya, pada CodeIgniter 4, `$this->request->isAJAX()` bernilai `false`, sehingga controller membalas dengan status HTTP 403 Forbidden (*Akses ditolak*). Setiap kali pengguna mengklik irisan grafik sebaran predikat atau titik grafik tren bulanan, modal drilldown gagal memuat data.
- **Rekomendasi Perbaikan:**
  Tambahkan header `X-Requested-With: XMLHttpRequest` pada konfigurasi request `fetch()` di `fetchChartDetail()`:
  ```javascript
  fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  ```

---

### DSH-02: Beban Pemrosesan Legacy SKP `rencana_kinerja` yang Tidak Digunakan di Tampilan
- **Kategori:** Pilar 3 (Integritas Data) & Pilar 4 (Performa & Optimasi)
- **Tingkat Keparahan:** Tinggi (High)
- **Lokasi Berkas:** `app/Controllers/Admin/Dashboard.php:73–205`
- **Analisis Masalah:**
  Sebanyak ~130 baris kode di `Admin\Dashboard::index()` mengeksekusi kueri tabel `rencana_kinerja`, mem-parsing JSON `realisasi_bulanan`, menghitung `$rataRataCapaianGlobal`, `$totalIndikator`, perulangan ganda untuk `$chartUserLabels`, `$chartUserData`, `$kinerja_per_user`, serta mengurutkan `$chartIndikatorLabels`. Seluruh data ini dikirimkan ke JSON response dan array data view `$data`, namun **sama sekali tidak dirender pada antarmuka admin**. Bento UI modern dashboard admin sepenuhnya mengandalkan data kinerja harian terverifikasi (`$rataRataValue`, `$top5Unit`, `$sebaranKinerja`, `$trendBulananData`). Pemrosesan ini membebani CPU, RAM, dan memperlambat waktu respon TTFB (Time to First Byte).
- **Rekomendasi Perbaikan:**
  Hapus blok kueri dan pemrosesan legacy `rencana_kinerja` di Admin Dashboard sehingga controller hanya fokus mengambil data yang benar-benar ditampilkan.

---

### DSH-03: Kueri Pegawai Tanpa Filter Status Aktif dan Membocorkan Kolom Sensitif ke Memori
- **Kategori:** Pilar 2 (Keamanan) & Pilar 4 (Efisiensi & Kueri Basis Data)
- **Tingkat Keparahan:** Tinggi (High)
- **Lokasi Berkas:**
  - `app/Controllers/Admin/Dashboard.php:214, 249, 336, 505, 534`
  - `app/Controllers/User/Dashboard.php:61–64`
- **Analisis Masalah:**
  Kueri penarikan pegawai menggunakan `where('role !=', 'admin')->findAll()` tanpa membatasi kolom (`select()`) dan tanpa memvalidasi status kepegawaian aktif. Akibatnya:
  1. Kolom sensitif seperti password hash dan token ikut ditarik ke dalam memori PHP.
  2. Pegawai non-aktif, pensiun, atau mutasi keluar lama ikut dihitung ke dalam analitik organisasi, tingkat partisipasi, dan berpotensi masuk ke tabel Bottom 5 ("Perlu Perhatian Khusus") secara tidak adil.
- **Rekomendasi Perbaikan:**
  Terapkan selective column query:
  `select('id, nama_lengkap, nip, jabatan, unit, role, status_pegawai')`
  dan tambahkan filter status aktif:
  `where('status_pegawai', 'aktif')` (atau pengecekan status aktif yang berlaku di sistem).

---

### DSH-04: Dead Code Blok "Kinerja Seluruh Pegawai (Per Unit)" pada Modul User Dashboard
- **Kategori:** Pilar 1 (Arsitektur) & Pilar 5 (Kebersihan Kode & Pemeliharaan)
- **Tingkat Keparahan:** Sedang (Medium)
- **Lokasi Berkas:**
  - `app/Controllers/User/Dashboard.php:85–151`
  - `app/Views/user/dashboard.php:215–280, 472–479, 682–782`
- **Analisis Masalah:**
  Pada User Dashboard terdapat pengecekan `$isSuper = hasAnyRole([...])`. Jika true, controller menghitung analitik unit seluruh organisasi dan view menampilkan grafik batang unit dan modal drilldown unit. Padahal `DashboardController.php` telah mengalihkan seluruh role pimpinan/super (`admin`, `direktur`, `wadir`, `kabag`, `kepegawaian`, `spm`, `manajemen`) langsung ke Admin Dashboard. Pengguna yang masuk ke User Dashboard murni adalah staf atau atasan langsung level operasional. Blok kode ini tidak pernah dieksekusi oleh staf biasa dan menjadi dead code yang membingungkan.
- **Rekomendasi Perbaikan:**
  Hapus blok `$isSuper` unit chart dari controller dan view User Dashboard agar modul user murni fokus pada Kinerja Pribadi, Radar Mutu ECC, dan Monitoring Staf Binaan (jika atasan) atau Rekan Unit.

---

### DSH-05: Inkonsistensi Mapping Predikat Kinerja dan Residu Key Lawas
- **Kategori:** Pilar 3 (Integritas Data) & Pilar 5 (Standarisasi Predikat)
- **Tingkat Keparahan:** Sedang (Medium)
- **Lokasi Berkas:**
  - `app/Controllers/Admin/Dashboard.php:471`
  - `app/Views/admin/dashboard.php:762–766, 971–975`
  - `app/Views/user/dashboard.php:166–170, 422–426, 737–741`
- **Analisis Masalah:**
  1. Pada fallback `$sebaranKinerja` di controller admin masih tertulis key `'cukup' => 0` padahal standar resmi adalah `'butuh_perbaikan'` dan `'sangat_kurang'`.
  2. Logika pewarnaan badge di JavaScript terpecah-pecah di beberapa lokasi dengan aturan rentang yang tidak seragam (misal: di view user, rentang 75–90% dipetakan ke `bg-secondary` bukan `bg-info`).
- **Rekomendasi Perbaikan:**
  Pusatkan logika penetapan warna badge dan label predikat kinerja di masing-masing view ke dalam fungsi helper JavaScript tunggal yang 100% mematuhi aturan baku `AGENTS.md` 4.C:
  - Sangat Baik (> 100% - 150%): `bg-success`
  - Baik (> 90% - 100%): `bg-primary`
  - Butuh Perbaikan (> 75% - 90%): `bg-info text-dark`
  - Kurang (> 25% - 75%): `bg-warning text-dark`
  - Sangat Kurang (<= 25% dan > 0%): `bg-danger`
  - Belum Dinilai (0% atau belum ada nilai): `bg-secondary`

---

### DSH-06: Duplikasi Perulangan Pegawai di Admin Dashboard
- **Kategori:** Pilar 4 (Efisiensi & Kueri Basis Data)
- **Tingkat Keparahan:** Rendah (Low)
- **Lokasi Berkas:** `app/Controllers/Admin/Dashboard.php:232–244` & `336–356`
- **Analisis Masalah:**
  Setelah melakukan loop pada `$daftarSemuaUser` untuk menghitung `$statPegawai` dan mengisi `$rekapDashboard`, controller admin melakukan loop ulang pada `$allUsersForUnit` dan memanggil kembali `hitungKinerjaPegawai($u['id'], $bulan_kinerja, ...)` untuk mengagregasi data unit (`$unitStats`). Hal ini menggandakan beban kalkulasi dalam memori.
- **Rekomendasi Perbaikan:**
  Gabungkan akumulasi `$unitStats` ke dalam loop pertama saat membangun `$rekapDashboard` ketika `$canSeeAll` aktif, sehingga cukup satu kali lintasan (*single-pass* $O(N)$).

---

### DSH-07: Mikro-Kopi Pesan Dialog Galat Masih Bersifat Teknis Jaringan
- **Kategori:** Pilar 7 (Gaya Bahasa, Notifikasi & Mikro-Kopi)
- **Tingkat Keparahan:** Rendah (Low)
- **Lokasi Berkas:** `app/Views/admin/dashboard.php:996`
- **Analisis Masalah:**
  Pesan saat AJAX fetch gagal berbunyi: *"Gagal menghubungi server."*. Berdasarkan panduan `AGENTS.md` 6, pesan dialog harus berorientasi pada aksi solutif pengguna tanpa mengekspos istilah infrastruktur teknis.
- **Rekomendasi Perbaikan:**
  Ubah pesan error menjadi kalimat ramah non-teknis: *"Gagal memuat data. Silakan coba lagi."*.

---

## 4. Kesimpulan & Langkah Selanjutnya
Modul Dashboard (Admin dan User) memiliki fondasi visual bento UI yang modern, interaktif, dan responsif. Dengan melakukan perbaikan terhadap 7 temuan di atas (khususnya bug header AJAX DSH-01, eliminasi dead code legacy DSH-02 & DSH-04, serta penyaringan pegawai aktif DSH-03), performa pemuatan dashboard akan melonjak drastis, keamanan data terjaga, dan pengalaman pengguna menjadi sempurna.

Rencana Implementasi (*Implementation Plan*) terstruktur telah disiapkan pada dokumen pendamping untuk dieksekusi setelah mendapatkan persetujuan pengguna.
