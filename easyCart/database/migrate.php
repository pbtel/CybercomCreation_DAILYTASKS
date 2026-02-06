<?php
/**
 * Advanced Database Migration & Restoration Script
 * Optimized for Phase 10 - Schema Normalization
 * 
 * This script will:
 * 1. Reset the database by executing schema.sql
 * 2. Restore Brands, Categories, and Products from PHP snapshots
 * 3. Restore Users and Orders from JSON backups
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/products.php';
require_once __DIR__ . '/categories.php';
require_once __DIR__ . '/brands.php';
require_once __DIR__ . '/users.php';
require_once __DIR__ . '/cart.php';
require_once __DIR__ . '/orders.php';

echo "===========================================\n";
echo "EasyCart Database Rebuild - Clean State\n";
echo "===========================================\n\n";

try {
    // 1. Reset & Recreate Schema
    echo "[1/5] Recreating Database Schema...\n";
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("schema.sql not found!");
    }
    $schemaSql = file_get_contents($schemaFile);
    $db = getDBConnection();
    $db->exec($schemaSql);
    echo "✓ Schema rebuilt from scratch.\n\n";

    // 2. Restore Catalog
    echo "[2/5] Restoring Catalog (Brands, Categories, Products)...\n";
    $brandMap = [];
    $catMap = [];

    // Brands
    $brandsFile = __DIR__ . '/original_brands.php';
    if (file_exists($brandsFile)) {
        include $brandsFile;
        if (isset($brands) && is_array($brands)) {
            foreach ($brands as $b) {
                $inserted = insertBrand([
                    'brand_slug' => $b['id'],
                    'name' => $b['name'],
                    'logo' => $b['logo'],
                    'description' => $b['description']
                ]);
                $brandMap[$b['name']] = $inserted['entity_id'];
                echo "  ✓ Restored brand: {$b['name']}\n";
            }
        }
    }

    // Categories
    $categoriesFile = __DIR__ . '/original_categories.php';
    if (file_exists($categoriesFile)) {
        include $categoriesFile;
        if (isset($categories) && is_array($categories)) {
            foreach ($categories as $c) {
                $inserted = insertCategory([
                    'category_slug' => $c['id'],
                    'name' => $c['name'],
                    'icon' => $c['icon'],
                    'description' => $c['description']
                ]);
                $catMap[$c['id']] = $inserted['entity_id'];
                echo "  ✓ Restored category: {$c['name']}\n";
            }
        }
    }

    // Products
    $productsFile = __DIR__ . '/original_products.php';
    if (file_exists($productsFile)) {
        include $productsFile;
        if (isset($products) && is_array($products)) {
            foreach ($products as $p) {
                $insertedProduct = insertProduct([
                    'sku' => $p['sku'] ?? 'SKU-' . str_pad($p['id'], 5, '0', STR_PAD_LEFT),
                    'name' => $p['name'],
                    'brand_id' => $brandMap[$p['brand']] ?? null,
                    'price' => $p['price'],
                    'original_price' => $p['original_price'],
                    'discount_percent' => $p['discount_percent'],
                    'rating' => $p['rating'],
                    'reviews_count' => $p['reviews_count'],
                    'stock' => $p['stock'],
                    'description' => $p['description'],
                    'shipping_type' => $p['shipping_type']
                ]);

                $newPid = $insertedProduct['entity_id'];

                // Restore Relation: Category
                if (isset($catMap[$p['category']])) {
                    linkProductToCategory($newPid, $catMap[$p['category']]);
                }

                // Restore Image
                if (isset($p['image'])) {
                    insertProductImage($newPid, $p['image'], true);
                }

                // Restore Attributes (Specs and Variants)
                if (isset($p['specs']) && is_array($p['specs'])) {
                    foreach ($p['specs'] as $k => $v) {
                        insertProductAttribute($newPid, $k, $v);
                    }
                }
                if (isset($p['variants']) && is_array($p['variants'])) {
                    foreach ($p['variants'] as $type => $values) {
                        foreach ($values as $val) {
                            insertProductAttribute($newPid, $type, $val);
                        }
                    }
                }
                if (isset($p['tags']) && is_array($p['tags'])) {
                    foreach ($p['tags'] as $tag) {
                        insertProductAttribute($newPid, 'tag', $tag);
                    }
                }
            }
            echo "  ✓ Products & attribute relations restored.\n";
        }
    }
    echo "✓ Catalog restoration complete.\n\n";

    // 3. Migrate Users
    echo "[3/5] Migrating Users...\n";
    $usersFile = __DIR__ . '/../data/users_db.json';
    $userIdMap = [];
    if (file_exists($usersFile)) {
        $usersData = json_decode(file_get_contents($usersFile), true);
        if ($usersData) {
            foreach ($usersData as $u) {
                $insertedUser = createUserDB($u['email'], $u['password'], $u['name']);
                $userIdMap[$u['user_id']] = $insertedUser['entity_id'];
                echo "  ✓ Migrated user: {$u['email']}\n";
            }
        }
    } else {
        echo "  ! Skipping users (no JSON backup).\n";
    }

    // 4. Migrate Orders
    echo "\n[4/5] Migrating Order History...\n";
    $ordersFile = __DIR__ . '/../data/orders_db.json';
    if (file_exists($ordersFile)) {
        $ordersData = json_decode(file_get_contents($ordersFile), true);
        if ($ordersData) {
            foreach ($ordersData as $o) {
                $newOrder = createOrderDB([
                    'user_id' => $userIdMap[$o['user_id']] ?? null,
                    'subtotal' => $o['subtotal'] ?? 0,
                    'shipping_cost' => $o['shipping'] ?? 0,
                    'tax' => $o['tax'] ?? 0,
                    'discount_amount' => $o['discount'] ?? 0,
                    'final_amount' => $o['total'] ?? 0,
                    'status' => $o['status'] ?? 'pending'
                ]);

                // Order Items
                if (isset($o['items']) && is_array($o['items'])) {
                    foreach ($o['items'] as $item) {
                        $unitPrice = $item['price'] ?? 0;
                        $quantity = $item['quantity'] ?? 1;
                        addOrderItemDB($newOrder['order_id'], [
                            'product_name' => $item['product_name'] ?? $item['name'] ?? 'Unknown Product',
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'variant' => $item['variant'] ?? [],
                            'subtotal' => $item['subtotal'] ?? ($unitPrice * $quantity)
                        ]);
                    }
                }

                // Order Address
                if (isset($o['shipping_address'])) {
                    $sa = $o['shipping_address'];
                    addOrderAddressDB($newOrder['order_id'], [
                        'full_name' => $sa['name'] ?? 'Guest',
                        'phone' => $sa['phone'] ?? '',
                        'address_line1' => $sa['address'] ?? '',
                        'city' => $sa['city'] ?? '',
                        'state' => $sa['state'] ?? '',
                        'pincode' => $sa['pincode'] ?? '',
                        'country' => $sa['country'] ?? 'India'
                    ]);
                }

                // Order Billing
                if (isset($o['payment_method'])) {
                    addOrderBillingDB($newOrder['order_id'], [
                        'payment_method' => $o['payment_method'],
                        'payment_status' => $o['payment_status'] ?? 'completed',
                        'coupon_code' => $o['coupon_code'] ?? null
                    ]);
                }

                // Order Shipping Method
                if (isset($o['shipping_method'])) {
                    addOrderShippingMethodDB($newOrder['order_id'], [
                        'shipping_method' => $o['shipping_method'],
                        'shipping_type' => $o['shipping_type'] ?? 'Standard'
                    ]);
                }
            }
            echo "  ✓ Restored " . count($ordersData) . " order records.\n";
        }
    } else {
        echo "  ! Skipping orders (no JSON backup).\n";
    }

    echo "\n[5/5] Finalizing Cleanup...\n";
    echo "✓ Database state is now synchronized with the project schema.\n";

    echo "\n===========================================\n";
    echo "MIGRATION COMPLETED SUCCESSFULLY!\n";
    echo "===========================================\n";

} catch (Exception $e) {
    echo "\n✗ FATAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}