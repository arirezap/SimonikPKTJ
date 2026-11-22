---
name: Evidence Command Center (ECC)
description: Sistem desain Clean Enterprise Bento Grid untuk manajemen kinerja, pemetaan SDM, dan tata kelola ketarunaan PKTJ Tegal yang sederhana dan mudah digunakan.
colors:
  primary: "#0d6efd"
  primary-dark: "#1e40af"
  primary-navy: "#1e3a8a"
  sky-accent: "#0284c7"
  navy-header: "#0f172a"
  emerald-success: "#10b981"
  amber-warning: "#f59e0b"
  amber-emphasis: "#d97706"
  crimson-danger: "#dc2626"
  purple-alumni: "#7c3aed"
  canvas: "#f8fafc"
  canvas-subtle: "#f1f5f9"
  surface: "#ffffff"
  surface-tint: "#eff6ff"
  border: "#e2e8f0"
  text-secondary: "#475569"
  text-muted: "#64748b"
typography:
  display:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif"
    fontSize: "1.75rem"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif"
    fontSize: "1.15rem"
    fontWeight: 700
    lineHeight: 1.3
  title:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif"
    fontSize: "0.95rem"
    fontWeight: 600
    lineHeight: 1.4
  body:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
  label:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "0.04em"
rounded:
  xs: "4px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "20px"
  pill: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  sm-md: "12px"
  md: "16px"
  lg: "24px"
  xl: "32px"
  2xl: "40px"
  3xl: "48px"
  3xl-plus: "56px"
  4xl: "64px"
  5xl: "80px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
    typography: "{typography.body}"
    rounded: "{rounded.pill}"
    padding: "8px 24px"
    height: "40px"
  button-primary-hover:
    backgroundColor: "{colors.primary-dark}"
    textColor: "{colors.surface}"
  button-pill-sm:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.navy-header}"
    typography: "{typography.body}"
    rounded: "{rounded.pill}"
    padding: "4px 12px"
    height: "32px"
  card-bento:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.navy-header}"
    rounded: "{rounded.lg}"
    padding: "24px"
  card-dark-action:
    backgroundColor: "{colors.navy-header}"
    textColor: "{colors.surface}"
    rounded: "{rounded.lg}"
    padding: "24px"
  input-control:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.navy-header}"
    typography: "{typography.body}"
    rounded: "{rounded.sm}"
    padding: "8px 12px"
    height: "36px"
  nav-link-active:
    backgroundColor: "{colors.surface-tint}"
    textColor: "{colors.primary-dark}"
    typography: "{typography.body}"
    rounded: "{rounded.sm}"
    padding: "8px 12px"
    height: "40px"
  tab-pill-active:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
    typography: "{typography.body}"
    rounded: "{rounded.md}"
    padding: "8px 16px"
    height: "36px"
  badge-status:
    backgroundColor: "{colors.surface-tint}"
    textColor: "{colors.primary-dark}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: "4px 12px"
---

# Design System: Evidence Command Center (ECC)

**Nama Resmi Aplikasi:** Evidence Command Center (ECC)  
**Versi:** 3.1.0 (Baseline ECC 1.5 Dikunci + Prinsip Clean & Easy to Use + Zero-Emoji + Bahasa Non-Teknis — November 2026)  
**Karakter Penggunaan:** Portal Kerja Harian & Pemantauan Eksekutif (PKTJ Tegal, Survei Pemetaan SDM, serta Layanan Mandiri Taruna & Alumni)

## Overview

**Creative North Star: "The Civic Command Bento"**

Evidence Command Center (ECC) dirancang agar bersih, tenang, lapang, dan langsung mudah digunakan oleh seluruh pimpinan, dosen, staf, taruna, maupun alumni PKTJ Tegal tanpa harus belajar aturan rumit. Setiap halaman memakai kartu Bento putih di atas latar abu-abu lembut dengan jarak napas lega berpola **4px Theory & 8-Point Grid** sehingga informasi penting cepat ditemukan dalam sekali lihat.

Kesederhanaan visual dan bahasa menjadi fondasi utama: seluruh tampilan bebas emoji, hemat ikon (hanya dipakai pada tempat yang benar-benar membantu fungsi), memakai tipografi `'Inter'` yang tajam untuk angka maupun tabel, mengikuti kepatuhan skala kelipatan `4px`, serta menggunakan kalimat Indonesia sehari-hari yang singkat dan bebas istilah IT atau pemrograman. Pola tampilan ECC 1.5 yang sudah berjalan menjadi acuan tunggal; rancangan kasar atau mock-up hanya menentukan urutan isi informasi tanpa meniru ukuran, warna, atau hiasan berlebihan.

