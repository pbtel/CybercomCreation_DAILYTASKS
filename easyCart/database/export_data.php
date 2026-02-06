<?php
/**
 * PostgreSQL Data Exporter / Migration Backup Script
 * This script analyzes the current data in PostgreSQL and generates
 * a portable migration file to restore the data later.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

echo "Analyzing PostgreSQL database: " . DB_NAME . "...\n";

try {
    $db = getDBConnection();
    $backupData = [];

    // 1. Fetch Brands
    echo "Exporting Brands...\n";
    $backupData['brands'] = fetchAll("SELECT brand_slug as id, name, logo, description FROM catalog_brand_entity");

    // 2. Fetch Categories
    echo "Exporting Categories...\n";
    $backupData['categories'] = fetchAll("SELECT category_slug as id, name, icon, description FROM catalog_category_entity");

    // 3. Fetch Products (Complex export with attributes)
    echo "Exporting Products...\n";
    $rawProducts = fetchAll("SELECT p.*, b.name as brand_name FROM catalog_product_entity p LEFT JOIN catalog_brand_entity b ON p.brand_id = b.entity_id");

    $exportedProducts = [];
    foreach ($rawProducts as $p) {
        $pid = $p['entity_id'];

        // Get attributes for this product
        $attrs = fetchAll("SELECT attribute_type, attribute_value FROM catalog_product_attribute WHERE product_id = :id", [':id' => $pid]);

        $specs = [];
        $variants = [];
        $tags = [];

        foreach ($attrs as $a) {
            $type = $a['attribute_type'];
            $val = $a['attribute_value'];

            if ($type === 'tag') {
                $tags[] = $val;
            } elseif (in_array($type, ['color', 'size', 'storage', 'strap', 'weight', 'switch', 'capacity', 'format', 'set', 'version'])) {
                $variants[$type][] = $val;
            } else {
                $specs[$type] = $val;
            }
        }

        // Get primary image
        $img = fetchOne("SELECT image_emoji FROM catalog_product_image WHERE product_id = :id AND is_primary = true", [':id' => $pid]);

        // Get first category
        $cat = fetchOne("SELECT c.category_slug FROM catalog_category_entity c JOIN catalog_category_products cp ON c.entity_id = cp.category_id WHERE cp.product_id = :id", [':id' => $pid]);

        $exportedProducts[] = [
            'id' => $pid,
            'name' => $p['name'],
            'brand' => $p['brand_name'],
            'price' => (float) $p['price'],
            'original_price' => (float) $p['original_price'],
            'discount_percent' => (int) $p['discount_percent'],
            'rating' => (float) $p['rating'],
            'reviews_count' => (int) $p['reviews_count'],
            'stock' => (int) $p['stock'],
            'description' => $p['description'],
            'shipping_type' => $p['shipping_type'],
            'image' => $img['image_emoji'] ?? '📦',
            'category' => $cat['category_slug'] ?? 'general',
            'specs' => $specs,
            'variants' => $variants,
            'tags' => $tags
        ];
    }
    $backupData['products'] = $exportedProducts;

    // Generate PHP files for migration
    echo "Generating migration source files...\n";

    file_put_contents(__DIR__ . '/original_brands.php', "<?php\n\$brands = " . var_export($backupData['brands'], true) . ";\n");
    file_put_contents(__DIR__ . '/original_categories.php', "<?php\n\$categories = " . var_export($backupData['categories'], true) . ";\n");
    file_put_contents(__DIR__ . '/original_products.php', "<?php\n\$products = " . var_export($backupData['products'], true) . ";\n");

    echo "✓ Success! Generated:\n";
    echo "  - database/original_brands.php\n";
    echo "  - database/original_categories.php\n";
    echo "  - database/original_products.php\n";
    echo "\nYour migration script (migrate.php) will now be able to use these files to restore your data.\n";

} catch (Exception $e) {
    echo "✗ Error exporting data: " . $e->getMessage() . "\n";
}
