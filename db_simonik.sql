-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Sep 2025 pada 03.39
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
(9, '2025-08-21-031504', 'App\\Database\\Migrations\\CreateSatuanTable', 'default', 'App', 1755748023, 3);

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
  `target_bulanan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`target_bulanan`)),
  `realisasi_bulanan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`realisasi_bulanan`)),
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
(6, 'direkturpktj', '$2y$10$HkZOrEwM5UspEBwcsGcyX.z/dMw/nBYw3fqw25fQ3HzOagpChfiI2', 'Direktur PKTJ', 'pktj@pktj.ac.id', 'manajemen', 'default.png'),
(7, 'wadir1pktj', '$2y$10$CUHHeyP13BAWvOMN6pCUBeYyXSorJhNqf6tHJqOGyvsMXOnxNmqRq', 'Wakil Direktur 1', 'wadir1@gmail.com', 'manajemen', 'default.png'),
(8, 'diklatpktj', '$2y$10$DMeeWEKxZD5Z9w.r4Ed1Pez0w1Qia.x2q0f7IOAX8st4Xt3.tV.W.', 'Pokja Diklat', 'diklat@pktj.ac.id', 'aak', 'default.png'),
(9, 'keuanganpktj', '$2y$10$iJegL2gtWXf36zF.qKnLBuqLhojF8S/5l9SnzzcLH6W3pixOGAFVu', 'Keuangan PKTJ', 'keuangan@pktj.ac.id', 'kuk', 'default.png');

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
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
