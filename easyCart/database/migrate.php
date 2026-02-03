<?php
/**
 * Data Migration Script - EasyCart Phase 6
 * Migrates data from PHP arrays and JSON files to PostgreSQL database
 */

// Set execution time limit
set_time_limit(300);

// Include required files
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/products.php';
require_once __DIR__ . '/../includes/categories.php';
require_once __DIR__ . '/../includes/brands.php';

echo "========================================\n";
echo "EasyCart Data Migration to PostgreSQL\n";
echo "========================================\n\n";

// Test database connection
echo "Testing database connection...\n";
if (!testDbConnection()) {
    die("ERROR: Could not connect to database. Please check your configuration.\n");
}
echo "✓ Database connection successful!\n\n";

// Start transaction
beginTransaction();

try {
    // ============================================
    // MIGRATE CATEGORIES
    // ============================================
    echo "Migrating categories...\n";
    $categoryMap = []; // Map old category IDs to new entity IDs
    
    foreach ($categories as $category) {
        // Insert category entity
        $entityId = dbInsert('catalog_category_entity', [
            'category_slug' => $category['id']
        ]);
        
        if ($entityId) {
            // Insert category attributes
            dbInsert('catalog_category_attribute', [
                'entity_id' => $entityId,
                'name' => $category['name'],
                'icon' => $category['icon'],
                'description' => $category['description']
            ]);
            
            $categoryMap[$category['id']] = $entityId;
            echo "  ✓ Migrated category: {$category['name']}\n";
        }
    }
    echo "✓ Categories migrated: " . count($categoryMap) . "\n\n";
    
    // ============================================
    // MIGRATE BRANDS
    // ============================================
    echo "Migrating brands...\n";
    $brandMap = []; // Map old brand IDs to new brand IDs
    
    foreach ($brands as $brand) {
        // Insert brand entity
        $brandId = dbInsert('catalog_brand_entity', [
            'brand_slug' => $brand['id']
        ]);
        
        if ($brandId) {
            // Insert brand attributes
            dbInsert('catalog_brand_attribute', [
                'brand_id' => $brandId,
                'name' => $brand['name'],
                'logo' => $brand['logo'],
                'description' => $brand['description']
            ]);
            
            $brandMap[$brand['id']] = $brandId;
            echo "  ✓ Migrated brand: {$brand['name']}\n";
        }
    }
    echo "✓ Brands migrated: " . count($brandMap) . "\n\n";
    
    // ============================================
    // MIGRATE PRODUCTS
    // ============================================
    echo "Migrating products...\n";
    $productCount = 0;
    
    foreach ($products as $product) {
        // Generate SKU from product ID and name
        $sku = 'PROD-' . str_pad($product['id'], 6, '0', STR_PAD_LEFT);
        
        // Insert product entity
        $productId = dbInsert('catalog_product_entity', [
            'sku' => $sku,
            'name' => $product['name']
        ]);
        
        if ($productId) {
            // Insert product attributes
            dbInsert('catalog_product_attribute', [
                'product_id' => $productId,
                'price' => $product['price'],
                'original_price' => $product['original_price'] ?? $product['price'],
                'discount_percent' => $product['discount_percent'] ?? 0,
                'shipping_type' => $product['shipping_type'] ?? 'Express',
                'rating' => $product['rating'] ?? 0,
                'reviews_count' => $product['reviews_count'] ?? 0,
                'stock' => $product['stock'] ?? 0,
                'image' => $product['image'] ?? '',
                'description' => $product['description'] ?? '',
                'specs' => json_encode($product['specs'] ?? []),
                'variants' => json_encode($product['variants'] ?? []),
                'featured' => $product['featured'] ?? false ? 'true' : 'false',
                'tags' => json_encode($product['tags'] ?? []),
                'category_id' => $product['category'] ?? null,
                'brand_id' => strtolower($product['brand']) ?? null
            ]);
            
            // Map product to category
            if (isset($product['category']) && isset($categoryMap[$product['category']])) {
                dbInsert('catalog_category_products', [
                    'category_id' => $categoryMap[$product['category']],
                    'product_id' => $productId,
                    'position' => $productCount
                ]);
            }
            
            $productCount++;
            if ($productCount % 10 == 0) {
                echo "  ✓ Migrated $productCount products...\n";
            }
        }
    }
    echo "✓ Products migrated: $productCount\n\n";
    
    // ============================================
    // MIGRATE USERS
    // ============================================
    echo "Migrating users...\n";
    $userMap = []; // Map old user IDs to new user IDs
    
    // Load users from JSON file
    $usersFile = __DIR__ . '/../data/users_db.json';
    $migratedUsersFile = __DIR__ . '/../data/users_db.json.migrated';
    
    // Check for original file first, then migrated file
    if (file_exists($usersFile)) {
        $usersData = json_decode(file_get_contents($usersFile), true);
        if ($usersData && is_array($usersData)) {
            foreach ($usersData as $user) {
                $newUserId = dbInsert('users', [
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'name' => $user['name']
                ]);
                
                if ($newUserId) {
                    $userMap[$user['user_id']] = $newUserId;
                    echo "  ✓ Migrated user: {$user['email']}\n";
                }
            }
        }
    } elseif (file_exists($migratedUsersFile)) {
        echo "  ℹ Users already migrated (found .migrated file). Skipping...\n";
    } else {
        echo "  ℹ No user data file found. Skipping user migration...\n";
    }
    echo "✓ Users migrated: " . count($userMap) . "\n\n";
    
    // ============================================
    // MIGRATE ORDERS
    // ============================================
    echo "Migrating orders...\n";
    $orderCount = 0;
    
    // Load orders from JSON file
    $ordersFile = __DIR__ . '/../data/orders_db.json';
    $migratedOrdersFile = __DIR__ . '/../data/orders_db.json.migrated';
    
    // Check for original file first, then migrated file
    if (file_exists($ordersFile)) {
        $ordersData = json_decode(file_get_contents($ordersFile), true);
        if ($ordersData && is_array($ordersData)) {
            foreach ($ordersData as $order) {
                // Map old user ID to new user ID
                $newUserId = $userMap[$order['user_id']] ?? null;
                
                // Insert order
                $orderId = dbInsert('sales_order', [
                    'user_id' => $newUserId,
                    'order_number' => $order['order_id'],
                    'subtotal' => $order['subtotal'],
                    'shipping_type' => $order['shipping_method'] ?? 'Standard',
                    'shipping_cost' => $order['shipping'] ?? 0,
                    'tax' => $order['tax'] ?? 0,
                    'discount' => $order['discount'] ?? 0,
                    'final_amount' => $order['total'],
                    'status' => $order['status'] ?? 'completed',
                    'created_at' => $order['date'] ?? date('Y-m-d H:i:s')
                ]);
                
                if ($orderId) {
                    // Insert order items
                    if (isset($order['items']) && is_array($order['items'])) {
                        foreach ($order['items'] as $item) {
                            // Find product by old ID
                            $productSku = 'PROD-' . str_pad($item['product_id'], 6, '0', STR_PAD_LEFT);
                            $productData = fetchOne(
                                "SELECT product_id FROM catalog_product_entity WHERE sku = :sku",
                                ['sku' => $productSku]
                            );
                            
                            if ($productData) {
                                dbInsert('sales_order_product', [
                                    'order_id' => $orderId,
                                    'product_id' => $productData['product_id'],
                                    'product_name' => $item['product_name'],
                                    'quantity' => $item['quantity'],
                                    'price' => $item['price'],
                                    'variant_data' => json_encode($item['variant'] ?? [])
                                ]);
                            }
                        }
                    }
                    
                    // Insert order address if available
                    if (isset($order['shipping_address'])) {
                        $addr = $order['shipping_address'];
                        dbInsert('sales_order_address', [
                            'order_id' => $orderId,
                            'full_name' => $addr['name'] ?? '',
                            'email' => $addr['email'] ?? '',
                            'phone' => $addr['phone'] ?? '',
                            'address_line1' => $addr['address'] ?? '',
                            'address_line2' => '',
                            'city' => $addr['city'] ?? '',
                            'state' => $addr['state'] ?? '',
                            'postal_code' => $addr['pincode'] ?? '',
                            'country' => $addr['country'] ?? 'IN'
                        ]);
                    }
                    
                    $orderCount++;
                }
            }
        }
    } elseif (file_exists($migratedOrdersFile)) {
        echo "  ℹ Orders already migrated (found .migrated file). Skipping...\n";
    } else {
        echo "  ℹ No order data file found. Skipping order migration...\n";
    }
    echo "✓ Orders migrated: $orderCount\n\n";
    
    // Commit transaction
    commitTransaction();
    
    echo "========================================\n";
    echo "✓ Migration completed successfully!\n";
    echo "========================================\n\n";
    echo "Summary:\n";
    echo "  - Categories: " . count($categoryMap) . "\n";
    echo "  - Brands: " . count($brandMap) . "\n";
    echo "  - Products: $productCount\n";
    echo "  - Users: " . count($userMap) . "\n";
    echo "  - Orders: $orderCount\n";
    echo "\n";
    
} catch (Exception $e) {
    rollbackTransaction();
    echo "\n❌ ERROR: Migration failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>
