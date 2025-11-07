-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Nov 2025 pada 03.47
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_simonik`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `indikator`
--

CREATE TABLE `indikator` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_indikator` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `nama_diklat` varchar(255) NOT NULL,
  `periode` varchar(255) NOT NULL,
  `jumlah_peserta` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `led_categories`
--

CREATE TABLE `led_categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `led_categories`
--

INSERT INTO `led_categories` (`id`, `nama_kategori`, `created_at`, `updated_at`) VALUES
(1, 'Kategori 1', '2025-10-27 13:53:17', '2025-10-27 13:53:17'),
(2, 'Kategori 2', '2025-10-27 13:53:21', '2025-10-27 13:53:21'),
(3, 'Ketegori 3', '2025-10-27 14:57:12', '2025-10-27 14:57:12'),
(4, 'Ketegori 4', '2025-10-27 14:57:14', '2025-10-27 14:57:14'),
(5, 'Ketegori 5', '2025-10-27 14:57:17', '2025-10-27 14:57:17'),
(6, 'Ketegori 6', '2025-10-27 14:57:18', '2025-10-27 14:57:18'),
(7, 'Kategori 7', '2025-10-28 01:09:32', '2025-10-28 01:09:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `led_criteria`
--

CREATE TABLE `led_criteria` (
  `id` int(11) UNSIGNED NOT NULL,
  `nomor_kriteria` varchar(50) NOT NULL COMMENT 'Nomor urut kriteria, cth: 1.1, 2.a',
  `nama_kriteria` text NOT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `role_assignment` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `led_criteria`
--

INSERT INTO `led_criteria` (`id`, `nomor_kriteria`, `nama_kriteria`, `kategori`, `role_assignment`, `created_at`, `updated_at`) VALUES
(271, '1', 'Kekhasan VMTS\r\n\r\nPernyataan VMTS yang unik dan spesifik sebagai identitas PT, UPPS, dan visi keilmuan program studi sebagai keunggulan kompetitif yang didukung dengan renstra dan kurikulum yang memadai. ', 'Kategori 1', 'aak', NULL, NULL),
(272, '2', 'Mekanisme penyusunan VMTS\r\n\r\nMekanisme dan keterlibatan pemangku kepentingan dalam penyusunan VMTS UPPS dan tujuan utama yang ingin dicapai dalam penyusunan visi keilmuan program studi  dengan mempertimbangkan kebutuhan masyarakat dan tantangan global.', 'Kategori 1', 'kuk', NULL, NULL),
(273, '3', 'Tingkat pemahaman dan pencapaian VMTS\r\n\r\nTingkat pemahaman dan pencapaian VMTS UPPS dan visi keilmuan program studi oleh seluruh pemangku kepentingan internal dan eksternal serta pencapaian konkret  jangka pendek dan jangka menengah yang telah ditetapkan dalam VMTS UPPS dan visi keilmuan program studi.', 'Kategori 2', 'aak', NULL, NULL),
(274, '4', 'Sistem Tata Pamong\r\n\r\nI. Kelengkapan struktur organisasi dan kebijakan operasional yang berpedoman pada statuta Perguruan Tinggi yang digunakan.\r\n\r\nII. Perwujudan Good University Governance mengacu pada sistem tata kelola yang efektif, transparan, dan akuntabel. ', 'Ketegori 4', 'aak', NULL, NULL),
(275, '5', 'Komitmen pimpinan dan kemampuan manajerial \r\n\r\nI. Pimpinan UPPS memiliki komitmen pada: \r\n(1) Visi dan tujuan organisasi; \r\n(2) Integritas dan transparansi; \r\n(3) Pengembangan sumber daya. \r\n\r\nII. Kemampuan manajerial pimpinan UPPS \r\n\r\n', 'Ketegori 3', 'kuk', NULL, NULL),
(276, '6', 'Kerja sama\r\n\r\nI. Relevansi kerja sama pendidikan, penelitian, dan PkM dengan visi UPPS serta visi keilmuan program studi.\r\n\r\nII. Kerja sama tingkat internasional, nasional, wilayah/lokal yang relevan dengan program studi dan dikelola oleh UPPS dalam 3 tahun terakhir.\r\n', 'Ketegori 3', NULL, NULL, NULL),
(277, '7', 'Pelaksanaan Kerja Sama\r\n\r\nUPPS memiliki bukti yang sahih terkait kerja sama yang telah memenuhi 3 aspek berikut: \r\n(1) Memberikan manfaat bagi program studi dalam pemenuhan proses pembelajaran, penelitian, PkM; \r\n(2) Memberikan peningkatan kinerja tridharma dan fasilitas pendukung program studi; dan \r\n(3) Memberikan kepuasan kepada mitra industri dan mitra kerja sama lainnya. ', 'Ketegori 6', NULL, NULL, NULL),
(278, '8', 'Pengelolaan keuangan\r\n\r\nUPPS memiliki praktik pengelolaan sumber daya keuangan secara efektif dan efisien.', 'Ketegori 5', 'kuk', NULL, NULL),
(279, '9', 'Biaya operasional, dana penelitian dan PkM\r\n\r\nBiaya Operasional pendidikan', 'Ketegori 6', 'kuk', NULL, NULL),
(280, '10', 'Dana penelitian DTPS.', 'Kategori 7', 'kuk', NULL, NULL),
(281, '11', 'Dana pengabdian kepada masyarakat', '', 'kuk', NULL, NULL),
(282, '12', 'Pemutakhiran kurikulum\r\n\r\nKeterlibatan pemangku kepentingan dalam proses evaluasi dan pemutakhiran kurikulum.', '', 'aak', NULL, NULL),
(283, '13', 'Profil lulusan dan CPL.\r\n\r\nI. Profil lulusan yang ditetapkan oleh Program Studi.\r\n\r\nII. Kesesuaian Profil lulusan dengan capaian pembelajaran (CPL)', '', 'aak', NULL, NULL),
(284, '14', 'Kesesuaian dan tinjauan CPL\r\n\r\nI. Kesesuaian CPL dengan standar kompetensi lulusan yang  mencakup: (1) Konsep  rekayasa terapan yang spesifik dengan disiplin ilmu terkait; (2) kemampuan teknis dan kemampuan beradaptasi dengan standar keteknikan dan Teknologi Baru; (3) Keterampilan komunikasi dan kemampuan kerja tim; (4) kepatuhan terhadap etika profesi.\r\n\r\nII. Proses tinjauan rutin CPL.', '', 'aak', NULL, NULL),
(285, '15', 'Rencana Proses Pembelajaran (RPS)\r\n\r\nI. Ketersediaan dan kelengkapan dokumen RPS yang terdiri dari:\r\n1. Nama program studi, nama dan kode mata kuliah, semester, sks, nama dosen pengampu;\r\n2. Capaian pembelajaran lulusan yang dibebankan pada capaian pembelajaran mata kuliah;\r\n3. Kemampuan akhir yang direncanakan pada tiap tahap pembelajaran untuk memenuhi capaian pembelajaran lulusan;\r\n4. Bahan kajian yang terkait dengan kemampuan yang akan dicapai;\r\n5. Metode pembelajaran;\r\n6. Waktu yang disediakan untuk mencapai kemampuan pada tiap tahap pembelajaran;\r\n7. Pengalaman belajar mahasiswa yang diwujudkan dalam deskripsi tugas yang harus dikerjakan oleh mahasiswa selama satu semester;\r\n8. Kriteria, indikator, dan bobot penilaian; dan\r\n9. Daftar referensi yang digunakan. \r\n\r\nII. Proses tinjauan rutin RPS', '', 'aak', NULL, NULL),
(286, '16', 'Proses Pembelajaran (PS)\r\n\r\nI. Proses pembelajaran untuk memastikan efektivitas, kualitas, dan keberhasilan pencapaian CPL.\r\n\r\nII. Tinjauan rutin proses pembelajaran.\r\n', '', 'aak', NULL, NULL),
(287, '17', 'Integrasi Penelitian dan PkM dalam pembelajaran.\r\n\r\nHasil Penelitian dan Pengabdian kepada Masyarakat (PkM) yang dijadikan sebagai bahan ajar minimal 10% dari mata kuliah inti Program Studi.', '', 'aak', NULL, NULL),
(288, '18', 'Pembelajaran yang dilaksanakan dalam bentuk penugasan, praktikum, praktik bengkel, atau praktik lapangan.', '', 'aak', NULL, NULL),
(289, '19', 'Basic Sciences dan Matematika\r\nKetersediaan mata kuliah basic sciences dan matematika.', '', 'aak', NULL, NULL),
(290, '20', 'Proyek rekayasa penciri bidang prodi (Capstone design)\n\nTerselenggaranya capstone design yang memiliki:\n(1) Panduan pelaksanaan.\n(2) Memiliki rumusan capaian pembelajaran mata kuliah.\n(3) Menggunakan standar-standar keteknikan dan batasan-batasan realistis berdasarkan pada pengetahuan dan keterampilan yang telah diperoleh di perkuliahan sebelumnya.\n(4) Mempunyai bukti sahih pelaksanaan.', NULL, NULL, NULL, NULL),
(291, '21', 'Suasana Akademik\r\n\r\nI. Pengelolaan suasana akademik\r\nII. Integritas dan kebebasan ilmiah: Kebebasan akademik, mimbar akademik dan otonomi keilmuan', '', 'aak', NULL, NULL),
(292, '22', 'Penelitian\n\nKesesuaian penelitian dalam mendukung VMTS UPPS dan visi keilmuan program studi yang mencakup unsur-unsur sebagai berikut: (1) UPPS memiliki peta jalan penelitian yang yang mendukung VMTS UPPS dan visi keilmuan  program studi: (2) Peta jalan memayungi tema penelitian dosen dan mahasiswa dalam mendukung pengembangan kapasitas dosen dan mahasiswa; (3) Melakukan evaluasi secara berkala untuk memastikan keselarasan dengan visi;  (4) memberikan dampak positif bagi masyarakat. ', NULL, NULL, NULL, NULL),
(293, '23', 'Penelitian DTPS yang sesuai dengan peta jalan penelitian dan  pelaksanaannya melibatkan mahasiswa program studi dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(294, '24', 'PkM\n\nKesesuaian PkM dalam mendukung VMTS UPPS dan visi keilmuan program studi yang mencakup unsur-unsur sebagai berikut: \n(1) UPPS memiliki peta jalan PkM yang yang mendukung VMTS UPPS dan visi keilmuan program studi; \n(2) Peta jalan yang memayungi tema PkM dosen dan mahasiswa dalam mendukung pengembangan kapasitas dosen dan mahasiswa; \n(3) Melakukan evaluasi secara berkala untuk memastikan keselarasan dengan visi; \n(4) memberikan dampak positif bagi masyarakat.', NULL, NULL, NULL, NULL),
(295, '25', 'PkM DTPS yang sesuai dengan peta jalan PkM dan pelaksanaannya melibatkan mahasiswa program studi dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(296, '26', 'Profil Dosen\n\nKecukupan jumlah DTPS.', NULL, NULL, NULL, NULL),
(297, '27', 'Kualifikasi akademik DTPS.', NULL, NULL, NULL, NULL),
(298, '28', 'Jabatan akademik DTPS.', NULL, NULL, NULL, NULL),
(299, '29', 'Sertifikasi kompetensi/profesi/industri DTPS', '', 'aak', NULL, NULL),
(300, '30', 'Keterlibatan dosen industri/praktisi.', NULL, NULL, NULL, NULL),
(301, '31', 'Tenaga Kependidikan\n\nKualifikasi dan kecukupan laboran/teknisi/administrator sistem untuk mendukung proses pembelajaran sesuai dengan kebutuhan program studi.\n', NULL, NULL, NULL, NULL),
(302, '32', 'Beban kerja DTPS\n\nRerata Beban Kerja (RBK) DTPS.', NULL, NULL, NULL, NULL),
(303, '33', 'Kegiatan penelitian DTPS yang mendukung visi UPPS dan visi keilmuan program studi dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(304, '34', 'Kegiatan PkM DTPS yang mendukung visi UPPS dan visi keilmuan program studi dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(305, '35', 'Pagelaran/pameran/presentasi/publikasi ilmiah dengan tema yang mendukung visi keilmuan program studi yang dihasilkan DTPS dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(306, '36', 'Luaran penelitian dan PkM yang mendukung visi UPPS dan visi keilmuan program studi yang dihasilkan DTPS dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(307, '37', 'Produk/jasa yang diadopsi oleh industri/masyarakat terhadap jumlah dosen tetap dalam 3 tahun  terakhir.', NULL, NULL, NULL, NULL),
(308, '38', 'Persentase DTPS yang memiliki karya ilmiah sebagai penulis pertama dan/atau penulis korespondensi dalam mendukung keunggulan kompetitif UPPS dan Program studi.', NULL, NULL, NULL, NULL),
(309, '39', 'Persentase Karya ilmiah Bereputasi (PKIB) DTPS pada jurnal bereputasi atau publikasi dalam prosiding internasional ber-ISSN/ISBN terindeks Scopus/IEEE Explore/SPIE yang disitasi dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(310, '40', 'Persentase DTPS yang memiliki pengakuan/rekognisi sesuai bidang ilmu', NULL, NULL, NULL, NULL),
(311, '41', 'Sarana dan Prasarana\n\nI. Kecukupan dan mutu sarana dan prasarana untuk mendukung kegiatan akademik\n\nII. Kecukupan dan mutu sarana dan prasarana untuk mendukung kegiatan non akademik\n', NULL, NULL, NULL, NULL),
(312, '42', 'Keselamatan Kesehatan Kerja dan Lingkungan (K3L)\n\nKeselamatan Kesehatan Kerja dan Lingkungan (K3L) yang meliputi: \n(1) UPPS memiliki kebijakan dan tata kelola K3L yang mencakup komitmen untuk memenuhi peraturan K3L; \n(2) Fasilitas K3L; \n(3) Bukti sahih pelaksanaan K3L; dan \n(4) Tinjauan secara berkala K3L dan pelaksanaannya.', NULL, NULL, NULL, NULL),
(313, '43', 'Rasio jumlah mahasiswa program studi terhadap jumlah DTPS.', NULL, NULL, NULL, NULL),
(314, '44', 'Mahasiswa Asing\n\nPersentase Mahasiswa Asing (PMA).\n', NULL, NULL, NULL, NULL),
(315, '45', 'IPK lulusan.\n', NULL, NULL, NULL, NULL),
(316, '46', 'I. Prestasi mahasiswa di bidang akademik dalam 5 tahun terakhir.\n\nII. Prestasi mahasiswa di bidang non akademik dalam 5 tahun terakhir.\n', NULL, NULL, NULL, NULL),
(317, '47', 'Produk/jasa karya mahasiswa, yang dihasilkan secara mandiri atau bersama DTPS, yang diadopsi oleh industri/masyarakat dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(318, '48', 'Masa studi.', NULL, NULL, NULL, NULL),
(319, '49', 'Persentase Kelulusan tepat waktu (PTW)', NULL, NULL, NULL, NULL),
(320, '50', 'Pagelaran / pameran / presentasi / publikasi ilmiah mahasiswa, yang dihasilkan secara mandiri atau bersama DTPS dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(321, '51', 'Luaran penelitian dan PkM yang dihasilkan mahasiswa, baik secara mandiri atau bersama DTPS dalam 3 tahun terakhir.', NULL, NULL, NULL, NULL),
(322, '52', 'Tracer Study\n\nPelaksanaan tracer study yang mencakup 5 aspek sebagai berikut: \n1) pelaksanaan tracer study terkoordinasi di tingkat PT,\n2) kegiatan tracer study dilakukan secara reguler setiap tahun dan terdokumentasi,\n3) isi kuesioner mencakup seluruh pertanyaan inti tracer study DIKTI.\n4) ditargetkan pada seluruh populasi (lulusan TS-4 s.d. TS-2),\n5) hasilnya disosialisasikan dan digunakan untuk pengembangan kurikulum dan pembelajaran. ', NULL, NULL, NULL, NULL),
(323, '53', 'Waktu Tunggu', NULL, NULL, NULL, NULL),
(324, '54', 'Kesesuaian bidang kerja', NULL, NULL, NULL, NULL),
(325, '55', 'Tingkat dan ukuran tempat kerja lulusan\n\nTingkat dan ukuran tempat kerja lulusan di tingkat internasional, nasional, dan wilayah/lokal.', NULL, NULL, NULL, NULL),
(326, '56', 'Tingkat kepuasan pengguna lulusan.', NULL, NULL, NULL, NULL),
(327, '57', 'Keberadaan unit penjaminan mutu dan komitmen pimpinan\n\nI. Keberadaan unit penjaminan mutu UPPS dan komitmen pimpinan dengan keberadaan 4 aspek: \n(1) Dokumen legal pembentukan unsur pelaksana penjaminan mutu; \n(2) Dokumen legal bahwa auditor bersifat independen; \n(3) Dokumen pelaksanaan audit mutu internal; \n(4) Dokumen Rapat Tinjauan Manajemen (RTM).\n\nII. Ketersediaan perangkat SPMI yang minimal mencakup: \n1. Kebijakan SPMI; \n2. Pedoman penerapan siklus PPEPP standar pendidikan tinggi dalam SPMI; \n3. Standar dan/atau kriteria, norma, acuan mutu penyelenggaraan pendidikan dan pengelolaan perguruan tinggi; dan \n4. Tata cara pendokumentasian implementasi SPMI, serta sistem penjaminan mutu memiliki pengakuan mutu dari lembaga audit eksternal, lembaga akreditasi, dan lembaga sertifikasi. \n\nTabel 7.a. LKPS.\n\n\n', NULL, NULL, NULL, NULL),
(328, '58', 'Indikator Kinerja Tambahan (IKT)\n\nIKT disusun sesuai dengan unsur: \n(1) Tujuan strategis organisasi; \n(2) Memberikan dampak positif dan terukur; \n(3) Menunjukkan daya saing internasional; \n(4) Telah diukur dan dianalisis untuk perbaikan UPPS dan Program studi. ', NULL, NULL, NULL, NULL),
(329, '59', 'Keterlaksanaan Penjaminan Mutu dan Audit Mutu Internal\n\nKeterlaksanaan Sistem Penjaminan Mutu Internal (SPMI) yang memenuhi aspek berikut: \n(1) Tersedianya dokumen IKU dan IKT Pendidikan, Penelitian dan PkM; \n(2)Terlaksananya siklus penjaminan mutu (siklus PPEPP); \n(3) Bukti sahih efektivitas pelaksanaan penjaminan mutu; \n(4)Tersedianya bukti peningkatan standar.', NULL, NULL, NULL, NULL),
(330, '60', 'Evaluasi Capaian Kinerja\n\nAnalisis ketercapaian atau ketidaktercapaian kinerja UPPS pada budaya, relevansi, akuntabilitas, dan diferensiasi misi yang memenuhi aspek: \n(1) Penggunaan metode yang tepat dalam mengukur kinerja; \n(2) Evaluasi indikator yang tidak tercapai dengan mencari akar masalah dan faktor pendukung ketercapaian; \n(3) Dilakukan proses tinjauan rutin hasil pengukuran kinerja; \n(4) Hasil pengukuran kinerja disebarluaskan kepada pemangku kepentingan.', NULL, NULL, NULL, NULL),
(331, '61', 'Kepuasan Pemangku Kepentingan\n\nPengukuran kepuasan para pemangku kepentingan (mahasiswa, dosen, tenaga kependidikan, lulusan, pengguna, mitra industri, dan mitra lainnya) terhadap layanan manajemen, yang memenuhi aspek-aspek berikut: \n(1) Menggunakan instrumen kepuasan yang sahih, andal, mudah digunakan; \n(2) Dilaksanakan secara berkala, serta datanya terekam secara komprehensif; \n(3) Dianalisis dengan metode yang tepat serta bermanfaat untuk pengambilan keputusan; \n(4) Tingkat kepuasan dan umpan balik ditindaklanjuti untuk perbaikan dan peningkatan mutu luaran secara berkala dan tersistem; \n(5) Dilakukan review terhadap pelaksanaan pengukuran kepuasan dosen dan mahasiswa, serta\n(6) Hasilnya dipublikasikan dan mudah diakses oleh dosen dan mahasiswa.', NULL, NULL, NULL, NULL),
(332, '62', 'Analisis Lingkungan Eksternal dalam Pengembangan UPPS dan Prodi serta analisis SWOT\r\n\r\nI. Analisis lingkungan eksternal dalam pengembangan UPPS dan Program Studi.\r\n\r\nII. Ketepatan analisis SWOT yang mengacu pada lingkungan eksternal dan analisis SWOT setiap kriteria.', '', 'kuk', NULL, NULL),
(333, '63', 'Tujuan Strategis Pengembangan\n\nKetepatan di dalam menetapkan tujuan strategis pengembangan.', NULL, NULL, NULL, NULL),
(334, '64', 'Program Pengembangan Berkelanjutan\r\n\r\nUPPS memiliki kebijakan, ketersediaan sumber daya, kemampuan melaksanakan, dan kerealistikan program pengembangan berkelanjutan.', '', 'kuk', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `led_scores`
--

CREATE TABLE `led_scores` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `tahun` int(4) NOT NULL,
  `led_criteria_id` int(11) UNSIGNED NOT NULL,
  `skor` decimal(5,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `led_scores`
--

INSERT INTO `led_scores` (`id`, `user_id`, `prodi`, `tahun`, `led_criteria_id`, `skor`, `created_at`, `updated_at`) VALUES
(1, 1, 'RSTJ', 2025, 271, 100.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(2, 1, 'RSTJ', 2025, 272, 30.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(3, 1, 'RSTJ', 2025, 273, 40.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(4, 1, 'RSTJ', 2025, 274, 20.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(5, 1, 'RSTJ', 2025, 275, 10.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(6, 1, 'RSTJ', 2025, 276, 20.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(7, 1, 'RSTJ', 2025, 277, 30.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(8, 1, 'RSTJ', 2025, 278, 30.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(9, 1, 'RSTJ', 2025, 279, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(10, 1, 'RSTJ', 2025, 280, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(11, 1, 'RSTJ', 2025, 281, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(12, 1, 'RSTJ', 2025, 282, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(13, 1, 'RSTJ', 2025, 283, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(14, 1, 'RSTJ', 2025, 284, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(15, 1, 'RSTJ', 2025, 285, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(16, 1, 'RSTJ', 2025, 286, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(17, 1, 'RSTJ', 2025, 287, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(18, 1, 'RSTJ', 2025, 288, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(19, 1, 'RSTJ', 2025, 289, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(20, 1, 'RSTJ', 2025, 290, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(21, 1, 'RSTJ', 2025, 291, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(22, 1, 'RSTJ', 2025, 292, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(23, 1, 'RSTJ', 2025, 293, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(24, 1, 'RSTJ', 2025, 294, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(25, 1, 'RSTJ', 2025, 295, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(26, 1, 'RSTJ', 2025, 296, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(27, 1, 'RSTJ', 2025, 297, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(28, 1, 'RSTJ', 2025, 298, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(29, 1, 'RSTJ', 2025, 299, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(30, 1, 'RSTJ', 2025, 300, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(31, 1, 'RSTJ', 2025, 301, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(32, 1, 'RSTJ', 2025, 302, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(33, 1, 'RSTJ', 2025, 303, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(34, 1, 'RSTJ', 2025, 304, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(35, 1, 'RSTJ', 2025, 305, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(36, 1, 'RSTJ', 2025, 306, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(37, 1, 'RSTJ', 2025, 307, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(38, 1, 'RSTJ', 2025, 308, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(39, 1, 'RSTJ', 2025, 309, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(40, 1, 'RSTJ', 2025, 310, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(41, 1, 'RSTJ', 2025, 311, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(42, 1, 'RSTJ', 2025, 312, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(43, 1, 'RSTJ', 2025, 313, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(44, 1, 'RSTJ', 2025, 314, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(45, 1, 'RSTJ', 2025, 315, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(46, 1, 'RSTJ', 2025, 316, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(47, 1, 'RSTJ', 2025, 317, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(48, 1, 'RSTJ', 2025, 318, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(49, 1, 'RSTJ', 2025, 319, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(50, 1, 'RSTJ', 2025, 320, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(51, 1, 'RSTJ', 2025, 321, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(52, 1, 'RSTJ', 2025, 322, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(53, 1, 'RSTJ', 2025, 323, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(54, 1, 'RSTJ', 2025, 324, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(55, 1, 'RSTJ', 2025, 325, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(56, 1, 'RSTJ', 2025, 326, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(57, 1, 'RSTJ', 2025, 327, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(58, 1, 'RSTJ', 2025, 328, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(59, 1, 'RSTJ', 2025, 329, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(60, 1, 'RSTJ', 2025, 330, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(61, 1, 'RSTJ', 2025, 331, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(62, 1, 'RSTJ', 2025, 332, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(63, 1, 'RSTJ', 2025, 333, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16'),
(64, 1, 'RSTJ', 2025, 334, 0.00, '2025-10-27 15:43:02', '2025-10-28 01:15:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `led_submissions`
--

CREATE TABLE `led_submissions` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `tahun` int(4) NOT NULL,
  `led_criteria_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `kabag_approved` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Persetujuan oleh Kabag',
  `catatan` text DEFAULT NULL,
  `file_bukti` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `led_submissions`
