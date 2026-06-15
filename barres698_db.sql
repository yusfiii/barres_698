-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2026 at 08:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barres698_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` int(11) NOT NULL,
  `nomor_anggota` int(2) DEFAULT NULL,
  `bpk_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT 'Laki-laki',
  `alamat` text DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `no_hp` varchar(15) NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `jabatan` enum('Ketua','Wakil Ketua','Sekretaris','Anggota') NOT NULL DEFAULT 'Anggota',
  `foto` varchar(255) DEFAULT NULL,
  `foto_ktp` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `nomor_anggota`, `bpk_id`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `nik`, `no_hp`, `status`, `jabatan`, `foto`, `foto_ktp`, `created_at`, `updated_at`) VALUES
(1, 9, 1, 'Muhammad Yusfi', 'Banjarbaru', '2004-06-02', 'Laki-laki', 'Jl. Intansari', '1985372380645214', '081348631450', 'aktif', 'Anggota', NULL, NULL, '2026-04-26 15:20:38', '2026-04-30 08:15:44'),
(2, 1, 1, 'Muhammad Farhani', 'Banjarmasin', '1963-06-15', 'Laki-laki', 'Intan sari', '1985372380645232', '081348631490', 'aktif', 'Ketua', '', '', '2026-05-23 15:02:02', '2026-05-25 15:18:01'),
(68, 1, 2, 'abueghifari', 'Kandangan', '2003-01-23', 'Laki-laki', 'bumi cahaya bintang', '1985372380645107', '081348631210', 'aktif', 'Anggota', NULL, NULL, '2026-05-25 15:38:28', '2026-05-25 15:38:28');

-- --------------------------------------------------------

--
-- Table structure for table `bpk`
--

CREATE TABLE `bpk` (
  `id` int(11) NOT NULL,
  `nomor_registrasi` varchar(5) NOT NULL,
  `nama_bpk` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `kelurahan` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `tahun_berdiri` year(4) DEFAULT NULL,
  `jumlah_anggota` int(11) DEFAULT 0,
  `fasilitas_pemadam_tangki` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fasilitas_pemadam_tangki`)),
  `fasilitas_pemadam_portable` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fasilitas_pemadam_portable`)),
  `fasilitas_ambulance` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fasilitas_ambulance`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bpk`
--

INSERT INTO `bpk` (`id`, `nomor_registrasi`, `nama_bpk`, `alamat`, `kecamatan`, `kelurahan`, `logo`, `latitude`, `longitude`, `tahun_berdiri`, `jumlah_anggota`, `fasilitas_pemadam_tangki`, `fasilitas_pemadam_portable`, `fasilitas_ambulance`, `created_at`, `updated_at`) VALUES
(1, '001', 'BPK INSAR 21', 'JL Intansari', 'Banjarbaru Selatan', 'Sungai Besar', '69f322ebe6551.jpg', -3.45236090, 114.84423233, '2009', 2, NULL, '{\"jumlah\":2,\"keterangan\":\"Baik\",\"foto\":null}', NULL, '2026-04-30 09:22:57', '2026-05-23 15:02:03'),
(2, '002', 'Emergency Hayati', '', 'Banjarbaru Selatan', 'Sungai Besar', NULL, -3.46527362, 114.82771754, '2003', 1, NULL, NULL, NULL, '2026-05-23 15:53:44', '2026-05-25 15:38:29');

-- --------------------------------------------------------

--
-- Table structure for table `heatmap_settings`
--

