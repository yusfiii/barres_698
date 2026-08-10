-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2026 at 10:12 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GenerateSemuaAnggotaBPK` ()   BEGIN
    DECLARE id_bpk INT DEFAULT 1;
    DECLARE jumlah_anggota INT;
    DECLARE counter_anggota INT;
    
    -- Looping untuk 29 BPK
    WHILE id_bpk <= 29 DO
        -- Menentukan jumlah anggota acak antara 18 sampai 30 untuk BPK ini
        SET jumlah_anggota = FLOOR(18 + (RAND() * 13));
        SET counter_anggota = 1;
        
        -- Looping untuk memasukkan anggota ke dalam BPK yang sedang diproses
        WHILE counter_anggota <= jumlah_anggota DO
            INSERT INTO `anggota` (
                `nomor_anggota`, `bpk_id`, `nama`, `tempat_lahir`, 
                `tanggal_lahir`, `jenis_kelamin`, `alamat`, `nik`, 
                `no_hp`, `status`, `jabatan`, `created_at`
            ) VALUES (
                counter_anggota, 
                id_bpk, 
                CONCAT('Dummy BPK ', id_bpk, ' - Anggota ', counter_anggota), 
                'Banjarbaru', 
                -- Tanggal lahir acak
                DATE_ADD('1980-01-01', INTERVAL FLOOR(RAND() * 10000) DAY), 
                -- Jenis kelamin acak
                IF(RAND() > 0.3, 'Laki-laki', 'Perempuan'), 
                'DATA_DUMMY_HAPUS', 
                -- NIK dan No HP acak tapi terstruktur
                CONCAT('6372', LPAD(id_bpk, 4, '0'), LPAD(counter_anggota, 8, '0')), 
                CONCAT('0813', LPAD(id_bpk, 4, '0'), LPAD(counter_anggota, 4, '0')), 
                'aktif', 
                -- Anggota pertama diset sebagai Ketua, sisanya Anggota
                IF(counter_anggota = 1, 'Ketua', 'Anggota'),
                NOW()
            );
            
            SET counter_anggota = counter_anggota + 1;
        END WHILE;
        
        SET id_bpk = id_bpk + 1;
    END WHILE;
END$$

DELIMITER ;

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
(126, 1, 1, 'Dummy BPK 1 - Anggota 1', 'Banjarbaru', '1995-05-27', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000100000001', '081300010001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(127, 2, 1, 'Dummy BPK 1 - Anggota 2', 'Banjarbaru', '2004-12-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000100000002', '081300010002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(128, 3, 1, 'Dummy BPK 1 - Anggota 3', 'Banjarbaru', '1986-04-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000003', '081300010003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(129, 4, 1, 'Dummy BPK 1 - Anggota 4', 'Banjarbaru', '1985-04-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000100000004', '081300010004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(130, 5, 1, 'Dummy BPK 1 - Anggota 5', 'Banjarbaru', '1996-02-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000100000005', '081300010005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(131, 6, 1, 'Dummy BPK 1 - Anggota 6', 'Banjarbaru', '1991-09-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000006', '081300010006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(132, 7, 1, 'Dummy BPK 1 - Anggota 7', 'Banjarbaru', '2003-03-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000007', '081300010007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(133, 8, 1, 'Dummy BPK 1 - Anggota 8', 'Banjarbaru', '1986-02-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000100000008', '081300010008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(134, 9, 1, 'Dummy BPK 1 - Anggota 9', 'Banjarbaru', '1997-04-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000009', '081300010009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(135, 10, 1, 'Dummy BPK 1 - Anggota 10', 'Banjarbaru', '2006-07-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000010', '081300010010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(136, 11, 1, 'Dummy BPK 1 - Anggota 11', 'Banjarbaru', '1999-11-19', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000011', '081300010011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(137, 12, 1, 'Dummy BPK 1 - Anggota 12', 'Banjarbaru', '1982-04-02', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000100000012', '081300010012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(138, 13, 1, 'Dummy BPK 1 - Anggota 13', 'Banjarbaru', '1980-09-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000013', '081300010013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(139, 14, 1, 'Dummy BPK 1 - Anggota 14', 'Banjarbaru', '2000-11-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000014', '081300010014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(140, 15, 1, 'Dummy BPK 1 - Anggota 15', 'Banjarbaru', '1986-07-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000015', '081300010015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(141, 16, 1, 'Dummy BPK 1 - Anggota 16', 'Banjarbaru', '1997-12-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000016', '081300010016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(142, 17, 1, 'Dummy BPK 1 - Anggota 17', 'Banjarbaru', '1988-06-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000017', '081300010017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(143, 18, 1, 'Dummy BPK 1 - Anggota 18', 'Banjarbaru', '2007-03-11', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000100000018', '081300010018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(144, 19, 1, 'Dummy BPK 1 - Anggota 19', 'Banjarbaru', '1982-03-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000019', '081300010019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(145, 20, 1, 'Dummy BPK 1 - Anggota 20', 'Banjarbaru', '1996-04-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000100000020', '081300010020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(146, 21, 1, 'Dummy BPK 1 - Anggota 21', 'Banjarbaru', '1995-09-29', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000100000021', '081300010021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(147, 1, 2, 'Dummy BPK 2 - Anggota 1', 'Banjarbaru', '1982-05-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000001', '081300020001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(148, 2, 2, 'Dummy BPK 2 - Anggota 2', 'Banjarbaru', '1989-03-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000002', '081300020002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(149, 3, 2, 'Dummy BPK 2 - Anggota 3', 'Banjarbaru', '1996-01-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000003', '081300020003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(150, 4, 2, 'Dummy BPK 2 - Anggota 4', 'Banjarbaru', '1983-03-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000004', '081300020004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(151, 5, 2, 'Dummy BPK 2 - Anggota 5', 'Banjarbaru', '2005-04-20', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000200000005', '081300020005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(152, 6, 2, 'Dummy BPK 2 - Anggota 6', 'Banjarbaru', '1994-02-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000006', '081300020006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(153, 7, 2, 'Dummy BPK 2 - Anggota 7', 'Banjarbaru', '1993-09-23', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000200000007', '081300020007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(154, 8, 2, 'Dummy BPK 2 - Anggota 8', 'Banjarbaru', '2003-12-28', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000200000008', '081300020008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(155, 9, 2, 'Dummy BPK 2 - Anggota 9', 'Banjarbaru', '1983-09-21', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000200000009', '081300020009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(156, 10, 2, 'Dummy BPK 2 - Anggota 10', 'Banjarbaru', '1999-01-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000010', '081300020010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(157, 11, 2, 'Dummy BPK 2 - Anggota 11', 'Banjarbaru', '1980-07-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000011', '081300020011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(158, 12, 2, 'Dummy BPK 2 - Anggota 12', 'Banjarbaru', '1982-02-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000012', '081300020012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(159, 13, 2, 'Dummy BPK 2 - Anggota 13', 'Banjarbaru', '1989-01-18', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000200000013', '081300020013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(160, 14, 2, 'Dummy BPK 2 - Anggota 14', 'Banjarbaru', '1995-09-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000014', '081300020014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(161, 15, 2, 'Dummy BPK 2 - Anggota 15', 'Banjarbaru', '2004-07-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000015', '081300020015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(162, 16, 2, 'Dummy BPK 2 - Anggota 16', 'Banjarbaru', '2004-09-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000016', '081300020016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(163, 17, 2, 'Dummy BPK 2 - Anggota 17', 'Banjarbaru', '1981-05-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000017', '081300020017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(164, 18, 2, 'Dummy BPK 2 - Anggota 18', 'Banjarbaru', '2002-12-23', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000200000018', '081300020018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(165, 19, 2, 'Dummy BPK 2 - Anggota 19', 'Banjarbaru', '1993-07-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000019', '081300020019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(166, 20, 2, 'Dummy BPK 2 - Anggota 20', 'Banjarbaru', '2003-02-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000020', '081300020020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(167, 21, 2, 'Dummy BPK 2 - Anggota 21', 'Banjarbaru', '1995-12-27', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000200000021', '081300020021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(168, 22, 2, 'Dummy BPK 2 - Anggota 22', 'Banjarbaru', '1993-09-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000022', '081300020022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(169, 23, 2, 'Dummy BPK 2 - Anggota 23', 'Banjarbaru', '1986-08-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000200000023', '081300020023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(170, 24, 2, 'Dummy BPK 2 - Anggota 24', 'Banjarbaru', '1982-06-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000024', '081300020024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(171, 25, 2, 'Dummy BPK 2 - Anggota 25', 'Banjarbaru', '1994-10-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000200000025', '081300020025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(172, 1, 3, 'Dummy BPK 3 - Anggota 1', 'Banjarbaru', '2003-04-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000001', '081300030001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(173, 2, 3, 'Dummy BPK 3 - Anggota 2', 'Banjarbaru', '2002-10-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000002', '081300030002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(174, 3, 3, 'Dummy BPK 3 - Anggota 3', 'Banjarbaru', '1984-07-10', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000300000003', '081300030003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(175, 4, 3, 'Dummy BPK 3 - Anggota 4', 'Banjarbaru', '1997-07-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000004', '081300030004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(176, 5, 3, 'Dummy BPK 3 - Anggota 5', 'Banjarbaru', '1999-05-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000005', '081300030005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(177, 6, 3, 'Dummy BPK 3 - Anggota 6', 'Banjarbaru', '1997-07-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000006', '081300030006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(178, 7, 3, 'Dummy BPK 3 - Anggota 7', 'Banjarbaru', '2002-08-18', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000300000007', '081300030007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(179, 8, 3, 'Dummy BPK 3 - Anggota 8', 'Banjarbaru', '2005-08-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000008', '081300030008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(180, 9, 3, 'Dummy BPK 3 - Anggota 9', 'Banjarbaru', '1988-11-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000009', '081300030009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(181, 10, 3, 'Dummy BPK 3 - Anggota 10', 'Banjarbaru', '2000-01-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000010', '081300030010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(182, 11, 3, 'Dummy BPK 3 - Anggota 11', 'Banjarbaru', '2007-05-03', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000300000011', '081300030011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(183, 12, 3, 'Dummy BPK 3 - Anggota 12', 'Banjarbaru', '1990-06-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000012', '081300030012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(184, 13, 3, 'Dummy BPK 3 - Anggota 13', 'Banjarbaru', '1987-07-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000013', '081300030013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(185, 14, 3, 'Dummy BPK 3 - Anggota 14', 'Banjarbaru', '2001-03-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000014', '081300030014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(186, 15, 3, 'Dummy BPK 3 - Anggota 15', 'Banjarbaru', '1986-05-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000015', '081300030015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(187, 16, 3, 'Dummy BPK 3 - Anggota 16', 'Banjarbaru', '1991-11-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000016', '081300030016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(188, 17, 3, 'Dummy BPK 3 - Anggota 17', 'Banjarbaru', '1984-11-04', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000300000017', '081300030017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(189, 18, 3, 'Dummy BPK 3 - Anggota 18', 'Banjarbaru', '1992-04-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000018', '081300030018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(190, 19, 3, 'Dummy BPK 3 - Anggota 19', 'Banjarbaru', '2005-10-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000019', '081300030019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(191, 20, 3, 'Dummy BPK 3 - Anggota 20', 'Banjarbaru', '2003-07-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000300000020', '081300030020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(192, 21, 3, 'Dummy BPK 3 - Anggota 21', 'Banjarbaru', '2002-08-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000021', '081300030021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(193, 22, 3, 'Dummy BPK 3 - Anggota 22', 'Banjarbaru', '2007-04-07', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000300000022', '081300030022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(194, 23, 3, 'Dummy BPK 3 - Anggota 23', 'Banjarbaru', '1993-01-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000300000023', '081300030023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(195, 1, 4, 'Dummy BPK 4 - Anggota 1', 'Banjarbaru', '2002-04-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000001', '081300040001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(196, 2, 4, 'Dummy BPK 4 - Anggota 2', 'Banjarbaru', '2007-04-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000002', '081300040002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(197, 3, 4, 'Dummy BPK 4 - Anggota 3', 'Banjarbaru', '1996-02-26', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000400000003', '081300040003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(198, 4, 4, 'Dummy BPK 4 - Anggota 4', 'Banjarbaru', '1986-10-24', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000400000004', '081300040004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(199, 5, 4, 'Dummy BPK 4 - Anggota 5', 'Banjarbaru', '1991-06-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000005', '081300040005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(200, 6, 4, 'Dummy BPK 4 - Anggota 6', 'Banjarbaru', '1999-01-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000006', '081300040006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(201, 7, 4, 'Dummy BPK 4 - Anggota 7', 'Banjarbaru', '1992-08-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000007', '081300040007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(202, 8, 4, 'Dummy BPK 4 - Anggota 8', 'Banjarbaru', '1993-07-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000008', '081300040008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(203, 9, 4, 'Dummy BPK 4 - Anggota 9', 'Banjarbaru', '1986-08-16', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000400000009', '081300040009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(204, 10, 4, 'Dummy BPK 4 - Anggota 10', 'Banjarbaru', '1984-02-04', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000400000010', '081300040010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(205, 11, 4, 'Dummy BPK 4 - Anggota 11', 'Banjarbaru', '1998-03-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000011', '081300040011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(206, 12, 4, 'Dummy BPK 4 - Anggota 12', 'Banjarbaru', '1987-07-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000012', '081300040012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(207, 13, 4, 'Dummy BPK 4 - Anggota 13', 'Banjarbaru', '1986-03-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000013', '081300040013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(208, 14, 4, 'Dummy BPK 4 - Anggota 14', 'Banjarbaru', '2003-06-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000014', '081300040014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(209, 15, 4, 'Dummy BPK 4 - Anggota 15', 'Banjarbaru', '1986-04-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000015', '081300040015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(210, 16, 4, 'Dummy BPK 4 - Anggota 16', 'Banjarbaru', '1998-05-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000016', '081300040016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(211, 17, 4, 'Dummy BPK 4 - Anggota 17', 'Banjarbaru', '1998-06-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000400000017', '081300040017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(212, 18, 4, 'Dummy BPK 4 - Anggota 18', 'Banjarbaru', '2000-10-03', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000400000018', '081300040018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(213, 1, 5, 'Dummy BPK 5 - Anggota 1', 'Banjarbaru', '1989-05-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000001', '081300050001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(214, 2, 5, 'Dummy BPK 5 - Anggota 2', 'Banjarbaru', '1990-04-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000002', '081300050002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(215, 3, 5, 'Dummy BPK 5 - Anggota 3', 'Banjarbaru', '1981-11-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000003', '081300050003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(216, 4, 5, 'Dummy BPK 5 - Anggota 4', 'Banjarbaru', '2005-03-08', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000500000004', '081300050004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(217, 5, 5, 'Dummy BPK 5 - Anggota 5', 'Banjarbaru', '2003-07-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000005', '081300050005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(218, 6, 5, 'Dummy BPK 5 - Anggota 6', 'Banjarbaru', '1981-06-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000006', '081300050006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(219, 7, 5, 'Dummy BPK 5 - Anggota 7', 'Banjarbaru', '1987-10-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000007', '081300050007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(220, 8, 5, 'Dummy BPK 5 - Anggota 8', 'Banjarbaru', '2004-11-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000008', '081300050008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(221, 9, 5, 'Dummy BPK 5 - Anggota 9', 'Banjarbaru', '1999-04-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000009', '081300050009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(222, 10, 5, 'Dummy BPK 5 - Anggota 10', 'Banjarbaru', '1986-04-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000010', '081300050010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(223, 11, 5, 'Dummy BPK 5 - Anggota 11', 'Banjarbaru', '1980-02-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000011', '081300050011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(224, 12, 5, 'Dummy BPK 5 - Anggota 12', 'Banjarbaru', '1980-06-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000012', '081300050012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(225, 13, 5, 'Dummy BPK 5 - Anggota 13', 'Banjarbaru', '1987-05-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000013', '081300050013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(226, 14, 5, 'Dummy BPK 5 - Anggota 14', 'Banjarbaru', '2007-04-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000014', '081300050014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(227, 15, 5, 'Dummy BPK 5 - Anggota 15', 'Banjarbaru', '1993-03-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000015', '081300050015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(228, 16, 5, 'Dummy BPK 5 - Anggota 16', 'Banjarbaru', '1986-01-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000016', '081300050016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(229, 17, 5, 'Dummy BPK 5 - Anggota 17', 'Banjarbaru', '2003-05-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000017', '081300050017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(230, 18, 5, 'Dummy BPK 5 - Anggota 18', 'Banjarbaru', '1985-08-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000018', '081300050018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(231, 19, 5, 'Dummy BPK 5 - Anggota 19', 'Banjarbaru', '1986-05-05', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000500000019', '081300050019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(232, 20, 5, 'Dummy BPK 5 - Anggota 20', 'Banjarbaru', '1995-02-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000500000020', '081300050020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(233, 21, 5, 'Dummy BPK 5 - Anggota 21', 'Banjarbaru', '1990-10-11', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000500000021', '081300050021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(234, 22, 5, 'Dummy BPK 5 - Anggota 22', 'Banjarbaru', '1994-02-18', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000500000022', '081300050022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(235, 1, 6, 'Dummy BPK 6 - Anggota 1', 'Banjarbaru', '2003-01-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000001', '081300060001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(236, 2, 6, 'Dummy BPK 6 - Anggota 2', 'Banjarbaru', '1993-09-17', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000600000002', '081300060002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(237, 3, 6, 'Dummy BPK 6 - Anggota 3', 'Banjarbaru', '2006-02-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000003', '081300060003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(238, 4, 6, 'Dummy BPK 6 - Anggota 4', 'Banjarbaru', '1996-05-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000004', '081300060004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(239, 5, 6, 'Dummy BPK 6 - Anggota 5', 'Banjarbaru', '2002-01-29', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000600000005', '081300060005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(240, 6, 6, 'Dummy BPK 6 - Anggota 6', 'Banjarbaru', '2005-06-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000006', '081300060006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(241, 7, 6, 'Dummy BPK 6 - Anggota 7', 'Banjarbaru', '1991-02-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000007', '081300060007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(242, 8, 6, 'Dummy BPK 6 - Anggota 8', 'Banjarbaru', '1990-03-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000008', '081300060008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(243, 9, 6, 'Dummy BPK 6 - Anggota 9', 'Banjarbaru', '1991-12-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000600000009', '081300060009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(244, 10, 6, 'Dummy BPK 6 - Anggota 10', 'Banjarbaru', '2005-05-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000010', '081300060010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(245, 11, 6, 'Dummy BPK 6 - Anggota 11', 'Banjarbaru', '1998-05-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000011', '081300060011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(246, 12, 6, 'Dummy BPK 6 - Anggota 12', 'Banjarbaru', '1991-02-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000012', '081300060012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(247, 13, 6, 'Dummy BPK 6 - Anggota 13', 'Banjarbaru', '1998-11-11', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000600000013', '081300060013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(248, 14, 6, 'Dummy BPK 6 - Anggota 14', 'Banjarbaru', '1991-10-18', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000600000014', '081300060014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(249, 15, 6, 'Dummy BPK 6 - Anggota 15', 'Banjarbaru', '2006-12-22', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000600000015', '081300060015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(250, 16, 6, 'Dummy BPK 6 - Anggota 16', 'Banjarbaru', '2003-01-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000016', '081300060016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(251, 17, 6, 'Dummy BPK 6 - Anggota 17', 'Banjarbaru', '1983-09-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000017', '081300060017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(252, 18, 6, 'Dummy BPK 6 - Anggota 18', 'Banjarbaru', '2006-06-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000018', '081300060018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(253, 19, 6, 'Dummy BPK 6 - Anggota 19', 'Banjarbaru', '1984-01-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000019', '081300060019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(254, 20, 6, 'Dummy BPK 6 - Anggota 20', 'Banjarbaru', '1983-10-04', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000600000020', '081300060020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(255, 21, 6, 'Dummy BPK 6 - Anggota 21', 'Banjarbaru', '1987-04-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000600000021', '081300060021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(256, 1, 7, 'Dummy BPK 7 - Anggota 1', 'Banjarbaru', '1988-01-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000001', '081300070001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(257, 2, 7, 'Dummy BPK 7 - Anggota 2', 'Banjarbaru', '1987-01-28', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000002', '081300070002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(258, 3, 7, 'Dummy BPK 7 - Anggota 3', 'Banjarbaru', '1987-08-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000003', '081300070003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(259, 4, 7, 'Dummy BPK 7 - Anggota 4', 'Banjarbaru', '2007-01-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000004', '081300070004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(260, 5, 7, 'Dummy BPK 7 - Anggota 5', 'Banjarbaru', '1987-07-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000005', '081300070005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(261, 6, 7, 'Dummy BPK 7 - Anggota 6', 'Banjarbaru', '1988-06-13', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000006', '081300070006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(262, 7, 7, 'Dummy BPK 7 - Anggota 7', 'Banjarbaru', '1990-11-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000007', '081300070007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(263, 8, 7, 'Dummy BPK 7 - Anggota 8', 'Banjarbaru', '2000-11-19', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000008', '081300070008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(264, 9, 7, 'Dummy BPK 7 - Anggota 9', 'Banjarbaru', '2005-03-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000009', '081300070009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(265, 10, 7, 'Dummy BPK 7 - Anggota 10', 'Banjarbaru', '1997-07-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000010', '081300070010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(266, 11, 7, 'Dummy BPK 7 - Anggota 11', 'Banjarbaru', '2003-05-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000011', '081300070011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(267, 12, 7, 'Dummy BPK 7 - Anggota 12', 'Banjarbaru', '1992-07-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000012', '081300070012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(268, 13, 7, 'Dummy BPK 7 - Anggota 13', 'Banjarbaru', '1980-09-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000013', '081300070013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(269, 14, 7, 'Dummy BPK 7 - Anggota 14', 'Banjarbaru', '1990-08-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000014', '081300070014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(270, 15, 7, 'Dummy BPK 7 - Anggota 15', 'Banjarbaru', '1983-03-11', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000015', '081300070015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(271, 16, 7, 'Dummy BPK 7 - Anggota 16', 'Banjarbaru', '1999-12-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000016', '081300070016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(272, 17, 7, 'Dummy BPK 7 - Anggota 17', 'Banjarbaru', '2001-05-16', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000017', '081300070017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(273, 18, 7, 'Dummy BPK 7 - Anggota 18', 'Banjarbaru', '1987-09-19', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000018', '081300070018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(274, 19, 7, 'Dummy BPK 7 - Anggota 19', 'Banjarbaru', '1989-01-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000019', '081300070019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(275, 20, 7, 'Dummy BPK 7 - Anggota 20', 'Banjarbaru', '2000-01-28', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000020', '081300070020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(276, 21, 7, 'Dummy BPK 7 - Anggota 21', 'Banjarbaru', '2006-05-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000021', '081300070021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(277, 22, 7, 'Dummy BPK 7 - Anggota 22', 'Banjarbaru', '1998-08-25', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000022', '081300070022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(278, 23, 7, 'Dummy BPK 7 - Anggota 23', 'Banjarbaru', '1987-10-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000023', '081300070023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(279, 24, 7, 'Dummy BPK 7 - Anggota 24', 'Banjarbaru', '1986-12-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000024', '081300070024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(280, 25, 7, 'Dummy BPK 7 - Anggota 25', 'Banjarbaru', '1987-11-28', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000700000025', '081300070025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(281, 26, 7, 'Dummy BPK 7 - Anggota 26', 'Banjarbaru', '1985-05-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000026', '081300070026', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(282, 27, 7, 'Dummy BPK 7 - Anggota 27', 'Banjarbaru', '1982-11-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000027', '081300070027', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(283, 28, 7, 'Dummy BPK 7 - Anggota 28', 'Banjarbaru', '1986-03-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000700000028', '081300070028', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(284, 1, 8, 'Dummy BPK 8 - Anggota 1', 'Banjarbaru', '2005-09-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000001', '081300080001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(285, 2, 8, 'Dummy BPK 8 - Anggota 2', 'Banjarbaru', '1991-05-30', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000800000002', '081300080002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(286, 3, 8, 'Dummy BPK 8 - Anggota 3', 'Banjarbaru', '1992-05-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000003', '081300080003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(287, 4, 8, 'Dummy BPK 8 - Anggota 4', 'Banjarbaru', '2003-08-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000004', '081300080004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(288, 5, 8, 'Dummy BPK 8 - Anggota 5', 'Banjarbaru', '1988-01-20', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000800000005', '081300080005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(289, 6, 8, 'Dummy BPK 8 - Anggota 6', 'Banjarbaru', '2002-10-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000006', '081300080006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(290, 7, 8, 'Dummy BPK 8 - Anggota 7', 'Banjarbaru', '1984-08-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000007', '081300080007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(291, 8, 8, 'Dummy BPK 8 - Anggota 8', 'Banjarbaru', '2000-04-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000008', '081300080008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(292, 9, 8, 'Dummy BPK 8 - Anggota 9', 'Banjarbaru', '1994-07-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000009', '081300080009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(293, 10, 8, 'Dummy BPK 8 - Anggota 10', 'Banjarbaru', '1992-03-25', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000800000010', '081300080010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(294, 11, 8, 'Dummy BPK 8 - Anggota 11', 'Banjarbaru', '1999-02-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000011', '081300080011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(295, 12, 8, 'Dummy BPK 8 - Anggota 12', 'Banjarbaru', '1987-04-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000012', '081300080012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(296, 13, 8, 'Dummy BPK 8 - Anggota 13', 'Banjarbaru', '2001-06-24', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000800000013', '081300080013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(297, 14, 8, 'Dummy BPK 8 - Anggota 14', 'Banjarbaru', '1993-09-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000014', '081300080014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(298, 15, 8, 'Dummy BPK 8 - Anggota 15', 'Banjarbaru', '1992-06-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000015', '081300080015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(299, 16, 8, 'Dummy BPK 8 - Anggota 16', 'Banjarbaru', '1984-03-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000016', '081300080016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(300, 17, 8, 'Dummy BPK 8 - Anggota 17', 'Banjarbaru', '2002-09-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000800000017', '081300080017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(301, 18, 8, 'Dummy BPK 8 - Anggota 18', 'Banjarbaru', '1990-10-08', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000800000018', '081300080018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(302, 19, 8, 'Dummy BPK 8 - Anggota 19', 'Banjarbaru', '2007-04-20', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000800000019', '081300080019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(303, 20, 8, 'Dummy BPK 8 - Anggota 20', 'Banjarbaru', '1991-02-14', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000800000020', '081300080020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(304, 21, 8, 'Dummy BPK 8 - Anggota 21', 'Banjarbaru', '2000-05-24', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000800000021', '081300080021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(305, 22, 8, 'Dummy BPK 8 - Anggota 22', 'Banjarbaru', '1993-08-10', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000800000022', '081300080022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(306, 1, 9, 'Dummy BPK 9 - Anggota 1', 'Banjarbaru', '1995-04-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000001', '081300090001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(307, 2, 9, 'Dummy BPK 9 - Anggota 2', 'Banjarbaru', '1985-01-30', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000002', '081300090002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(308, 3, 9, 'Dummy BPK 9 - Anggota 3', 'Banjarbaru', '1993-11-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000003', '081300090003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(309, 4, 9, 'Dummy BPK 9 - Anggota 4', 'Banjarbaru', '2005-08-27', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000004', '081300090004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(310, 5, 9, 'Dummy BPK 9 - Anggota 5', 'Banjarbaru', '1986-12-05', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000005', '081300090005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(311, 6, 9, 'Dummy BPK 9 - Anggota 6', 'Banjarbaru', '1990-03-27', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000006', '081300090006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(312, 7, 9, 'Dummy BPK 9 - Anggota 7', 'Banjarbaru', '2002-08-19', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000007', '081300090007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(313, 8, 9, 'Dummy BPK 9 - Anggota 8', 'Banjarbaru', '1989-05-13', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000008', '081300090008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(314, 9, 9, 'Dummy BPK 9 - Anggota 9', 'Banjarbaru', '1981-08-15', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000009', '081300090009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(315, 10, 9, 'Dummy BPK 9 - Anggota 10', 'Banjarbaru', '1981-04-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000010', '081300090010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(316, 11, 9, 'Dummy BPK 9 - Anggota 11', 'Banjarbaru', '1989-07-02', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000011', '081300090011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(317, 12, 9, 'Dummy BPK 9 - Anggota 12', 'Banjarbaru', '1984-11-03', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000012', '081300090012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(318, 13, 9, 'Dummy BPK 9 - Anggota 13', 'Banjarbaru', '1986-06-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000013', '081300090013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(319, 14, 9, 'Dummy BPK 9 - Anggota 14', 'Banjarbaru', '2004-09-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000014', '081300090014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(320, 15, 9, 'Dummy BPK 9 - Anggota 15', 'Banjarbaru', '1980-03-29', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000015', '081300090015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(321, 16, 9, 'Dummy BPK 9 - Anggota 16', 'Banjarbaru', '1981-05-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000016', '081300090016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(322, 17, 9, 'Dummy BPK 9 - Anggota 17', 'Banjarbaru', '2003-09-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000017', '081300090017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(323, 18, 9, 'Dummy BPK 9 - Anggota 18', 'Banjarbaru', '1980-01-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000018', '081300090018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(324, 19, 9, 'Dummy BPK 9 - Anggota 19', 'Banjarbaru', '1986-03-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000019', '081300090019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(325, 20, 9, 'Dummy BPK 9 - Anggota 20', 'Banjarbaru', '1988-09-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000020', '081300090020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(326, 21, 9, 'Dummy BPK 9 - Anggota 21', 'Banjarbaru', '2006-11-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000021', '081300090021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(327, 22, 9, 'Dummy BPK 9 - Anggota 22', 'Banjarbaru', '2002-05-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000022', '081300090022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(328, 23, 9, 'Dummy BPK 9 - Anggota 23', 'Banjarbaru', '1999-01-17', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000023', '081300090023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(329, 24, 9, 'Dummy BPK 9 - Anggota 24', 'Banjarbaru', '2003-05-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000024', '081300090024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(330, 25, 9, 'Dummy BPK 9 - Anggota 25', 'Banjarbaru', '2006-10-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372000900000025', '081300090025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(331, 26, 9, 'Dummy BPK 9 - Anggota 26', 'Banjarbaru', '2004-01-05', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000026', '081300090026', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(332, 27, 9, 'Dummy BPK 9 - Anggota 27', 'Banjarbaru', '2003-10-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372000900000027', '081300090027', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(333, 1, 10, 'Dummy BPK 10 - Anggota 1', 'Banjarbaru', '2004-12-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000001', '081300100001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(334, 2, 10, 'Dummy BPK 10 - Anggota 2', 'Banjarbaru', '2006-04-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000002', '081300100002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(335, 3, 10, 'Dummy BPK 10 - Anggota 3', 'Banjarbaru', '1980-06-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000003', '081300100003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(336, 4, 10, 'Dummy BPK 10 - Anggota 4', 'Banjarbaru', '1998-11-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000004', '081300100004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(337, 5, 10, 'Dummy BPK 10 - Anggota 5', 'Banjarbaru', '2006-03-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000005', '081300100005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(338, 6, 10, 'Dummy BPK 10 - Anggota 6', 'Banjarbaru', '2007-03-05', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001000000006', '081300100006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(339, 7, 10, 'Dummy BPK 10 - Anggota 7', 'Banjarbaru', '1986-06-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000007', '081300100007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(340, 8, 10, 'Dummy BPK 10 - Anggota 8', 'Banjarbaru', '1994-07-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000008', '081300100008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(341, 9, 10, 'Dummy BPK 10 - Anggota 9', 'Banjarbaru', '2006-06-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000009', '081300100009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(342, 10, 10, 'Dummy BPK 10 - Anggota 10', 'Banjarbaru', '1995-11-03', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001000000010', '081300100010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(343, 11, 10, 'Dummy BPK 10 - Anggota 11', 'Banjarbaru', '1988-03-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000011', '081300100011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(344, 12, 10, 'Dummy BPK 10 - Anggota 12', 'Banjarbaru', '1993-02-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000012', '081300100012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(345, 13, 10, 'Dummy BPK 10 - Anggota 13', 'Banjarbaru', '1991-10-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000013', '081300100013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(346, 14, 10, 'Dummy BPK 10 - Anggota 14', 'Banjarbaru', '2003-06-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000014', '081300100014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(347, 15, 10, 'Dummy BPK 10 - Anggota 15', 'Banjarbaru', '1990-06-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000015', '081300100015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(348, 16, 10, 'Dummy BPK 10 - Anggota 16', 'Banjarbaru', '1994-08-29', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001000000016', '081300100016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(349, 17, 10, 'Dummy BPK 10 - Anggota 17', 'Banjarbaru', '2000-06-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000017', '081300100017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(350, 18, 10, 'Dummy BPK 10 - Anggota 18', 'Banjarbaru', '1990-04-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000018', '081300100018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(351, 19, 10, 'Dummy BPK 10 - Anggota 19', 'Banjarbaru', '1991-10-16', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001000000019', '081300100019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(352, 20, 10, 'Dummy BPK 10 - Anggota 20', 'Banjarbaru', '2001-09-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000020', '081300100020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(353, 21, 10, 'Dummy BPK 10 - Anggota 21', 'Banjarbaru', '1984-11-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000021', '081300100021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(354, 22, 10, 'Dummy BPK 10 - Anggota 22', 'Banjarbaru', '1987-03-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000022', '081300100022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(355, 23, 10, 'Dummy BPK 10 - Anggota 23', 'Banjarbaru', '1990-01-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000023', '081300100023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(356, 24, 10, 'Dummy BPK 10 - Anggota 24', 'Banjarbaru', '1997-07-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000024', '081300100024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(357, 25, 10, 'Dummy BPK 10 - Anggota 25', 'Banjarbaru', '2003-12-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001000000025', '081300100025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48');
INSERT INTO `anggota` (`id`, `nomor_anggota`, `bpk_id`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `nik`, `no_hp`, `status`, `jabatan`, `foto`, `foto_ktp`, `created_at`, `updated_at`) VALUES
(358, 1, 11, 'Dummy BPK 11 - Anggota 1', 'Banjarbaru', '1982-02-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000001', '081300110001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(359, 2, 11, 'Dummy BPK 11 - Anggota 2', 'Banjarbaru', '1987-04-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000002', '081300110002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(360, 3, 11, 'Dummy BPK 11 - Anggota 3', 'Banjarbaru', '1990-01-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000003', '081300110003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(361, 4, 11, 'Dummy BPK 11 - Anggota 4', 'Banjarbaru', '1995-06-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001100000004', '081300110004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(362, 5, 11, 'Dummy BPK 11 - Anggota 5', 'Banjarbaru', '1991-03-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000005', '081300110005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(363, 6, 11, 'Dummy BPK 11 - Anggota 6', 'Banjarbaru', '1999-06-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000006', '081300110006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(364, 7, 11, 'Dummy BPK 11 - Anggota 7', 'Banjarbaru', '2002-02-17', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001100000007', '081300110007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(365, 8, 11, 'Dummy BPK 11 - Anggota 8', 'Banjarbaru', '2006-06-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000008', '081300110008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(366, 9, 11, 'Dummy BPK 11 - Anggota 9', 'Banjarbaru', '1982-01-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000009', '081300110009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(367, 10, 11, 'Dummy BPK 11 - Anggota 10', 'Banjarbaru', '2000-02-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000010', '081300110010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(368, 11, 11, 'Dummy BPK 11 - Anggota 11', 'Banjarbaru', '1988-07-11', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001100000011', '081300110011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(369, 12, 11, 'Dummy BPK 11 - Anggota 12', 'Banjarbaru', '1987-12-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000012', '081300110012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(370, 13, 11, 'Dummy BPK 11 - Anggota 13', 'Banjarbaru', '1999-10-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000013', '081300110013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(371, 14, 11, 'Dummy BPK 11 - Anggota 14', 'Banjarbaru', '1982-06-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000014', '081300110014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(372, 15, 11, 'Dummy BPK 11 - Anggota 15', 'Banjarbaru', '1985-09-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000015', '081300110015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(373, 16, 11, 'Dummy BPK 11 - Anggota 16', 'Banjarbaru', '1984-10-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001100000016', '081300110016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(374, 17, 11, 'Dummy BPK 11 - Anggota 17', 'Banjarbaru', '1991-04-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000017', '081300110017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(375, 18, 11, 'Dummy BPK 11 - Anggota 18', 'Banjarbaru', '1987-01-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000018', '081300110018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(376, 19, 11, 'Dummy BPK 11 - Anggota 19', 'Banjarbaru', '1985-08-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000019', '081300110019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(377, 20, 11, 'Dummy BPK 11 - Anggota 20', 'Banjarbaru', '1995-02-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000020', '081300110020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(378, 21, 11, 'Dummy BPK 11 - Anggota 21', 'Banjarbaru', '1998-01-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000021', '081300110021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(379, 22, 11, 'Dummy BPK 11 - Anggota 22', 'Banjarbaru', '1994-08-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000022', '081300110022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(380, 23, 11, 'Dummy BPK 11 - Anggota 23', 'Banjarbaru', '1986-02-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000023', '081300110023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(381, 24, 11, 'Dummy BPK 11 - Anggota 24', 'Banjarbaru', '1991-11-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000024', '081300110024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(382, 25, 11, 'Dummy BPK 11 - Anggota 25', 'Banjarbaru', '1985-12-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000025', '081300110025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(383, 26, 11, 'Dummy BPK 11 - Anggota 26', 'Banjarbaru', '1986-10-07', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001100000026', '081300110026', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(384, 27, 11, 'Dummy BPK 11 - Anggota 27', 'Banjarbaru', '2000-01-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000027', '081300110027', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(385, 28, 11, 'Dummy BPK 11 - Anggota 28', 'Banjarbaru', '1999-05-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001100000028', '081300110028', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(386, 1, 12, 'Dummy BPK 12 - Anggota 1', 'Banjarbaru', '1981-01-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000001', '081300120001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(387, 2, 12, 'Dummy BPK 12 - Anggota 2', 'Banjarbaru', '1984-07-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000002', '081300120002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(388, 3, 12, 'Dummy BPK 12 - Anggota 3', 'Banjarbaru', '2002-05-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000003', '081300120003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(389, 4, 12, 'Dummy BPK 12 - Anggota 4', 'Banjarbaru', '1980-08-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000004', '081300120004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(390, 5, 12, 'Dummy BPK 12 - Anggota 5', 'Banjarbaru', '1981-10-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000005', '081300120005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(391, 6, 12, 'Dummy BPK 12 - Anggota 6', 'Banjarbaru', '1981-01-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000006', '081300120006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(392, 7, 12, 'Dummy BPK 12 - Anggota 7', 'Banjarbaru', '1984-03-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001200000007', '081300120007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(393, 8, 12, 'Dummy BPK 12 - Anggota 8', 'Banjarbaru', '1995-05-13', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001200000008', '081300120008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(394, 9, 12, 'Dummy BPK 12 - Anggota 9', 'Banjarbaru', '1987-10-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000009', '081300120009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(395, 10, 12, 'Dummy BPK 12 - Anggota 10', 'Banjarbaru', '1989-05-16', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001200000010', '081300120010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(396, 11, 12, 'Dummy BPK 12 - Anggota 11', 'Banjarbaru', '2006-01-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001200000011', '081300120011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(397, 12, 12, 'Dummy BPK 12 - Anggota 12', 'Banjarbaru', '2006-08-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000012', '081300120012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(398, 13, 12, 'Dummy BPK 12 - Anggota 13', 'Banjarbaru', '2005-10-19', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000013', '081300120013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(399, 14, 12, 'Dummy BPK 12 - Anggota 14', 'Banjarbaru', '1983-12-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000014', '081300120014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(400, 15, 12, 'Dummy BPK 12 - Anggota 15', 'Banjarbaru', '1987-01-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000015', '081300120015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(401, 16, 12, 'Dummy BPK 12 - Anggota 16', 'Banjarbaru', '1996-01-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000016', '081300120016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(402, 17, 12, 'Dummy BPK 12 - Anggota 17', 'Banjarbaru', '2002-04-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000017', '081300120017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(403, 18, 12, 'Dummy BPK 12 - Anggota 18', 'Banjarbaru', '1984-10-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000018', '081300120018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(404, 19, 12, 'Dummy BPK 12 - Anggota 19', 'Banjarbaru', '1980-09-05', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001200000019', '081300120019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(405, 20, 12, 'Dummy BPK 12 - Anggota 20', 'Banjarbaru', '2004-05-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000020', '081300120020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(406, 21, 12, 'Dummy BPK 12 - Anggota 21', 'Banjarbaru', '1998-02-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000021', '081300120021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(407, 22, 12, 'Dummy BPK 12 - Anggota 22', 'Banjarbaru', '1995-11-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000022', '081300120022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(408, 23, 12, 'Dummy BPK 12 - Anggota 23', 'Banjarbaru', '1981-07-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001200000023', '081300120023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(409, 24, 12, 'Dummy BPK 12 - Anggota 24', 'Banjarbaru', '2003-02-04', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001200000024', '081300120024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(410, 25, 12, 'Dummy BPK 12 - Anggota 25', 'Banjarbaru', '1994-05-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000025', '081300120025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(411, 26, 12, 'Dummy BPK 12 - Anggota 26', 'Banjarbaru', '1990-12-16', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001200000026', '081300120026', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(412, 27, 12, 'Dummy BPK 12 - Anggota 27', 'Banjarbaru', '2003-11-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000027', '081300120027', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(413, 28, 12, 'Dummy BPK 12 - Anggota 28', 'Banjarbaru', '2006-02-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001200000028', '081300120028', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(414, 1, 13, 'Dummy BPK 13 - Anggota 1', 'Banjarbaru', '2003-08-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000001', '081300130001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(415, 2, 13, 'Dummy BPK 13 - Anggota 2', 'Banjarbaru', '2005-09-20', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001300000002', '081300130002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(416, 3, 13, 'Dummy BPK 13 - Anggota 3', 'Banjarbaru', '1982-08-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000003', '081300130003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(417, 4, 13, 'Dummy BPK 13 - Anggota 4', 'Banjarbaru', '1990-09-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001300000004', '081300130004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(418, 5, 13, 'Dummy BPK 13 - Anggota 5', 'Banjarbaru', '1995-01-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000005', '081300130005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(419, 6, 13, 'Dummy BPK 13 - Anggota 6', 'Banjarbaru', '2005-06-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000006', '081300130006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(420, 7, 13, 'Dummy BPK 13 - Anggota 7', 'Banjarbaru', '2000-02-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000007', '081300130007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(421, 8, 13, 'Dummy BPK 13 - Anggota 8', 'Banjarbaru', '1996-03-03', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001300000008', '081300130008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(422, 9, 13, 'Dummy BPK 13 - Anggota 9', 'Banjarbaru', '1996-08-07', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001300000009', '081300130009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(423, 10, 13, 'Dummy BPK 13 - Anggota 10', 'Banjarbaru', '1985-08-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000010', '081300130010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(424, 11, 13, 'Dummy BPK 13 - Anggota 11', 'Banjarbaru', '1992-04-08', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001300000011', '081300130011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(425, 12, 13, 'Dummy BPK 13 - Anggota 12', 'Banjarbaru', '1998-03-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000012', '081300130012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(426, 13, 13, 'Dummy BPK 13 - Anggota 13', 'Banjarbaru', '1996-08-21', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001300000013', '081300130013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(427, 14, 13, 'Dummy BPK 13 - Anggota 14', 'Banjarbaru', '1995-04-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000014', '081300130014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(428, 15, 13, 'Dummy BPK 13 - Anggota 15', 'Banjarbaru', '1984-08-03', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001300000015', '081300130015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(429, 16, 13, 'Dummy BPK 13 - Anggota 16', 'Banjarbaru', '1984-01-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000016', '081300130016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(430, 17, 13, 'Dummy BPK 13 - Anggota 17', 'Banjarbaru', '1987-07-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000017', '081300130017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(431, 18, 13, 'Dummy BPK 13 - Anggota 18', 'Banjarbaru', '2005-03-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000018', '081300130018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(432, 19, 13, 'Dummy BPK 13 - Anggota 19', 'Banjarbaru', '1980-08-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001300000019', '081300130019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(433, 20, 13, 'Dummy BPK 13 - Anggota 20', 'Banjarbaru', '1983-02-23', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001300000020', '081300130020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(434, 21, 13, 'Dummy BPK 13 - Anggota 21', 'Banjarbaru', '2007-02-22', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001300000021', '081300130021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(435, 1, 14, 'Dummy BPK 14 - Anggota 1', 'Banjarbaru', '1994-12-19', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000001', '081300140001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(436, 2, 14, 'Dummy BPK 14 - Anggota 2', 'Banjarbaru', '2007-01-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000002', '081300140002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(437, 3, 14, 'Dummy BPK 14 - Anggota 3', 'Banjarbaru', '2003-08-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000003', '081300140003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(438, 4, 14, 'Dummy BPK 14 - Anggota 4', 'Banjarbaru', '1992-11-18', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000004', '081300140004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(439, 5, 14, 'Dummy BPK 14 - Anggota 5', 'Banjarbaru', '1985-02-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000005', '081300140005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(440, 6, 14, 'Dummy BPK 14 - Anggota 6', 'Banjarbaru', '1989-04-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000006', '081300140006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(441, 7, 14, 'Dummy BPK 14 - Anggota 7', 'Banjarbaru', '2000-07-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000007', '081300140007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(442, 8, 14, 'Dummy BPK 14 - Anggota 8', 'Banjarbaru', '1985-09-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000008', '081300140008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(443, 9, 14, 'Dummy BPK 14 - Anggota 9', 'Banjarbaru', '1986-03-28', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000009', '081300140009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(444, 10, 14, 'Dummy BPK 14 - Anggota 10', 'Banjarbaru', '1992-02-07', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000010', '081300140010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(445, 11, 14, 'Dummy BPK 14 - Anggota 11', 'Banjarbaru', '1989-07-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000011', '081300140011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(446, 12, 14, 'Dummy BPK 14 - Anggota 12', 'Banjarbaru', '1997-12-05', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000012', '081300140012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(447, 13, 14, 'Dummy BPK 14 - Anggota 13', 'Banjarbaru', '1988-03-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000013', '081300140013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(448, 14, 14, 'Dummy BPK 14 - Anggota 14', 'Banjarbaru', '2002-03-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000014', '081300140014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(449, 15, 14, 'Dummy BPK 14 - Anggota 15', 'Banjarbaru', '1998-11-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000015', '081300140015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(450, 16, 14, 'Dummy BPK 14 - Anggota 16', 'Banjarbaru', '2002-09-10', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000016', '081300140016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(451, 17, 14, 'Dummy BPK 14 - Anggota 17', 'Banjarbaru', '1994-08-07', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000017', '081300140017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(452, 18, 14, 'Dummy BPK 14 - Anggota 18', 'Banjarbaru', '1998-05-31', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000018', '081300140018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(453, 19, 14, 'Dummy BPK 14 - Anggota 19', 'Banjarbaru', '2006-10-26', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000019', '081300140019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(454, 20, 14, 'Dummy BPK 14 - Anggota 20', 'Banjarbaru', '1995-04-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000020', '081300140020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(455, 21, 14, 'Dummy BPK 14 - Anggota 21', 'Banjarbaru', '2002-01-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000021', '081300140021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(456, 22, 14, 'Dummy BPK 14 - Anggota 22', 'Banjarbaru', '1986-12-22', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000022', '081300140022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(457, 23, 14, 'Dummy BPK 14 - Anggota 23', 'Banjarbaru', '1996-05-27', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001400000023', '081300140023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(458, 24, 14, 'Dummy BPK 14 - Anggota 24', 'Banjarbaru', '1982-07-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000024', '081300140024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(459, 25, 14, 'Dummy BPK 14 - Anggota 25', 'Banjarbaru', '1989-06-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000025', '081300140025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(460, 26, 14, 'Dummy BPK 14 - Anggota 26', 'Banjarbaru', '1999-09-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000026', '081300140026', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(461, 27, 14, 'Dummy BPK 14 - Anggota 27', 'Banjarbaru', '1995-04-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000027', '081300140027', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(462, 28, 14, 'Dummy BPK 14 - Anggota 28', 'Banjarbaru', '1983-02-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001400000028', '081300140028', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(463, 1, 15, 'Dummy BPK 15 - Anggota 1', 'Banjarbaru', '1980-01-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000001', '081300150001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(464, 2, 15, 'Dummy BPK 15 - Anggota 2', 'Banjarbaru', '2006-12-15', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001500000002', '081300150002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(465, 3, 15, 'Dummy BPK 15 - Anggota 3', 'Banjarbaru', '1998-10-13', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001500000003', '081300150003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(466, 4, 15, 'Dummy BPK 15 - Anggota 4', 'Banjarbaru', '1984-10-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000004', '081300150004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(467, 5, 15, 'Dummy BPK 15 - Anggota 5', 'Banjarbaru', '1983-09-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000005', '081300150005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(468, 6, 15, 'Dummy BPK 15 - Anggota 6', 'Banjarbaru', '2006-11-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000006', '081300150006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(469, 7, 15, 'Dummy BPK 15 - Anggota 7', 'Banjarbaru', '1992-06-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000007', '081300150007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(470, 8, 15, 'Dummy BPK 15 - Anggota 8', 'Banjarbaru', '2001-10-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000008', '081300150008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(471, 9, 15, 'Dummy BPK 15 - Anggota 9', 'Banjarbaru', '1980-10-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000009', '081300150009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(472, 10, 15, 'Dummy BPK 15 - Anggota 10', 'Banjarbaru', '1991-02-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000010', '081300150010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(473, 11, 15, 'Dummy BPK 15 - Anggota 11', 'Banjarbaru', '1992-04-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000011', '081300150011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(474, 12, 15, 'Dummy BPK 15 - Anggota 12', 'Banjarbaru', '2004-04-04', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001500000012', '081300150012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(475, 13, 15, 'Dummy BPK 15 - Anggota 13', 'Banjarbaru', '2001-08-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000013', '081300150013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(476, 14, 15, 'Dummy BPK 15 - Anggota 14', 'Banjarbaru', '1980-07-22', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001500000014', '081300150014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(477, 15, 15, 'Dummy BPK 15 - Anggota 15', 'Banjarbaru', '1987-10-15', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001500000015', '081300150015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(478, 16, 15, 'Dummy BPK 15 - Anggota 16', 'Banjarbaru', '1986-01-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000016', '081300150016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(479, 17, 15, 'Dummy BPK 15 - Anggota 17', 'Banjarbaru', '1997-06-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000017', '081300150017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(480, 18, 15, 'Dummy BPK 15 - Anggota 18', 'Banjarbaru', '1983-05-23', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001500000018', '081300150018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(481, 19, 15, 'Dummy BPK 15 - Anggota 19', 'Banjarbaru', '1996-11-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001500000019', '081300150019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(482, 1, 16, 'Dummy BPK 16 - Anggota 1', 'Banjarbaru', '1991-01-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000001', '081300160001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(483, 2, 16, 'Dummy BPK 16 - Anggota 2', 'Banjarbaru', '1987-11-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000002', '081300160002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(484, 3, 16, 'Dummy BPK 16 - Anggota 3', 'Banjarbaru', '2003-05-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000003', '081300160003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(485, 4, 16, 'Dummy BPK 16 - Anggota 4', 'Banjarbaru', '1996-07-08', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000004', '081300160004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(486, 5, 16, 'Dummy BPK 16 - Anggota 5', 'Banjarbaru', '2001-09-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000005', '081300160005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(487, 6, 16, 'Dummy BPK 16 - Anggota 6', 'Banjarbaru', '1997-09-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000006', '081300160006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(488, 7, 16, 'Dummy BPK 16 - Anggota 7', 'Banjarbaru', '1984-04-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000007', '081300160007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(489, 8, 16, 'Dummy BPK 16 - Anggota 8', 'Banjarbaru', '1983-07-14', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000008', '081300160008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(490, 9, 16, 'Dummy BPK 16 - Anggota 9', 'Banjarbaru', '1981-06-14', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000009', '081300160009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(491, 10, 16, 'Dummy BPK 16 - Anggota 10', 'Banjarbaru', '2003-03-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000010', '081300160010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(492, 11, 16, 'Dummy BPK 16 - Anggota 11', 'Banjarbaru', '1996-11-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000011', '081300160011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(493, 12, 16, 'Dummy BPK 16 - Anggota 12', 'Banjarbaru', '1982-11-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000012', '081300160012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(494, 13, 16, 'Dummy BPK 16 - Anggota 13', 'Banjarbaru', '1991-10-27', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000013', '081300160013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(495, 14, 16, 'Dummy BPK 16 - Anggota 14', 'Banjarbaru', '1987-03-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000014', '081300160014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(496, 15, 16, 'Dummy BPK 16 - Anggota 15', 'Banjarbaru', '1982-11-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000015', '081300160015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(497, 16, 16, 'Dummy BPK 16 - Anggota 16', 'Banjarbaru', '1996-09-17', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000016', '081300160016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(498, 17, 16, 'Dummy BPK 16 - Anggota 17', 'Banjarbaru', '1998-02-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000017', '081300160017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(499, 18, 16, 'Dummy BPK 16 - Anggota 18', 'Banjarbaru', '1983-01-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000018', '081300160018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(500, 19, 16, 'Dummy BPK 16 - Anggota 19', 'Banjarbaru', '1983-01-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000019', '081300160019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(501, 20, 16, 'Dummy BPK 16 - Anggota 20', 'Banjarbaru', '1984-02-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000020', '081300160020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(502, 21, 16, 'Dummy BPK 16 - Anggota 21', 'Banjarbaru', '2003-07-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000021', '081300160021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(503, 22, 16, 'Dummy BPK 16 - Anggota 22', 'Banjarbaru', '2007-04-26', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000022', '081300160022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(504, 23, 16, 'Dummy BPK 16 - Anggota 23', 'Banjarbaru', '1985-09-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000023', '081300160023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(505, 24, 16, 'Dummy BPK 16 - Anggota 24', 'Banjarbaru', '2006-12-22', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001600000024', '081300160024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(506, 25, 16, 'Dummy BPK 16 - Anggota 25', 'Banjarbaru', '2003-01-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001600000025', '081300160025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(507, 1, 17, 'Dummy BPK 17 - Anggota 1', 'Banjarbaru', '1990-02-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000001', '081300170001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(508, 2, 17, 'Dummy BPK 17 - Anggota 2', 'Banjarbaru', '1992-10-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000002', '081300170002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(509, 3, 17, 'Dummy BPK 17 - Anggota 3', 'Banjarbaru', '1995-08-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000003', '081300170003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(510, 4, 17, 'Dummy BPK 17 - Anggota 4', 'Banjarbaru', '1997-01-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000004', '081300170004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(511, 5, 17, 'Dummy BPK 17 - Anggota 5', 'Banjarbaru', '1999-07-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000005', '081300170005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(512, 6, 17, 'Dummy BPK 17 - Anggota 6', 'Banjarbaru', '2006-08-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000006', '081300170006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(513, 7, 17, 'Dummy BPK 17 - Anggota 7', 'Banjarbaru', '1980-09-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000007', '081300170007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(514, 8, 17, 'Dummy BPK 17 - Anggota 8', 'Banjarbaru', '2000-06-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000008', '081300170008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(515, 9, 17, 'Dummy BPK 17 - Anggota 9', 'Banjarbaru', '2006-08-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000009', '081300170009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(516, 10, 17, 'Dummy BPK 17 - Anggota 10', 'Banjarbaru', '2000-06-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000010', '081300170010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(517, 11, 17, 'Dummy BPK 17 - Anggota 11', 'Banjarbaru', '1991-06-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000011', '081300170011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(518, 12, 17, 'Dummy BPK 17 - Anggota 12', 'Banjarbaru', '1986-10-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000012', '081300170012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(519, 13, 17, 'Dummy BPK 17 - Anggota 13', 'Banjarbaru', '2006-07-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000013', '081300170013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(520, 14, 17, 'Dummy BPK 17 - Anggota 14', 'Banjarbaru', '1984-10-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000014', '081300170014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(521, 15, 17, 'Dummy BPK 17 - Anggota 15', 'Banjarbaru', '1995-10-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000015', '081300170015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(522, 16, 17, 'Dummy BPK 17 - Anggota 16', 'Banjarbaru', '1990-01-19', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000016', '081300170016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(523, 17, 17, 'Dummy BPK 17 - Anggota 17', 'Banjarbaru', '2001-11-03', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001700000017', '081300170017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(524, 18, 17, 'Dummy BPK 17 - Anggota 18', 'Banjarbaru', '2003-05-03', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001700000018', '081300170018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(525, 19, 17, 'Dummy BPK 17 - Anggota 19', 'Banjarbaru', '1980-05-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001700000019', '081300170019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(526, 1, 18, 'Dummy BPK 18 - Anggota 1', 'Banjarbaru', '2004-03-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000001', '081300180001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(527, 2, 18, 'Dummy BPK 18 - Anggota 2', 'Banjarbaru', '1986-03-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000002', '081300180002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(528, 3, 18, 'Dummy BPK 18 - Anggota 3', 'Banjarbaru', '1988-09-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000003', '081300180003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(529, 4, 18, 'Dummy BPK 18 - Anggota 4', 'Banjarbaru', '2007-01-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000004', '081300180004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(530, 5, 18, 'Dummy BPK 18 - Anggota 5', 'Banjarbaru', '2005-05-03', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000005', '081300180005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(531, 6, 18, 'Dummy BPK 18 - Anggota 6', 'Banjarbaru', '1998-11-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000006', '081300180006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(532, 7, 18, 'Dummy BPK 18 - Anggota 7', 'Banjarbaru', '2000-12-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000007', '081300180007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(533, 8, 18, 'Dummy BPK 18 - Anggota 8', 'Banjarbaru', '1988-08-16', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000008', '081300180008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(534, 9, 18, 'Dummy BPK 18 - Anggota 9', 'Banjarbaru', '1982-12-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000009', '081300180009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(535, 10, 18, 'Dummy BPK 18 - Anggota 10', 'Banjarbaru', '1986-10-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000010', '081300180010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(536, 11, 18, 'Dummy BPK 18 - Anggota 11', 'Banjarbaru', '1999-03-07', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000011', '081300180011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(537, 12, 18, 'Dummy BPK 18 - Anggota 12', 'Banjarbaru', '1982-06-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000012', '081300180012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(538, 13, 18, 'Dummy BPK 18 - Anggota 13', 'Banjarbaru', '1991-06-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000013', '081300180013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(539, 14, 18, 'Dummy BPK 18 - Anggota 14', 'Banjarbaru', '1983-02-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000014', '081300180014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(540, 15, 18, 'Dummy BPK 18 - Anggota 15', 'Banjarbaru', '1991-12-14', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000015', '081300180015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(541, 16, 18, 'Dummy BPK 18 - Anggota 16', 'Banjarbaru', '1986-12-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000016', '081300180016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(542, 17, 18, 'Dummy BPK 18 - Anggota 17', 'Banjarbaru', '2005-05-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000017', '081300180017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(543, 18, 18, 'Dummy BPK 18 - Anggota 18', 'Banjarbaru', '1988-04-11', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000018', '081300180018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(544, 19, 18, 'Dummy BPK 18 - Anggota 19', 'Banjarbaru', '1991-02-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000019', '081300180019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(545, 20, 18, 'Dummy BPK 18 - Anggota 20', 'Banjarbaru', '2007-03-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000020', '081300180020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(546, 21, 18, 'Dummy BPK 18 - Anggota 21', 'Banjarbaru', '1986-02-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000021', '081300180021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(547, 22, 18, 'Dummy BPK 18 - Anggota 22', 'Banjarbaru', '1987-10-25', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000022', '081300180022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(548, 23, 18, 'Dummy BPK 18 - Anggota 23', 'Banjarbaru', '1991-07-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000023', '081300180023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(549, 24, 18, 'Dummy BPK 18 - Anggota 24', 'Banjarbaru', '1991-07-16', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000024', '081300180024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(550, 25, 18, 'Dummy BPK 18 - Anggota 25', 'Banjarbaru', '1986-02-04', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001800000025', '081300180025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(551, 26, 18, 'Dummy BPK 18 - Anggota 26', 'Banjarbaru', '1991-12-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001800000026', '081300180026', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(552, 1, 19, 'Dummy BPK 19 - Anggota 1', 'Banjarbaru', '2004-10-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000001', '081300190001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(553, 2, 19, 'Dummy BPK 19 - Anggota 2', 'Banjarbaru', '1994-07-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000002', '081300190002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(554, 3, 19, 'Dummy BPK 19 - Anggota 3', 'Banjarbaru', '2005-02-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000003', '081300190003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(555, 4, 19, 'Dummy BPK 19 - Anggota 4', 'Banjarbaru', '1998-08-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000004', '081300190004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(556, 5, 19, 'Dummy BPK 19 - Anggota 5', 'Banjarbaru', '1997-10-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000005', '081300190005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(557, 6, 19, 'Dummy BPK 19 - Anggota 6', 'Banjarbaru', '1986-07-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000006', '081300190006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(558, 7, 19, 'Dummy BPK 19 - Anggota 7', 'Banjarbaru', '1998-06-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000007', '081300190007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(559, 8, 19, 'Dummy BPK 19 - Anggota 8', 'Banjarbaru', '1997-10-28', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001900000008', '081300190008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(560, 9, 19, 'Dummy BPK 19 - Anggota 9', 'Banjarbaru', '1988-06-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000009', '081300190009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(561, 10, 19, 'Dummy BPK 19 - Anggota 10', 'Banjarbaru', '1980-01-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000010', '081300190010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(562, 11, 19, 'Dummy BPK 19 - Anggota 11', 'Banjarbaru', '1986-06-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000011', '081300190011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(563, 12, 19, 'Dummy BPK 19 - Anggota 12', 'Banjarbaru', '1993-08-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000012', '081300190012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(564, 13, 19, 'Dummy BPK 19 - Anggota 13', 'Banjarbaru', '1988-03-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000013', '081300190013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(565, 14, 19, 'Dummy BPK 19 - Anggota 14', 'Banjarbaru', '1986-01-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000014', '081300190014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(566, 15, 19, 'Dummy BPK 19 - Anggota 15', 'Banjarbaru', '1993-06-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000015', '081300190015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(567, 16, 19, 'Dummy BPK 19 - Anggota 16', 'Banjarbaru', '1989-08-11', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001900000016', '081300190016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(568, 17, 19, 'Dummy BPK 19 - Anggota 17', 'Banjarbaru', '1986-10-18', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001900000017', '081300190017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(569, 18, 19, 'Dummy BPK 19 - Anggota 18', 'Banjarbaru', '1995-03-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000018', '081300190018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(570, 19, 19, 'Dummy BPK 19 - Anggota 19', 'Banjarbaru', '1989-07-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372001900000019', '081300190019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(571, 20, 19, 'Dummy BPK 19 - Anggota 20', 'Banjarbaru', '1995-11-19', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001900000020', '081300190020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(572, 21, 19, 'Dummy BPK 19 - Anggota 21', 'Banjarbaru', '2004-08-26', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372001900000021', '081300190021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(573, 1, 20, 'Dummy BPK 20 - Anggota 1', 'Banjarbaru', '1982-04-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000001', '081300200001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(574, 2, 20, 'Dummy BPK 20 - Anggota 2', 'Banjarbaru', '1994-12-19', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000002', '081300200002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(575, 3, 20, 'Dummy BPK 20 - Anggota 3', 'Banjarbaru', '1997-02-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000003', '081300200003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(576, 4, 20, 'Dummy BPK 20 - Anggota 4', 'Banjarbaru', '2006-01-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000004', '081300200004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(577, 5, 20, 'Dummy BPK 20 - Anggota 5', 'Banjarbaru', '1993-02-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000005', '081300200005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(578, 6, 20, 'Dummy BPK 20 - Anggota 6', 'Banjarbaru', '1994-04-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000006', '081300200006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(579, 7, 20, 'Dummy BPK 20 - Anggota 7', 'Banjarbaru', '1996-02-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002000000007', '081300200007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(580, 8, 20, 'Dummy BPK 20 - Anggota 8', 'Banjarbaru', '1993-04-18', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002000000008', '081300200008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(581, 9, 20, 'Dummy BPK 20 - Anggota 9', 'Banjarbaru', '2005-08-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000009', '081300200009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(582, 10, 20, 'Dummy BPK 20 - Anggota 10', 'Banjarbaru', '1990-12-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000010', '081300200010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(583, 11, 20, 'Dummy BPK 20 - Anggota 11', 'Banjarbaru', '1985-07-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000011', '081300200011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(584, 12, 20, 'Dummy BPK 20 - Anggota 12', 'Banjarbaru', '1986-04-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000012', '081300200012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(585, 13, 20, 'Dummy BPK 20 - Anggota 13', 'Banjarbaru', '1994-11-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000013', '081300200013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(586, 14, 20, 'Dummy BPK 20 - Anggota 14', 'Banjarbaru', '1986-11-20', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002000000014', '081300200014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(587, 15, 20, 'Dummy BPK 20 - Anggota 15', 'Banjarbaru', '2004-06-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002000000015', '081300200015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48');
INSERT INTO `anggota` (`id`, `nomor_anggota`, `bpk_id`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `nik`, `no_hp`, `status`, `jabatan`, `foto`, `foto_ktp`, `created_at`, `updated_at`) VALUES
(588, 16, 20, 'Dummy BPK 20 - Anggota 16', 'Banjarbaru', '1998-11-25', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002000000016', '081300200016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(589, 17, 20, 'Dummy BPK 20 - Anggota 17', 'Banjarbaru', '1982-04-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000017', '081300200017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(590, 18, 20, 'Dummy BPK 20 - Anggota 18', 'Banjarbaru', '1990-12-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000018', '081300200018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(591, 19, 20, 'Dummy BPK 20 - Anggota 19', 'Banjarbaru', '2003-08-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000019', '081300200019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(592, 20, 20, 'Dummy BPK 20 - Anggota 20', 'Banjarbaru', '2001-06-13', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002000000020', '081300200020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(593, 21, 20, 'Dummy BPK 20 - Anggota 21', 'Banjarbaru', '1983-04-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000021', '081300200021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(594, 22, 20, 'Dummy BPK 20 - Anggota 22', 'Banjarbaru', '1986-01-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000022', '081300200022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(595, 23, 20, 'Dummy BPK 20 - Anggota 23', 'Banjarbaru', '1983-07-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000023', '081300200023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(596, 24, 20, 'Dummy BPK 20 - Anggota 24', 'Banjarbaru', '1992-01-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000024', '081300200024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(597, 25, 20, 'Dummy BPK 20 - Anggota 25', 'Banjarbaru', '1986-02-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000025', '081300200025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(598, 26, 20, 'Dummy BPK 20 - Anggota 26', 'Banjarbaru', '1987-04-09', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002000000026', '081300200026', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(599, 27, 20, 'Dummy BPK 20 - Anggota 27', 'Banjarbaru', '1981-02-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000027', '081300200027', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(600, 28, 20, 'Dummy BPK 20 - Anggota 28', 'Banjarbaru', '1990-09-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000028', '081300200028', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(601, 29, 20, 'Dummy BPK 20 - Anggota 29', 'Banjarbaru', '1981-05-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002000000029', '081300200029', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(602, 1, 21, 'Dummy BPK 21 - Anggota 1', 'Banjarbaru', '1981-06-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000001', '081300210001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(603, 2, 21, 'Dummy BPK 21 - Anggota 2', 'Banjarbaru', '1995-05-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000002', '081300210002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(604, 3, 21, 'Dummy BPK 21 - Anggota 3', 'Banjarbaru', '1984-05-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000003', '081300210003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(605, 4, 21, 'Dummy BPK 21 - Anggota 4', 'Banjarbaru', '2006-05-22', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000004', '081300210004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(606, 5, 21, 'Dummy BPK 21 - Anggota 5', 'Banjarbaru', '2004-05-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000005', '081300210005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(607, 6, 21, 'Dummy BPK 21 - Anggota 6', 'Banjarbaru', '1986-04-11', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000006', '081300210006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(608, 7, 21, 'Dummy BPK 21 - Anggota 7', 'Banjarbaru', '1989-07-17', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000007', '081300210007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(609, 8, 21, 'Dummy BPK 21 - Anggota 8', 'Banjarbaru', '1995-04-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000008', '081300210008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(610, 9, 21, 'Dummy BPK 21 - Anggota 9', 'Banjarbaru', '1993-06-19', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000009', '081300210009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(611, 10, 21, 'Dummy BPK 21 - Anggota 10', 'Banjarbaru', '1988-07-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000010', '081300210010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(612, 11, 21, 'Dummy BPK 21 - Anggota 11', 'Banjarbaru', '1993-03-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000011', '081300210011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(613, 12, 21, 'Dummy BPK 21 - Anggota 12', 'Banjarbaru', '1989-06-30', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000012', '081300210012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(614, 13, 21, 'Dummy BPK 21 - Anggota 13', 'Banjarbaru', '1987-01-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000013', '081300210013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(615, 14, 21, 'Dummy BPK 21 - Anggota 14', 'Banjarbaru', '2001-05-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000014', '081300210014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(616, 15, 21, 'Dummy BPK 21 - Anggota 15', 'Banjarbaru', '1994-04-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000015', '081300210015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(617, 16, 21, 'Dummy BPK 21 - Anggota 16', 'Banjarbaru', '2004-10-20', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000016', '081300210016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(618, 17, 21, 'Dummy BPK 21 - Anggota 17', 'Banjarbaru', '1993-10-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000017', '081300210017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(619, 18, 21, 'Dummy BPK 21 - Anggota 18', 'Banjarbaru', '1992-03-14', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000018', '081300210018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(620, 19, 21, 'Dummy BPK 21 - Anggota 19', 'Banjarbaru', '2005-03-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000019', '081300210019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(621, 20, 21, 'Dummy BPK 21 - Anggota 20', 'Banjarbaru', '1992-11-04', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000020', '081300210020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(622, 21, 21, 'Dummy BPK 21 - Anggota 21', 'Banjarbaru', '1997-05-08', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002100000021', '081300210021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(623, 22, 21, 'Dummy BPK 21 - Anggota 22', 'Banjarbaru', '2002-06-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000022', '081300210022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(624, 23, 21, 'Dummy BPK 21 - Anggota 23', 'Banjarbaru', '2003-08-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002100000023', '081300210023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(625, 1, 22, 'Dummy BPK 22 - Anggota 1', 'Banjarbaru', '1982-08-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000001', '081300220001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(626, 2, 22, 'Dummy BPK 22 - Anggota 2', 'Banjarbaru', '2006-05-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000002', '081300220002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(627, 3, 22, 'Dummy BPK 22 - Anggota 3', 'Banjarbaru', '1992-07-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000003', '081300220003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(628, 4, 22, 'Dummy BPK 22 - Anggota 4', 'Banjarbaru', '1980-12-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000004', '081300220004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(629, 5, 22, 'Dummy BPK 22 - Anggota 5', 'Banjarbaru', '1994-03-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002200000005', '081300220005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(630, 6, 22, 'Dummy BPK 22 - Anggota 6', 'Banjarbaru', '1994-07-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000006', '081300220006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(631, 7, 22, 'Dummy BPK 22 - Anggota 7', 'Banjarbaru', '1992-05-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000007', '081300220007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(632, 8, 22, 'Dummy BPK 22 - Anggota 8', 'Banjarbaru', '2002-04-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000008', '081300220008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(633, 9, 22, 'Dummy BPK 22 - Anggota 9', 'Banjarbaru', '1990-07-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000009', '081300220009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(634, 10, 22, 'Dummy BPK 22 - Anggota 10', 'Banjarbaru', '2006-05-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000010', '081300220010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(635, 11, 22, 'Dummy BPK 22 - Anggota 11', 'Banjarbaru', '2003-09-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000011', '081300220011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(636, 12, 22, 'Dummy BPK 22 - Anggota 12', 'Banjarbaru', '2001-06-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000012', '081300220012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(637, 13, 22, 'Dummy BPK 22 - Anggota 13', 'Banjarbaru', '1983-02-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002200000013', '081300220013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(638, 14, 22, 'Dummy BPK 22 - Anggota 14', 'Banjarbaru', '1982-03-02', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002200000014', '081300220014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(639, 15, 22, 'Dummy BPK 22 - Anggota 15', 'Banjarbaru', '1993-05-21', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002200000015', '081300220015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(640, 16, 22, 'Dummy BPK 22 - Anggota 16', 'Banjarbaru', '1995-11-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000016', '081300220016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(641, 17, 22, 'Dummy BPK 22 - Anggota 17', 'Banjarbaru', '1996-04-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000017', '081300220017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(642, 18, 22, 'Dummy BPK 22 - Anggota 18', 'Banjarbaru', '1982-06-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002200000018', '081300220018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(643, 1, 23, 'Dummy BPK 23 - Anggota 1', 'Banjarbaru', '2000-03-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000001', '081300230001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(644, 2, 23, 'Dummy BPK 23 - Anggota 2', 'Banjarbaru', '1983-10-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000002', '081300230002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(645, 3, 23, 'Dummy BPK 23 - Anggota 3', 'Banjarbaru', '1980-02-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000003', '081300230003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(646, 4, 23, 'Dummy BPK 23 - Anggota 4', 'Banjarbaru', '2003-04-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000004', '081300230004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(647, 5, 23, 'Dummy BPK 23 - Anggota 5', 'Banjarbaru', '1982-10-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000005', '081300230005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(648, 6, 23, 'Dummy BPK 23 - Anggota 6', 'Banjarbaru', '1987-10-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000006', '081300230006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(649, 7, 23, 'Dummy BPK 23 - Anggota 7', 'Banjarbaru', '1994-02-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000007', '081300230007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(650, 8, 23, 'Dummy BPK 23 - Anggota 8', 'Banjarbaru', '1985-11-02', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002300000008', '081300230008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(651, 9, 23, 'Dummy BPK 23 - Anggota 9', 'Banjarbaru', '1991-05-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000009', '081300230009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(652, 10, 23, 'Dummy BPK 23 - Anggota 10', 'Banjarbaru', '2007-03-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000010', '081300230010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(653, 11, 23, 'Dummy BPK 23 - Anggota 11', 'Banjarbaru', '1983-04-28', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000011', '081300230011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(654, 12, 23, 'Dummy BPK 23 - Anggota 12', 'Banjarbaru', '1990-02-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000012', '081300230012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(655, 13, 23, 'Dummy BPK 23 - Anggota 13', 'Banjarbaru', '2004-10-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000013', '081300230013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(656, 14, 23, 'Dummy BPK 23 - Anggota 14', 'Banjarbaru', '2005-02-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000014', '081300230014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(657, 15, 23, 'Dummy BPK 23 - Anggota 15', 'Banjarbaru', '1987-11-10', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002300000015', '081300230015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(658, 16, 23, 'Dummy BPK 23 - Anggota 16', 'Banjarbaru', '1985-02-19', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000016', '081300230016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(659, 17, 23, 'Dummy BPK 23 - Anggota 17', 'Banjarbaru', '2006-11-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000017', '081300230017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(660, 18, 23, 'Dummy BPK 23 - Anggota 18', 'Banjarbaru', '2006-12-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000018', '081300230018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(661, 19, 23, 'Dummy BPK 23 - Anggota 19', 'Banjarbaru', '2003-01-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000019', '081300230019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(662, 20, 23, 'Dummy BPK 23 - Anggota 20', 'Banjarbaru', '1984-01-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002300000020', '081300230020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(663, 21, 23, 'Dummy BPK 23 - Anggota 21', 'Banjarbaru', '1986-11-17', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002300000021', '081300230021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(664, 22, 23, 'Dummy BPK 23 - Anggota 22', 'Banjarbaru', '1991-11-09', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002300000022', '081300230022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(665, 1, 24, 'Dummy BPK 24 - Anggota 1', 'Banjarbaru', '1998-06-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000001', '081300240001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(666, 2, 24, 'Dummy BPK 24 - Anggota 2', 'Banjarbaru', '1989-07-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000002', '081300240002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(667, 3, 24, 'Dummy BPK 24 - Anggota 3', 'Banjarbaru', '1994-03-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000003', '081300240003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(668, 4, 24, 'Dummy BPK 24 - Anggota 4', 'Banjarbaru', '2000-08-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000004', '081300240004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(669, 5, 24, 'Dummy BPK 24 - Anggota 5', 'Banjarbaru', '1996-09-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000005', '081300240005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(670, 6, 24, 'Dummy BPK 24 - Anggota 6', 'Banjarbaru', '2002-05-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000006', '081300240006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(671, 7, 24, 'Dummy BPK 24 - Anggota 7', 'Banjarbaru', '1981-02-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000007', '081300240007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(672, 8, 24, 'Dummy BPK 24 - Anggota 8', 'Banjarbaru', '1992-04-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000008', '081300240008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(673, 9, 24, 'Dummy BPK 24 - Anggota 9', 'Banjarbaru', '1983-09-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000009', '081300240009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(674, 10, 24, 'Dummy BPK 24 - Anggota 10', 'Banjarbaru', '1994-11-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000010', '081300240010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(675, 11, 24, 'Dummy BPK 24 - Anggota 11', 'Banjarbaru', '1982-08-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000011', '081300240011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(676, 12, 24, 'Dummy BPK 24 - Anggota 12', 'Banjarbaru', '2005-01-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000012', '081300240012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(677, 13, 24, 'Dummy BPK 24 - Anggota 13', 'Banjarbaru', '1993-12-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000013', '081300240013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(678, 14, 24, 'Dummy BPK 24 - Anggota 14', 'Banjarbaru', '1992-04-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000014', '081300240014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(679, 15, 24, 'Dummy BPK 24 - Anggota 15', 'Banjarbaru', '2005-06-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002400000015', '081300240015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(680, 16, 24, 'Dummy BPK 24 - Anggota 16', 'Banjarbaru', '1996-11-16', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002400000016', '081300240016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(681, 17, 24, 'Dummy BPK 24 - Anggota 17', 'Banjarbaru', '1989-08-27', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002400000017', '081300240017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(682, 18, 24, 'Dummy BPK 24 - Anggota 18', 'Banjarbaru', '1984-03-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000018', '081300240018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(683, 19, 24, 'Dummy BPK 24 - Anggota 19', 'Banjarbaru', '1999-11-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000019', '081300240019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(684, 20, 24, 'Dummy BPK 24 - Anggota 20', 'Banjarbaru', '1991-01-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000020', '081300240020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(685, 21, 24, 'Dummy BPK 24 - Anggota 21', 'Banjarbaru', '1984-03-15', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002400000021', '081300240021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(686, 22, 24, 'Dummy BPK 24 - Anggota 22', 'Banjarbaru', '1987-08-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000022', '081300240022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(687, 23, 24, 'Dummy BPK 24 - Anggota 23', 'Banjarbaru', '2005-02-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000023', '081300240023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(688, 24, 24, 'Dummy BPK 24 - Anggota 24', 'Banjarbaru', '2004-09-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000024', '081300240024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(689, 25, 24, 'Dummy BPK 24 - Anggota 25', 'Banjarbaru', '2004-06-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000025', '081300240025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(690, 26, 24, 'Dummy BPK 24 - Anggota 26', 'Banjarbaru', '2003-03-02', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000026', '081300240026', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(691, 27, 24, 'Dummy BPK 24 - Anggota 27', 'Banjarbaru', '1981-02-18', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002400000027', '081300240027', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(692, 28, 24, 'Dummy BPK 24 - Anggota 28', 'Banjarbaru', '1984-09-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000028', '081300240028', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(693, 29, 24, 'Dummy BPK 24 - Anggota 29', 'Banjarbaru', '2004-10-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000029', '081300240029', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(694, 30, 24, 'Dummy BPK 24 - Anggota 30', 'Banjarbaru', '1997-07-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002400000030', '081300240030', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(695, 1, 25, 'Dummy BPK 25 - Anggota 1', 'Banjarbaru', '1998-04-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000001', '081300250001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(696, 2, 25, 'Dummy BPK 25 - Anggota 2', 'Banjarbaru', '1997-08-10', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002500000002', '081300250002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(697, 3, 25, 'Dummy BPK 25 - Anggota 3', 'Banjarbaru', '1985-07-12', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000003', '081300250003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(698, 4, 25, 'Dummy BPK 25 - Anggota 4', 'Banjarbaru', '1981-04-26', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002500000004', '081300250004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(699, 5, 25, 'Dummy BPK 25 - Anggota 5', 'Banjarbaru', '1982-09-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000005', '081300250005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(700, 6, 25, 'Dummy BPK 25 - Anggota 6', 'Banjarbaru', '1991-08-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000006', '081300250006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(701, 7, 25, 'Dummy BPK 25 - Anggota 7', 'Banjarbaru', '1985-02-12', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002500000007', '081300250007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(702, 8, 25, 'Dummy BPK 25 - Anggota 8', 'Banjarbaru', '2000-01-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000008', '081300250008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(703, 9, 25, 'Dummy BPK 25 - Anggota 9', 'Banjarbaru', '1985-09-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000009', '081300250009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(704, 10, 25, 'Dummy BPK 25 - Anggota 10', 'Banjarbaru', '1990-03-01', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000010', '081300250010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(705, 11, 25, 'Dummy BPK 25 - Anggota 11', 'Banjarbaru', '1984-12-31', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000011', '081300250011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(706, 12, 25, 'Dummy BPK 25 - Anggota 12', 'Banjarbaru', '1983-07-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000012', '081300250012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(707, 13, 25, 'Dummy BPK 25 - Anggota 13', 'Banjarbaru', '2002-06-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000013', '081300250013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(708, 14, 25, 'Dummy BPK 25 - Anggota 14', 'Banjarbaru', '1991-01-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000014', '081300250014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(709, 15, 25, 'Dummy BPK 25 - Anggota 15', 'Banjarbaru', '1988-04-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000015', '081300250015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(710, 16, 25, 'Dummy BPK 25 - Anggota 16', 'Banjarbaru', '1983-02-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000016', '081300250016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(711, 17, 25, 'Dummy BPK 25 - Anggota 17', 'Banjarbaru', '1992-08-14', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002500000017', '081300250017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(712, 18, 25, 'Dummy BPK 25 - Anggota 18', 'Banjarbaru', '1999-10-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002500000018', '081300250018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(713, 1, 26, 'Dummy BPK 26 - Anggota 1', 'Banjarbaru', '1985-06-22', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000001', '081300260001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(714, 2, 26, 'Dummy BPK 26 - Anggota 2', 'Banjarbaru', '2004-01-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000002', '081300260002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(715, 3, 26, 'Dummy BPK 26 - Anggota 3', 'Banjarbaru', '2004-03-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000003', '081300260003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(716, 4, 26, 'Dummy BPK 26 - Anggota 4', 'Banjarbaru', '2004-05-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000004', '081300260004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(717, 5, 26, 'Dummy BPK 26 - Anggota 5', 'Banjarbaru', '2006-11-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000005', '081300260005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(718, 6, 26, 'Dummy BPK 26 - Anggota 6', 'Banjarbaru', '1997-02-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000006', '081300260006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(719, 7, 26, 'Dummy BPK 26 - Anggota 7', 'Banjarbaru', '1981-01-06', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000007', '081300260007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(720, 8, 26, 'Dummy BPK 26 - Anggota 8', 'Banjarbaru', '1982-04-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000008', '081300260008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(721, 9, 26, 'Dummy BPK 26 - Anggota 9', 'Banjarbaru', '1987-01-19', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000009', '081300260009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(722, 10, 26, 'Dummy BPK 26 - Anggota 10', 'Banjarbaru', '1983-10-19', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000010', '081300260010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(723, 11, 26, 'Dummy BPK 26 - Anggota 11', 'Banjarbaru', '1988-09-20', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000011', '081300260011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(724, 12, 26, 'Dummy BPK 26 - Anggota 12', 'Banjarbaru', '2001-12-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000012', '081300260012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(725, 13, 26, 'Dummy BPK 26 - Anggota 13', 'Banjarbaru', '1990-09-19', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000013', '081300260013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(726, 14, 26, 'Dummy BPK 26 - Anggota 14', 'Banjarbaru', '1985-09-29', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000014', '081300260014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(727, 15, 26, 'Dummy BPK 26 - Anggota 15', 'Banjarbaru', '1992-09-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000015', '081300260015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(728, 16, 26, 'Dummy BPK 26 - Anggota 16', 'Banjarbaru', '2005-09-14', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000016', '081300260016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(729, 17, 26, 'Dummy BPK 26 - Anggota 17', 'Banjarbaru', '1997-12-29', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000017', '081300260017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(730, 18, 26, 'Dummy BPK 26 - Anggota 18', 'Banjarbaru', '1981-01-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000018', '081300260018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(731, 19, 26, 'Dummy BPK 26 - Anggota 19', 'Banjarbaru', '2001-07-29', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000019', '081300260019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(732, 20, 26, 'Dummy BPK 26 - Anggota 20', 'Banjarbaru', '1997-07-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000020', '081300260020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(733, 21, 26, 'Dummy BPK 26 - Anggota 21', 'Banjarbaru', '1981-12-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000021', '081300260021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(734, 22, 26, 'Dummy BPK 26 - Anggota 22', 'Banjarbaru', '1996-01-24', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002600000022', '081300260022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(735, 23, 26, 'Dummy BPK 26 - Anggota 23', 'Banjarbaru', '1993-12-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000023', '081300260023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(736, 24, 26, 'Dummy BPK 26 - Anggota 24', 'Banjarbaru', '1990-10-11', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000024', '081300260024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(737, 25, 26, 'Dummy BPK 26 - Anggota 25', 'Banjarbaru', '2004-07-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002600000025', '081300260025', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(738, 1, 27, 'Dummy BPK 27 - Anggota 1', 'Banjarbaru', '1996-01-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000001', '081300270001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(739, 2, 27, 'Dummy BPK 27 - Anggota 2', 'Banjarbaru', '1991-03-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000002', '081300270002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(740, 3, 27, 'Dummy BPK 27 - Anggota 3', 'Banjarbaru', '1993-10-01', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002700000003', '081300270003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(741, 4, 27, 'Dummy BPK 27 - Anggota 4', 'Banjarbaru', '2003-05-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000004', '081300270004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(742, 5, 27, 'Dummy BPK 27 - Anggota 5', 'Banjarbaru', '1999-06-18', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002700000005', '081300270005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(743, 6, 27, 'Dummy BPK 27 - Anggota 6', 'Banjarbaru', '2002-05-27', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000006', '081300270006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(744, 7, 27, 'Dummy BPK 27 - Anggota 7', 'Banjarbaru', '1984-02-02', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002700000007', '081300270007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(745, 8, 27, 'Dummy BPK 27 - Anggota 8', 'Banjarbaru', '1992-12-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000008', '081300270008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(746, 9, 27, 'Dummy BPK 27 - Anggota 9', 'Banjarbaru', '1998-01-18', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000009', '081300270009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(747, 10, 27, 'Dummy BPK 27 - Anggota 10', 'Banjarbaru', '1987-02-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000010', '081300270010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(748, 11, 27, 'Dummy BPK 27 - Anggota 11', 'Banjarbaru', '1980-05-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000011', '081300270011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(749, 12, 27, 'Dummy BPK 27 - Anggota 12', 'Banjarbaru', '2005-07-04', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002700000012', '081300270012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(750, 13, 27, 'Dummy BPK 27 - Anggota 13', 'Banjarbaru', '1995-08-19', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002700000013', '081300270013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(751, 14, 27, 'Dummy BPK 27 - Anggota 14', 'Banjarbaru', '1992-08-28', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002700000014', '081300270014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(752, 15, 27, 'Dummy BPK 27 - Anggota 15', 'Banjarbaru', '1997-09-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000015', '081300270015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(753, 16, 27, 'Dummy BPK 27 - Anggota 16', 'Banjarbaru', '1983-02-16', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000016', '081300270016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(754, 17, 27, 'Dummy BPK 27 - Anggota 17', 'Banjarbaru', '1989-09-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000017', '081300270017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(755, 18, 27, 'Dummy BPK 27 - Anggota 18', 'Banjarbaru', '1999-12-17', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000018', '081300270018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(756, 19, 27, 'Dummy BPK 27 - Anggota 19', 'Banjarbaru', '1997-10-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000019', '081300270019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(757, 20, 27, 'Dummy BPK 27 - Anggota 20', 'Banjarbaru', '2002-03-06', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000020', '081300270020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(758, 21, 27, 'Dummy BPK 27 - Anggota 21', 'Banjarbaru', '1994-07-15', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000021', '081300270021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(759, 22, 27, 'Dummy BPK 27 - Anggota 22', 'Banjarbaru', '2001-11-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000022', '081300270022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(760, 23, 27, 'Dummy BPK 27 - Anggota 23', 'Banjarbaru', '1990-08-20', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002700000023', '081300270023', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(761, 24, 27, 'Dummy BPK 27 - Anggota 24', 'Banjarbaru', '1985-05-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002700000024', '081300270024', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(762, 1, 28, 'Dummy BPK 28 - Anggota 1', 'Banjarbaru', '2002-05-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000001', '081300280001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(763, 2, 28, 'Dummy BPK 28 - Anggota 2', 'Banjarbaru', '1999-02-08', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002800000002', '081300280002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(764, 3, 28, 'Dummy BPK 28 - Anggota 3', 'Banjarbaru', '2004-06-10', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000003', '081300280003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(765, 4, 28, 'Dummy BPK 28 - Anggota 4', 'Banjarbaru', '1998-08-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000004', '081300280004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(766, 5, 28, 'Dummy BPK 28 - Anggota 5', 'Banjarbaru', '2004-08-30', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002800000005', '081300280005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(767, 6, 28, 'Dummy BPK 28 - Anggota 6', 'Banjarbaru', '2007-01-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000006', '081300280006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(768, 7, 28, 'Dummy BPK 28 - Anggota 7', 'Banjarbaru', '1998-06-04', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000007', '081300280007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(769, 8, 28, 'Dummy BPK 28 - Anggota 8', 'Banjarbaru', '2004-05-05', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002800000008', '081300280008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(770, 9, 28, 'Dummy BPK 28 - Anggota 9', 'Banjarbaru', '2002-12-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000009', '081300280009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(771, 10, 28, 'Dummy BPK 28 - Anggota 10', 'Banjarbaru', '2005-04-24', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000010', '081300280010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(772, 11, 28, 'Dummy BPK 28 - Anggota 11', 'Banjarbaru', '1980-08-13', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002800000011', '081300280011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(773, 12, 28, 'Dummy BPK 28 - Anggota 12', 'Banjarbaru', '1982-12-03', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000012', '081300280012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(774, 13, 28, 'Dummy BPK 28 - Anggota 13', 'Banjarbaru', '2002-08-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000013', '081300280013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(775, 14, 28, 'Dummy BPK 28 - Anggota 14', 'Banjarbaru', '1999-11-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000014', '081300280014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(776, 15, 28, 'Dummy BPK 28 - Anggota 15', 'Banjarbaru', '1990-02-13', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000015', '081300280015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(777, 16, 28, 'Dummy BPK 28 - Anggota 16', 'Banjarbaru', '1993-04-26', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002800000016', '081300280016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(778, 17, 28, 'Dummy BPK 28 - Anggota 17', 'Banjarbaru', '2005-01-26', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000017', '081300280017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(779, 18, 28, 'Dummy BPK 28 - Anggota 18', 'Banjarbaru', '1980-07-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000018', '081300280018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(780, 19, 28, 'Dummy BPK 28 - Anggota 19', 'Banjarbaru', '1984-01-13', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002800000019', '081300280019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(781, 20, 28, 'Dummy BPK 28 - Anggota 20', 'Banjarbaru', '1996-10-29', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002800000020', '081300280020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(782, 1, 29, 'Dummy BPK 29 - Anggota 1', 'Banjarbaru', '1992-04-15', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002900000001', '081300290001', 'aktif', 'Ketua', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(783, 2, 29, 'Dummy BPK 29 - Anggota 2', 'Banjarbaru', '1995-08-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000002', '081300290002', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(784, 3, 29, 'Dummy BPK 29 - Anggota 3', 'Banjarbaru', '2002-09-15', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002900000003', '081300290003', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(785, 4, 29, 'Dummy BPK 29 - Anggota 4', 'Banjarbaru', '1996-09-08', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000004', '081300290004', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(786, 5, 29, 'Dummy BPK 29 - Anggota 5', 'Banjarbaru', '1983-03-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000005', '081300290005', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(787, 6, 29, 'Dummy BPK 29 - Anggota 6', 'Banjarbaru', '1999-11-07', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000006', '081300290006', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(788, 7, 29, 'Dummy BPK 29 - Anggota 7', 'Banjarbaru', '1999-12-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000007', '081300290007', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(789, 8, 29, 'Dummy BPK 29 - Anggota 8', 'Banjarbaru', '1988-07-15', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002900000008', '081300290008', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(790, 9, 29, 'Dummy BPK 29 - Anggota 9', 'Banjarbaru', '1990-02-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000009', '081300290009', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(791, 10, 29, 'Dummy BPK 29 - Anggota 10', 'Banjarbaru', '1986-05-27', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002900000010', '081300290010', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(792, 11, 29, 'Dummy BPK 29 - Anggota 11', 'Banjarbaru', '1982-12-21', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002900000011', '081300290011', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(793, 12, 29, 'Dummy BPK 29 - Anggota 12', 'Banjarbaru', '2006-03-21', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000012', '081300290012', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(794, 13, 29, 'Dummy BPK 29 - Anggota 13', 'Banjarbaru', '1985-12-24', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002900000013', '081300290013', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(795, 14, 29, 'Dummy BPK 29 - Anggota 14', 'Banjarbaru', '1994-07-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000014', '081300290014', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(796, 15, 29, 'Dummy BPK 29 - Anggota 15', 'Banjarbaru', '1983-09-23', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000015', '081300290015', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(797, 16, 29, 'Dummy BPK 29 - Anggota 16', 'Banjarbaru', '2001-12-05', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000016', '081300290016', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(798, 17, 29, 'Dummy BPK 29 - Anggota 17', 'Banjarbaru', '2007-03-09', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000017', '081300290017', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(799, 18, 29, 'Dummy BPK 29 - Anggota 18', 'Banjarbaru', '1997-12-30', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000018', '081300290018', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(800, 19, 29, 'Dummy BPK 29 - Anggota 19', 'Banjarbaru', '1995-02-25', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000019', '081300290019', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(801, 20, 29, 'Dummy BPK 29 - Anggota 20', 'Banjarbaru', '1996-05-29', 'Perempuan', 'DATA_DUMMY_HAPUS', '6372002900000020', '081300290020', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(802, 21, 29, 'Dummy BPK 29 - Anggota 21', 'Banjarbaru', '1991-02-22', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000021', '081300290021', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48'),
(803, 22, 29, 'Dummy BPK 29 - Anggota 22', 'Banjarbaru', '1989-04-20', 'Laki-laki', 'DATA_DUMMY_HAPUS', '6372002900000022', '081300290022', 'aktif', 'Anggota', NULL, NULL, '2026-08-05 13:45:48', '2026-08-05 13:45:48');

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
(2, '002', 'Emergency Hayati', 'Tasbih Regency, Guntungmanggis, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Sungai Besar', '1785934987_6a73348b7c3ba.jpg', -3.46527362, 114.82771754, '2003', 1, '2026-05-23 15:53:44', '2026-08-05 13:03:07'),
(3, '003', 'BPK AN-NAFI', 'Jl. Rambai Tengah II No.9A, RT.002/RW.005, Guntung Paikat, Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Kemuning', '1785935024_6a7334b01f393.jpg', -3.44807054, 114.83140236, '2012', 0, '2026-07-07 07:15:24', '2026-08-05 13:03:44'),
(4, '004', 'G474H FIRE FIGHTER & RESCUE', 'Jl. Listrik 2, Loktabat Sel., Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Guntung Paikat', '1785935034_6a7334ba99d0f.png', -3.44486512, 114.83713723, '2006', 0, '2026-07-07 10:22:19', '2026-08-05 13:03:54'),
(5, '005', 'PMK HARMA', 'Jl. Zafri Zam-Zam II, Guntungmanggis, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Kemuning', '1785935046_6a7334c62a8ef.jpg', -3.45283885, 114.82731496, '2005', 0, '2026-07-07 10:50:12', '2026-08-05 13:04:06'),
(6, '006', 'MADA FIRE GROUP', 'Jl. Raya Cancer No.42, RT.45/RW.8, sungai besar, Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Selatan', 'Sungai Besar', '1785935058_6a7334d278a7f.jpg', -3.45855688, 114.84740450, '2020', 0, '2026-07-07 14:03:45', '2026-08-05 13:04:18'),
(7, '007', 'PMK BAUNTUNG', 'Jalan Jati ujung No.25, Loktabat Sel., Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70713', 'Banjarbaru Selatan', 'Kemuning', '1785935068_6a7334dc5a12a.jpg', -3.44485836, 114.82899116, '2012', 0, '2026-07-07 14:14:17', '2026-08-05 13:04:28'),
(8, '008', 'REGAS FIRE & RESCUE', 'Jl. Permata Intan, Sungai Besar, Kec. Banjarbaru Selatan, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Utara', 'Sungai Ulin', '1785935079_6a7334e726a51.png', -3.44827730, 114.86631772, '2000', 0, '2026-07-07 14:26:22', '2026-08-05 13:04:39'),
(9, '009', 'ETB FIRE RESCUE', 'Komplek Amaco, Jl. Nilam V, RT.021/RW.009, Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru, Kalimantan Selatan 70712', 'Banjarbaru Utara', 'Loktabat Utara', '1785935095_6a7334f73960c.png', -3.43576771, 114.82367871, '2000', 0, '2026-07-07 14:41:27', '2026-08-05 13:04:55'),
(10, '011', 'BPK BATRA', 'Gg. Abadi, Guntung Payung, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70714', 'Banjarbaru Utara', 'Loktabat Utara', '1785935116_6a73350c4e010.jpg', -3.44164388, 114.81834593, '2000', 0, '2026-07-07 14:43:20', '2026-08-05 13:05:16'),
(11, '010', 'PALAPA FIRE RESCUE', 'Komp Palapa, Kelurahan Mentaos Banjarbaru Utara', 'Banjarbaru Utara', 'Mentaos', '1785935104_6a7335001d5c4.jpg', -3.43825709, 114.83512258, '2024', 0, '2026-07-21 13:12:02', '2026-08-05 13:05:04'),
(12, '012', 'BPK TRISAKTI', 'Jl. H. Mistar Cokrokusumo, Bangkal, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70732', 'Cempaka', 'Cempaka', '1785935126_6a733516f04d1.jpg', -3.48901904, 114.85266880, '2000', 0, '2026-07-21 13:27:53', '2026-08-05 13:05:26'),
(13, '013', 'BPK Relawan Haul (RH)', 'Jl. H. Mistar Cokrokusumo, Bangkal, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70732', 'Cempaka', 'Sungai Tiung', '1785935136_6a7335202ab45.jpg', -3.49863071, 114.84753260, '2000', 0, '2026-07-21 13:43:54', '2026-08-05 13:05:36'),
(14, '014', 'BPK SIBAT BABBUSALAM', 'Jl. H. Mistar Cokrokusumo, Cempaka, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70852', 'Cempaka', 'Bangkal', '1785935148_6a73352cd9283.jpg', -3.52288985, 114.81062517, '2000', 0, '2026-07-21 13:45:49', '2026-08-05 13:05:48'),
(15, '015', 'NEW RESCUE MASPAL', '', 'Cempaka', 'Palam', '1785935157_6a733535d4fe1.jpg', -3.47345543, 114.80912017, '2000', 0, '2026-07-21 14:05:06', '2026-08-05 13:05:57'),
(16, '016', 'BPK RAST', 'Sungai Tiung, Cempaka, Banjarbaru City, South Kalimantan 70732', 'Cempaka', 'Sungai Tiung', '1785935168_6a7335402de1d.jpeg', -3.50383768, 114.84097320, '2000', 0, '2026-07-21 14:06:56', '2026-08-05 13:06:08'),
(17, '017', 'AL-BIDAYAH FIRE FIGHTER', 'Gg. Bersama, Sungai Tiung, Kec. Cemp., Kota Banjar Baru, Kalimantan Selatan 70732', 'Cempaka', 'Cempaka', '1785935179_6a73354b3bab1.png', -3.49459948, 114.85576008, '2000', 0, '2026-07-21 14:08:00', '2026-08-05 13:06:19'),
(18, '018', 'KAMPUR FIRE RESCUE', 'Jl. Purnawirawan, RT.05 RW02/RW.Kampung Purun, Palam, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70732', 'Cempaka', 'Palam', '1785935215_6a73356fc63c6.png', -3.50181611, 114.78715901, '2000', 0, '2026-07-21 14:10:06', '2026-08-05 13:06:55'),
(19, '019', 'AL-FATIH FIRE RESCUE', 'Jl. Kenanga No.32, Landasan Ulin Tim., Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70721', 'Landasan Ulin', 'Landasan Ulin Timur', '1785935226_6a73357a1f39a.jpg', -3.45216952, 114.76316136, '2000', 0, '2026-07-21 14:13:01', '2026-08-05 13:07:06'),
(20, '020', 'AL-MA\'UNAH FIRE', 'Jl. Tekukur, Landasan Ulin Tengah, Kec. Liang Anggang, Kota Banjar Baru, Kalimantan Selatan 70724', 'Landasan Ulin', 'Landasan Ulin Timur', '1785935234_6a7335829f878.jpg', -3.45605334, 114.74086665, '2000', 0, '2026-07-21 14:18:21', '2026-08-05 13:07:14'),
(21, '021', 'BPK GUP', 'Jl. Betet 36-26, Landasan Ulin Tengah, Kec. Liang Anggang, Kota Banjar Baru, Kalimantan Selatan 70724', 'Landasan Ulin', 'Landasan Ulin Timur', '1785935243_6a73358b24992.jpg', -3.45562626, 114.74024945, '2000', 0, '2026-07-21 14:19:40', '2026-08-05 13:07:23'),
(22, '022', 'SAUDARA FIRE RESCUE', 'Jl. Kuranji, Landasan Ulin Tim., Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70721', 'Landasan Ulin', 'Guntung Manggis', '1785935263_6a73359f74a07.jpg', -3.44985937, 114.77283229, '2000', 0, '2026-07-21 14:21:34', '2026-08-05 13:07:43'),
(23, '023', 'BPK BAROKAH SYAMSUDIN NOOR', 'Syamsudin Noor, Landasan Ulin, Banjarbaru City, South Kalimantan 70721', 'Landasan Ulin', 'Syamsudin Noor', NULL, -3.43027443, 114.76673967, '2000', 0, '2026-07-21 14:28:16', '2026-07-21 14:28:16'),
(24, '024', 'BPK SEPAKAT', 'Landasan Ulin Utara, Liang Anggang, Banjarbaru City, South Kalimantan 70724', 'Landasan Ulin', 'Syamsudin Noor', '1785935300_6a7335c4cb09a.jpg', -3.42975383, 114.75447048, '2000', 0, '2026-07-21 14:29:19', '2026-08-05 13:08:20'),
(25, '025', 'GUNTUNG MANGGIS FIRE RESCUE', 'Komplek.GPIP II, Jl. Kebun Durian No.15 Kel Blok P, Guntungmanggis, Kec. Landasan Ulin, Kota Banjar Baru, Kalimantan Selatan 70721', 'Landasan Ulin', 'Guntung Manggis', '1785935310_6a7335ce9caef.jpg', -3.46772800, 114.79336240, '2000', 0, '2026-07-21 14:31:13', '2026-08-05 13:08:30'),
(26, '026', 'BPK PRABU', 'Jl. Trikora simpang 4 Peramuan, RT.04/RW.04, Landasan Ulin Sel., Timur, Kota Banjar Baru, Kalimantan Selatan 70723', 'Liang Anggang', 'Landasan Ulin Tengah', '1785935319_6a7335d77b3c3.jpg', -3.45183274, 114.73494097, '2000', 0, '2026-07-21 14:32:31', '2026-08-05 13:08:39'),
(27, '027', 'PENGAYUAN RESCUE', 'Jl. Pintas Sambangan No.20, Landasan Ulin Sel., Kec. Liang Anggang, Kota Banjar Baru, Kalimantan Selatan 70723', 'Liang Anggang', 'Landasan Ulin Selatan', NULL, -3.50172284, 114.71167251, '2000', 0, '2026-07-21 14:34:03', '2026-07-21 14:34:03'),
(28, '028', 'BPK SWASTA PRIBUMI 07', 'Jalan Sriwijaya No.KM.21,600, Landasan Ulin Utara, Kec. Liang Anggang, Kota Banjar Baru, Kalimantan Selatan 70722', 'Liang Anggang', 'Landasan Ulin Utara', NULL, -3.42772919, 114.71580624, '2000', 0, '2026-07-21 14:39:02', '2026-07-21 14:39:02'),
(29, '029', 'BPK RELAWAN BINA PUTRA BERSATU 2025 FIRE RESCUE', 'Landasan Ulin Timur, Landasan Ulin, Banjarbaru City, South Kalimantan 70721', 'Landasan Ulin', 'Guntung Payung', '1785935353_6a7335f90e7bb.jpeg', -3.44399169, 114.78614847, '2000', 0, '2026-07-21 14:40:50', '2026-08-05 13:09:13');

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
(11, 25, 15, 70, '2026-06-20 10:25:46'),
(12, 30, 22, 85, '2026-07-23 14:59:53'),
(13, 20, 15, 85, '2026-08-01 08:44:54'),
(14, 20, 15, 85, '2026-08-01 08:50:22'),
(15, 20, 15, 85, '2026-08-01 15:39:19'),
(16, 20, 15, 85, '2026-08-01 15:50:33'),
(17, 30, 10, 85, '2026-08-01 15:51:56'),
(18, 30, 10, 85, '2026-08-01 16:00:15'),
(19, 40, 15, 85, '2026-08-05 08:00:07'),
(20, 40, 15, 85, '2026-08-05 08:02:52'),
(21, 40, 15, 85, '2026-08-05 12:56:46'),
(22, 40, 15, 85, '2026-08-05 12:56:51'),
(23, 40, 30, 70, '2026-08-07 01:42:57'),
(24, 40, 30, 70, '2026-08-07 01:43:02');

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
(13, '2026-01-19 17:06:00', -3.44334001, 114.74310055, 'Jl. Kampung Baru No. 11 RT. 02 RW. 02 Kel. Landasan Ulin Timur Kec. Liang Anggang', 'Landasan Ulin', 'Landasan Ulin Timur', 2, 3, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Menindaklanjuti laporan Via Grup WA Emergency Banjarbaru telah terjadi kebakaran bangunan. Petugas melakukan pemadaman dan pendinginan. Terdampak 80% bangunan terbakar. Asal api masih dalam tahap penyelidikan Kepolisian. Kerugian kurang lebih 100 Juta Rupiah.', NULL, 5, NULL, '2026-07-21 15:37:13'),
(214, '2020-01-05 14:20:00', -3.43125600, 114.83214500, 'Jl. Komet Raya No.50', 'Banjarbaru Utara', 'Komet', 1, 0, 0, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Terjadi Kebakaran Sebuah Bangunan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(215, '2020-01-12 09:15:00', -3.44589200, 114.86123400, 'JL. Garuda Rt.03/04', 'Banjarbaru Utara', 'Komet', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(216, '2020-01-22 20:10:00', -3.42874100, 114.84567800, 'Sungai Ulin, Banjarbaru', 'Banjarbaru Utara', 'Sungai Ulin', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(217, '2020-02-03 11:30:00', -3.45123400, 114.83890100, 'Jl. Karang Anyar 1', 'Banjarbaru Utara', 'Loktabat Utara', 2, 2, 6, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Menindaklanjuti laporan warga', NULL, 5, NULL, '2026-07-21 16:28:32'),
(218, '2020-02-15 02:45:00', -3.43987600, 114.85234500, 'Jl. Panglima Batur Timur', 'Banjarbaru Utara', 'Mentaos', 1, 1, 4, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(219, '2020-02-28 16:10:00', -3.43345100, 114.82899000, 'Jl. Rahayu RT.02', 'Banjarbaru Utara', 'Mentaos', 1, 1, 3, 0, 0, NULL, NULL, NULL, 'Pemadaman dibantu warga sekitar', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(220, '2020-03-08 19:25:00', -3.44876500, 114.86543200, 'Komp. Beringin Raya', 'Banjarbaru Utara', 'Sungai Ulin', 0, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, NULL, 'Lahan kosong terbakar', NULL, 5, NULL, '2026-07-21 16:28:32'),
(221, '2020-03-20 08:40:00', -3.42567800, 114.83123400, 'Jl. Merpati, Komet', 'Banjarbaru Utara', 'Komet', 1, 2, 5, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(222, '2020-04-05 13:15:00', -3.44123400, 114.85567800, 'Jl. Taruna Bumi', 'Banjarbaru Utara', 'Loktabat Utara', 1, 0, 0, 0, 0, NULL, NULL, 'rusak ringan', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(223, '2020-04-18 22:50:00', -3.43678900, 114.84234500, 'Jl. Pangeran Suriansyah', 'Banjarbaru Utara', 'Mentaos', 2, 2, 7, 0, 0, 'Korsleting listrik', NULL, 'rusak total', 'Kebakaran Koperasi', NULL, 5, NULL, '2026-07-21 16:28:32'),
(224, '2020-05-02 10:20:00', -3.45345600, 114.86987600, 'Jl. PM Noor RT.08', 'Banjarbaru Utara', 'Sungai Ulin', 1, 1, 4, 0, 0, 'Dalam penyelidikan', NULL, 'rusak sedang', NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(225, '2020-05-15 05:30:00', -3.42987600, 114.83567800, 'Gg. Keluarga, Kel. Komet', 'Banjarbaru Utara', 'Komet', 1, 1, 3, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(226, '2020-06-01 17:45:00', -3.44456700, 114.84876500, 'Jl. Sido Mulyo', 'Banjarbaru Utara', 'Loktabat Utara', 0, 0, 0, 0, 0, NULL, NULL, NULL, 'Hanya tumpukan sampah', NULL, 5, NULL, '2026-07-21 16:28:32'),
(227, '2020-06-14 12:10:00', -3.43234500, 114.82678900, 'Jl. Mentaos Raya', 'Banjarbaru Utara', 'Mentaos', 1, 1, 5, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(228, '2020-07-08 21:35:00', -3.45567800, 114.86234500, 'Komp. Binti, Sungai Ulin', 'Banjarbaru Utara', 'Sungai Ulin', 1, 0, 0, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Laporan Via Grup WA', NULL, 5, NULL, '2026-07-21 16:28:32'),
(229, '2020-08-10 15:50:00', -3.43876500, 114.83987600, 'Jl. Amaco RT.10', 'Banjarbaru Utara', 'Loktabat Utara', 1, 2, 6, 0, 0, NULL, NULL, 'rusak berat', NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(230, '2020-09-05 08:25:00', -3.42678900, 114.84654300, 'Jl. Cendrawasih', 'Banjarbaru Utara', 'Mentaos', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Laporan Warga', NULL, 5, NULL, '2026-07-21 16:28:32'),
(231, '2020-10-12 19:40:00', -3.45098700, 114.85876500, 'Komp. Bumi Permata', 'Banjarbaru Utara', 'Sungai Ulin', 2, 2, 8, 0, 0, 'Korsleting listrik', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(232, '2020-11-20 11:15:00', -3.44234500, 114.83345600, 'Komp. Karantina', 'Banjarbaru Utara', 'Loktabat Utara', 1, 1, 4, 0, 0, NULL, NULL, NULL, NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(233, '2020-12-25 04:30:00', -3.43543200, 114.85123400, 'Jl. Nuri, Kel. Mentaos', 'Banjarbaru Utara', 'Mentaos', 1, 1, 3, 0, 0, 'Dalam penyelidikan', NULL, 'rusak sedang', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(234, '2020-01-03 18:41:00', -3.44987600, 114.84123400, 'Indomaret Mistar Cokrokusumo', 'Banjarbaru Selatan', 'Sungai Besar', 1, 0, 0, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Menindaklanjuti laporan Via WA', NULL, 5, NULL, '2026-07-21 16:28:32'),
(235, '2020-01-18 08:17:00', -3.46876500, 114.81543200, 'Kemuning, Banjarbaru', 'Banjarbaru Selatan', 'Kemuning', 1, 1, 2, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(236, '2020-02-10 14:48:00', -3.45543200, 114.82987600, 'Griya Mawar Asri', 'Banjarbaru Selatan', 'Loktabat Selatan', 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(237, '2020-03-05 21:10:00', -3.47234500, 114.84876500, 'Jl. Kelapa Gading', 'Banjarbaru Selatan', 'Sungai Besar', 1, 1, 4, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(238, '2020-03-22 10:30:00', -3.46123400, 114.82234500, 'Jl. Kemuning Raya', 'Banjarbaru Selatan', 'Kemuning', 1, 0, 0, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Kebakaran Bangunan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(239, '2020-04-12 16:45:00', -3.44567800, 114.81876500, 'Komp. Banjarbaru Indah', 'Banjarbaru Selatan', 'Loktabat Selatan', 2, 2, 7, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(240, '2020-05-08 03:20:00', -3.46543200, 114.84567800, 'Jl. Jeruk, Sungai Besar', 'Banjarbaru Selatan', 'Sungai Besar', 1, 1, 5, 0, 0, NULL, NULL, 'rusak sedang', NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(241, '2020-05-25 11:15:00', -3.47123400, 114.82876500, 'Gg. Karya, Kemuning', 'Banjarbaru Selatan', 'Kemuning', 1, 1, 3, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(242, '2020-06-10 19:50:00', -3.45876500, 114.82123400, 'Jl. RO Ulin', 'Banjarbaru Selatan', 'Loktabat Selatan', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(243, '2020-07-02 08:40:00', -3.44876500, 114.85123400, 'Komp. Unlam', 'Banjarbaru Selatan', 'Sungai Besar', 0, 0, 0, 0, 0, NULL, NULL, NULL, 'Lahan ilalang', NULL, 5, NULL, '2026-07-21 16:28:32'),
(244, '2020-07-18 14:25:00', -3.46345600, 114.81654300, 'Jl. Zam Zam', 'Banjarbaru Selatan', 'Kemuning', 1, 2, 6, 0, 0, 'Korsleting listrik', NULL, 'rusak total', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(245, '2020-08-05 22:10:00', -3.45234500, 114.83234500, 'Jl. Nusantara', 'Banjarbaru Selatan', 'Loktabat Selatan', 1, 1, 4, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(246, '2020-08-22 06:15:00', -3.46987600, 114.84123400, 'Jl. Pendidikan', 'Banjarbaru Selatan', 'Sungai Besar', 1, 1, 3, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(247, '2020-09-15 17:30:00', -3.45678900, 114.82567800, 'Komp. Beringin', 'Banjarbaru Selatan', 'Kemuning', 2, 2, 8, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Laporan Via WA', NULL, 5, NULL, '2026-07-21 16:28:32'),
(248, '2020-10-01 10:45:00', -3.44765400, 114.81987600, 'Jl. S. Parman', 'Banjarbaru Selatan', 'Loktabat Selatan', 1, 0, 0, 0, 0, NULL, NULL, 'rusak ringan', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(249, '2020-10-20 23:55:00', -3.47456700, 114.85432100, 'Jl. H. Idak', 'Banjarbaru Selatan', 'Sungai Besar', 1, 1, 4, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Terjadi Kebakaran Koperasi', NULL, 5, NULL, '2026-07-21 16:28:32'),
(250, '2020-11-08 04:10:00', -3.46654300, 114.82198700, 'Jl. Jati, Kemuning', 'Banjarbaru Selatan', 'Kemuning', 1, 2, 7, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Laporan warga', NULL, 5, NULL, '2026-07-21 16:28:32'),
(251, '2020-11-25 15:20:00', -3.45198700, 114.82654300, 'Komp. Palam Asri', 'Banjarbaru Selatan', 'Loktabat Selatan', 0, 0, 0, 0, 0, NULL, NULL, NULL, 'Pembakaran sampah', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(252, '2020-12-12 09:35:00', -3.46198700, 114.84765400, 'Jl. Al-Jafri', 'Banjarbaru Selatan', 'Sungai Besar', 1, 1, 5, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(253, '2020-12-30 20:45:00', -3.45398700, 114.81765400, 'Gg. Swadaya, Kemuning', 'Banjarbaru Selatan', 'Kemuning', 1, 1, 3, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(254, '2020-01-20 11:38:00', -3.48567800, 114.84123400, 'Cempaka, Banjarbaru', 'Cempaka', 'Cempaka', 6, 6, 19, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(255, '2020-02-05 15:20:00', -3.51234500, 114.86543200, 'Jl. H. Mistar Cokrokusumo', 'Cempaka', 'Cempaka', 1, 1, 4, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Laporan warga', NULL, 5, NULL, '2026-07-21 16:28:32'),
(256, '2020-02-18 09:10:00', -3.49876500, 114.87234500, 'Jl. Sungai Tiung RT.01', 'Cempaka', 'Sungai Tiung', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(257, '2020-03-10 22:45:00', -3.51876500, 114.85123400, 'Bangkal, Banjarbaru', 'Cempaka', 'Bangkal', 0, 0, 0, 0, 0, NULL, NULL, NULL, 'Lahan gambut', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(258, '2020-03-28 06:30:00', -3.48123400, 114.85876500, 'Komp. Perumahan Cempaka', 'Cempaka', 'Cempaka', 1, 1, 5, 0, 0, 'Korsleting listrik', NULL, 'rusak total', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(259, '2020-04-15 14:15:00', -3.50456700, 114.86876500, 'Gg. Keluarga, Sungai Tiung', 'Cempaka', 'Sungai Tiung', 1, 2, 6, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Laporan via grup', NULL, 5, NULL, '2026-07-21 16:28:32'),
(260, '2020-05-05 19:50:00', -3.51543200, 114.84567800, 'Jl. Bangkal Raya', 'Cempaka', 'Bangkal', 2, 2, 8, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(261, '2020-05-22 10:25:00', -3.49123400, 114.84876500, 'Pasar Cempaka', 'Cempaka', 'Cempaka', 3, 3, 12, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(262, '2020-06-12 02:40:00', -3.50123400, 114.87876500, 'Jl. Transpol', 'Cempaka', 'Sungai Tiung', 1, 1, 4, 0, 0, NULL, NULL, 'rusak ringan', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(263, '2020-07-01 17:10:00', -3.51123400, 114.85567800, 'Bangkal Dalam', 'Cempaka', 'Bangkal', 1, 1, 5, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(264, '2020-07-20 08:55:00', -3.48876500, 114.86123400, 'Jl. Kertak Baru', 'Cempaka', 'Cempaka', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(265, '2020-08-08 21:20:00', -3.49567800, 114.86543200, 'Komp. Griya, Sungai Tiung', 'Cempaka', 'Sungai Tiung', 1, 1, 3, 0, 0, 'Korsleting listrik', NULL, 'rusak total', NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(266, '2020-08-25 11:45:00', -3.51987600, 114.84876500, 'Jl. Karet, Bangkal', 'Cempaka', 'Bangkal', 1, 2, 7, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Laporan warga', NULL, 5, NULL, '2026-07-21 16:28:32'),
(267, '2020-09-12 16:30:00', -3.48345600, 114.85234500, 'Gg. Manggis, Cempaka', 'Cempaka', 'Cempaka', 0, 0, 0, 0, 0, NULL, NULL, NULL, 'Lahan kosong', NULL, 5, NULL, '2026-07-21 16:28:32'),
(268, '2020-10-05 05:15:00', -3.50876500, 114.87123400, 'Jl. Cempaka Sari', 'Cempaka', 'Sungai Tiung', 1, 1, 4, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(269, '2020-10-22 13:50:00', -3.51678900, 114.85987600, 'Bangkal', 'Cempaka', 'Bangkal', 2, 2, 9, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(270, '2020-11-10 20:05:00', -3.49456700, 114.84567800, 'Komp. Bukit Sirkuit', 'Cempaka', 'Cempaka', 1, 1, 3, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(271, '2020-11-28 09:40:00', -3.50543200, 114.86234500, 'Jl. Suka Ramai', 'Cempaka', 'Sungai Tiung', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Laporan via grup', NULL, 5, NULL, '2026-07-21 16:28:32'),
(272, '2020-12-15 15:25:00', -3.51345600, 114.85345600, 'Jl. Harapan, Bangkal', 'Cempaka', 'Bangkal', 1, 1, 5, 0, 0, NULL, NULL, 'rusak berat', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(273, '2020-12-28 23:10:00', -3.48987600, 114.86876500, 'Jl. Awang Peramuan', 'Cempaka', 'Cempaka', 1, 2, 6, 0, 0, 'Korsleting listrik', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(274, '2020-01-04 05:20:00', -3.45123400, 114.78123400, 'Jl. Trikora RT.06', 'Landasan Ulin', 'Landasan Ulin Timur', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(275, '2020-01-10 16:26:00', -3.42567800, 114.76876500, 'Jl. Lingkar Utara', 'Landasan Ulin', 'Syamsudin Noor', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(276, '2020-01-19 17:06:00', -3.46876500, 114.79543200, 'Jl. Kampung Baru', 'Landasan Ulin', 'Landasan Ulin Timur', 2, 3, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(277, '2020-02-08 13:58:00', -3.44123400, 114.80123400, 'Guntung Manggis', 'Landasan Ulin', 'Guntung Manggis', 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(278, '2020-03-01 10:32:00', -3.46543200, 114.77567800, 'Jl. Pembataan', 'Landasan Ulin', 'Guntung Manggis', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(279, '2020-03-18 20:15:00', -3.45876500, 114.79876500, 'Komp. Berlina', 'Landasan Ulin', 'Landasan Ulin Timur', 1, 1, 4, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(280, '2020-04-08 09:40:00', -3.43123400, 114.76234500, 'Jl. Kebun Karet', 'Landasan Ulin', 'Syamsudin Noor', 1, 1, 3, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Laporan warga', NULL, 5, NULL, '2026-07-21 16:28:32'),
(281, '2020-04-25 15:55:00', -3.46123400, 114.78543200, 'Jl. Bina Murni', 'Landasan Ulin', 'Landasan Ulin Timur', 0, 0, 0, 0, 0, NULL, NULL, NULL, 'Pembakaran lahan liar', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(282, '2020-05-12 04:20:00', -3.44567800, 114.80876500, 'Komp. Wengga Kuda', 'Landasan Ulin', 'Guntung Manggis', 1, 2, 5, 0, 0, 'Korsleting listrik', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(283, '2020-05-30 11:35:00', -3.45543200, 114.77123400, 'Jl. Sapta Marga Raya', 'Landasan Ulin', 'Guntung Manggis', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(284, '2020-06-18 18:10:00', -3.44876500, 114.79123400, 'Jl. A. Yani Km 24', 'Landasan Ulin', 'Landasan Ulin Timur', 2, 2, 8, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(285, '2020-07-05 07:45:00', -3.43876500, 114.75876500, 'Komp. Angkasa Permai', 'Landasan Ulin', 'Syamsudin Noor', 1, 1, 4, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', NULL, NULL, NULL, NULL, '2026-07-21 16:28:32'),
(286, '2020-07-22 21:20:00', -3.46345600, 114.78876500, 'Jl. Kasturi', 'Landasan Ulin', 'Landasan Ulin Timur', 1, 1, 5, 0, 0, 'Korsleting listrik', NULL, 'rusak total', 'Laporan Via WA', NULL, 5, NULL, '2026-07-21 16:28:32'),
(287, '2020-08-15 13:50:00', -3.44987600, 114.79876500, 'Jl. Teratai', 'Landasan Ulin', 'Guntung Manggis', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(288, '2020-09-02 05:15:00', -3.45876500, 114.81123400, 'Komp. Benawa Raya', 'Landasan Ulin', 'Guntung Manggis', 1, 1, 3, 0, 0, NULL, NULL, 'rusak ringan', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(289, '2020-09-20 16:40:00', -3.46987600, 114.78123400, 'Jl. Bina Putra', 'Landasan Ulin', 'Landasan Ulin Timur', 0, 0, 0, 0, 0, NULL, NULL, NULL, 'Lahan belakang', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(290, '2020-10-08 10:25:00', -3.42876500, 114.77543200, 'Komp. Halim Perdana', 'Landasan Ulin', 'Syamsudin Noor', 1, 2, 7, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Laporan warga', NULL, 5, NULL, '2026-07-21 16:28:32'),
(291, '2020-10-25 23:10:00', -3.45345600, 114.79345600, 'Jl. Sukamara', 'Landasan Ulin', 'Landasan Ulin Timur', 2, 2, 6, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(292, '2020-11-12 08:55:00', -3.46198700, 114.80543200, 'Komp. Citra Hasan', 'Landasan Ulin', 'Guntung Manggis', 1, 1, 4, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(293, '2020-12-05 19:30:00', -3.44234500, 114.78567800, 'Jl. Pangeran Antasari', 'Landasan Ulin', 'Guntung Manggis', 1, 1, 5, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(294, '2020-01-25 04:12:00', -3.44567800, 114.74123400, 'Jl. A Yani km 23', 'Liang Anggang', 'Landasan Ulin Tengah', 1, 2, 4, 0, 0, 'Dalam penyelidikan', NULL, NULL, NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(295, '2020-02-12 11:30:00', -3.46876500, 114.71543200, 'Jl. Pengayoman', 'Liang Anggang', 'Landasan Ulin Barat', 1, 0, 0, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Laporan warga', NULL, 5, NULL, '2026-07-21 16:28:32'),
(296, '2020-03-02 20:45:00', -3.48123400, 114.74876500, 'Jl. Lingkar Selatan', 'Liang Anggang', 'Landasan Ulin Selatan', 0, 0, 0, 0, 0, NULL, NULL, NULL, 'Lahan gambut terbakar', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(297, '2020-03-20 09:15:00', -3.45123400, 114.73123400, 'Komp. Kidaung', 'Liang Anggang', 'Landasan Ulin Tengah', 1, 1, 5, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(298, '2020-04-05 16:20:00', -3.47543200, 114.72123400, 'Jl. Makmur', 'Liang Anggang', 'Landasan Ulin Barat', 2, 2, 7, 0, 0, 'Korsleting listrik', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(299, '2020-04-22 03:50:00', -3.48876500, 114.73876500, 'Jl. Kenanga', 'Liang Anggang', 'Landasan Ulin Selatan', 1, 1, 3, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(300, '2020-05-10 14:10:00', -3.44123400, 114.74876500, 'Komp. Sejahtera', 'Liang Anggang', 'Landasan Ulin Tengah', 1, 2, 6, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Laporan Via WA', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(301, '2020-05-28 22:35:00', -3.46123400, 114.71123400, 'Jl. Melati', 'Liang Anggang', 'Landasan Ulin Barat', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(302, '2020-06-15 10:55:00', -3.48345600, 114.75123400, 'Gg. Swadaya', 'Liang Anggang', 'Landasan Ulin Selatan', 1, 1, 4, 0, 0, NULL, NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(303, '2020-07-02 18:25:00', -3.45876500, 114.73876500, 'Jl. A. Yani Km 21', 'Liang Anggang', 'Landasan Ulin Tengah', 2, 2, 8, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(304, '2020-07-20 05:40:00', -3.47123400, 114.72876500, 'Jl. Abadi', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 5, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Menindaklanjuti laporan', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(305, '2020-08-08 13:15:00', -3.48567800, 114.74567800, 'Komp. Surya Indah', 'Liang Anggang', 'Landasan Ulin Selatan', 1, 0, 0, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(306, '2020-08-25 21:50:00', -3.44876500, 114.73543200, 'Jl. Kurnia', 'Liang Anggang', 'Landasan Ulin Tengah', 0, 0, 0, 0, 0, NULL, NULL, NULL, 'Lahan semak', NULL, 5, NULL, '2026-07-21 16:28:32'),
(307, '2020-09-12 09:30:00', -3.46543200, 114.71876500, 'Gg. Mufakat', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 3, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(308, '2020-09-30 16:45:00', -3.47876500, 114.75432100, 'Jl. Pandu', 'Liang Anggang', 'Landasan Ulin Selatan', 1, 2, 7, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(309, '2020-10-18 02:10:00', -3.45432100, 114.74567800, 'Komp. Puri Damai', 'Liang Anggang', 'Landasan Ulin Tengah', 2, 2, 9, 0, 0, 'Dalam penyelidikan', NULL, 'rusak berat', 'Laporan warga', NULL, NULL, NULL, '2026-07-21 16:28:32'),
(310, '2020-11-05 11:25:00', -3.47987600, 114.72567800, 'Jl. Sukamaju', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 4, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Terjadi Kebakaran', NULL, 5, NULL, '2026-07-21 16:28:32'),
(311, '2020-11-22 20:55:00', -3.48987600, 114.73123400, 'Jl. A. Yani Km 22', 'Liang Anggang', 'Landasan Ulin Selatan', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Menindaklanjuti laporan', NULL, 5, NULL, '2026-07-21 16:28:32'),
(312, '2020-12-10 07:40:00', -3.44234500, 114.73234500, 'Landasan Ulin Tengah', 'Liang Anggang', 'Landasan Ulin Tengah', 1, 1, 5, 0, 0, NULL, NULL, 'rusak berat', NULL, NULL, 5, NULL, '2026-07-21 16:28:32'),
(313, '2020-12-28 15:15:00', -3.46198700, 114.72198700, 'Landasan Ulin Barat', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 3, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Laporan warga', NULL, 5, NULL, '2026-07-21 16:28:32'),
(324, '2020-08-01 09:15:00', -3.45900000, 114.72500000, 'Jl. Kurnia Gg. Nusantara 1', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 3, 0, 0, 'Korsleting listrik', NULL, 'rusak ringan', 'Data tes KDE LUB (Titik 1)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(325, '2020-08-02 14:20:00', -3.45905000, 114.72505000, 'Jl. Kurnia Gg. Nusantara 1', 'Liang Anggang', 'Landasan Ulin Barat', 1, 2, 5, 0, 0, 'Dalam penyelidikan', NULL, 'rusak sedang', 'Data tes KDE LUB (Titik 2)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(326, '2020-08-03 20:30:00', -3.45910000, 114.72495000, 'Jl. Kurnia Gg. Nusantara 2', 'Liang Anggang', 'Landasan Ulin Barat', 2, 2, 8, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Data tes KDE LUB (Titik 3)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(327, '2020-08-04 11:45:00', -3.45895000, 114.72510000, 'Jl. Kurnia Gg. Nusantara 2', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 4, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Data tes KDE LUB (Titik 4)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(328, '2020-08-05 08:10:00', -3.45890000, 114.72490000, 'Jl. Kurnia Gg. Nusantara 3', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 3, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Data tes KDE LUB (Titik 5)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(329, '2020-08-06 05:25:00', -3.45915000, 114.72515000, 'Jl. Kurnia Gg. Nusantara 3', 'Liang Anggang', 'Landasan Ulin Barat', 1, 2, 6, 0, 0, 'Korsleting listrik', NULL, 'rusak sedang', 'Data tes KDE LUB (Titik 6)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(330, '2020-08-07 16:50:00', -3.45885000, 114.72485000, 'Jl. Kurnia Gg. Nusantara 4', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 4, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Data tes KDE LUB (Titik 7)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(331, '2020-08-08 22:15:00', -3.45902000, 114.72498000, 'Jl. Kurnia Gg. Nusantara 4', 'Liang Anggang', 'Landasan Ulin Barat', 1, 0, 0, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Data tes KDE LUB (Titik 8)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(332, '2020-08-09 13:40:00', -3.45898000, 114.72502000, 'Jl. Kurnia Gg. Nusantara 5', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 5, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Data tes KDE LUB (Titik 9)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(333, '2020-08-10 08:05:00', -3.45908000, 114.72508000, 'Jl. Kurnia Gg. Nusantara 5', 'Liang Anggang', 'Landasan Ulin Barat', 2, 2, 7, 0, 0, 'Korsleting listrik', NULL, 'rusak total', 'Data tes KDE LUB (Titik 10)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(334, '2020-08-11 19:30:00', -3.45912000, 114.72492000, 'Jl. Kurnia Gg. Nusantara 1', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 3, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Data tes KDE LUB (Titik 11)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(335, '2020-08-12 14:15:00', -3.45888000, 114.72512000, 'Jl. Kurnia Gg. Nusantara 2', 'Liang Anggang', 'Landasan Ulin Barat', 1, 2, 5, 0, 0, 'Korsleting listrik', NULL, 'rusak sedang', 'Data tes KDE LUB (Titik 12)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(336, '2020-08-13 10:45:00', -3.45892000, 114.72488000, 'Jl. Kurnia Gg. Nusantara 3', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 4, 0, 0, 'Dalam penyelidikan', NULL, 'rusak ringan', 'Data tes KDE LUB (Titik 13)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(337, '2020-08-14 21:05:00', -3.45906000, 114.72506000, 'Jl. Kurnia Gg. Nusantara 4', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 2, 0, 0, 'Korsleting listrik', NULL, 'rusak berat', 'Data tes KDE LUB (Titik 14)', NULL, 5, NULL, '2026-07-23 14:35:23'),
(338, '2020-08-15 16:55:00', -3.45896000, 114.72496000, 'Jl. Kurnia Gg. Nusantara 5', 'Liang Anggang', 'Landasan Ulin Barat', 1, 1, 4, 0, 0, 'Dalam penyelidikan', NULL, 'rusak total', 'Data tes KDE LUB (Titik 15)', NULL, 5, NULL, '2026-07-23 14:35:23');

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
(102, 5, 'superadmin', 'super_admin', 'yusfi', 'Menambahkan data kejadian kebakaran baru (ID: 13)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 15:37:13'),
(103, 9, 'superadmin2', 'super_admin', 'abue', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 14:08:42'),
(104, 9, 'superadmin2', 'super_admin', 'abue', 'Memperbarui konfigurasi Heatmap (Radius: 30, Blur: 22, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 14:59:53'),
(105, 9, 'superadmin2', 'super_admin', 'abue', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 15:00:07'),
(106, 9, 'superadmin2', 'super_admin', 'abue', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 15:00:39'),
(107, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 03:15:02'),
(108, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 14:29:59'),
(109, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 14:53:25'),
(110, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 14:53:32'),
(111, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 15:18:07'),
(112, 9, 'superadmin2', 'super_admin', 'abue', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-30 15:18:12'),
(113, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-08-01 07:58:44'),
(114, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 20, Blur: 15, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-08-01 08:44:54'),
(115, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 20, Blur: 15, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-08-01 08:50:22'),
(116, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 20, Blur: 15, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-08-01 15:39:19'),
(117, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 20, Blur: 15, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-08-01 15:50:33'),
(118, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 30, Blur: 10, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-08-01 15:51:56'),
(119, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 30, Blur: 10, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-08-01 16:00:15'),
(120, 9, 'superadmin2', 'super_admin', 'abue', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', '2026-08-03 15:55:48'),
(121, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 06:59:01'),
(122, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 40, Blur: 15, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 08:00:07'),
(123, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 40, Blur: 15, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 08:02:52'),
(124, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 40, Blur: 15, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 12:56:46'),
(125, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 40, Blur: 15, Intensitas: 85%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 12:56:51'),
(126, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: Emergency Hayati', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:03:07'),
(127, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK AN-NAFI', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:03:44'),
(128, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: G474H FIRE FIGHTER & RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:03:54'),
(129, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: PMK HARMA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:04:06'),
(130, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: MADA FIRE GROUP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:04:18'),
(131, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: PMK BAUNTUNG', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:04:28'),
(132, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: REGAS FIRE & RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:04:39'),
(133, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: ETB FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:04:55'),
(134, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: PALAPA FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:05:04'),
(135, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK BATRA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:05:16'),
(136, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK TRISAKTI', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:05:26'),
(137, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK Relawan Haul (RH)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:05:36'),
(138, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK SIBAT BABBUSALAM', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:05:48'),
(139, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: NEW RESCUE MASPAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:05:57'),
(140, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK RAST', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:06:08'),
(141, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: AL-BIDAYAH FIRE FIGHTER', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:06:19'),
(142, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: KAMPUR FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:06:55'),
(143, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: AL-FATIH FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:07:06'),
(144, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: AL-MA\'UNAH FIRE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:07:14'),
(145, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK GUP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:07:23'),
(146, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: SAUDARA FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:07:43'),
(147, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK SEPAKAT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:08:20'),
(148, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: GUNTUNG MANGGIS FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:08:30'),
(149, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK PRABU', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:08:39'),
(150, 5, 'superadmin', 'super_admin', 'yusfi', 'Mengedit data BPK: BPK RELAWAN BINA PUTRA BERSATU 2025 FIRE RESCUE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:09:13'),
(151, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:13:11'),
(152, 9, 'superadmin2', 'super_admin', 'abue', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:36:22'),
(153, 9, 'superadmin2', 'super_admin', 'abue', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:36:45'),
(154, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:36:51'),
(155, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:37:30'),
(156, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 13:37:35'),
(157, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-05 22:36:53'),
(158, 5, 'superadmin', 'super_admin', 'yusfi', 'Logout dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-06 14:24:39'),
(159, 6, 'admin_bpk_001', 'admin_bpk', 'BPK INSAR 21', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-06 14:24:45'),
(160, 5, 'superadmin', 'super_admin', 'yusfi', 'Login ke sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-07 01:05:34'),
(161, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 40, Blur: 30, Intensitas: 70%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-07 01:42:57'),
(162, 5, 'superadmin', 'super_admin', 'yusfi', 'Memperbarui konfigurasi Heatmap (Radius: 40, Blur: 30, Intensitas: 70%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-07 01:43:02');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=804;

--
-- AUTO_INCREMENT for table `bpk`
--
ALTER TABLE `bpk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `heatmap_settings`
--
ALTER TABLE `heatmap_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `hydrant`
--
ALTER TABLE `hydrant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `kejadian_kebakaran`
--
ALTER TABLE `kejadian_kebakaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

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
