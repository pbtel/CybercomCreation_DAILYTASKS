<?php
/**
 * Products Data - EasyCart Phase 6
 * Database-driven product management
 */

require_once __DIR__ . '/db.php';

/**
 * Get product by ID from database
 * @param int $productId
 * @return array|null
 */
function getProductById($productId) {
    $sql = "SELECT 
                pe.product_id as id,
                pe.sku,
                pe.name,
                pa.price,
                pa.original_price,
                pa.discount_percent,
                pa.shipping_type,
                pa.rating,
                pa.reviews_count,
                pa.stock,
                pa.image,
                pa.description,
                pa.specs,
                pa.variants,
                pa.featured,
                pa.tags,
                pa.category_id as category,
                pa.brand_id as brand
            FROM catalog_product_entity pe
            JOIN catalog_product_attribute pa ON pe.product_id = pa.product_id
            WHERE pe.product_id = :product_id";
    
    $product = fetchOne($sql, ['product_id' => $productId]);
    
    if ($product) {
        // Decode JSON fields
        $product['specs'] = json_decode($product['specs'], true) ?? [];
        $product['variants'] = json_decode($product['variants'], true) ?? [];
        $product['tags'] = json_decode($product['tags'], true) ?? [];
        $product['featured'] = $product['featured'] === 't' || $product['featured'] === true;
        
        // Convert numeric fields
        $product['id'] = (int)$product['id'];
        $product['price'] = (float)$product['price'];
        $product['original_price'] = (float)$product['original_price'];
        $product['discount_percent'] = (int)$product['discount_percent'];
        $product['rating'] = (float)$product['rating'];
        $product['reviews_count'] = (int)$product['reviews_count'];
        $product['stock'] = (int)$product['stock'];
    }
    
    return $product;
}

/**
 * Get all products from database
 * @return array
 */
function getAllProducts() {
    $sql = "SELECT 
                pe.product_id as id,
                pe.sku,
                pe.name,
                pa.price,
                pa.original_price,
                pa.discount_percent,
                pa.shipping_type,
                pa.rating,
                pa.reviews_count,
                pa.stock,
                pa.image,
                pa.description,
                pa.specs,
                pa.variants,
                pa.featured,
                pa.tags,
                pa.category_id as category,
                pa.brand_id as brand
            FROM catalog_product_entity pe
            JOIN catalog_product_attribute pa ON pe.product_id = pa.product_id
            ORDER BY pe.product_id";
    
    $products = fetchAll($sql);
    
    if ($products) {
        foreach ($products as &$product) {
            // Decode JSON fields
            $product['specs'] = json_decode($product['specs'], true) ?? [];
            $product['variants'] = json_decode($product['variants'], true) ?? [];
            $product['tags'] = json_decode($product['tags'], true) ?? [];
            $product['featured'] = $product['featured'] === 't' || $product['featured'] === true;
            
            // Convert numeric fields
            $product['id'] = (int)$product['id'];
            $product['price'] = (float)$product['price'];
            $product['original_price'] = (float)$product['original_price'];
            $product['discount_percent'] = (int)$product['discount_percent'];
            $product['rating'] = (float)$product['rating'];
            $product['reviews_count'] = (int)$product['reviews_count'];
            $product['stock'] = (int)$product['stock'];
        }
    }
    
    return $products ?? [];
}

/**
 * Get products by category
 * @param string $categoryId
 * @return array
 */
function getProductsByCategory($categoryId) {
    $sql = "SELECT 
                pe.product_id as id,
                pe.sku,
                pe.name,
                pa.price,
                pa.original_price,
                pa.discount_percent,
                pa.shipping_type,
                pa.rating,
                pa.reviews_count,
                pa.stock,
                pa.image,
                pa.description,
                pa.specs,
                pa.variants,
                pa.featured,
                pa.tags,
                pa.category_id as category,
                pa.brand_id as brand
            FROM catalog_product_entity pe
            JOIN catalog_product_attribute pa ON pe.product_id = pa.product_id
            WHERE pa.category_id = :category_id
            ORDER BY pe.product_id";
    
    $products = fetchAll($sql, ['category_id' => $categoryId]);
    
    if ($products) {
        foreach ($products as &$product) {
            // Decode JSON fields
            $product['specs'] = json_decode($product['specs'], true) ?? [];
            $product['variants'] = json_decode($product['variants'], true) ?? [];
            $product['tags'] = json_decode($product['tags'], true) ?? [];
            $product['featured'] = $product['featured'] === 't' || $product['featured'] === true;
            
            // Convert numeric fields
            $product['id'] = (int)$product['id'];
            $product['price'] = (float)$product['price'];
            $product['original_price'] = (float)$product['original_price'];
            $product['discount_percent'] = (int)$product['discount_percent'];
            $product['rating'] = (float)$product['rating'];
            $product['reviews_count'] = (int)$product['reviews_count'];
            $product['stock'] = (int)$product['stock'];
        }
    }
    
    return $products ?? [];
}

/**
 * Get products by brand
 * @param string $brandId
 * @return array
 */
