-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 05:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tugas1_muhammadrifqisaifulloh`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_cek_pembayaran_muhammadrifqisaifulloh`
--

CREATE TABLE `tb_cek_pembayaran_muhammadrifqisaifulloh` (
  `nisn` varchar(10) NOT NULL,
  `tgl_terakhir_bayar` date DEFAULT NULL,
  `tgl_sekarang` date DEFAULT NULL,
  `status_pembayaran` enum('Belum Lunas','Sudah Lunas') NOT NULL DEFAULT 'Belum Lunas',
  `jumlah_bulan` varchar(5) DEFAULT NULL,
  `nama` varchar(50) NOT NULL,
  `no_telp` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_cek_pembayaran_muhammadrifqisaifulloh`
--

INSERT INTO `tb_cek_pembayaran_muhammadrifqisaifulloh` (`nisn`, `tgl_terakhir_bayar`, `tgl_sekarang`, `status_pembayaran`, `jumlah_bulan`, `nama`, `no_telp`) VALUES
('0011223344', '2026-05-10', '2026-05-25', 'Sudah Lunas', '1', 'Ahmad Nurhuda', '081234567890'),
('0011223345', NULL, '2026-05-25', 'Belum Lunas', '1', 'Rifqi Saifulloh', '081234567891');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kelas_muhammadrifqisaifulloh`
--

CREATE TABLE `tb_kelas_muhammadrifqisaifulloh` (
  `id_kelas` varchar(11) NOT NULL,
  `nama_kelas` varchar(10) NOT NULL,
  `komp_keahlian` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kelas_muhammadrifqisaifulloh`
--

INSERT INTO `tb_kelas_muhammadrifqisaifulloh` (`id_kelas`, `nama_kelas`, `komp_keahlian`) VALUES
('K1', 'XII RPL 1', 'Rekayasa Perangkat Lunak'),
('K2', 'XII RPL 2', 'Rekayasa Perangkat Lunak'),
('K3', 'XII TKJ 1', 'Teknik Komputer dan Jaringan'),
('K4', 'XII TKJ 2', 'Teknik Komputer dan Jaringan'),
('K5', 'XII MM 1', 'Multimedia');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pembayaran_muhammadrifqisaifulloh`
--

CREATE TABLE `tb_pembayaran_muhammadrifqisaifulloh` (
  `id_pembayaran` varchar(11) NOT NULL,
  `status` enum('Belum Lunas','Sudah Lunas') NOT NULL DEFAULT 'Belum Lunas',
  `nisn` varchar(10) NOT NULL,
  `tgl_bayar` date DEFAULT NULL,
  `tgl_terakhir_bayar` date DEFAULT NULL,
  `batas_pembayaran` date DEFAULT NULL,
  `jumlah_bulan` varchar(10) DEFAULT NULL,
  `id_spp` varchar(11) NOT NULL,
  `nominal_bayar` varchar(100) DEFAULT NULL,
  `jumlah_bayar` varchar(40) DEFAULT NULL,
  `kembalian` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pembayaran_muhammadrifqisaifulloh`
--

INSERT INTO `tb_pembayaran_muhammadrifqisaifulloh` (`id_pembayaran`, `status`, `nisn`, `tgl_bayar`, `tgl_terakhir_bayar`, `batas_pembayaran`, `jumlah_bulan`, `id_spp`, `nominal_bayar`, `jumlah_bayar`, `kembalian`) VALUES
('PAY1', 'Sudah Lunas', '0011223344', '2026-05-10', '2026-05-10', '2026-05-10', '1', 'SPP1', '250000', '250000', '0'),
('PAY2', 'Belum Lunas', '0011223345', NULL, NULL, '2026-05-10', '1', 'SPP1', '250000', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `tb_petugas_muhammadrifqisaifulloh`
--

CREATE TABLE `tb_petugas_muhammadrifqisaifulloh` (
  `id_petugas` varchar(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(32) NOT NULL,
  `nama_petugas` varchar(35) NOT NULL,
  `level` enum('admin','petugas','siswa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_petugas_muhammadrifqisaifulloh`
--

INSERT INTO `tb_petugas_muhammadrifqisaifulloh` (`id_petugas`, `username`, `password`, `nama_petugas`, `level`) VALUES
('P1', 'admin1', '21232f297a57a5a743894a0e4a801fc3', 'Rifqi Admin', 'admin'),
('P2', 'petugas1', 'afb91ef692fd08c445e8cb1bab2ccf9c', 'Saifulloh Petugas', 'petugas'),
('P3', 'petugas2', 'ac5604a8b8504d4ff5b842480df02e91', 'Muhammad Petugas', 'petugas'),
('P4', 'siswa1', '013f0f67779f3b1686c604db150d12ea', 'Ahmad Nurhuda', 'siswa'),
('P5', 'siswa2', '331633a246a4e1ceefc9539a71fcd124', 'Rifqi Saifulloh', 'siswa');

-- --------------------------------------------------------

--
-- Table structure for table `tb_siswa_muhammadrifqisaifulloh`
--

CREATE TABLE `tb_siswa_muhammadrifqisaifulloh` (
  `nisn` varchar(10) NOT NULL,
  `nis` varchar(8) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `id_kelas` varchar(11) NOT NULL,
  `nama_kelas` varchar(10) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(13) DEFAULT NULL,
  `id_spp` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_siswa_muhammadrifqisaifulloh`
--

INSERT INTO `tb_siswa_muhammadrifqisaifulloh` (`nisn`, `nis`, `nama`, `id_kelas`, `nama_kelas`, `alamat`, `no_telp`, `id_spp`) VALUES
('0011223344', '12345678', 'Ahmad Nurhuda', 'K1', 'XII RPL 1', 'Jl. Merdeka No. 1, Jakarta', '081234567890', 'SPP1'),
('0011223345', '12345679', 'Rifqi Saifulloh', 'K2', 'XII RPL 2', 'Jl. Sudirman No. 2, Bandung', '081234567891', 'SPP1'),
('0011223346', '12345680', 'Kharisma Galuh', 'K3', 'XII TKJ 1', 'Jl. Mawar No. 3, Surabaya', '081234567892', 'SPP2'),
('0011223347', '12345681', 'Mirza Ahda', 'K4', 'XII TKJ 2', 'Jl. Melati No. 4, Yogyakarta', '081234567893', 'SPP2'),
('0011223348', '12345682', 'Eka Zuly', 'K5', 'XII MM 1', 'Jl. Dahlia No. 5, Medan', '081234567894', 'SPP3');

-- --------------------------------------------------------

--
-- Table structure for table `tb_spp_muhammadrifqisaifulloh`
--

CREATE TABLE `tb_spp_muhammadrifqisaifulloh` (
  `id_spp` varchar(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `nominal` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_spp_muhammadrifqisaifulloh`
--

INSERT INTO `tb_spp_muhammadrifqisaifulloh` (`id_spp`, `tahun`, `nominal`) VALUES
('SPP1', 2024, '250000'),
('SPP2', 2025, '300000'),
('SPP3', 2026, '350000'),
('SPP4', 2027, '400000'),
('SPP5', 2028, '450000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_cek_pembayaran_muhammadrifqisaifulloh`
--
ALTER TABLE `tb_cek_pembayaran_muhammadrifqisaifulloh`
  ADD PRIMARY KEY (`nisn`),
  ADD KEY `fk_cek_pembayaran_siswa_info` (`nisn`,`nama`,`no_telp`);

--
-- Indexes for table `tb_kelas_muhammadrifqisaifulloh`
--
ALTER TABLE `tb_kelas_muhammadrifqisaifulloh`
  ADD PRIMARY KEY (`id_kelas`),
  ADD UNIQUE KEY `uq_kelas_id_nama` (`id_kelas`,`nama_kelas`);

--
-- Indexes for table `tb_pembayaran_muhammadrifqisaifulloh`
--
ALTER TABLE `tb_pembayaran_muhammadrifqisaifulloh`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `fk_pembayaran_siswa_spp` (`nisn`,`id_spp`);

--
-- Indexes for table `tb_petugas_muhammadrifqisaifulloh`
--
ALTER TABLE `tb_petugas_muhammadrifqisaifulloh`
  ADD PRIMARY KEY (`id_petugas`),
  ADD UNIQUE KEY `uq_petugas_username` (`username`);

--
-- Indexes for table `tb_siswa_muhammadrifqisaifulloh`
--
ALTER TABLE `tb_siswa_muhammadrifqisaifulloh`
  ADD PRIMARY KEY (`nisn`),
  ADD UNIQUE KEY `uq_siswa_nis` (`nis`),
  ADD UNIQUE KEY `uq_siswa_nisn_spp` (`nisn`,`id_spp`),
  ADD UNIQUE KEY `uq_siswa_cek` (`nisn`,`nama`,`no_telp`),
  ADD KEY `fk_siswa_spp` (`id_spp`),
  ADD KEY `fk_siswa_kelas` (`id_kelas`,`nama_kelas`);

--
-- Indexes for table `tb_spp_muhammadrifqisaifulloh`
--
ALTER TABLE `tb_spp_muhammadrifqisaifulloh`
  ADD PRIMARY KEY (`id_spp`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_cek_pembayaran_muhammadrifqisaifulloh`
--
ALTER TABLE `tb_cek_pembayaran_muhammadrifqisaifulloh`
  ADD CONSTRAINT `fk_cek_pembayaran_siswa_info` FOREIGN KEY (`nisn`,`nama`,`no_telp`) REFERENCES `tb_siswa_muhammadrifqisaifulloh` (`nisn`, `nama`, `no_telp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_pembayaran_muhammadrifqisaifulloh`
--
ALTER TABLE `tb_pembayaran_muhammadrifqisaifulloh`
  ADD CONSTRAINT `fk_pembayaran_siswa_spp` FOREIGN KEY (`nisn`,`id_spp`) REFERENCES `tb_siswa_muhammadrifqisaifulloh` (`nisn`, `id_spp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_siswa_muhammadrifqisaifulloh`
--
ALTER TABLE `tb_siswa_muhammadrifqisaifulloh`
  ADD CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`id_kelas`,`nama_kelas`) REFERENCES `tb_kelas_muhammadrifqisaifulloh` (`id_kelas`, `nama_kelas`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_siswa_spp` FOREIGN KEY (`id_spp`) REFERENCES `tb_spp_muhammadrifqisaifulloh` (`id_spp`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
