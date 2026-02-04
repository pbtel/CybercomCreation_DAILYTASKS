<?php
/**
 * Categories Data - EasyCart Phase 6
 * Database-driven category management
 */

require_once __DIR__ . '/db.php';

/**
 * Get category by ID from database
 * @param string $categoryId Category slug
 * @return array|null
 */
function getCategoryById($categoryId) {
    $sql = "SELECT 
                ce.entity_id,
                ce.category_slug as id,
                ca.name,
                ca.image,
                ca.description
            FROM catalog_category_entity ce
            JOIN catalog_category_attribute ca ON ce.entity_id = ca.entity_id
            WHERE ce.category_slug = :category_id";
    
    $category = fetchOne($sql, ['category_id' => $categoryId]);
    
    if ($category) {
        // Get product count
        $countSql = "SELECT COUNT(*) as count 
                     FROM catalog_category_products ccp
                     WHERE ccp.category_id = :entity_id";
        $countResult = fetchOne($countSql, ['entity_id' => $category['entity_id']]);
        $category['product_count'] = (int)($countResult['count'] ?? 0);
        
        // Remove entity_id from result (keep only id which is the slug)
        unset($category['entity_id']);
    }
    
    return $category;
}

/**
 * Get all categories from database
 * @return array
 */
function getAllCategories() {
    $sql = "SELECT 
                ce.entity_id,
                ce.category_slug as id,
                ca.name,
                ca.image,
                ca.description
            FROM catalog_category_entity ce
            JOIN catalog_category_attribute ca ON ce.entity_id = ca.entity_id
            ORDER BY ce.entity_id";
    
    $categories = fetchAll($sql);
    
    if ($categories) {
        foreach ($categories as &$category) {
            // Get product count for each category
            $countSql = "SELECT COUNT(*) as count 
                         FROM catalog_category_products ccp
                         WHERE ccp.category_id = :entity_id";
            $countResult = fetchOne($countSql, ['entity_id' => $category['entity_id']]);
            $category['product_count'] = (int)($countResult['count'] ?? 0);
            
            // Remove entity_id from result
            unset($category['entity_id']);
        }
    }
    
    return $categories ?? [];
}

// For backward compatibility, create a $categories variable
$categories = getAllCategories();
?>
