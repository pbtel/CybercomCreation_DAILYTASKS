<?php
require_once 'includes/db.php';

try {
    $pdo = getDbConnection();
    if (!$pdo) {
        echo "No connection.\n";
        exit;
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_product_entity");
    $count = $stmt->fetchColumn();
    echo "Products: $count\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>