function getProductsByBrand($brandId) {
    $sql = "SELECT 
                pe.product_id as id,
                pe.sku,
                pe.name,
                pa.price,
                pa.original_price,
                pa.discount_percent,
                pa.shipping_type,
                pa.rating,
                pa.reviews_count,
                pa.stock,
                pa.image,
                pa.description,
                pa.specs,
                pa.variants,
                pa.featured,
                pa.tags,
                pa.category_id as category,
                pa.brand_id as brand
            FROM catalog_product_entity pe
            JOIN catalog_product_attribute pa ON pe.product_id = pa.product_id
            WHERE LOWER(pa.brand_id) = LOWER(:brand_id)
            ORDER BY pe.product_id";
    
    $products = fetchAll($sql, ['brand_id' => $brandId]);
    
    if ($products) {
        foreach ($products as &$product) {
            // Decode JSON fields
            $product['specs'] = json_decode($product['specs'], true) ?? [];
            $product['variants'] = json_decode($product['variants'], true) ?? [];
            $product['tags'] = json_decode($product['tags'], true) ?? [];
            $product['featured'] = $product['featured'] === 't' || $product['featured'] === true;
            
            // Convert numeric fields
            $product['id'] = (int)$product['id'];
            $product['price'] = (float)$product['price'];
            $product['original_price'] = (float)$product['original_price'];
            $product['discount_percent'] = (int)$product['discount_percent'];
            $product['rating'] = (float)$product['rating'];
            $product['reviews_count'] = (int)$product['reviews_count'];
            $product['stock'] = (int)$product['stock'];
        }
    }
    
    return $products ?? [];
}

/**
 * Search products by name or description
 * @param string $query
 * @return array
 */
function searchProducts($query) {
    $sql = "SELECT 
                pe.product_id as id,
                pe.sku,
                pe.name,
                pa.price,
                pa.original_price,
                pa.discount_percent,
                pa.shipping_type,
                pa.rating,
                pa.reviews_count,
                pa.stock,
                pa.image,
                pa.description,
                pa.specs,
                pa.variants,
                pa.featured,
                pa.tags,
                pa.category_id as category,
                pa.brand_id as brand
            FROM catalog_product_entity pe
            JOIN catalog_product_attribute pa ON pe.product_id = pa.product_id
            WHERE LOWER(pe.name) LIKE LOWER(:query) 
               OR LOWER(pa.description) LIKE LOWER(:query)
            ORDER BY pe.product_id";
    
    $searchQuery = '%' . $query . '%';
    $products = fetchAll($sql, ['query' => $searchQuery]);
    
    if ($products) {
        foreach ($products as &$product) {
            // Decode JSON fields
            $product['specs'] = json_decode($product['specs'], true) ?? [];
            $product['variants'] = json_decode($product['variants'], true) ?? [];
            $product['tags'] = json_decode($product['tags'], true) ?? [];
            $product['featured'] = $product['featured'] === 't' || $product['featured'] === true;
            
            // Convert numeric fields
            $product['id'] = (int)$product['id'];
            $product['price'] = (float)$product['price'];
            $product['original_price'] = (float)$product['original_price'];
            $product['discount_percent'] = (int)$product['discount_percent'];
            $product['rating'] = (float)$product['rating'];
            $product['reviews_count'] = (int)$product['reviews_count'];
            $product['stock'] = (int)$product['stock'];
        }
    }
    
    return $products ?? [];
}

/**
 * Get featured products
 * @return array
 */
function getFeaturedProducts() {
    $sql = "SELECT 
                pe.product_id as id,
                pe.sku,
                pe.name,
                pa.price,
                pa.original_price,
                pa.discount_percent,
                pa.shipping_type,
                pa.rating,
                pa.reviews_count,
                pa.stock,
                pa.image,
                pa.description,
                pa.specs,
                pa.variants,
                pa.featured,
                pa.tags,
                pa.category_id as category,
                pa.brand_id as brand
            FROM catalog_product_entity pe
            JOIN catalog_product_attribute pa ON pe.product_id = pa.product_id
            WHERE pa.featured = true
            ORDER BY pe.product_id";
    
    $products = fetchAll($sql);
    
    if ($products) {
        foreach ($products as &$product) {
            // Decode JSON fields
            $product['specs'] = json_decode($product['specs'], true) ?? [];
            $product['variants'] = json_decode($product['variants'], true) ?? [];
            $product['tags'] = json_decode($product['tags'], true) ?? [];
            $product['featured'] = $product['featured'] === 't' || $product['featured'] === true;
            
            // Convert numeric fields
            $product['id'] = (int)$product['id'];
            $product['price'] = (float)$product['price'];
            $product['original_price'] = (float)$product['original_price'];
            $product['discount_percent'] = (int)$product['discount_percent'];
            $product['rating'] = (float)$product['rating'];
            $product['reviews_count'] = (int)$product['reviews_count'];
            $product['stock'] = (int)$product['stock'];
        }
    }
    
    return $products ?? [];
}

// For backward compatibility, create a $products variable
$products = getAllProducts();
?>
