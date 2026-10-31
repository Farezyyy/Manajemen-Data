-- --------------------------------------------------------
-- Host:                     127.0.0.1
-- Waktu pembuatan:        $(date 'Y-m-d H:i:s')
-- Versi server:           8.0.33 (sesuai environment Anda)
-- Versi PHP:              8.2+ (gunakan PDO, prepared statements)
-- --------------------------------------------------------

-- Membuat database (jalankan jika belum ada)
CREATE DATABASE IF NOT EXISTS `sistem_manajemen_data` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistem_manajemen_data`;

-- --------------------------------------------------------
-- Tabel users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `nama` VARCHAR(100) NOT NULL,
    `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
    `password` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel kategori
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kategori` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(50) NOT NULL,
    `deskripsi` TEXT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabel produk
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `produk` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `kode` VARCHAR(20) NOT NULL UNIQUE,
    `nama` VARCHAR(100) NOT NULL,
    `kategori_id` INT UNSIGNED DEFAULT NULL,
    `harga` DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT '0.00',
    `stok` INT UNSIGNED NOT NULL DEFAULT '0',
    `deskripsi` TEXT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX `fk_produk_kategori` (`kategori_id`),
    CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Inisialisasi data dasar
-- --------------------------------------------------------
-- User admin (password: 'admin123'  — hash: $2y$10...) — gunakan password_hash di produksi
INSERT IGNORE INTO `users` (`username`, `nama`, `role`, `password`) VALUES
('admin', 'Administrator', 'admin', '$2y$10$iD2R7xzxURj0xyhhrLw/8OvHAv1UMNgqVwi8zjvA5jLW6obkFuP/W');

-- Kategori default (tanpa kategori)
INSERT IGNORE INTO `kategori` (`nama`, `deskripsi`) VALUES
('Umum', 'Produk tanpa kategori spesifik');

-- Produk contoh (jika kategori_id=1 maka dia pakai kategori 1/Umum, kalau NULL pakai "-- Tanpa kategori --")
INSERT IGNORE INTO `produk` (`kode`, `nama`, `kategori_id`, `harga`, `stok`, `deskripsi`) VALUES
('PROD-001', 'Laptop Gaming', 1, 12500000.00, 15, 'Laptop performa tinggi untuk gaming'),
('PROD-002', 'Mouse Wireless', 1, 350000.00, 42, 'Mouse ergonomik tanpa kabel'),
('PROD-003', 'Mechanical Keyboard', 1, 950000.00, 28, 'Keyboard mekanis anti-ghosting'),
('PROD-004', 'Monitor 24 inch', NULL, 2800000.00, 10, 'Monitor Full HD 24 inci');

-- --------------------------------------------------------
-- Lihat konfigurasi di config/database.php
-- define('DB_HOST', '127.0.0.1');
-- define('DB_NAME', 'sistem_manajemen_data');
-- define('DB_USER', 'root');
-- define('DB_PASS', '');
-- --------------------------------------------------------
