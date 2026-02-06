<?php
/**
 * Products Data - EasyCart Phase 6
 * Database-backed product management
 * This file now fetches products from PostgreSQL database
 */

// Include database product functions
require_once __DIR__ . '/../database/products.php';

// Keep global $products for backward compatibility (will be populated from database)
$products = [];

/**
 * Initialize products from database
 */
function initializeProducts()
{
    global $products;
    if (empty($products)) {
        $dbProducts = getAllProductsFromDB();
        // Transform database format to match old array format
        $products = array_map(function ($p) {
            return transformProductFromDB($p);
        }, $dbProducts);
    }
}

/**
 * Transform database product format to legacy format
 */
function transformProductFromDB($dbProduct)
{
    $product = [
        'id' => $dbProduct['entity_id'],
        'name' => $dbProduct['name'],
        'price' => (float) $dbProduct['price'],
        'original_price' => (float) $dbProduct['original_price'],
        'discount_percent' => (int) $dbProduct['discount_percent'],
        'rating' => (float) $dbProduct['rating'],
        'reviews_count' => (int) $dbProduct['reviews_count'],
        'stock' => (int) $dbProduct['stock'],
        'description' => $dbProduct['description'],
        'shipping_type' => $dbProduct['shipping_type'],
        'featured' => false,
        'brand' => $dbProduct['brand_name'] ?? '',
        'specs' => $dbProduct['specs'] ?? [],
        'variants' => $dbProduct['variants'] ?? [],
        'tags' => $dbProduct['tags'] ?? []
    ];

    // Get primary image
    $product['image'] = '📦'; // Default image emoji
    if (!empty($dbProduct['images'])) {
        foreach ($dbProduct['images'] as $img) {
            if ($img['is_primary'] === 't' || $img['is_primary'] === true) {
                $product['image'] = $img['image_emoji'];
                break;
            }
        }
        if (!isset($product['image']) && !empty($dbProduct['images'])) {
            $product['image'] = $dbProduct['images'][0]['image_emoji'];
        }
    }

    // Get primary category
    $product['category'] = 'general'; // Default category
    if (!empty($dbProduct['categories'])) {
        $product['category'] = $dbProduct['categories'][0]['category_slug'];
    }

    return $product;
}

/**
 * Get product by ID
 */
function getProductById($id)
{
    $dbProduct = getProductByIdFromDB($id);
    if ($dbProduct) {
        return transformProductFromDB($dbProduct);
    }
    return null;
}

/**
 * Get products by category
 */
function getProductsByCategory($category)
{
    if ($category === 'all') {
        initializeProducts();
        global $products;
        return $products;
    }

    $dbProducts = getProductsByCategoryFromDB($category);
    return array_map(function ($p) {
        return transformProductFromDB($p);
    }, $dbProducts);
}

/**
 * Get featured products
 */
function getFeaturedProducts()
{
    $dbProducts = getFeaturedProductsFromDB();
    return array_map(function ($p) {
        return transformProductFromDB($p);
    }, $dbProducts);
}

/**
 * Get products by price range
 */
function getProductsByPriceRange($min, $max)
{
    initializeProducts();
    global $products;
    return array_filter($products, function ($product) use ($min, $max) {
        return $product['price'] >= $min && $product['price'] <= $max;
    });
}

/**
 * Get products by rating
 */
function getProductsByRating($minRating)
{
    initializeProducts();
    global $products;
    return array_filter($products, function ($product) use ($minRating) {
        return $product['rating'] >= $minRating;
    });
}

// Initialize products on file include
initializeProducts();
