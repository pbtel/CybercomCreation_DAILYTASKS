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
                ba.image,
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
                ba.image,
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

// For backward compatibility, create a $brands variable
$brands = getAllBrands();
?>