**Key Characteristics:**
- Tampilan bersih (*clean*) dan mudah digunakan (*easy to use*) dengan ruang putih lega serta alur tindakan yang jelas (filter di atas, tombol aksi utama di bawah)
- Kepatuhan **4px Theory & 8-Point Grid**: setiap padding, margin, gap, radius, tinggi kontrol, dan kotak ikon wajib merupakan kelipatan utuh `4px` (`4, 8, 12, 16, 24, 32, 36, 40, 44, 48, 56, 64, 80px`) tanpa nilai ganjil/acak
- 100% bebas emoji di seluruh antarmuka pengguna—tidak ada emoji pada judul, tombol, badge status, notifikasi, pesan dialog, tabel, maupun kalender
- Kebijakan ikon minimalis: ikon Bootstrap (`bi bi-...`) hanya dipakai bila sangat dibutuhkan untuk navigasi utama, tombol aksi, atau penanda status penting
- 100% bahasa non-teknis bagi pengguna: dilarang memunculkan istilah IT atau pemrograman pada label, penjelasan form, notifikasi, alert, dan pop-up konfirmasi
- Kartu Bento putih (`16px` sudut membulat) di atas kanvas terang yang konsisten di ECC 1.x, Survei SDM, dan ECC 2.0
- Adaptasi responsif mulus: tabel desktop ringkas berubah menjadi kartu sentuh ramah jempol (`>= 44px`, kelipatan `4px`) di layar ponsel (`< 768px`)

## Colors

ECC menggunakan palet biru-slate resmi PKTJ yang tenang agar angka capaian dan status penting langsung menonjol tanpa membuat mata cepat lelah.

### Primary
- **Royal Cobalt** (`primary`): Warna utama tombol simpan/kirim, penanda menu sidebar yang sedang aktif, dan sorotan fokus input.
- **Executive Indigo** (`primary-dark`): Warna teks menu aktif, kondisi hover tombol utama, dan aksen judul bagian.
- **PKTJ Navy Accent** (`primary-navy`): Warna tombol utama halaman masuk dan penegas identitas institusi.
- **Sky Command** (`sky-accent`): Aksen tombol unduh laporan di Ringkasan Ketarunaan dan penanda status *Aktif*.

### Secondary
- **Verification Emerald** (`emerald-success`): Penanda status tervalidasi, cuti resmi, capaian sangat baik, serta tanda laporan harian sudah terkirim.
- **Amber Advisory** (`amber-warning` / `amber-emphasis`): Penanda status draf, skorsing, peringatan akses terbatas (*"Akses Terbatas"*), dan pengumuman pemeliharaan sistem.
- **Crimson Alert** (`crimson-danger`): Penanda penolakan, status drop out, pelanggaran terbukti, baris ganda, serta tombol keluar/hapus.
- **Alumni Violet** (`purple-alumni`): Penanda status *Lulus* dan tautan profil alumni tervalidasi.

### Neutral
- **Deep Slate Navy** (`navy-header`): Warna utama teks judul, angka KPI besar, serta latar satu kartu fokus gelap (*Perlu Tindak Lanjut*) pada Ringkasan.
- **Pure Surface White** (`surface`): Latar dasar kartu Bento, header atas, sidebar navigasi, tabel, dan kolom isian.
- **Cool Slate Canvas** (`canvas` / `canvas-subtle`): Latar halaman lembut untuk memisahkan kartu putih dengan latar belakang secara alami.
- **Slate Hairline** (`border`): Garis pemisah tipis (`1px`) pada kartu, tabel, dan kolom input.
- **Slate Secondary & Muted** (`text-secondary` / `text-muted`): Teks menu, judul kolom tabel, keterangan bantu singkat, dan catatan kaki halaman.
- **Pastel Surface Tint** (`surface-tint`): Latar lembut untuk menu aktif dan kotak status *Aktif*.

### Named Rules
**The Baseline Preservation Rule.** Pertahankan tampilan latar terang kartu putih di seluruh modul; kartu gelap (`navy-header`) dibatasi maksimal satu kartu fokus penindaklanjutan pada halaman Ringkasan agar halaman tetap terasa ringan dan bersih.
**The Contrast Floor Rule.** Semua teks, angka, dan ikon penting di atas kartu berwarna pastel maupun sidebar putih harus mudah dibaca dengan kontras minimal standar keterbacaan (`>= 4.5:1` untuk teks normal, `>= 3.0:1` untuk ikon)—gunakan variasi warna teks gelap/emphasis dibandingkan kuning cerah atau abu-abu terlalu pucat.

## Typography

**Display Font:** `Inter` (cadangan `system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif`)  
**Body Font:** `Inter` (cadangan `system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif`)  
**Label/Mono Font:** `SFMono-Regular, Menlo, Monaco, Consolas, monospace` (khusus tampilan arsip cetak atau kode referensi bila diperlukan)

