<?php
require_once __DIR__ . '/../app/core/Database.php';

try {
    $db = Database::getInstance();
    $sqlFile = __DIR__ . '/../database/drop_cart_columns.sql';

    if (!file_exists($sqlFile)) {
        die("SQL file not found: $sqlFile\n");
    }

    echo "Applying migration: " . basename($sqlFile) . "\n";
    $sql = file_get_contents($sqlFile);
    $db->query($sql);
    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
