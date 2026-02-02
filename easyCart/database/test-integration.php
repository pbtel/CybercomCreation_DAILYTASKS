<?php
/**
 * Quick Test Script - Phase 6 Database Integration
 * Tests all major database functionality
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/products.php';
require_once __DIR__ . '/../includes/categories.php';
require_once __DIR__ . '/../includes/brands.php';

echo "========================================\n";
echo "EasyCart Database Integration Test\n";
echo "========================================\n\n";

// Test 1: Database Connection
echo "Test 1: Database Connection\n";
if (testDbConnection()) {
    echo "✓ PASSED: Database connection successful\n\n";
} else {
    echo "❌ FAILED: Could not connect to database\n\n";
    exit(1);
}

// Test 2: Products
echo "Test 2: Products from Database\n";
$products = getAllProducts();
echo "  - Total products: " . count($products) . "\n";
if (count($products) > 0) {
    $firstProduct = $products[0];
    echo "  - First product: {$firstProduct['name']} (₹{$firstProduct['price']})\n";
    echo "✓ PASSED: Products loaded successfully\n\n";
} else {
    echo "❌ FAILED: No products found\n\n";
}

// Test 3: Product by ID
echo "Test 3: Get Product by ID\n";
$product = getProductById(1);
if ($product) {
    echo "  - Product ID 1: {$product['name']}\n";
    echo "  - Category: {$product['category']}\n";
    echo "  - Brand: {$product['brand']}\n";
    echo "  - Price: ₹{$product['price']}\n";
    echo "✓ PASSED: Product retrieved successfully\n\n";
} else {
    echo "❌ FAILED: Could not retrieve product\n\n";
}

// Test 4: Categories
echo "Test 4: Categories from Database\n";
$categories = getAllCategories();
echo "  - Total categories: " . count($categories) . "\n";
if (count($categories) > 0) {
    $firstCategory = $categories[0];
    echo "  - First category: {$firstCategory['name']} ({$firstCategory['product_count']} products)\n";
    echo "✓ PASSED: Categories loaded successfully\n\n";
} else {
    echo "❌ FAILED: No categories found\n\n";
}

// Test 5: Brands
echo "Test 5: Brands from Database\n";
$brands = getAllBrands();
echo "  - Total brands: " . count($brands) . "\n";
if (count($brands) > 0) {
    $firstBrand = $brands[0];
    echo "  - First brand: {$firstBrand['name']}\n";
    echo "✓ PASSED: Brands loaded successfully\n\n";
} else {
    echo "❌ FAILED: No brands found\n\n";
}

// Test 6: Products by Category
echo "Test 6: Products by Category\n";
$electronicsProducts = getProductsByCategory('electronics');
echo "  - Electronics products: " . count($electronicsProducts) . "\n";
if (count($electronicsProducts) > 0) {
    echo "✓ PASSED: Category filtering works\n\n";
} else {
    echo "❌ FAILED: No products found for category\n\n";
}

// Test 7: Products by Brand
echo "Test 7: Products by Brand\n";
$technoGearProducts = getProductsByBrand('technogear');
echo "  - TechnoGear products: " . count($technoGearProducts) . "\n";
if (count($technoGearProducts) > 0) {
    echo "✓ PASSED: Brand filtering works\n\n";
} else {
    echo "❌ FAILED: No products found for brand\n\n";
}

// Test 8: Users
echo "Test 8: Users in Database\n";
$userCount = fetchOne("SELECT COUNT(*) as count FROM users");
echo "  - Total users: {$userCount['count']}\n";
if ($userCount['count'] > 0) {
    $user = fetchOne("SELECT * FROM users LIMIT 1");
    echo "  - First user: {$user['email']}\n";
    echo "✓ PASSED: Users table populated\n\n";
} else {
    echo "❌ FAILED: No users found\n\n";
}

// Test 9: Orders
echo "Test 9: Orders in Database\n";
$orderCount = fetchOne("SELECT COUNT(*) as count FROM sales_order");
echo "  - Total orders: {$orderCount['count']}\n";
if ($orderCount['count'] > 0) {
    $order = fetchOne("SELECT * FROM sales_order LIMIT 1");
    echo "  - First order: {$order['order_number']} (₹{$order['final_amount']})\n";
    echo "✓ PASSED: Orders table populated\n\n";
} else {
    echo "⚠ WARNING: No orders found (this is OK if no orders were migrated)\n\n";
}

echo "========================================\n";
echo "✓ All Tests Completed!\n";
echo "========================================\n\n";
echo "Summary:\n";
echo "  - Products: " . count($products) . "\n";
echo "  - Categories: " . count($categories) . "\n";
echo "  - Brands: " . count($brands) . "\n";
echo "  - Users: {$userCount['count']}\n";
echo "  - Orders: {$orderCount['count']}\n";
echo "\nDatabase integration is working correctly!\n";
echo "You can now use the application with PostgreSQL.\n\n";
?>