**Character:** Huruf modern yang bersih dan mudah dibaca pada ukuran kecil maupun rapat, dengan kesejajaran angka lurus (*tabular*) sehingga deretan nilai, NIP, tanggal, dan jumlah taruna selalu rapi lurus ke bawah. *(Referensi internal §3.1 — Skala Tipografi & Ikonografi).*

### Hierarchy
- **Display** (`700` bold, `1.75rem` / `28px`, tinggi baris `1.2`, spasi huruf `-0.02em`): Judul utama halaman (`h1`) dan angka besar KPI (hingga `2.25rem` pada ringkasan eksekutif).
- **Headline** (`700` bold, `1.15rem` / `18px`, tinggi baris `1.3`): Judul kartu Bento dan judul kotak dialog konfirmasi.
- **Title** (`600` semi-bold, `0.95rem` / `15px`, tinggi baris `1.4`): Subjudul bagian dan judul kartu di layar ponsel.
- **Body** (`400`/`500` regular/medium, `0.875rem` / `14px`, tinggi baris `1.5`): Isi tabel, kolom isian formulir, tautan sidebar (`13px`), dan pesan panduan singkat.
- **Label** (`600`/`700` semi-bold/bold, `0.75rem` / `12px`, tinggi baris `1.4`, huruf kapital di header tabel/badge): Kepala kolom tabel, lencana peran, waktu ringkas, dan keterangan kecil di bawah angka.

### Named Rules
**The Tabular Precision Rule.** Seluruh angka pada kartu metrik, kolom nilai kinerja, NIP, tanggal, dan hitungan populasi taruna wajib rata sejajar (*tabular numerals*) agar mudah dibandingkan antarbaris.
**The Single-Line Sidebar Rule.** Setiap nama menu sidebar (`0.8125rem`) tampil rapi dalam satu baris tunggal tanpa membungkus turun ke baris kedua pada mode lebar (`256px`) maupun ringkas (`72px`).

## Layout

Struktur ruang memakai **4px Theory** (`4px` sebagai satuan dasar pembagi terkecil) yang dikelompokkan dalam irama **8-Point Grid** agar setiap elemen antarmuka selaras, mudah dipindai, dan seragam di seluruh halaman:

### Aturan Kepatuhan 4px Theory & 8-Point Grid
- **Satuan Dasar `4px` (*Base Atomic Unit*)**: Seluruh nilai `padding`, `margin`, `gap`, `border-radius`, tinggi tombol/kontrol, dan wadah ikon **wajib kelipatan utuh `4px`** (`n x 4px`).
- **Skala Utama Kelipatan `8px` (*Macro Layout & Bento*)**: `8px` (`2x4`), `16px` (`4x4`), `24px` (`6x4`), `32px` (`8x4`), `40px` (`10x4`), `48px` (`12x4`), `56px` (`14x4` untuk tinggi header logo sidebar & header notifikasi), `64px` (`16x4`), `72px` (`18x4` untuk sidebar ringkas), `80px` (`20x4`), dan `256px` (`64x4` untuk sidebar penuh).
- **Skala Presisi Kelipatan `4px` (*Micro Steps & Controls*)**:
  - `4px` (`1x4`): celah rapat antar lencana, sel kalender ponsel, dan sudut elemen kecil.
  - `12px` (`3x4`): padding vertikal sel tabel (`12px 16px`), sudut ubin status, dan tab navigasi.
  - `20px` (`5x4`): sudut dialog konfirmasi/kartu login dan indentasi sub-menu (khusus komponen tersebut, tidak dipakai untuk jarak kartu acak).
  - `28px` (`7x4`): tinggi pill filter cepat dan lencana status ringkas (`24px–28px`).
  - `36px` (`9x4`): tinggi standar input dan kontrol toolbar desktop.
  - `44px` (`11x4`): batas minimal ergonomi area sentuh ponsel (`min-height: 44px`).
- **Nilai Terlarang (*Off-Grid*)**: Dilarang memakai angka ganjil atau bukan kelipatan `4px` seperti `5px`, `6px`, `7px`, `10px`, `13px`, `14px`, `15px`, `18px`, `62px`, atau `95px` pada tata letak baru maupun perbaikan tampilan. *(Pengecualian tunggal: ketebalan garis batas `1px` dan aksen border/indikator tepi `2px–3px`).*

### Named Rules
**The 4px Divisibility Rule.** Setiap ukuran spasi, lengkung sudut, tinggi kontrol, dan kotak ikon harus habis dibagi `4` dalam piksel; pilih kelipatan `8px` untuk jarak antarblok dan gunakan langkah `4px` (`12px`, `28px`, `36px`, `44px`) hanya pada elemen mikro dan ergonomi sentuh.

