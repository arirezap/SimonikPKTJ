-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 23 Des 2025 pada 08.53
-- Versi server: 5.7.44
-- Versi PHP: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ekinerja_kinerja`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `indikator`
--

CREATE TABLE `indikator` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_indikator` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `led_criteria`
--

CREATE TABLE `led_criteria` (
  `id` int(11) UNSIGNED NOT NULL,
  `prodi` varchar(50) NOT NULL DEFAULT 'RSTJ',
  `nama_kriteria` text NOT NULL,
  `id_standar` int(11) UNSIGNED DEFAULT NULL,
  `role_assignment` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `tahun` int(4) NOT NULL,
  `led_criteria_id` int(11) UNSIGNED NOT NULL,
  `skor` decimal(5,2) DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `id` int(11) UNSIGNED NOT NULL,
  `nama_standar` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `tahun` int(4) NOT NULL,
  `led_criteria_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `catatan_kabag` text COMMENT 'Catatan/revisi dari Kabag untuk staf',
  `catatan_wadir` text COMMENT 'Catatan/revisi dari Wadir untuk staf/kabag',
  `kabag_approved` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Persetujuan oleh Kabag',
  `catatan` text,
  `file_bukti` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(371, 7, 'RSTJ', 2025, 583, 'Ada', 'sudah lengkap', 'dokumen sudah sesuai dengan VMTS', 1, 'https://drive.google.com/file/d/1dv-Cl_uXVAjv4qOc8gI5frbhlg2SaUrb/view?usp=drive_link', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(372, 7, 'RSTJ', 2025, 586, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(373, 7, 'RSTJ', 2025, 594, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(374, 7, 'RSTJ', 2025, 595, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(375, 7, 'RSTJ', 2025, 596, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(376, 7, 'RSTJ', 2025, 597, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(377, 7, 'RSTJ', 2025, 598, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(378, 7, 'RSTJ', 2025, 599, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(379, 7, 'RSTJ', 2025, 600, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(380, 7, 'RSTJ', 2025, 601, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(381, 7, 'RSTJ', 2025, 602, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(382, 7, 'RSTJ', 2025, 603, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(383, 7, 'RSTJ', 2025, 604, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(384, 7, 'RSTJ', 2025, 605, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(385, 7, 'RSTJ', 2025, 606, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(386, 7, 'RSTJ', 2025, 607, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(387, 7, 'RSTJ', 2025, 608, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(388, 7, 'RSTJ', 2025, 609, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(389, 7, 'RSTJ', 2025, 610, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(390, 7, 'RSTJ', 2025, 611, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(391, 7, 'RSTJ', 2025, 612, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(392, 7, 'RSTJ', 2025, 613, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(393, 7, 'RSTJ', 2025, 615, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(394, 7, 'RSTJ', 2025, 616, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(395, 7, 'RSTJ', 2025, 617, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(396, 7, 'RSTJ', 2025, 618, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(397, 7, 'RSTJ', 2025, 619, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(398, 7, 'RSTJ', 2025, 620, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(399, 7, 'RSTJ', 2025, 621, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(400, 7, 'RSTJ', 2025, 622, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(401, 7, 'RSTJ', 2025, 625, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(402, 7, 'RSTJ', 2025, 626, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25');
INSERT INTO `led_submissions` (`id`, `user_id`, `prodi`, `tahun`, `led_criteria_id`, `status`, `catatan_kabag`, `catatan_wadir`, `kabag_approved`, `catatan`, `file_bukti`, `created_at`, `updated_at`) VALUES
(403, 7, 'RSTJ', 2025, 627, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(404, 7, 'RSTJ', 2025, 628, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(405, 7, 'RSTJ', 2025, 629, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(406, 7, 'RSTJ', 2025, 630, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(407, 7, 'RSTJ', 2025, 631, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(408, 7, 'RSTJ', 2025, 632, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(409, 7, 'RSTJ', 2025, 633, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(410, 7, 'RSTJ', 2025, 634, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(411, 7, 'RSTJ', 2025, 635, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(412, 7, 'RSTJ', 2025, 636, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(413, 7, 'RSTJ', 2025, 637, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
(414, 7, 'RSTJ', 2025, 638, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:45:05', '2025-11-27 03:34:25'),
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
(497, 7, 'RSTJ', 2025, 584, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(498, 7, 'RSTJ', 2025, 585, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(499, 7, 'RSTJ', 2025, 587, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(500, 7, 'RSTJ', 2025, 588, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(501, 7, 'RSTJ', 2025, 589, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(502, 7, 'RSTJ', 2025, 590, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(503, 7, 'RSTJ', 2025, 591, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(504, 7, 'RSTJ', 2025, 592, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(505, 7, 'RSTJ', 2025, 593, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(506, 7, 'RSTJ', 2025, 614, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(507, 7, 'RSTJ', 2025, 623, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(508, 7, 'RSTJ', 2025, 624, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(509, 7, 'RSTJ', 2025, 639, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(510, 7, 'RSTJ', 2025, 640, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(511, 7, 'RSTJ', 2025, 641, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(512, 7, 'RSTJ', 2025, 642, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(513, 7, 'RSTJ', 2025, 643, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(514, 7, 'RSTJ', 2025, 644, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(515, 7, 'RSTJ', 2025, 645, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
(516, 7, 'RSTJ', 2025, 646, 'Ada', 'oke', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:15', '2025-11-27 03:34:25'),
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
(556, 7, 'TO', 2025, 766, 'Ada', 'baik', 'baik', 1, 'https://drive.google.com/drive/folders/113HrM1O_tAadOE5PNQOfpmIpbX4ySnJj?usp=sharing', NULL, '2025-11-17 14:56:58', '2025-11-17 15:09:18');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(31, '2025-11-08-025948', 'App\\Database\\Migrations\\CreateRemunerasiTable', 'default', 'App', 1762583377, 17);

-- --------------------------------------------------------

--
-- Struktur dari tabel `remunerasi`
--

CREATE TABLE `remunerasi` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` int(2) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_by_user_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `realisasi_bulanan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `tahun_anggaran` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `id` int(10) UNSIGNED NOT NULL,
  `nama_sasaran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `id` int(10) UNSIGNED NOT NULL,
  `nama_satuan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `foto`) VALUES
(1, 'admin', '$2y$10$pxxJBsCs/hg2IayNs96EP.acQRX36No8LOEVav03377bHvPzWa9Qq', 'Administrator Utama', 'admin@simonik.com', 'admin', 'default.png'),
(5, 'itpktj', '$2y$10$fFEyXyikpIUfgTtckqX8d.6VLk49MclSPBiMzo.FazKhibujnUSb6', 'Unit Teknologi Informasi', 'it.pktj@pktj.ac.id', 'aak', 'default.png'),
(6, 'direkturpktj', '$2y$10$HkZOrEwM5UspEBwcsGcyX.z/dMw/nBYw3fqw25fQ3HzOagpChfiI2', 'Direktur PKTJ', 'pktj@pktj.ac.id', 'manajemen', 'default.png'),
(7, 'wadir1pktj', '$2y$10$CUHHeyP13BAWvOMN6pCUBeYyXSorJhNqf6tHJqOGyvsMXOnxNmqRq', 'Wakil Direktur 1', 'wadir1@gmail.com', 'manajemen', 'default.png'),
(8, 'diklatpktj', '$2y$10$DMeeWEKxZD5Z9w.r4Ed1Pez0w1Qia.x2q0f7IOAX8st4Xt3.tV.W.', 'Pokja Diklat', 'diklat@pktj.ac.id', 'aak', 'default.png'),
(9, 'keuanganpktj', '$2y$10$iJegL2gtWXf36zF.qKnLBuqLhojF8S/5l9SnzzcLH6W3pixOGAFVu', 'Keuangan PKTJ', 'keuangan@pktj.ac.id', 'kuk', 'default.png'),
(10, 'wadir2', '$2y$10$4IzlifiiFbLXnTU5GgkTaOBd.Nkm1G25pVBmrXFW9GxGw0DIJzcr6', 'Wakil Direktur 2', 'wadir2@gmail.com', 'manajemen', 'default.png'),
(11, 'wadir3', '$2y$10$Cc.p5W4Vw2umh/dTaU1pluEuMOtpfp1Mmt8QreodyJcEY1qlLELzy', 'Wakil Direktur 3', 'wadir3@gmail.com', 'manajemen', 'default.png'),
(12, 'baakpktj', '$2y$10$aPcpd60VjQT/I8/rFhhg.ulAnqupe6FVaXi76LqxVRa2TPamWKuB6', 'Kabag AAK', 'baakpktj@pktj.ac.id', 'kabag_aak', 'default.png'),
(13, 'kukpktj', '$2y$10$0B2mb/Vfm2QWNwXp/2Q6jOPqxi0rkGaNdsV6TROVYApC6/p6gDtoq', 'Kabag KUK', 'kukpktj@pktj.ac.id', 'kabag_kuk', 'default.png'),
(14, 'spmpktj', '$2y$10$kWb9DPxTDUawqQzyLObLIuPngSFju0PU4V2dN/E58ILB8SDDNzvJG', 'SPM PKTJ', 'spm@pktj.ac.id', 'spm', 'default.png'),
(15, 'pusbangkar', '$2y$10$cES7Gb82NRUSsMsiC1EJP.bvjuOwY8UJvVA9uCm3LKxitN0RcGlfu', 'Pusbangkar', 'pusbangkar@pktj.ac.id', 'aak', 'default.png'),
(16, 'akademik', '$2y$10$I6SAmPW3wnduwnx7GYd3IOCqJG5mbgN.1hqpHwmTiPfgcxbpU.jSC', 'Akademik PKTJ', 'akademik@pktj.ac.id', 'aak', 'default.png');

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
-- AUTO_INCREMENT untuk tabel `led_criteria`
--
ALTER TABLE `led_criteria`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=767;

--
-- AUTO_INCREMENT untuk tabel `led_scores`
--
ALTER TABLE `led_scores`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT untuk tabel `led_standar`
--
ALTER TABLE `led_standar`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `led_submissions`
--
ALTER TABLE `led_submissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=557;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `remunerasi`
--
ALTER TABLE `remunerasi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rencana_kinerja`
--
ALTER TABLE `rencana_kinerja`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT untuk tabel `sasaran`
--
ALTER TABLE `sasaran`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `remunerasi`
--
ALTER TABLE `remunerasi`
  ADD CONSTRAINT `remunerasi_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `remunerasi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
