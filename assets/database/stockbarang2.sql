-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 31 Jul 2026 pada 10.09
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
-- Database: `stockbarang2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `login`
--

CREATE TABLE `login` (
  `iduser` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `level` enum('admin','user_cabang') DEFAULT 'user_cabang',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cabang_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `login`
--

INSERT INTO `login` (`iduser`, `email`, `password`, `nama_lengkap`, `level`, `created_at`, `cabang_id`) VALUES
(1, 'admin@gmail.com', 'Adminsigma', 'Administrator', 'admin', '2026-04-04 12:19:56', NULL),
(2, 'batuah@gmail.com', 'Batuahsigma', 'User Cabang Batuah', 'user_cabang', '2026-04-21 13:51:05', 7),
(6, 'palaran@gmail.com', 'Palaransigma', 'User Cabang Palaran', 'user_cabang', '2026-06-24 14:07:27', 5),
(7, 'loajanan@gmail.com', 'Loajanansigma', 'User Cabang oa janan', 'user_cabang', '2026-07-05 11:55:26', 6),
(10, 'dondang@gmail.com', 'Dondangsigma', 'user cabang dondang', 'user_cabang', '2026-07-10 06:04:01', 8);

-- --------------------------------------------------------

--
-- Struktur dari tabel `material`
--

CREATE TABLE `material` (
  `id_material` int(11) NOT NULL,
  `kode_material` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `harga` decimal(15,0) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `material`
--

INSERT INTO `material` (`id_material`, `kode_material`, `nama`, `satuan`, `harga`) VALUES
(1, 'MAT-001', 'Suspension Clamp ( 50 - 70 sqmm )', 'Bh', 115185),
(2, 'MAT-002', 'Material Small Angle / Suspension Assembly ( 50 - 70 sqmm ) ; Non Steinles Steel Strip + Yorke', 'Bh', 115185),
(3, 'MAT-003', 'Material Fixed Dead End Assembly Assembly ( 50 - 70 sqmm ) ; Non Steinles Steel Strip + Yorke', 'Bh', 140385),
(4, 'MAT-004', 'Material Adjustable Dead End Assembly ( 50 - 70 sqmm ) ; Non Steinles Steel Strip + Yorke', 'Bh', 162000),
(5, 'MAT-005', 'Steinles Steel Strip ( P 100 cm x L 20 mm x T 0,7 mm ) + Stoping Buckle / Yoke ( 2 Bh )', 'Bh', 29000),
(6, 'MAT-006', 'Konektor Tembus Kedap Air ( 1 Baut ) ; 35-50 / 10-25 sqmm', 'Bh', 27500),
(7, 'MAT-007', 'Konektor Tembus Kedap Air ( 1 Baut ) ; 70-95 / 10-25 sqmm', 'Bh', 30500),
(8, 'MAT-008', 'SKAT 4 ( 50 sqmm ) ; L1', 'Bh', 69739),
(9, 'MAT-009', 'SKAT 5 ( 70 sqmm ) ; L1', 'Bh', 75300),
(10, 'MAT-010', 'SKAT 6 ( 95 sqmm ) ; L1', 'Bh', 92813),
(11, 'MAT-011', 'SKAT 7 ( 120 sqmm ) ; L1', 'Bh', 107188),
(12, 'MAT-012', 'SKAT 8 ( 150  sqmm ) ; L1', 'Bh', 116700),
(13, 'MAT-013', 'SKAT 10 ( 240 sqmm ) ; L1', 'Bh', 165900),
(14, 'MAT-014', 'SKAT 10 ( 240 sqmm ) ; L2', 'Bh', 0),
(15, 'MAT-015', 'SKT 4 ( 50 sqmm )  ; L1; Outdoor', 'Bh', 52291),
(16, 'MAT-016', 'SKT 5 ( 70 sqmm )  ; L1 ; Outdoor', 'Bh', 61500),
(17, 'MAT-017', 'SKT 6 ( 95  sqmm )  ; L1 ; Outdoor', 'Bh', 85200),
(18, 'MAT-018', 'SKT 7 ( 120  sqmm )  ; L1 ; Outdoor', 'Bh', 95000),
(19, 'MAT-019', 'SKT 8 ( 150  sqmm )  ; L1 ; Outdoor', 'Bh', 123600),
(20, 'MAT-020', 'SAT 4T5 ( 50 sqmm Al - 70 sqmm Cu )', 'Bh', 71400),
(21, 'MAT-021', 'SAT 5T5 ( 70 sqmm Al - 70 sqmm Cu )', 'Bh', 75300),
(22, 'MAT-022', 'SAT 7T7 ( 70 sqmm Al - 150 sqmm Cu )', 'Bh', 103646),
(23, 'MAT-023', 'SAT 8T5 ( 150 sqmm Al - 70 sqmm Cu )', 'Bh', 135000),
(24, 'MAT-024', 'CCO 1T1 ( 10/16 sqmm - 10/16 sqmm )', 'Bh', 7100),
(25, 'MAT-025', 'CCO 5T1 ( 35/70 sqmm - 10/16 sqmm )', 'Bh', 18958),
(26, 'MAT-026', 'CCO 5T3 ( 35/70 sqmm - 16/35 sqmm )', 'Bh', 20937),
(27, 'MAT-027', 'CCO 5T5 ( 35/70 sqmm - 35/70 sqmm )', 'Bh', 21979),
(28, 'MAT-028', 'CCO 8T5 ( 70/150 sqmm - 35/70 sqmm )', 'Bh', 27187),
(29, 'MAT-029', 'CCO 5T8 ( 70/150 sqmm - 70/150 sqmm )', 'Bh', 29479),
(30, 'MAT-030', 'CCO 10T5 ( 120/240 sqmm - 35/70 sqmm )', 'Bh', 43229),
(31, 'MAT-031', 'CCO 10T7 ( 120/240 sqmm - 70/150 sqmm )', 'Bh', 0),
(32, 'MAT-032', 'CCO 10T8 ( 120/240 sqmm - 70/150 sqmm )', 'Bh', 44375),
(33, 'MAT-033', 'CCO 7T8 ( 120/150 sqmm - 120/150 sqmm )', 'Bh', 0),
(34, 'MAT-034', 'CCO 8T8 ( 150/150 sqmm - 150/150 sqmm )', 'Bh', 0),
(35, 'MAT-035', 'CCO 8T10 ( 150/240 sqmm - 150/240 sqmm )', 'Bh', 0),
(36, 'MAT-036', 'CCO 10T10 ( 240/240 sqmm - 240/240 sqmm )', 'Bh', 0),
(37, 'MAT-037', 'SPAA 4 ( 50 sqmm )', 'Bh', 54396),
(38, 'MAT-038', 'SPAA 5 ( 70 sqmm )', 'Bh', 77300),
(39, 'MAT-039', 'SPAA 6 ( 90 sqmm )', 'Bh', 103438),
(40, 'MAT-040', 'SPAA 8 ( 150 sqmm )', 'Bh', 136400),
(41, 'MAT-041', 'SPAA 10 ( 240 sqmm )', 'Bh', 175800),
(42, 'MAT-042', 'SAA 4T3 ( 50 - 35 sqmm Al )', 'Bh', 31500),
(43, 'MAT-043', 'SAA 4T4 ( 50 - 50 sqmm Al )', 'Bh', 27000),
(44, 'MAT-044', 'SAA 5T3 ( 70 - 35 sqmm Al )', 'Bh', 36500),
(45, 'MAT-045', 'SAA 5T4 ( 70 - 50 sqmm Al )', 'Bh', 36500),
(46, 'MAT-046', 'SAA 5T5 ( 70 - 70 sqmm Al )', 'Bh', 36500),
(47, 'MAT-047', 'SAA 8T8 ( 150 - 150 sqmm Al )', 'Bh', 71400),
(48, 'MAT-048', 'SAA 10T10 ( 240 - 240 sqmm Al )', 'Bh', 98000),
(49, 'MAT-049', 'Fuse Link ; Tipe K ; 2A', 'Bh', 59800),
(50, 'MAT-050', 'Fuse Link ; Tipe K ; 3 A', 'Bh', 59800),
(51, 'MAT-051', 'Fuse Link ; Tipe K ; 4 A', 'Bh', 59800),
(52, 'MAT-052', 'Fuse Link ; Tipe K ; 5 A', 'Bh', 59800),
(53, 'MAT-053', 'Fuse Link ; Tipe K ; 6 A', 'Bh', 59800),
(54, 'MAT-054', 'Fuse Link ; Tipe K ; 8 A', 'Bh', 71300),
(55, 'MAT-055', 'Fuse Link ; Tipe K ; 10  A', 'Bh', 71300),
(56, 'MAT-056', 'Fuse Link ; Tipe K ; 12 A', 'Bh', 71300),
(57, 'MAT-057', 'Fuse Link ; Tipe K ; 15 A', 'Bh', 71300),
(58, 'MAT-058', 'Fuse Link ; Tipe K ; 20 A', 'Bh', 71300),
(59, 'MAT-059', 'Fuse Link ; Tipe K ; 25 A', 'Bh', 98411),
(60, 'MAT-060', 'Fuse Link ; Tipe K ; 30 A', 'Bh', 98411),
(61, 'MAT-061', 'Fuse Link ; Tipe K ; 40 A', 'Bh', 98411),
(62, 'MAT-062', 'Fuse Link ; Tipe K ; 50 A', 'Bh', 181125),
(63, 'MAT-063', 'Fuse Link ; Tipe K ; 60 A', 'Bh', 181125),
(64, 'MAT-064', 'Fuse Link ; Tipe K ; 63 A', 'Bh', 181125),
(65, 'MAT-065', 'Fuse Link ; Tipe K ; 65 A', 'Bh', 181125),
(66, 'MAT-066', 'Fuse Link ; Tipe K ; 80 A', 'Bh', 181125),
(67, 'MAT-067', 'Fuse Link ; Tipe K ; 100 A', 'Bh', 181125),
(68, 'MAT-068', 'NH / NT Fuse ; Size 00 6 A', 'Bh', 59900),
(69, 'MAT-069', 'NH / NT Fuse ; Size 00 10 A', 'Bh', 59900),
(70, 'MAT-070', 'NH / NT Fuse ; Size 00 16 A', 'Bh', 59900),
(71, 'MAT-071', 'NH / NT Fuse ; Size 00 20 A', 'Bh', 59900),
(72, 'MAT-072', 'NH / NT Fuse ; Size 00 25 A', 'Bh', 59900),
(73, 'MAT-073', 'NH / NT Fuse ; Size 00 32 A', 'Bh', 59900),
(74, 'MAT-074', 'NH / NT Fuse ; Size 00 35 A', 'Bh', 59900),
(75, 'MAT-075', 'NH / NT Fuse ; Size 00 40 A', 'Bh', 59900),
(76, 'MAT-076', 'NH / NT Fuse ; Size 00 50 A', 'Bh', 59900),
(77, 'MAT-077', 'NH / NT Fuse ; Size 00 63 A', 'Bh', 59900),
(78, 'MAT-078', 'NH / NT Fuse ; Size 00 80 A', 'Bh', 59900),
(79, 'MAT-079', 'NH / NT Fuse ; Size 00 100 A', 'Bh', 59900),
(80, 'MAT-080', 'NH / NT Fuse ; Size 00 125 A', 'Bh', 59900),
(81, 'MAT-081', 'NH / NT Fuse ; Size 00 160 A', 'Bh', 59900),
(82, 'MAT-082', 'NH / NT Fuse ; Size 0 50 A', 'Bh', 70400),
(83, 'MAT-083', 'NH / NT Fuse ; Size 0 63 A', 'Bh', 70400),
(84, 'MAT-084', 'NH / NT Fuse ; Size 0 80 A', 'Bh', 70400),
(85, 'MAT-085', 'NH / NT Fuse ; Size 0 100 A', 'Bh', 70400),
(86, 'MAT-086', 'NH / NT Fuse ; Size 0 125 A', 'Bh', 70400),
(87, 'MAT-087', 'NH / NT Fuse ; Size 0 160 A', 'Bh', 70400),
(88, 'MAT-088', 'NH / NT Fuse ; Size 1 50 A', 'Bh', 83000),
(89, 'MAT-089', 'NH / NT Fuse ; Size 1 60 A', 'Bh', 83000),
(90, 'MAT-090', 'NH / NT Fuse ; Size 1 63 A', 'Bh', 83000),
(91, 'MAT-091', 'NH / NT Fuse ; Size 1 80 A', 'Bh', 83000),
(92, 'MAT-092', 'NH / NT Fuse ; Size 1 100 A', 'Bh', 83000),
(93, 'MAT-093', 'NH / NT Fuse ; Size 1 125 A', 'Bh', 83000),
(94, 'MAT-094', 'NH / NT Fuse ; Size 1 160 A', 'Bh', 83000),
(95, 'MAT-095', 'NH / NT Fuse ; Size 1 200 A', 'Bh', 83000),
(96, 'MAT-096', 'NH / NT Fuse ; Size 1 224 A', 'Bh', 83000),
(97, 'MAT-097', 'NH / NT Fuse ; Size 1 225 A', 'Bh', 83000),
(98, 'MAT-098', 'NH / NT Fuse ; Size 1 250 A', 'Bh', 83000),
(99, 'MAT-099', 'NH / NT Fuse ; Size 2 250 A', 'Bh', 129000),
(100, 'MAT-100', 'NH / NT Fuse ; Size 2  300 A', 'Bh', 129000),
(101, 'MAT-101', 'NH / NT Fuse ; Size 2 315 A', 'Bh', 129000),
(102, 'MAT-102', 'NH / NT Fuse ; Size 2 355 A', 'Bh', 129000),
(103, 'MAT-103', 'NH / NT Fuse ; Size 2 400 A', 'Bh', 129000),
(104, 'MAT-104', 'Terminasi Indoor ( 70 - 120 Sqmm ) 1 Core ; 24 kV ; ( Tipe coldshrink ) ; 3 Set', 'Bh', 3848868),
(105, 'MAT-105', 'Terminasi Indoor ( 150-300 Sqmm ) 1 Core ; 24 kV ; ( Tipe coldshrink ) ; 3 Set', 'Bh', 4320312),
(106, 'MAT-106', 'Terminasi Outdoor ( 70 - 120 Sqmm ) 1 Core ; 24 kV ; ( Tipe coldshrink ) ; 3 Set', 'Bh', 4218750),
(107, 'MAT-107', 'Terminasi Outdoor ( 150 - 300 Sqmm ) 1 Core ; 24 kV ; ( Tipe coldshrink ) ; 3 Set', 'Bh', 4458833),
(108, 'MAT-108', 'Terminasi Indoor ( 70 - 120 Sqmm ) 3 Core ; 24 kV ; ( Tipe Heatshrink )', 'Bh', 3645833),
(109, 'MAT-109', 'Terminasi Indoor ( 120 - 185 Sqmm ) 3 Core ; 24 kV ; ( Tipe Coldshrink )', 'Bh', 0),
(110, 'MAT-110', 'Terminasi Indoor ( 150 - 300 Sqmm ) 3 Core ; 24 kV ; ( Tipe Heatshrink )', 'Bh', 4144583),
(111, 'MAT-111', 'Terminasi Outdoor ( 70 - 120 Sqmm ) 3 Core ; 24 kV ; ( Tipe Heatshrink )', 'Bh', 6324400),
(112, 'MAT-112', 'Terminasi Outdoor ( 150 - 300 Sqmm ) 3 Core ; 24 kV ; ( Tipe Heatshrink )', 'Bh', 7359200),
(113, 'MAT-113', 'Joint Bimetal Al/Cu 35 - 50', 'Bh', 64583),
(114, 'MAT-114', 'Joint Bimetal Al/Cu 50 - 70', 'Bh', 71400),
(115, 'MAT-115', 'Joint Bimetal Al/Cu 70 - 70', 'Bh', 75300),
(116, 'MAT-116', 'Joint Bimetal Al/Cu 70 - 95', 'Bh', 85312),
(117, 'MAT-117', 'Joint Bimetal Al/Cu 70 - 120', 'Bh', 80699),
(118, 'MAT-118', 'Joint Bimetal Al/Cu 120 - 70', 'Bh', 0),
(119, 'MAT-119', 'Joint Bimetal Al/Cu 120 - 240', 'Bh', 139205),
(120, 'MAT-120', 'Joint Bimetal Al/Cu 150 - 150', 'Bh', 0),
(121, 'MAT-121', 'Fuse Holder NT1', 'Bh', 160340),
(122, 'MAT-122', 'Fuse Holder NT2', 'Bh', 177870),
(123, 'MAT-123', 'Wall Saklar 400 A', 'Bh', 3477153),
(124, 'MAT-124', 'Wall Saklar 630 A', 'Bh', 4582608),
(125, 'MAT-125', 'Service Wedge Clamp 616 ( 1 Phase ) 6-10-16', 'Bh', 11000),
(126, 'MAT-126', 'Service Wedge Clamp 625 (3 Phase) 6.10,16,25', 'Bh', 11000),
(127, 'MAT-127', 'Tekep Isolator Tumpu ( Polimer ) ; Si penjol', 'Bh', 103500),
(128, 'MAT-128', 'Tekep Isolator Tumpu ( Polimer ) ; Silicon', 'Bh', 132825),
(129, 'MAT-129', 'Cover Busing Silicon/Polymer', 'Bh', 138863),
(130, 'MAT-130', 'Cover FCO Atas Silicon/Polymer', 'Bh', 152145),
(131, 'MAT-131', 'Cover FCO Bawah Silicon/Polymer', 'Bh', 106864),
(132, 'MAT-132', 'Cover LA Silicon/Polymer Corong 1', 'Bh', 104811),
(133, 'MAT-133', 'Cover LA Silicon/Polymer Corong 2', 'Bh', 96600),
(134, 'MAT-134', 'Current limiting device (CLD)', 'Bh', 2656500),
(135, 'MAT-135', 'Multi Chamber Arester (MCA)', 'Bh', 2354625),
(136, 'MAT-136', 'FRP Cross Arm', 'Bh', 301875),
(137, 'MAT-137', 'Tree Guard (70-150 sqmm) PLP', 'Bh', 289800),
(138, 'MAT-138', 'Tree Guard (240 sqmm)', 'Bh', 301875),
(139, 'MAT-139', 'Ground rod 2.75 meter diameter 5/8 inch', 'Bh', 1800000),
(140, 'MAT-140', 'Treckschoor lengkap (wire 50mm pipa 2 meter galvanis)', 'Bh', 1199500),
(141, 'MAT-141', 'Traves UNP 10x200 cm (Galvanis lengkap Arm Tie)', 'Bh', 860200),
(142, 'MAT-142', 'Penghalang panjat (tiang besi)', 'Bh', 112875),
(143, 'MAT-143', 'Penghalang panjat (tiang beton)', 'Bh', 112875),
(144, 'MAT-144', 'Papan tanda bahaya', 'Bh', 102480),
(145, 'MAT-145', 'Single Top Ties/ Side Ties (70 mm)', 'Bh', 145021),
(146, 'MAT-146', 'Single Top Ties/ Side Ties (150 mm)', 'Bh', 163496),
(147, 'MAT-147', 'Single Top Ties/ Side Ties (240 mm)', 'Bh', 163496),
(148, 'MAT-148', 'Bending Wire', 'Roll (100m)', 0),
(149, 'DDA-009', 'Meja', 'Batang', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(1) DEFAULT 1,
  `nama_perusahaan` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_perusahaan`, `alamat`, `telepon`, `updated_at`) VALUES
