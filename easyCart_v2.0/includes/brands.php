<?php
/**
 * Brands Data - EasyCart Phase 6
 * Database-backed brand management
 */

// Include database brand functions
require_once __DIR__ . '/../database/brands.php';

// Keep global $brands for backward compatibility
$brands = [];

/**
 * Initialize brands from database
 */
function initializeBrands()
{
    global $brands;
    if (empty($brands)) {
        $dbBrands = getAllBrandsFromDB();
        $brands = array_map(function ($b) {
            return [
                'id' => $b['brand_slug'],
                'name' => $b['name'],
                'logo' => $b['logo'],
                'description' => $b['description']
            ];
        }, $dbBrands);
    }
}

/**
 * Get brand by ID (slug)
 */
function getBrandById($id)
{
    $dbBrand = getBrandBySlugFromDB($id);
    if ($dbBrand) {
        return [
            'id' => $dbBrand['brand_slug'],
            'name' => $dbBrand['name'],
            'logo' => $dbBrand['logo'],
            'description' => $dbBrand['description']
        ];
    }
    return null;
}

/**
 * Get all brands
 */
function getAllBrands()
{
    initializeBrands();
    global $brands;
    return $brands;
}

/**
 * Get products by brand
 */
function getProductsByBrand($brandId)
{
    require_once __DIR__ . '/../database/products.php';
    $dbProducts = getProductsByBrandFromDB($brandId);

    // Transform to legacy format
    require_once __DIR__ . '/products.php';
    return array_map(function ($p) {
        return transformProductFromDB($p);
    }, $dbProducts);
}

// Initialize brands on file include
initializeBrands();
?>