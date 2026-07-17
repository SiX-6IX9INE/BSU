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


-- Dumping database structure for bsu_store
CREATE DATABASE IF NOT EXISTS `bsu_store` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `bsu_store`;

-- Dumping structure for table bsu_store.licenses
CREATE TABLE IF NOT EXISTS `licenses` (
  `license_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `license_key` varchar(255) DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`license_id`),
  UNIQUE KEY `license_key` (`license_key`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `licenses_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bsu_store.licenses: ~0 rows (approximately)

-- Dumping structure for table bsu_store.logs_buy
CREATE TABLE IF NOT EXISTS `logs_buy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT curdate(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bsu_store.logs_buy: ~0 rows (approximately)

-- Dumping structure for table bsu_store.products
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `license_duration_days` int(11) DEFAULT NULL,
  `featured` int(1) NOT NULL DEFAULT 0,
  `img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bsu_store.products: ~2 rows (approximately)
INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `license_duration_days`, `featured`, `img`) VALUES
	(1, 'Window 11 Home', 'ระบบปฏิบัติการ Windows 11 มาพร้อมกับ Features และ ความปลอดภัยที่ดีขึ้น รวมทั้งรูปแบบหน้าตา Interface ใหม่ๆ ที่จะสร้างประสบการณ์การใช้งานแบบใหม่สำหรับคอมพิวเตอร์คุณ ให้ความสนุกในแบบคุณ มีคุณสมบัติที่คุณต้องการเพื่อเวลาโหลดที่รวดเร็ว ประสบการณ์การใช้งานที่น่าประทับใจ ', 1000, -1, 1, 'window11home.png'),
	(2, 'Window 11 Pro', 'ระบบปฏิบัติการ Windows 11 Pro FPP แบบซื้อครั้งเดียวใช้ได้ตลอดชีพ เพียงเก็บรักษาตัว USB แฟลชไดร์ฟไว้คู่กับ Serial Key รองรับการติดตั้งไม่เกิน 1 เครื่อง สามารถย้ายไปติดตั้งในเครื่องใหม่ได้เมื่อมีการเปลี่ยนคอมพิวเตอร์ ติดตั้งง่าย ไม่กี่ขั้นตอน มาพร้อมกับ Features และ ความปลอดภัยที่ดีขึ้น รวมทั้งรูปแบบหน้าตา Interface ใหม่ๆ เครื่องมือเสียงและ Application ที่จะร่วมสร้างประสบการณ์การใช้งานแบบใหม่สำหรับคอมพิวเตอร์คุณ ให้ความสนุกในแบบคุณ ด้วยการตั้งค่าได้ตามต้องการ Windows 11 ไม่เพียงใช้ร่วมกับฮาร์ดแวร์และซอฟต์แวร์ได้หลากหลาย แต่ยังมีคุณสมบัติที่คุณต้องการเพื่อเวลาโหลดที่รวดเร็ว ประสบการณ์การใช้งานที่น่าประทับใจ และภาพอันน่าทึ่ง', 1500, -1, 1, 'window11pro.png');

-- Dumping structure for table bsu_store.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bsu_store.users: ~0 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `created_at`) VALUES
	(1, 'test', '$2y$10$mAmT.xdoYkLgCKr4PbJSYO0GwspKwm.TabpbDTVOFeH7rL/7ktOba', 'test@gmail.com', '0123456', '2025-04-09 18:20:21');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
