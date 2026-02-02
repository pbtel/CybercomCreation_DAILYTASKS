<?php
/**
 * Brands Data - EasyCart Phase 6
 * Database-driven brand management
 */

require_once __DIR__ . '/db.php';

/**
 * Get brand by ID from database
 * @param string $brandId Brand slug
 * @return array|null
 */
function getBrandById($brandId) {
    $sql = "SELECT 
                be.brand_id,
                be.brand_slug as id,
                ba.name,
                ba.logo,
                ba.description
            FROM catalog_brand_entity be
            JOIN catalog_brand_attribute ba ON be.brand_id = ba.brand_id
            WHERE be.brand_slug = :brand_id";
    
    $brand = fetchOne($sql, ['brand_id' => $brandId]);
    
    if ($brand) {
        // Remove brand_id from result (keep only id which is the slug)
        unset($brand['brand_id']);
    }
    
    return $brand;
}

/**
 * Get all brands from database
 * @return array
 */
function getAllBrands() {
    $sql = "SELECT 
                be.brand_id,
                be.brand_slug as id,
                ba.name,
                ba.logo,
                ba.description
            FROM catalog_brand_entity be
            JOIN catalog_brand_attribute ba ON be.brand_id = ba.brand_id
            ORDER BY be.brand_id";
    
    $brands = fetchAll($sql);
    
    if ($brands) {
        foreach ($brands as &$brand) {
            // Remove brand_id from result
            unset($brand['brand_id']);
        }
    }
    
    return $brands ?? [];
}

/**
 * Get products by brand (uses products_db.php function)
 * @param string $brandId
 * @return array
 */
function getProductsByBrand($brandId) {
    // This function is defined in products_db.php
    // Include it if not already included
    if (!function_exists('getProductsByBrand')) {
        require_once __DIR__ . '/products_db.php';
    }
    return getProductsByBrand($brandId);
}

// For backward compatibility, create a $brands variable
$brands = getAllBrands();
?>