### Penerapan Kerangka & Responsivitas
- **Kerangka Aplikasi Utama (ECC 1.x & ECC 2.0):** Sidebar kiri (`256px` penuh, `72px` mode ringkas dengan pratinjau melayang pada layar `>= 992px`; drawer geser di layar `< 992px`), bilah atas menempel dengan efek buram lembut (`backdrop-filter: blur(12px)`), area isi utama dengan jarak tepi `24px`, dan footer bersih di bagian bawah. Tombol pindah modul ditempatkan di bagian paling bawah sidebar (`Modul Ketarunaan` <-> `Modul Kinerja`).
- **Tata Letak Fokus Mandiri:**
  - **Survei Pemetaan SDM:** Satu kolom terpusat (lebar maksimal `880px`) agar responden fokus mengisi tanpa gangguan sidebar.
  - **Keamanan Akun Taruna:** Kotak ringkas terpusat (lebar maksimal `720px`) untuk pembaruan kata sandi mandiri taruna.
- **Kepadatan Terkontrol pada Kartu & Tabel:** Kartu Bento memakai padding `24px` di desktop (`16px` di layar ponsel `< 768px`). Tabel data panjang dibatasi tinggi gulir `480px` (`440px` di pop-up) dengan header kolom tetap di atas dan ruang sel `12px 16px`.
- **Header Tabel Wajib Dapat Diklik untuk Pengurutan (*Clickable Table Header Sorting*):** Setiap ada tabel data di halaman mana pun, kepala kolom datanya (`<th>`) wajib dapat diklik (dan diakses keyboard dengan atribut `aria-sort` serta ikon indikator urut) untuk mengurutkan isi tabel naik/turun (*client-side* untuk tabel ringkas atau *server-side* untuk tabel berpaginasi). Kolom murni tombol aksi (jika ada) boleh tidak ikut diurutkan.
- **Kenyamanan Layar Ponsel (`< 768px`):** Grid multi-kolom tersusun vertikal otomatis, tabel lebar beralih menjadi kartu sentuh (`>= 44px` area sentuh), ukuran huruf input dipastikan `16px` agar layar ponsel tidak melakukan zoom otomatis, dan grafik melingkar membungkus rapi di atas batang progres pada layar sempit.

## Elevation & Depth

ECC memakai kedalaman visual yang sangat halus: garis batas tipis `1px` untuk membedakan kartu secara jernih, dipadukan bayangan lembut hanya untuk memberi kesan lapisan ringan tanpa tampilan berat atau mengilap berlebihan.

### Shadow Vocabulary
- **Kartu Tata Kelola Diam** (`box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04)`): Kedalaman ringan untuk kartu tata kelola alumni, beasiswa, lowongan, dan pelanggaran (`1px solid #e2e8f0`).
- **Kartu Bento Ringkasan** (`box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.02)`): Elevasi standar pada halaman ringkasan.
- **Kartu Bento Saat Disentuh Kursor** (`box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08), 0 4px 10px rgba(15, 23, 42, 0.03)` disertai naik tipis `-2.5px`): Penegasan halus untuk kartu atau ubin yang bisa diklik.
- **Menu Notifikasi & Pop-Up Dialog** (`box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.16), 0 4px 16px -2px rgba(15, 23, 42, 0.06)`): Lapisan melayang untuk daftar notifikasi (`384px`) dan dialog konfirmasi.
- **Cincin Fokus Aksesibel** (`box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15)`): Tanda fokus biru lembut ketika pengguna berpindah memakai papan ketik (*Tab*).

### Named Rules
**The Restrained Surface Rule.** Kartu tetap tenang tanpa guncangan saat diam; efek angkat tipis hanya muncul saat kursor mengarah ke kartu yang dapat diklik, dan seluruh gerak dinonaktifkan otomatis bagi pengguna dengan pengaturan *Reduce Motion*.
**The Simple Motion Rule.** Gerakan animasi dibuat sederhana, cepat (`150ms–250ms`), dan tidak mengubah ukuran lebar/tinggi layout secara mendadak agar halaman terasa responsif dan tidak memicu kedipan tata letak.

## Shapes

Konsistensi lengkung sudut memudahkan mata membedakan wadah besar, sub-bagian, dan tombol aksi:
- **Sudut Kecil (`4px`):** Kotak warna keterangan kalender, tepi batang gulir, kotak centang, dan lencana peran topbar.
- **Kontrol & Navigasi (`8px`):** Kotak pilih dropdown, tombol tanggal, langkah wizard, tautan menu sidebar, dan tombol navigasi `32px × 32px` pada jendela detail.
- **Sub-Kartu & Wadah Ikon (`12px`):** Ubin status pendidikan taruna, pill tab navigasi, kotak ikon header (`40px × 40px` / `48px × 48px`), dan kartu ringkas versi ponsel.
- **Kartu Bento Utama (`16px`):** Semua kartu ringkasan, panel formulir, bilah tab tata kelola, dan panel notifikasi.
- **Kotak Dialog & Kartu Login (`20px`):** Jendela konfirmasi dan kartu halaman masuk.
- **Kapsul Penuh (`9999px` / pill):** Tombol aksi utama (`40px`), tombol aksi ringkas di dalam tabel (`32px`), lencana status, dan pengalih segmen.

