<?php
/**
 * Categories Data - EasyCart Phase 6
 * Database-backed category management
 */

// Include database category functions
require_once __DIR__ . '/../database/categories.php';

// Keep global $categories for backward compatibility
$categories = [];

/**
 * Initialize categories from database
 */
function initializeCategories()
{
    global $categories;
    if (empty($categories)) {
        $dbCategories = getAllCategoriesFromDB();
        $categories = array_map(function ($c) {
            return [
                'id' => $c['category_slug'],
                'name' => $c['name'],
                'icon' => $c['icon'],
                'description' => $c['description'],
                'product_count' => (int) $c['product_count']
            ];
        }, $dbCategories);
    }
}

/**
 * Get category by ID (slug)
 */
function getCategoryById($id)
{
    $dbCategory = getCategoryBySlugFromDB($id);
    if ($dbCategory) {
        return [
            'id' => $dbCategory['category_slug'],
            'name' => $dbCategory['name'],
            'icon' => $dbCategory['icon'],
            'description' => $dbCategory['description'],
            'product_count' => (int) $dbCategory['product_count']
        ];
    }
    return null;
}

/**
 * Get all categories with product counts
 */
function getAllCategories()
{
    global $products;
    initializeCategories();
    global $categories;
    return $categories;
}

// Initialize categories on file include
initializeCategories();
?>