<?php
/**
 * Run Migration: Add Kontra Category and Pairing
 */

require_once __DIR__ . '/../features/shared/lib/database/mysqli-db.php';

echo "Running migration: 019_add_kontra_category_and_pairing.sql\n";

$migrationFile = __DIR__ . '/../database/migrations/019_add_kontra_category_and_pairing.sql';
$sql = file_get_contents($migrationFile);

// Split by semicolons and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue;
    }
    
    try {
        $mysqli->query($statement);
        echo "✓ Executed statement\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        echo "Statement: " . substr($statement, 0, 100) . "...\n";
    }
}

echo "\nMigration completed!\n";
