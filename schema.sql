-- schema.sql
-- Database creation script for Rubber Clone AI
-- Target Database Management System: MariaDB / MySQL (v10.4+ / v8.0+)

CREATE DATABASE IF NOT EXISTS `rubberclone` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rubberclone`;

-- 1. Table: users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(150) NOT NULL,
  `agency` VARCHAR(100) DEFAULT 'RISDA Pekebun Kecil',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `avatar_url` VARCHAR(255) DEFAULT NULL,
  `registration_date` BIGINT NOT NULL, -- Unix timestamp in milliseconds
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table: analysis_records
CREATE TABLE IF NOT EXISTS `analysis_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `clone_name` VARCHAR(100) NOT NULL,
  `confidence` FLOAT NOT NULL,
  `timestamp` BIGINT NOT NULL, -- Unix timestamp in milliseconds
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  `location_name` VARCHAR(255) DEFAULT 'Stesen RISDA, Malaysia',
  `image_url` VARCHAR(255) DEFAULT NULL,
  `notes` TEXT,
  `soil_type` VARCHAR(100) DEFAULT 'Tiada Maklumat',
  `rainfall` VARCHAR(100) DEFAULT 'Tiada Maklumat',
  `elevation` VARCHAR(100) DEFAULT 'Tiada Maklumat',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table: cms_settings
CREATE TABLE IF NOT EXISTS `cms_settings` (
  `key` VARCHAR(50) PRIMARY KEY,
  `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table: blog_posts
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `author` VARCHAR(100) DEFAULT 'RISDA Pentadbir',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table: clone_samples
CREATE TABLE IF NOT EXISTS `clone_samples` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `clone_name` VARCHAR(50) NOT NULL,
  `warisan` VARCHAR(100),
  `potensi_hasil` VARCHAR(50),
  `anggaran_kayu` VARCHAR(50),
  `bentuk_daun` VARCHAR(100),
  `bentuk_hujung_daun` VARCHAR(100),
  `bentuk_pangkal_daun` VARCHAR(100),
  `kedudukan_lai_daun` VARCHAR(100),
  `bentuk_tepi_daun` VARCHAR(100),
  `warna_daun_kilauan` VARCHAR(100),
  `permukaan_daun` VARCHAR(100),
  `pandangan_memanjang` VARCHAR(100),
  `pandangan_melintang` VARCHAR(100),
  `saiz_gagang_daun` VARCHAR(100),
  `saiz_anak_gagang` VARCHAR(100),
  `warna_lateks` VARCHAR(100),
  `status` ENUM('active','inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.1 Seed Data: 3 Klon Getah Sebenar (PB 350, PB 260, RRIM 2002)
INSERT INTO `clone_samples` (`clone_name`, `warisan`, `potensi_hasil`, `anggaran_kayu`, `bentuk_daun`, `bentuk_hujung_daun`, `bentuk_pangkal_daun`, `kedudukan_lai_daun`, `bentuk_tepi_daun`, `warna_daun_kilauan`, `permukaan_daun`, `pandangan_memanjang`, `pandangan_melintang`, `saiz_gagang_daun`, `saiz_anak_gagang`, `warna_lateks`) VALUES
('PB 350', 'RRIM 600 × PB 235', '2,765', '19T/1.6', 'Bulat (rounded)', 'Kuspidat (Cuspidate)', 'Bulat (Obtuse)', 'Bersentuh ke bertindih', 'Gelombang', 'Hijau tua, sedikit berkilat', 'Licin', 'Rata/Selanjar', 'Rata', 'Sederhana panjang, rata', 'Pendek dan rata', 'Putih'),
('PB 260', 'PB5/51 × PB49', '2,675', '1.29/pokok', 'Bujur telur (Obovate) ke Bujur sama (Elliptical)', 'Akuminat (Accuminate)', 'Baji/Tirus (Cuneate)', 'Terpisah ke Bersentuhan', 'Keriting', 'Hijau muda/kekuningan, sedikit berkilat', 'Kasar', 'Menurun', 'Bentuk perahu (boat shape)', 'Sederhana panjang dan rata', 'Sederhana panjang dan menurun', 'Krim'),
('RRIM 2002', 'PB 5/51 × FORD 351', '2,348', '17Th/1.10', 'Bujur sama (Elliptical)', 'Akuminat (Acuminate)', 'Bulat (Obtuse)', 'Bersentuhan ke bertindih', 'Licin', 'Hijau muda, sedikit berkilat', 'Licin', 'Rata/Selanjar', 'Bentuk perahu (boat shape)', 'Sederhana panjang, rata', 'Pendek, rata', 'Kekuningan (yellowish)');

-- 6. Table: announcements (Pekeliling & Makluman RISDA)
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `publish_at` BIGINT NOT NULL,   -- Unix timestamp in milliseconds
  `expires_at` BIGINT DEFAULT NULL, -- Unix timestamp in milliseconds (NULL = no expiration)
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `author` VARCHAR(100) DEFAULT 'RISDA Pentadbir',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.1 Seed Data: Pekeliling RISDA (Semasa & Berjadual)
INSERT INTO `announcements` (`title`, `content`, `publish_at`, `expires_at`, `status`) VALUES
('MAKLUMAN INTENSIF BAJA GETAH RISDA 2026', 'Bantuan Skim Baja RISDA 2026 kini dibuka untuk permohonan berskala besar di seluruh Semenanjung Malaysia. Pastikan klon getah yang ditanam berdaftar.', 1700000000000, 2085000000000, 'active'),
('PEKELILING PENGAGIHAN BENIH KLON PB 350 & PB 260', 'RISDA mengumumkan pengagihan benih klon getah PB 350 dan PB 260 bersubsidi untuk zon utara mulai bulan ini.', 1700000000000, 2085000000000, 'active');

