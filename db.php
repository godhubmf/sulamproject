<?php
// Database connection bootstrap for Laragon + phpMyAdmin
// Target DB: masjidkamek; Target table: users
// Exposes $mysqli (mysqli connection)

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'masjidkamek';
$DB_CHARSET = 'utf8mb4';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
    $mysqli->set_charset($DB_CHARSET);

    // Create database if it doesn't exist
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$DB_NAME}` CHARACTER SET {$DB_CHARSET} COLLATE {$DB_CHARSET}_unicode_ci");
    $mysqli->select_db($DB_NAME);

        // Ensure the `users` table exists with the latest schema
    $createUserTableSql = <<<SQL
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(120) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(120) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `roles` VARCHAR(50) NOT NULL DEFAULT 'user',
    `no_telefon` VARCHAR(20) NULL,
    `alamat` TEXT NULL,
    `status_perkahwinan` ENUM('bujang','berkahwin','bercerai','duda','janda','lain-lain') NULL,
    `is_meninggal` TINYINT(1) NOT NULL DEFAULT 0,
    `pendapatan` DECIMAL(10,2) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_username` (`username`),
    UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET={$DB_CHARSET} COLLATE {$DB_CHARSET}_unicode_ci;
SQL;
    $mysqli->query($createUserTableSql);
    // Ensure other tables exist (minimal bootstrap)
    $mysqli->query("CREATE TABLE IF NOT EXISTS `waris` (\n  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n  `user_id` INT UNSIGNED NOT NULL,\n  `name` VARCHAR(120) NOT NULL,\n  `email` VARCHAR(120) NULL,\n  `no_telefon` VARCHAR(20) NULL,\n  `alamat` TEXT NULL,\n  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n  PRIMARY KEY (`id`),\n  KEY `idx_waris_user_id` (`user_id`),\n  CONSTRAINT `fk_waris_user_boot` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE\n) ENGINE=InnoDB DEFAULT CHARSET={$DB_CHARSET} COLLATE {$DB_CHARSET}_unicode_ci;");
    $mysqli->query("CREATE TABLE IF NOT EXISTS `events` (\n  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n  `description` TEXT NOT NULL,\n  `gamba` VARCHAR(255) NULL,\n  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n  PRIMARY KEY (`id`)\n) ENGINE=InnoDB DEFAULT CHARSET={$DB_CHARSET} COLLATE {$DB_CHARSET}_unicode_ci;");
    $mysqli->query("CREATE TABLE IF NOT EXISTS `donations` (\n  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n  `description` TEXT NOT NULL,\n  `gamba` VARCHAR(255) NULL,\n  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n  PRIMARY KEY (`id`)\n) ENGINE=InnoDB DEFAULT CHARSET={$DB_CHARSET} COLLATE {$DB_CHARSET}_unicode_ci;");
    $mysqli->query("CREATE TABLE IF NOT EXISTS `deaths` (\n  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n  `user_id` INT UNSIGNED NOT NULL,\n  `time` TIME NULL,\n  `tarikh` DATE NULL,\n  `tarikh_islam` VARCHAR(50) NULL,\n  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n  PRIMARY KEY (`id`),\n  KEY `idx_deaths_user_id` (`user_id`),\n  CONSTRAINT `fk_deaths_user_boot` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE\n) ENGINE=InnoDB DEFAULT CHARSET={$DB_CHARSET} COLLATE {$DB_CHARSET}_unicode_ci;");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo '<h2>Database connection error</h2>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}
