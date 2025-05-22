-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for mie_ayam
CREATE DATABASE IF NOT EXISTS `mie_ayam` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `mie_ayam`;

-- Dumping structure for table mie_ayam.admin
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table mie_ayam.admin: ~1 rows (approximately)
INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `created_at`) VALUES
	(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '2025-05-22 05:21:30');

  --password : password

-- Dumping structure for table mie_ayam.kategori
CREATE TABLE IF NOT EXISTS `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table mie_ayam.kategori: ~5 rows (approximately)
INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
	(1, 'Mie Ayam', 'Menu mie ayam dengan berbagai varian', '2025-05-22 05:21:30'),
	(2, 'Mie Pangsit', 'Menu mie dengan pangsit', '2025-05-22 05:21:30'),
	(3, 'Bakso', 'Menu bakso dan variannya', '2025-05-22 05:21:30'),
	(4, 'Minuman', 'Berbagai minuman segar', '2025-05-22 05:21:30'),
	(5, 'Tambahan', 'Menu tambahan dan pelengkap', '2025-05-22 05:21:30');

-- Dumping structure for table mie_ayam.menu
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_menu` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_menu_kategori` (`kategori_id`),
  KEY `idx_menu_status` (`status`),
  KEY `idx_menu_featured` (`is_featured`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table mie_ayam.menu: ~14 rows (approximately)
INSERT INTO `menu` (`id`, `nama_menu`, `deskripsi`, `harga`, `kategori_id`, `gambar`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
	(1, 'Mie Ayam Original', 'Mie ayam klasik dengan ayam suwir, pangsit goreng, dan sayuran segar', 15000.00, 1, 'mie_ayam_original.jpg', 'aktif', 1, '2025-05-22 05:21:30', '2025-05-22 05:27:32'),
	(2, 'Mie Ayam Bakso', 'Mie ayam dilengkapi dengan bakso sapi kenyal', 18000.00, 1, 'mie_ayam_bakso.jpg', 'aktif', 1, '2025-05-22 05:21:30', '2025-05-22 05:27:32'),
	(3, 'Mie Ayam Ceker', 'Mie ayam dengan ceker ayam empuk dan bumbu meresap', 20000.00, 1, 'mie_ayam_ceker.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 05:27:32'),
	(5, 'Mie Pangsit Goreng', 'Mie dengan pangsit goreng renyah', 17000.00, 2, 'mie_pangsit_goreng.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 05:27:32'),
	(6, 'Bakso Original', 'Bakso sapi asli dengan kuah kaldu segar', 12000.00, 3, 'bakso_original.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 06:18:24'),
	(7, 'Bakso Urat', 'Bakso urat kenyal dengan tekstur unik', 15000.00, 3, 'bakso_urat.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 05:27:32'),
	(8, 'Bakso Jumbo', 'Bakso berukuran jumbo dengan isian telur', 18000.00, 3, 'bakso_jumbo.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 05:27:32'),
	(9, 'Es Teh Manis', 'Es teh manis segar', 5000.00, 4, 'es_teh_manis.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 05:27:32'),
	(10, 'Es Jeruk', 'Es jeruk segar dengan potongan jeruk asli', 8000.00, 4, 'es_jeruk.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 05:27:32'),
	(11, 'Teh Hangat', 'Teh hangat untuk penghangat badan', 4000.00, 4, 'teh_hangat.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 05:44:56'),
	(12, 'Kerupuk', 'Kerupuk renyah pelengkap', 3000.00, 5, 'kerupuk.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 05:27:32'),
	(13, 'Pangsit Goreng (5 pcs)', 'Pangsit goreng isi daging cincang', 8000.00, 5, 'pangsit_goreng.jpg', 'aktif', 0, '2025-05-22 05:21:30', '2025-05-22 05:27:33'),
	(14, 'Dimsum', 'Dimsum isi ayan', 10000.00, 5, 'dimsum.jpg', 'aktif', 0, '2025-05-22 06:03:44', '2025-05-22 06:03:51'),
	(15, 'Dimsum Mentai', 'Dimsum Menatai isi ayam', 12000.00, 5, 'dimsum_mentai.jpg', 'aktif', 1, '2025-05-22 06:04:32', '2025-05-22 06:20:49');

-- Dumping structure for table mie_ayam.pengaturan
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_toko` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jam_buka` time DEFAULT NULL,
  `jam_tutup` time DEFAULT NULL,
  `deskripsi_toko` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table mie_ayam.pengaturan: ~1 rows (approximately)
INSERT INTO `pengaturan` (`id`, `nama_toko`, `alamat`, `telepon`, `whatsapp`, `email`, `jam_buka`, `jam_tutup`, `deskripsi_toko`, `logo`, `updated_at`) VALUES
	(1, 'Mie Ayam Pak Budi', 'H7R8+836, Jl. Hayam Wuruk, Kb. Jeruk, Kec. Tanjungkarang Timur, Kota Bandar Lampung, Lampung 35121', '0721-123456', '6281495112', 'mieayampakbudi@gmail.com', '10:00:00', '21:00:00', 'Mie Ayam Pak Budi adalah warung mie ayam legendaris yang telah melayani pelanggan selama puluhan tahun dengan cita rasa otentik dan harga terjangkau.', NULL, '2025-05-22 06:34:40');

-- Dumping structure for table mie_ayam.reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `komentar` text NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reviews_status` (`status`),
  KEY `idx_reviews_menu` (`menu_id`),
  KEY `idx_reviews_rating` (`rating`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table mie_ayam.reviews: ~6 rows (approximately)
INSERT INTO `reviews` (`id`, `nama`, `email`, `rating`, `komentar`, `menu_id`, `status`, `created_at`) VALUES
	(1, 'Budi Santoso', 'budi@email.com', 5, 'Mie ayamnya enak banget! Porsi besar dan harga terjangkau. Pasti balik lagi!', 1, 'approved', '2025-05-22 05:21:30'),
	(2, 'Allan Runcandel', 'allan@mail.com', 4, 'Rasanya authentic dan pelayanannya ramah. Cuma agak lama nunggu karena ramai.', 1, 'approved', '2025-05-22 05:21:30'),
	(3, 'Ahmad Rahman', 'ahmad@email.com', 5, 'Baksonya kenyal, kuahnya gurih. Recommended banget untuk makan siang!', 6, 'approved', '2025-05-22 05:21:30'),
	(4, 'Tiara Widya Putri', 'ara@gmail.com', 5, 'Dimsum mentainya mantap! Isinya padat dan banyak.', 15, 'approved', '2025-05-22 05:21:30'),
	(5, 'Roni Wijaya', 'roni@email.com', 5, 'Udah langganan dari tahun lalu. Konsisten rasanya dan harganya masih bersahabat.', 2, 'approved', '2025-05-22 05:21:30'),
	(6, 'Maulana', 'maulana@mail.com', 4, 'Tempatnya bersih, makanannya fresh. Cuma parkir agak susah kalau weekend.', 3, 'approved', '2025-05-22 05:21:30');

-- Dumping structure for table mie_ayam.toko_rating
CREATE TABLE IF NOT EXISTS `toko_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table mie_ayam.toko_rating: ~1 rows (approximately)
INSERT INTO `toko_rating` (`id`, `total_rating`, `total_reviews`, `updated_at`) VALUES
	(1, 4.50, 1200, '2025-05-22 06:06:38');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