## Components

### Buttons
- **Shape:** Kapsul penuh (`9999px`) untuk tombol aksi utama maupun tombol ringkas dalam tabel; sudut `8px` untuk tombol toolbar filter dan tombol lihat/sembunyikan kata sandi (`44px × 36px`).
- **Primary:** Warna biru `primary`, teks putih tebal, tinggi `40px` (`8px 24px` padding), dengan respons tekan ringan saat diklik agar terasa responsif.
- **Compact & Toolbar Variants:** Tombol aksi di tabel memakai tinggi `32px` (`4px 12px`); tombol toolbar filter memakai tinggi `36px` di desktop dan diperbesar menjadi minimal `44px` di layar ponsel agar nyaman disentuh jempol.
- **Hover / Focus:** Berubah halus ke `primary-dark`; dialog konfirmasi selalu meletakkan tombol *Batal* di sebelah kiri dan tombol aksi utama di sebelah kanan.

### Chips
- **Style:** Lencana kapsul (`9999px`, `4px 12px` padding) dengan latar warna pastel lembut dan garis tepi tipis sewarna supaya status cepat terbaca tanpa terlihat berat.
- **State & Variants:** Diperbolehkan **khusus** untuk lencana status data di dalam baris tabel atau kartu antrean (*Tervalidasi*, *Diajukan*, *Draf*, *Perlu Perbaikan*), predikat nilai kinerja, dan tombol filter cepat. **Dilarang keras** dipakai sebagai pill konteks, label lingkungan data, atau penanda status akses di area judul halaman maupun di samping item menu sidebar (*"Data Operasional"*, *"Data Simulasi"*, *"Profil dasar"*, *"PIC Tracer Study"*, *"Akses Terbatas"*).

### Cards / Containers
- **Corner Style:** Sudut `16px` untuk kartu utama dan `12px` untuk ubin di dalamnya.
- **Background:** Putih bersih (`#ffffff`) dengan garis batas tipis; satu kartu gelap (`#0f172a`) dipakai khusus untuk panggilan tindakan antrean prioritas.
- **Shadow Strategy:** Bayangan tipis saat diam agar kartu bersih; tidak menggunakan banyak garis kotak di dalam kartu bila jarak spasi sudah cukup memisahkan informasi.
- **Internal Padding:** `24px` pada layar desktop dan `16px` pada layar ponsel atau kotak metrik ringkas.

### Inputs / Fields
- **Style:** Tinggi nyaman `36px–40px` berjarak rapi dengan sudut `8px`; menggunakan ukuran font `16px` pada layar ponsel agar nyaman diketik tanpa zoom otomatis.
- **Focus:** Cincin cahaya biru lembut tanpa garis kotak kasar.
- **Safeguards:** Input angka penilaian menyembunyikan panah putar bawaan browser, mengunci perubahan angka tak sengaja saat menggulir mouse, serta menerima penulisan desimal koma Indonesia. Pemilih tanggal menandai hari libur aktif dengan merah tegas dan meredupkan tanggal yang belum tiba.

### Navigation
- **Sidebar & Topbar:** Sidebar tersusun per kategori singkat dengan garis biru vertikal di sisi item aktif; topbar menampilkan salam ringkas, lencana peran, tanggal hari ini, lonceng notifikasi, dan menu profil (*Profil Saya*, *Keluar*).
- **Tab Tata Kelola & Analitik:** Bilah tab putih `16px` berisi pill `12px` yang berubah biru solid saat aktif untuk berpindah tampilan tanpa kebingungan.
- **Wizard Pelanggaran 4 Tahap:** Alur bertahap (*1. Pencatatan*, *2. Pemeriksaan*, *3. Penetapan dan Tindak Lanjut*, *4. Dokumen Bukti*) yang otomatis menjaga urutan agar staf mengisi kasus tahap demi tahap secara mudah.

### Signature Components
- **Grid 6 Status Pendidikan Taruna:** Enam ubin berwarna lembut yang langsung merangkum jumlah taruna (*Aktif*, *Skorsing*, *Cuti Akademik*, *Mengundurkan Diri*, *Drop Out*, *Lulus*) disertai ringkasan total di bagian bawahnya.
- **Kalender Aktivitas Harian:** Grid 7 kolom bebas emoji (`64px` desktop, `48px` ponsel) dengan 5 tingkat intensitas hijau lembut serta legenda kapsul `32px` yang membuka rincian pekerjaan per tanggal dalam satu klik.
- **Dialog Unduhan Berkas & Notifikasi Sudut:** Indikator progres bersih saat menyiapkan berkas Excel/PDF serta pesan singkat di pojok layar untuk aksi rutin seperti simpan draf agar alur kerja pengguna tidak terhenti.

