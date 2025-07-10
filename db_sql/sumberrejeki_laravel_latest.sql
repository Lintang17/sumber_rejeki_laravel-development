-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 03, 2025 at 09:09 AM
-- Server version: 10.4.16-MariaDB
-- PHP Version: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sumberrejeki_laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `beli`
--

CREATE TABLE `beli` (
  `idbeli` int(11) NOT NULL,
  `notransaksi` text NOT NULL,
  `id` int(11) NOT NULL,
  `tanggalbeli` date NOT NULL,
  `totalbeli` text NOT NULL,
  `alamatpengiriman` text NOT NULL,
  `totalberat` varchar(255) NOT NULL,
  `kota` text NOT NULL,
  `ongkir` text NOT NULL,
  `statusbeli` text NOT NULL,
  `resipengiriman` text NOT NULL,
  `waktu` datetime NOT NULL,
  `pembeliandari` varchar(250) NOT NULL DEFAULT 'Penjualan Toko'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `beli`
--

INSERT INTO `beli` (`idbeli`, `notransaksi`, `id`, `tanggalbeli`, `totalbeli`, `alamatpengiriman`, `totalberat`, `kota`, `ongkir`, `statusbeli`, `resipengiriman`, `waktu`, `pembeliandari`) VALUES
(14, '#INV-20250215061052', 7, '2025-02-15', '10000', 'Banyuasin', '', 'Palembang', '7000', 'Barang Di Kirim', '12213123', '2025-02-15 06:10:52', 'Penjualan Toko'),
(15, '#INV-20250215062530', 7, '2025-02-15', '10000', 'Banyuasin', '', 'Palembang', '7000', 'Barang Telah Sampai ke Pemesan', '', '2025-02-15 06:25:30', 'Penjualan Toko'),
(16, '#INV-20250215104705', 7, '2025-02-15', '10000', 'Banyuasin', '', 'Palembang', '7000', 'Barang Telah Sampai ke Pemesan', '2123213213', '2025-02-15 10:47:05', 'Penjualan Toko');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `idkategori` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`idkategori`, `nama`, `created_at`, `update_at`) VALUES
(1, 'Meja', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_03_02_094914_create_personal_access_tokens_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `idpembayaran` int(11) NOT NULL,
  `idbeli` int(11) NOT NULL,
  `nama` text NOT NULL,
  `tanggaltransfer` text NOT NULL,
  `tanggal` datetime NOT NULL,
  `bukti` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`idpembayaran`, `idbeli`, `nama`, `tanggaltransfer`, `tanggal`, `bukti`) VALUES
(4, 4, 'Fahrul Adib', '2025-02-15', '2025-02-15 00:00:00', '202502150345301739354863.png'),
(5, 13, 'Fahrul Adib', '2025-02-15', '2025-02-15 00:00:00', '202502150453161739354863.png'),
(6, 14, 'Fahrul Adib', '2025-02-15', '2025-02-15 00:00:00', '202502150611031739354863.png'),
(7, 15, 'Fahrul Adib', '2025-02-15', '2025-02-15 00:00:00', '202502150625501739268974.png'),
(8, 16, 'Fahrul Adib', '2025-02-15', '2025-02-15 00:00:00', '202502151047181739268974.png');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `idpembelian` int(11) NOT NULL,
  `notabeli` text NOT NULL,
  `namabarang` text NOT NULL,
  `harga` text NOT NULL,
  `jumlah` text NOT NULL,
  `total` text NOT NULL,
  `grandtotal` text NOT NULL,
  `tanggalpembelian` date NOT NULL,
  `waktuinputbeli` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`idpembelian`, `notabeli`, `namabarang`, `harga`, `jumlah`, `total`, `grandtotal`, `tanggalpembelian`, `waktuinputbeli`, `deleted_at`) VALUES
