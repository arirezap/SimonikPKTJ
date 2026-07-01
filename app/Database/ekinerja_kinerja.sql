-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 19 Jun 2026 pada 07.31
-- Versi server: 8.4.3
-- Versi PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `ekinerja_kinerja`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `indikator`
--

CREATE TABLE `indikator` (
  `id` int UNSIGNED NOT NULL,
  `nama_indikator` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `indikator`
--

INSERT INTO `indikator` (`id`, `nama_indikator`) VALUES
(1, 'IKK 1 Tingkat Penyerapan Diklat Pembentukan SDM Transportasi Darat/Laut/Udara yang Berkompetensi'),
(2, 'IKK 2 Persentase Peserta Diklat Transportasi'),
(3, 'IKK 3 Tingkat Pemenuhan ASN Transportasi Program Pembentukan'),
(4, 'IKK 4 Tingkat Pemenuhan ASN Transportasi Program Pelatihan'),
(5, 'IKK 5 Tingkat Pemenuhan SDM Transportasi Program Pembentukan'),
(6, 'IKK 6 Tingkat Pemenuhan SDM Transportasi Program Pelatihan'),
(7, 'IKK 7 Tingkat Pemenuhan Akreditasi dan Sertifikasi'),
(8, 'IKK 8 Tingkat Pemenuhan Sertifikasi Pelatihan oleh Lembaga yang Berwenang'),
(9, 'IKK 9 Persentase penelitian, HAKI dan Produk Inovasi Dosen dan Mahasiswa'),
(10, 'IKK 10 Tingkat pemenuhan sertifikasi pelatihan oleh lembaga yang berwenang'),
(11, 'IKK 11 Persentase kegiatan pengabdian masyarakat yang berdampak di bidang transportasi'),
(12, 'IKK 12 Persentase kualitas dan kuantitas dosen'),
(13, 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat'),
(14, 'IKK 14 Persentase Kerjasama di bidang Pendidikan dan pelatihan dengan stakeholder transportasi dan pendidikan'),
(15, 'IKK 20 Indeks Pemenuhan SDM BPSDMP'),
(16, 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP'),
(17, 'IKK 23 Indikator Kinerja Pelaksanaan Anggaran (IKPA)'),
(18, 'IKK 24 Nilai SAKIP BPSDMP');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_diklat`
--

CREATE TABLE `jadwal_diklat` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `nama_diklat` varchar(255) NOT NULL,
  `periode` varchar(255) NOT NULL,
  `jumlah_peserta` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `led_criteria`
--

CREATE TABLE `led_criteria` (
  `id` int UNSIGNED NOT NULL,
  `prodi` varchar(50) NOT NULL DEFAULT 'RSTJ',
  `nama_kriteria` text NOT NULL,
  `id_standar` int UNSIGNED DEFAULT NULL,
  `role_assignment` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `led_criteria`
--

INSERT INTO `led_criteria` (`id`, `prodi`, `nama_kriteria`, `id_standar`, `role_assignment`, `created_at`, `updated_at`) VALUES
(583, 'RSTJ', 'Kekhasan VMTS\n\nPernyataan VMTS yang unik dan spesifik sebagai identitas PT, UPPS, dan visi keilmuan program studi sebagai keunggulan kompetitif yang didukung dengan renstra dan kurikulum yang memadai.', 1, 'aak', NULL, NULL),
(584, 'RSTJ', 'Mekanisme penyusunan VMTS\n\nMekanisme dan keterlibatan pemangku kepentingan dalam penyusunan VMTS UPPS dan tujuan utama yang ingin dicapai dalam penyusunan visi keilmuan program studi  dengan mempertimbangkan kebutuhan masyarakat dan tantangan global.', 1, 'kuk', NULL, NULL),
(585, 'RSTJ', 'Tingkat pemahaman dan pencapaian VMTS\n\nTingkat pemahaman dan pencapaian VMTS UPPS dan visi keilmuan program studi oleh seluruh pemangku kepentingan internal dan eksternal serta pencapaian konkret  jangka pendek dan jangka menengah yang telah ditetapkan dalam VMTS UPPS dan visi keilmuan program studi.', 1, 'kuk', NULL, NULL),
(586, 'RSTJ', 'Sistem Tata Pamong\n\nI. Kelengkapan struktur organisasi dan kebijakan operasional yang berpedoman pada statuta Perguruan Tinggi yang digunakan.\n\nII. Perwujudan Good University Governance mengacu pada sistem tata kelola yang efektif, transparan, dan akuntabel.', 2, 'aak', NULL, NULL),
(587, 'RSTJ', 'Komitmen pimpinan dan kemampuan manajerial \n\nI. Pimpinan UPPS memiliki komitmen pada: \n(1) Visi dan tujuan organisasi; \n(2) Integritas dan transparansi; \n(3) Pengembangan sumber daya. \n\nII. Kemampuan manajerial pimpinan UPPS', 2, 'kuk', NULL, NULL),
(588, 'RSTJ', 'Kerja sama\n\nI. Relevansi kerja sama pendidikan, penelitian, dan PkM dengan visi UPPS serta visi keilmuan program studi.\n\nII. Kerja sama tingkat internasional, nasional, wilayah/lokal yang relevan dengan program studi dan dikelola oleh UPPS dalam 3 tahun terakhir.', 2, 'kuk', NULL, NULL),
(589, 'RSTJ', 'Pelaksanaan Kerja Sama\n\nUPPS memiliki bukti yang sahih terkait kerja sama yang telah memenuhi 3 aspek berikut: \n(1) Memberikan manfaat bagi program studi dalam pemenuhan proses pembelajaran, penelitian, PkM; \n(2) Memberikan peningkatan kinerja tridharma dan fasilitas pendukung program studi; dan \n(3) Memberikan kepuasan kepada mitra industri dan mitra kerja sama lainnya.', 2, 'kuk', NULL, NULL),
(590, 'RSTJ', 'Pengelolaan keuangan\n\nUPPS memiliki praktik pengelolaan sumber daya keuangan secara efektif dan efisien.', 2, 'kuk', NULL, NULL),
(591, 'RSTJ', 'Biaya operasional, dana penelitian dan PkM\n\nBiaya Operasional pendidikan', 2, 'kuk', NULL, NULL),
(592, 'RSTJ', 'Dana penelitian DTPS.', 7, 'kuk', NULL, NULL),
(593, 'RSTJ', 'Dana pengabdian kepada masyarakat', 7, 'kuk', NULL, NULL),
(594, 'RSTJ', 'Pemutakhiran kurikulum\n\nKeterlibatan pemangku kepentingan dalam proses evaluasi dan pemutakhiran kurikulum.', 7, 'aak', NULL, NULL),
(595, 'RSTJ', 'Profil lulusan dan CPL.\n\nI. Profil lulusan yang ditetapkan oleh Program Studi.\n\nII. Kesesuaian Profil lulusan dengan capaian pembelajaran (CPL)', 7, 'aak', NULL, NULL),
(596, 'RSTJ', 'Kesesuaian dan tinjauan CPL\n\nI. Kesesuaian CPL dengan standar kompetensi lulusan yang  mencakup: (1) Konsep  rekayasa terapan yang spesifik dengan disiplin ilmu terkait; (2) kemampuan teknis dan kemampuan beradaptasi dengan standar keteknikan dan Teknologi Baru; (3) Keterampilan komunikasi dan kemampuan kerja tim; (4) kepatuhan terhadap etika profesi.\n\nII. Proses tinjauan rutin CPL.', 7, 'aak', NULL, NULL),
(597, 'RSTJ', 'Rencana Proses Pembelajaran (RPS)\n\nI. Ketersediaan dan kelengkapan dokumen RPS yang terdiri dari:\n1. Nama program studi, nama dan kode mata kuliah, semester, sks, nama dosen pengampu;\n2. Capaian pembelajaran lulusan yang dibebankan pada capaian pembelajaran mata kuliah;\n3. Kemampuan akhir yang direncanakan pada tiap tahap pembelajaran untuk memenuhi capaian pembelajaran lulusan;\n4. Bahan kajian yang terkait dengan kemampuan yang akan dicapai;\n5. Metode pembelajaran;\n6. Waktu yang disediakan untuk mencapai kemampuan pada tiap tahap pembelajaran;\n7. Pengalaman belajar mahasiswa yang diwujudkan dalam deskripsi tugas yang harus dikerjakan oleh mahasiswa selama satu semester;\n8. Kriteria, indikator, dan bobot penilaian; dan\n9. Daftar referensi yang digunakan. \n\nII. Proses tinjauan rutin RPS', 7, 'aak', NULL, NULL),
(598, 'RSTJ', 'Proses Pembelajaran (PS)\n\nI. Proses pembelajaran untuk memastikan efektivitas, kualitas, dan keberhasilan pencapaian CPL.\n\nII. Tinjauan rutin proses pembelajaran.', 7, 'aak', NULL, NULL),
(599, 'RSTJ', 'Integrasi Penelitian dan PkM dalam pembelajaran.\n\nHasil Penelitian dan Pengabdian kepada Masyarakat (PkM) yang dijadikan sebagai bahan ajar minimal 10% dari mata kuliah inti Program Studi.', 7, 'aak', NULL, NULL),
(600, 'RSTJ', 'Pembelajaran yang dilaksanakan dalam bentuk penugasan, praktikum, praktik bengkel, atau praktik lapangan.', 7, 'aak', NULL, NULL),
(601, 'RSTJ', 'Basic Sciences dan Matematika\nKetersediaan mata kuliah basic sciences dan matematika.', 7, 'aak', NULL, NULL),
(602, 'RSTJ', 'Proyek rekayasa penciri bidang prodi (Capstone design)\n\nTerselenggaranya capstone design yang memiliki:\n(1) Panduan pelaksanaan.\n(2) Memiliki rumusan capaian pembelajaran mata kuliah.\n(3) Menggunakan standar-standar keteknikan dan batasan-batasan realistis berdasarkan pada pengetahuan dan keterampilan yang telah diperoleh di perkuliahan sebelumnya.\n(4) Mempunyai bukti sahih pelaksanaan.', 7, 'aak', NULL, NULL),
(603, 'RSTJ', 'Suasana Akademik\n\nI. Pengelolaan suasana akademik\nII. Integritas dan kebebasan ilmiah: Kebebasan akademik, mimbar akademik dan otonomi keilmuan', 7, 'aak', NULL, NULL),
(604, 'RSTJ', 'Penelitian\n\nKesesuaian penelitian dalam mendukung VMTS UPPS dan visi keilmuan program studi yang mencakup unsur-unsur sebagai berikut: (1) UPPS memiliki peta jalan penelitian yang yang mendukung VMTS UPPS dan visi keilmuan  program studi: (2) Peta jalan memayungi tema penelitian dosen dan mahasiswa dalam mendukung pengembangan kapasitas dosen dan mahasiswa; (3) Melakukan evaluasi secara berkala untuk memastikan keselarasan dengan visi;  (4) memberikan dampak positif bagi masyarakat.', 7, 'aak', NULL, NULL),
(605, 'RSTJ', 'Penelitian DTPS yang sesuai dengan peta jalan penelitian dan  pelaksanaannya melibatkan mahasiswa program studi dalam 3 tahun terakhir.', 7, 'aak', NULL, NULL),
(606, 'RSTJ', 'PkM\n\nKesesuaian PkM dalam mendukung VMTS UPPS dan visi keilmuan program studi yang mencakup unsur-unsur sebagai berikut: \n(1) UPPS memiliki peta jalan PkM yang yang mendukung VMTS UPPS dan visi keilmuan program studi; \n(2) Peta jalan yang memayungi tema PkM dosen dan mahasiswa dalam mendukung pengembangan kapasitas dosen dan mahasiswa; \n(3) Melakukan evaluasi secara berkala untuk memastikan keselarasan dengan visi; \n(4) memberikan dampak positif bagi masyarakat.', 3, 'aak', NULL, NULL),
(607, 'RSTJ', 'PkM DTPS yang sesuai dengan peta jalan PkM dan pelaksanaannya melibatkan mahasiswa program studi dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(608, 'RSTJ', 'Profil Dosen\n\nKecukupan jumlah DTPS.', 3, 'aak', NULL, NULL),
(609, 'RSTJ', 'Kualifikasi akademik DTPS.', 3, 'aak', NULL, NULL),
(610, 'RSTJ', 'Jabatan akademik DTPS.', 3, 'aak', NULL, NULL),
(611, 'RSTJ', 'Sertifikasi kompetensi/profesi/industri DTPS', 3, 'aak', NULL, NULL),
(612, 'RSTJ', 'Keterlibatan dosen industri/praktisi.', 3, 'aak', NULL, NULL),
(613, 'RSTJ', 'Tenaga Kependidikan\n\nKualifikasi dan kecukupan laboran/teknisi/administrator sistem untuk mendukung proses pembelajaran sesuai dengan kebutuhan program studi.', 3, 'aak', NULL, NULL),
(614, 'RSTJ', 'Beban kerja DTPS\n\nRerata Beban Kerja (RBK) DTPS.', 3, 'kuk', NULL, NULL),
(615, 'RSTJ', 'Kegiatan penelitian DTPS yang mendukung visi UPPS dan visi keilmuan program studi dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(616, 'RSTJ', 'Kegiatan PkM DTPS yang mendukung visi UPPS dan visi keilmuan program studi dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(617, 'RSTJ', 'Pagelaran/pameran/presentasi/publikasi ilmiah dengan tema yang mendukung visi keilmuan program studi yang dihasilkan DTPS dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(618, 'RSTJ', 'Luaran penelitian dan PkM yang mendukung visi UPPS dan visi keilmuan program studi yang dihasilkan DTPS dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(619, 'RSTJ', 'Produk/jasa yang diadopsi oleh industri/masyarakat terhadap jumlah dosen tetap dalam 3 tahun  terakhir.', 3, 'aak', NULL, NULL),
(620, 'RSTJ', 'Persentase DTPS yang memiliki karya ilmiah sebagai penulis pertama dan/atau penulis korespondensi dalam mendukung keunggulan kompetitif UPPS dan Program studi.', 3, 'aak', NULL, NULL),
(621, 'RSTJ', 'Persentase Karya ilmiah Bereputasi (PKIB) DTPS pada jurnal bereputasi atau publikasi dalam prosiding internasional ber-ISSN/ISBN terindeks Scopus/IEEE Explore/SPIE yang disitasi dalam 3 tahun terakhir.', 5, 'aak', NULL, NULL),
(622, 'RSTJ', 'Persentase DTPS yang memiliki pengakuan/rekognisi sesuai bidang ilmu', 5, 'aak', NULL, NULL),
(623, 'RSTJ', 'Sarana dan Prasarana\n\nI. Kecukupan dan mutu sarana dan prasarana untuk mendukung kegiatan akademik\n\nII. Kecukupan dan mutu sarana dan prasarana untuk mendukung kegiatan non akademik', 6, 'kuk', NULL, NULL),
(624, 'RSTJ', 'Keselamatan Kesehatan Kerja dan Lingkungan (K3L)\n\nKeselamatan Kesehatan Kerja dan Lingkungan (K3L) yang meliputi: \n(1) UPPS memiliki kebijakan dan tata kelola K3L yang mencakup komitmen untuk memenuhi peraturan K3L; \n(2) Fasilitas K3L; \n(3) Bukti sahih pelaksanaan K3L; dan \n(4) Tinjauan secara berkala K3L dan pelaksanaannya.', 6, 'kuk', NULL, NULL),
(625, 'RSTJ', 'Rasio jumlah mahasiswa program studi terhadap jumlah DTPS.', 6, 'aak', NULL, NULL),
(626, 'RSTJ', 'Mahasiswa Asing\n\nPersentase Mahasiswa Asing (PMA).', 6, 'aak', NULL, NULL),
(627, 'RSTJ', 'IPK lulusan.', 6, 'aak', NULL, NULL),
(628, 'RSTJ', 'I. Prestasi mahasiswa di bidang akademik dalam 5 tahun terakhir.\n\nII. Prestasi mahasiswa di bidang non akademik dalam 5 tahun terakhir.', 6, 'aak', NULL, NULL),
(629, 'RSTJ', 'Produk/jasa karya mahasiswa, yang dihasilkan secara mandiri atau bersama DTPS, yang diadopsi oleh industri/masyarakat dalam 3 tahun terakhir.', 6, 'aak', NULL, NULL),
(630, 'RSTJ', 'Masa studi.', 6, 'aak', NULL, NULL),
(631, 'RSTJ', 'Persentase Kelulusan tepat waktu (PTW)', 6, 'aak', NULL, NULL),
(632, 'RSTJ', 'Pagelaran / pameran / presentasi / publikasi ilmiah mahasiswa, yang dihasilkan secara mandiri atau bersama DTPS dalam 3 tahun terakhir.', 6, 'aak', NULL, NULL),
(633, 'RSTJ', 'Luaran penelitian dan PkM yang dihasilkan mahasiswa, baik secara mandiri atau bersama DTPS dalam 3 tahun terakhir.', 6, 'aak', NULL, NULL),
(634, 'RSTJ', 'Tracer Study\n\nPelaksanaan tracer study yang mencakup 5 aspek sebagai berikut: \n1) pelaksanaan tracer study terkoordinasi di tingkat PT,\n2) kegiatan tracer study dilakukan secara reguler setiap tahun dan terdokumentasi,\n3) isi kuesioner mencakup seluruh pertanyaan inti tracer study DIKTI.\n4) ditargetkan pada seluruh populasi (lulusan TS-4 s.d. TS-2),\n5) hasilnya disosialisasikan dan digunakan untuk pengembangan kurikulum dan pembelajaran.', 6, 'aak', NULL, NULL),
(635, 'RSTJ', 'Waktu Tunggu', 6, 'aak', NULL, NULL),
(636, 'RSTJ', 'Kesesuaian bidang kerja', 6, 'aak', NULL, NULL),
(637, 'RSTJ', 'Tingkat dan ukuran tempat kerja lulusan\n\nTingkat dan ukuran tempat kerja lulusan di tingkat internasional, nasional, dan wilayah/lokal.', 6, 'aak', NULL, NULL),
(638, 'RSTJ', 'Tingkat kepuasan pengguna lulusan.', 4, 'aak', NULL, NULL),
(639, 'RSTJ', 'Keberadaan unit penjaminan mutu dan komitmen pimpinan\n\nI. Keberadaan unit penjaminan mutu UPPS dan komitmen pimpinan dengan keberadaan 4 aspek: \n(1) Dokumen legal pembentukan unsur pelaksana penjaminan mutu; \n(2) Dokumen legal bahwa auditor bersifat independen; \n(3) Dokumen pelaksanaan audit mutu internal; \n(4) Dokumen Rapat Tinjauan Manajemen (RTM).\n\nII. Ketersediaan perangkat SPMI yang minimal mencakup: \n1. Kebijakan SPMI; \n2. Pedoman penerapan siklus PPEPP standar pendidikan tinggi dalam SPMI; \n3. Standar dan/atau kriteria, norma, acuan mutu penyelenggaraan pendidikan dan pengelolaan perguruan tinggi; dan \n4. Tata cara pendokumentasian implementasi SPMI, serta sistem penjaminan mutu memiliki pengakuan mutu dari lembaga audit eksternal, lembaga akreditasi, dan lembaga sertifikasi. \n\nTabel 7.a. LKPS.', 4, 'kuk', NULL, NULL),
(640, 'RSTJ', 'Indikator Kinerja Tambahan (IKT)\n\nIKT disusun sesuai dengan unsur: \n(1) Tujuan strategis organisasi; \n(2) Memberikan dampak positif dan terukur; \n(3) Menunjukkan daya saing internasional; \n(4) Telah diukur dan dianalisis untuk perbaikan UPPS dan Program studi.', 4, 'kuk', NULL, NULL),
(641, 'RSTJ', 'Keterlaksanaan Penjaminan Mutu dan Audit Mutu Internal\n\nKeterlaksanaan Sistem Penjaminan Mutu Internal (SPMI) yang memenuhi aspek berikut: \n(1) Tersedianya dokumen IKU dan IKT Pendidikan, Penelitian dan PkM; \n(2)Terlaksananya siklus penjaminan mutu (siklus PPEPP); \n(3) Bukti sahih efektivitas pelaksanaan penjaminan mutu; \n(4)Tersedianya bukti peningkatan standar.', 4, 'kuk', NULL, NULL),
(642, 'RSTJ', 'Evaluasi Capaian Kinerja\n\nAnalisis ketercapaian atau ketidaktercapaian kinerja UPPS pada budaya, relevansi, akuntabilitas, dan diferensiasi misi yang memenuhi aspek: \n(1) Penggunaan metode yang tepat dalam mengukur kinerja; \n(2) Evaluasi indikator yang tidak tercapai dengan mencari akar masalah dan faktor pendukung ketercapaian; \n(3) Dilakukan proses tinjauan rutin hasil pengukuran kinerja; \n(4) Hasil pengukuran kinerja disebarluaskan kepada pemangku kepentingan.', 4, 'kuk', NULL, NULL),
(643, 'RSTJ', 'Kepuasan Pemangku Kepentingan\n\nPengukuran kepuasan para pemangku kepentingan (mahasiswa, dosen, tenaga kependidikan, lulusan, pengguna, mitra industri, dan mitra lainnya) terhadap layanan manajemen, yang memenuhi aspek-aspek berikut: \n(1) Menggunakan instrumen kepuasan yang sahih, andal, mudah digunakan; \n(2) Dilaksanakan secara berkala, serta datanya terekam secara komprehensif; \n(3) Dianalisis dengan metode yang tepat serta bermanfaat untuk pengambilan keputusan; \n(4) Tingkat kepuasan dan umpan balik ditindaklanjuti untuk perbaikan dan peningkatan mutu luaran secara berkala dan tersistem; \n(5) Dilakukan review terhadap pelaksanaan pengukuran kepuasan dosen dan mahasiswa, serta\n(6) Hasilnya dipublikasikan dan mudah diakses oleh dosen dan mahasiswa.', 4, 'kuk', NULL, NULL),
(644, 'RSTJ', 'Analisis Lingkungan Eksternal dalam Pengembangan UPPS dan Prodi serta analisis SWOT\n\nI. Analisis lingkungan eksternal dalam pengembangan UPPS dan Program Studi.\n\nII. Ketepatan analisis SWOT yang mengacu pada lingkungan eksternal dan analisis SWOT setiap kriteria.', NULL, 'kuk', NULL, NULL),
(645, 'RSTJ', 'Tujuan Strategis Pengembangan\n\nKetepatan di dalam menetapkan tujuan strategis pengembangan.', NULL, 'kuk', NULL, NULL),
(646, 'RSTJ', 'Program Pengembangan Berkelanjutan\n\nUPPS memiliki kebijakan, ketersediaan sumber daya, kemampuan melaksanakan, dan kerealistikan program pengembangan berkelanjutan.', NULL, 'kuk', NULL, NULL),
(647, 'TRO', 'Kekhasan VMTS\n\nPernyataan VMTS yang unik dan spesifik sebagai identitas PT, UPPS, dan visi keilmuan program studi sebagai keunggulan kompetitif yang didukung dengan renstra dan kurikulum yang memadai.', 1, 'aak', NULL, NULL),
(648, 'TRO', 'Mekanisme penyusunan VMTS\n\nMekanisme dan keterlibatan pemangku kepentingan dalam penyusunan VMTS UPPS dan tujuan utama yang ingin dicapai dalam penyusunan visi keilmuan program studi  dengan mempertimbangkan kebutuhan masyarakat dan tantangan global.', 1, 'kuk', NULL, NULL),
(649, 'TRO', 'Tingkat pemahaman dan pencapaian VMTS\n\nTingkat pemahaman dan pencapaian VMTS UPPS dan visi keilmuan program studi oleh seluruh pemangku kepentingan internal dan eksternal serta pencapaian konkret  jangka pendek dan jangka menengah yang telah ditetapkan dalam VMTS UPPS dan visi keilmuan program studi.', 1, 'kuk', NULL, NULL),
(650, 'TRO', 'Sistem Tata Pamong\n\nI. Kelengkapan struktur organisasi dan kebijakan operasional yang berpedoman pada statuta Perguruan Tinggi yang digunakan.\n\nII. Perwujudan Good University Governance mengacu pada sistem tata kelola yang efektif, transparan, dan akuntabel.', 2, 'aak', NULL, NULL),
(651, 'TRO', 'Komitmen pimpinan dan kemampuan manajerial \n\nI. Pimpinan UPPS memiliki komitmen pada: \n(1) Visi dan tujuan organisasi; \n(2) Integritas dan transparansi; \n(3) Pengembangan sumber daya. \n\nII. Kemampuan manajerial pimpinan UPPS', 2, 'kuk', NULL, NULL),
(652, 'TRO', 'Kerja sama\n\nI. Relevansi kerja sama pendidikan, penelitian, dan PkM dengan visi UPPS serta visi keilmuan program studi.\n\nII. Kerja sama tingkat internasional, nasional, wilayah/lokal yang relevan dengan program studi dan dikelola oleh UPPS dalam 3 tahun terakhir.', 2, 'kuk', NULL, NULL),
(653, 'TRO', 'Pelaksanaan Kerja Sama\n\nUPPS memiliki bukti yang sahih terkait kerja sama yang telah memenuhi 3 aspek berikut: \n(1) Memberikan manfaat bagi program studi dalam pemenuhan proses pembelajaran, penelitian, PkM; \n(2) Memberikan peningkatan kinerja tridharma dan fasilitas pendukung program studi; dan \n(3) Memberikan kepuasan kepada mitra industri dan mitra kerja sama lainnya.', 2, 'kuk', NULL, NULL),
(654, 'TRO', 'Pengelolaan keuangan\n\nUPPS memiliki praktik pengelolaan sumber daya keuangan secara efektif dan efisien.', 2, 'kuk', NULL, NULL),
(655, 'TRO', 'Biaya operasional, dana penelitian dan PkM\n\nBiaya Operasional pendidikan', 2, 'kuk', NULL, NULL),
(656, 'TRO', 'Dana penelitian DTPS.', 7, 'kuk', NULL, NULL),
(657, 'TRO', 'Dana pengabdian kepada masyarakat', 7, 'kuk', NULL, NULL),
(658, 'TRO', 'Pemutakhiran kurikulum\n\nKeterlibatan pemangku kepentingan dalam proses evaluasi dan pemutakhiran kurikulum.', 7, 'aak', NULL, NULL),
(659, 'TRO', 'Profil lulusan dan CPL.\n\nI. Profil lulusan yang ditetapkan oleh Program Studi.\n\nII. Kesesuaian Profil lulusan dengan capaian pembelajaran (CPL)', 7, 'aak', NULL, NULL),
(660, 'TRO', 'Kesesuaian dan tinjauan CPL\n\nI. Kesesuaian CPL dengan standar kompetensi lulusan yang  mencakup: (1) Konsep  rekayasa terapan yang spesifik dengan disiplin ilmu terkait; (2) kemampuan teknis dan kemampuan beradaptasi dengan standar keteknikan dan Teknologi Baru; (3) Keterampilan komunikasi dan kemampuan kerja tim; (4) kepatuhan terhadap etika profesi.\n\nII. Proses tinjauan rutin CPL.', 7, 'aak', NULL, NULL),
(661, 'TRO', 'Rencana Proses Pembelajaran (RPS)\n\nI. Ketersediaan dan kelengkapan dokumen RPS yang terdiri dari:\n1. Nama program studi, nama dan kode mata kuliah, semester, sks, nama dosen pengampu;\n2. Capaian pembelajaran lulusan yang dibebankan pada capaian pembelajaran mata kuliah;\n3. Kemampuan akhir yang direncanakan pada tiap tahap pembelajaran untuk memenuhi capaian pembelajaran lulusan;\n4. Bahan kajian yang terkait dengan kemampuan yang akan dicapai;\n5. Metode pembelajaran;\n6. Waktu yang disediakan untuk mencapai kemampuan pada tiap tahap pembelajaran;\n7. Pengalaman belajar mahasiswa yang diwujudkan dalam deskripsi tugas yang harus dikerjakan oleh mahasiswa selama satu semester;\n8. Kriteria, indikator, dan bobot penilaian; dan\n9. Daftar referensi yang digunakan. \n\nII. Proses tinjauan rutin RPS', 7, 'aak', NULL, NULL),
(662, 'TRO', 'Proses Pembelajaran (PS)\n\nI. Proses pembelajaran untuk memastikan efektivitas, kualitas, dan keberhasilan pencapaian CPL.\n\nII. Tinjauan rutin proses pembelajaran.', 7, 'aak', NULL, NULL),
(663, 'TRO', 'Integrasi Penelitian dan PkM dalam pembelajaran.\n\nHasil Penelitian dan Pengabdian kepada Masyarakat (PkM) yang dijadikan sebagai bahan ajar minimal 10% dari mata kuliah inti Program Studi.', 7, 'aak', NULL, NULL),
(664, 'TRO', 'Pembelajaran yang dilaksanakan dalam bentuk penugasan, praktikum, praktik bengkel, atau praktik lapangan.', 7, 'aak', NULL, NULL),
(665, 'TRO', 'Basic Sciences dan Matematika\nKetersediaan mata kuliah basic sciences dan matematika.', 7, 'aak', NULL, NULL),
(666, 'TRO', 'Proyek rekayasa penciri bidang prodi (Capstone design)\n\nTerselenggaranya capstone design yang memiliki:\n(1) Panduan pelaksanaan.\n(2) Memiliki rumusan capaian pembelajaran mata kuliah.\n(3) Menggunakan standar-standar keteknikan dan batasan-batasan realistis berdasarkan pada pengetahuan dan keterampilan yang telah diperoleh di perkuliahan sebelumnya.\n(4) Mempunyai bukti sahih pelaksanaan.', 7, 'aak', NULL, NULL),
(667, 'TRO', 'Suasana Akademik\n\nI. Pengelolaan suasana akademik\nII. Integritas dan kebebasan ilmiah: Kebebasan akademik, mimbar akademik dan otonomi keilmuan', 7, 'aak', NULL, NULL),
(668, 'TRO', 'Penelitian\n\nKesesuaian penelitian dalam mendukung VMTS UPPS dan visi keilmuan program studi yang mencakup unsur-unsur sebagai berikut: (1) UPPS memiliki peta jalan penelitian yang yang mendukung VMTS UPPS dan visi keilmuan  program studi: (2) Peta jalan memayungi tema penelitian dosen dan mahasiswa dalam mendukung pengembangan kapasitas dosen dan mahasiswa; (3) Melakukan evaluasi secara berkala untuk memastikan keselarasan dengan visi;  (4) memberikan dampak positif bagi masyarakat.', 7, 'aak', NULL, NULL),
(669, 'TRO', 'Penelitian DTPS yang sesuai dengan peta jalan penelitian dan  pelaksanaannya melibatkan mahasiswa program studi dalam 3 tahun terakhir.', 7, 'aak', NULL, NULL),
(670, 'TRO', 'PkM\n\nKesesuaian PkM dalam mendukung VMTS UPPS dan visi keilmuan program studi yang mencakup unsur-unsur sebagai berikut: \n(1) UPPS memiliki peta jalan PkM yang yang mendukung VMTS UPPS dan visi keilmuan program studi; \n(2) Peta jalan yang memayungi tema PkM dosen dan mahasiswa dalam mendukung pengembangan kapasitas dosen dan mahasiswa; \n(3) Melakukan evaluasi secara berkala untuk memastikan keselarasan dengan visi; \n(4) memberikan dampak positif bagi masyarakat.', 3, 'aak', NULL, NULL),
(671, 'TRO', 'PkM DTPS yang sesuai dengan peta jalan PkM dan pelaksanaannya melibatkan mahasiswa program studi dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(672, 'TRO', 'Profil Dosen\n\nKecukupan jumlah DTPS.', 3, 'aak', NULL, NULL),
(673, 'TRO', 'Kualifikasi akademik DTPS.', 3, 'aak', NULL, NULL),
(674, 'TRO', 'Jabatan akademik DTPS.', 3, 'aak', NULL, NULL),
(675, 'TRO', 'Sertifikasi kompetensi/profesi/industri DTPS', 3, 'aak', NULL, NULL),
(676, 'TRO', 'Keterlibatan dosen industri/praktisi.', 3, '', NULL, NULL),
(677, 'TRO', 'Tenaga Kependidikan\n\nKualifikasi dan kecukupan laboran/teknisi/administrator sistem untuk mendukung proses pembelajaran sesuai dengan kebutuhan program studi.', 3, 'aak', NULL, NULL),
(678, 'TRO', 'Beban kerja DTPS\n\nRerata Beban Kerja (RBK) DTPS.', 3, 'kuk', NULL, NULL),
(679, 'TRO', 'Kegiatan penelitian DTPS yang mendukung visi UPPS dan visi keilmuan program studi dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(680, 'TRO', 'Kegiatan PkM DTPS yang mendukung visi UPPS dan visi keilmuan program studi dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(681, 'TRO', 'Pagelaran/pameran/presentasi/publikasi ilmiah dengan tema yang mendukung visi keilmuan program studi yang dihasilkan DTPS dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(682, 'TRO', 'Luaran penelitian dan PkM yang mendukung visi UPPS dan visi keilmuan program studi yang dihasilkan DTPS dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(683, 'TRO', 'Produk/jasa yang diadopsi oleh industri/masyarakat terhadap jumlah dosen tetap dalam 3 tahun  terakhir.', 3, 'aak', NULL, NULL),
(684, 'TRO', 'Persentase DTPS yang memiliki karya ilmiah sebagai penulis pertama dan/atau penulis korespondensi dalam mendukung keunggulan kompetitif UPPS dan Program studi.', 3, 'aak', NULL, NULL),
(685, 'TRO', 'Persentase Karya ilmiah Bereputasi (PKIB) DTPS pada jurnal bereputasi atau publikasi dalam prosiding internasional ber-ISSN/ISBN terindeks Scopus/IEEE Explore/SPIE yang disitasi dalam 3 tahun terakhir.', 5, 'aak', NULL, NULL),
(686, 'TRO', 'Persentase DTPS yang memiliki pengakuan/rekognisi sesuai bidang ilmu', 5, 'aak', NULL, NULL),
(687, 'TRO', 'Sarana dan Prasarana\n\nI. Kecukupan dan mutu sarana dan prasarana untuk mendukung kegiatan akademik\n\nII. Kecukupan dan mutu sarana dan prasarana untuk mendukung kegiatan non akademik', 6, 'kuk', NULL, NULL),
(688, 'TRO', 'Keselamatan Kesehatan Kerja dan Lingkungan (K3L)\n\nKeselamatan Kesehatan Kerja dan Lingkungan (K3L) yang meliputi: \n(1) UPPS memiliki kebijakan dan tata kelola K3L yang mencakup komitmen untuk memenuhi peraturan K3L; \n(2) Fasilitas K3L; \n(3) Bukti sahih pelaksanaan K3L; dan \n(4) Tinjauan secara berkala K3L dan pelaksanaannya.', 6, 'kuk', NULL, NULL),
(689, 'TRO', 'Rasio jumlah mahasiswa program studi terhadap jumlah DTPS.', 6, 'aak', NULL, NULL),
(690, 'TRO', 'Mahasiswa Asing\n\nPersentase Mahasiswa Asing (PMA).', 6, 'aak', NULL, NULL),
(691, 'TRO', 'IPK lulusan.', 6, 'aak', NULL, NULL),
(692, 'TRO', 'I. Prestasi mahasiswa di bidang akademik dalam 5 tahun terakhir.\n\nII. Prestasi mahasiswa di bidang non akademik dalam 5 tahun terakhir.', 6, 'aak', NULL, NULL),
(693, 'TRO', 'Produk/jasa karya mahasiswa, yang dihasilkan secara mandiri atau bersama DTPS, yang diadopsi oleh industri/masyarakat dalam 3 tahun terakhir.', 6, 'aak', NULL, NULL),
(694, 'TRO', 'Masa studi.', 6, 'aak', NULL, NULL),
(695, 'TRO', 'Persentase Kelulusan tepat waktu (PTW)', 6, 'aak', NULL, NULL),
(696, 'TRO', 'Pagelaran / pameran / presentasi / publikasi ilmiah mahasiswa, yang dihasilkan secara mandiri atau bersama DTPS dalam 3 tahun terakhir.', 6, 'aak', NULL, NULL),
(697, 'TRO', 'Luaran penelitian dan PkM yang dihasilkan mahasiswa, baik secara mandiri atau bersama DTPS dalam 3 tahun terakhir.', 6, 'aak', NULL, NULL),
(698, 'TRO', 'Tracer Study\n\nPelaksanaan tracer study yang mencakup 5 aspek sebagai berikut: \n1) pelaksanaan tracer study terkoordinasi di tingkat PT,\n2) kegiatan tracer study dilakukan secara reguler setiap tahun dan terdokumentasi,\n3) isi kuesioner mencakup seluruh pertanyaan inti tracer study DIKTI.\n4) ditargetkan pada seluruh populasi (lulusan TS-4 s.d. TS-2),\n5) hasilnya disosialisasikan dan digunakan untuk pengembangan kurikulum dan pembelajaran.', 6, 'aak', NULL, NULL),
(699, 'TRO', 'Waktu Tunggu', 6, 'aak', NULL, NULL),
(700, 'TRO', 'Kesesuaian bidang kerja', 6, 'aak', NULL, NULL),
(701, 'TRO', 'Tingkat dan ukuran tempat kerja lulusan\n\nTingkat dan ukuran tempat kerja lulusan di tingkat internasional, nasional, dan wilayah/lokal.', 6, 'aak', NULL, NULL),
(702, 'TRO', 'Tingkat kepuasan pengguna lulusan.', 4, 'aak', NULL, NULL),
(703, 'TRO', 'Keberadaan unit penjaminan mutu dan komitmen pimpinan\n\nI. Keberadaan unit penjaminan mutu UPPS dan komitmen pimpinan dengan keberadaan 4 aspek: \n(1) Dokumen legal pembentukan unsur pelaksana penjaminan mutu; \n(2) Dokumen legal bahwa auditor bersifat independen; \n(3) Dokumen pelaksanaan audit mutu internal; \n(4) Dokumen Rapat Tinjauan Manajemen (RTM).\n\nII. Ketersediaan perangkat SPMI yang minimal mencakup: \n1. Kebijakan SPMI; \n2. Pedoman penerapan siklus PPEPP standar pendidikan tinggi dalam SPMI; \n3. Standar dan/atau kriteria, norma, acuan mutu penyelenggaraan pendidikan dan pengelolaan perguruan tinggi; dan \n4. Tata cara pendokumentasian implementasi SPMI, serta sistem penjaminan mutu memiliki pengakuan mutu dari lembaga audit eksternal, lembaga akreditasi, dan lembaga sertifikasi. \n\nTabel 7.a. LKPS.', 4, 'kuk', NULL, NULL),
(704, 'TRO', 'Indikator Kinerja Tambahan (IKT)\n\nIKT disusun sesuai dengan unsur: \n(1) Tujuan strategis organisasi; \n(2) Memberikan dampak positif dan terukur; \n(3) Menunjukkan daya saing internasional; \n(4) Telah diukur dan dianalisis untuk perbaikan UPPS dan Program studi.', 4, 'kuk', NULL, NULL),
(705, 'TRO', 'Keterlaksanaan Penjaminan Mutu dan Audit Mutu Internal\n\nKeterlaksanaan Sistem Penjaminan Mutu Internal (SPMI) yang memenuhi aspek berikut: \n(1) Tersedianya dokumen IKU dan IKT Pendidikan, Penelitian dan PkM; \n(2)Terlaksananya siklus penjaminan mutu (siklus PPEPP); \n(3) Bukti sahih efektivitas pelaksanaan penjaminan mutu; \n(4)Tersedianya bukti peningkatan standar.', 4, 'kuk', NULL, NULL),
(706, 'TRO', 'Evaluasi Capaian Kinerja\n\nAnalisis ketercapaian atau ketidaktercapaian kinerja UPPS pada budaya, relevansi, akuntabilitas, dan diferensiasi misi yang memenuhi aspek: \n(1) Penggunaan metode yang tepat dalam mengukur kinerja; \n(2) Evaluasi indikator yang tidak tercapai dengan mencari akar masalah dan faktor pendukung ketercapaian; \n(3) Dilakukan proses tinjauan rutin hasil pengukuran kinerja; \n(4) Hasil pengukuran kinerja disebarluaskan kepada pemangku kepentingan.', 4, 'kuk', NULL, NULL),
(707, 'TRO', 'Kepuasan Pemangku Kepentingan\n\nPengukuran kepuasan para pemangku kepentingan (mahasiswa, dosen, tenaga kependidikan, lulusan, pengguna, mitra industri, dan mitra lainnya) terhadap layanan manajemen, yang memenuhi aspek-aspek berikut: \n(1) Menggunakan instrumen kepuasan yang sahih, andal, mudah digunakan; \n(2) Dilaksanakan secara berkala, serta datanya terekam secara komprehensif; \n(3) Dianalisis dengan metode yang tepat serta bermanfaat untuk pengambilan keputusan; \n(4) Tingkat kepuasan dan umpan balik ditindaklanjuti untuk perbaikan dan peningkatan mutu luaran secara berkala dan tersistem; \n(5) Dilakukan review terhadap pelaksanaan pengukuran kepuasan dosen dan mahasiswa, serta\n(6) Hasilnya dipublikasikan dan mudah diakses oleh dosen dan mahasiswa.', 4, 'kuk', NULL, NULL),
(708, 'TRO', 'Analisis Lingkungan Eksternal dalam Pengembangan UPPS dan Prodi serta analisis SWOT\n\nI. Analisis lingkungan eksternal dalam pengembangan UPPS dan Program Studi.\n\nII. Ketepatan analisis SWOT yang mengacu pada lingkungan eksternal dan analisis SWOT setiap kriteria.', NULL, 'kuk', NULL, NULL),
(709, 'TRO', 'Tujuan Strategis Pengembangan\n\nKetepatan di dalam menetapkan tujuan strategis pengembangan.', NULL, 'kuk', NULL, NULL),
(710, 'TRO', 'Program Pengembangan Berkelanjutan\n\nUPPS memiliki kebijakan, ketersediaan sumber daya, kemampuan melaksanakan, dan kerealistikan program pengembangan berkelanjutan.', NULL, 'kuk', NULL, NULL),
(711, 'TO', 'Kekhasan VMTS\n\nPernyataan VMTS yang unik dan spesifik sebagai identitas PT, UPPS, dan visi keilmuan program studi sebagai keunggulan kompetitif yang didukung dengan renstra dan kurikulum yang memadai.', 1, 'aak', NULL, NULL),
(712, 'TO', 'Mekanisme penyusunan VMTS\n\nMekanisme dan keterlibatan pemangku kepentingan dalam penyusunan VMTS UPPS dan tujuan utama yang ingin dicapai dalam penyusunan visi keilmuan program studi  dengan mempertimbangkan kebutuhan masyarakat dan tantangan global.', 1, 'kuk', NULL, NULL),
(713, 'TO', 'Tingkat pemahaman dan pencapaian VMTS\n\nTingkat pemahaman dan pencapaian VMTS UPPS dan visi keilmuan program studi oleh seluruh pemangku kepentingan internal dan eksternal serta pencapaian konkret  jangka pendek dan jangka menengah yang telah ditetapkan dalam VMTS UPPS dan visi keilmuan program studi.', 1, 'kuk', NULL, NULL),
(714, 'TO', 'Sistem Tata Pamong\n\nI. Kelengkapan struktur organisasi dan kebijakan operasional yang berpedoman pada statuta Perguruan Tinggi yang digunakan.\n\nII. Perwujudan Good University Governance mengacu pada sistem tata kelola yang efektif, transparan, dan akuntabel.', 2, 'aak', NULL, NULL),
(715, 'TO', 'Komitmen pimpinan dan kemampuan manajerial \n\nI. Pimpinan UPPS memiliki komitmen pada: \n(1) Visi dan tujuan organisasi; \n(2) Integritas dan transparansi; \n(3) Pengembangan sumber daya. \n\nII. Kemampuan manajerial pimpinan UPPS', 2, 'kuk', NULL, NULL),
(716, 'TO', 'Kerja sama\n\nI. Relevansi kerja sama pendidikan, penelitian, dan PkM dengan visi UPPS serta visi keilmuan program studi.\n\nII. Kerja sama tingkat internasional, nasional, wilayah/lokal yang relevan dengan program studi dan dikelola oleh UPPS dalam 3 tahun terakhir.', 2, 'kuk', NULL, NULL),
(717, 'TO', 'Pelaksanaan Kerja Sama\n\nUPPS memiliki bukti yang sahih terkait kerja sama yang telah memenuhi 3 aspek berikut: \n(1) Memberikan manfaat bagi program studi dalam pemenuhan proses pembelajaran, penelitian, PkM; \n(2) Memberikan peningkatan kinerja tridharma dan fasilitas pendukung program studi; dan \n(3) Memberikan kepuasan kepada mitra industri dan mitra kerja sama lainnya.', 2, 'kuk', NULL, NULL),
(718, 'TO', 'Pengelolaan keuangan\n\nUPPS memiliki praktik pengelolaan sumber daya keuangan secara efektif dan efisien.', 2, 'kuk', NULL, NULL),
(719, 'TO', 'Biaya operasional, dana penelitian dan PkM\n\nBiaya Operasional pendidikan', 2, 'kuk', NULL, NULL),
(720, 'TO', 'Dana penelitian DTPS.', 7, 'kuk', NULL, NULL),
(721, 'TO', 'Dana pengabdian kepada masyarakat', 7, 'kuk', NULL, NULL),
(722, 'TO', 'Pemutakhiran kurikulum\n\nKeterlibatan pemangku kepentingan dalam proses evaluasi dan pemutakhiran kurikulum.', 7, 'aak', NULL, NULL),
(723, 'TO', 'Profil lulusan dan CPL.\n\nI. Profil lulusan yang ditetapkan oleh Program Studi.\n\nII. Kesesuaian Profil lulusan dengan capaian pembelajaran (CPL)', 7, 'aak', NULL, NULL),
(724, 'TO', 'Kesesuaian dan tinjauan CPL\n\nI. Kesesuaian CPL dengan standar kompetensi lulusan yang  mencakup: (1) Konsep  rekayasa terapan yang spesifik dengan disiplin ilmu terkait; (2) kemampuan teknis dan kemampuan beradaptasi dengan standar keteknikan dan Teknologi Baru; (3) Keterampilan komunikasi dan kemampuan kerja tim; (4) kepatuhan terhadap etika profesi.\n\nII. Proses tinjauan rutin CPL.', 7, 'aak', NULL, NULL),
(725, 'TO', 'Rencana Proses Pembelajaran (RPS)\n\nI. Ketersediaan dan kelengkapan dokumen RPS yang terdiri dari:\n1. Nama program studi, nama dan kode mata kuliah, semester, sks, nama dosen pengampu;\n2. Capaian pembelajaran lulusan yang dibebankan pada capaian pembelajaran mata kuliah;\n3. Kemampuan akhir yang direncanakan pada tiap tahap pembelajaran untuk memenuhi capaian pembelajaran lulusan;\n4. Bahan kajian yang terkait dengan kemampuan yang akan dicapai;\n5. Metode pembelajaran;\n6. Waktu yang disediakan untuk mencapai kemampuan pada tiap tahap pembelajaran;\n7. Pengalaman belajar mahasiswa yang diwujudkan dalam deskripsi tugas yang harus dikerjakan oleh mahasiswa selama satu semester;\n8. Kriteria, indikator, dan bobot penilaian; dan\n9. Daftar referensi yang digunakan. \n\nII. Proses tinjauan rutin RPS', 7, 'aak', NULL, NULL),
(726, 'TO', 'Proses Pembelajaran (RPS)\n\nI. Proses pembelajaran untuk memastikan efektivitas, kualitas, dan keberhasilan pencapaian CPL.\n\nII. Tinjauan rutin proses pembelajaran.', 7, 'aak', NULL, NULL),
(727, 'TO', 'Integrasi Penelitian dan PkM dalam pembelajaran.\n\nHasil Penelitian dan Pengabdian kepada Masyarakat (PkM) yang dijadikan sebagai bahan ajar minimal 10% dari mata kuliah inti Program Studi.', 7, 'aak', NULL, NULL),
(728, 'TO', 'Pembelajaran yang dilaksanakan dalam bentuk penugasan, praktikum, praktik bengkel, atau praktik lapangan.', 7, 'aak', NULL, NULL),
(729, 'TO', 'Suasana Akademik\n\nPengelolaan suasana akademik', 7, 'aak', NULL, NULL),
(730, 'TO', 'Penelitian\n\nKesesuaian penelitian dalam mendukung VMTS UPPS dan visi keilmuan program studi yang mencakup unsur-unsur sebagai berikut: (1) UPPS memiliki peta jalan penelitian yang yang mendukung VMTS UPPS dan visi keilmuan  program studi: (2) Peta jalan memayungi tema penelitian dosen dan mahasiswa dalam mendukung pengembangan kapasitas dosen dan mahasiswa; (3) Melakukan evaluasi secara berkala untuk memastikan keselarasan dengan visi;  (4) memberikan dampak positif bagi masyarakat.', 7, 'aak', NULL, NULL),
(731, 'TO', 'PkM\n\nKesesuaian PkM dalam mendukung VMTS UPPS dan visi keilmuan program studi yang mencakup unsur-unsur sebagai berikut: \n(1) UPPS memiliki peta jalan PkM yang yang mendukung VMTS UPPS dan visi keilmuan program studi; \n(2) Peta jalan yang memayungi tema PkM dosen dan mahasiswa dalam mendukung pengembangan kapasitas dosen dan mahasiswa; \n(3) Melakukan evaluasi secara berkala untuk memastikan keselarasan dengan visi; \n(4) memberikan dampak positif bagi masyarakat.', 3, 'aak', NULL, NULL),
(732, 'TO', 'PkM DTPS yang sesuai dengan peta jalan PkM dan pelaksanaannya melibatkan mahasiswa program studi dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(733, 'TO', 'Profil Dosen\n\nKecukupan jumlah DTPS.', 3, 'aak', NULL, NULL),
(734, 'TO', 'Kualifikasi akademik DTPS.', 3, 'aak', NULL, NULL),
(735, 'TO', 'Jabatan akademik DTPS.', 3, 'aak', NULL, NULL),
(736, 'TO', 'Sertifikasi kompetensi/profesi/industri DTPS', 3, 'aak', NULL, NULL),
(737, 'TO', 'Keterlibatan dosen industri/praktisi.', 3, 'aak', NULL, NULL),
(738, 'TO', 'Tenaga Kependidikan\n\nKualifikasi dan kecukupan laboran/teknisi/administrator sistem untuk mendukung proses pembelajaran sesuai dengan kebutuhan program studi.', 3, 'aak', NULL, NULL),
(739, 'TO', 'Beban kerja DTPS\n\nRerata Beban Kerja (RBK) DTPS.', 3, 'kuk', NULL, NULL),
(740, 'TO', 'Kegiatan penelitian DTPS yang mendukung visi UPPS dan visi keilmuan program studi dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(741, 'TO', 'Kegiatan PkM DTPS yang mendukung visi UPPS dan visi keilmuan program studi dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(742, 'TO', 'Pagelaran/pameran/presentasi/publikasi ilmiah dengan tema yang mendukung visi keilmuan program studi yang dihasilkan DTPS dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(743, 'TO', 'Luaran penelitian dan PkM yang mendukung visi UPPS dan visi keilmuan program studi yang dihasilkan DTPS dalam 3 tahun terakhir.', 3, 'aak', NULL, NULL),
(744, 'TO', 'Produk/jasa yang diadopsi oleh industri/masyarakat terhadap jumlah dosen tetap dalam 3 tahun  terakhir.', 3, 'aak', NULL, NULL),
(745, 'TO', 'Persentase DTPS yang memiliki pengakuan/rekognisi sesuai bidang ilmu', 5, 'aak', NULL, NULL),
(746, 'TO', 'Sarana dan Prasarana\n\nI. Kecukupan dan mutu sarana dan prasarana untuk mendukung kegiatan akademik\n\nII. Kecukupan dan mutu sarana dan prasarana untuk mendukung kegiatan non akademik', 6, 'kuk', NULL, NULL),
(747, 'TO', 'Keselamatan Kesehatan Kerja dan Lingkungan (K3L)\n\nKeselamatan Kesehatan Kerja dan Lingkungan (K3L) yang meliputi: \n(1) UPPS memiliki kebijakan dan tata kelola K3L yang mencakup komitmen untuk memenuhi peraturan K3L; \n(2) Fasilitas K3L; \n(3) Bukti sahih pelaksanaan K3L; dan \n(4) Tinjauan secara berkala K3L dan pelaksanaannya.', 6, 'kuk', NULL, NULL),
(748, 'TO', 'Rasio jumlah mahasiswa program studi terhadap jumlah DTPS.', 6, 'aak', NULL, NULL),
(749, 'TO', 'IPK lulusan.', 6, 'aak', NULL, NULL),
(750, 'TO', 'I. Prestasi mahasiswa di bidang akademik dalam 5 tahun terakhir.\n\nII. Prestasi mahasiswa di bidang non akademik dalam 5 tahun terakhir.', 6, 'aak', NULL, NULL),
(751, 'TO', 'Produk/jasa karya mahasiswa, yang dihasilkan secara mandiri atau bersama DTPS, yang diadopsi oleh industri/masyarakat dalam 3 tahun terakhir.', 6, 'aak', NULL, NULL),
(752, 'TO', 'Masa studi.', 6, 'aak', NULL, NULL),
(753, 'TO', 'Persentase Kelulusan tepat waktu (PTW)', 6, 'aak', NULL, NULL),
(754, 'TO', 'Tracer Study\n\nPelaksanaan tracer study yang mencakup 5 aspek sebagai berikut: \n1) pelaksanaan tracer study terkoordinasi di tingkat PT,\n2) kegiatan tracer study dilakukan secara reguler setiap tahun dan terdokumentasi,\n3) isi kuesioner mencakup seluruh pertanyaan inti tracer study DIKTI.\n4) ditargetkan pada seluruh populasi (lulusan TS-4 s.d. TS-2),\n5) hasilnya disosialisasikan dan digunakan untuk pengembangan kurikulum dan pembelajaran.', 6, 'aak', NULL, NULL),
(755, 'TO', 'Waktu Tunggu', 6, 'aak', NULL, NULL),
(756, 'TO', 'Kesesuaian bidang kerja', 6, 'aak', NULL, NULL),
(757, 'TO', 'Tingkat dan ukuran tempat kerja lulusan\n\nTingkat dan ukuran tempat kerja lulusan di tingkat internasional, nasional, dan wilayah/lokal.', 6, 'aak', NULL, NULL),
(758, 'TO', 'Tingkat kepuasan pengguna lulusan.', 4, 'aak', NULL, NULL),
(759, 'TO', 'Keberadaan unit penjaminan mutu dan komitmen pimpinan\n\nI. Keberadaan unit penjaminan mutu UPPS dan komitmen pimpinan dengan keberadaan 4 aspek: \n(1) Dokumen legal pembentukan unsur pelaksana penjaminan mutu; \n(2) Dokumen legal bahwa auditor bersifat independen; \n(3) Dokumen pelaksanaan audit mutu internal; \n(4) Dokumen Rapat Tinjauan Manajemen (RTM).\n\nII. Ketersediaan perangkat SPMI yang minimal mencakup: \n1. Kebijakan SPMI; \n2. Pedoman penerapan siklus PPEPP standar pendidikan tinggi dalam SPMI; \n3. Standar dan/atau kriteria, norma, acuan mutu penyelenggaraan pendidikan dan pengelolaan perguruan tinggi; dan \n4. Tata cara pendokumentasian implementasi SPMI, serta sistem penjaminan mutu memiliki pengakuan mutu dari lembaga audit eksternal, lembaga akreditasi, dan lembaga sertifikasi.', 4, 'kuk', NULL, NULL),
(760, 'TO', 'Indikator Kinerja Tambahan (IKT)\n\nIKT disusun sesuai dengan unsur: \n(1) Tujuan strategis organisasi; \n(2) Memberikan dampak positif dan terukur; \n(3) Menunjukkan daya saing internasional; \n(4) Telah diukur dan dianalisis untuk perbaikan UPPS dan Program studi.', 4, 'kuk', NULL, NULL),
(761, 'TO', 'Keterlaksanaan Penjaminan Mutu dan Audit Mutu Internal\n\nKeterlaksanaan Sistem Penjaminan Mutu Internal (SPMI) yang memenuhi aspek berikut: \n(1) Tersedianya dokumen IKU dan IKT Pendidikan, Penelitian dan PkM; \n(2)Terlaksananya siklus penjaminan mutu (siklus PPEPP); \n(3) Bukti sahih efektivitas pelaksanaan penjaminan mutu; \n(4)Tersedianya bukti peningkatan standar.', 4, 'kuk', NULL, NULL),
(762, 'TO', 'Evaluasi Capaian Kinerja\n\nAnalisis ketercapaian atau ketidaktercapaian kinerja UPPS pada budaya, relevansi, akuntabilitas, dan diferensiasi misi yang memenuhi aspek: \n(1) Penggunaan metode yang tepat dalam mengukur kinerja; \n(2) Evaluasi indikator yang tidak tercapai dengan mencari akar masalah dan faktor pendukung ketercapaian; \n(3) Dilakukan proses tinjauan rutin hasil pengukuran kinerja; \n(4) Hasil pengukuran kinerja disebarluaskan kepada pemangku kepentingan.', 4, 'kuk', NULL, NULL),
(763, 'TO', 'Kepuasan Pemangku Kepentingan\n\nPengukuran kepuasan para pemangku kepentingan (mahasiswa, dosen, tenaga kependidikan, lulusan, pengguna, mitra industri, dan mitra lainnya) terhadap layanan manajemen, yang memenuhi aspek-aspek berikut: \n(1) Menggunakan instrumen kepuasan yang sahih, andal, mudah digunakan; \n(2) Dilaksanakan secara berkala, serta datanya terekam secara komprehensif; \n(3) Dianalisis dengan metode yang tepat serta bermanfaat untuk pengambilan keputusan; \n(4) Tingkat kepuasan dan umpan balik ditindaklanjuti untuk perbaikan dan peningkatan mutu luaran secara berkala dan tersistem; \n(5) Dilakukan review terhadap pelaksanaan pengukuran kepuasan dosen dan mahasiswa, serta\n(6) Hasilnya dipublikasikan dan mudah diakses oleh dosen dan mahasiswa.', 4, 'kuk', NULL, NULL),
(764, 'TO', 'Analisis Lingkungan Eksternal dalam Pengembangan UPPS dan Prodi serta analisis SWOT\n\nI. Analisis lingkungan eksternal dalam pengembangan UPPS dan Program Studi.\n\nII. Ketepatan analisis SWOT yang mengacu pada lingkungan eksternal dan analisis SWOT setiap kriteria.', NULL, 'kuk', NULL, NULL),
(765, 'TO', 'Tujuan Strategis Pengembangan\n\nKetepatan di dalam menetapkan tujuan strategis pengembangan.', NULL, 'kuk', NULL, NULL),
(766, 'TO', 'Program Pengembangan Berkelanjutan\n\nUPPS memiliki kebijakan, ketersediaan sumber daya, kemampuan melaksanakan, dan kerealistikan program pengembangan berkelanjutan.', NULL, 'kuk', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `led_scores`
--

CREATE TABLE `led_scores` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `tahun` int NOT NULL,
  `led_criteria_id` int UNSIGNED NOT NULL,
  `skor` decimal(5,2) DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `led_scores`
--

INSERT INTO `led_scores` (`id`, `user_id`, `prodi`, `tahun`, `led_criteria_id`, `skor`, `created_at`, `updated_at`) VALUES
(1, 14, 'RSTJ', 2025, 271, 100.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(2, 14, 'RSTJ', 2025, 272, 100.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(3, 14, 'RSTJ', 2025, 273, 40.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(4, 14, 'RSTJ', 2025, 274, 20.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(5, 14, 'RSTJ', 2025, 275, 10.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(6, 14, 'RSTJ', 2025, 276, 20.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(7, 14, 'RSTJ', 2025, 277, 30.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(8, 14, 'RSTJ', 2025, 278, 30.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(9, 14, 'RSTJ', 2025, 279, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(10, 14, 'RSTJ', 2025, 280, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(11, 14, 'RSTJ', 2025, 281, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(12, 14, 'RSTJ', 2025, 282, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(13, 14, 'RSTJ', 2025, 283, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(14, 14, 'RSTJ', 2025, 284, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(15, 14, 'RSTJ', 2025, 285, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(16, 14, 'RSTJ', 2025, 286, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(17, 14, 'RSTJ', 2025, 287, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(18, 14, 'RSTJ', 2025, 288, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(19, 14, 'RSTJ', 2025, 289, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(20, 14, 'RSTJ', 2025, 290, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(21, 14, 'RSTJ', 2025, 291, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(22, 14, 'RSTJ', 2025, 292, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(23, 14, 'RSTJ', 2025, 293, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(24, 14, 'RSTJ', 2025, 294, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(25, 14, 'RSTJ', 2025, 295, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(26, 14, 'RSTJ', 2025, 296, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(27, 14, 'RSTJ', 2025, 297, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(28, 14, 'RSTJ', 2025, 298, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(29, 14, 'RSTJ', 2025, 299, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(30, 14, 'RSTJ', 2025, 300, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(31, 14, 'RSTJ', 2025, 301, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(32, 14, 'RSTJ', 2025, 302, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(33, 14, 'RSTJ', 2025, 303, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(34, 14, 'RSTJ', 2025, 304, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(35, 14, 'RSTJ', 2025, 305, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(36, 14, 'RSTJ', 2025, 306, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(37, 14, 'RSTJ', 2025, 307, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(38, 14, 'RSTJ', 2025, 308, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(39, 14, 'RSTJ', 2025, 309, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(40, 14, 'RSTJ', 2025, 310, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(41, 14, 'RSTJ', 2025, 311, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(42, 14, 'RSTJ', 2025, 312, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(43, 14, 'RSTJ', 2025, 313, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(44, 14, 'RSTJ', 2025, 314, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(45, 14, 'RSTJ', 2025, 315, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(46, 14, 'RSTJ', 2025, 316, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(47, 14, 'RSTJ', 2025, 317, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(48, 14, 'RSTJ', 2025, 318, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(49, 14, 'RSTJ', 2025, 319, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(50, 14, 'RSTJ', 2025, 320, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(51, 14, 'RSTJ', 2025, 321, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(52, 14, 'RSTJ', 2025, 322, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(53, 14, 'RSTJ', 2025, 323, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(54, 14, 'RSTJ', 2025, 324, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(55, 14, 'RSTJ', 2025, 325, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(56, 14, 'RSTJ', 2025, 326, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(57, 14, 'RSTJ', 2025, 327, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(58, 14, 'RSTJ', 2025, 328, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(59, 14, 'RSTJ', 2025, 329, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(60, 14, 'RSTJ', 2025, 330, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(61, 14, 'RSTJ', 2025, 331, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(62, 14, 'RSTJ', 2025, 332, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(63, 14, 'RSTJ', 2025, 333, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(64, 14, 'RSTJ', 2025, 334, 0.00, '2025-10-27 15:43:02', '2025-11-07 15:39:33'),
(65, 1, 'RSTJ', 2025, 583, 100.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(66, 1, 'RSTJ', 2025, 584, 100.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(67, 1, 'RSTJ', 2025, 585, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(68, 1, 'RSTJ', 2025, 586, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(69, 1, 'RSTJ', 2025, 587, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(70, 1, 'RSTJ', 2025, 588, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(71, 1, 'RSTJ', 2025, 589, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(72, 1, 'RSTJ', 2025, 590, 100.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(73, 1, 'RSTJ', 2025, 591, 90.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(74, 1, 'RSTJ', 2025, 592, 70.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(75, 1, 'RSTJ', 2025, 593, 75.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(76, 1, 'RSTJ', 2025, 594, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(77, 1, 'RSTJ', 2025, 595, 100.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(78, 1, 'RSTJ', 2025, 596, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(79, 1, 'RSTJ', 2025, 597, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(80, 1, 'RSTJ', 2025, 598, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(81, 1, 'RSTJ', 2025, 599, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(82, 1, 'RSTJ', 2025, 600, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(83, 1, 'RSTJ', 2025, 601, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(84, 1, 'RSTJ', 2025, 602, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(85, 1, 'RSTJ', 2025, 603, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(86, 1, 'RSTJ', 2025, 604, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(87, 1, 'RSTJ', 2025, 605, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(88, 1, 'RSTJ', 2025, 606, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(89, 1, 'RSTJ', 2025, 607, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(90, 1, 'RSTJ', 2025, 608, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(91, 1, 'RSTJ', 2025, 609, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(92, 1, 'RSTJ', 2025, 610, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(93, 1, 'RSTJ', 2025, 611, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(94, 1, 'RSTJ', 2025, 612, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(95, 1, 'RSTJ', 2025, 613, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(96, 1, 'RSTJ', 2025, 614, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(97, 1, 'RSTJ', 2025, 615, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(98, 1, 'RSTJ', 2025, 616, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(99, 1, 'RSTJ', 2025, 617, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(100, 1, 'RSTJ', 2025, 618, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(101, 1, 'RSTJ', 2025, 619, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(102, 1, 'RSTJ', 2025, 620, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(103, 1, 'RSTJ', 2025, 621, 75.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(104, 1, 'RSTJ', 2025, 622, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(105, 1, 'RSTJ', 2025, 623, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(106, 1, 'RSTJ', 2025, 624, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(107, 1, 'RSTJ', 2025, 625, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(108, 1, 'RSTJ', 2025, 626, 50.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(109, 1, 'RSTJ', 2025, 627, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(110, 1, 'RSTJ', 2025, 628, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(111, 1, 'RSTJ', 2025, 629, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(112, 1, 'RSTJ', 2025, 630, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(113, 1, 'RSTJ', 2025, 631, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(114, 1, 'RSTJ', 2025, 632, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(115, 1, 'RSTJ', 2025, 633, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(116, 1, 'RSTJ', 2025, 634, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(117, 1, 'RSTJ', 2025, 635, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(118, 1, 'RSTJ', 2025, 636, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(119, 1, 'RSTJ', 2025, 637, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(120, 1, 'RSTJ', 2025, 638, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(121, 1, 'RSTJ', 2025, 639, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(122, 1, 'RSTJ', 2025, 640, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(123, 1, 'RSTJ', 2025, 641, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(124, 1, 'RSTJ', 2025, 642, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(125, 1, 'RSTJ', 2025, 643, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(126, 1, 'RSTJ', 2025, 644, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(127, 1, 'RSTJ', 2025, 645, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(128, 1, 'RSTJ', 2025, 646, 80.00, '2025-11-17 15:10:46', '2025-11-17 15:17:47'),
(129, 1, 'TRO', 2025, 647, 100.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(130, 1, 'TRO', 2025, 648, 100.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(131, 1, 'TRO', 2025, 649, 100.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(132, 1, 'TRO', 2025, 650, 90.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(133, 1, 'TRO', 2025, 651, 90.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(134, 1, 'TRO', 2025, 652, 90.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(135, 1, 'TRO', 2025, 653, 90.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(136, 1, 'TRO', 2025, 654, 90.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(137, 1, 'TRO', 2025, 655, 76.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(138, 1, 'TRO', 2025, 656, 70.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(139, 1, 'TRO', 2025, 657, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(140, 1, 'TRO', 2025, 658, 70.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(141, 1, 'TRO', 2025, 659, 95.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(142, 1, 'TRO', 2025, 660, 95.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(143, 1, 'TRO', 2025, 661, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(144, 1, 'TRO', 2025, 662, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(145, 1, 'TRO', 2025, 663, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(146, 1, 'TRO', 2025, 664, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(147, 1, 'TRO', 2025, 665, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(148, 1, 'TRO', 2025, 666, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(149, 1, 'TRO', 2025, 667, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(150, 1, 'TRO', 2025, 668, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(151, 1, 'TRO', 2025, 669, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(152, 1, 'TRO', 2025, 670, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(153, 1, 'TRO', 2025, 671, 75.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(154, 1, 'TRO', 2025, 672, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(155, 1, 'TRO', 2025, 673, 70.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(156, 1, 'TRO', 2025, 674, 70.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(157, 1, 'TRO', 2025, 675, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(158, 1, 'TRO', 2025, 676, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(159, 1, 'TRO', 2025, 677, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(160, 1, 'TRO', 2025, 678, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(161, 1, 'TRO', 2025, 679, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(162, 1, 'TRO', 2025, 680, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(163, 1, 'TRO', 2025, 681, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(164, 1, 'TRO', 2025, 682, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(165, 1, 'TRO', 2025, 683, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(166, 1, 'TRO', 2025, 684, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(167, 1, 'TRO', 2025, 685, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(168, 1, 'TRO', 2025, 686, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(169, 1, 'TRO', 2025, 687, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(170, 1, 'TRO', 2025, 688, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(171, 1, 'TRO', 2025, 689, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(172, 1, 'TRO', 2025, 690, 40.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(173, 1, 'TRO', 2025, 691, 75.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(174, 1, 'TRO', 2025, 692, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(175, 1, 'TRO', 2025, 693, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(176, 1, 'TRO', 2025, 694, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(177, 1, 'TRO', 2025, 695, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(178, 1, 'TRO', 2025, 696, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(179, 1, 'TRO', 2025, 697, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(180, 1, 'TRO', 2025, 698, 75.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(181, 1, 'TRO', 2025, 699, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(182, 1, 'TRO', 2025, 700, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(183, 1, 'TRO', 2025, 701, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(184, 1, 'TRO', 2025, 702, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(185, 1, 'TRO', 2025, 703, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(186, 1, 'TRO', 2025, 704, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(187, 1, 'TRO', 2025, 705, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(188, 1, 'TRO', 2025, 706, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(189, 1, 'TRO', 2025, 707, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(190, 1, 'TRO', 2025, 708, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(191, 1, 'TRO', 2025, 709, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(192, 1, 'TRO', 2025, 710, 80.00, '2025-11-17 15:23:17', '2025-11-17 15:23:17'),
(193, 1, 'TO', 2025, 711, 100.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(194, 1, 'TO', 2025, 712, 100.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(195, 1, 'TO', 2025, 713, 100.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(196, 1, 'TO', 2025, 714, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(197, 1, 'TO', 2025, 715, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(198, 1, 'TO', 2025, 716, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(199, 1, 'TO', 2025, 717, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(200, 1, 'TO', 2025, 718, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(201, 1, 'TO', 2025, 719, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(202, 1, 'TO', 2025, 720, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(203, 1, 'TO', 2025, 721, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(204, 1, 'TO', 2025, 722, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(205, 1, 'TO', 2025, 723, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(206, 1, 'TO', 2025, 724, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(207, 1, 'TO', 2025, 725, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(208, 1, 'TO', 2025, 726, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(209, 1, 'TO', 2025, 727, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(210, 1, 'TO', 2025, 728, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(211, 1, 'TO', 2025, 729, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(212, 1, 'TO', 2025, 730, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(213, 1, 'TO', 2025, 731, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(214, 1, 'TO', 2025, 732, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(215, 1, 'TO', 2025, 733, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(216, 1, 'TO', 2025, 734, 75.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(217, 1, 'TO', 2025, 735, 75.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(218, 1, 'TO', 2025, 736, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(219, 1, 'TO', 2025, 737, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(220, 1, 'TO', 2025, 738, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(221, 1, 'TO', 2025, 739, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(222, 1, 'TO', 2025, 740, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(223, 1, 'TO', 2025, 741, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(224, 1, 'TO', 2025, 742, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(225, 1, 'TO', 2025, 743, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(226, 1, 'TO', 2025, 744, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(227, 1, 'TO', 2025, 745, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(228, 1, 'TO', 2025, 746, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(229, 1, 'TO', 2025, 747, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(230, 1, 'TO', 2025, 748, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(231, 1, 'TO', 2025, 749, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(232, 1, 'TO', 2025, 750, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(233, 1, 'TO', 2025, 751, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(234, 1, 'TO', 2025, 752, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(235, 1, 'TO', 2025, 753, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(236, 1, 'TO', 2025, 754, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(237, 1, 'TO', 2025, 755, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(238, 1, 'TO', 2025, 756, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(239, 1, 'TO', 2025, 757, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(240, 1, 'TO', 2025, 758, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(241, 1, 'TO', 2025, 759, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(242, 1, 'TO', 2025, 760, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(243, 1, 'TO', 2025, 761, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(244, 1, 'TO', 2025, 762, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(245, 1, 'TO', 2025, 763, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(246, 1, 'TO', 2025, 764, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(247, 1, 'TO', 2025, 765, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46'),
(248, 1, 'TO', 2025, 766, 80.00, '2025-11-17 15:24:46', '2025-11-17 15:24:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `led_standar`
--

CREATE TABLE `led_standar` (
  `id` int UNSIGNED NOT NULL,
  `nama_standar` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `led_standar`
--

INSERT INTO `led_standar` (`id`, `nama_standar`, `created_at`, `updated_at`) VALUES
(1, 'Standar 1 VMTS ', '2025-10-27 13:53:17', '2025-11-26 02:25:06'),
(2, 'Standar 2 Akuntabilitas', '2025-10-27 13:53:21', '2025-11-26 02:25:25'),
(3, 'Standar 4 SDM', '2025-10-27 14:57:12', '2025-11-26 02:25:54'),
(4, 'Standar 7 SPM', '2025-10-27 14:57:14', '2025-11-26 02:26:22'),
(5, 'Standar 5 Sarpras & K3L', '2025-10-27 14:57:17', '2025-11-26 02:26:07'),
(6, 'Standar 6 Mahasiswa dan Luaran Mahasiswa', '2025-10-27 14:57:18', '2025-11-26 02:26:14'),
(7, 'Standar 3 Relevansi Tridharma', '2025-10-28 01:09:32', '2025-11-26 02:25:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `led_submissions`
--

CREATE TABLE `led_submissions` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `tahun` int NOT NULL,
  `led_criteria_id` int UNSIGNED NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `catatan_kabag` text COMMENT 'Catatan/revisi dari Kabag untuk staf',
  `catatan_wadir` text COMMENT 'Catatan/revisi dari Wadir untuk staf/kabag',
  `kabag_approved` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Persetujuan oleh Kabag',
  `catatan` text,
  `file_bukti` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `led_submissions`
--

INSERT INTO `led_submissions` (`id`, `user_id`, `prodi`, `tahun`, `led_criteria_id`, `status`, `catatan_kabag`, `catatan_wadir`, `kabag_approved`, `catatan`, `file_bukti`, `created_at`, `updated_at`) VALUES
(1, 5, 'RSTJ', 2025, 271, 'Ada', 'oke', '', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(2, 1, 'RSTJ', 2025, 272, 'Ada', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(3, 1, 'RSTJ', 2025, 273, 'Tidak Ada', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(4, 5, 'RSTJ', 2025, 274, 'Ada', 'lengkap', '', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(5, 1, 'RSTJ', 2025, 275, 'Terlampir', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(6, 1, 'RSTJ', 2025, 276, 'Ada', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(7, 1, 'RSTJ', 2025, 277, 'Ada', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(8, 1, 'RSTJ', 2025, 278, 'Terlampir', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(9, 1, 'RSTJ', 2025, 279, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(10, 1, 'RSTJ', 2025, 280, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(11, 1, 'RSTJ', 2025, 281, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(12, 5, 'RSTJ', 2025, 282, 'Ada', '', '', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(13, 5, 'RSTJ', 2025, 283, 'Ada', '', '', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(14, 5, 'RSTJ', 2025, 284, '', 'sudah lengkap', '', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(15, 5, 'RSTJ', 2025, 285, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(16, 5, 'RSTJ', 2025, 286, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(17, 5, 'RSTJ', 2025, 287, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(18, 5, 'RSTJ', 2025, 288, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(19, 5, 'RSTJ', 2025, 289, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(20, 5, 'RSTJ', 2025, 290, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(21, 5, 'RSTJ', 2025, 291, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(22, 5, 'RSTJ', 2025, 292, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(23, 5, 'RSTJ', 2025, 293, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(24, 5, 'RSTJ', 2025, 294, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(25, 5, 'RSTJ', 2025, 295, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(26, 5, 'RSTJ', 2025, 296, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(27, 5, 'RSTJ', 2025, 297, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(28, 5, 'RSTJ', 2025, 298, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(29, 5, 'RSTJ', 2025, 299, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(30, 5, 'RSTJ', 2025, 300, '', NULL, '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(31, 5, 'RSTJ', 2025, 301, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(32, 1, 'RSTJ', 2025, 302, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(33, 5, 'RSTJ', 2025, 303, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(34, 5, 'RSTJ', 2025, 304, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(35, 5, 'RSTJ', 2025, 305, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(36, 5, 'RSTJ', 2025, 306, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(37, 5, 'RSTJ', 2025, 307, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(38, 5, 'RSTJ', 2025, 308, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(39, 5, 'RSTJ', 2025, 309, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(40, 5, 'RSTJ', 2025, 310, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(41, 1, 'RSTJ', 2025, 311, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(42, 1, 'RSTJ', 2025, 312, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(43, 5, 'RSTJ', 2025, 313, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(44, 5, 'RSTJ', 2025, 314, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(45, 5, 'RSTJ', 2025, 315, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(46, 5, 'RSTJ', 2025, 316, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(47, 5, 'RSTJ', 2025, 317, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(48, 5, 'RSTJ', 2025, 318, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(49, 5, 'RSTJ', 2025, 319, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(50, 5, 'RSTJ', 2025, 320, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(51, 5, 'RSTJ', 2025, 321, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(52, 5, 'RSTJ', 2025, 322, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(53, 5, 'RSTJ', 2025, 323, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(54, 5, 'RSTJ', 2025, 324, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(55, 5, 'RSTJ', 2025, 325, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(56, 5, 'RSTJ', 2025, 326, '', '', '', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-10-27 04:24:34', '2025-11-17 09:03:49'),
(57, 1, 'RSTJ', 2025, 327, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(58, 1, 'RSTJ', 2025, 328, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(59, 1, 'RSTJ', 2025, 329, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(60, 1, 'RSTJ', 2025, 330, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(61, 1, 'RSTJ', 2025, 331, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(62, 1, 'RSTJ', 2025, 332, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(63, 1, 'RSTJ', 2025, 333, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(64, 1, 'RSTJ', 2025, 334, '', NULL, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-11-17 07:46:33'),
(65, 1, 'TRO', 2025, 271, 'Ada', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(66, 1, 'TRO', 2025, 272, 'Ada', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(67, 1, 'TRO', 2025, 273, 'Ada', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(68, 1, 'TRO', 2025, 274, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(69, 1, 'TRO', 2025, 275, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(70, 1, 'TRO', 2025, 276, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(71, 1, 'TRO', 2025, 277, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(72, 1, 'TRO', 2025, 278, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(73, 1, 'TRO', 2025, 279, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(74, 1, 'TRO', 2025, 280, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(75, 1, 'TRO', 2025, 281, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(76, 1, 'TRO', 2025, 282, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(77, 1, 'TRO', 2025, 283, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(78, 1, 'TRO', 2025, 284, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(79, 1, 'TRO', 2025, 285, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(80, 1, 'TRO', 2025, 286, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(81, 1, 'TRO', 2025, 287, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(82, 1, 'TRO', 2025, 288, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(83, 1, 'TRO', 2025, 289, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(84, 1, 'TRO', 2025, 290, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(85, 1, 'TRO', 2025, 291, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(86, 1, 'TRO', 2025, 292, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(87, 1, 'TRO', 2025, 293, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(88, 1, 'TRO', 2025, 294, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(89, 1, 'TRO', 2025, 295, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(90, 1, 'TRO', 2025, 296, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(91, 1, 'TRO', 2025, 297, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(92, 1, 'TRO', 2025, 298, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(93, 1, 'TRO', 2025, 299, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(94, 1, 'TRO', 2025, 300, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(95, 1, 'TRO', 2025, 301, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(96, 1, 'TRO', 2025, 302, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(97, 1, 'TRO', 2025, 303, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(98, 1, 'TRO', 2025, 304, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(99, 1, 'TRO', 2025, 305, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(100, 1, 'TRO', 2025, 306, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(101, 1, 'TRO', 2025, 307, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(102, 1, 'TRO', 2025, 308, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(103, 1, 'TRO', 2025, 309, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(104, 1, 'TRO', 2025, 310, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(105, 1, 'TRO', 2025, 311, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(106, 1, 'TRO', 2025, 312, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(107, 1, 'TRO', 2025, 313, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(108, 1, 'TRO', 2025, 314, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(109, 1, 'TRO', 2025, 315, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(110, 1, 'TRO', 2025, 316, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(111, 1, 'TRO', 2025, 317, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(112, 1, 'TRO', 2025, 318, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(113, 1, 'TRO', 2025, 319, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(114, 1, 'TRO', 2025, 320, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(115, 1, 'TRO', 2025, 321, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(116, 1, 'TRO', 2025, 322, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(117, 1, 'TRO', 2025, 323, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(118, 1, 'TRO', 2025, 324, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(119, 1, 'TRO', 2025, 325, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(120, 1, 'TRO', 2025, 326, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(121, 1, 'TRO', 2025, 327, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(122, 1, 'TRO', 2025, 328, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(123, 1, 'TRO', 2025, 329, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(124, 1, 'TRO', 2025, 330, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(125, 1, 'TRO', 2025, 331, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(126, 1, 'TRO', 2025, 332, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(127, 1, 'TRO', 2025, 333, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(128, 1, 'TRO', 2025, 334, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(129, 1, 'RSTJ', 2024, 271, 'Ada', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(130, 1, 'RSTJ', 2024, 272, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(131, 1, 'RSTJ', 2024, 273, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(132, 1, 'RSTJ', 2024, 274, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(133, 1, 'RSTJ', 2024, 275, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(134, 1, 'RSTJ', 2024, 276, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(135, 1, 'RSTJ', 2024, 277, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(136, 1, 'RSTJ', 2024, 278, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(137, 1, 'RSTJ', 2024, 279, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(138, 1, 'RSTJ', 2024, 280, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(139, 1, 'RSTJ', 2024, 281, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(140, 1, 'RSTJ', 2024, 282, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(141, 1, 'RSTJ', 2024, 283, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(142, 1, 'RSTJ', 2024, 284, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(143, 1, 'RSTJ', 2024, 285, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(144, 1, 'RSTJ', 2024, 286, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(145, 1, 'RSTJ', 2024, 287, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(146, 1, 'RSTJ', 2024, 288, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(147, 1, 'RSTJ', 2024, 289, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(148, 1, 'RSTJ', 2024, 290, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(149, 1, 'RSTJ', 2024, 291, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(150, 1, 'RSTJ', 2024, 292, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(151, 1, 'RSTJ', 2024, 293, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(152, 1, 'RSTJ', 2024, 294, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(153, 1, 'RSTJ', 2024, 295, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(154, 1, 'RSTJ', 2024, 296, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(155, 1, 'RSTJ', 2024, 297, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(156, 1, 'RSTJ', 2024, 298, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(157, 1, 'RSTJ', 2024, 299, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(158, 1, 'RSTJ', 2024, 300, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(159, 1, 'RSTJ', 2024, 301, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(160, 1, 'RSTJ', 2024, 302, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(161, 1, 'RSTJ', 2024, 303, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(162, 1, 'RSTJ', 2024, 304, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(163, 1, 'RSTJ', 2024, 305, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(164, 1, 'RSTJ', 2024, 306, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(165, 1, 'RSTJ', 2024, 307, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(166, 1, 'RSTJ', 2024, 308, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(167, 1, 'RSTJ', 2024, 309, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(168, 1, 'RSTJ', 2024, 310, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(169, 1, 'RSTJ', 2024, 311, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(170, 1, 'RSTJ', 2024, 312, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(171, 1, 'RSTJ', 2024, 313, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(172, 1, 'RSTJ', 2024, 314, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(173, 1, 'RSTJ', 2024, 315, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(174, 1, 'RSTJ', 2024, 316, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(175, 1, 'RSTJ', 2024, 317, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(176, 1, 'RSTJ', 2024, 318, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(177, 1, 'RSTJ', 2024, 319, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(178, 1, 'RSTJ', 2024, 320, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(179, 1, 'RSTJ', 2024, 321, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(180, 1, 'RSTJ', 2024, 322, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(181, 1, 'RSTJ', 2024, 323, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(182, 1, 'RSTJ', 2024, 324, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(183, 1, 'RSTJ', 2024, 325, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(184, 1, 'RSTJ', 2024, 326, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(185, 1, 'RSTJ', 2024, 327, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(186, 1, 'RSTJ', 2024, 328, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(187, 1, 'RSTJ', 2024, 329, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(188, 1, 'RSTJ', 2024, 330, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(189, 1, 'RSTJ', 2024, 331, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(190, 1, 'RSTJ', 2024, 332, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(191, 1, 'RSTJ', 2024, 333, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(192, 1, 'RSTJ', 2024, 334, '', NULL, NULL, 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(193, 1, 'TO', 2025, 271, 'Tidak Ada', NULL, NULL, 0, 'lengkap', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(194, 1, 'TO', 2025, 272, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(195, 1, 'TO', 2025, 273, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(196, 1, 'TO', 2025, 274, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(197, 1, 'TO', 2025, 275, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(198, 1, 'TO', 2025, 276, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(199, 1, 'TO', 2025, 277, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(200, 1, 'TO', 2025, 278, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(201, 1, 'TO', 2025, 279, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(202, 1, 'TO', 2025, 280, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(203, 1, 'TO', 2025, 281, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(204, 1, 'TO', 2025, 282, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(205, 1, 'TO', 2025, 283, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(206, 1, 'TO', 2025, 284, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(207, 1, 'TO', 2025, 285, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(208, 1, 'TO', 2025, 286, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(209, 1, 'TO', 2025, 287, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(210, 1, 'TO', 2025, 288, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(211, 1, 'TO', 2025, 289, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(212, 1, 'TO', 2025, 290, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(213, 1, 'TO', 2025, 291, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(214, 1, 'TO', 2025, 292, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(215, 1, 'TO', 2025, 293, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(216, 1, 'TO', 2025, 294, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(217, 1, 'TO', 2025, 295, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(218, 1, 'TO', 2025, 296, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(219, 1, 'TO', 2025, 297, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(220, 1, 'TO', 2025, 298, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(221, 1, 'TO', 2025, 299, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(222, 5, 'TO', 2025, 300, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-11-17 09:24:39'),
(223, 1, 'TO', 2025, 301, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(224, 1, 'TO', 2025, 302, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(225, 1, 'TO', 2025, 303, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(226, 1, 'TO', 2025, 304, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(227, 1, 'TO', 2025, 305, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(228, 1, 'TO', 2025, 306, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(229, 1, 'TO', 2025, 307, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(230, 1, 'TO', 2025, 308, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(231, 1, 'TO', 2025, 309, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(232, 1, 'TO', 2025, 310, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(233, 1, 'TO', 2025, 311, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(234, 1, 'TO', 2025, 312, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(235, 1, 'TO', 2025, 313, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(236, 1, 'TO', 2025, 314, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(237, 1, 'TO', 2025, 315, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(238, 1, 'TO', 2025, 316, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(239, 1, 'TO', 2025, 317, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(240, 1, 'TO', 2025, 318, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(241, 1, 'TO', 2025, 319, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(242, 1, 'TO', 2025, 320, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(243, 1, 'TO', 2025, 321, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(244, 1, 'TO', 2025, 322, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(245, 1, 'TO', 2025, 323, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(246, 1, 'TO', 2025, 324, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(247, 1, 'TO', 2025, 325, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(248, 1, 'TO', 2025, 326, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(249, 1, 'TO', 2025, 327, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(250, 1, 'TO', 2025, 328, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(251, 1, 'TO', 2025, 329, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(252, 1, 'TO', 2025, 330, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(253, 1, 'TO', 2025, 331, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(254, 1, 'TO', 2025, 332, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(255, 1, 'TO', 2025, 333, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(256, 1, 'TO', 2025, 334, '', NULL, NULL, 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(257, 5, 'RSTJ', 2025, 364, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(258, 5, 'RSTJ', 2025, 463, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(259, 5, 'RSTJ', 2025, 464, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(260, 5, 'RSTJ', 2025, 465, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(261, 5, 'RSTJ', 2025, 466, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(262, 5, 'RSTJ', 2025, 467, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(263, 5, 'RSTJ', 2025, 468, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(264, 5, 'RSTJ', 2025, 469, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(265, 5, 'RSTJ', 2025, 470, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(266, 5, 'RSTJ', 2025, 471, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(267, 5, 'RSTJ', 2025, 472, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(268, 5, 'RSTJ', 2025, 473, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(269, 5, 'RSTJ', 2025, 474, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(270, 5, 'RSTJ', 2025, 475, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(271, 5, 'RSTJ', 2025, 476, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(272, 5, 'RSTJ', 2025, 477, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(273, 5, 'RSTJ', 2025, 478, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(274, 5, 'RSTJ', 2025, 479, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(275, 5, 'RSTJ', 2025, 480, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(276, 5, 'RSTJ', 2025, 481, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(277, 5, 'RSTJ', 2025, 482, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(278, 5, 'RSTJ', 2025, 483, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(279, 5, 'RSTJ', 2025, 484, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(280, 5, 'RSTJ', 2025, 485, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(281, 5, 'RSTJ', 2025, 486, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(282, 5, 'RSTJ', 2025, 487, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(283, 5, 'RSTJ', 2025, 488, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(284, 5, 'RSTJ', 2025, 489, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(285, 5, 'RSTJ', 2025, 490, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(286, 5, 'RSTJ', 2025, 491, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(287, 5, 'RSTJ', 2025, 492, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(288, 5, 'RSTJ', 2025, 493, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(289, 5, 'RSTJ', 2025, 494, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(290, 5, 'RSTJ', 2025, 495, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(291, 5, 'RSTJ', 2025, 496, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(292, 5, 'RSTJ', 2025, 497, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(293, 5, 'RSTJ', 2025, 498, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(294, 5, 'RSTJ', 2025, 499, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(295, 5, 'RSTJ', 2025, 500, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(296, 5, 'RSTJ', 2025, 501, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(297, 5, 'RSTJ', 2025, 502, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(298, 5, 'RSTJ', 2025, 503, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(299, 5, 'RSTJ', 2025, 504, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(300, 5, 'RSTJ', 2025, 505, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(301, 5, 'RSTJ', 2025, 506, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(302, 5, 'RSTJ', 2025, 507, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(303, 5, 'RSTJ', 2025, 508, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(304, 5, 'RSTJ', 2025, 509, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(305, 5, 'RSTJ', 2025, 510, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(306, 5, 'RSTJ', 2025, 511, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(307, 5, 'RSTJ', 2025, 512, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(308, 5, 'RSTJ', 2025, 513, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(309, 5, 'RSTJ', 2025, 514, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(310, 5, 'RSTJ', 2025, 515, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(311, 5, 'RSTJ', 2025, 516, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(312, 5, 'RSTJ', 2025, 517, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(313, 5, 'RSTJ', 2025, 518, NULL, NULL, NULL, 0, '', NULL, '2025-11-13 06:21:50', '2025-11-17 09:03:49'),
(314, 5, 'TO', 2025, 364, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(315, 5, 'TO', 2025, 463, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(316, 5, 'TO', 2025, 464, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(317, 5, 'TO', 2025, 465, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(318, 5, 'TO', 2025, 466, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(319, 5, 'TO', 2025, 467, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(320, 5, 'TO', 2025, 468, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(321, 5, 'TO', 2025, 469, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(322, 5, 'TO', 2025, 470, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(323, 5, 'TO', 2025, 471, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(324, 5, 'TO', 2025, 472, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(325, 5, 'TO', 2025, 473, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(326, 5, 'TO', 2025, 474, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(327, 5, 'TO', 2025, 475, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(328, 5, 'TO', 2025, 476, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(329, 5, 'TO', 2025, 477, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(330, 5, 'TO', 2025, 478, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(331, 5, 'TO', 2025, 479, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(332, 5, 'TO', 2025, 480, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(333, 5, 'TO', 2025, 481, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(334, 5, 'TO', 2025, 482, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(335, 5, 'TO', 2025, 483, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(336, 5, 'TO', 2025, 484, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(337, 5, 'TO', 2025, 485, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(338, 5, 'TO', 2025, 486, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(339, 5, 'TO', 2025, 487, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(340, 5, 'TO', 2025, 488, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(341, 5, 'TO', 2025, 489, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(342, 5, 'TO', 2025, 490, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(343, 5, 'TO', 2025, 491, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(344, 5, 'TO', 2025, 492, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(345, 5, 'TO', 2025, 493, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(346, 5, 'TO', 2025, 494, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(347, 5, 'TO', 2025, 495, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(348, 5, 'TO', 2025, 496, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(349, 5, 'TO', 2025, 497, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(350, 5, 'TO', 2025, 498, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(351, 5, 'TO', 2025, 499, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(352, 5, 'TO', 2025, 500, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(353, 5, 'TO', 2025, 501, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(354, 5, 'TO', 2025, 502, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(355, 5, 'TO', 2025, 503, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(356, 5, 'TO', 2025, 504, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(357, 5, 'TO', 2025, 505, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(358, 5, 'TO', 2025, 506, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(359, 5, 'TO', 2025, 507, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(360, 5, 'TO', 2025, 508, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(361, 5, 'TO', 2025, 509, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(362, 5, 'TO', 2025, 510, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(363, 5, 'TO', 2025, 511, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(364, 5, 'TO', 2025, 512, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(365, 5, 'TO', 2025, 513, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(366, 5, 'TO', 2025, 514, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(367, 5, 'TO', 2025, 515, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(368, 5, 'TO', 2025, 516, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(369, 5, 'TO', 2025, 517, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(370, 5, 'TO', 2025, 518, NULL, NULL, NULL, 0, '', NULL, '2025-11-17 09:24:39', '2025-11-17 09:24:39'),
(371, 0, 'RSTJ', 2025, 583, 'Ada', 'sudah lengkap', 'dokumen sudah sesuai dengan VMTS', 1, 'https://drive.google.com/file/d/1dv-Cl_uXVAjv4qOc8gI5frbhlg2SaUrb/view?usp=drive_link', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(372, 0, 'RSTJ', 2025, 586, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(373, 0, 'RSTJ', 2025, 594, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(374, 0, 'RSTJ', 2025, 595, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(375, 0, 'RSTJ', 2025, 596, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(376, 0, 'RSTJ', 2025, 597, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(377, 0, 'RSTJ', 2025, 598, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(378, 0, 'RSTJ', 2025, 599, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(379, 0, 'RSTJ', 2025, 600, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(380, 0, 'RSTJ', 2025, 601, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(381, 0, 'RSTJ', 2025, 602, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(382, 0, 'RSTJ', 2025, 603, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(383, 0, 'RSTJ', 2025, 604, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(384, 0, 'RSTJ', 2025, 605, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(385, 0, 'RSTJ', 2025, 606, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(386, 0, 'RSTJ', 2025, 607, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(387, 0, 'RSTJ', 2025, 608, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(388, 0, 'RSTJ', 2025, 609, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(389, 0, 'RSTJ', 2025, 610, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(390, 0, 'RSTJ', 2025, 611, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(391, 0, 'RSTJ', 2025, 612, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(392, 0, 'RSTJ', 2025, 613, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(393, 0, 'RSTJ', 2025, 615, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(394, 0, 'RSTJ', 2025, 616, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(395, 0, 'RSTJ', 2025, 617, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(396, 0, 'RSTJ', 2025, 618, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(397, 0, 'RSTJ', 2025, 619, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(398, 0, 'RSTJ', 2025, 620, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(399, 0, 'RSTJ', 2025, 621, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(400, 0, 'RSTJ', 2025, 622, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(401, 0, 'RSTJ', 2025, 625, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(402, 0, 'RSTJ', 2025, 626, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38');
INSERT INTO `led_submissions` (`id`, `user_id`, `prodi`, `tahun`, `led_criteria_id`, `status`, `catatan_kabag`, `catatan_wadir`, `kabag_approved`, `catatan`, `file_bukti`, `created_at`, `updated_at`) VALUES
(403, 0, 'RSTJ', 2025, 627, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(404, 0, 'RSTJ', 2025, 628, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(405, 0, 'RSTJ', 2025, 629, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(406, 0, 'RSTJ', 2025, 630, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(407, 0, 'RSTJ', 2025, 631, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(408, 0, 'RSTJ', 2025, 632, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(409, 0, 'RSTJ', 2025, 633, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(410, 0, 'RSTJ', 2025, 634, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(411, 0, 'RSTJ', 2025, 635, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(412, 0, 'RSTJ', 2025, 636, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(413, 0, 'RSTJ', 2025, 637, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(414, 0, 'RSTJ', 2025, 638, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2026-04-09 08:19:38'),
(415, 16, 'RSTJ', 2025, 676, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:30:52'),
(416, 7, 'TRO', 2025, 647, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(417, 7, 'TRO', 2025, 650, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(418, 7, 'TRO', 2025, 658, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(419, 7, 'TRO', 2025, 659, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(420, 7, 'TRO', 2025, 660, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(421, 7, 'TRO', 2025, 661, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(422, 7, 'TRO', 2025, 662, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(423, 7, 'TRO', 2025, 663, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(424, 7, 'TRO', 2025, 664, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(425, 7, 'TRO', 2025, 665, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(426, 7, 'TRO', 2025, 666, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(427, 7, 'TRO', 2025, 667, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(428, 7, 'TRO', 2025, 668, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(429, 7, 'TRO', 2025, 669, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(430, 7, 'TRO', 2025, 670, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(431, 7, 'TRO', 2025, 671, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(432, 7, 'TRO', 2025, 672, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(433, 7, 'TRO', 2025, 673, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(434, 7, 'TRO', 2025, 674, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(435, 7, 'TRO', 2025, 675, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(436, 7, 'TRO', 2025, 676, '', NULL, 'baik', 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(437, 7, 'TRO', 2025, 677, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(438, 7, 'TRO', 2025, 679, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(439, 7, 'TRO', 2025, 680, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(440, 7, 'TRO', 2025, 681, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(441, 7, 'TRO', 2025, 682, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(442, 7, 'TRO', 2025, 683, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(443, 7, 'TRO', 2025, 684, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(444, 7, 'TRO', 2025, 685, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(445, 7, 'TRO', 2025, 686, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(446, 7, 'TRO', 2025, 689, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(447, 7, 'TRO', 2025, 690, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(448, 7, 'TRO', 2025, 691, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(449, 7, 'TRO', 2025, 692, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(450, 7, 'TRO', 2025, 693, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(451, 7, 'TRO', 2025, 694, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(452, 7, 'TRO', 2025, 695, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(453, 7, 'TRO', 2025, 696, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(454, 7, 'TRO', 2025, 697, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(455, 7, 'TRO', 2025, 698, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(456, 7, 'TRO', 2025, 699, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(457, 7, 'TRO', 2025, 700, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(458, 7, 'TRO', 2025, 701, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(459, 7, 'TRO', 2025, 702, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:56', '2025-11-17 15:06:14'),
(460, 9, 'TO', 2025, 676, NULL, NULL, NULL, 0, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 14:56:58'),
(461, 7, 'TO', 2025, 711, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(462, 7, 'TO', 2025, 714, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(463, 7, 'TO', 2025, 722, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(464, 7, 'TO', 2025, 723, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(465, 7, 'TO', 2025, 724, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(466, 7, 'TO', 2025, 725, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(467, 7, 'TO', 2025, 726, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(468, 7, 'TO', 2025, 727, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(469, 7, 'TO', 2025, 728, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(470, 7, 'TO', 2025, 729, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(471, 7, 'TO', 2025, 730, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(472, 7, 'TO', 2025, 731, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(473, 7, 'TO', 2025, 732, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(474, 7, 'TO', 2025, 733, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(475, 7, 'TO', 2025, 734, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(476, 7, 'TO', 2025, 735, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(477, 7, 'TO', 2025, 736, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(478, 7, 'TO', 2025, 737, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(479, 7, 'TO', 2025, 738, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(480, 7, 'TO', 2025, 740, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(481, 7, 'TO', 2025, 741, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(482, 7, 'TO', 2025, 742, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(483, 7, 'TO', 2025, 743, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(484, 7, 'TO', 2025, 744, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(485, 7, 'TO', 2025, 745, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(486, 7, 'TO', 2025, 748, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(487, 7, 'TO', 2025, 749, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(488, 7, 'TO', 2025, 750, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(489, 7, 'TO', 2025, 751, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(490, 7, 'TO', 2025, 752, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(491, 7, 'TO', 2025, 753, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(492, 7, 'TO', 2025, 754, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(493, 7, 'TO', 2025, 755, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(494, 7, 'TO', 2025, 756, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(495, 7, 'TO', 2025, 757, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(496, 7, 'TO', 2025, 758, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:46:25', '2025-11-17 15:09:18'),
(497, 0, 'RSTJ', 2025, 584, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(498, 0, 'RSTJ', 2025, 585, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(499, 0, 'RSTJ', 2025, 587, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(500, 0, 'RSTJ', 2025, 588, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(501, 0, 'RSTJ', 2025, 589, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(502, 0, 'RSTJ', 2025, 590, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(503, 0, 'RSTJ', 2025, 591, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(504, 0, 'RSTJ', 2025, 592, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(505, 0, 'RSTJ', 2025, 593, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(506, 0, 'RSTJ', 2025, 614, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(507, 0, 'RSTJ', 2025, 623, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(508, 0, 'RSTJ', 2025, 624, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(509, 0, 'RSTJ', 2025, 639, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(510, 0, 'RSTJ', 2025, 640, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(511, 0, 'RSTJ', 2025, 641, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(512, 0, 'RSTJ', 2025, 642, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(513, 0, 'RSTJ', 2025, 643, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(514, 0, 'RSTJ', 2025, 644, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(515, 0, 'RSTJ', 2025, 645, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(516, 0, 'RSTJ', 2025, 646, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2026-04-09 08:19:38'),
(517, 7, 'TRO', 2025, 648, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(518, 7, 'TRO', 2025, 649, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(519, 7, 'TRO', 2025, 651, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(520, 7, 'TRO', 2025, 652, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(521, 7, 'TRO', 2025, 653, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(522, 7, 'TRO', 2025, 654, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(523, 7, 'TRO', 2025, 655, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(524, 7, 'TRO', 2025, 656, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(525, 7, 'TRO', 2025, 657, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(526, 7, 'TRO', 2025, 678, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(527, 7, 'TRO', 2025, 687, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(528, 7, 'TRO', 2025, 688, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(529, 7, 'TRO', 2025, 703, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(530, 7, 'TRO', 2025, 704, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(531, 7, 'TRO', 2025, 705, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(532, 7, 'TRO', 2025, 706, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(533, 7, 'TRO', 2025, 707, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(534, 7, 'TRO', 2025, 708, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(535, 7, 'TRO', 2025, 709, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(536, 7, 'TRO', 2025, 710, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:40', '2025-11-17 15:06:14'),
(537, 7, 'TO', 2025, 712, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(538, 7, 'TO', 2025, 713, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(539, 7, 'TO', 2025, 715, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(540, 7, 'TO', 2025, 716, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(541, 7, 'TO', 2025, 717, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(542, 7, 'TO', 2025, 718, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(543, 7, 'TO', 2025, 719, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(544, 7, 'TO', 2025, 720, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(545, 7, 'TO', 2025, 721, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(546, 7, 'TO', 2025, 739, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(547, 7, 'TO', 2025, 746, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(548, 7, 'TO', 2025, 747, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(549, 7, 'TO', 2025, 759, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(550, 7, 'TO', 2025, 760, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(551, 7, 'TO', 2025, 761, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(552, 7, 'TO', 2025, 762, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(553, 7, 'TO', 2025, 763, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(554, 7, 'TO', 2025, 764, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(555, 7, 'TO', 2025, 765, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(556, 7, 'TO', 2025, 766, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18'),
(557, 173, 'RSTJ', 2026, 583, NULL, 'sudah lengkap', NULL, 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(558, 173, 'RSTJ', 2026, 586, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(559, 173, 'RSTJ', 2026, 594, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(560, 173, 'RSTJ', 2026, 595, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(561, 173, 'RSTJ', 2026, 596, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(562, 173, 'RSTJ', 2026, 597, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(563, 173, 'RSTJ', 2026, 598, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(564, 173, 'RSTJ', 2026, 599, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(565, 173, 'RSTJ', 2026, 600, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(566, 173, 'RSTJ', 2026, 601, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(567, 173, 'RSTJ', 2026, 602, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(568, 173, 'RSTJ', 2026, 603, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(569, 173, 'RSTJ', 2026, 604, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(570, 173, 'RSTJ', 2026, 605, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(571, 173, 'RSTJ', 2026, 606, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(572, 173, 'RSTJ', 2026, 607, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(573, 173, 'RSTJ', 2026, 608, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(574, 173, 'RSTJ', 2026, 609, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(575, 173, 'RSTJ', 2026, 610, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(576, 173, 'RSTJ', 2026, 611, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(577, 173, 'RSTJ', 2026, 612, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(578, 173, 'RSTJ', 2026, 613, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(579, 173, 'RSTJ', 2026, 615, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(580, 173, 'RSTJ', 2026, 616, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(581, 173, 'RSTJ', 2026, 617, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(582, 173, 'RSTJ', 2026, 618, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(583, 173, 'RSTJ', 2026, 619, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(584, 173, 'RSTJ', 2026, 620, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(585, 173, 'RSTJ', 2026, 621, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(586, 173, 'RSTJ', 2026, 622, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(587, 173, 'RSTJ', 2026, 625, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(588, 173, 'RSTJ', 2026, 626, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(589, 173, 'RSTJ', 2026, 627, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(590, 173, 'RSTJ', 2026, 628, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(591, 173, 'RSTJ', 2026, 629, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(592, 173, 'RSTJ', 2026, 630, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(593, 173, 'RSTJ', 2026, 631, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(594, 173, 'RSTJ', 2026, 632, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(595, 173, 'RSTJ', 2026, 633, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(596, 173, 'RSTJ', 2026, 634, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(597, 173, 'RSTJ', 2026, 635, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(598, 173, 'RSTJ', 2026, 636, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(599, 173, 'RSTJ', 2026, 637, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18'),
(600, 173, 'RSTJ', 2026, 638, NULL, '', NULL, 0, '', NULL, '2026-04-13 02:47:07', '2026-04-13 02:48:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_indikator`
--

CREATE TABLE `master_indikator` (
  `id` int UNSIGNED NOT NULL,
  `sasaran_id` int UNSIGNED DEFAULT NULL,
  `nama_indikator` text COLLATE utf8mb4_general_ci NOT NULL,
  `satuan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_indikator`
--

INSERT INTO `master_indikator` (`id`, `sasaran_id`, `nama_indikator`, `satuan`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Persentase Lulusan yang Terserap Dunia Kerja', 'Persen', NULL, NULL),
(2, NULL, 'Jumlah Publikasi Ilmiah Internasional', 'Dokumen', NULL, NULL),
(3, NULL, 'Indeks Kepuasan Masyarakat', 'Indeks', NULL, NULL),
(4, NULL, 'Nilai SAKIP Instansi', 'Nilai', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-08-13-051242', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1755062097, 1),
(2, '2025-08-13-051246', 'App\\Database\\Migrations\\CreateRencanaKinerjaTable', 'default', 'App', 1755062097, 1),
(3, '2025-08-21-024901', 'App\\Database\\Migrations\\CreateJadwalDiklatTable', 'default', 'App', 1755744589, 2),
(7, '2025-08-21-031503', 'App\\Database\\Migrations\\CreateSasaranTable', 'default', 'App', 1755748023, 3),
(8, '2025-08-21-031504', 'App\\Database\\Migrations\\CreateIndikatorTable', 'default', 'App', 1755748023, 3),
(9, '2025-08-21-031504', 'App\\Database\\Migrations\\CreateSatuanTable', 'default', 'App', 1755748023, 3),
(10, '2025-10-23-042434', 'App\\Database\\Migrations\\CreateLedCriteriaTable', 'default', 'App', 1761193613, 4),
(15, '2025-10-24-033551', 'App\\Database\\Migrations\\CreateLedSubmissionsTable', 'default', 'App', 1761528522, 5),
(16, '2025-10-27-082942', 'App\\Database\\Migrations\\AddKategoriToLedCriteria', 'default', 'App', 1761553940, 6),
(17, '2025-10-27-084917', 'App\\Database\\Migrations\\CreateLedCategoriesTable', 'default', 'App', 1761556312, 7),
(18, '2025-10-27-152053', 'App\\Database\\Migrations\\CreateLedScoresTable', 'default', 'App', 1761578471, 8),
(19, '2025-10-28-135727', 'App\\Database\\Migrations\\AddRoleToLedCriteria', 'default', 'App', 1761660099, 9),
(21, '2025-10-29-080404', 'App\\Database\\Migrations\\AddKabagApprovalToLedSubmissions', 'default', 'App', 1762489195, 10),
(22, '2025-11-07-041046', 'App\\Database\\Migrations\\NormalizeLedCriteriaCategory', 'default', 'App', 1762489195, 10),
(23, '2025-11-07-085738', 'App\\Database\\Migrations\\UbahLedCriteriaPerProdi', 'default', 'App', 1762505878, 11),
(24, '2025-11-07-113812', 'App\\Database\\Migrations\\RemoveSortOrderFromLedCriteria', 'default', 'App', 1762515510, 12),
(25, '2025-11-07-115145', 'App\\Database\\Migrations\\RenameKategoriToStandar', 'default', 'App', 1762516330, 13),
(27, '2025-11-07-122151', 'App\\Database\\Migrations\\FixLedSchemaConsistency', 'default', 'App', 1762528479, 14),
(28, '2025-11-07-151021', 'App\\Database\\Migrations\\DropIdKategoriFromLedCriteria', 'default', 'App', 1762528530, 15),
(29, '2025-11-08-025948', 'App\\Database\\Migrations\\AddCommentsToLedSubmissions', 'default', 'App', 1762570958, 16),
(31, '2025-11-08-025948', 'App\\Database\\Migrations\\CreateRemunerasiTable', 'default', 'App', 1762583377, 17),
(32, '2025-08-13-051242', 'App\\Database\\Migrations\\AddKinerjaColumnsToUsers', 'default', 'App', 1770444414, 18),
(33, '2025-08-13-051242', 'App\\Database\\Migrations\\AddAtasanToUsers', 'default', 'App', 1770444606, 19),
(34, '2025-11-08-062613', 'App\\Database\\Migrations\\CreateRemunerasiTable', 'default', 'App', 1770536390, 20),
(35, '2026-02-07-060611', 'App\\Database\\Migrations\\AddKinerjaColumnsToUsers', 'default', 'App', 1770536390, 20),
(36, '2026-02-07-060946', 'App\\Database\\Migrations\\AddAtasanToUsers', 'default', 'App', 1770536390, 20),
(37, '2026-02-08-073109', 'App\\Database\\Migrations\\CreateSkpTable', 'default', 'App', 1770536390, 20),
(38, '2026-02-08-074335', 'App\\Database\\Migrations\\CreateSkpHeaders', 'default', 'App', 1770536631, 21),
(39, '2026-02-08-081237', 'App\\Database\\Migrations\\AddFieldsToSkpTargets', 'default', 'App', 1770538591, 22),
(40, '2026-02-08-085653', 'App\\Database\\Migrations\\CreateMasterIndikator', 'default', 'App', 1770541030, 23),
(42, '2026-04-10-030246', 'App\\Database\\Migrations\\CreateUnitKerjaTable', 'default', 'App', 1775793092, 24),
(43, '2026-04-10-035010', 'App\\Database\\Migrations\\AddParentUnitToUnitKerja', 'default', 'App', 1775793141, 25);

-- --------------------------------------------------------

--
-- Struktur dari tabel `remunerasi`
--

CREATE TABLE `remunerasi` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `tahun` int NOT NULL,
  `bulan` int NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_by_user_id` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rencana_kinerja`
--

CREATE TABLE `rencana_kinerja` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `sasaran_program` text NOT NULL,
  `indikator_kinerja` text NOT NULL,
  `satuan` varchar(100) NOT NULL,
  `target_utama` varchar(255) NOT NULL,
  `kegiatan` text NOT NULL,
  `target_bulanan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `realisasi_bulanan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `tahun_anggaran` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `rencana_kinerja`
--

INSERT INTO `rencana_kinerja` (`id`, `user_id`, `sasaran_program`, `indikator_kinerja`, `satuan`, `target_utama`, `kegiatan`, `target_bulanan`, `realisasi_bulanan`, `tahun_anggaran`) VALUES
(17, 5, 'Meningkatnya SDM transportasi yang kompeten', 'IKK 1 Tingkat Penyerapan Diklat Pembentukan SDM Transportasi Darat/Laut/Udara yang Berkompetensi', 'Laporan', '12', 'penggunaan lab cbt', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"2\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(18, 5, 'Meningkatnya SDM transportasi yang kompeten', 'IKK 1 Tingkat Penyerapan Diklat Pembentukan SDM Transportasi Darat/Laut/Udara yang Berkompetensi', 'Laporan', '12', 'bantuan teknis', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"10\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(19, 5, 'Meningkatnya SDM transportasi yang kompeten', 'IKK 1 Tingkat Penyerapan Diklat Pembentukan SDM Transportasi Darat/Laut/Udara yang Berkompetensi', 'Laporan', '12', 'setting alat kegiatan hybrid atau online', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(20, 5, 'Meningkatnya SDM transportasi yang kompeten', 'IKK 1 Tingkat Penyerapan Diklat Pembentukan SDM Transportasi Darat/Laut/Udara yang Berkompetensi', 'Laporan', '12', 'tersedianya jaringan internet di pktj', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(22, 15, 'Meningkatnya Kualitas Pelayanan, Pengembangan, Pendidikan dan Pelatihan Transportasi Darat', 'IKK 5 Tingkat Pemenuhan SDM Transportasi Program Pembentukan', 'Laporan', '12', 'Pendampingan Kegiatan Taruna di dalam kampus', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"1\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(23, 6, 'Meningkatnya Kompetensi ASN Transportasi', 'IKK 3 Tingkat Pemenuhan ASN Transportasi Program Pembentukan', 'Laporan', '12', 'Terwujudnya organisasi yang agile dan SDM unggul', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(24, 7, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 2 Persentase Peserta Diklat Transportasi', '%', '100', 'Jumlah peserta diklat transportasi pada tahun 2024', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\"]', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"\"]', 2025),
(25, 6, 'Meningkatnya Kompetensi ASN Transportasi', 'IKK 3 Tingkat Pemenuhan ASN Transportasi Program Pembentukan', 'Laporan', '12', 'Terwujudnya pemenuhan ASN transportasi program pembentukan', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(26, 7, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 1 Tingkat Penyerapan Diklat Pembentukan SDM Transportasi Darat/Laut/Udara yang Berkompetensi', '%', '100', 'Terpenuhinya tingkat penyerapan diklat pembentukan SDM Transportasi Darat', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\"]', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"\"]', 2025),
(27, 6, 'Meningkatnya Kompetensi ASN Transportasi', 'IKK 4 Tingkat Pemenuhan ASN Transportasi Program Pelatihan', 'Laporan', '12', 'Terwujudnya pemenuhan ASN transportasi program pelatihan', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(28, 7, 'Meningkatnya Akreditasi Lembaga Pendidikan Vokasi, Sertifikasi Pelatihan dan Tenaga Kerja Sektor Transportasi Darat', 'IKK 7 Tingkat Pemenuhan Akreditasi dan Sertifikasi', '%', '100', 'Terpenuhinya Akreditasi dan Sertifikasi Lembaga', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\"]', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"\"]', 2025),
(29, 6, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 5 Tingkat Pemenuhan SDM Transportasi Program Pembentukan', 'Dokumentasi', '12', 'Terwujudnya kegiatan perkuliahan, bimbingan praktik kerja lapangan/magang dan tugas akhir/skripsi taruna/mahasiswa', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(30, 6, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 5 Tingkat Pemenuhan SDM Transportasi Program Pembentukan', 'Dokumentasi', '12', 'Terwujudnya tugas koordinasi dan monitoring penyelenggaraan pendidikan dan pembinaan taruna/mahasiswa', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(31, 10, 'Terwujudnya Organisasi yang Agile dan SDM Unggul', 'IKK 3 Tingkat Pemenuhan ASN Transportasi Program Pembentukan', 'Indeks', '65', 'Indeks Profesional ASN', '[\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\"]', '[\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"\"]', 2025),
(32, 7, 'Meningkatnya Akreditasi Lembaga Pendidikan Vokasi, Sertifikasi Pelatihan dan Tenaga Kerja Sektor Transportasi Darat', 'IKK 12 Persentase kualitas dan kuantitas dosen', '%', '100', 'Realisasi rasio dosen terhadap taruna diklat pembentukan', '[\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\"]', '[\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"5\",\"\"]', 2025),
(33, 10, 'Terwujudnya Birokrasi yang Akuntabel dan Berorientasi pada Layanan Prima', 'IKK 23 Indikator Kinerja Pelaksanaan Anggaran (IKPA)', 'Nilai', '90', 'Indikator Kinerja Pelaksanaan Anggaran (IKPA)', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\"]', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"\"]', 2025),
(34, 7, 'Meningkatnya Akreditasi Lembaga Pendidikan Vokasi, Sertifikasi Pelatihan dan Tenaga Kerja Sektor Transportasi Darat', 'IKK 9 Persentase penelitian, HAKI dan Produk Inovasi Dosen dan Mahasiswa', '%', '100', 'Realisasi jumlah penelitian yang dihasilkan', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\"]', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"\"]', 2025),
(35, 10, 'Terwujudnya Birokrasi yang Akuntabel dan Berorientasi pada Layanan Prima', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', '%', '100', 'Persentase Pemenuhan Akuntabilitas di Lingkungan PKTJ', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\"]', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"\"]', 2025),
(36, 7, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 6 Tingkat Pemenuhan SDM Transportasi Program Pelatihan', '%', '100', 'Lulusan pelatihan Pada Tahun 2025', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\"]', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"\"]', 2025),
(37, 10, 'Terwujudnya Birokrasi yang Akuntabel dan Berorientasi pada Layanan Prima', 'IKK 23 Indikator Kinerja Pelaksanaan Anggaran (IKPA)', 'Rupiah', '38125408200', 'Realisasi Pendapatan BLU', '[\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\"]', '[\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"\"]', 2025),
(41, 14, 'Tercapainya Persentase Kerjasama di Bidang Pendidikan dan Pelatihan Dengan Stakeholder Transportasi dan Pendidikan', 'IKK 14 Persentase Kerjasama di bidang Pendidikan dan pelatihan dengan stakeholder transportasi dan pendidikan', 'Dokumentasi', '1', 'Penatausahaan dokumen di SPM rapi dan akuntabel', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(42, 14, 'Tercapainya Indeks Efisiensi Peningkatan Layanan BLU', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Kuantitas', '80', 'Nilai indeks kepuasan masyarakat terhadap institusi', '[\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"0\"]', '[\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"80\",\"\"]', 2025),
(43, 14, 'Tercapainya Modernisasi Pengelolaan BLU', 'IKK 23 Indikator Kinerja Pelaksanaan Anggaran (IKPA)', 'Dokumen', '1', 'Jumlah dokumen audit mutu internal di PKTJ', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(44, 14, 'Tercapainya Indeks Profesionalisme ASN', 'IKK 20 Indeks Pemenuhan SDM BPSDMP', 'Kuantitas', '3', 'Jumlah dokumen akreditasi prodi di PKTJ', '[\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"0\"]', '[\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"3\",\"\"]', 2025),
(45, 9, 'Terwujudnya Birokrasi yang Akuntabel dan Berorientasi pada Layanan Prima', 'IKK 23 Indikator Kinerja Pelaksanaan Anggaran (IKPA)', 'Laporan', '12', 'Tersusunya Dokumen RPD setiap bulan', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(46, 9, 'Meningkatnya SDM transportasi yang kompeten', 'IKK 24 Nilai SAKIP BPSDMP', 'Dokumen', '5', 'Tersusunya Laporan Keuangan PKTJ', '[\"1\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"1\",\"0\",\"0\",\"1\",\"1\"]', '[\"1\",\"\",\"1\",\"\",\"\",\"\",\"\",\"1\",\"\",\"\",\"1\",\"\"]', 2025),
(47, 9, 'Meningkatnya SDM transportasi yang kompeten', 'IKK 24 Nilai SAKIP BPSDMP', 'Laporan', '12', 'Tersusunnya Laporan Monitoring Evaluasi Rencana Aksi', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(48, 9, 'Meningkatnya Kompetensi ASN Transportasi', 'IKK 24 Nilai SAKIP BPSDMP', 'Laporan', '12', 'Tersusunnya Laporan Realisasi Capaian KPI', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(49, 9, 'Meningkatnya Kompetensi ASN Transportasi', 'IKK 24 Nilai SAKIP BPSDMP', 'Laporan', '12', 'Tersusunnya Laporan Monitoring dan Evaluasi Keuangan', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(50, 9, 'Meningkatnya Kompetensi ASN Transportasi', 'IKK 24 Nilai SAKIP BPSDMP', 'Laporan', '12', 'Tersusunnya Laporan Capaian Kinerja PKTJ', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\"]', 2025),
(51, 9, 'Meningkatnya SDM transportasi yang kompeten', 'IKK 24 Nilai SAKIP BPSDMP', 'Dokumen', '4', 'Tersusunnya Dokumen RKA- KL PKTJ', '[\"1\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"1\",\"1\"]', '[\"1\",\"\",\"1\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(52, 9, 'Meningkatnya Kompetensi ASN Transportasi', 'IKK 23 Indikator Kinerja Pelaksanaan Anggaran (IKPA)', '%', '100', 'Tercapaianya Realisasi Belanja Anggaran BLU', '[\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\"]', '[\"8\",\"9\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"8\",\"\"]', 2025),
(53, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Laporan', '1', 'Mengawasi setiap kegiatan Taruna/Perwira Siswa mulai dan membina kedisplinan serta ketertiban Taruna/Perwira Siswa', '[\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"\"]', 2025),
(54, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Laporan', '1', 'Mengawasi, memonitor dan mengevaluasi kehidupan taruna di asrama dan kehidupan perwira Siswa diluar asrama serta membuat usulan point positif bagi Taruna yang berprestasi dan point negatif atau sanksi bagi taruna yang melanggar poltibtar daan ketentuan lain.', '[\"0\",\"0\",\"0\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"0\",\"\",\"\",\"\",\"1\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"]', 2025),
(55, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Laporan', '1', 'Mengawasi dan memantau kehidupan, kondisi kesehatan dan permasalahan-permasalahan / kendala yang dihadapi Taruna di asrama', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"0\",\"\",\"\",\"\",\"\",\"1\",\"\",\"\",\"\",\"\",\"\",\"\"]', 2025),
(57, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Dokumen', '1', 'Menyusun rencana, jadwal, program atau materi latihan ekstrakurikuler seni dan menyiapkan bahan pembinaan di bidang seni tiap-tiap Taruna sehingga dapat dikembangkan lebih lanjut', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\",\"\",\"\",\"\",\"\"]', 2025),
(58, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Dokumen', '1', 'Menyusun rencana kegiatan bahan sikap mental jiwa korsa dan keagamaan', '[\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"\"]', 2025),
(59, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Laporan', '1', 'Mengawasi setiap kegiatan Taruna/Perwira Siswa mulai dan membina kedisiplinan serta ketertiban Taruna/Perwira Siswa', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"1\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(60, 8, 'Meningkatnya SDM transportasi yang kompeten', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Laporan', '0', 'Mengawasi, memonitor dan mengevaluasi kehidupan taruna di asrama dan kehidupan perwira Siswa diluar asrama serta membuat usulan point positif bagi Taruna yang berprestasi dan point negatif atau sanksi bagi taruna yang melanggar poltibtar daan ketentuan lain.', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"]', 2025),
(61, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Laporan', '1', 'Mengawasi dan memantau kehidupan, kondisi kesehatan dan permasalahan-permasalahan / kendala yang dihadapi Taruna di asrama', '[\"0\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"0\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"\"]', 2025),
(62, 9, 'Meningkatnya Kinerja Pengelolaan Keuangan Efektif, Efisien, dan Akuntabel', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', 'Dokumen', '4', 'Jumlah dokumen bahan rencana dan program', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(63, 9, 'Meningkatnya Kinerja Pengelolaan Keuangan Efektif, Efisien, dan Akuntabel', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', 'Dokumen', '12', 'Jumlah Laporan menyusun bahan pengelola keuangan', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(64, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Dokumen', '1', 'Menyusun rencana, jadwal, program atau materi latihan ekstrakurikuler seni dan menyiapkan bahan pembinaan di bidang seni tiap-tiap Taruna sehingga dapat dikembangkan lebih lanjut', '[\"0\",\"0\",\"0\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"0\",\"0\",\"0\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"\",\"\"]', 2025),
(65, 9, 'Meningkatnya Kinerja Pengelolaan Keuangan Efektif, Efisien, dan Akuntabel', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', 'Dokumen', '1', 'Jumlah dokumen pengesahan belanja blu', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(66, 9, 'Meningkatnya Kinerja Pengelolaan Keuangan Efektif, Efisien, dan Akuntabel', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', 'Dokumen', '2', 'Jumlah Dokumen', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(67, 9, 'Meningkatnya Kinerja Pengelolaan Keuangan Efektif, Efisien, dan Akuntabel', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', '%', '100', 'Prosentase capaian dokumen bahan rencana dan program', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"8\",\"\"]', 2025),
(68, 9, 'Tercapainya Persentase Kerjasama di Bidang Pendidikan dan Pelatihan Dengan Stakeholder Transportasi dan Pendidikan', 'IKK 24 Nilai SAKIP BPSDMP', 'Laporan', '1', 'Tersusunya Laporan Kinerja Instansi Pemerintah', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(69, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Dokumen', '0', 'Menyusun jadwal, program atau materi latihan ekstrakurikuler olahraga dan menyusun program dan jadwal tes kesamaptaan Taruna secara berkala serta mengevaluasi hasil kesamaptaan Taruna', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"\"]', 2025),
(70, 8, 'Meningkatnya SDM transportasi yang kompeten', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Dokumen', '1', 'Menyusun rencana kegiatan bahan sikap mental jiwa korsa dan keagamaan', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"1\",\"0\",\"0\"]', '[\"\",\"0\",\"\",\"\",\"\",\"\",\"\",\"\",\"0\",\"1\",\"\",\"\"]', 2025),
(71, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Laporan', '1', 'Mengelola kegiatan masa dasar pembentukan karakter (Madatukar), masa dasar pembinaan mental(Madabintal ) dan masa pemantapan pembinaan mental (matapbintal)', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"1\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\",\"\",\"\"]', 2025),
(72, 9, 'Meningkatnya Kinerja Pengelolaan Keuangan Efektif, Efisien, dan Akuntabel', 'IKK 23 Indikator Kinerja Pelaksanaan Anggaran (IKPA)', 'Laporan', '12', 'Tersusunya Dokumen RPD setiap bulan', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(73, 9, 'Tercapainya Indeks Efisiensi Peningkatan Layanan BLU', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', 'Dokumen', '12', 'Tercapaiya Realiasai Pendapatan dari Optimalisasi Aset', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(74, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Laporan', '1', 'Melaksanakan tugas kedinasan lain yang diperintahkan oleh pimpinan baik secara tertulis maupun lisan', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\",\"\",\"\",\"\"]', 2025),
(75, 9, 'Meningkatnya Akreditasi Lembaga Pendidikan Vokasi, Sertifikasi Pelatihan dan Tenaga Kerja Sektor Transportasi Darat', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', 'Dokumen', '1', 'Jumlah Laporan', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(76, 9, 'Tercapainya Persentase Kerjasama di Bidang Pendidikan dan Pelatihan Dengan Stakeholder Transportasi dan Pendidikan', 'IKK 23 Indikator Kinerja Pelaksanaan Anggaran (IKPA)', '%', '100', 'Tercapainya Realisasi Belanja Anggaran RM', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"8\",\"\"]', 2025),
(77, 9, 'Tercapainya Modernisasi Pengelolaan BLU', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Laporan', '12', 'Tercapaiya Target Realisasi Pendapatan BLU Setiap Bulan', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025),
(78, 8, 'Meningkatnya Kompetensi SDM Transportasi', 'IKK 13 Tingkat Kualitas Layanan Pendidikan dan Pelatihan SDM Transportasi Darat', 'Dokumen', '1', 'Menyusun rencana, jadwal, program atau materi latihan ekstrakurikuler seni dan menyiapkan bahan pembinaan di bidang seni tiap-tiap Taruna sehingga dapat dikembangkan lebih lanjut', '[\"0\",\"0\",\"0\",\"1\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"0\",\"1\",\"\",\"0\",\"0\",\"\",\"0\",\"0\",\"0\",\"\"]', 2025),
(79, 9, 'Tercapainya Modernisasi Pengelolaan BLU', 'IKK 9 Persentase penelitian, HAKI dan Produk Inovasi Dosen dan Mahasiswa', '%', '100', 'TercapaiyaModernisasiPengelolaanBLU', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"8\",\"\"]', 2025),
(80, 9, 'Tercapainya Persentase Kerjasama di Bidang Pendidikan dan Pelatihan Dengan Stakeholder Transportasi dan Pendidikan', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', '%', '100', 'Presentase capaian dokumen pengelolaan keuangan', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"8\",\"\"]', 2025),
(81, 9, 'Tercapainya Persentase Kerjasama di Bidang Pendidikan dan Pelatihan Dengan Stakeholder Transportasi dan Pendidikan', 'IKK 22 Persentase Pemenuhan Akuntabilitas di Lingkungan BPSDMP', '%', '100', 'Laporan Tepat Waktu', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"1\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"1\",\"\"]', 2025);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sasaran`
--

CREATE TABLE `sasaran` (
  `id` int UNSIGNED NOT NULL,
  `nama_sasaran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `sasaran`
--

INSERT INTO `sasaran` (`id`, `nama_sasaran`) VALUES
(2, 'Meningkatnya SDM transportasi yang kompeten'),
(3, 'Meningkatnya Kompetensi ASN Transportasi'),
(4, 'Meningkatnya Kompetensi SDM Transportasi'),
(5, 'Meningkatnya Akreditasi Lembaga Pendidikan Vokasi, Sertifikasi Pelatihan dan Tenaga Kerja Sektor Transportasi Darat'),
(6, 'Meningkatnya Kualitas Pelayanan, Pengembangan, Pendidikan dan Pelatihan Transportasi Darat'),
(7, 'Terwujudnya Organisasi yang Agile dan SDM Unggul'),
(8, 'Terwujudnya Birokrasi yang Akuntabel dan Berorientasi pada Layanan Prima'),
(9, 'Meningkatnya Kinerja Pengelolaan Keuangan Efektif, Efisien, dan Akuntabel'),
(10, 'Tercapainya Persentase Kerjasama di Bidang Pendidikan dan Pelatihan Dengan Stakeholder Transportasi dan Pendidikan'),
(11, 'Tercapainya Indeks Efisiensi Peningkatan Layanan BLU'),
(12, 'Melakukan Koordinasi Terkait ke TUan di Lingkungan PKTJ'),
(13, 'Tercapainya Modernisasi Pengelolaan BLU'),
(14, 'Tercapainya Indeks Profesionalisme ASN'),
(15, 'Tercapainya Penyelenggaraan Manajemen SDM yang Efektif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `satuan`
--

CREATE TABLE `satuan` (
  `id` int UNSIGNED NOT NULL,
  `nama_satuan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `satuan`
--

INSERT INTO `satuan` (`id`, `nama_satuan`) VALUES
(1, '%'),
(2, 'Laporan'),
(3, 'Indeks'),
(4, 'Nilai'),
(5, 'Dokumentasi'),
(6, 'Rupiah'),
(7, 'Kuantitas'),
(8, 'Dokumen');

-- --------------------------------------------------------

--
-- Struktur dari tabel `skp_headers`
--

CREATE TABLE `skp_headers` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `tahun` year NOT NULL,
  `model_skp` enum('Kuantitatif','Kualitatif') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Kuantitatif',
  `periode_awal` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `status` enum('Draft','Diajukan','Disetujui') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Draft',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `skp_targets`
--

CREATE TABLE `skp_targets` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `skp_header_id` int UNSIGNED DEFAULT NULL,
  `tahun` year NOT NULL,
  `jenis` enum('Utama','Tambahan') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Utama',
  `rhk_pimpinan` text COLLATE utf8mb4_general_ci,
  `rencana_kinerja` text COLLATE utf8mb4_general_ci NOT NULL,
  `aspek` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `indikator` text COLLATE utf8mb4_general_ci NOT NULL,
  `target` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `satuan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `unit_kerja`
--

CREATE TABLE `unit_kerja` (
  `id` int UNSIGNED NOT NULL,
  `nama_unit` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `parent_unit` enum('aak','kuk') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Penanggung jawab utama (AAK atau KUK)',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `unit_kerja`
--

INSERT INTO `unit_kerja` (`id`, `nama_unit`, `parent_unit`, `created_at`, `updated_at`) VALUES
(1, 'Unit Teknik Informatika', 'aak', '2026-04-10 03:55:45', '2026-04-10 03:55:45'),
(2, 'Tim Substansi Bidang Umum', 'kuk', '2026-04-10 08:45:53', '2026-04-10 08:45:53'),
(3, 'Tim Substansi Bidang Keuangan', 'kuk', '2026-04-10 08:46:23', '2026-04-10 08:46:23'),
(4, 'Tim Substansi Bidang Kerjasama', 'kuk', '2026-04-10 08:46:43', '2026-04-10 08:46:43'),
(5, 'Tim Substansi Bidang Administrasi Akademik', 'aak', '2026-04-10 08:47:00', '2026-04-10 08:47:00'),
(6, 'Tim Substansi Bidang Administrasi Ketarunaan dan Alumni', 'aak', '2026-04-10 08:47:33', '2026-04-10 08:47:33'),
(7, 'Satuan Pemeriksaan Internal', 'kuk', '2026-04-10 08:47:49', '2026-04-10 08:47:49'),
(8, 'Satuan Penjaminan Mutu', 'kuk', '2026-04-10 08:47:56', '2026-04-10 08:47:56'),
(9, 'Pusat Penelitian dan Pengabdian Masyarakat', 'kuk', '2026-04-10 08:48:15', '2026-04-10 08:48:15'),
(10, 'Pusat Pengembangan Karakter', 'aak', '2026-04-10 08:48:22', '2026-04-10 08:48:22'),
(11, 'Prodi TRO', 'aak', '2026-04-10 08:48:31', '2026-04-10 08:48:31'),
(12, 'Prodi RSTJ', 'aak', '2026-04-10 08:48:38', '2026-04-10 08:48:38'),
(13, 'Prodi TO', 'aak', '2026-04-10 08:48:47', '2026-04-10 08:48:47'),
(14, 'Unit Laboratorium', 'aak', '2026-04-10 08:49:13', '2026-04-10 08:49:13'),
(15, 'Unit Perpustakaan', 'aak', '2026-04-10 08:49:29', '2026-04-10 08:49:29'),
(16, 'Unit Bahasa', 'aak', '2026-04-10 08:49:37', '2026-04-10 08:49:37'),
(17, 'Unit Pengembangan Usaha', 'kuk', '2026-04-10 08:50:01', '2026-04-10 08:50:01'),
(18, 'Unit Asrama', 'kuk', '2026-04-10 08:50:15', '2026-04-10 08:50:15'),
(19, 'Unit Kesehatan', 'kuk', '2026-04-10 08:50:26', '2026-04-10 08:50:26'),
(20, 'Pokja Diklat', 'aak', '2026-04-10 08:50:41', '2026-04-10 08:50:41'),
(21, 'Pokja Humas dan PPID', 'kuk', '2026-04-10 08:50:48', '2026-04-10 08:50:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `atasan_id` int UNSIGNED DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `pangkat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `atasan_id`, `foto`, `nip`, `jabatan`, `unit`, `pangkat`) VALUES
(1, 'admin', '$2y$10$UkLmczVlKdYiRFLjnVqyHenXPX026.PvzPzlb8iN1Uy8oKpx7ODsG', 'Administrator Utama', 'admin@simonik.com', 'admin', NULL, 'default.png', '', '', '', ''),
(8, 'diklatpktj', '$2y$10$DMeeWEKxZD5Z9w.r4Ed1Pez0w1Qia.x2q0f7IOAX8st4Xt3.tV.W.', 'Pokja Diklat', 'diklat@pktj.ac.id', 'user', 12, 'default.png', '', '', '', ''),
(9, 'keuanganpktj', '$2y$10$iJegL2gtWXf36zF.qKnLBuqLhojF8S/5l9SnzzcLH6W3pixOGAFVu', 'Keuangan PKTJ', 'keuangan@pktj.ac.id', 'kuk', NULL, 'default.png', NULL, NULL, NULL, NULL),
(13, 'kukpktj', '$2y$10$0B2mb/Vfm2QWNwXp/2Q6jOPqxi0rkGaNdsV6TROVYApC6/p6gDtoq', 'Kabag KUK', 'kukpktj@pktj.ac.id', 'kabag_kuk', 50, 'default.png', '', '', '', ''),
(14, 'spmpktj', '$2y$10$kWb9DPxTDUawqQzyLObLIuPngSFju0PU4V2dN/E58ILB8SDDNzvJG', 'SPM PKTJ', 'spm@pktj.ac.id', 'spm', NULL, 'default.png', NULL, NULL, NULL, NULL),
(18, '198008012008121001', '0527794b060365bd33c5572ecbebf8cc', 'AGUS HARIYANTO', '198008012008121001@pktj.ac.id', 'kabag_kuk', 50, NULL, '198008012008121001', 'Kepala Bagian Keuangan, Umum, Kerjasama', 'Bagian Keuangan, Umum, Kerjasama', ''),
(20, '199710162020122005', 'cdbf4732b3922aa3c918b47e98bc71dc', 'GHINA KHAIRUNISSA', '199710162020122005@pktj.ac.id', 'user', 0, NULL, '199710162020122005', '', '', ''),
(21, '199807242022101001', 'c3df22b55ae6153c8a560a12b0c1b73e', 'ILHAM BAGUS PRASETYO', '199807242022101001@pktj.ac.id', 'user', 173, NULL, '199807242022101001', '', '', ''),
(22, '199910162022102001', 'fe602edc5c0ac1c36f482715603a8ee7', 'NORMALITA TRI WIDYASTUTI', '199910162022102001@pktj.ac.id', 'user', 0, NULL, '199910162022102001', '', '', ''),
(23, '200001282022102001', '8f7da64dd68c616155fcaee502d8baf8', 'DYAH PANGESTU SUHARTATI', '200001282022102001@pktj.ac.id', 'user', 0, NULL, '200001282022102001', '', '', ''),
(24, '200007292022102001', '8b2f4d5023b4f19c8370d8e74aa4d6bc', 'META HARISMA NOR AINI', '200007292022102001@pktj.ac.id', 'user', 0, NULL, '200007292022102001', '', '', ''),
(25, '198307032006041001', '2d51335eb61f893733840f1cac53fff6', 'ALAN JULIARAMA TEDJAKUSUMAH', '198307032006041001@pktj.ac.id', 'user', 0, NULL, '198307032006041001', '', 'Tim Substansi Bidang Umum', ''),
(26, '199907122023102001', 'b6f8f53008f4ed59bd20a6c21d236008', 'NADILLA EKA KUSUMAWATI', '199907122023102001@pktj.ac.id', 'user', 0, NULL, '199907122023102001', '', '', ''),
(27, '200010252023101001', '24e92386898863da2e59be6ff7900f38', 'MUHAMMAD ISRO`RISQI', '200010252023101001@pktj.ac.id', 'user', 173, NULL, '200010252023101001', '', '', ''),
(28, '200009172022101001', '0870b322722d3b9e93904820b41a5f89', 'MUHAMMAD AL ALI FAIZAL', '200009172022101001@pktj.ac.id', 'user', 0, NULL, '200009172022101001', '', '', ''),
(29, '199704202018122003', '646383ccfea1273fb77d4ce022bdcdd5', 'SALLY APRIYANTI NASUTION', '199704202018122003@pktj.ac.id', 'user', 0, NULL, '199704202018122003', '', '', ''),
(30, '198707212010121005', 'f19544c77cb6d11fc5d2bd0ba0123a65', 'FARIZQI', '198707212010121005@pktj.ac.id', 'user', 173, NULL, '198707212010121005', '', '', ''),
(31, '199711102020121004', 'e1e473b0ced29190157ee474e403aff9', 'BAGUS IKAPRAJA', '199711102020121004@pktj.ac.id', 'user', 173, NULL, '199711102020121004', '', '', ''),
(32, '197708312007121001', '551c5c393d27fe0a3ff2225d6d82d901', 'ANZAL BURHANI HAQQI', '197708312007121001@pktj.ac.id', 'user', 0, NULL, '197708312007121001', '', '', ''),
(33, '196910152012121002', 'e2096629b369a3d87efe8138100ef6e6', 'TUGAS BUDIARTO', '196910152012121002@pktj.ac.id', 'user', 0, NULL, '196910152012121002', '', '', ''),
(34, '197707292012121002', '85afaecf189045eb6b6b97da2c7a9a9c', 'SURATMAN', '197707292012121002@pktj.ac.id', 'user', 0, NULL, '197707292012121002', '', '', ''),
(35, '197808242012121004', '1a1892e03330e7bc083e6680f5a5f3d0', 'NOWO SAKTI WIBOWO', '197808242012121004@pktj.ac.id', 'user', 173, NULL, '197808242012121004', '', '', ''),
(36, '199505182022031013', 'c4f85adc28677d94230166ea3661963e', 'MUHAMAD FAHRI AZIZI', '199505182022031013@pktj.ac.id', 'user', 173, NULL, '199505182022031013', '', '', ''),
(37, '199505222022032017', '246be18cfab77f01cca0280d095d64bc', 'PUTRI MEINDRI PERMATASARI', '199505222022032017@pktj.ac.id', 'user', 0, NULL, '199505222022032017', '', '', ''),
(38, '199807172020121005', '77664c5158f5f181840f1125579be91e', 'MUHAMAD YULI  DWI RIVANGI', '199807172020121005@pktj.ac.id', 'user', 173, NULL, '199807172020121005', '', '', ''),
(39, '200001312021121001', '829417b794053f7c859a576504bf8127', 'ARIK WIJAYA', '200001312021121001@pktj.ac.id', 'user', 0, NULL, '200001312021121001', '', '', ''),
(40, '200011102023102001', '18357aea2a45e6332b21ca7e3c77a38f', 'DENISYA HADDAD FIRDAUS', '200011102023102001@pktj.ac.id', 'user', 173, NULL, '200011102023102001', '', '', ''),
(41, '199901062024121001', '674227b5700abdcf78e0078d6a0c50f8', 'RIFQI JINAN ALBADI', '199901062024121001@pktj.ac.id', 'user', 18, NULL, '199901062024121001', '', 'Tim Substansi Bidang Keuangan', ''),
(42, '196802071990031012', '9ee039fedd49e6997cef59b81d342c0c', 'EDI PURWANTO', '196802071990031012@pktj.ac.id', 'manajemen', 50, NULL, '196802071990031012', 'Wakil Direktur 1', '', ''),
(43, '196606011991031004', '89761696506df0c04b45a3e03057f917', 'SUGIANTO', '196606011991031004@pktj.ac.id', 'manajemen', 50, NULL, '196606011991031004', 'Wakil Direktur 2', '', ''),
(44, '196502201988031007', '458f66ae957fc32e5bb06f40358662c5', 'BUANG TURASNO', '196502201988031007@pktj.ac.id', 'user', 0, NULL, '196502201988031007', '', '', ''),
(45, '198111182008122002', 'd214712c5364c3f7a2dc47b33ca495cc', 'INDRI PUSPITASARI', '198111182008122002@pktj.ac.id', 'user', 0, NULL, '198111182008122002', '', '', ''),
(46, '196212181989031006', 'fd72a0736c1ba842120023ee338c327c', 'GUNAWAN', '196212181989031006@pktj.ac.id', 'user', 0, NULL, '196212181989031006', '', '', ''),
(47, '196603261986031007', 'ff9f3cf535e4790640392e49e9a3b3ca', 'AGUS BUDI PURWANTORO', '196603261986031007@pktj.ac.id', 'user', 0, NULL, '196603261986031007', '', '', ''),
(48, '197006041996031002', '71eb14122bdb11c5023aab21eb4b7a76', 'MOHAMAD HERMAWAN', '197006041996031002@pktj.ac.id', 'user', 0, NULL, '197006041996031002', '', '', ''),
(49, '197005191993011001', 'c03be0d9e481bbf9a4621a1fb0244d35', 'HANENDYO PUTRO', '197005191993011001@pktj.ac.id', 'user', 0, NULL, '197005191993011001', '', '', ''),
(50, '197307011996021002', 'e6aa1c30351a38622b00030e87a1b460', 'BAMBANG ISTIYANTO', '197307011996021002@pktj.ac.id', 'manajemen', NULL, '1776056545_e5aa8872b3b475849e96.png', '197307011996021002', 'Direktur', '', ''),
(51, '197311092005022001', '43029d75ee00afbb970fe0aef7c863a2', 'NOVITASARI TRI NUGRAH ENI', '197311092005022001@pktj.ac.id', 'user', 0, NULL, '197311092005022001', '', '', ''),
(52, '197302052005021001', '2b19c53d1db36f134485c1e8c1764dce', 'CORSINUS TRISNO SUSANTO', '197302052005021001@pktj.ac.id', 'user', 173, NULL, '197302052005021001', '', '', ''),
(53, '197411292006041001', '5564f5aa6e28fed743f4b077e5382907', 'R.ARIEF NOVIANTO', '197411292006041001@pktj.ac.id', 'user', 173, NULL, '197411292006041001', '', '', ''),
(54, '197411261998031001', '754cf3f24efea9d901a5ad2db801e725', 'SOLEKHUDIN', '197411261998031001@pktj.ac.id', 'user', 0, NULL, '197411261998031001', '', '', ''),
(55, '198105222008121002', '6840a457fb50c0988687de6755f18727', 'SETYA WIJAYANTA', '198105222008121002@pktj.ac.id', 'manajemen', 50, NULL, '198105222008121002', 'Wakil Direktur 2', '', ''),
(56, '198002022008122001', '8f74ce498b2cb0188e29b047d42ee055', 'NAOMI SRIE KUSUMASTUTIE', '198002022008122001@pktj.ac.id', 'user', 0, NULL, '198002022008122001', '', '', ''),
(57, '198208132003121003', 'c404f23a23f3d7996b4bd2a409c8c3d9', 'SETIA HADI PRAMUDI', '198208132003121003@pktj.ac.id', 'manajemen', 18, NULL, '198208132003121003', '', 'Satuan Penjaminan Mutu', ''),
(58, '197102191991031001', '072a9a28a7eddfb73a3aa12a98dafe24', 'SUGIYARSO', '197102191991031001@pktj.ac.id', 'user', 173, NULL, '197102191991031001', '', '', ''),
(59, '198305042008121001', '81eb6ae231768e828f5da0e126e3a3b8', 'ANTON BUDIHARJO', '198305042008121001@pktj.ac.id', 'user', 0, NULL, '198305042008121001', '', '', ''),
(60, '198110132005021001', '15e70cec4b2cadddcf885dde8585240a', 'KARYONO', '198110132005021001@pktj.ac.id', 'user', 0, NULL, '198110132005021001', '', '', ''),
(61, '197510282008121002', '9bea5d71d1d361d35bfd6e054f09401c', 'NANANG OKTA WIDIANDARU', '197510282008121002@pktj.ac.id', 'user', 0, NULL, '197510282008121002', '', '', ''),
(62, '198409232008121002', 'de2380166a8662faea6c3c82bbf42e8d', 'ALFAN BAHARUDDIN', '198409232008121002@pktj.ac.id', 'user', 0, NULL, '198409232008121002', '', '', ''),
(63, '198311072008122002', 'b5a5cb8e2de56caa1c4fa3cd59f6ee74', 'SUPRIATIN', '198311072008122002@pktj.ac.id', 'user', 173, NULL, '198311072008122002', '', '', ''),
(64, '198501072008121003', 'c25bfb6e1bd3d8b9e866329227231dee', 'SUGIYARTO', '198501072008121003@pktj.ac.id', 'user', 173, NULL, '198501072008121003', '', '', ''),
(65, '198401262008121001', 'e5e67dfe0d8ed13c9a561efcba7a9ae0', 'GUNAWAN', '198401262008121001@pktj.ac.id', 'user', 173, NULL, '198401262008121001', '', '', ''),
(66, '198307042009121004', '4a8866216e4b9b1b626f65320919c46a', 'ERY MUTHORIQ', '198307042009121004@pktj.ac.id', 'user', 0, NULL, '198307042009121004', '', '', ''),
(67, '197702192003121001', '363defe126361462d264b9039635e881', 'KUNTO ANDHITO', '197702192003121001@pktj.ac.id', 'user', 0, NULL, '197702192003121001', '', '', ''),
(68, '198401302009121002', '51a32d29d8a2eeacecb91bb2cb6f2df6', 'ERMAWAN PRASETYA', '198401302009121002@pktj.ac.id', 'user', 0, NULL, '198401302009121002', '', '', ''),
(69, '198409282009121004', '358006196592289c7d7349680d822aa6', 'IRFAN TRIANDANA', '198409282009121004@pktj.ac.id', 'user', 0, NULL, '198409282009121004', '', '', ''),
(70, '198504072009122003', '4da02092b52cc2c5ec135c41deb850f9', 'IMA NATRIA', '198504072009122003@pktj.ac.id', 'user', 173, NULL, '198504072009122003', '', '', ''),
(71, '198301312009121004', 'b1d8a03202019cc43de334f4e16f3258', 'EDI SUTRISNO', '198301312009121004@pktj.ac.id', 'user', 0, NULL, '198301312009121004', '', '', ''),
(72, '198507012008121002', '949c0f190de9813240bd2cc206cb2398', 'AJI RONALDO', '198507012008121002@pktj.ac.id', 'user', 173, NULL, '198507012008121002', '', 'Tim Substansi Bidang Administrasi Ketarunaan dan Alumni', ''),
(73, '197601041998031001', '3290160c862f6caf10ed58771d3e15a2', 'SUPARTONO', '197601041998031001@pktj.ac.id', 'user', 173, NULL, '197601041998031001', '', '', ''),
(74, '198506052008122002', 'ab7cb372fed192ae80bb8044e7fed118', 'PIPIT RUSMANDANI', '198506052008122002@pktj.ac.id', 'user', 173, NULL, '198506052008122002', '', '', ''),
(75, '198006022009121001', 'e175684d7c272a7d2ad06e7a41595afd', 'ETHYS PRANOTO', '198006022009121001@pktj.ac.id', 'user', 173, NULL, '198006022009121001', '', '', ''),
(76, '198503112008121004', '6132edfff8b10a0a228f36f3a5b32076', 'FAHRIZAL ADHIKRISNA', '198503112008121004@pktj.ac.id', 'user', 0, NULL, '198503112008121004', '', '', ''),
(77, '198805282019021002', '791a84bc82095f6b11c38882116a0d89', 'JOKO SISWANTO', '198805282019021002@pktj.ac.id', 'user', 57, NULL, '198805282019021002', '', 'Satuan Penjaminan Mutu', ''),
(78, '198908222019021001', 'a1188516719869b8cd9a6b5fcb721242', 'MOKHAMMAD RIFQI TSANI', '198908222019021001@pktj.ac.id', 'manajemen', 173, NULL, '198908222019021001', '', '', ''),
(79, '199301042019021002', '2c3bada0cd26f95e2dbef5abc87989ed', 'MUHAMMAD IMAN NUR HAKIM', '199301042019021002@pktj.ac.id', 'user', 173, NULL, '199301042019021002', '', '', ''),
(80, '199210092019021002', '5ac119e07726701a59ca47ef5c3913b8', 'MOCH. AZIZ KURNIAWAN', '199210092019021002@pktj.ac.id', 'user', 0, NULL, '199210092019021002', '', '', ''),
(81, '198309252008121001', '242fc07bdf10cabc0a7907342b8bf851', 'AHMAD BASUKI', '198309252008121001@pktj.ac.id', 'user', 0, NULL, '198309252008121001', '', '', ''),
(82, '198806052019021004', '58823f8c68529f259fb7f288c7dc3ce6', 'FRANS TOHOM', '198806052019021004@pktj.ac.id', 'user', 173, NULL, '198806052019021004', '', '', ''),
(83, '198806272019021001', '7e6ccd9df7ddec9839d165cb46e39a2b', 'AAT ESKA FAHMADI', '198806272019021001@pktj.ac.id', 'user', 173, NULL, '198806272019021001', '', 'Unit Bahasa', ''),
(84, '199110242019021002', 'd52cb16cd36efff0928a50b38979681e', 'YOGI OKTOPIANTO', '199110242019021002@pktj.ac.id', 'user', 173, NULL, '199110242019021002', '', '', ''),
(85, '199112052019021002', '7d275d5840458b86cb4d0eddb0b1cbab', 'SUPRAPTO HADI', '199112052019021002@pktj.ac.id', 'user', 173, NULL, '199112052019021002', '', '', ''),
(86, '198507162019021001', '344601f8f9483c564169da3ddc02304e', 'RIZA PHAHLEVI MARWANTO', '198507162019021001@pktj.ac.id', 'user', 173, NULL, '198507162019021001', '', '', ''),
(87, '198712092019021001', 'bdd93b3a690ca6922fa7dbff5bb0c5d1', 'BRASIE PRADANA SELA BUNGA RISKA AYU', '198712092019021001@pktj.ac.id', 'user', 0, NULL, '198712092019021001', '', '', ''),
(88, '198511282019021001', 'd82b7a7620b5a5ef97712a3a59c377ef', 'REZA YOGA ANINDITA', '198511282019021001@pktj.ac.id', 'user', 0, NULL, '198511282019021001', '', '', ''),
(89, '199104152019021005', 'b653a3ca1df05657d269d9c380b12e8d', 'RIZAL APRIANTO', '199104152019021005@pktj.ac.id', 'user', 173, NULL, '199104152019021005', '', '', ''),
(90, '198504152019021003', '1091bfe44a8c28148717a18e32b72dcf', 'RIFANO', '198504152019021003@pktj.ac.id', 'user', 173, NULL, '198504152019021003', '', '', ''),
(91, '198508122019021001', '0859890ba1e0b96e9bc74df62d329f41', 'RAKA PRATINDY', '198508122019021001@pktj.ac.id', 'user', 173, NULL, '198508122019021001', '', '', ''),
(92, '198909192019022001', 'e0ba2c2cb8efac2839502ed453ed9f96', 'SITI SHOFIAH', '198909192019022001@pktj.ac.id', 'user', 173, NULL, '198909192019022001', '', '', ''),
(93, '199006212019021001', '83b65abc63930505d5a26dfaa00a328a', 'HELMI WIBOWO', '199006212019021001@pktj.ac.id', 'user', 0, NULL, '199006212019021001', '', '', ''),
(94, '199104162019022002', '5ea5c7240c4f30590f13bec3d1642653', 'NURUL FITRIANI', '199104162019022002@pktj.ac.id', 'user', 173, NULL, '199104162019022002', '', '', ''),
(95, '199306172019022002', '8f7465d373e1652d36ceb3dcc131adc5', 'AINUN RAHMAWATI', '199306172019022002@pktj.ac.id', 'user', 0, NULL, '199306172019022002', '', 'Prodi RSTJ', ''),
(96, '199105132010121003', 'b1e276695af4a4a3cb0993b4b66655be', 'KORNELIUS JEPRIADI', '199105132010121003@pktj.ac.id', 'user', 0, NULL, '199105132010121003', '', '', ''),
(97, '198707052019021003', '84b9ab16cfb96f3e5ddfc89ff867f0c0', 'SRIANTO', '198707052019021003@pktj.ac.id', 'user', 0, NULL, '198707052019021003', '', '', ''),
(98, '198710042019021001', '4d4d5a765268d13a4027b2405950496e', 'ABDUL HARIS FIRMANSYAH', '198710042019021001@pktj.ac.id', 'user', 0, NULL, '198710042019021001', '', '', ''),
(99, '199309072019021001', '3c3b380a4f9fbb486c43bacdad94c345', 'LANGGENG ASMORO', '199309072019021001@pktj.ac.id', 'user', 173, NULL, '199309072019021001', '', '', ''),
(100, '198402292019021001', '2d38e985ce359ee6c92ffa73436016a9', 'DWI WAHYU HIDAYAT', '198402292019021001@pktj.ac.id', 'user', 173, NULL, '198402292019021001', '', '', ''),
(101, '199406302019022007', '09de66fb3a5136b37f42b5e19a500f0a', 'JUNIANA PASARIBU', '199406302019022007@pktj.ac.id', 'user', 0, NULL, '199406302019022007', '', '', ''),
(102, '199011102019021002', '8c5c4e0b32eb4e9d35c2bf79a41d15e8', 'FARIS HUMAMI', '199011102019021002@pktj.ac.id', 'user', 173, NULL, '199011102019021002', '', '', ''),
(103, '198804192010121003', '6538281b448d3a5f6768187c7adc55e7', 'AJIE SETIAWAN', '198804192010121003@pktj.ac.id', 'user', 0, NULL, '198804192010121003', '', 'Pokja Humas dan PPID', ''),
(104, '198912272010122002', '1c3bcb7794fe85b9745a2fceb640741c', 'DESTRIA RAHMITA', '198912272010122002@pktj.ac.id', 'user', 173, NULL, '198912272010122002', '', '', ''),
(105, '198408162008121002', '435fa192f35ba82a5aeefe9314886444', 'AGUS HARRY SETIAWAN', '198408162008121002@pktj.ac.id', 'user', 18, NULL, '198408162008121002', '', 'Satuan Pemeriksaan Internal', ''),
(106, '198505162009031006', 'af7305ebbcf624b06401157af37b4840', 'SIHAR AMBARITA', '198505162009031006@pktj.ac.id', 'user', 0, NULL, '198505162009031006', '', '', ''),
(107, '198907142009121003', '4acc59e682cdfa828e05691c3c28a617', 'JOHAN HERMAWAN', '198907142009121003@pktj.ac.id', 'user', 173, NULL, '198907142009121003', '', '', ''),
(108, '198312282008122001', 'd794d77e66d07e29af00e686cf23fe06', 'DESSY ASTRIANI', '198312282008122001@pktj.ac.id', 'user', 0, NULL, '198312282008122001', '', '', ''),
(109, '199009102019021005', '8084c20d12b01fddcb9fab57b59c922f', 'ANDI YUSUF DAULAY', '199009102019021005@pktj.ac.id', 'user', 0, NULL, '199009102019021005', '', '', ''),
(110, '199403102022031011', '4e787cd2e04b4abf65ad2ac20aae3529', 'RAMADHAN DWI PRASETYO', '199403102022031011@pktj.ac.id', 'user', 173, NULL, '199403102022031011', '', '', ''),
(111, '198106182009121001', 'e9ca9b9fcbdd99b3f58fe38e9a8fe95a', 'SYAIBANI IKHSAN', '198106182009121001@pktj.ac.id', 'user', 173, NULL, '198106182009121001', '', '', ''),
(112, '198901282010121007', 'b60919ef56dd16c96220af930d6ac448', 'AGUNG BACHRUDIN', '198901282010121007@pktj.ac.id', 'user', 18, NULL, '198901282010121007', '', 'Unit Asrama', ''),
(113, '198208022010121004', 'e6e4d13094da4f5da90cfd9746c48b84', 'MUHAMMAD ABDUL MUHLIS', '198208022010121004@pktj.ac.id', 'user', 0, NULL, '198208022010121004', '', '', ''),
(114, '199303252019021001', 'f4a00ba7e418d3f30e0d14b017be1969', 'BAGUS RIYADI FITRIYAN', '199303252019021001@pktj.ac.id', 'user', 0, NULL, '199303252019021001', '', '', ''),
(115, '199502132019022003', '8f8c28d9c1a5c603adb91b0b0fb15285', 'ASTRI LESTARI', '199502132019022003@pktj.ac.id', 'user', 0, NULL, '199502132019022003', '', '', ''),
(116, '198705282008121001', '9d7fca87f3ea92d5f67cd31654db6d44', 'VINNO EL TOSI', '198705282008121001@pktj.ac.id', 'user', 0, NULL, '198705282008121001', '', '', ''),
(117, '198512122010121005', '73e2baa437eac79e35a4aa054fecae74', 'BAREG SEPTY MARTINDO', '198512122010121005@pktj.ac.id', 'user', 173, NULL, '198512122010121005', '', '', ''),
(118, '198909242010121002', '06ea8069c6efb07bb4559a9c716bea7d', 'AGUNG BUDI DHARMAWAN', '198909242010121002@pktj.ac.id', 'user', 173, NULL, '198909242010121002', '', 'Pokja Diklat', ''),
(119, '199308022019022003', '5164ab88409a005a3608a18a0127c8ea', 'INAS FADIYAH HANIN', '199308022019022003@pktj.ac.id', 'user', 0, NULL, '199308022019022003', '', '', ''),
(120, '199501172019021002', 'cbad9063f1ac2dffb9c04f2a54cd4c86', 'ALIF ANGGRIAT', '199501172019021002@pktj.ac.id', 'user', 0, NULL, '199501172019021002', '', '', ''),
(121, '198912142019022006', 'b4910228a7f4ca07aa6a381e62256d4f', 'EVY PRIHANA', '198912142019022006@pktj.ac.id', 'user', 0, NULL, '198912142019022006', '', '', ''),
(122, '198204012009121001', 'e61aa1c92552bc6c9cc54858c0368876', 'SULIFAN NUR AZMI', '198204012009121001@pktj.ac.id', 'user', 0, NULL, '198204012009121001', '', '', ''),
(123, '198302152006041003', '62e806a1f05328327827c8297d7f2010', 'SABAR KRISTANTO', '198302152006041003@pktj.ac.id', 'user', 0, NULL, '198302152006041003', '', '', ''),
(124, '198907182011011003', '0e33a78823b2dbb2da75dff779e0d4ed', 'AHMAD ASHARI', '198907182011011003@pktj.ac.id', 'user', 18, NULL, '198907182011011003', '', 'Tim Substansi Bidang Umum', ''),
(125, '199205152015031003', '01f64903e0d72e99b7c245a9d4744f39', 'AHMAD FAUZI', '199205152015031003@pktj.ac.id', 'user', 0, NULL, '199205152015031003', '', 'Tim Substansi Bidang Keuangan', ''),
(126, '199310072020122009', '741c5c29d00c0c5b31d1aea7e022e033', 'AMALINA WINDA ILHAMI', '199310072020122009@pktj.ac.id', 'user', 0, NULL, '199310072020122009', '', '', ''),
(127, '199702182020121008', 'a9126a1eee693bb795856edc88d5f43a', 'MUHAMMAD REZA ARTHA NUGRAHA', '199702182020121008@pktj.ac.id', 'user', 173, NULL, '199702182020121008', '', '', ''),
(128, '197911242010122002', '3c26b92dace2d3e0480aedf9ab95ea9f', 'SAKEM NOVITA SARI', '197911242010122002@pktj.ac.id', 'user', 0, NULL, '197911242010122002', '', '', ''),
(129, '197609062009121001', 'f26593f6f4f74881ef3984f38255ee27', 'FATKHUROZAK', '197609062009121001@pktj.ac.id', 'user', 173, NULL, '197609062009121001', '', '', ''),
(130, '199506052022031019', '0c9afbcce0ddd5610bfde2f4b4500217', 'DARU ADE JUNIARTO', '199506052022031019@pktj.ac.id', 'user', 173, NULL, '199506052022031019', '', '', ''),
(131, '199704212022032017', 'e3f3de3c6c1c376067e2780afacad5ad', 'DIAJENG AYU DEWI PRITO', '199704212022032017@pktj.ac.id', 'user', 173, NULL, '199704212022032017', '', '', ''),
(132, '198104072023212005', 'b161fddaa063f2238101584bb15e5309', 'HENI PURWANTININGRUM', '198104072023212005@pktj.ac.id', 'user', 0, NULL, '198104072023212005', '', '', ''),
(133, '198408152023212004', 'f46143861fe51490e36f81a41f6dab43', 'SANTI IVANA', '198408152023212004@pktj.ac.id', 'user', 0, NULL, '198408152023212004', '', '', ''),
(134, '198905262023212002', 'd296debae9b6561ec4eb8a5a7b92a6f2', 'ATIN KHOLISAH', '198905262023212002@pktj.ac.id', 'user', 0, NULL, '198905262023212002', '', '', ''),
(135, '198806092023212028', 'c64b32c813318b5bed7768ca4cb280b4', 'DANI FITRIA BRILIANTI', '198806092023212028@pktj.ac.id', 'user', 173, NULL, '198806092023212028', '', '', ''),
(136, '199211102023212045', '234bfb68b811f3755aa049c1f7988d6f', 'ARINI NOVIATUL BAROROH', '199211102023212045@pktj.ac.id', 'user', 0, NULL, '199211102023212045', '', '', ''),
(137, '199212172023211017', 'd9fecd7a3af84aa2ba3636ac7778413d', 'BAGUS ARDI PRAYOGO', '199212172023211017@pktj.ac.id', 'user', 173, NULL, '199212172023211017', '', '', ''),
(138, '199401122023212029', '66b6762170f0fc518adbd590edddae57', 'EMA PRATAMI ROSYADA', '199401122023212029@pktj.ac.id', 'user', 173, NULL, '199401122023212029', '', '', ''),
(139, '199707152023211002', '8efe40a1430e511d21b4f73360034c20', 'GIGIH BAHTIAR TRIADY', '199707152023211002@pktj.ac.id', 'user', 0, NULL, '199707152023211002', '', '', ''),
(140, '199811252023211001', 'c3f33ea8f5168af27935efe9d3ab09af', 'MASRUKHIN', '199811252023211001@pktj.ac.id', 'user', 173, NULL, '199811252023211001', '', '', ''),
(141, '198601272023211016', 'bbfdd124a0682a3ebeac462cc9a12259', 'JONI HERIYANTO', '198601272023211016@pktj.ac.id', 'user', 173, NULL, '198601272023211016', '', '', ''),
(142, '198808042023211017', 'c9b564e10ac9f1143532a9cd2da7acfb', 'AJI PRAMUJO', '198808042023211017@pktj.ac.id', 'user', 173, NULL, '198808042023211017', '', 'Tim Substansi Bidang Administrasi Akademik', ''),
(143, '199010232023211024', '7ffccfd9bb46816c0e970df96ba84525', 'IBNU AFAN', '199010232023211024@pktj.ac.id', 'user', 0, NULL, '199010232023211024', '', 'Satuan Penjaminan Mutu', ''),
(144, '199206152023212050', '833755d06845759cdb403ac1d7317730', 'UMUL MU\'MININ', '199206152023212050@pktj.ac.id', 'user', 0, NULL, '199206152023212050', '', '', ''),
(145, '199505312023212038', '3dc5858d604c25e294519b8af75749df', 'FARAH MEIGHINA', '199505312023212038@pktj.ac.id', 'user', 0, NULL, '199505312023212038', '', '', ''),
(146, '199806112023211004', '708f2626a4234918e1a748d41c16e815', 'ARI REZA PRAKASA', 'ari@pktj.ac.id', 'user', 173, '1775787299_6d2401313512eaf5f6f8.png', '199806112023211004', 'Pranata Komputer Ahli Pertama', 'Unit Teknik Informatika', 'IX'),
(147, '199006082024212019', 'd3a381acf8fddaebceae3e69cb11ff16', 'DYAH AYU LARASATI', '199006082024212019@pktj.ac.id', 'user', 0, NULL, '199006082024212019', '', '', ''),
(148, '199201142024212015', '642a0b9587cb2824d0def9382048ce35', 'LURA SATIVA', '199201142024212015@pktj.ac.id', 'user', 0, NULL, '199201142024212015', '', '', ''),
(149, '199309152024212017', '4deae42d70f30ab44e898585a1e340b0', 'FRISCA LEVI INDRIYANA', '199309152024212017@pktj.ac.id', 'user', 0, NULL, '199309152024212017', '', '', ''),
(150, '199403222024212016', 'fecb1f7a1a7967d91e0f7f3a775ded00', 'SHELLA ARIES TANTIA YUSUF', '199403222024212016@pktj.ac.id', 'user', 173, NULL, '199403222024212016', '', '', ''),
(151, '199604272024211006', 'b7bda0fdc4b1fc26092d5cd81743fc8f', 'ARIP NUGRAHA', '199604272024211006@pktj.ac.id', 'user', 0, NULL, '199604272024211006', '', '', ''),
(152, '199606152024212024', '70cafb20d237cfd7e1d771e28ae4fb23', 'WINI PRIMADIANTI', '199606152024212024@pktj.ac.id', 'user', 0, NULL, '199606152024212024', '', '', ''),
(153, '200002232024212014', '4c641606680a9242b2085b5d35a5fd32', 'AISAH FARHANI', '200002232024212014@pktj.ac.id', 'user', 0, NULL, '200002232024212014', '', '', ''),
(154, '200003022024212008', 'd2b5d59e72a63f1e7dbf35e0b2a0381b', 'MIFTAH IMTI SHOLIKHAH', '200003022024212008@pktj.ac.id', 'user', 0, NULL, '200003022024212008', '', '', ''),
(155, '197905162025211020', '8475c980436b6f3b8eea33118813215c', 'RACHMAT MURDIANTO', '197905162025211020@pktj.ac.id', 'user', 173, NULL, '197905162025211020', '', '', ''),
(156, '197906162025211021', 'cc94a98f0fa56999d207749180be2203', 'LUKMAN HAKIM', '197906162025211021@pktj.ac.id', 'user', 0, NULL, '197906162025211021', '', '', ''),
(157, '198110072025212021', '0c8f8a29a5419de16424c7f62c821a5a', 'DHI ASTUTI RAHMAWATI DEWI', '198110072025212021@pktj.ac.id', 'user', 173, NULL, '198110072025212021', '', '', ''),
(158, '198305052025211027', 'a2189517c66c7bd9dd78aed56147df05', 'DIMAS TRI ADHITIAWARMAN', '198305052025211027@pktj.ac.id', 'user', 0, NULL, '198305052025211027', '', '', ''),
(159, '198402142025212016', 'f6ef6b8308f5872577d9738b3d94fe44', 'MULIANI CHAERUN NISA', '198402142025212016@pktj.ac.id', 'user', 0, NULL, '198402142025212016', '', '', ''),
(160, '198403032025212024', '6e195cfdbc7e87d0f4c0aa32a076f6ce', 'WARKONI', '198403032025212024@pktj.ac.id', 'user', 0, NULL, '198403032025212024', '', '', ''),
(161, '198608082025211042', 'f19c1cf9dd7127b5505e97664c07b01c', 'HERI PURNOMO', '198608082025211042@pktj.ac.id', 'user', 0, NULL, '198608082025211042', '', '', ''),
(162, '198710292025211026', 'b75a3033d61dc302238042fd997d0b1b', 'ZULFIKKAAR ARIF SETIAWAN', '198710292025211026@pktj.ac.id', 'user', 0, NULL, '198710292025211026', '', '', ''),
(163, '198805022025212029', 'f313e7ec112a4fd40e75f0e5cf173367', 'RAHMI NUR ARIFIANTI', '198805022025212029@pktj.ac.id', 'user', 0, NULL, '198805022025212029', '', '', ''),
(164, '198909082025211028', '1a12753d0835752b4376ab1fc978b092', 'SHOLEHUDIN', '198909082025211028@pktj.ac.id', 'user', 0, NULL, '198909082025211028', '', '', ''),
(165, '199108122025211022', '6a71411817f1b82d5e4529982d6848e4', 'AGUS SUTRISNO', '199108122025211022@pktj.ac.id', 'user', 173, NULL, '199108122025211022', '', 'Tim Substansi Bidang Administrasi Ketarunaan dan Alumni', ''),
(166, '199201042025211033', '3c8afc561415d0798765359af494a2c1', 'MUHAMMAD MAHFUD SETIAWAN YUSUF', '199201042025211033@pktj.ac.id', 'user', 0, NULL, '199201042025211033', '', '', ''),
(167, '199302082025211021', 'e5290c8ddf2c66bf033fc2f628fa7586', 'SUTOWO', '199302082025211021@pktj.ac.id', 'user', 0, NULL, '199302082025211021', '', '', ''),
(168, '199807172025211009', '0f4b7a071ca51c6f612eedb9bbfedd89', 'RIYAN DWI PRIHARTANTO', '199807172025211009@pktj.ac.id', 'user', 0, NULL, '199807172025211009', '', '', ''),
(169, '198012012025212019', 'c0ec235de71748b09caa53d2cf727ef3', 'DESSI ROSDIANA', '198012012025212019@pktj.ac.id', 'user', 173, NULL, '198012012025212019', '', '', ''),
(170, '198307052025212025', 'cefbd275e968910b13848e54b917dbe0', 'HERNI SETIYOWATI', '198307052025212025@pktj.ac.id', 'user', 0, NULL, '198307052025212025', '', '', ''),
(171, '198312292025212014', '2ec1a96dbcb7a6561d02b4c55f37dfc8', 'PIPIN WIDYASARI', '198312292025212014@pktj.ac.id', 'user', 173, NULL, '198312292025212014', '', '', ''),
(172, '199905292025211006', 'fc6ce807d9c8cb1ebb0c28d1734982b3', 'RADEN BERLIAWAN KARTIKA PUTRA', '199905292025211006@pktj.ac.id', 'user', 0, NULL, '199905292025211006', '', '', ''),
(173, '197402041997032005', '$2y$10$75N/KdQ2gldpBFtE3aUDB.qn7cgEZFPuGDFk6fgcpiksNtkZXWsLu', 'PRIMA ANNA MARIA GORETY CORNELIS', '197402041997032005@pktj.ac.id', 'kabag_aak', 50, NULL, '197402041997032005', 'Kabag AAK', 'Tim Substansi Bidang Administrasi Ketarunaan dan Alumni', '');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `indikator`
--
ALTER TABLE `indikator`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jadwal_diklat`
--
ALTER TABLE `jadwal_diklat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `led_criteria`
--
ALTER TABLE `led_criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `led_scores`
--
ALTER TABLE `led_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prodi_tahun_led_criteria_id` (`prodi`,`tahun`,`led_criteria_id`);

--
-- Indeks untuk tabel `led_standar`
--
ALTER TABLE `led_standar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `led_submissions`
--
ALTER TABLE `led_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_indikator`
--
ALTER TABLE `master_indikator`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `remunerasi`
--
ALTER TABLE `remunerasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_tahun_bulan` (`user_id`,`tahun`,`bulan`),
  ADD KEY `remunerasi_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indeks untuk tabel `rencana_kinerja`
--
ALTER TABLE `rencana_kinerja`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sasaran`
--
ALTER TABLE `sasaran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `skp_headers`
--
ALTER TABLE `skp_headers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `skp_headers_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `skp_targets`
--
ALTER TABLE `skp_targets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `unit_kerja`
--
ALTER TABLE `unit_kerja`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_unit` (`nama_unit`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `indikator`
--
ALTER TABLE `indikator`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `jadwal_diklat`
--
ALTER TABLE `jadwal_diklat`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `led_criteria`
--
ALTER TABLE `led_criteria`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=768;

--
-- AUTO_INCREMENT untuk tabel `led_scores`
--
ALTER TABLE `led_scores`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT untuk tabel `led_standar`
--
ALTER TABLE `led_standar`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `led_submissions`
--
ALTER TABLE `led_submissions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=601;

--
-- AUTO_INCREMENT untuk tabel `master_indikator`
--
ALTER TABLE `master_indikator`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `remunerasi`
--
ALTER TABLE `remunerasi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rencana_kinerja`
--
ALTER TABLE `rencana_kinerja`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT untuk tabel `sasaran`
--
ALTER TABLE `sasaran`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `skp_headers`
--
ALTER TABLE `skp_headers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `skp_targets`
--
ALTER TABLE `skp_targets`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `unit_kerja`
--
ALTER TABLE `unit_kerja`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `remunerasi`
--
ALTER TABLE `remunerasi`
  ADD CONSTRAINT `remunerasi_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `remunerasi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `skp_headers`
--
ALTER TABLE `skp_headers`
  ADD CONSTRAINT `skp_headers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