## Do's and Don'ts

### Do:
- **Do** gunakan nama **"Evidence Command Center (ECC)"** (atau **ECC**) dan kata **"staf"** secara konsisten di seluruh teks tampilan, tombol, label, variabel, dan dokumentasi.
- **Do** terapkan **4px Theory & 8-Point Grid**: pastikan seluruh `padding`, `margin`, `gap`, radius, tinggi kontrol, dan kotak ikon habis dibagi `4` (`4, 8, 12, 16, 20, 24, 28, 32, 36, 40, 44, 48, 56, 64, 72, 80px`) dan patuhi acuan ECC 1.5.
- **Do** jaga tampilan tetap bersih (*clean*), lapang, dan mudah digunakan (*easy to use*) tanpa penumpukan elemen yang membingungkan.
- **Do** tulis seluruh label antarmuka, panduan, notifikasi, alert, dan pop-up menggunakan bahasa Indonesia sehari-hari yang singkat, padat, jelas, serta ramah pengguna.
- **Do** batasi pemakaian ikon Bootstrap (`bi bi-...`) hanya untuk kebutuhan nyata (navigasi utama, tombol aksi penting, penanda status kritis, dan avatar).
- **Do** gunakan kontrol tombol yang dapat diakses papan ketik (`<button>`), urutan heading runtut (`h1` -> `h2`), header kolom tabel yang dapat diklik untuk pengurutan (`aria-sort`), label aksesibilitas yang jelas, dan kontras teks minimal `4.5:1` pada kartu terang maupun pastel.
- **Do** tampilkan keadaan kosong (*empty state*) dengan kalimat singkat yang jelas pada setiap tabel atau daftar notifikasi agar pengguna tidak mengira halaman sedang macet.
- **Do** nonaktifkan animasi secara otomatis ketika pengguna mengaktifkan preferensi gerak minimal (*Reduce Motion*).
- **Do** jaga area judul halaman dan item menu sidebar tetap bersih—cukup tampilkan judul utama dan subjudul singkat tanpa ditempeli badge pill keterangan tambahan; jika informasi penanda data diperlukan, letakkan di pengaturan atau keterangan teks polos di catatan kaki.

### Don't:
- **Don't** pasang desain badge pill konteks, status lingkungan, atau penanda lingkup di samping atau di bawah judul halaman serta item menu sidebar di halaman mana pun (dilarang pill seperti *"Data Operasional"*, *"Data Simulasi"*, *"Profil dasar"*, *"PIC Tracer Study"*, *"Akses Terbatas"*, atau pill tahun di header). Pill hanya untuk lencana status baris tabel/data dan tombol aksi CTA.
- **Don't** gunakan angka ukuran/spasi di luar kelipatan `4px` (seperti `5px`, `6px`, `7px`, `10px`, `13px`, `14px`, `15px`, `18px`, `62px`, `95px`) pada komponen atau perbaikan antarmuka.
- **Don't** gunakan emoji dalam bentuk apa pun di seluruh antarmuka (dilarang pada judul, tombol, status, notifikasi, tabel, sel kalender, pop-up konfirmasi, maupun pesan error).
- **Don't** menumpuk ikon dekoratif di setiap judul kartu, setiap judul kolom tabel, setiap label form, atau di setiap baris daftar jika teksnya sudah cukup menjelaskan maksudnya.
- **Don't** gunakan istilah teknis IT, bahasa pemrograman, atau istilah infrastruktur sistem (seperti *database*, *basis data*, *server*, *jaringan*, *API*, *SQL*, *query*, *JSON*, *cache*, *session*, *token*, *script*, *exception*, *syntax*, *null*, *error code*, *backend*, *toleransi sistem*) dalam pesan error, notifikasi, modal, tooltip, maupun label tampilan pengguna.
- **Don't** sebutkan nama aplikasi lama atau kata selain *"staf"* (*"bawahan"*, *"staff"*) di mana pun pada tampilan maupun dokumentasi.
- **Don't** meniru mock-up mentah (*pixel-copy*) yang melanggar jarak 8-Point Grid (`14px`, `18px`, `20px`), menyalin label simulasi, atau menyisipkan badge dekoratif tambahan.
- **Don't** tampilkan tanggal dan jam persetujuan/penilaian di antarmuka target atau rekap kinerja—cukup tampilkan nama pejabat penilai/penyetuju untuk menjaga kepatuhan audit.
- **Don't** ubah ukuran dimensi layout (`width` atau `all`) secara paksa di transisi atau membiarkan kegagalan permintaan halaman berlalu tanpa pesan ringkas kepada pengguna.

---

## Lampiran Spesifikasi Modul & Norma Operasional ECC

> Lampiran ini menjaga keselarasan rujukan aturan detail modul ECC 1.x, Survei SDM, dan ECC 2.0 (termasuk `§3.1`, `§18`, `§21.0`, dan `§21.5`).

