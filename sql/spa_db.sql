-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 12, 2025 at 07:35 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spa_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `appointment_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','canceled','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`appointment_id`),
  UNIQUE KEY `uq_slot` (`appointment_date`,`appointment_time`),
  KEY `fk_user` (`user_id`),
  KEY `fk_service` (`service_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `user_id`, `service_id`, `appointment_date`, `appointment_time`, `status`, `created_at`) VALUES
('LH01', 'KH02', 'DV01', '2025-12-15', '09:00:00', 'completed', '2025-12-09 09:09:07'),
('LH02', 'KH02', 'DV03', '2025-12-16', '14:00:00', 'confirmed', '2025-12-09 09:09:07'),
('LH03', 'KH03', 'DV05', '2025-12-17', '10:00:00', 'completed', '2025-12-09 09:09:07'),
('LH04', 'KH04', 'DV01', '2025-12-30', '11:00:00', 'canceled', '2025-12-11 11:08:11'),
('LH05', 'KH04', 'DV01', '2025-12-30', '08:00:00', 'confirmed', '2025-12-12 01:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `appointment_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int NOT NULL,
  `method` enum('cash','bank_transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('unpaid','paid','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `fk_appointment` (`appointment_id`)
) ;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `appointment_id`, `amount`, `method`, `status`, `created_at`) VALUES
('MaHD01', 'LH02', 450000, 'cash', 'paid', '2025-12-09 09:09:07'),
('MaHD02', 'LH03', 250000, 'bank_transfer', 'paid', '2025-12-09 09:09:07'),
('MaHD03', 'LH01', 350000, 'cash', 'paid', '2025-12-09 09:09:07'),
('MaHD04', 'LH05', 350000, 'cash', 'paid', '2025-12-12 01:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `service_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_vi_no_dau` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `duration_minutes` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`service_id`)
) ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `name_en`, `name_vi_no_dau`, `price`, `duration_minutes`, `description`, `image_url`) VALUES
('DV01', 'Facial Care', 'Cham Soc Mat', 400000, 75, 'Cham soc da mat co ban giup da sang min, thu gian va tai tao nang luong moi.', '../uploads/services/DV01_1765479994.webp'),
('DV02', 'Deep Cleansing Facial', 'Cham Soc Mat Sau', 500000, 90, 'Lam sach sau, tay te bao chet va duong chat chuyen sau cho lan da khoe dep tu ben trong.', '../uploads/services/DV02_1765480013.webp'),
('DV03', 'Body Massage 60mins', 'Massage Body 60 Phut', 450000, 60, 'Massage toan than voi tinh dau thien nhien, giam met moi va mang lai cam giac thu thai tuyet doi.', '../uploads/services/DV03_1765480081.webp'),
('DV04', 'Body Massage 90mins', 'Massage Body 90 Phut', 650000, 90, 'Massage sau toan than giup giai toa cang thang, tang cuong luu thong mau va phuc hoi co bap.', '../uploads/services/DV04_1765480111.webp'),
('DV05', 'Head Spa & Hair Wash', 'Goi Dau Duong Sinh', 250000, 60, 'Goi dau ket hop massage dau, co vai gay tao cam giac sang khoai va thu gian sau ngay dai.', '../uploads/services/DV05_1765480145.webp'),
('DV06', 'Foot Massage 45mins', 'Massage Chan 45 Phut', 300000, 45, 'Massage chan va bap chan kich thich huyet dao, giam dau nhuc va mang lai cam giac nhe nhang.', '../uploads/services/DV06_1765480173.webp'),
('DV07', 'Hot Stone Massage 90mins', 'Massage Da Nong', 700000, 90, 'Thu gian sau voi da nong, giup co the am ap, giam dau nhuc va tang cam giac yen binh.', '../uploads/services/DV07_1765480180.webp'),
('DV08', 'Full Body Scrub', 'Tay Da Chet Toan Than', 400000, 60, 'Tay da chet toan than giup da sang min, mem mai va san sang don nhan duong chat moi.', '../uploads/services/DV08_1765480201.webp'),
('DV09', 'Armpit Waxing', 'Waxing Nach', 100000, 15, 'Dich vu waxing nhanh chong, hieu qua giup vung nach sang min, tu tin voi moi trang phuc.', '../uploads/services/DV09_1765480211.webp'),
('DV10', 'Neck & Shoulder Therapy', 'Tri Lieu Co Vai Gay', 350000, 60, 'Tri lieu co vai gay giam met moi, giai toa cang thang va mang lai cam giac thu thai.', '../uploads/services/DV10_1765480253.webp');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','customer') COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `role`, `is_active`, `created_at`) VALUES
('KH01', 'admin', 'admin@spa.com', '$2y$10$2OrFzQKhmCiNan90xZqlYubdA/ZcbxE8bZGY6OblpiVHo5RUYHWO6', 'admin', 1, '2025-12-09 09:09:07'),
('KH02', 'bao', 'bao@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'customer', 1, '2025-12-09 09:09:07'),
('KH03', 'linh', 'linh@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'customer', 1, '2025-12-09 09:09:07'),
('KH04', 'tuimetmoi', 'tqbao200468@gmail.com', '$2y$10$wHd6C6C30iSfLg7vLThdwO6T7nlRJNlLs9zkoYZ3fnMl6i6070ICu', 'customer', 1, '2025-12-09 10:48:14');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
