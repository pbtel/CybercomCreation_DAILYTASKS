<?php
/**
 * Database Connection Test - EasyCart Phase 6
 * Tests PostgreSQL connection and verifies tables exist
 */

require_once __DIR__ . '/../config/database.php';

echo "========================================\n";
echo "EasyCart Database Connection Test\n";
echo "========================================\n\n";

// Test 1: Connection
echo "Test 1: Testing database connection...\n";
$pdo = getDbConnection();

if ($pdo === null) {
    echo "❌ FAILED: Could not connect to database\n";
    echo "Please check:\n";
    echo "  - PostgreSQL is running\n";
    echo "  - Database 'easycart_db' exists\n";
    echo "  - Credentials in config/database.php are correct\n";
    exit(1);
}

echo "✓ PASSED: Database connection successful\n\n";

// Test 2: Check tables exist
echo "Test 2: Checking if tables exist...\n";

$tables = [
    'catalog_product_entity',
    'catalog_product_attribute',
    'catalog_category_entity',
    'catalog_category_attribute',
    'catalog_category_products',
    'catalog_brand_entity',
    'catalog_brand_attribute',
    'product_images',
    'users',
    'sales_cart',
    'sales_cart_product',
    'sales_cart_address',
    'sales_cart_meta',
    'sales_order',
    'sales_order_product',
    'sales_order_address',
    'sales_order_meta'
];

$allTablesExist = true;

foreach ($tables as $table) {
    $sql = "SELECT EXISTS (
                SELECT FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_name = :table_name
            )";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['table_name' => $table]);
        $result = $stmt->fetch();
        
        if ($result['exists'] === 't' || $result['exists'] === true) {
            echo "  ✓ Table '$table' exists\n";
        } else {
            echo "  ❌ Table '$table' NOT FOUND\n";
            $allTablesExist = false;
        }
    } catch (PDOException $e) {
        echo "  ❌ Error checking table '$table': " . $e->getMessage() . "\n";
        $allTablesExist = false;
    }
}

echo "\n";

if (!$allTablesExist) {
    echo "❌ FAILED: Some tables are missing\n";
    echo "Please run: database/setup.bat\n";
    exit(1);
}

echo "✓ PASSED: All tables exist\n\n";

// Test 3: Check data
echo "Test 3: Checking if data exists...\n";

$dataTables = [
    'catalog_product_entity' => 'Products',
    'catalog_category_entity' => 'Categories',
    'catalog_brand_entity' => 'Brands',
    'users' => 'Users'
];

foreach ($dataTables as $table => $label) {
    $sql = "SELECT COUNT(*) as count FROM $table";
    try {
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch();
        $count = $result['count'];
        
        if ($count > 0) {
            echo "  ✓ $label: $count records\n";
        } else {
            echo "  ⚠ $label: No data (run migrate.php to import data)\n";
        }
    } catch (PDOException $e) {
        echo "  ❌ Error checking $label: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "========================================\n";
echo "✓ Database test completed successfully!\n";
echo "========================================\n";
?>