--

INSERT INTO `led_submissions` (`id`, `user_id`, `prodi`, `tahun`, `led_criteria_id`, `status`, `kabag_approved`, `catatan`, `file_bukti`, `created_at`, `updated_at`) VALUES
(1, 12, 'RSTJ', 2025, 271, 'Tidak Ada', 0, 'https://drive.google.com/drive/folders/14DFfxDMwXo3MH69ypxhCUBgCkTkr4aMZ?usp=share_link', NULL, '2025-10-27 04:24:34', '2025-10-29 08:52:07'),
(2, 1, 'RSTJ', 2025, 272, 'Ada', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-27 14:58:54'),
(3, 12, 'RSTJ', 2025, 273, 'Tidak Ada', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-29 08:52:07'),
(4, 12, 'RSTJ', 2025, 274, 'Ada', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-29 08:52:07'),
(5, 1, 'RSTJ', 2025, 275, 'Terlampir', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-27 14:58:54'),
(6, 5, 'RSTJ', 2025, 276, 'Ada', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(7, 5, 'RSTJ', 2025, 277, 'Ada', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(8, 5, 'RSTJ', 2025, 278, 'Terlampir', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(9, 5, 'RSTJ', 2025, 279, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(10, 5, 'RSTJ', 2025, 280, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(11, 5, 'RSTJ', 2025, 281, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(12, 5, 'RSTJ', 2025, 282, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(13, 5, 'RSTJ', 2025, 283, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(14, 5, 'RSTJ', 2025, 284, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(15, 5, 'RSTJ', 2025, 285, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(16, 5, 'RSTJ', 2025, 286, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(17, 5, 'RSTJ', 2025, 287, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(18, 5, 'RSTJ', 2025, 288, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(19, 5, 'RSTJ', 2025, 289, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(20, 5, 'RSTJ', 2025, 290, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(21, 5, 'RSTJ', 2025, 291, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(22, 5, 'RSTJ', 2025, 292, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(23, 5, 'RSTJ', 2025, 293, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(24, 5, 'RSTJ', 2025, 294, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(25, 5, 'RSTJ', 2025, 295, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(26, 5, 'RSTJ', 2025, 296, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(27, 5, 'RSTJ', 2025, 297, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(28, 5, 'RSTJ', 2025, 298, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(29, 5, 'RSTJ', 2025, 299, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(30, 5, 'RSTJ', 2025, 300, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(31, 5, 'RSTJ', 2025, 301, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(32, 5, 'RSTJ', 2025, 302, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(33, 5, 'RSTJ', 2025, 303, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(34, 5, 'RSTJ', 2025, 304, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(35, 5, 'RSTJ', 2025, 305, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(36, 5, 'RSTJ', 2025, 306, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(37, 5, 'RSTJ', 2025, 307, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(38, 5, 'RSTJ', 2025, 308, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(39, 5, 'RSTJ', 2025, 309, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(40, 5, 'RSTJ', 2025, 310, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(41, 5, 'RSTJ', 2025, 311, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(42, 5, 'RSTJ', 2025, 312, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(43, 5, 'RSTJ', 2025, 313, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(44, 5, 'RSTJ', 2025, 314, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(45, 5, 'RSTJ', 2025, 315, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(46, 5, 'RSTJ', 2025, 316, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(47, 5, 'RSTJ', 2025, 317, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(48, 5, 'RSTJ', 2025, 318, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(49, 5, 'RSTJ', 2025, 319, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(50, 5, 'RSTJ', 2025, 320, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(51, 5, 'RSTJ', 2025, 321, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(52, 5, 'RSTJ', 2025, 322, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(53, 5, 'RSTJ', 2025, 323, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(54, 5, 'RSTJ', 2025, 324, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(55, 5, 'RSTJ', 2025, 325, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(56, 5, 'RSTJ', 2025, 326, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(57, 5, 'RSTJ', 2025, 327, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(58, 5, 'RSTJ', 2025, 328, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(59, 5, 'RSTJ', 2025, 329, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(60, 5, 'RSTJ', 2025, 330, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(61, 5, 'RSTJ', 2025, 331, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(62, 5, 'RSTJ', 2025, 332, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(63, 5, 'RSTJ', 2025, 333, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(64, 5, 'RSTJ', 2025, 334, '', 0, '', NULL, '2025-10-27 04:24:34', '2025-10-28 15:27:55'),
(65, 1, 'TRO', 2025, 271, 'Ada', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(66, 1, 'TRO', 2025, 272, 'Ada', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(67, 1, 'TRO', 2025, 273, 'Ada', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(68, 1, 'TRO', 2025, 274, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(69, 1, 'TRO', 2025, 275, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(70, 1, 'TRO', 2025, 276, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(71, 1, 'TRO', 2025, 277, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(72, 1, 'TRO', 2025, 278, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(73, 1, 'TRO', 2025, 279, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(74, 1, 'TRO', 2025, 280, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(75, 1, 'TRO', 2025, 281, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(76, 1, 'TRO', 2025, 282, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(77, 1, 'TRO', 2025, 283, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(78, 1, 'TRO', 2025, 284, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(79, 1, 'TRO', 2025, 285, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(80, 1, 'TRO', 2025, 286, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(81, 1, 'TRO', 2025, 287, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(82, 1, 'TRO', 2025, 288, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(83, 1, 'TRO', 2025, 289, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(84, 1, 'TRO', 2025, 290, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(85, 1, 'TRO', 2025, 291, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(86, 1, 'TRO', 2025, 292, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(87, 1, 'TRO', 2025, 293, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(88, 1, 'TRO', 2025, 294, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(89, 1, 'TRO', 2025, 295, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(90, 1, 'TRO', 2025, 296, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(91, 1, 'TRO', 2025, 297, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(92, 1, 'TRO', 2025, 298, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(93, 1, 'TRO', 2025, 299, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(94, 1, 'TRO', 2025, 300, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(95, 1, 'TRO', 2025, 301, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(96, 1, 'TRO', 2025, 302, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(97, 1, 'TRO', 2025, 303, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(98, 1, 'TRO', 2025, 304, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(99, 1, 'TRO', 2025, 305, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(100, 1, 'TRO', 2025, 306, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(101, 1, 'TRO', 2025, 307, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(102, 1, 'TRO', 2025, 308, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(103, 1, 'TRO', 2025, 309, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(104, 1, 'TRO', 2025, 310, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(105, 1, 'TRO', 2025, 311, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(106, 1, 'TRO', 2025, 312, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(107, 1, 'TRO', 2025, 313, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(108, 1, 'TRO', 2025, 314, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(109, 1, 'TRO', 2025, 315, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(110, 1, 'TRO', 2025, 316, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(111, 1, 'TRO', 2025, 317, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(112, 1, 'TRO', 2025, 318, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(113, 1, 'TRO', 2025, 319, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(114, 1, 'TRO', 2025, 320, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(115, 1, 'TRO', 2025, 321, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(116, 1, 'TRO', 2025, 322, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(117, 1, 'TRO', 2025, 323, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(118, 1, 'TRO', 2025, 324, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(119, 1, 'TRO', 2025, 325, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(120, 1, 'TRO', 2025, 326, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(121, 1, 'TRO', 2025, 327, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(122, 1, 'TRO', 2025, 328, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(123, 1, 'TRO', 2025, 329, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(124, 1, 'TRO', 2025, 330, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(125, 1, 'TRO', 2025, 331, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(126, 1, 'TRO', 2025, 332, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(127, 1, 'TRO', 2025, 333, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(128, 1, 'TRO', 2025, 334, '', 0, '', NULL, '2025-10-27 04:28:03', '2025-10-28 01:23:27'),
(129, 1, 'RSTJ', 2024, 271, 'Ada', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(130, 1, 'RSTJ', 2024, 272, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(131, 1, 'RSTJ', 2024, 273, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(132, 1, 'RSTJ', 2024, 274, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(133, 1, 'RSTJ', 2024, 275, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(134, 1, 'RSTJ', 2024, 276, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(135, 1, 'RSTJ', 2024, 277, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(136, 1, 'RSTJ', 2024, 278, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(137, 1, 'RSTJ', 2024, 279, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(138, 1, 'RSTJ', 2024, 280, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(139, 1, 'RSTJ', 2024, 281, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(140, 1, 'RSTJ', 2024, 282, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(141, 1, 'RSTJ', 2024, 283, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(142, 1, 'RSTJ', 2024, 284, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(143, 1, 'RSTJ', 2024, 285, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(144, 1, 'RSTJ', 2024, 286, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(145, 1, 'RSTJ', 2024, 287, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(146, 1, 'RSTJ', 2024, 288, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(147, 1, 'RSTJ', 2024, 289, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(148, 1, 'RSTJ', 2024, 290, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(149, 1, 'RSTJ', 2024, 291, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(150, 1, 'RSTJ', 2024, 292, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(151, 1, 'RSTJ', 2024, 293, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(152, 1, 'RSTJ', 2024, 294, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(153, 1, 'RSTJ', 2024, 295, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(154, 1, 'RSTJ', 2024, 296, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(155, 1, 'RSTJ', 2024, 297, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(156, 1, 'RSTJ', 2024, 298, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(157, 1, 'RSTJ', 2024, 299, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(158, 1, 'RSTJ', 2024, 300, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(159, 1, 'RSTJ', 2024, 301, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(160, 1, 'RSTJ', 2024, 302, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(161, 1, 'RSTJ', 2024, 303, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(162, 1, 'RSTJ', 2024, 304, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(163, 1, 'RSTJ', 2024, 305, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(164, 1, 'RSTJ', 2024, 306, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(165, 1, 'RSTJ', 2024, 307, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(166, 1, 'RSTJ', 2024, 308, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(167, 1, 'RSTJ', 2024, 309, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(168, 1, 'RSTJ', 2024, 310, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(169, 1, 'RSTJ', 2024, 311, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(170, 1, 'RSTJ', 2024, 312, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(171, 1, 'RSTJ', 2024, 313, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(172, 1, 'RSTJ', 2024, 314, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(173, 1, 'RSTJ', 2024, 315, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(174, 1, 'RSTJ', 2024, 316, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(175, 1, 'RSTJ', 2024, 317, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(176, 1, 'RSTJ', 2024, 318, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(177, 1, 'RSTJ', 2024, 319, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(178, 1, 'RSTJ', 2024, 320, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(179, 1, 'RSTJ', 2024, 321, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(180, 1, 'RSTJ', 2024, 322, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(181, 1, 'RSTJ', 2024, 323, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(182, 1, 'RSTJ', 2024, 324, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(183, 1, 'RSTJ', 2024, 325, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(184, 1, 'RSTJ', 2024, 326, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(185, 1, 'RSTJ', 2024, 327, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(186, 1, 'RSTJ', 2024, 328, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(187, 1, 'RSTJ', 2024, 329, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(188, 1, 'RSTJ', 2024, 330, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(189, 1, 'RSTJ', 2024, 331, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(190, 1, 'RSTJ', 2024, 332, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(191, 1, 'RSTJ', 2024, 333, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(192, 1, 'RSTJ', 2024, 334, '', 0, '', NULL, '2025-10-27 04:38:24', '2025-10-27 04:38:24'),
(193, 1, 'TO', 2025, 271, 'Tidak Ada', 0, 'lengkap', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(194, 1, 'TO', 2025, 272, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(195, 1, 'TO', 2025, 273, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(196, 1, 'TO', 2025, 274, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(197, 1, 'TO', 2025, 275, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(198, 1, 'TO', 2025, 276, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(199, 1, 'TO', 2025, 277, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(200, 1, 'TO', 2025, 278, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(201, 1, 'TO', 2025, 279, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(202, 1, 'TO', 2025, 280, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(203, 1, 'TO', 2025, 281, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(204, 1, 'TO', 2025, 282, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(205, 1, 'TO', 2025, 283, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(206, 1, 'TO', 2025, 284, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(207, 1, 'TO', 2025, 285, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(208, 1, 'TO', 2025, 286, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(209, 1, 'TO', 2025, 287, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(210, 1, 'TO', 2025, 288, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(211, 1, 'TO', 2025, 289, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(212, 1, 'TO', 2025, 290, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(213, 1, 'TO', 2025, 291, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(214, 1, 'TO', 2025, 292, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(215, 1, 'TO', 2025, 293, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(216, 1, 'TO', 2025, 294, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(217, 1, 'TO', 2025, 295, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(218, 1, 'TO', 2025, 296, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(219, 1, 'TO', 2025, 297, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(220, 1, 'TO', 2025, 298, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(221, 1, 'TO', 2025, 299, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(222, 1, 'TO', 2025, 300, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(223, 1, 'TO', 2025, 301, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(224, 1, 'TO', 2025, 302, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(225, 1, 'TO', 2025, 303, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(226, 1, 'TO', 2025, 304, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(227, 1, 'TO', 2025, 305, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(228, 1, 'TO', 2025, 306, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(229, 1, 'TO', 2025, 307, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(230, 1, 'TO', 2025, 308, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(231, 1, 'TO', 2025, 309, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(232, 1, 'TO', 2025, 310, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(233, 1, 'TO', 2025, 311, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(234, 1, 'TO', 2025, 312, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(235, 1, 'TO', 2025, 313, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(236, 1, 'TO', 2025, 314, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(237, 1, 'TO', 2025, 315, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(238, 1, 'TO', 2025, 316, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(239, 1, 'TO', 2025, 317, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(240, 1, 'TO', 2025, 318, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(241, 1, 'TO', 2025, 319, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(242, 1, 'TO', 2025, 320, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(243, 1, 'TO', 2025, 321, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(244, 1, 'TO', 2025, 322, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(245, 1, 'TO', 2025, 323, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(246, 1, 'TO', 2025, 324, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(247, 1, 'TO', 2025, 325, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(248, 1, 'TO', 2025, 326, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(249, 1, 'TO', 2025, 327, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(250, 1, 'TO', 2025, 328, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(251, 1, 'TO', 2025, 329, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(252, 1, 'TO', 2025, 330, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(253, 1, 'TO', 2025, 331, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(254, 1, 'TO', 2025, 332, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(255, 1, 'TO', 2025, 333, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29'),
(256, 1, 'TO', 2025, 334, '', 0, '', NULL, '2025-10-27 06:27:35', '2025-10-27 06:39:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(20, '2025-10-29-080404', 'App\\Database\\Migrations\\AddKabagApprovalToLedSubmissions', 'default', 'App', 1761725074, 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rencana_kinerja`
--

CREATE TABLE `rencana_kinerja` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `sasaran_program` text NOT NULL,
  `indikator_kinerja` text NOT NULL,
  `satuan` varchar(100) NOT NULL,
  `target_utama` varchar(255) NOT NULL,
  `kegiatan` text NOT NULL,
  `target_bulanan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `realisasi_bulanan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `tahun_anggaran` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rencana_kinerja`
--

INSERT INTO `rencana_kinerja` (`id`, `user_id`, `sasaran_program`, `indikator_kinerja`, `satuan`, `target_utama`, `kegiatan`, `target_bulanan`, `realisasi_bulanan`, `tahun_anggaran`) VALUES
(12, 2, 'kegiatan1', 'pengunjung perpus', 'laporan', '50', 'kegiatan 1', '[\"1\",\"2\",\"10\",\"2\",\"10\",\"3\",\"18\",\"4\",\"0\",\"26\",\"0\",\"0\"]', '[\"7\",\"0\",\"9\",\"\",\"\",\"4\",\"\",\"40\",\"\",\"\",\"\",\"\"]', 2025),
(13, 2, 'kegiatan2', 'indikaotr2', 'laporan', '22', 'kegiatan2', '[\"5\",\"5\",\"2\",\"1\",\"1\",\"1\",\"1\",\"6\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"10\",\"\",\"\",\"\",\"\"]', 2025),
(17, 5, 'Pendidikan dan Pelatihan', 'Penggunaan Lab CBT', 'laporan', '12', 'penggunaan lab cbt', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\",\"0\",\"0\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"2\",\"1\",\"1\",\"1\",\"\",\"\",\"\",\"\"]', 2025),
(18, 5, 'Perkantoran', 'Bantuan Teknis terhadap civitas akademika', 'laporan', '12', 'bantuan teknis', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\",\"0\",\"0\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"10\",\"1\",\"1\",\"1\",\"\",\"\",\"\",\"\"]', 2025),
(19, 5, 'Kegiatan online / hybrid', 'Setting ruangan dan perangkat online conference', 'laporan', '12', 'setting alat kegiatan hybrid atau online', '[\"0\",\"0\",\"0\",\"3\",\"0\",\"0\",\"1\",\"1\",\"0\",\"1\",\"1\",\"1\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"3\",\"\",\"\",\"\",\"\"]', 2025),
(20, 5, 'Perkantoran', 'Tersedianya jaringan internet di PKTJ', 'laporan', '12', 'tersedianya jaringan internet di pktj', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"0\",\"0\",\"0\",\"0\"]', '[\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"1\",\"\",\"\",\"\",\"\"]', 2025),
(21, 9, 'Penyerapan anggaran', 'penyerapan anggaran', 'Rp.', '1000000000', 'penyerapan', '[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"0\"]', '[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"]', 2025);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sasaran`
--

CREATE TABLE `sasaran` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_sasaran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(8, 'Terwujudnya Birokrasi yang Akuntabel dan Berorientasi pada Layanan Prima');

-- --------------------------------------------------------

--
-- Struktur dari tabel `satuan`
--

CREATE TABLE `satuan` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_satuan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `satuan`
--

INSERT INTO `satuan` (`id`, `nama_satuan`) VALUES
(1, '%'),
(2, 'Laporan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `foto`) VALUES
(1, 'admin', '$2y$10$pxxJBsCs/hg2IayNs96EP.acQRX36No8LOEVav03377bHvPzWa9Qq', 'Administrator Utama', 'admin@simonik.com', 'admin', 'default.png'),
(2, 'user', '$2y$10$nFaewWnr5k59gbwJnxeFTONWLBbSXD21vYtcmos1EoV9j6idjBSXS', 'Pengguna Biasa', 'user@simonik.com', 'aak', 'default.png'),
(4, 'perpustakaanpktj', '$2y$10$ckHRWAltsXNwM5ifWazrUOviD0OIdlaORMgYlnFxlDaqBREffgln2', 'Perpustakaan PKTJ', 'library@pktj.ac.id', 'aak', 'default.png'),
(5, 'itpktj', '$2y$10$fFEyXyikpIUfgTtckqX8d.6VLk49MclSPBiMzo.FazKhibujnUSb6', 'Unit Teknologi Informasi', 'it.pktj@pktj.ac.id', 'aak', 'default.png'),
(6, 'direkturpktj', '$2y$10$HkZOrEwM5UspEBwcsGcyX.z/dMw/nBYw3fqw25fQ3HzOagpChfiI2', 'Direktur PKTJ', 'pktj@pktj.ac.id', 'admin', 'default.png'),
(7, 'wadir1pktj', '$2y$10$CUHHeyP13BAWvOMN6pCUBeYyXSorJhNqf6tHJqOGyvsMXOnxNmqRq', 'Wakil Direktur 1', 'wadir1@gmail.com', 'manajemen', 'default.png'),
(8, 'diklatpktj', '$2y$10$DMeeWEKxZD5Z9w.r4Ed1Pez0w1Qia.x2q0f7IOAX8st4Xt3.tV.W.', 'Pokja Diklat', 'diklat@pktj.ac.id', 'aak', 'default.png'),
(9, 'keuanganpktj', '$2y$10$iJegL2gtWXf36zF.qKnLBuqLhojF8S/5l9SnzzcLH6W3pixOGAFVu', 'Keuangan PKTJ', 'keuangan@pktj.ac.id', 'kuk', 'default.png'),
(10, 'wadir2', '$2y$10$4IzlifiiFbLXnTU5GgkTaOBd.Nkm1G25pVBmrXFW9GxGw0DIJzcr6', 'Wakil Direktur 2', 'wadir2@gmail.com', 'manajemen', 'default.png'),
(11, 'wadir3', '$2y$10$Cc.p5W4Vw2umh/dTaU1pluEuMOtpfp1Mmt8QreodyJcEY1qlLELzy', 'Wakil Direktur 3', 'wadir3@gmail.com', 'manajemen', 'default.png'),
(12, 'baakpktj', '$2y$10$aPcpd60VjQT/I8/rFhhg.ulAnqupe6FVaXi76LqxVRa2TPamWKuB6', 'Kabag AAK', 'baakpktj@pktj.ac.id', 'kabag_aak', 'default.png'),
(13, 'kukpktj', '$2y$10$0B2mb/Vfm2QWNwXp/2Q6jOPqxi0rkGaNdsV6TROVYApC6/p6gDtoq', 'Kabag KUK', 'kukpktj@pktj.ac.id', 'kabag_kuk', 'default.png'),
(14, 'spmpktj', '$2y$10$kWb9DPxTDUawqQzyLObLIuPngSFju0PU4V2dN/E58ILB8SDDNzvJG', 'SPM PKTJ', 'spm@pktj.ac.id', 'spm', 'default.png');

--
-- Indexes for dumped tables
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
-- Indeks untuk tabel `led_categories`
--
ALTER TABLE `led_categories`
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
-- Indeks untuk tabel `led_submissions`
--
ALTER TABLE `led_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `jadwal_diklat`
--
ALTER TABLE `jadwal_diklat`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `led_categories`
--
ALTER TABLE `led_categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `led_criteria`
--
ALTER TABLE `led_criteria`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=335;

--
-- AUTO_INCREMENT untuk tabel `led_scores`
--
ALTER TABLE `led_scores`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT untuk tabel `led_submissions`
--
ALTER TABLE `led_submissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `rencana_kinerja`
--
ALTER TABLE `rencana_kinerja`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `sasaran`
--
ALTER TABLE `sasaran`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
