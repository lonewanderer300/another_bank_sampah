-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Des 2025 pada 05.41
-- Versi server: 8.4.3
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bank_sampah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `agent`
--

CREATE TABLE `agent` (
  `id_agent` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `wilayah` enum('Dusun Selatan','Dusun Hilir','Dusun Utara','Gunung Bintang Awai','Jenamas','Karau Kuala') DEFAULT NULL,
  `status` enum('pending','aktif','nonaktif') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `agent`
--

INSERT INTO `agent` (`id_agent`, `id_user`, `wilayah`, `status`) VALUES
(1, 2, 'Dusun Selatan', 'aktif'),
(2, 3, 'Dusun Selatan', 'aktif'),
(3, 4, 'Gunung Bintang Awai', 'aktif'),
(4, 13, 'Jenamas', 'aktif'),
(5, 16, 'Dusun Selatan', 'aktif'),
(6, 20, '', 'pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_setoran`
--

CREATE TABLE `detail_setoran` (
  `id_detail` int NOT NULL,
  `id_setoran` int DEFAULT NULL,
  `id_jenis` int DEFAULT NULL,
  `berat` decimal(10,2) DEFAULT NULL,
  `subtotal_poin` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `detail_setoran`
--

INSERT INTO `detail_setoran` (`id_detail`, `id_setoran`, `id_jenis`, `berat`, `subtotal_poin`) VALUES
(1, 1, 1, 5.00, 7500.00),
(2, 1, 2, 3.00, 3600.00),
(3, 1, 5, 4.50, 3650.00),
(4, 2, 1, 4.00, 6000.00),
(5, 2, 3, 3.80, 5700.00),
(6, 3, 4, 5.20, 7800.00),
(7, 4, 4, 1.00, 7000.00),
(8, 4, 2, 1.00, 6500.00),
(9, 4, 3, 1.00, 17000.00),
(10, 4, 5, 1.00, 3000.00),
(11, 4, 1, 1.00, 9000.00),
(12, 5, 4, 3.00, 21000.00),
(13, 5, 2, 3.00, 19500.00),
(14, 5, 3, 3.00, 51000.00),
(15, 5, 5, 3.00, 9000.00),
(16, 5, 1, 3.00, 27000.00),
(17, 6, 4, 2.00, 14000.00),
(18, 6, 2, 2.00, 13000.00),
(19, 6, 3, 2.00, 34000.00),
(20, 6, 5, 2.00, 6000.00),
(21, 6, 1, 2.00, 18000.00),
(22, 7, 4, 4.00, 28000.00),
(23, 7, 2, 2.00, 13000.00),
(24, 7, 3, 7.00, 119000.00),
(25, 7, 5, 2.00, 6000.00),
(26, 7, 1, 1.00, 9000.00),
(27, 8, 2, 1.00, 1000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `harga_histori`
--

CREATE TABLE `harga_histori` (
  `id_histori` int NOT NULL,
  `id_jenis` int NOT NULL,
  `harga` int NOT NULL,
  `tanggal_update` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `harga_histori`
--

INSERT INTO `harga_histori` (`id_histori`, `id_jenis`, `harga`, `tanggal_update`) VALUES
(1, 1, 9000, '2025-10-26'),
(2, 2, 6500, '2025-10-26'),
(3, 3, 17000, '2025-10-26'),
(4, 4, 7000, '2025-10-26'),
(5, 5, 3000, '2025-10-26'),
(6, 6, 2000, '2025-10-28'),
(7, 2, 1000, '2025-10-28'),
(8, 6, 1500, '2025-10-28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `iuran`
--

CREATE TABLE `iuran` (
  `id_iuran` int NOT NULL,
  `id_nasabah` int NOT NULL,
  `biaya` int NOT NULL,
  `deadline` date NOT NULL,
  `status_iuran` enum('sudah bayar','belum bayar') NOT NULL,
  `tanggal_bayar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `iuran`
--

INSERT INTO `iuran` (`id_iuran`, `id_nasabah`, `biaya`, `deadline`, `status_iuran`, `tanggal_bayar`) VALUES
(1, 1, 18000, '2025-11-17', 'belum bayar', NULL),
(3, 4, 20000, '2025-11-29', 'belum bayar', NULL),
(4, 5, 18000, '2025-12-10', 'belum bayar', NULL),
(5, 6, 18000, '2025-12-10', 'belum bayar', NULL),
(6, 7, 18000, '2025-12-10', 'belum bayar', NULL),
(7, 8, 18000, '2025-12-13', 'belum bayar', NULL),
(8, 9, 20000, '2025-12-21', 'belum bayar', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `iuran_master`
--

CREATE TABLE `iuran_master` (
  `id_master` int NOT NULL,
  `tipe_nasabah` enum('Perorangan','Kelompok') NOT NULL,
  `jumlah_nasabah` int NOT NULL,
  `biaya` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `iuran_master`
--

INSERT INTO `iuran_master` (`id_master`, `tipe_nasabah`, `jumlah_nasabah`, `biaya`) VALUES
(1, 'Perorangan', 1, 18000),
(2, 'Kelompok', 2, 23000),
(3, 'Kelompok', 3, 28000),
(4, 'Kelompok', 4, 33000),
(5, 'Kelompok', 5, 35000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_sampah`
--

CREATE TABLE `jenis_sampah` (
  `id_jenis` int NOT NULL,
  `id_kategori` int DEFAULT NULL,
  `nama_jenis` varchar(100) DEFAULT NULL,
  `satuan` varchar(10) DEFAULT 'kg',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `kode` varchar(1000) NOT NULL,
  `id_tipe_sampah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `jenis_sampah`
--

INSERT INTO `jenis_sampah` (`id_jenis`, `id_kategori`, `nama_jenis`, `satuan`, `created_at`, `kode`, `id_tipe_sampah`) VALUES
(1, 2, 'Plastik', 'kg', '2025-10-06 05:21:54', 'pl1', 1),
(2, 1, 'Kertas', 'kg', '2025-10-06 05:21:54', 'kt1', 1),
(3, 3, 'Logam', 'kg', '2025-10-06 05:21:54', 'lg1', 1),
(4, 4, 'Kaca', 'kg', '2025-10-06 05:21:54', 'kc1', 1),
(5, 5, 'Organik', 'kg', '2025-10-06 05:21:54', 'og1', 1),
(6, 1, 'kertas warna', 'kg', '2025-10-28 06:41:19', 'kt2', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_sampah`
--

CREATE TABLE `kategori_sampah` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `kategori_sampah`
--

INSERT INTO `kategori_sampah` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Kertas'),
(2, 'Plastik'),
(3, 'Logam'),
(4, 'kaca'),
(5, 'organik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nasabah`
--

CREATE TABLE `nasabah` (
  `id_nasabah` int NOT NULL,
  `id_users` int NOT NULL,
  `tipe_nasabah` enum('Perorangan','Kelompok') NOT NULL,
  `jumlah_nasabah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `nasabah`
--

INSERT INTO `nasabah` (`id_nasabah`, `id_users`, `tipe_nasabah`, `jumlah_nasabah`) VALUES
(1, 10, 'Perorangan', 1),
(2, 11, 'Kelompok', 3),
(3, 8, 'Perorangan', 1),
(4, 18, 'Kelompok', 3),
(5, 19, 'Perorangan', 1),
(6, 21, 'Perorangan', 1),
(7, 22, 'Perorangan', 1),
(8, 23, 'Perorangan', 1),
(9, 17, 'Perorangan', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int NOT NULL,
  `id_agent` int NOT NULL,
  `nama_petugas` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `id_agent`, `nama_petugas`) VALUES
(2, 5, 'tolol'),
(3, 5, 'adam tolol'),
(4, 4, 'ikan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekening_user`
--

CREATE TABLE `rekening_user` (
  `id_rekening_user` int NOT NULL,
  `id_user` int NOT NULL,
  `no_rekening` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `rekening_user`
--

INSERT INTO `rekening_user` (`id_rekening_user`, `id_user`, `no_rekening`) VALUES
(1, 17, 'b1231');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tipe_sampah`
--

CREATE TABLE `tipe_sampah` (
  `id_tipe_sampah` int NOT NULL,
  `nama_tipe` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `tipe_sampah`
--

INSERT INTO `tipe_sampah` (`id_tipe_sampah`, `nama_tipe`) VALUES
(1, 'Rumah_tangga'),
(2, 'Rumah_tangga');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_penarikan`
--

CREATE TABLE `transaksi_penarikan` (
  `id_penarikan` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `transaksi_penarikan`
--

INSERT INTO `transaksi_penarikan` (`id_penarikan`, `id_user`, `tanggal`, `jumlah`, `keterangan`) VALUES
(1, 5, '2025-10-04', 10000.00, 'Penarikan saldo ke e-wallet'),
(2, 7, '2025-10-05', 5000.00, 'Penarikan tunai');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_setoran`
--

CREATE TABLE `transaksi_setoran` (
  `id_setoran` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_agent` int DEFAULT NULL,
  `tanggal_setor` date DEFAULT NULL,
  `status` enum('pending','selesai') DEFAULT 'pending',
  `total_berat` decimal(10,2) DEFAULT '0.00',
  `total_poin` decimal(15,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `transaksi_setoran`
--

INSERT INTO `transaksi_setoran` (`id_setoran`, `id_user`, `id_agent`, `tanggal_setor`, `status`, `total_berat`, `total_poin`) VALUES
(1, 5, 2, '2025-10-01', 'selesai', 12.50, 18750.00),
(2, 6, 2, '2025-10-02', 'selesai', 7.80, 11700.00),
(3, 7, 3, '2025-10-03', 'selesai', 5.20, 7800.00),
(4, 9, 4, '2025-10-26', 'selesai', 5.00, 42500.00),
(5, 9, 4, '2025-10-30', 'selesai', 15.00, 127500.00),
(6, 9, 4, '2025-11-07', 'selesai', 10.00, 85000.00),
(7, 5, 4, '2025-10-30', 'selesai', 16.00, 175000.00),
(8, 9, 4, '2025-10-30', 'selesai', 1.00, 1000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','agent','admin') DEFAULT 'user',
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `bio` text,
  `id_agent_pilihan` int DEFAULT NULL,
  `poin` decimal(10,2) DEFAULT '0.00',
  `saldo` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `username`, `password`, `role`, `avatar`, `phone`, `address`, `latitude`, `longitude`, `bio`, `id_agent_pilihan`, `poin`, `saldo`, `created_at`) VALUES
(1, 'Admin Utama', 'admin@example.com', 'admin', '$2y$10$JFLMe9BogfrY.ukQ/TTzHuB/.WYf9OnrjPcMklMTrravKSCzMSlKi', 'admin', 'https://ui-avatars.com/api/?name=Admin+Utama', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-06 05:21:54'),
(2, 'Bank Sampah Sejahtera', 'sejahtera@example.com', 'sejahtera', '$2y$10$JFLMe9BogfrY.ukQ/TTzHuB/.WYf9OnrjPcMklMTrravKSCzMSlKi', 'agent', 'https://ui-avatars.com/api/?name=Bank+Sampah+Sejahtera', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-06 05:21:54'),
(3, 'Bank Sampah Hijau Lestari', 'hijau@example.com', 'hijau', '123456', 'agent', 'https://ui-avatars.com/api/?name=Hijau+Lestari', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-06 05:21:54'),
(4, 'Bank Sampah Bersih Indah', 'bersih@example.com', 'bersih', '123456', 'agent', 'https://ui-avatars.com/api/?name=Bersih+Indah', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-06 05:21:54'),
(5, 'Ilham Zeichi', 'ilham@example.com', 'ilham', '$2y$10$jLZo3KrHXqHSnWaTD9tbPeUVoGYK2Q5QnDFB5DHWeHhWRZYhdtnHO', 'user', 'https://ui-avatars.com/api/?name=Ilham+Zeichi', '085216675858', '', -1.82067740, 114.84283447, '', 4, 175050.00, 190000.00, '2025-10-06 05:21:54'),
(6, 'Siti Rahma', 'siti@example.com', 'siti', '123456', 'user', 'https://ui-avatars.com/api/?name=Siti+Rahma', NULL, NULL, NULL, NULL, NULL, NULL, 30.00, 8000.00, '2025-10-06 05:21:54'),
(7, 'Budi Santoso', 'budi@example.com', 'budi', '123456', 'user', 'https://ui-avatars.com/api/?name=Budi+Santoso', NULL, NULL, NULL, NULL, NULL, NULL, 75.00, 25000.00, '2025-10-06 05:21:54'),
(8, 'Zeichi', 'awkoakwo@cik.com', 'awkoakwo', '$2y$10$JFLMe9BogfrY.ukQ/TTzHuB/.WYf9OnrjPcMklMTrravKSCzMSlKi', 'user', 'https://ui-avatars.com/api/?name=Zeichi', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-08 05:03:59'),
(9, 'fatih', 'fatihfernandooo@gmail.com', 'fatihfernandooo', '$2y$10$JFLMe9BogfrY.ukQ/TTzHuB/.WYf9OnrjPcMklMTrravKSCzMSlKi', 'user', 'https://ui-avatars.com/api/?name=fatits', '087749933085', '', -1.99138590, 114.79494095, '', 4, 256000.00, 256000.00, '2025-10-11 03:28:07'),
(10, 'adam', 'q@we', NULL, '$2y$10$Yu9XdmgwsQ4FRbPyANcj3egLfYjbOaWw.vEIo6qJEsYHTZMOlrc96', 'user', NULL, '', '', NULL, NULL, '', NULL, 0.00, 0.00, '2025-10-16 04:32:10'),
(11, 'qwe', 'gogew37588@operades.com', NULL, '$2y$10$nF4lKn300fboY0Mn9ei/R.njGrpRvZM7zLC7c2vi5ZHyAn8ijBJnq', 'user', NULL, '123', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-18 06:06:06'),
(12, 'sudah_muak', 'kwin07434@gmail.com', NULL, '$2y$10$LZ6YYhXaP9pZXtNvn7LnqeiQMfk.NSX.ODYype0ntWwGVh1Wb3/yq', 'user', NULL, '083830042024', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-23 02:55:40'),
(13, 'bank uhuy', 'cihuy@mail.com', NULL, '$2y$10$7AxZiROQGWuuUoCxDUpsjeQR6nA4lI4sqchZP2Qdoyi7jVMgMESNq', 'agent', NULL, '098765432', '', -1.85104587, 114.84369278, '', NULL, 0.00, 0.00, '2025-10-23 03:01:08'),
(14, 'sudah_muak', 'alvinmasterl4d2@gmail.com', NULL, '$2y$10$3vg7pFOCdURAXRjn6tHjn.Cnk.HXApWBnzBF0mOLreJ1e1mfrigf6', 'user', NULL, '083830042024', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-23 03:06:26'),
(15, 'kazuma', 'alvinmasterl4d2@gmail.com', NULL, '$2y$10$9IJV1488yt/weXFWzK8UEOBiuF6Z5T3f3GjMeQ5ToLZy669WF3n6O', 'user', NULL, '083830042024', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-23 03:07:35'),
(16, 'tolol', 'tolol@tolol.mail', NULL, '$2y$10$Cw0Pf3FF7w6MZDDwt9YT3OIQCETSaUiLdvmK0NNLu4Y5b5J0O/isi', 'agent', NULL, '123', '', -1.81419186, 114.77339450, '', NULL, 0.00, 0.00, '2025-10-28 02:36:39'),
(17, 'akutolol', 'tt@kecil.com', NULL, '$2y$10$0DizbAcH7wsbQzTA9EqEX..YHxncQzsBax6r.dy.b3Ub5Wqq5LYsW', 'user', NULL, '123', '', -1.80926765, 114.77777059, '', 4, 0.00, 0.00, '2025-10-30 03:38:40'),
(18, 'kamutolol', 'lutolol@tolol.com', NULL, '$2y$10$MbOaJZEuBEVqEdqSrMwmJebebGIMBIWFBCELOs12Io5PY/nXVaKPO', 'user', NULL, '123', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-10-30 06:14:30'),
(19, 'kisaki', 'w@gmail.com', NULL, '$2y$10$2YAHVkn3WaFzwQ4UdXCQVOxnYhYCAJADHrIpIH79vmJ4to.HZg6g6', 'user', NULL, '123', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-11-10 03:57:52'),
(20, 'asd', 'e@gmail.com', NULL, '$2y$10$kcgu1WyZXkdnHMoX3NrSuOZl3MGLo047DeAWObuCtYTgVxAwBKuCy', 'agent', NULL, '123', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-11-10 06:49:02'),
(21, 'hina', 'h@gmail.com', NULL, '$2y$10$.xbxinO5IVu4VevHDdsAje6UR4HvisvV6lcnBNu5mHFUd2VQhfKH6', 'user', NULL, '123', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-11-10 06:55:30'),
(22, 'tt', 'tt@besar.com', NULL, '$2y$10$VXCcbg9xRLkBTw8FzFqiTeB2y3EUsP2xhjj3cdcUE.eBwp6Ni4h3y', 'user', NULL, '123', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-11-10 07:35:17'),
(23, 'tolol', 'kamu@tolol.com', NULL, '$2y$10$g8vVbhE3JtFKgw9fYOK8jOA23tm0EYkPl7r0zTxV2JA9Y9ftTZw9e', 'user', NULL, '123', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, '2025-11-13 02:55:20');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`id_agent`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `detail_setoran`
--
ALTER TABLE `detail_setoran`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_setoran` (`id_setoran`),
  ADD KEY `id_jenis` (`id_jenis`);

--
-- Indeks untuk tabel `harga_histori`
--
ALTER TABLE `harga_histori`
  ADD PRIMARY KEY (`id_histori`),
  ADD KEY `id_jenis` (`id_jenis`);

--
-- Indeks untuk tabel `iuran`
--
ALTER TABLE `iuran`
  ADD PRIMARY KEY (`id_iuran`);

--
-- Indeks untuk tabel `iuran_master`
--
ALTER TABLE `iuran_master`
  ADD PRIMARY KEY (`id_master`);

--
-- Indeks untuk tabel `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  ADD PRIMARY KEY (`id_jenis`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `kategori_sampah`
--
ALTER TABLE `kategori_sampah`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `nasabah`
--
ALTER TABLE `nasabah`
  ADD PRIMARY KEY (`id_nasabah`);

--
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`);

--
-- Indeks untuk tabel `rekening_user`
--
ALTER TABLE `rekening_user`
  ADD PRIMARY KEY (`id_rekening_user`);

--
-- Indeks untuk tabel `tipe_sampah`
--
ALTER TABLE `tipe_sampah`
  ADD PRIMARY KEY (`id_tipe_sampah`);

--
-- Indeks untuk tabel `transaksi_penarikan`
--
ALTER TABLE `transaksi_penarikan`
  ADD PRIMARY KEY (`id_penarikan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `transaksi_setoran`
--
ALTER TABLE `transaksi_setoran`
  ADD PRIMARY KEY (`id_setoran`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_agent` (`id_agent`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_agent_pilihan` (`id_agent_pilihan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `agent`
--
ALTER TABLE `agent`
  MODIFY `id_agent` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `detail_setoran`
--
ALTER TABLE `detail_setoran`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `harga_histori`
--
ALTER TABLE `harga_histori`
  MODIFY `id_histori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `iuran`
--
ALTER TABLE `iuran`
  MODIFY `id_iuran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `iuran_master`
--
ALTER TABLE `iuran_master`
  MODIFY `id_master` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  MODIFY `id_jenis` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kategori_sampah`
--
ALTER TABLE `kategori_sampah`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `nasabah`
--
ALTER TABLE `nasabah`
  MODIFY `id_nasabah` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `rekening_user`
--
ALTER TABLE `rekening_user`
  MODIFY `id_rekening_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tipe_sampah`
--
ALTER TABLE `tipe_sampah`
  MODIFY `id_tipe_sampah` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `transaksi_penarikan`
--
ALTER TABLE `transaksi_penarikan`
  MODIFY `id_penarikan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `transaksi_setoran`
--
ALTER TABLE `transaksi_setoran`
  MODIFY `id_setoran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `agent`
--
ALTER TABLE `agent`
  ADD CONSTRAINT `agent_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_setoran`
--
ALTER TABLE `detail_setoran`
  ADD CONSTRAINT `detail_setoran_ibfk_1` FOREIGN KEY (`id_setoran`) REFERENCES `transaksi_setoran` (`id_setoran`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_setoran_ibfk_2` FOREIGN KEY (`id_jenis`) REFERENCES `jenis_sampah` (`id_jenis`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `harga_histori`
--
ALTER TABLE `harga_histori`
  ADD CONSTRAINT `harga_histori_ibfk_1` FOREIGN KEY (`id_jenis`) REFERENCES `jenis_sampah` (`id_jenis`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  ADD CONSTRAINT `jenis_sampah_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_sampah` (`id_kategori`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `transaksi_penarikan`
--
ALTER TABLE `transaksi_penarikan`
  ADD CONSTRAINT `transaksi_penarikan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi_setoran`
--
ALTER TABLE `transaksi_setoran`
  ADD CONSTRAINT `transaksi_setoran_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_setoran_ibfk_2` FOREIGN KEY (`id_agent`) REFERENCES `agent` (`id_agent`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_agent_pilihan` FOREIGN KEY (`id_agent_pilihan`) REFERENCES `agent` (`id_agent`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