CREATE TABLE `heatmap_settings` (
  `id` int(11) NOT NULL,
  `radius` int(11) DEFAULT 25,
  `blur` int(11) DEFAULT 15,
  `intensity` int(11) DEFAULT 70,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `heatmap_settings`
--

INSERT INTO `heatmap_settings` (`id`, `radius`, `blur`, `intensity`, `updated_at`) VALUES
(1, 25, 15, 70, '2026-04-12 09:58:36');

-- --------------------------------------------------------

--
-- Table structure for table `hydrant`
--

CREATE TABLE `hydrant` (
  `id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `alamat` text NOT NULL,
  `kecamatan` varchar(100) NOT NULL,
  `kelurahan` varchar(100) NOT NULL,
  `tahun_pemasangan` year(4) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('berfungsi','rusak') NOT NULL DEFAULT 'berfungsi',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hydrant`
--

INSERT INTO `hydrant` (`id`, `latitude`, `longitude`, `alamat`, `kecamatan`, `kelurahan`, `tahun_pemasangan`, `foto`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, -3.45583300, 114.98416700, 'Jalan R.O Ulin', 'Banjarbaru Selatan', 'Loktabat Selatan', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(2, -3.44757800, 114.83690000, 'Perumahan di Jalan Cahaya Komplek Ichwan Raya', 'Banjarbaru Selatan', 'Loktabat Selatan', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(3, -3.45333300, 114.81805600, 'Kantor Kecamatan Banjarbaru Selatan', 'Banjarbaru Selatan', 'Loktabat Selatan', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(4, -3.44805600, 114.81277800, 'Jl. Nusantara', 'Banjarbaru Selatan', 'Loktabat Selatan', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(5, -3.46208800, 114.82278500, 'Komplek Green Tasbih Loktabat Selatan', 'Banjarbaru Selatan', 'Loktabat Selatan', '2022', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(6, -3.46164200, 114.83993100, 'Komplek Galuh Marindu II', 'Banjarbaru Selatan', 'Sungai Besar', '2021', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(7, -3.44500000, 114.84750000, 'Jalan Mistar Cokrokusumo Kelurahan Sungai Besar (Dpn Giant)', 'Banjarbaru Selatan', 'Sungai Besar', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(8, -3.45472200, 114.84555600, 'Komplek Ratu Elok', 'Banjarbaru Selatan', 'Sungai Besar', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(9, -3.45669400, 114.84310000, 'Jl. Wana Bhakti Kel. Sei Besar', 'Banjarbaru Selatan', 'Sungai Besar', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(10, -3.46055600, 114.84611100, 'Jl. Aquarius Raya Kelurahan Sungai Besar', 'Banjarbaru Selatan', 'Sungai Besar', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(11, -3.46416700, 114.84722200, 'Komp. Banua Permai', 'Banjarbaru Selatan', 'Sungai Besar', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(12, -3.46537800, 114.85843900, 'Komplek Villa Idaman 2 jl Gazebo', 'Banjarbaru Selatan', 'Sungai Besar', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(13, -3.45333300, 114.84361100, 'Jalan Intan Sari', 'Banjarbaru Selatan', 'Sungai Besar', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(14, -3.45277800, 114.84694400, 'Komplek Mustika Permai', 'Banjarbaru Selatan', 'Sungai Besar', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(15, -3.44688100, 114.83923100, 'Jalan Unlam III', 'Banjarbaru Selatan', 'Sungai Besar', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(16, -3.45831900, 114.84543600, 'Jalan Sagitarius Komplek Bumi Cahaya Bintang', 'Banjarbaru Selatan', 'Sungai Besar', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(17, -3.45781700, 114.84006900, 'Komplek Widya Citra Elok Dahlina Sei Besar', 'Banjarbaru Selatan', 'Sungai Besar', '2022', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(18, -3.45776700, 114.85180000, 'Komplek Antero Raya', 'Banjarbaru Selatan', 'Sungai Besar', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(19, -3.46277800, 114.84972200, 'Komplek Banua Permai', 'Banjarbaru Selatan', 'Sungai Besar', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(20, -3.45222200, 114.83166700, 'Jalan Rosella', 'Banjarbaru Selatan', 'Kemuning', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(21, -3.45224700, 114.82976700, 'Jl. Al Jafri Kelurahan Kemuning', 'Banjarbaru Selatan', 'Kemuning', '2021', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(22, -3.44722200, 114.82888900, 'Gt. Lua Depan Masjid', 'Banjarbaru Selatan', 'Kemuning', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(23, -3.45722200, 114.82694400, 'Komplek Kemuning', 'Banjarbaru Selatan', 'Kemuning', '2023', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(24, -3.46083300, 114.83305600, 'Komplek Halim', 'Banjarbaru Selatan', 'Guntung Paikat', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(25, -3.45111100, 114.83611100, 'Jalan Karamunting Ujung', 'Banjarbaru Selatan', 'Guntung Paikat', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(26, -3.45419200, 114.83483300, 'Jl. Pandawa Kelurahan Guntung Paikat', 'Banjarbaru Selatan', 'Guntung Paikat', '2021', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(27, -3.43073300, 114.81730800, 'Jalan Karang Anyar (Kantor Kelurahan Loktabat Utara)', 'Banjarbaru Utara', 'Loktabat Utara', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(28, -3.44400800, 114.82900300, 'Jalan Jati', 'Banjarbaru Utara', 'Loktabat Utara', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(29, -3.44351400, 114.82816400, 'Jalan Lanan', 'Banjarbaru Utara', 'Loktabat Utara', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(30, -3.44741700, 114.82823600, 'Jalan Bina Karya', 'Banjarbaru Utara', 'Loktabat Utara', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(31, -3.43580600, 114.84191100, 'Jalan Pelita', 'Banjarbaru Utara', 'Loktabat Utara', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(32, -3.43245000, 114.82431100, 'Jalan Taruna Praja', 'Banjarbaru Utara', 'Loktabat Utara', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(33, -3.44203300, 114.83455000, 'Depan SDN Banjarbaru Utara 2 (SDN Mawar)', 'Banjarbaru Utara', 'Loktabat Utara', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(34, -3.43235600, 114.80420600, 'Komplek Mustika Jaya Rt. 48', 'Banjarbaru Utara', 'Loktabat Utara', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(35, -3.43216400, 114.82020000, 'Wilayah Balitan Dekat Darul Hijrah', 'Banjarbaru Utara', 'Loktabat Utara', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(36, -3.43310300, 114.79580300, 'Jl. Karang Sawo Kelurahan Loktabat Utara', 'Banjarbaru Utara', 'Loktabat Utara', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(37, -3.43039600, 114.79881600, 'Jalan Pondok Empat', 'Banjarbaru Utara', 'Loktabat Utara', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(38, -3.42722200, 114.81333300, 'Komplek Griya Alam Lestari Krg Anyar 2 Loktabat Utara', 'Banjarbaru Utara', 'Loktabat Utara', '2022', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(39, -3.43222200, 114.80888900, 'Jl. Karang Anyar III Kel. Loktabat Utara', 'Banjarbaru Utara', 'Loktabat Utara', '2023', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(40, -3.43222200, 114.80888900, 'Jl. Basril', 'Banjarbaru Utara', 'Loktabat Utara', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(41, -3.43636700, 114.83657500, 'Jalan Komet Raya', 'Banjarbaru Utara', 'Komet', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(42, -3.44138900, 114.83972200, 'Jalan Garuda Kelurahan Komet', 'Banjarbaru Utara', 'Komet', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(43, -3.43805600, 114.83666700, 'Jl. Palapa', 'Banjarbaru Utara', 'Komet', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(44, -3.43833300, 114.83750000, 'Jalan Murai', 'Banjarbaru Utara', 'Komet', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(45, -3.44048600, 114.84685600, 'Gang Purnama Kelurahan Komet', 'Banjarbaru Utara', 'Komet', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(46, -3.43888600, 114.83027500, 'Jl. RP. Soeparto (Samping Balaikota) Kawasan Lap. Moerdjani', 'Banjarbaru Utara', 'Mentaos', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(47, -3.45083300, 114.86250000, 'Jalan Jeruk', 'Banjarbaru Utara', 'Sungai Ulin', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(48, -3.45444400, 114.88555600, 'Perumahan di Jalan Seledri', 'Banjarbaru Utara', 'Sungai Ulin', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(49, -3.44331900, 114.85925600, 'Jalan Perjuangan Kelurahan Sei. Ulin', 'Banjarbaru Utara', 'Sungai Ulin', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(50, -3.45481100, 114.86620300, 'Komp. Green Orchid Bukit Sirkuit Sungai Ulin', 'Banjarbaru Utara', 'Sungai Ulin', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(51, -3.45000000, 114.87027800, 'Perumahan Citra Permata Indah', 'Banjarbaru Utara', 'Sungai Ulin', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(52, -3.44835800, 114.87043100, 'Komplek BPI', 'Banjarbaru Utara', 'Sungai Ulin', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(53, -3.44671700, 114.86000700, 'Komplek Citra Garden City Sungai Ulin', 'Banjarbaru Utara', 'Sungai Ulin', '2022', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(54, -3.45694400, 114.86138900, 'Jl. Jeruk Komp. Bukit Sirkuit Permai', 'Banjarbaru Utara', 'Sungai Ulin', '2023', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(55, -3.51199400, 114.81722500, 'Kantor Kelurahan Bangkal Kecamatan Cempaka', 'Cempaka', 'Bangkal', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(56, -3.50445300, 114.82399100, 'Jl. Mistar Cokrokusumo Depan UPT-BPP Bangkal', 'Cempaka', 'Bangkal', '2022', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(57, -3.50194700, 114.83833100, 'Kantor Kecamatan Cempaka', 'Cempaka', 'Cempaka', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(58, -3.47666700, 114.85138900, 'Jl. SMA 3', 'Cempaka', 'Cempaka', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(59, -3.48650600, 114.85465300, 'Pasar Cempaka', 'Cempaka', 'Cempaka', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(60, -3.47637800, 114.85776100, 'Komplek Graha Praja Idaman jl. Gunung Kupang', 'Cempaka', 'Cempaka', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(61, -3.47638900, 114.83944400, 'Jalan Aneka Tambang', 'Cempaka', 'Cempaka', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(62, -3.47012000, 114.85119800, 'Komplek Fitria Cempaka / Komplek Pelangi Jaya Lestari', 'Cempaka', 'Cempaka', '2022', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(63, -3.46601700, 114.84691300, 'Komplek Cempaka Sari', 'Cempaka', 'Cempaka', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(64, -3.47333300, 114.84694400, 'Komplek Galuh', 'Cempaka', 'Cempaka', '2023', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(65, -3.47277800, 114.85472200, 'Komplek Graha Citra Megah', 'Cempaka', 'Cempaka', '2023', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(66, -3.48055600, 114.85722200, 'Komplek Berlian Permai Cempaka', 'Cempaka', 'Cempaka', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(67, -3.49079700, 114.79755800, 'Komplek Lambung Mangkurat Kelurahan Palam', 'Cempaka', 'Palam', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(68, -3.46387800, 114.81093600, 'Komplek Griya Mawar Asri Jalan Raya Palam', 'Cempaka', 'Palam', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(69, -3.49621400, 114.79870000, 'Jl. Purnawirawan Kelurahan Palam', 'Cempaka', 'Palam', '2021', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(70, -3.50450000, 114.85272000, 'Jl. Transpol Cempaka', 'Cempaka', 'Sungai Tiung', '2024', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(71, -3.49777800, 114.85138900, 'Komplek Pesona Fitria Mandiri', 'Cempaka', 'Sungai Tiung', '2024', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(72, -3.48366100, 114.80167200, 'Komplek Griya Cahaya Abadi Palam', 'Landasan Ulin', 'Guntung Manggis', '2022', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(73, -3.45903600, 114.80381700, 'Komplek Kruing Indah Jl. Sungai Sumba', 'Landasan Ulin', 'Guntung Manggis', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(74, -3.48246400, 114.77533600, 'Jalan Guntung Manggis', 'Landasan Ulin', 'Guntung Manggis', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(75, -3.46416700, 114.80277800, 'Komplek Wengga Jalan Trikora', 'Landasan Ulin', 'Guntung Manggis', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(76, -3.46763600, 114.78540800, 'Komplek Benawa Raya', 'Landasan Ulin', 'Guntung Manggis', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(77, -3.48235800, 114.79165000, 'Jl. Danau Seran Kel. Guntung Manggis', 'Landasan Ulin', 'Guntung Manggis', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(78, -3.45802500, 114.79995600, 'Jl. Sungai Sumba Kel. Guntung Manggis', 'Landasan Ulin', 'Guntung Manggis', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(79, -3.45722200, 114.79250000, 'Jl. Guntung manggis (Depan Ruko)', 'Landasan Ulin', 'Guntung Manggis', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(80, -3.46229200, 114.76590300, 'Jl. Guntung Harapan Kel. Guntung Manggis', 'Landasan Ulin', 'Guntung Manggis', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(81, -3.48275600, 114.77954200, 'Jl. Transad Kel. Guntung Manggis', 'Landasan Ulin', 'Guntung Manggis', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(82, -3.46425000, 114.78546400, 'Komplek Benawa Raya (Depan Masjid)', 'Landasan Ulin', 'Guntung Manggis', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(83, -3.45333300, 114.79250000, 'Komplek Berlina Jaya 1', 'Landasan Ulin', 'Guntung Manggis', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(84, -3.46351900, 114.79601000, 'Jl. Guntung Paring Gt. Manggis', 'Landasan Ulin', 'Guntung Manggis', '2022', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(85, -3.48111100, 114.77805600, 'Komplek Guntung Manggis Living Style', 'Landasan Ulin', 'Guntung Manggis', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(86, -3.44166700, 114.74694400, 'Jalan Hercules', 'Landasan Ulin', 'Landasan Ulin Timur', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(87, -3.44861100, 114.75444400, 'Jalan Sidomulyo Raya', 'Landasan Ulin', 'Landasan Ulin Timur', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(88, -3.45061400, 114.77340800, 'Jalan Kuranji Kelurahan Landasan Ulin Timur', 'Landasan Ulin', 'Landasan Ulin Timur', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(89, -3.45500000, 114.76361100, 'Kantor Kecamatan Landasan Ulin', 'Landasan Ulin', 'Landasan Ulin Timur', '2023', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(90, -3.45805600, 114.76083300, 'Komplek Griya Utama Trikora 8', 'Landasan Ulin', 'Landasan Ulin Timur', '2024', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(91, -3.45416700, 114.74888900, 'Kantor Lurah Landasan Ulin Timur', 'Landasan Ulin', 'Landasan Ulin Timur', '2024', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(92, -3.44638900, 114.74444400, 'Jl. Hercules', 'Landasan Ulin', 'Landasan Ulin Timur', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(93, -3.43666700, 114.74916700, 'Komplek Citra Raya Angkasa', 'Landasan Ulin', 'Syamsudin Noor', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(94, -3.43290600, 114.74164700, 'Jalan Golf', 'Landasan Ulin', 'Syamsudin Noor', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(95, -3.41604400, 114.74819700, 'Komplek Wella Mandiri Kelurahan Syamsudin Noor', 'Landasan Ulin', 'Syamsudin Noor', '2021', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(96, -3.41083300, 114.75055600, 'Griya Ramania', 'Landasan Ulin', 'Syamsudin Noor', '2023', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(97, -3.42722200, 114.75333300, 'Jl. Bataan (Kasturi II)', 'Landasan Ulin', 'Syamsudin Noor', '2024', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(98, -3.43083300, 114.75805600, 'Jl. Kasturi I Tegal Arum (Depan Kantor UPTD Pelayanan Krisis dan Epidemi Kesehatan)', 'Landasan Ulin', 'Syamsudin Noor', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(99, -3.41638900, 114.75750000, 'Jl. Tambak Tarap', 'Landasan Ulin', 'Syamsudin Noor', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(100, -3.44833300, 114.78055600, 'Jalan A. Yani Km. 29', 'Landasan Ulin', 'Guntung Payung', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(101, -3.43972200, 114.79194400, 'Jalan Sapta Marga', 'Landasan Ulin', 'Guntung Payung', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(102, -3.44555600, 115.00194400, 'Komp. Mustika Griya Angkasa', 'Landasan Ulin', 'Guntung Payung', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(103, -3.47221900, 114.82312800, 'Jalan Sumber Ilmu Depan Langgar Nurul Huda Guntung Upih', 'Landasan Ulin', 'Guntung Payung', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(104, -3.44482500, 114.79601400, 'Komplek Graha Permata Indah Jalan Soeratno Gt Payung', 'Landasan Ulin', 'Guntung Payung', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(105, -3.43854200, 114.81698600, 'Jalan Bina Satria Depan Makam Muslimin Guntung Jingah', 'Landasan Ulin', 'Guntung Payung', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(106, -3.40618900, 114.74514700, 'Jalan Golf Pondok Pisang', 'Liang Anggang', 'Landasan Ulin Barat', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(107, -3.42993300, 114.72788100, 'Jalan Sukamara', 'Liang Anggang', 'Landasan Ulin Barat', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(108, -3.44892500, 114.69706400, 'Kantor Kelurahan Landasan Ulin Barat Kecamatan Liang Anggang', 'Liang Anggang', 'Landasan Ulin Barat', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(109, -3.44074200, 114.71835000, 'Jl. Sriwijaya Kel. Landasan Ulin Barat', 'Liang Anggang', 'Landasan Ulin Barat', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(110, -3.42220800, 114.71840800, 'Jl. Caraka Jaya Kelurahan Landasan Ulin Utara', 'Liang Anggang', 'Landasan Ulin Utara', '2021', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(111, -3.41785000, 114.73206100, 'Jl. Kurnia Kelurahan Landasan Ulin Utara', 'Liang Anggang', 'Landasan Ulin Utara', '2021', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(112, -3.43387500, 114.72299200, 'Jl. Sukamaju Kelurahan Landasan Ulin Utara', 'Liang Anggang', 'Landasan Ulin Utara', '2021', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(113, -3.41972200, 114.71361100, 'Mesjid Jami Hidayatul Akbar', 'Liang Anggang', 'Landasan Ulin Utara', '2024', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(114, -3.41111100, 114.73916700, 'Perumahan Permata Golf III Pondok Pisang', 'Liang Anggang', 'Landasan Ulin Utara', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(115, -3.40000000, 114.74611100, 'SMP 11 Banjarbaru', 'Liang Anggang', 'Landasan Ulin Utara', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(116, 0.00000000, 0.00000000, 'Perumahan Komplek Safanusa', 'Liang Anggang', 'Landasan Ulin Selatan', NULL, NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(117, -3.45671900, 114.70253600, 'Kantor Kelurahan Landasan Ulin Selatan', 'Liang Anggang', 'Landasan Ulin Selatan', '2020', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(118, -3.45105300, 114.74147800, 'Komplek CPMA', 'Liang Anggang', 'Landasan Ulin Tengah', '2017', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(119, -3.44419400, 114.73820600, 'Jalan Peramuan SMP 4 Banjarbaru', 'Liang Anggang', 'Landasan Ulin Tengah', '2018', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46'),
(120, -3.45750000, 114.73500000, 'Jl. Peramuan Komplek Puri Peramuan Indah', 'Liang Anggang', 'Landasan Ulin Tengah', '2025', NULL, 'berfungsi', NULL, '2026-06-14 15:01:46', '2026-06-14 15:01:46');

-- --------------------------------------------------------

--
-- Table structure for table `kejadian_kebakaran`
--

CREATE TABLE `kejadian_kebakaran` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `alamat` text NOT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `kelurahan` varchar(50) DEFAULT NULL,
  `jumlah_bangunan` int(11) DEFAULT 0,
  `jumlah_KK` int(11) DEFAULT 0,
  `jumlah_individu` int(11) DEFAULT 0,
  `korban_luka` int(11) DEFAULT 0,
  `korban_jiwa` int(11) DEFAULT 0,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kejadian_kebakaran`
--

INSERT INTO `kejadian_kebakaran` (`id`, `waktu`, `latitude`, `longitude`, `alamat`, `kecamatan`, `kelurahan`, `jumlah_bangunan`, `jumlah_KK`, `jumlah_individu`, `korban_luka`, `korban_jiwa`, `foto`, `created_at`) VALUES
(1, '2026-04-26 14:48:00', -3.46172100, 114.81119600, 'Jalan Raya Palam, Griya Mawar Asri, Loktabat Selatan, Banjarbaru, Kalimantan Selatan, Kalimantan, 70712, Indonesia', 'Banjarbaru Utara', 'Loktabat Selatan', 0, 0, 0, 0, 0, NULL, '2026-04-12 09:58:37'),
(2, '2026-04-08 13:58:00', -3.46591000, 114.75725500, 'Guntung Manggis, Banjarbaru, Kalimantan Selatan, Kalimantan, 70724, Indonesia', 'Landasan Ulin', 'Guntung Manggis', 0, 0, 0, 0, 0, NULL, '2026-04-12 09:58:37'),
(3, '2026-04-29 20:10:00', -3.45460540, 114.86671160, 'Sungai Ulin, Banjarbaru, Kalimantan Selatan, Kalimantan, 70714, Indonesia', 'Banjarbaru Utara', 'Sungai Ulin', 1, 0, 0, 0, 0, NULL, '2026-04-12 09:58:37'),
(4, '2026-04-01 11:38:00', -3.49107400, 114.85190000, 'Cempaka, Banjarbaru, Kalimantan Selatan, Kalimantan, 70734, Indonesia', 'Cempaka', 'Cempaka', 6, 6, 19, 0, 0, NULL, '2026-04-12 09:58:37'),
(5, '2023-01-02 11:00:00', -3.45500000, 114.81800000, 'Jl. A Yani Km 36', 'Banjarbaru Utara', 'Guntung Manggis', 1, 1, 4, 0, 0, NULL, '2026-04-12 09:58:37'),
(6, '2026-04-24 08:17:00', -3.47230200, 114.82134300, 'Kemuning, Banjarbaru, Kalimantan Selatan, Kalimantan, 70731, Indonesia', 'Banjarbaru Selatan', 'Kemuning', 1, 1, 2, 0, 0, NULL, '2026-04-12 09:58:37'),
(7, '2026-04-01 04:12:00', -3.44205020, 114.73338690, 'Jalan Jenderal Achmad Yani, Landasan Ulin Timur, Banjarbaru, Kalimantan Selatan, Kalimantan, 70723, Indonesia', 'Landasan Ulin', 'Landasan Ulin Tengah', 1, 2, 4, 0, 0, '20260426_153554_69edc05a634ae.jpeg', '2026-04-12 09:58:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `role` enum('super_admin','admin_bpk') NOT NULL,
  `bpk_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `no_hp`, `role`, `bpk_id`, `created_at`) VALUES
(5, 'superadmin', '$2y$10$RSeBrsl2uu8PUFXOVZF0/OrclLt3PFFRBnT7QGXx3Qt5v.5YCB7dm', '081234567890', 'super_admin', NULL, '2026-04-25 03:02:50'),
(6, 'admin_bpk1', '$2y$10$RSeBrsl2uu8PUFXOVZF0/OrclLt3PFFRBnT7QGXx3Qt5v.5YCB7dm', '081234567891', 'admin_bpk', 1, '2026-04-25 03:02:50'),
(7, 'admin_bpk2', '$2y$10$RSeBrsl2uu8PUFXOVZF0/OrclLt3PFFRBnT7QGXx3Qt5v.5YCB7dm', '081234567892', 'admin_bpk', 2, '2026-04-25 03:02:50'),
(8, 'admin_bpk3', '$2y$10$RSeBrsl2uu8PUFXOVZF0/OrclLt3PFFRBnT7QGXx3Qt5v.5YCB7dm', '081234567893', 'admin_bpk', 3, '2026-04-25 03:02:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `unique_nomor_per_bpk` (`bpk_id`,`nomor_anggota`);

--
-- Indexes for table `bpk`
--
ALTER TABLE `bpk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_registrasi` (`nomor_registrasi`);

--
-- Indexes for table `heatmap_settings`
--
ALTER TABLE `heatmap_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hydrant`
--
ALTER TABLE `hydrant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kecamatan` (`kecamatan`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `kejadian_kebakaran`
--
ALTER TABLE `kejadian_kebakaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_location` (`latitude`,`longitude`),
  ADD KEY `idx_waktu` (`waktu`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `bpk`
--
ALTER TABLE `bpk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `heatmap_settings`
--
ALTER TABLE `heatmap_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hydrant`
--
ALTER TABLE `hydrant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `kejadian_kebakaran`
--
ALTER TABLE `kejadian_kebakaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_ibfk_1` FOREIGN KEY (`bpk_id`) REFERENCES `bpk` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
