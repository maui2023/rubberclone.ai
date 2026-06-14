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
