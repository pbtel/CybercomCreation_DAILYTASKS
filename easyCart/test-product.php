<?php
require_once 'config/database.php';
$pdo = getDbConnection();
$stmt = $pdo->query('SELECT product_id, name FROM catalog_product_entity LIMIT 5');
echo "Product IDs in database:\n";
while($row = $stmt->fetch()) {
    echo $row['product_id'] . ': ' . $row['name'] . "\n";
}
?>