(30, '20250215044701', 'keripik', '10000', '1', '10000', '10000', '2025-02-15', '2025-02-15 10:47:01', '2025-05-14 19:49:52'),
(31, '20250301121912', 'keripik', '10000', '2', '20000', '80000', '2025-03-01', '2025-03-01 12:19:12', '2025-05-14 19:49:45'),
(32, '20250301121912', 'Paracetamol', '20000', '3', '60000', '80000', '2025-03-01', '2025-03-01 12:19:12', '2025-05-14 19:49:45'),
(33, '20250303044532', 'Oli', '10000', '1', '10000', '30000', '2025-03-03', '2025-03-03 16:45:32', '2025-05-14 19:49:39'),
(34, '20250303044532', 'keripik', '10000', '2', '20000', '30000', '2025-03-03', '2025-03-03 16:45:32', '2025-05-14 19:49:39'),
(35, '20250304045842', 'Paracetamol', '10000', '2', '20000', '70000', '2025-03-04', '2025-03-04 16:58:42', '2025-05-14 19:49:33'),
(36, '20250304045842', 'Oli', '50000', '1', '50000', '70000', '2025-03-04', '2025-03-04 16:58:43', '2025-05-14 19:49:33'),
(39, '20250512122106', 'Sepatu', '10000', '1', '10000', '10000', '2025-05-12', '2025-05-12 12:21:06', '2025-05-14 17:30:32'),
(40, '20250512171116', 'Sisir', '3000', '10', '30000', '30000', '2025-05-12', '2025-05-12 17:11:16', '2025-05-14 19:48:40'),
(41, '20250513170104', 'Kemeja', '100000', '10', '1000000', '1000000', '2025-05-13', '2025-05-13 17:01:04', '2025-05-14 13:33:35'),
(42, '20250514212959', 'sofa turki', '2500000', '8', '20000000', '20000000', '2025-05-14', '2025-05-14 21:29:59', '2025-05-21 03:42:49'),
(43, '20250515234233', 'sofa turki', '2500000', '4', '10000000', '10000000', '2025-05-14', '2025-05-15 23:42:33', '2025-05-15 23:43:14'),
(44, '20250516132552', 'kursi sofa fjl', '3200000', '10', '32000000', '32000000', '2025-05-16', '2025-05-16 13:25:52', '2025-05-21 03:42:43'),
(45, '20250521052330', 'sofa turki', '2300000', '15', '34500000', '34500000', '2025-05-21', '2025-05-21 05:23:30', NULL),
(46, '20250521052353', 'kursi sofa fjl', '2800000', '18', '50400000', '50400000', '2025-05-21', '2025-05-21 05:23:53', NULL),
(47, '20250523125048', 'Sepeda', '1000000', '2', '2000000', '2000000', '2025-05-23', '2025-05-23 12:50:48', '2025-05-27 23:25:54'),
(48, '20250524170836', 'Sepatu', '300000', '10', '3000000', '3000000', '2025-05-24', '2025-05-24 17:08:36', '2025-05-27 23:26:05'),
(49, '20250527194801', 'kursi makan balero 4 fjl', '3000000', '30', '90000000', '90000000', '2025-05-27', '2025-05-27 19:48:01', NULL),
(50, '20250527194945', 'gazebo', '2500000', '18', '45000000', '45000000', '2025-05-27', '2025-05-27 19:49:45', NULL),
(51, '20250527195052', 'meja bar', '3500000', '25', '87500000', '87500000', '2025-05-27', '2025-05-27 19:50:52', NULL),
(52, '20250527195514', 'kursi meja meeting jati', '5500000', '10', '55000000', '55000000', '2025-05-27', '2025-05-27 19:55:14', NULL),
(53, '20250527195847', 'lemari 2 pintu jati fjl', '5200000', '8', '41600000', '41600000', '2025-05-27', '2025-05-27 19:58:47', NULL),
(54, '20250527200120', 'divan jati fjl', '8200000', '12', '98400000', '98400000', '2025-05-27', '2025-05-27 20:01:20', NULL),
(55, '20250527200357', 'kursi sofa minimalis', '2200000', '12', '26400000', '26400000', '2025-05-27', '2025-05-27 20:03:57', NULL),
(56, '20250527200621', 'daun pintu kayu', '3300000', '15', '49500000', '49500000', '2025-05-27', '2025-05-27 20:06:21', NULL),
(57, '20250528150449', 'meja fjl', '3200000', '15', '48000000', '48000000', '2025-05-28', '2025-05-28 15:04:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pembelianproduk`
--

CREATE TABLE `pembelianproduk` (
  `idpembeliandetail` int(11) NOT NULL,
  `idbeli` int(11) NOT NULL,
  `idproduk` int(11) NOT NULL,
  `nama` text NOT NULL,
  `harga` text NOT NULL,
  `berat` text NOT NULL,
  `subberat` text NOT NULL,
  `subharga` text NOT NULL,
  `jumlah` text NOT NULL,
  `harga_setelah_diskon` int(11) NOT NULL DEFAULT 0,
  `diskon` int(11) NOT NULL DEFAULT 0,
  `request` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembelianproduk`
--

INSERT INTO `pembelianproduk` (`idpembeliandetail`, `idbeli`, `idproduk`, `nama`, `harga`, `berat`, `subberat`, `subharga`, `jumlah`, `harga_setelah_diskon`, `diskon`, `request`) VALUES
(10, 14, 35, 'Paracetamol', '10000', '', '', '10000', '1', 0, 0, NULL),
(11, 15, 35, 'Paracetamol', '10000', '', '', '10000', '1', 0, 0, 'Warna hitam ukuran L'),
(12, 16, 34, 'keripik', '10000', '', '', '10000', '1', 0, 0, 'sadsadads');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `idpenjualan` int(11) NOT NULL,
  `notajual` text NOT NULL,
  `kodenota` text NOT NULL,
  `namabarang` text NOT NULL,
  `harga` text NOT NULL,
  `jumlah` text NOT NULL,
  `total` text NOT NULL,
  `grandtotal` text NOT NULL,
  `dp` decimal(12,2) DEFAULT NULL,
  `uangpembeli` text NOT NULL,
  `kembalian` text NOT NULL,
  `tanggalpenjualan` date NOT NULL DEFAULT current_timestamp(),
  `waktuinputjual` datetime NOT NULL DEFAULT current_timestamp(),
  `diskon` int(11) NOT NULL,
  `status` varchar(250) NOT NULL DEFAULT 'Transaksi Kasir',
  `statustransaksi` varchar(250) DEFAULT NULL,
  `statuspembelian` varchar(250) NOT NULL DEFAULT 'Lunas',
  `metodepembayaran` varchar(250) DEFAULT NULL,
  `custom` text NOT NULL,
  `statusgudang` varchar(250) NOT NULL DEFAULT 'Menunggu Konfirmasi',
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`idpenjualan`, `notajual`, `kodenota`, `namabarang`, `harga`, `jumlah`, `total`, `grandtotal`, `dp`, `uangpembeli`, `kembalian`, `tanggalpenjualan`, `waktuinputjual`, `diskon`, `status`, `statustransaksi`, `statuspembelian`, `metodepembayaran`, `custom`, `statusgudang`, `deleted_at`) VALUES
(57, '20250523113553', '20251', 'sofa turki', '3000000', '1', '3000000', '3000000', '1000000.00', '3000000', '1000000', '2025-05-23', '2025-05-23 11:35:53', 0, 'Transaksi Kasir', 'Menunggu Konfirmasi Admin', 'Lunas', 'Tunai', '', 'Menunggu Konfirmasi', '2025-05-27 23:24:30'),
(58, '20250523171641', '20252', 'sofa turki', '3000000', '1', '3000000', '3000000', '1000000.00', '1000000', '0', '2025-05-23', '2025-05-23 17:16:41', 0, 'Transaksi Kasir', 'Menunggu Konfirmasi Admin', 'DP', 'Tunai', '', 'Menunggu Konfirmasi', '2025-05-27 23:24:37'),
(59, '20250523173723', '20253', 'sofa turki', '3000000', '1', '3000000', '3000000', '1000000.00', '3000000', '1000000', '2025-05-23', '2025-05-23 17:37:23', 0, 'Transaksi Kasir', 'Menunggu Konfirmasi Admin', 'Lunas', 'Tunai', '', 'Menunggu Konfirmasi', '2025-05-27 23:24:42'),
(60, '20250524170627', '20254', 'sofa turki', '3000000', '1', '3000000', '3000000', '1000000.00', '3000000', '1000000', '2025-05-24', '2025-05-24 10:26:02', 0, 'Transaksi Kasir', 'Menunggu Konfirmasi Admin', 'Lunas', 'Tunai', '', 'Menunggu Konfirmasi', '2025-05-27 23:23:26'),
(61, '20250527232915', '20251', 'kursi makan balero 4 fjl', '3200000', '1', '3200000', '8700000', '5500000.00', '6000000', '500000', '2025-05-27', '2025-05-27 23:29:15', 50, 'Transaksi Kasir', 'Menunggu Konfirmasi Admin', 'DP', 'Tunai', '', 'Menunggu Konfirmasi', NULL),
(62, '20250527232915', '20251', 'gazebo', '2800000', '3', '8400000', '8700000', '5500000.00', '6000000', '500000', '2025-05-27', '2025-05-27 23:29:15', 50, 'Transaksi Kasir', 'Menunggu Konfirmasi Admin', 'DP', 'Tunai', '', 'Menunggu Konfirmasi', NULL),
(63, '20250527232915', '20251', 'kursi sofa minimalis', '2900000', '2', '5800000', '8700000', '5500000.00', '6000000', '500000', '2025-05-27', '2025-05-27 23:29:15', 50, 'Transaksi Kasir', 'Menunggu Konfirmasi Admin', 'DP', 'Tunai', '', 'Menunggu Konfirmasi', NULL),
(64, '20250528150819', '20252', 'divan jati fjl', '8800000', '2', '17600000', '10250000', '0.00', '10500000', '250000', '2025-05-28', '2025-05-28 15:08:19', 50, 'Transaksi Kasir', 'Menunggu Konfirmasi Admin', 'Lunas', 'Tunai', '', 'Menunggu Konfirmasi', NULL),
(65, '20250528150819', '20252', 'kursi sofa minimalis', '2900000', '1', '2900000', '10250000', '0.00', '10500000', '250000', '2025-05-28', '2025-05-28 15:08:19', 50, 'Transaksi Kasir', 'Menunggu Konfirmasi Admin', 'Lunas', 'Tunai', '', 'Menunggu Konfirmasi', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `idproduk` int(11) NOT NULL,
  `namaproduk` text NOT NULL,
  `hargajual` text NOT NULL,
  `stok` varchar(255) NOT NULL,
  `fotoproduk` text NOT NULL,
  `barcode` text NOT NULL,
  `link` text NOT NULL,
  `kodeqr` text NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`idproduk`, `namaproduk`, `hargajual`, `stok`, `fotoproduk`, `barcode`, `link`, `kodeqr`, `deleted_at`) VALUES
(40, 'keripik', '10000', '8', 'lambang-koperasi.png', '20250218024703', 'http://192.168.100.218/tokodartocoy/scan.php?kode=20250218024703', '20250218024703.png', '2025-05-14 19:48:52'),
(41, 'Paracetamol', '1000', '105', '_11.jpeg', '20250218024723', 'http://192.168.100.218/tokodartocoy/scan.php?kode=20250218024723', '20250218024723.png', '2025-05-14 19:49:00'),
(42, 'Shampo', '30000', '97', 'lambang-koperasi.png', '20250218104920', 'http://192.168.100.218/tokodartocoy/scan.php?kode=20250218104920', '20250218104920.png', '2025-05-14 19:49:06'),
(48, 'Kemeja', '110000', '9', '1747130513.jpg', '20250513050153', 'http://localhost:8000/scanqr/kode/20250513050153', '20250513050153.png', '2025-05-14 19:49:13'),
(49, 'sofa turki', '3000000', '3', '1747233032.jpg', '20250514093032', 'http://127.0.0.1:8000/scanqr/kode/20250514093032', '20250514093032.png', '2025-05-16 00:52:11'),
(50, 'sofa turki', '3000000', '-7', '1747331554.jpg', '20250516125234', 'http://127.0.0.1:8000/scanqr/kode/20250516125234', '20250516125234.png', '2025-05-16 04:00:37'),
(51, 'sofa turki', '3000000', '7', '1747342864.jpg', '20250516040104', 'http://127.0.0.1:8000/scanqr/kode/20250516040104', '20250516040104.png', '2025-05-21 05:21:39'),
(52, 'kursi sofa fjl', '3500000', '8', '1747376789.jpg', '20250516012629', 'http://127.0.0.1:8000/scanqr/kode/20250516012629', '20250516012629.png', '2025-05-21 05:21:44'),
(53, 'sofa turki', '3000000', '6', '1747779881.jpg', '20250521052441', 'http://127.0.0.1:8000/scanqr/kode/20250521052441', '20250521052441.png', NULL),
(54, 'kursi sofa fjl', '3499999', '18', '1747779914.jpg', '20250521052514', 'http://127.0.0.1:8000/scanqr/kode/20250521052514', '20250521052514.png', '2025-05-21 05:25:40'),
(55, 'kursi sofa fjl', '3200000', '13', '1747779959.jpg', '20250521052559', 'http://127.0.0.1:8000/scanqr/kode/20250521052559', '20250521052559.png', NULL),
(56, 'kursi makan balero 4 fjl', '3200000', '29', '1748350148.jpg', '20250527074908', 'http://127.0.0.1:8000/scanqr/kode/20250527074908', '20250527074908.png', NULL),
(57, 'gazebo', '2800000', '15', '1748350218.jpg', '20250527075018', 'http://127.0.0.1:8000/scanqr/kode/20250527075018', '20250527075018.png', NULL),
(58, 'meja bar', '4200000', '25', '1748350288.jpg', '20250527075128', 'http://127.0.0.1:8000/scanqr/kode/20250527075128', '20250527075128.png', NULL),
(59, 'kursi meja meeting jati', '6200000', '10', '1748350547.jpg', '20250527075547', 'http://127.0.0.1:8000/scanqr/kode/20250527075547', '20250527075547.png', NULL),
(60, 'lemari 2 pintu jati fjl', '5800000', '8', '1748350774.jpg', '20250527075934', 'http://127.0.0.1:8000/scanqr/kode/20250527075934', '20250527075934.png', NULL),
(61, 'divan jati fjl', '8800000', '10', '1748350922.jpg', '20250527080202', 'http://127.0.0.1:8000/scanqr/kode/20250527080202', '20250527080202.png', NULL),
(62, 'kursi sofa minimalis', '2900000', '9', '1748351075.jpg', '20250527080435', 'http://127.0.0.1:8000/scanqr/kode/20250527080435', '20250527080435.png', NULL),
(63, 'daun pintu kayu', '3800000', '15', '1748351221.jpg', '20250527080701', 'http://127.0.0.1:8000/scanqr/kode/20250527080701', '20250527080701.png', NULL),
(64, 'meja fjl', '3500000', '15', '1748419527.jpg', '20250528030527', 'http://127.0.0.1:8000/scanqr/kode/20250528030527', '20250528030527.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('y3u1P2flZ1gt4VCtdV2p4fFwuFxyupd8QQu0dfn0', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiclJNZElLV0tLY2w1bWZLOEk1OTlsYzlYUUZaS002VlRYUm9DdkN0dSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9rYXNpci9jZXRha25vdGEvMjAyNTA1MjcyMzI5MTUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1748934423);

-- --------------------------------------------------------

--
-- Table structure for table `stockopname`
--

CREATE TABLE `stockopname` (
  `idstockopname` int(11) NOT NULL,
  `idproduk` text NOT NULL,
  `stoksistem` text NOT NULL,
  `stokgudang` text NOT NULL,
  `tanggalstockopname` date NOT NULL,
  `selisih` text NOT NULL,
  `waktuinputstockopname` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stockopname`
--

INSERT INTO `stockopname` (`idstockopname`, `idproduk`, `stoksistem`, `stokgudang`, `tanggalstockopname`, `selisih`, `waktuinputstockopname`) VALUES
(11, '55', '13', '13', '2025-05-26', '0', '2025-05-26 02:32:28');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `idsupplier` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`idsupplier`, `nama`, `alamat`, `telepon`, `created_at`, `updated_at`) VALUES
(1, 'bigfoam', 'Jl. Kartini, Surabaya', '082345897456', '2025-05-20 19:55:57', '2025-05-20 20:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nohp` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `alamat`, `nohp`, `role`, `file`) VALUES
(1, 'Administrator', 'admin@gmail.com', NULL, '$2y$12$ue0S.SaryhZNuShOfkK57OgaqmByqJ2mlT4yOeffgqMhlccy6avzK', NULL, NULL, '2025-06-03 06:49:36', 'Banyuasin', '082273647388', 'Admin', 'profile/owA4GL7BENn90xFluOrXeEInUOcKuLKDNy6TeATL.webp'),
(2, 'Kasir', 'kasir@gmail.com', NULL, '$2y$12$ak0xsfHIUUk7IDDR66XJn..QlLbtbjR.uJ3raDsrwhVYIOjx.pk.q', NULL, NULL, '2025-06-03 04:29:06', 'Banyuasin', '082273829339', 'Kasir', 'profile/i2MrZ650vLkLopMkiZOQv42D2VIutKZeVAktgMzI.webp'),
(3, 'Owner', 'owner@gmail.com', NULL, '$2y$12$k9YgI8rUKsy0JPTtUTzXK.h4YtfHsCtSFIlQxs9zfj3pLL1hRn5ou', NULL, '2025-03-02 03:46:59', '2025-05-25 10:43:19', NULL, NULL, 'Owner', NULL),
(5, 'Gudang', 'gudang@gmail.com', NULL, '$2y$12$1KX3ItVjgg5MMekr3iE2Y.vnd4q7DNV/c/SgTmaoZkKjdhPvE8VBq', NULL, '2025-05-12 04:52:53', '2025-06-03 06:50:50', NULL, NULL, 'Gudang', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beli`
--
ALTER TABLE `beli`
  ADD PRIMARY KEY (`idbeli`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`idkategori`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`idpembayaran`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`idpembelian`);

--
-- Indexes for table `pembelianproduk`
--
ALTER TABLE `pembelianproduk`
  ADD PRIMARY KEY (`idpembeliandetail`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`idpenjualan`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`idproduk`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stockopname`
--
ALTER TABLE `stockopname`
  ADD PRIMARY KEY (`idstockopname`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`idsupplier`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beli`
--
ALTER TABLE `beli`
  MODIFY `idbeli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `idkategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `idpembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `idpembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `pembelianproduk`
--
ALTER TABLE `pembelianproduk`
  MODIFY `idpembeliandetail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `idpenjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `idproduk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `stockopname`
--
ALTER TABLE `stockopname`
  MODIFY `idstockopname` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `idsupplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
