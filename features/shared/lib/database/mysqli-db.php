<?php
// Legacy mysqli-based database bootstrap used by feature pages
// Exposes $mysqli (mysqli connection)

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'masjidkamek';
$DB_CHARSET = 'utf8mb4';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    // Connect directly to the specific database
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $mysqli->set_charset($DB_CHARSET);
} catch (mysqli_sql_exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