### A.1 Kebijakan Tanpa Emoji, Pembatasan Ikon & Bahasa Non-Teknis (§18)
1. **Aturan Nol Emoji**: Seluruh modul aplikasi, termasuk dialog konfirmasi, banner peringatan, status harian, legenda kalender, dan dokumen ekspor, wajib **100% bebas emoji**.
2. **Aturan Ikon Minimalis**: Pasang ikon hanya saat membantu kejelasan aksi (*Tambah*, *Edit*, *Hapus*, *Unduh*, *Cari*, *Kembali*, menu sidebar, indikator setuju/tolak/draf, atau avatar). Jika label teks sudah jelas—misalnya judul kartu ringkasan atau kepala kolom tabel—biarkan tampil bersih tanpa ikon tambahan.
3. **Larangan Mutlak Bahasa IT / Pemrograman pada Teks Pengguna**:
   - **Hindari:** *"Gagal mengirim laporan harian karena gangguan basis data / server error 500."*  
     **Gunakan:** *"Gagal mengirim laporan. Silakan coba lagi."*
   - **Hindari:** *"Pengisian laporan periode Agustus 2026 ditutup sejak 05 September 2026 (Batas akhir bulan + toleransi 5 hari)."*  
     **Gunakan:** *"Pengisian laporan periode Agustus 2026 telah ditutup sejak 05 September 2026."*
   - **Hindari:** *"Silakan isi minimal satu kegiatan pada Tugas Pokok atau Tugas Tambahan sebelum mengirim ke atasan langsung."*  
     **Gunakan:** *"Isi minimal 1 kegiatan pokok atau tugas tambahan."*
   - **Hindari:** *"Data taruna masih kosong / null pada query validasi."*  
     **Gunakan:** *"Belum ada data yang menunggu validasi."*
4. **Standar Dialog Konfirmasi Singkat**:
   - **Judul Dialog:** 2–4 kata (*"Hapus Kegiatan?"*, *"Kirim Laporan?"*, *"Keluar dari Aplikasi?"*).
   - **Teks Penjelasan:** Maksimal 1 kalimat pendek dan tenang (*"Kegiatan ini akan dihapus."*).
   - **Tombol Aksi:** Kata kerja singkat (*"Ya, Hapus"*, *"Kirim"*, *"Batal"*).
   - **Umpan Balik Sukses:** *"Tersimpan"* / *"Terhapus"* dengan 1 kalimat ringkas (*"Draf berhasil disimpan."*).
5. **Tabel Pembakuan Istilah Bahasa Indonesia Resmi**:
   | Tidak Baku (Dilarang) | Baku ECC (Wajib) | Penempatan |
   | :--- | :--- | :--- |
   | `Password` / `Kata sandi` | **Kata Sandi** | Halaman masuk, profil, ubah kata sandi |
   | `Forgot Password` | **Lupa Kata Sandi?** | Halaman masuk |
   | `Email Address` | **Alamat Email** | Formulir profil, kelola pengguna |
   | `Logout` | **Keluar** | Menu profil pojok kanan atas |
   | `Simpan Perubahan` | **Simpan Profil** | Tombol utama formulir profil |
   | `Bawahan` / `Staff` | **Staf** | Seluruh modul, label, dan kode |

### A.2 Struktur Navigasi Sidebar Terpadu & Switcher Lintas Modul
1. **Sidebar ECC 1.x**: Memuat menu `Dashboard`, `ECC` (`LED`, `Simulasi Penilaian`), `Target Kinerja Bulanan`, `Lapor Kegiatan Harian`, `Rekap & Penilaian Kinerja`, `Kepegawaian` (`Monitoring Target Kinerja`, `Monitoring Penilaian Kinerja`, `Hasil Survei SDM`), `Kontrak Kinerja`, `Pakta Integritas`, `Panduan Penggunaan`, `Kelola Tim`, `Master Data`, `Kelola Pengguna` / `Daftar Pegawai`, `Log Aktivitas Sistem`, `Pengaturan Sistem`, serta tombol switcher **`Modul Ketarunaan`** di paling bawah bagi pemegang akses.
2. **Sidebar ECC 2.0**: Memuat `DASHBOARD` (`Ringkasan`), `DATA KETARUNAAN` (`Database Taruna` / `Profil Taruna Saya`, `Rekam Jejak Taruna` / `Rekam Jejak Saya`, `Prestasi`, `Pelanggaran`, `Beasiswa`), `KARIER & ALUMNI` (`Lowongan Pekerjaan`, `Alumni` tanpa badge pill tambahan), `TATA KELOLA` (`Validasi Data`, `Laporan`, `Pengaturan`), serta tombol switcher **`Modul Kinerja`** di paling bawah bagi akun pegawai.

