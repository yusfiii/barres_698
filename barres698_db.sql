-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 21, 2026 at 06:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
(68, 1, 2, 'abueghifari', 'Kandangan', '2003-01-23', 'Laki-laki', 'bumi cahaya bintang', '1985372380645107', '081348631210', 'aktif', 'Anggota', NULL, NULL, '2026-05-25 15:38:28', '2026-05-25 15:38:28'),
(69, 2, 1, 'Akhmad Juliani', 'Banjarbaru', '1984-03-06', 'Laki-laki', 'Jl. Intansari', '6372060207050002', '081234560284', 'aktif', 'Anggota', NULL, NULL, '2026-07-16 15:04:01', '2026-07-16 15:04:01');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bpk`
--

INSERT INTO `bpk` (`id`, `nomor_registrasi`, `nama_bpk`, `alamat`, `kecamatan`, `kelurahan`, `logo`, `latitude`, `longitude`, `tahun_berdiri`, `jumlah_anggota`, `created_at`, `updated_at`) VALUES
(1, '001', 'BPK INSAR 21', 'JL Intansari, Loktabat Selatan, Banjarbaru Selatan, Banjarbaru City, South Kalimantan 70714', 'Banjarbaru Selatan', 'Sungai Besar', '69f322ebe6551.jpg', -3.45224614, 114.84423834, '2009', 3, '2026-04-30 09:22:57', '2026-07-21 14:53:01'),
(2, '002', 'Emergency Hayati', 'Tasbih Regency, Guntungmanggis, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Sungai Besar', '', -3.46527362, 114.82771754, '2003', 1, '2026-05-23 15:53:44', '2026-07-21 14:42:03'),
(3, '003', 'BPK AN-NAFI', 'Jl. Rambai Tengah II No.9A, RT.002/RW.005, Guntung Paikat, Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Kemuning', '', -3.44807054, 114.83140236, '2012', 0, '2026-07-07 07:15:24', '2026-07-21 14:43:31'),
(4, '004', 'G474H FIRE FIGHTER & RESCUE', 'Jl. Listrik 2, Loktabat Sel., Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Guntung Paikat', '', -3.44486512, 114.83713723, '2006', 0, '2026-07-07 10:22:19', '2026-07-21 14:44:31'),
(5, '005', 'PMK HARMA', 'Jl. Zafri Zam-Zam II, Guntungmanggis, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Kemuning', '', -3.45283885, 114.82731496, '2005', 0, '2026-07-07 10:50:12', '2026-07-21 14:45:25'),
(6, '006', 'MADA FIRE GROUP', 'Jl. Raya Cancer No.42, RT.45/RW.8, sungai besar, Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Sungai Besar', '', -3.45855688, 114.84740450, '2020', 0, '2026-07-07 14:03:45', '2026-07-21 14:46:11'),
(7, '007', 'PMK BAUNTUNG', 'Jalan Jati ujung No.25, Loktabat Sel., Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70713', 'Banjarbaru Selatan', 'Kemuning', '', -3.44485836, 114.82899116, '2012', 0, '2026-07-07 14:14:17', '2026-07-21 14:48:08'),
(8, '008', 'REGAS FIRE & RESCUE', 'Jl. Permata Intan, Sungai Besar, Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Utara', 'Sungai Ulin', '', -3.44827730, 114.86631772, '2000', 0, '2026-07-07 14:26:22', '2026-07-21 14:49:25'),
(9, '009', 'ETB FIRE RESCUE', 'Komplek Amaco, Jl. Nilam V, RT.021/RW.009, Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru, Kalimantan Selatan 70712', 'Banjarbaru Utara', 'Loktabat Utara', '', -3.43576771, 114.82367871, '2000', 0, '2026-07-07 14:41:27', '2026-07-21 14:49:59'),
(10, '011', 'BPK BATRA', 'Gg. Abadi, Guntung Payung, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Utara', 'Loktabat Utara', '', -3.44164388, 114.81834593, '2000', 0, '2026-07-07 14:43:20', '2026-07-21 14:51:17'),
(11, '010', 'PALAPA FIRE RESCUE', 'Komp Palapa, Kelurahan Mentaos Banjarbaru Utara', 'Banjarbaru Utara', 'Mentaos', '', -3.43825709, 114.83512258, '2024', 0, '2026-07-21 13:12:02', '2026-07-21 13:15:44'),
(12, '012', 'BPK TRISAKTI', 'Jl. H. Mistar Cokrokusumo, Bangkal, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70732', 'Cempaka', 'Cempaka', '', -3.48901904, 114.85266880, '2000', 0, '2026-07-21 13:27:53', '2026-07-21 14:52:08'),
(13, '013', 'BPK Relawan Haul (RH)', 'Jl. H. Mistar Cokrokusumo, Bangkal, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70732', 'Cempaka', 'Sungai Tiung', NULL, -3.49863071, 114.84753260, '2000', 0, '2026-07-21 13:43:54', '2026-07-21 13:43:54'),
(14, '014', 'BPK SIBAT BABBUSALAM', 'Jl. H. Mistar Cokrokusumo, Cempaka, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70852', 'Cempaka', 'Bangkal', NULL, -3.52288985, 114.81062517, '2000', 0, '2026-07-21 13:45:49', '2026-07-21 13:45:49'),
(15, '015', 'NEW RESCUE MASPAL', '', 'Cempaka', 'Palam', NULL, -3.47345543, 114.80912017, '2000', 0, '2026-07-21 14:05:06', '2026-07-21 14:05:06'),
(16, '016', 'BPK RAST', 'Sungai Tiung, Cempaka, Banjarbaru City, South Kalimantan 70732', 'Cempaka', 'Sungai Tiung', NULL, -3.50383768, 114.84097320, '2000', 0, '2026-07-21 14:06:56', '2026-07-21 14:06:56'),
(17, '017', 'AL-BIDAYAH FIRE FIGHTER', 'Gg. Bersama, Sungai Tiung, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70732', 'Cempaka', 'Cempaka', NULL, -3.49459948, 114.85576008, '2000', 0, '2026-07-21 14:08:00', '2026-07-21 14:08:00'),
(18, '018', 'KAMPUR FIRE RESCUE', 'Jl. Purnawirawan, RT.05 RW02/RW.Kampung Purun, Palam, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70732', 'Cempaka', 'Palam', NULL, -3.50181611, 114.78715901, '2000', 0, '2026-07-21 14:10:06', '2026-07-21 14:10:06'),
(19, '019', 'AL-FATIH FIRE RESCUE', 'Jl. Kenanga No.32, Landasan Ulin Tim., Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70721', 'Landasan Ulin', 'Landasan Ulin Timur', NULL, -3.45216952, 114.76316136, '2000', 0, '2026-07-21 14:13:01', '2026-07-21 14:13:01'),
(20, '020', 'AL-MA\'UNAH FIRE', 'Jl. Tekukur, Landasan Ulin Tengah, Kec. Liang Anggang, Kota Banjar Baru, Kalimantan Selatan 70724', 'Landasan Ulin', 'Landasan Ulin Timur', NULL, -3.45605334, 114.74086665, '2000', 0, '2026-07-21 14:18:21', '2026-07-21 14:18:21'),
(21, '021', 'BPK GUP', 'Jl. Betet 36-26, Landasan Ulin Tengah, Kec. Liang Anggang, Kota Banjar Baru, Kalimantan Selatan 70724', 'Landasan Ulin', 'Landasan Ulin Timur', '', -3.45562626, 114.74024945, '2000', 0, '2026-07-21 14:19:40', '2026-07-21 14:54:03'),
(22, '022', 'SAUDARA FIRE RESCUE', 'Jl. Kuranji, Landasan Ulin Tim., Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70721', 'Landasan Ulin', 'Guntung Manggis', NULL, -3.44985937, 114.77283229, '2000', 0, '2026-07-21 14:21:34', '2026-07-21 14:21:34'),
(23, '023', 'BPK BAROKAH SYAMSUDIN NOOR', 'Syamsudin Noor, Landasan Ulin, Banjarbaru City, South Kalimantan 70721', 'Landasan Ulin', 'Syamsudin Noor', NULL, -3.43027443, 114.76673967, '2000', 0, '2026-07-21 14:28:16', '2026-07-21 14:28:16'),
(24, '024', 'BPK SEPAKAT', 'Landasan Ulin Utara, Liang Anggang, Banjarbaru City, South Kalimantan 70724', 'Landasan Ulin', 'Syamsudin Noor', NULL, -3.42975383, 114.75447048, '2000', 0, '2026-07-21 14:29:19', '2026-07-21 14:29:19'),
(25, '025', 'GUNTUNG MANGGIS FIRE RESCUE', 'Komplek.GPIP II, Jl. Kebun Durian No.15 Kel Blok P, Guntungmanggis, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70721', 'Landasan Ulin', 'Guntung Manggis', NULL, -3.46772800, 114.79336240, '2000', 0, '2026-07-21 14:31:13', '2026-07-21 14:31:13'),
(26, '026', 'BPK PRABU', 'Jl. Trikora simpang 4 Peramuan, RT.04/RW.04, Landasan Ulin Sel., Timur, Kota Banjar Baru, Kalimantan Selatan 70723', 'Liang Anggang', 'Landasan Ulin Tengah', NULL, -3.45183274, 114.73494097, '2000', 0, '2026-07-21 14:32:31', '2026-07-21 14:32:31'),
(27, '027', 'PENGAYUAN RESCUE', 'Jl. Pintas Sambangan No.20, Landasan Ulin Sel., Kec. Liang Anggang, Kota Banjar Baru, Kalimantan Selatan 70723', 'Liang Anggang', 'Landasan Ulin Selatan', NULL, -3.50172284, 114.71167251, '2000', 0, '2026-07-21 14:34:03', '2026-07-21 14:34:03'),
(28, '028', 'BPK SWASTA PRIBUMI 07', 'Jalan Sriwijaya No.KM.21,600, Landasan Ulin Utara, Kec. Liang Anggang, Kota Banjar Baru, Kalimantan Selatan 70722', 'Liang Anggang', 'Landasan Ulin Utara', NULL, -3.42772919, 114.71580624, '2000', 0, '2026-07-21 14:39:02', '2026-07-21 14:39:02'),
(29, '029', 'BPK RELAWAN BINA PUTRA BERSATU 2025 FIRE RESCUE', 'Landasan Ulin Timur, Landasan Ulin, Banjarbaru City, South Kalimantan 70721', 'Landasan Ulin', 'Guntung Payung', NULL, -3.44399169, 114.78614847, '2000', 0, '2026-07-21 14:40:50', '2026-07-21 14:40:50');

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
(1, 25, 15, 70, '2026-04-12 09:58:36'),
(2, 44, 15, 70, '2026-06-20 09:41:48'),
(3, 44, 15, 70, '2026-06-20 09:42:53'),
(4, 44, 15, 70, '2026-06-20 09:44:03'),
(5, 44, 15, 70, '2026-06-20 09:44:24'),
(6, 44, 15, 70, '2026-06-20 10:22:30'),
(7, 44, 15, 37, '2026-06-20 10:22:45'),
(8, 14, 15, 37, '2026-06-20 10:22:56'),
(9, 25, 15, 70, '2026-06-20 10:23:05'),
(10, 25, 15, 70, '2026-06-20 10:25:32'),
(11, 25, 15, 70, '2026-06-20 10:25:46');

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
  `penyebab` varchar(100) DEFAULT NULL,
  `penyebab_lainnya` text DEFAULT NULL,
  `kerusakan` enum('rusak ringan','rusak sedang','rusak berat','rusak total') DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `diupdate_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kejadian_kebakaran`
--

INSERT INTO `kejadian_kebakaran` (`id`, `waktu`, `latitude`, `longitude`, `alamat`, `kecamatan`, `kelurahan`, `jumlah_bangunan`, `jumlah_KK`, `jumlah_individu`, `korban_luka`, `korban_jiwa`, `penyebab`, `penyebab_lainnya`, `kerusakan`, `keterangan`, `foto`, `dibuat_oleh`, `diupdate_oleh`, `created_at`) VALUES
(1, '2026-04-26 14:48:00', -3.46172100, 114.81119600, 'Jalan Raya Palam, Griya Mawar Asri, Loktabat Selatan, Banjarbaru, Kalimantan Selatan, Kalimantan, 70712, Indonesia', 'Banjarbaru Utara', 'Loktabat Selatan', 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-12 09:58:37'),
(2, '2026-04-08 13:58:00', -3.46591000, 114.75725500, 'Guntung Manggis, Banjarbaru, Kalimantan Selatan, Kalimantan, 70724, Indonesia', 'Landasan Ulin', 'Guntung Manggis', 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-12 09:58:37'),
(3, '2026-04-29 20:10:00', -3.45460540, 114.86671160, 'Sungai Ulin, Banjarbaru, Kalimantan Selatan, Kalimantan, 70714, Indonesia', 'Banjarbaru Utara', 'Sungai Ulin', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-12 09:58:37'),
(4, '2026-04-01 11:38:00', -3.49107400, 114.85190000, 'Cempaka, Banjarbaru, Kalimantan Selatan, Kalimantan, 70734, Indonesia', 'Cempaka', 'Cempaka', 6, 6, 19, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-12 09:58:37'),
(5, '2026-03-01 10:32:00', -3.47326714, 114.79804234, 'Jl. Pembataan Guntung Paring Rt.36/07, Kel. Guntung Manggis, Kec. Landasan Ulin', 'Landasan Ulin', 'Guntung Manggis', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '20260620_154228_6a364464e2a70.jpeg', NULL, NULL, '2026-04-12 09:58:37'),
(6, '2026-04-24 08:17:00', -3.47230200, 114.82134300, 'Kemuning, Banjarbaru, Kalimantan Selatan, Kalimantan, 70731, Indonesia', 'Banjarbaru Selatan', 'Kemuning', 1, 1, 2, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-12 09:58:37'),
(7, '2026-04-01 04:12:00', -3.44205020, 114.73338690, 'Jl. A Yani km 23, Gg Hidayah, Rt 06 Rw 02, Kel : Landasan Ulin Tengah, Kec : Liang Anggang', 'Liang Anggang', 'Landasan Ulin Tengah', 1, 2, 4, 0, 0, 'Dalam penyelidikan', NULL, '', '', NULL, NULL, 5, '2026-04-12 09:58:37'),
(8, '2026-03-05 19:55:00', -3.44165240, 114.83981610, 'JL. Garuda Rt.03/04 Kel. Komet Kec. Banjarbaru Utara', 'Banjarbaru Utara', 'Komet', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-28 09:06:00'),
(9, '2026-02-20 20:36:00', -3.43701900, 114.83680000, 'Jl. Komet Raya No.50 Rt.01 Kel. Mentaos Kec.Banjarbaru Utara', 'Banjarbaru Utara', 'Komet', 1, 0, 0, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Terjadi Kebakaran Sebuah Bangunan Koperasi Simpan Pinjam Yang diduga Disebabkan Oleh Konsleting Listrik Dari Router Wifi, Kerugian  Kurang Lebih RP.300.000 Dan Tidak Ada Korban Jiwa.', NULL, 5, NULL, '2026-07-21 15:22:18'),
(10, '2026-01-03 18:41:00', -3.44873507, 114.84706729, 'Indomaret, Jalan Mistar Cokrokusumo, Sungai Besar, Banjarbaru, Kalimantan Selatan, Kalimantan, 70714, Indonesia', 'Banjarbaru Selatan', 'Sungai Besar', 1, 0, 0, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Menindaklanjuti laporan Via Grup WA Emergency Banjarbaru telah terjadi konsleting listrik', NULL, 5, NULL, '2026-07-21 15:25:13'),
(11, '2026-01-04 05:20:00', -3.45750265, 114.76273341, 'Jl. Trikora RT. 06 RW. 09 Kel. Landasan Ulin Timur Kec. Landasan Ulin', 'Landasan Ulin', 'Landasan Ulin Timur', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Menindaklanjuti laporan warga Via Grup WA Emergency Banjarbaru telah terjadi kebakaran bangunan. Terdampak 1 Buah Toko Sembako Dhafva Uk. 5 x 6 M. Kerugian 80 Juta Rupiah. Petugas melakukan pemadaman dan pendinginan. Situasi aman terkendali.', NULL, 5, NULL, '2026-07-21 15:28:06'),
(12, '2026-01-10 16:26:00', -3.42742368, 114.76436004, 'Jl. Lingkar Utara RT. 044 RW. 009 Kel. Syamsudin Noor Kec. Landasan Ulin', 'Landasan Ulin', 'Syamsudin Noor', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Menindaklanjuti laporan warga Via Grup WA Emergency Banjarbaru telah terjadi kebakaran bangunan. 1 Buah Kios Pengisian air terbakar 100 %', NULL, 5, NULL, '2026-07-21 15:30:38'),
(13, '2026-01-19 17:06:00', -3.44334001, 114.74310055, 'Jl. Kampung Baru No. 11 RT. 02 RW. 02 Kel. Landasan Ulin Timur Kec. Liang Anggang', 'Landasan Ulin', 'Landasan Ulin Timur', 2, 3, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Menindaklanjuti laporan Via Grup WA Emergency Banjarbaru telah terjadi kebakaran bangunan. Petugas melakukan pemadaman dan pendinginan. Terdampak 80% bangunan terbakar. Asal api masih dalam tahap penyelidikan Kepolisian. Kerugian kurang lebih 100 Juta Rupiah.', NULL, 5, NULL, '2026-07-21 15:37:13');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` enum('super_admin','admin_bpk') NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `aktivitas` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `user_id`, `username`, `role`, `nama`, `aktivitas`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 9, 'superadmin2', 'super_admin', 'abue', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 01:58:22'),
(2, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 01:59:46'),
(3, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data kejadian kebakaran ID: 7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 07:43:56'),
(4, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 14:02:30'),
(5, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 05:56:13'),
(6, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:39:34'),
(7, 6, 'admin_bpk1', 'admin_bpk', NULL, 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:39:39'),
(8, 6, 'admin_bpk1', 'admin_bpk', NULL, 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:39:57'),
(9, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:40:01'),
(10, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK AN-NAFI dan akun Admin BPK: admin_bpk_003', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 07:15:24'),
(11, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: G474H FIRE FIGHTER & RESCUE dan akun Admin BPK: admin_bpk_004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 10:22:19'),
(12, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 10:24:28'),
(13, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 10:24:45'),
(14, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: G474H FIRE FIGHTER & RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 10:29:56'),
(15, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: G474H FIRE FIGHTER & RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 10:30:05'),
(16, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: G474H FIRE FIGHTER & RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 10:36:08'),
(17, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: ETB FIRE RESCUE dan akun Admin BPK: admin_bpk_009', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 14:41:27'),
(18, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK BATRA dan akun Admin BPK: admin_bpk_011', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 14:43:20'),
(19, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 15:39:15'),
(20, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 15:39:25'),
(21, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 15:40:02'),
(22, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 11:03:47'),
(23, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 12:21:02'),
(24, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 14:24:35'),
(25, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 14:34:52'),
(26, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 14:45:12'),
(27, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 14:51:03'),
(28, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 15:08:56'),
(29, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 15:53:10'),
(30, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 15:56:36'),
(31, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 16:11:04'),
(32, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 16:11:09'),
(33, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 16:12:53'),
(34, 9, 'superadmin2', 'super_admin', 'abue', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 16:24:24'),
(35, 9, 'superadmin2', 'super_admin', 'abue', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:44:32'),
(36, 9, 'superadmin2', 'super_admin', 'abue', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:14:37'),
(37, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:14:51'),
(38, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-14 15:20:08'),
(39, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 04:29:00'),
(40, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 04:30:10'),
(41, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 04:30:29'),
(42, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 04:30:50'),
(43, 9, 'superadmin2', 'super_admin', 'abue', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 07:54:34'),
(44, 9, 'superadmin2', 'super_admin', 'abue', 'Melihat rekapitulasi data Sarana & Prasarana seluruh BPK', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 14:25:57'),
(45, 9, 'superadmin2', 'super_admin', 'abue', 'Melihat rekapitulasi data Sarana & Prasarana seluruh BPK', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 14:26:02'),
(46, 9, 'superadmin2', 'super_admin', 'abue', 'Menambahkan data hydrant baru (ID: 121) di Banjarbaru Selatan', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 14:34:16'),
(47, 9, 'superadmin2', 'super_admin', 'abue', 'Menghapus data hydrant ID: 121', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 14:35:04'),
(48, 9, 'superadmin2', 'super_admin', 'abue', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 14:47:51'),
(49, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 14:47:59'),
(50, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Menambahkan anggota baru: Akhmad Juliani (No: 02)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:04:01'),
(51, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Menambahkan anggota baru: Akhmad Juliani (No: 03)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:08:25'),
(52, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Menghapus anggota: Akhmad Juliani', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:08:43'),
(53, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-16 15:09:05'),
(54, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:09:49'),
(55, 8, 'admin_bpk_005', 'admin_bpk', 'PMK HARMA', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:10:44'),
(56, 8, 'admin_bpk_005', 'admin_bpk', 'PMK HARMA', 'Memperbarui data inventaris SAPRAS milik BPK: PMK HARMA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:11:41'),
(57, 8, 'admin_bpk_005', 'admin_bpk', 'PMK HARMA', 'Memperbarui data inventaris SAPRAS milik BPK: PMK HARMA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:12:46'),
(58, 8, 'admin_bpk_005', 'admin_bpk', 'PMK HARMA', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:13:28'),
(59, 9, 'superadmin2', 'super_admin', 'abue', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-17 07:37:17'),
(60, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 07:50:10'),
(61, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: PALAPA FIRE RESCUE dan akun Admin BPK: admin_bpk_010', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 13:12:02'),
(62, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: PALAPA FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 13:15:25'),
(63, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: PALAPA FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 13:15:44'),
(64, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK TRISAKTI dan akun Admin BPK: admin_bpk_012', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 13:27:53'),
(65, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK TRISAKTI', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 13:40:37'),
(66, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK Relawan Haul (RH) dan akun Admin BPK: admin_bpk_013', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 13:43:54'),
(67, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK SIBAT BABBUSALAM dan akun Admin BPK: admin_bpk_014', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 13:45:50'),
(68, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: NEW RESCUE MASPAL dan akun Admin BPK: admin_bpk_015', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:05:07'),
(69, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK RAST dan akun Admin BPK: admin_bpk_016', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:06:56'),
(70, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: AL-BIDAYAH FIRE FIGHTER dan akun Admin BPK: admin_bpk_017', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:08:00'),
(71, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: KAMPUR FIRE RESCUE dan akun Admin BPK: admin_bpk_018', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:10:06'),
(72, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: AL-FATIH FIRE RESCUE dan akun Admin BPK: admin_bpk_019', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:13:01'),
(73, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: AL-MA\'UNAH FIRE dan akun Admin BPK: admin_bpk_020', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:18:22'),
(74, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK GUP dan akun Admin BPK: admin_bpk_021', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:19:41'),
(75, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: SAUDARA FIRE RESCUE dan akun Admin BPK: admin_bpk_022', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:21:34'),
(76, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK BAROKAH SYAMSUDIN NOOR dan akun Admin BPK: admin_bpk_023', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:28:17'),
(77, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK SEPAKAT dan akun Admin BPK: admin_bpk_024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:29:19'),
(78, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: GUNTUNG MANGGIS FIRE RESCUE dan akun Admin BPK: admin_bpk_025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:31:13'),
(79, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK PRABU dan akun Admin BPK: admin_bpk_026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:32:32'),
(80, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: PENGAYUAN RESCUE dan akun Admin BPK: admin_bpk_027', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:34:03'),
(81, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK SWASTA PRIBUMI 07 dan akun Admin BPK: admin_bpk_028', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:39:02'),
(82, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan BPK: BPK RELAWAN BINA PUTRA BERSATU 2025 FIRE RESCUE dan akun Admin BPK: admin_bpk_029', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:40:51'),
(83, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: Emergency Hayati', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:42:03'),
(84, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK AN-NAFI', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:43:31'),
(85, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: G474H FIRE FIGHTER & RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:44:31'),
(86, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: PMK HARMA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:45:25'),
(87, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: MADA FIRE GROUP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:46:11'),
(88, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: PMK BAUNTUNG', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:48:08'),
(89, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: REGAS FIRE & RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:49:25'),
(90, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: ETB FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:49:59'),
(91, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK BATRA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:51:17'),
(92, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK TRISAKTI', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:52:08'),
(93, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK INSAR 21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:53:01'),
(94, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK GUP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 14:54:03'),
(95, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan akun baru: admin_bpk_006 (ADMIN BPK)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:05:58'),
(96, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan akun baru: admin_bpk_007 (ADMIN BPK)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:06:36'),
(97, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan akun baru: admin_bpk_008 (ADMIN BPK)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:07:29'),
(98, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan data kejadian kebakaran baru (ID: 9)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:22:18'),
(99, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan data kejadian kebakaran baru (ID: 10)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:25:13'),
(100, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan data kejadian kebakaran baru (ID: 11)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:28:06'),
(101, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan data kejadian kebakaran baru (ID: 12)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:30:38'),
(102, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan data kejadian kebakaran baru (ID: 13)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:37:13');

-- --------------------------------------------------------

--
-- Table structure for table `sapras_bpk`
--

CREATE TABLE `sapras_bpk` (
  `id` int(11) NOT NULL,
  `bpk_id` int(11) NOT NULL,
  `mobil_tangki` int(11) DEFAULT 0,
  `mobil_portabel` int(11) DEFAULT 0,
  `mesin_pompa` int(11) DEFAULT 0,
  `selang_1_5_inc` int(11) DEFAULT 0,
  `selang_2_5_inc` int(11) DEFAULT 0,
  `selang_isap` int(11) DEFAULT 0,
  `nozle` int(11) DEFAULT 0,
  `helm_apd` int(11) DEFAULT 0,
  `baju_apd` int(11) DEFAULT 0,
  `celana_apd` int(11) DEFAULT 0,
  `sepatu_apd` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sapras_bpk`
--

INSERT INTO `sapras_bpk` (`id`, `bpk_id`, `mobil_tangki`, `mobil_portabel`, `mesin_pompa`, `selang_1_5_inc`, `selang_2_5_inc`, `selang_isap`, `nozle`, `helm_apd`, `baju_apd`, `celana_apd`, `sepatu_apd`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 2, 2, 3, 6, 2, 2, 2, 2, 2, 4, '2026-07-14 15:17:17', '2026-07-14 15:17:17'),
(2, 5, 1, 2, 3, 4, 5, 3, 3, 2, 2, 2, 3, '2026-07-16 15:11:41', '2026-07-16 15:12:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `role` enum('super_admin','admin_bpk') NOT NULL,
  `bpk_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `nama`, `password`, `no_hp`, `role`, `bpk_id`, `created_at`, `updated_at`) VALUES
(5, 'superadmin', 'yusfi', '$2y$10$RSeBrsl2uu8PUFXOVZF0/OrclLt3PFFRBnT7QGXx3Qt5v.5YCB7dm', '081234567890', 'super_admin', NULL, '2026-04-25 03:02:50', '2026-07-04 11:03:26'),
(6, 'admin_bpk_001', 'BPK INSAR 21', '$2y$10$RSeBrsl2uu8PUFXOVZF0/OrclLt3PFFRBnT7QGXx3Qt5v.5YCB7dm', '081234567891', 'admin_bpk', 1, '2026-04-25 03:02:50', '2026-07-07 10:57:49'),
(7, 'admin_bpk_002', 'Emergency Hayati', '$2y$10$RSeBrsl2uu8PUFXOVZF0/OrclLt3PFFRBnT7QGXx3Qt5v.5YCB7dm', '081234567892', 'admin_bpk', 2, '2026-04-25 03:02:50', '2026-07-21 14:42:03'),
(8, 'admin_bpk_005', 'PMK HARMA', '$2y$10$RSeBrsl2uu8PUFXOVZF0/OrclLt3PFFRBnT7QGXx3Qt5v.5YCB7dm', '081234567893', 'admin_bpk', 5, '2026-04-25 03:02:50', '2026-07-07 10:57:03'),
(9, 'superadmin2', 'abue', '$2y$10$fiItG1YuM.X2.UbbFkxNr.djDKfKjyIyXtLm/g4srcjj6AlfPjy0O', '081264905183', 'super_admin', NULL, '2026-07-04 11:03:15', NULL),
(10, 'admin_bpk_003', 'BPK AN-NAFI', '$2y$10$ymerrB2I2XZ2yBi5JSl.Vucew5ep9/OUvGTW.WHrIzI3HMv4947.6', '081234567875', 'admin_bpk', 3, '2026-07-07 07:15:24', NULL),
(11, 'admin_bpk_004', 'G474H FIRE FIGHTER & RESCUE', '$2y$10$ROmQq9S9F8ptHLkYsLlOw.XI/fAA9pVeL38UHS5DGOuVooWRVMLri', '081234567872', 'admin_bpk', 4, '2026-07-07 10:22:19', NULL),
(12, 'admin_bpk_009', 'ETB FIRE RESCUE', '$2y$10$SWKQqukz52SRsPYYmzpgs.mOGwHp62jEBuu9zmfa4QsUuV52Kpn86', '081234567818', 'admin_bpk', 9, '2026-07-07 14:41:27', NULL),
(13, 'admin_bpk_011', 'BPK BATRA', '$2y$10$4Y.kkhb8ga1t67Di7NtZzOVvX5lIbXkwb3FfPsu9hfZgrkIlFcHHe', '081234567837', 'admin_bpk', 10, '2026-07-07 14:43:20', NULL),
(14, 'admin_bpk_010', 'PALAPA FIRE RESCUE', '$2y$10$SHWFrMA.xON1b55sj1WZlOovCf8Wv5zToZuLEHr6Idyuipm48CmDC', '081234567866', 'admin_bpk', 11, '2026-07-21 13:12:02', NULL),
(15, 'admin_bpk_012', 'BPK TRISAKTI', '$2y$10$qY3elfCXsbxeT5DksAyaZuaCZ9u8rch2NwlhVLN.fjSEmwv9MxZy2', '081234567812', 'admin_bpk', 12, '2026-07-21 13:27:53', NULL),
(16, 'admin_bpk_013', 'BPK Relawan Haul (RH)', '$2y$10$KARizVxd/zT8LMX7kkjize9FHitEhZ5drC2RsrAtL1as2ijmO8VzS', '081234567877', 'admin_bpk', 13, '2026-07-21 13:43:54', NULL),
(17, 'admin_bpk_014', 'BPK SIBAT BABBUSALAM', '$2y$10$in8.ONkIMBtC10iNPl/0gOuOjald5nqvi.YmAS3YbITjrkeX8ZfiO', '081234567876', 'admin_bpk', 14, '2026-07-21 13:45:50', NULL),
(18, 'admin_bpk_015', 'NEW RESCUE MASPAL', '$2y$10$xlLnslOyOgkmVyZ.3.u8VuAmarHxp5HQ.dVNRrjSb1xAM0CMvEoY6', '081234567888', 'admin_bpk', 15, '2026-07-21 14:05:07', NULL),
(19, 'admin_bpk_016', 'BPK RAST', '$2y$10$HRk90.GObuYFSlSe8xmQsePoQ3YAkCjYSpArwF83Y/z7h4bU8gG3.', '081234567882', 'admin_bpk', 16, '2026-07-21 14:06:56', NULL),
(20, 'admin_bpk_017', 'AL-BIDAYAH FIRE FIGHTER', '$2y$10$4WnM3EaZbX4pG4gJJ7poweLDdgk49lQp21aIwo7IZQJU2.fEbDvdi', '081234567830', 'admin_bpk', 17, '2026-07-21 14:08:00', NULL),
(21, 'admin_bpk_018', 'KAMPUR FIRE RESCUE', '$2y$10$VDiPtZFtUnctpe2ERbyZ2eFm5o9KULzOc2Zdob4Fk3EmK95r4O2ku', '081234567873', 'admin_bpk', 18, '2026-07-21 14:10:06', NULL),
(22, 'admin_bpk_019', 'AL-FATIH FIRE RESCUE', '$2y$10$o5zRPj.WahyZ7XpBtPShbOjY./pYG5erjI4Y9GCKTgV/qQSUMpzom', '081234567883', 'admin_bpk', 19, '2026-07-21 14:13:01', NULL),
(23, 'admin_bpk_020', 'AL-MA\'UNAH FIRE', '$2y$10$pTiRMgIzgbCMdpC.wxT13Ouis.bNoyapTYnFGExJxOhNoKIiU.gmm', '081234567826', 'admin_bpk', 20, '2026-07-21 14:18:22', NULL),
(24, 'admin_bpk_021', 'BPK GUP', '$2y$10$8iM71GqzYxf6fPtJjjTPR.75k3WluE8Go9NgWoeX5JwwDEgac7NJ2', '081234567812', 'admin_bpk', 21, '2026-07-21 14:19:41', NULL),
(25, 'admin_bpk_022', 'SAUDARA FIRE RESCUE', '$2y$10$g0..WDit0xPcu7LZUabDK.vfsJtu5YToiR2DxhRr7GU1/pWtcsbHi', '081234567898', 'admin_bpk', 22, '2026-07-21 14:21:34', NULL),
(26, 'admin_bpk_023', 'BPK BAROKAH SYAMSUDIN NOOR', '$2y$10$GmMfE.xjN7.JIqNwLRwAD.GxZQTzTobSQvg1lP7U0y5cAcSD8pvU6', '081234567837', 'admin_bpk', 23, '2026-07-21 14:28:17', NULL),
(27, 'admin_bpk_024', 'BPK SEPAKAT', '$2y$10$DxbgnFrLE6dn3nZXadSv2OmoAGXmTmHo23bT59EqUtOCKSWiO33CG', '081234567880', 'admin_bpk', 24, '2026-07-21 14:29:19', NULL),
(28, 'admin_bpk_025', 'GUNTUNG MANGGIS FIRE RESCUE', '$2y$10$mT80e0VB5OtMeRzVx26Rxe2g4okaIZ/dBOa1hqUYHpXaOR5N1vbvS', '081234567890', 'admin_bpk', 25, '2026-07-21 14:31:13', NULL),
(29, 'admin_bpk_026', 'BPK PRABU', '$2y$10$2ZezR1BFWSSE3BOiXnSYsezQHsmkXgxZCEJJkLE51bUF4uy.YKT9.', '081234567811', 'admin_bpk', 26, '2026-07-21 14:32:32', NULL),
(30, 'admin_bpk_027', 'PENGAYUAN RESCUE', '$2y$10$YqdIqGt76ZuVp6gY.ShLZOcnpzQfxnDIPqFBqvQc1ztm6dT/e.tai', '081234567892', 'admin_bpk', 27, '2026-07-21 14:34:03', NULL),
(31, 'admin_bpk_028', 'BPK SWASTA PRIBUMI 07', '$2y$10$c3w64Xf1CB6d6tb/.aakXeIgENtHDvUo02UvItP0T39FOhMB6Lmba', '081234567826', 'admin_bpk', 28, '2026-07-21 14:39:02', NULL),
(32, 'admin_bpk_029', 'BPK RELAWAN BINA PUTRA BERSATU 2025 FIRE RESCUE', '$2y$10$tMUrCYMlj.FY2wd4CZMy..XmjepEqwyCVtHb4n/idqNwv2GnrxxOO', '081234567852', 'admin_bpk', 29, '2026-07-21 14:40:51', NULL),
(33, 'admin_bpk_006', 'MADA FIRE GROUP', '$2y$10$/ae5045.NHYLwVVv/9FssO1IwuPuDwt..qe39j2GAlvXjIE5MaY.S', '081234568465', 'admin_bpk', 6, '2026-07-21 15:05:58', NULL),
(34, 'admin_bpk_007', 'PMK BAUNTUNG', '$2y$10$UEePmvQqfVj0oropQP2CDOX/UWLNNrVOWPvnTsgs7D6Y9GYhxrDoO', '081234567021', 'admin_bpk', 7, '2026-07-21 15:06:35', NULL),
(35, 'admin_bpk_008', 'REGAS FIRE & RESCUE', '$2y$10$69WDlg6HTbQor9fXInWMZ.wC/qrHc/w54tBq8vxKyCBYOl/BkqoC2', '081348630014', 'admin_bpk', 8, '2026-07-21 15:07:29', NULL);

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
  ADD KEY `idx_waktu` (`waktu`),
  ADD KEY `fk_kejadian_dibuat_oleh` (`dibuat_oleh`),
  ADD KEY `fk_kejadian_diupdate_oleh` (`diupdate_oleh`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `sapras_bpk`
--
ALTER TABLE `sapras_bpk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bpk_id` (`bpk_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `bpk`
--
ALTER TABLE `bpk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `heatmap_settings`
--
ALTER TABLE `heatmap_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `hydrant`
--
ALTER TABLE `hydrant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `kejadian_kebakaran`
--
ALTER TABLE `kejadian_kebakaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `sapras_bpk`
--
ALTER TABLE `sapras_bpk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_ibfk_1` FOREIGN KEY (`bpk_id`) REFERENCES `bpk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kejadian_kebakaran`
--
ALTER TABLE `kejadian_kebakaran`
  ADD CONSTRAINT `fk_kejadian_dibuat_oleh` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kejadian_diupdate_oleh` FOREIGN KEY (`diupdate_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sapras_bpk`
--
ALTER TABLE `sapras_bpk`
  ADD CONSTRAINT `sapras_bpk_ibfk_1` FOREIGN KEY (`bpk_id`) REFERENCES `bpk` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
