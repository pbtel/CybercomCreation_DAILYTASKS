<?php
/**
 * Brand Database Operations
 * Phase 6 - Database Integration
 */

require_once __DIR__ . '/db.php';

/**
 * Get all brands
 */
function getAllBrandsFromDB()
{
    $sql = "SELECT * FROM catalog_brand_entity ORDER BY name";
    return fetchAll($sql);
}

/**
 * Get brand by slug
 */
function getBrandBySlugFromDB($slug)
{
    $sql = "SELECT * FROM catalog_brand_entity WHERE brand_slug = :slug";
    return fetchOne($sql, [':slug' => $slug]);
}

/**
 * Get brand by ID
 */
function getBrandByIdFromDB($id)
{
    $sql = "SELECT * FROM catalog_brand_entity WHERE entity_id = :id";
    return fetchOne($sql, [':id' => $id]);
}

/**
 * Insert a new brand
 */
function insertBrand($brandData)
{
    return dbInsert('catalog_brand_entity', $brandData);
}

/**
 * Insert brand attribute
 */
function insertBrandAttribute($brandId, $attributeType, $attributeValue)
{
    return dbInsert('catalog_brand_attribute', [
        'brand_id' => $brandId,
        'attribute_type' => $attributeType,
        'attribute_value' => $attributeValue
    ]);
}
?>