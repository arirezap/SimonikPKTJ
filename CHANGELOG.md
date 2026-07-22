# CHANGELOG: Pembaruan Sistem Simonik PKTJ (Sesi Terakhir)

Dokumen ini berisi riwayat fitur, perbaikan (*bug fixes*), dan peningkatan antarmuka (UI/UX) yang telah diselesaikan pada sesi ini. Dokumen ini sangat berguna sebagai konteks apabila Anda memulai sesi (obrolan) baru dengan AI.

## 1. Peningkatan UI/UX (Antarmuka Pengguna)
- **Halaman Login (Bento & Modernisasi):** 
  - Desain ulang halaman login menjadi lebih ringkas dan modern (ala Bento).
  - Skala halaman dikecilkan agar pas di layar laptop tanpa *scroll* bawah (*fit-to-screen*).
  - Membuang spinner Bootstrap bawaan dan menggantinya dengan spinner CSS murni yang mulus (efek putaran cincin dan efek tombol memudar) saat memproses login.
- **Sidebar (Tema Terang):**
  - Mengubah latar belakang *sidebar* menjadi putih bersih (`bg-white` & `border-end`).
  - Menyesuaikan warna font agar cocok dengan tema putih.
  - Menambahkan animasi *zoom* ringan (hover) pada Logo PKTJ di *sidebar* melalui CSS (`.sidebar-logo:hover`).
- **Top Bar (Smart Scroll):**
  - Merapikan teks sapaan dari "Selamat datang kembali," menjadi sekadar "Selamat datang,".
  - Mengimplementasikan fitur **Smart Topbar**: Saat area konten di-*scroll* ke bawah lebih dari 60px, *top bar* akan otomatis menyembunyikan diri (*slide-up*). Saat di-*scroll* ke atas, ia muncul kembali.
- **Halaman Profil (Bento Grid Pro Max):**
  - Mengubah tampilan *form* profil linier menjadi **Bento Grid** (kartu-kartu terpisah untuk Identitas, Kredensial, dan Info Pegawai).
  - **Inisial Cerdas:** Menghapus gambar `default.png`. Jika *user* tidak memiliki foto atau menghapus foto profil, sistem otomatis mencetak Inisial Nama besar (misal: "AR").
  - Transisi Inisial Profil melalui *Javascript* berjalan mulus tanpa perlu memuat ulang halaman (*client-side*).

## 2. Struktur Pangkalan Data & Model (Database)
- **Penambahan Nomor HP:**
  - Telah dibuat berkas Migrasi Database CodeIgniter (`AddNoHpToUsers`) untuk menambah kolom `no_hp` (VARCHAR 20) ke tabel `users`. Migrasi sukses dieksekusi.
  - Model `app/Models/User.php` telah diperbarui dengan menambahkan `'no_hp'` ke dalam `$allowedFields`.
  - UI pada *Halaman Profil* telah disesuaikan dari "Nomor WhatsApp (Aktif)" dan `no_wa` menjadi **No. HP** (`no_hp`).

## 3. Perbaikan Kutu (*Bug Fixes* & Logika)
- **Perbaikan Kinerja Bulanan (Laporan Harian):**
  - *Bug Tab Melompat:* Memperbaiki *bug* di mana saat *user* mengganti filter bulan di tab "Target Kinerja Saya", UI malah melompat ke tab "Persetujuan Target Staf". Diselesaikan dengan menyisipkan input tersembunyi `source_tab` untuk melacak asal form.
  - *Bug Form Resubmission (403 Security Exception):* Memperbaiki error keamanan bawaan browser/CodeIgniter (CSRF Token) saat pengguna me-*refresh* (F5) halaman Target Kinerja. Diatasi dengan merombak kode pada `LaporanHarianController::index` menjadi pola keamanan **PRG (Post-Redirect-Get)**. Kini me-refresh halaman usai *filter* bulan/tahun 100% aman dan tidak melempar galat.
