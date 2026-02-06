<?php
/**
 * Category Database Operations
 * Phase 6 - Database Integration
 */

require_once __DIR__ . '/db.php';

/**
 * Get all categories with product counts
 */
function getAllCategoriesFromDB()
{
    $sql = "SELECT c.*, 
            (SELECT COUNT(*) FROM catalog_category_products cp WHERE cp.category_id = c.entity_id) as product_count
            FROM catalog_category_entity c
            ORDER BY c.entity_id";

    return fetchAll($sql);
}

/**
 * Get category by slug
 */
function getCategoryBySlugFromDB($slug)
{
    $sql = "SELECT c.*,
            (SELECT COUNT(*) FROM catalog_category_products cp WHERE cp.category_id = c.entity_id) as product_count
            FROM catalog_category_entity c
            WHERE c.category_slug = :slug";

    return fetchOne($sql, [':slug' => $slug]);
}

/**
 * Get category by ID
 */
function getCategoryByIdFromDB($id)
{
    $sql = "SELECT c.*,
            (SELECT COUNT(*) FROM catalog_category_products cp WHERE cp.category_id = c.entity_id) as product_count
            FROM catalog_category_entity c
            WHERE c.entity_id = :id";

    return fetchOne($sql, [':id' => $id]);
}

/**
 * Insert a new category
 */
function insertCategory($categoryData)
{
    return dbInsert('catalog_category_entity', $categoryData);
}

/**
 * Insert category attribute
 */
function insertCategoryAttribute($categoryId, $attributeType)
{
    return dbInsert('catalog_category_attribute', [
        'category_id' => $categoryId,
        'attribute_type' => $attributeType
    ]);
}
?>