### A.3 Standar Formulir Kinerja, Ekspor Dokumen Resmi & Keamanan Unggahan
- **Hierarki Formulir Target**: Konteks bulan/tahun dan tombol *"Salin dari Bulan Lain"* berada di bagian atas tabel; tombol aksi utama (*Tambah Target*, *Simpan Draf*, *Ajukan Target* / *Perbarui & Ajukan Ulang*) berada di bagian bawah.
- **Penilaian & Ekspor**: Rentang nilai kinerja seragam `0–150%`, dukungan penulisan desimal koma Indonesia, prasyarat target `disetujui` sebelum atasan menilai staf, serta cetak A4 Kontrak Kinerja/Pakta dan PDF rekap tahunan yang menyelaraskan riwayat pejabat penilai tanpa memajang tanggal/jam persetujuan pada tampilan UI.
- **Keamanan Berkas Unggahan**: Batas maksimal `2MB` di sisi peramban maupun server, verifikasi jenis gambar asli (`JPG`/`JPEG`/`PNG`), penamaan berkas acak, serta pemblokiran eksekusi skrip pada folder unggahan.

### §21.0 Baseline UI/UX ECC 1.5 & Kedudukan Mock-up ECC 2.0
> Sumber kebenaran visual adalah tampilan ECC 1.5 yang berjalan dan dokumen ini. Mock-up hanya dipakai untuk menyusun urutan informasi, bukan disalin ukurannya.

| Aspek | Diambil dari Mock-up | Wajib Mengikuti Standar ECC |
| :--- | :--- | :--- |
| Shell (`sidebar`, `header-promax`, `footer-promax`) | Tidak ada | Identik ECC 1.5 tanpa judul/logo tambahan ala mock-up |
| Wadah & Spasi | Urutan & kelompok blok | Kartu Bento `16px`, padding `24px` desktop / `16px` mobile, 8-Point Grid |
| Warna & Ikon | Makna status | Token palet resmi + bebas emoji + ikon seminimal mungkin |
| Komponen & Teks | Jenis tampilan (tabel, donut, antrean) | Select2, Flatpickr, SweetAlert2, kontrol `32/36/40px`, bahasa singkat non-teknis |

### §21.5 Standar Visual Dashboard Ringkasan ECC 2.0 (`/ketarunaan/dashboard`)
1. **Header & Filter Bar**: Judul `Dashboard ECC 2.0` dan subjudul *"Ringkasan status pendidikan, capaian, dan kualitas data"* tampil bersih **tanpa pill penanda mode** (`Data Operasional`/`Data Simulasi`) di area judul; didampingi filter Program Studi (`RSTJ`, `TRO`, `TO`), filter Angkatan, pemilih *Tanggal acuan* Flatpickr, dan tombol `Unduh Laporan` (khusus peran berwenang laporan). Keterangan lingkungan data cukup tampil sebagai teks polos di baris catatan kaki bawah.
2. **6 Pilar Status Pendidikan Taruna**:
   | Status | Warna Utama | Soft Background | Ikon BS | Keterangan |
   | :--- | :--- | :--- | :--- | :--- |
   | **Aktif** | `#0d6efd` / `#0284c7` | `#eff6ff` | `bi-people-fill` | Sedang menjalani pendidikan aktif |
   | **Skorsing** | `#664d03` | `#fef3c7` | `bi-pause-circle-fill` | Pembinaan sementara; ada rencana kembali |
   | **Cuti Akademik** | `#198754` | `#ecfdf5` | `bi-file-text-fill` | Izin cuti resmi; ada rencana kembali |
   | **Mengundurkan Diri** | `#664d03` / `#ea580c` | `#fff7ed` | `bi-box-arrow-right` | Berhenti atas permohonan resmi |
   | **Drop Out** | `#dc3545` / `#dc2626` | `#fef2f2` | `bi-x-circle-fill` | Diberhentikan secara resmi |
   | **Lulus** | `#7c3aed` | `#f5f3ff` | `bi-mortarboard-fill` | Lulusan resmi tervalidasi |
3. **Baris 4 Kartu Metrik Operasional**: *Prestasi Tervalidasi*, *Pelanggaran Terbukti* (mematuhi pembatasan visibilitas kasus sensitif), *Penerima Beasiswa*, dan *Lowongan Aktif*.
4. **Bento Grid Tengah & Bawah**: *Sebaran Status per Program Studi*, *Kualitas Data* (Donut Chart + 2 progress bar), *Perlu Tindak Lanjut* (kartu fokus `#0f172a`), *Aktivitas Terbaru*, dan *Akses Cepat* (tombol tambah/impor/ubah status tampil sesuai kewenangan pengelola taruna).
5. **Status Data Operasional Aktual**: Sistem berjalan pada mode **`produksi`** dengan data riil taruna aktif Prodi RSTJ Tingkat 1–4 (`427` taruna pada `18` kelas riil) tanpa data simulasi atau angka rekaan.