(1, 'PLN ULP SAMARINDA SEBRANG', 'Jl. Bung Tomo, Baqa Kec. Samarinda Seberang, Kotta Samarinda, Kalimantan Timur 75131', '123', '2026-06-10 13:39:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `permintaan`
--

CREATE TABLE `permintaan` (
  `id_permintaan` int(11) NOT NULL,
  `no_permintaan` varchar(50) DEFAULT NULL,
  `id_tujuan` int(11) NOT NULL,
  `tanggal_permintaan` date NOT NULL,
  `status` enum('pending','disetujui','dikirim','ditolak','selesai','perlu_perbaikan') DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `permintaan`
--

INSERT INTO `permintaan` (`id_permintaan`, `no_permintaan`, `id_tujuan`, `tanggal_permintaan`, `status`, `catatan`, `created_at`) VALUES
(38, 'REQ/202607/001', 5, '2026-07-14', 'pending', '', '2026-07-14 06:50:21'),
(40, 'REQ/202607/002', 6, '2026-07-14', 'disetujui', '', '2026-07-14 06:54:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `permintaan_detail`
--

CREATE TABLE `permintaan_detail` (
  `id_detail` int(11) NOT NULL,
  `id_permintaan` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `permintaan_detail`
--

INSERT INTO `permintaan_detail` (`id_detail`, `id_permintaan`, `id_material`, `jumlah`) VALUES
(36, 38, 148, 30),
(38, 40, 148, 30);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_gudang`
--

CREATE TABLE `stock_gudang` (
  `id_stock` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `jumlah` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `stock_gudang`
--

INSERT INTO `stock_gudang` (`id_stock`, `id_material`, `jumlah`) VALUES
(149, 2, 5),
(150, 3, 1),
(151, 35, 1),
(152, 49, 1),
(153, 50, 1),
(154, 54, 3),
(155, 55, 1),
(156, 56, 1),
(157, 98, 0),
(158, 100, 0),
(159, 129, 10),
(160, 130, 3),
(161, 131, 3),
(162, 133, 10),
(163, 140, 2),
(164, 141, 10),
(165, 148, 20),
(166, 1, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_tujuan`
--

CREATE TABLE `stock_tujuan` (
  `id_stock_tujuan` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_tujuan` int(11) NOT NULL,
  `jumlah` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `stock_tujuan`
--

INSERT INTO `stock_tujuan` (`id_stock_tujuan`, `id_material`, `id_tujuan`, `jumlah`) VALUES
(12, 2, 1, 0),
(13, 3, 1, 0),
(14, 129, 1, 20),
(15, 49, 3, 1),
(16, 54, 3, 1),
(17, 55, 4, 1),
(18, 98, 4, 2),
(19, 100, 4, 1),
(20, 148, 7, 0),
(21, 2, 3, 0),
(22, 2, 7, 2),
(23, 35, 7, 1),
(24, 54, 7, 0),
(25, 129, 7, 0),
(26, 133, 7, 0),
(27, 129, 5, 3),
(28, 133, 5, 27),
(29, 141, 5, 7),
(30, 54, 5, 0),
(31, 129, 3, 0),
(32, 1, 1, 0),
(33, 49, 7, 0),
(34, 1, 6, 15),
(35, 35, 5, 0),
(36, 129, 6, 25),
(37, 1, 2, 0),
(38, 133, 6, 40),
(39, 1, 5, 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `surat_jalan`
--

CREATE TABLE `surat_jalan` (
  `id_surat` int(11) NOT NULL,
  `no_surat` varchar(50) NOT NULL,
  `id_permintaan` int(11) NOT NULL,
  `id_tujuan` int(11) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('draft','dikirim','diterima') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `surat_jalan`
--

INSERT INTO `surat_jalan` (`id_surat`, `no_surat`, `id_permintaan`, `id_tujuan`, `tanggal_surat`, `tanggal_kirim`, `catatan`, `status`, `created_at`) VALUES
(13, 'SJ/001/VII/2026', 14, 5, '2026-07-05', '2026-07-05', '', 'dikirim', '2026-07-05 10:14:44'),
(14, 'SJ/002/VII/2026', 15, 5, '2026-07-05', '2026-07-05', '', 'dikirim', '2026-07-05 10:28:49'),
(15, 'SJ/003/VII/2026', 20, 5, '2026-07-06', '2026-07-06', '', 'dikirim', '2026-07-06 14:38:44'),
(16, 'SJ/004/VII/2026', 22, 5, '2026-07-06', '2026-07-06', '', 'dikirim', '2026-07-06 14:57:13'),
(17, 'SJ/005/VII/2026', 25, 5, '2026-07-06', '2026-07-06', '', 'dikirim', '2026-07-06 15:04:37'),
(18, 'SJ/006/VII/2026', 27, 6, '2026-07-10', '2026-07-10', '', 'dikirim', '2026-07-10 02:29:25'),
(19, 'SJ/007/VII/2026', 28, 6, '2026-07-10', '2026-07-10', '', 'dikirim', '2026-07-10 05:11:25'),
(20, 'SJ/008/VII/2026', 30, 6, '2026-07-10', '2026-07-10', '', 'dikirim', '2026-07-10 05:53:00'),
(21, 'SJ/009/VII/2026', 31, 6, '2026-07-10', '2026-07-10', '', 'dikirim', '2026-07-10 05:54:49'),
(22, 'SJ/010/VII/2026', 33, 6, '2026-07-10', '2026-07-10', '', 'dikirim', '2026-07-10 06:07:55'),
(23, 'SJ/011/VII/2026', 32, 5, '2026-07-10', '2026-07-10', '', 'dikirim', '2026-07-10 06:08:14'),
(24, 'SJ/012/VII/2026', 34, 6, '2026-07-10', '2026-07-10', '', 'dikirim', '2026-07-10 06:19:47'),
(25, 'SJ/013/VII/2026', 35, 5, '2026-07-10', '2026-07-10', '', 'dikirim', '2026-07-10 06:20:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `surat_jalan_detail`
--

CREATE TABLE `surat_jalan_detail` (
  `id_detail` int(11) NOT NULL,
  `id_surat` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `jumlah_kirim` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `surat_jalan_detail`
--

INSERT INTO `surat_jalan_detail` (`id_detail`, `id_surat`, `id_material`, `jumlah_kirim`) VALUES
(11, 13, 129, 3),
(12, 14, 133, 7),
(13, 15, 141, 7),
(14, 16, 54, 1),
(15, 17, 129, 5),
(16, 18, 1, 5),
(17, 19, 129, 5),
(18, 20, 133, 25),
(19, 21, 129, 25),
(20, 22, 1, 15),
(21, 23, 1, 10),
(22, 24, 133, 40),
(23, 25, 133, 20);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_keluar_dari_cabang`
--

CREATE TABLE `transaksi_keluar_dari_cabang` (
  `id_transaksi` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_tujuan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `qty` int(11) NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_keluar_dari_cabang`
--

INSERT INTO `transaksi_keluar_dari_cabang` (`id_transaksi`, `id_material`, `id_tujuan`, `tanggal`, `qty`, `keterangan`, `created_at`) VALUES
(13, 1, 2, '2026-07-10', 10, '', '2026-07-10 05:50:17'),
(14, 129, 6, '2026-07-10', 5, '', '2026-07-10 05:55:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_keluar_gudang`
--

CREATE TABLE `transaksi_keluar_gudang` (
  `id_transaksi` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_tujuan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_keluar_gudang`
--

INSERT INTO `transaksi_keluar_gudang` (`id_transaksi`, `id_material`, `id_tujuan`, `tanggal`, `qty`) VALUES
(6, 2, 1, '2025-02-06', 2),
(7, 3, 1, '2025-02-25', 1),
(8, 35, 1, '2025-02-27', 2),
(9, 54, 1, '2025-02-24', 3),
(10, 129, 1, '2025-02-03', 6),
(11, 129, 1, '2025-02-06', 3),
(12, 129, 1, '2025-02-07', 15),
(13, 129, 1, '2025-02-08', 3),
(14, 129, 1, '2025-02-14', 9),
(15, 129, 1, '2025-02-21', 21),
(16, 129, 1, '2025-02-24', 3),
(17, 129, 1, '2025-02-27', 3),
(18, 130, 1, '2025-02-03', 3),
(19, 131, 1, '2025-02-03', 3),
(20, 133, 1, '2025-02-03', 3),
(21, 133, 1, '2025-02-06', 3),
(22, 133, 1, '2025-02-07', 12),
(23, 133, 1, '2025-02-14', 6),
(24, 133, 1, '2025-02-21', 18),
(25, 133, 1, '2025-02-27', 15),
(26, 140, 1, '2025-02-22', 2),
(27, 141, 1, '2025-02-06', 2),
(28, 141, 1, '2025-02-12', 2),
(29, 141, 1, '2025-02-20', 9),
(30, 141, 1, '2025-02-22', 2),
(31, 141, 1, '2025-02-25', 2),
(32, 148, 1, '2025-02-03', 15),
(33, 148, 1, '2025-02-06', 6),
(34, 148, 1, '2025-02-12', 3),
(35, 148, 1, '2025-02-20', 24),
(36, 148, 1, '2025-02-24', 1),
(37, 148, 1, '2025-02-25', 3),
(38, 49, 3, '2025-02-06', 1),
(39, 54, 3, '2025-02-20', 1),
(40, 55, 4, '2025-02-02', 1),
(41, 98, 4, '2025-02-02', 1),
(42, 98, 4, '2025-02-28', 1),
(43, 100, 4, '2025-02-27', 1),
(44, 55, 6, '2025-02-06', 1),
(45, 56, 6, '2025-02-21', 1),
(46, 62, 6, '2025-02-16', 1),
(47, 55, 7, '2025-02-12', 1),
(48, 128, 1, '2025-03-20', 2),
(49, 129, 1, '2025-03-05', 3),
(50, 129, 1, '2025-03-06', 3),
(51, 129, 1, '2025-03-07', 3),
(52, 129, 1, '2025-03-11', 3),
(53, 129, 1, '2025-03-14', 3),
(54, 129, 1, '2025-03-17', 3),
(55, 129, 1, '2025-03-18', 3),
(56, 129, 1, '2025-03-19', 3),
(57, 129, 1, '2025-03-21', 3),
(58, 133, 1, '2025-03-05', 3),
(59, 133, 1, '2025-03-06', 3),
(60, 133, 1, '2025-03-07', 3),
(61, 133, 1, '2025-03-11', 3),
(62, 133, 1, '2025-03-14', 3),
(63, 133, 1, '2025-03-17', 3),
(64, 133, 1, '2025-03-18', 3),
(65, 133, 1, '2025-03-19', 3),
(66, 140, 1, '2025-03-17', 1),
(67, 141, 1, '2025-03-20', 1),
(68, 143, 1, '2025-03-19', 2),
(69, 148, 1, '2025-03-03', 21),
(70, 148, 1, '2025-03-20', 3),
(71, 50, 3, '2025-03-02', 1),
(72, 52, 3, '2025-03-12', 1),
(73, 52, 3, '2025-03-14', 1),
(74, 54, 3, '2025-03-09', 1),
(75, 55, 3, '2025-03-08', 1),
(76, 99, 3, '2025-03-12', 1),
(77, 100, 3, '2025-03-11', 1),
(78, 100, 3, '2025-03-18', 1),
(79, 50, 4, '2025-03-09', 1),
(80, 98, 4, '2025-03-14', 1),
(81, 100, 4, '2025-03-14', 1),
(82, 50, 6, '2025-03-01', 1),
(83, 56, 6, '2025-03-21', 1),
(84, 3, 1, '2025-04-01', 4),
(85, 27, 1, '2025-04-01', 12),
(86, 33, 1, '2025-04-01', 3),
(87, 81, 1, '2025-04-01', 2),
(88, 95, 1, '2025-04-01', 1),
(89, 121, 1, '2025-04-01', 3),
(90, 128, 1, '2025-04-01', 12),
(91, 129, 1, '2025-04-01', 30),
(92, 133, 1, '2025-04-01', 51),
(93, 140, 1, '2025-04-01', 5),
(94, 141, 1, '2025-04-01', 14),
(95, 143, 1, '2025-04-01', 34),
(96, 51, 3, '2025-04-01', 1),
(97, 51, 3, '2025-04-09', 1),
(98, 53, 3, '2025-04-22', 1),
(99, 98, 4, '2025-04-22', 1),
(100, 100, 4, '2025-04-08', 1),
(101, 103, 4, '2025-04-17', 1),
(102, 49, 6, '2025-04-10', 1),
(103, 51, 6, '2025-04-10', 1),
(104, 52, 6, '2025-04-26', 1),
(105, 56, 6, '2025-04-24', 1),
(106, 50, 9, '2025-04-07', 1),
(107, 3, 1, '2025-05-01', 6),
(108, 98, 1, '2025-05-01', 3),
(109, 129, 1, '2025-05-01', 30),
(110, 130, 1, '2025-05-01', 6),
(111, 131, 1, '2025-05-01', 6),
(112, 133, 1, '2025-05-01', 9),
(113, 139, 1, '2025-05-01', 14),
(114, 140, 1, '2025-05-01', 12),
(115, 141, 1, '2025-05-01', 4),
(116, 143, 1, '2025-05-01', 5),
(117, 148, 1, '2025-05-01', 3),
(118, 79, 2, '2025-05-16', 1),
(119, 49, 3, '2025-05-11', 1),
(120, 49, 3, '2025-05-18', 1),
(121, 50, 3, '2025-05-06', 3),
(122, 56, 3, '2025-05-06', 1),
(123, 56, 3, '2025-05-18', 2),
(124, 57, 3, '2025-05-09', 1),
(125, 59, 3, '2025-05-09', 1),
(126, 70, 3, '2025-05-06', 1),
(127, 80, 3, '2025-05-11', 1),
(128, 92, 3, '2025-05-05', 1),
(129, 95, 3, '2025-05-22', 1),
(130, 98, 3, '2025-05-03', 1),
(131, 100, 3, '2025-05-04', 1),
(132, 103, 3, '2025-05-16', 1),
(133, 49, 4, '2025-05-09', 1),
(134, 54, 4, '2025-05-29', 1),
(135, 95, 4, '2025-05-05', 1),
(136, 95, 4, '2025-05-16', 1),
(137, 100, 4, '2025-05-25', 1),
(138, 103, 4, '2025-05-18', 1),
(139, 103, 4, '2025-05-19', 1),
(140, 52, 5, '2025-05-03', 1),
(141, 59, 5, '2025-05-01', 1),
(142, 3, 1, '2025-06-01', 20),
(143, 94, 1, '2025-06-01', 3),
(144, 128, 1, '2025-06-01', 45),
(145, 129, 1, '2025-06-01', 3),
(146, 130, 1, '2025-06-01', 6),
(147, 131, 1, '2025-06-01', 12),
(148, 133, 1, '2025-06-01', 6),
(149, 139, 1, '2025-06-01', 35),
(150, 140, 1, '2025-06-01', 8),
(151, 141, 1, '2025-06-01', 6),
(152, 143, 1, '2025-06-01', 51),
(153, 51, 3, '2025-06-02', 1),
(154, 51, 3, '2025-06-08', 1),
(155, 52, 3, '2025-06-08', 1),
(156, 55, 3, '2025-06-28', 1),
(157, 59, 3, '2025-06-02', 1),
(158, 61, 3, '2025-06-07', 1),
(159, 49, 4, '2025-06-28', 1),
(160, 50, 4, '2025-06-18', 4),
(161, 51, 4, '2025-06-28', 1),
(162, 57, 4, '2025-06-10', 1),
(163, 61, 4, '2025-06-02', 1),
(164, 80, 4, '2025-06-16', 1),
(165, 100, 4, '2025-06-08', 1),
(166, 51, 5, '2025-06-23', 1),
(167, 95, 5, '2025-06-04', 1),
(168, 49, 7, '2025-06-28', 1),
(169, 54, 9, '2025-06-27', 1),
(171, 129, 1, '2026-06-01', 20),
(184, 2, 7, '2026-06-14', 1),
(185, 35, 7, '2026-06-14', 1),
(188, 2, 7, '2026-06-24', 1),
(192, 129, 5, '2026-07-05', 3),
(193, 133, 5, '2026-07-05', 7),
(194, 141, 5, '2026-07-06', 7),
(200, 129, 6, '2026-07-10', 5),
(201, 1, 2, '2026-07-10', 10),
(203, 129, 6, '2026-07-10', 25),
(204, 1, 6, '2026-07-10', 15),
(205, 133, 6, '2026-07-10', 40),
(206, 1, 5, '2026-07-14', 10),
(207, 133, 5, '2026-07-14', 20);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_keluar_tujuan`
--

CREATE TABLE `transaksi_keluar_tujuan` (
  `id_transaksi` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `dari_tujuan` int(11) NOT NULL,
  `ke_tujuan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `qty` int(11) NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_masuk_gudang`
--

CREATE TABLE `transaksi_masuk_gudang` (
  `id_transaksi` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `qty` int(11) NOT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_masuk_gudang`
--

INSERT INTO `transaksi_masuk_gudang` (`id_transaksi`, `id_material`, `tanggal`, `qty`, `supplier`, `keterangan`) VALUES
(1, 141, '2025-02-17', 5, 'Pusat', NULL),
(2, 8, '2025-04-11', 44, 'Pusat', NULL),
(3, 9, '2025-04-11', 18, 'Pusat', NULL),
(4, 16, '2025-04-11', 30, 'Pusat', NULL),
(5, 49, '2025-04-11', 100, 'Pusat', NULL),
(6, 50, '2025-04-11', 20, 'Pusat', NULL),
(7, 51, '2025-04-11', 100, 'Pusat', NULL),
(8, 52, '2025-04-11', 40, 'Pusat', NULL),
(9, 53, '2025-04-11', 37, 'Pusat', NULL),
(10, 54, '2025-04-11', 50, 'Pusat', NULL),
(11, 58, '2025-04-11', 20, 'Pusat', NULL),
(12, 59, '2025-04-11', 20, 'Pusat', NULL),
(13, 134, '2025-04-11', 50, 'Pusat', NULL),
(14, 140, '2025-04-18', 5, 'Pusat', NULL),
(15, 27, '2025-04-21', 100, 'Pusat', NULL),
(16, 28, '2025-04-21', 40, 'Pusat', NULL),
(17, 35, '2025-04-21', 10, 'Pusat', NULL),
(18, 132, '2025-04-21', 45, 'Pusat', NULL),
(19, 133, '2025-04-21', 42, 'Pusat', NULL),
(20, 136, '2025-04-21', 13, 'Pusat', NULL),
(21, 141, '2025-04-21', 13, 'Pusat', NULL),
(22, 24, '2025-04-28', 150, 'Pusat', NULL),
(23, 42, '2025-04-28', 20, 'Pusat', NULL),
(24, 148, '2025-04-28', 1, 'Pusat', NULL),
(25, 5, '2025-05-07', 25, 'Pusat', NULL),
(26, 102, '2025-05-07', 3, 'Pusat', NULL),
(27, 103, '2025-05-07', 8, 'Pusat', NULL),
(28, 112, '2025-05-07', 1, 'Pusat', NULL),
(29, 128, '2025-05-07', 30, 'Pusat', NULL),
(30, 50, '2025-05-15', 35, 'Pusat', NULL),
(31, 51, '2025-05-15', 75, 'Pusat', NULL),
(32, 52, '2025-05-15', 10, 'Pusat', NULL),
(33, 54, '2025-05-15', 25, 'Pusat', NULL),
(34, 56, '2025-05-15', 50, 'Pusat', NULL),
(35, 57, '2025-05-15', 50, 'Pusat', NULL),
(36, 148, '2025-05-15', 1, 'Pusat', NULL),
(37, 38, '2025-05-23', 35, 'Pusat', NULL),
(38, 128, '2025-05-23', 46, 'Pusat', NULL),
(39, 133, '2025-05-23', 36, 'Pusat', NULL),
(40, 8, '2025-05-26', 24, 'Pusat', NULL),
(41, 10, '2025-05-26', 30, 'Pusat', NULL),
(42, 92, '2025-05-26', 10, 'Pusat', NULL),
(43, 95, '2025-05-26', 10, 'Pusat', NULL),
(44, 140, '2025-05-28', 4, 'Pusat', NULL),
(45, 2, '2025-05-31', 10, 'Pusat', NULL),
(46, 3, '2025-05-31', 25, 'Pusat', NULL),
(47, 40, '2025-05-31', 17, 'Pusat', NULL),
(48, 43, '2025-05-31', 22, 'Pusat', NULL),
(49, 45, '2025-05-31', 16, 'Pusat', NULL),
(50, 58, '2025-05-31', 20, 'Pusat', NULL),
(51, 59, '2025-05-31', 3, 'Pusat', NULL),
(52, 60, '2025-05-31', 4, 'Pusat', NULL),
(53, 61, '2025-05-31', 10, 'Pusat', NULL),
(54, 64, '2025-05-31', 5, 'Pusat', NULL),
(55, 92, '2025-05-31', 25, 'Pusat', NULL),
(56, 94, '2025-05-31', 5, 'Pusat', NULL),
(57, 54, '2025-06-03', 10, 'Pusat', NULL),
(58, 56, '2025-06-03', 16, 'Pusat', NULL),
(59, 57, '2025-06-03', 20, 'Pusat', NULL),
(60, 58, '2025-06-03', 20, 'Pusat', NULL),
(61, 59, '2025-06-03', 20, 'Pusat', NULL),
(62, 60, '2025-06-03', 20, 'Pusat', NULL),
(63, 139, '2025-06-03', 15, 'Pusat', NULL),
(64, 139, '2025-06-04', 15, 'Pusat', NULL),
(65, 9, '2025-06-10', 24, 'Pusat', NULL),
(66, 21, '2025-06-10', 160, 'Pusat', NULL),
(67, 23, '2025-06-10', 50, 'Pusat', NULL),
(68, 34, '2025-06-10', 30, 'Pusat', NULL),
(69, 35, '2025-06-10', 10, 'Pusat', NULL),
(70, 38, '2025-06-10', 30, 'Pusat', NULL),
(71, 40, '2025-06-10', 17, 'Pusat', NULL),
(72, 44, '2025-06-10', 40, 'Pusat', NULL),
(73, 92, '2025-06-10', 30, 'Pusat', NULL),
(74, 93, '2025-06-10', 20, 'Pusat', NULL),
(75, 94, '2025-06-10', 30, 'Pusat', NULL),
(76, 95, '2025-06-10', 30, 'Pusat', NULL),
(77, 96, '2025-06-10', 30, 'Pusat', NULL),
(78, 99, '2025-06-10', 30, 'Pusat', NULL),
(79, 100, '2025-06-10', 15, 'Pusat', NULL),
(80, 103, '2025-06-10', 30, 'Pusat', NULL),
(81, 113, '2025-06-10', 25, 'Pusat', NULL),
(82, 126, '2025-06-10', 100, 'Pusat', NULL),
(83, 136, '2025-06-24', 10, 'Pusat', NULL),
(84, 139, '2025-06-24', 10, 'Pusat', NULL),
(85, 141, '2025-06-24', 10, 'Pusat', NULL),
(89, 2, '2026-07-10', 10, 'Pusat', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_masuk_tujuan`
--

CREATE TABLE `transaksi_masuk_tujuan` (
  `id_transaksi` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `id_tujuan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `qty` int(11) NOT NULL,
  `sumber` varchar(50) DEFAULT 'Gudang',
  `asal_transfer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_masuk_tujuan`
--

INSERT INTO `transaksi_masuk_tujuan` (`id_transaksi`, `id_material`, `id_tujuan`, `tanggal`, `qty`, `sumber`, `asal_transfer`) VALUES
(6, 49, 3, '2025-02-06', 1, 'Gudang', NULL),
(7, 54, 3, '2025-02-20', 1, 'Gudang', NULL),
(8, 55, 4, '2025-02-02', 1, 'Gudang', NULL),
(9, 98, 4, '2025-02-02', 1, 'Gudang', NULL),
(10, 98, 4, '2025-02-28', 1, 'Gudang', NULL),
(11, 100, 4, '2025-02-27', 1, 'Gudang', NULL),
(13, 129, 1, '2026-06-01', 20, 'Gudang', NULL),
(26, 2, 7, '2026-06-14', 1, 'Permintaan', NULL),
(27, 35, 7, '2026-06-14', 1, 'Permintaan', NULL),
(30, 2, 7, '2026-06-24', 1, 'Permintaan', NULL),
(34, 129, 5, '2026-07-05', 3, 'Permintaan', NULL),
(35, 133, 5, '2026-07-05', 7, 'Permintaan', NULL),
(36, 141, 5, '2026-07-06', 7, 'Permintaan', NULL),
(45, 129, 6, '2026-07-10', 5, 'Permintaan', NULL),
(46, 1, 2, '2026-07-10', 10, 'Gudang', NULL),
(48, 129, 6, '2026-07-10', 25, 'Permintaan', NULL),
(50, 1, 6, '2026-07-10', 15, 'Permintaan', NULL),
(51, 133, 6, '2026-07-10', 40, 'Permintaan', NULL),
(52, 1, 5, '2026-07-14', 10, 'Permintaan', NULL),
(53, 133, 5, '2026-07-14', 20, 'Permintaan', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tujuan`
--

CREATE TABLE `tujuan` (
  `id_tujuan` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tujuan`
--

INSERT INTO `tujuan` (`id_tujuan`, `nama`) VALUES
(1, 'Out - Har'),
(2, 'Out - PRC'),
(3, 'Out - DGA 1'),
(4, 'Out - DGA 2'),
(5, 'Out - DGA Palaran'),
(6, 'Out - DGA Loa Janan'),
(7, 'Out - Batuah'),
(8, 'Out - Dondang'),
(9, 'Out - Sanga - Sanga');

-- --------------------------------------------------------

--
-- Struktur dari tabel `verifikasi_penerimaan`
--

CREATE TABLE `verifikasi_penerimaan` (
  `id_verifikasi` int(11) NOT NULL,
  `id_permintaan` int(11) NOT NULL,
  `id_tujuan` int(11) NOT NULL,
  `tanggal_terima` date NOT NULL,
  `jumlah_diterima` int(11) NOT NULL,
  `status_verifikasi` enum('sesuai','tidak_sesuai','kurang','lebih') DEFAULT 'sesuai',
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `verifikasi_penerimaan`
--

INSERT INTO `verifikasi_penerimaan` (`id_verifikasi`, `id_permintaan`, `id_tujuan`, `tanggal_terima`, `jumlah_diterima`, `status_verifikasi`, `keterangan`) VALUES
(5, 6, 7, '2026-06-24', 1, 'kurang', ''),
(6, 7, 7, '2026-06-24', 3, 'tidak_sesuai', 'barangnya rusak'),
(7, 9, 7, '2026-07-05', 3, 'tidak_sesuai', ''),
(10, 12, 7, '2026-07-05', 2, 'kurang', 'kurang 5'),
(11, 14, 5, '2026-07-05', 3, 'sesuai', ''),
(12, 15, 5, '2026-07-05', 7, 'sesuai', ''),
(13, 20, 5, '2026-07-06', 7, 'sesuai', ''),
(14, 22, 5, '2026-07-06', 2, 'lebih', ''),
(15, 25, 5, '2026-07-06', 6, 'lebih', ''),
(16, 27, 6, '2026-07-10', 4, 'kurang', ''),
(17, 28, 6, '2026-07-10', 5, 'sesuai', ''),
(18, 30, 6, '2026-07-10', 20, 'kurang', ''),
(19, 31, 6, '2026-07-10', 25, 'sesuai', '');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`iduser`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_login_tujuan` (`cabang_id`);

--
-- Indeks untuk tabel `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id_material`),
  ADD UNIQUE KEY `kode_material` (`kode_material`);

--
-- Indeks untuk tabel `permintaan`
--
ALTER TABLE `permintaan`
  ADD PRIMARY KEY (`id_permintaan`),
  ADD KEY `fk_permintaan_tujuan` (`id_tujuan`);

--
-- Indeks untuk tabel `permintaan_detail`
--
ALTER TABLE `permintaan_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_permintaan` (`id_permintaan`),
  ADD KEY `id_material` (`id_material`);

--
-- Indeks untuk tabel `stock_gudang`
--
ALTER TABLE `stock_gudang`
  ADD PRIMARY KEY (`id_stock`),
  ADD KEY `id_material` (`id_material`);

--
-- Indeks untuk tabel `stock_tujuan`
--
ALTER TABLE `stock_tujuan`
  ADD PRIMARY KEY (`id_stock_tujuan`),
  ADD UNIQUE KEY `unique_stok` (`id_material`,`id_tujuan`),
  ADD KEY `id_tujuan` (`id_tujuan`);

--
-- Indeks untuk tabel `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD PRIMARY KEY (`id_surat`),
  ADD KEY `id_permintaan` (`id_permintaan`),
  ADD KEY `id_tujuan` (`id_tujuan`);

--
-- Indeks untuk tabel `surat_jalan_detail`
--
ALTER TABLE `surat_jalan_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_surat` (`id_surat`),
  ADD KEY `id_material` (`id_material`);

--
-- Indeks untuk tabel `transaksi_keluar_dari_cabang`
--
ALTER TABLE `transaksi_keluar_dari_cabang`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_tujuan` (`id_tujuan`);

--
-- Indeks untuk tabel `transaksi_keluar_gudang`
--
ALTER TABLE `transaksi_keluar_gudang`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_tujuan` (`id_tujuan`);

--
-- Indeks untuk tabel `transaksi_keluar_tujuan`
--
ALTER TABLE `transaksi_keluar_tujuan`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `dari_tujuan` (`dari_tujuan`),
  ADD KEY `ke_tujuan` (`ke_tujuan`);

--
-- Indeks untuk tabel `transaksi_masuk_gudang`
--
ALTER TABLE `transaksi_masuk_gudang`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_material` (`id_material`);

--
-- Indeks untuk tabel `transaksi_masuk_tujuan`
--
ALTER TABLE `transaksi_masuk_tujuan`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_tujuan` (`id_tujuan`);

--
-- Indeks untuk tabel `tujuan`
--
ALTER TABLE `tujuan`
  ADD PRIMARY KEY (`id_tujuan`);

--
-- Indeks untuk tabel `verifikasi_penerimaan`
--
ALTER TABLE `verifikasi_penerimaan`
  ADD PRIMARY KEY (`id_verifikasi`),
  ADD KEY `id_permintaan` (`id_permintaan`),
  ADD KEY `id_tujuan` (`id_tujuan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `login`
--
ALTER TABLE `login`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `material`
--
ALTER TABLE `material`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT untuk tabel `permintaan`
--
ALTER TABLE `permintaan`
  MODIFY `id_permintaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `permintaan_detail`
--
ALTER TABLE `permintaan_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `stock_gudang`
--
ALTER TABLE `stock_gudang`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT untuk tabel `stock_tujuan`
--
ALTER TABLE `stock_tujuan`
  MODIFY `id_stock_tujuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT untuk tabel `surat_jalan`
--
ALTER TABLE `surat_jalan`
  MODIFY `id_surat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `surat_jalan_detail`
--
ALTER TABLE `surat_jalan_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `transaksi_keluar_dari_cabang`
--
ALTER TABLE `transaksi_keluar_dari_cabang`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `transaksi_keluar_gudang`
--
ALTER TABLE `transaksi_keluar_gudang`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT untuk tabel `transaksi_keluar_tujuan`
--
ALTER TABLE `transaksi_keluar_tujuan`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `transaksi_masuk_gudang`
--
ALTER TABLE `transaksi_masuk_gudang`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT untuk tabel `transaksi_masuk_tujuan`
--
ALTER TABLE `transaksi_masuk_tujuan`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `tujuan`
--
ALTER TABLE `tujuan`
  MODIFY `id_tujuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `verifikasi_penerimaan`
--
ALTER TABLE `verifikasi_penerimaan`
  MODIFY `id_verifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `fk_login_tujuan` FOREIGN KEY (`cabang_id`) REFERENCES `tujuan` (`id_tujuan`);

--
-- Ketidakleluasaan untuk tabel `permintaan`
--
ALTER TABLE `permintaan`
  ADD CONSTRAINT `fk_permintaan_tujuan` FOREIGN KEY (`id_tujuan`) REFERENCES `tujuan` (`id_tujuan`);

--
-- Ketidakleluasaan untuk tabel `permintaan_detail`
--
ALTER TABLE `permintaan_detail`
  ADD CONSTRAINT `fk_permintaan_detail_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `fk_permintaan_detail_permintaan` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan` (`id_permintaan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stock_gudang`
--
ALTER TABLE `stock_gudang`
  ADD CONSTRAINT `stock_gudang_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`);

--
-- Ketidakleluasaan untuk tabel `stock_tujuan`
--
ALTER TABLE `stock_tujuan`
  ADD CONSTRAINT `stock_tujuan_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `stock_tujuan_ibfk_2` FOREIGN KEY (`id_tujuan`) REFERENCES `tujuan` (`id_tujuan`);

--
-- Ketidakleluasaan untuk tabel `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD CONSTRAINT `surat_jalan_ibfk_2` FOREIGN KEY (`id_tujuan`) REFERENCES `tujuan` (`id_tujuan`);

--
-- Ketidakleluasaan untuk tabel `surat_jalan_detail`
--
ALTER TABLE `surat_jalan_detail`
  ADD CONSTRAINT `fk_surat_detail_material` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `fk_surat_detail_surat` FOREIGN KEY (`id_surat`) REFERENCES `surat_jalan` (`id_surat`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi_keluar_dari_cabang`
--
ALTER TABLE `transaksi_keluar_dari_cabang`
  ADD CONSTRAINT `transaksi_keluar_dari_cabang_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `transaksi_keluar_dari_cabang_ibfk_2` FOREIGN KEY (`id_tujuan`) REFERENCES `tujuan` (`id_tujuan`);

--
-- Ketidakleluasaan untuk tabel `transaksi_keluar_gudang`
--
ALTER TABLE `transaksi_keluar_gudang`
  ADD CONSTRAINT `transaksi_keluar_gudang_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `transaksi_keluar_gudang_ibfk_2` FOREIGN KEY (`id_tujuan`) REFERENCES `tujuan` (`id_tujuan`);

--
-- Ketidakleluasaan untuk tabel `transaksi_keluar_tujuan`
--
ALTER TABLE `transaksi_keluar_tujuan`
  ADD CONSTRAINT `transaksi_keluar_tujuan_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `transaksi_keluar_tujuan_ibfk_2` FOREIGN KEY (`dari_tujuan`) REFERENCES `tujuan` (`id_tujuan`),
  ADD CONSTRAINT `transaksi_keluar_tujuan_ibfk_3` FOREIGN KEY (`ke_tujuan`) REFERENCES `tujuan` (`id_tujuan`);

--
-- Ketidakleluasaan untuk tabel `transaksi_masuk_gudang`
--
ALTER TABLE `transaksi_masuk_gudang`
  ADD CONSTRAINT `transaksi_masuk_gudang_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`);

--
-- Ketidakleluasaan untuk tabel `transaksi_masuk_tujuan`
--
ALTER TABLE `transaksi_masuk_tujuan`
  ADD CONSTRAINT `transaksi_masuk_tujuan_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`),
  ADD CONSTRAINT `transaksi_masuk_tujuan_ibfk_2` FOREIGN KEY (`id_tujuan`) REFERENCES `tujuan` (`id_tujuan`);

--
-- Ketidakleluasaan untuk tabel `verifikasi_penerimaan`
--
ALTER TABLE `verifikasi_penerimaan`
  ADD CONSTRAINT `verifikasi_penerimaan_ibfk_2` FOREIGN KEY (`id_tujuan`) REFERENCES `tujuan` (`id_tujuan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
