<?php
/**
 * Product Database Operations
 * Phase 6 - Database Integration
 */

require_once __DIR__ . '/db.php';

/**
 * Get all products with their attributes and images
 */
function getAllProductsFromDB()
{
    $sql = "SELECT p.*, b.name as brand_name, b.brand_slug, b.logo as brand_logo
            FROM catalog_product_entity p
            LEFT JOIN catalog_brand_entity b ON p.brand_id = b.entity_id
            ORDER BY p.entity_id";

    $products = fetchAll($sql);

    // Enrich each product with attributes, images, categories, and tags
    foreach ($products as &$product) {
        $product['attributes'] = getProductAttributes($product['entity_id']);
        $product['images'] = getProductImages($product['entity_id']);
        $product['categories'] = getProductCategories($product['entity_id']);
        $product['tags'] = getProductTags($product['entity_id']);
        $product['variants'] = formatProductVariants($product['attributes']);
        $product['specs'] = formatProductSpecs($product['attributes']);
    }

    return $products;
}

/**
 * Get product by ID
 */
function getProductByIdFromDB($productId)
{
    $sql = "SELECT p.*, b.name as brand_name, b.brand_slug, b.logo as brand_logo
            FROM catalog_product_entity p
            LEFT JOIN catalog_brand_entity b ON p.brand_id = b.entity_id
            WHERE p.entity_id = :id";

    $product = fetchOne($sql, [':id' => $productId]);

    if ($product) {
        $product['attributes'] = getProductAttributes($product['entity_id']);
        $product['images'] = getProductImages($product['entity_id']);
        $product['categories'] = getProductCategories($product['entity_id']);
        $product['tags'] = getProductTags($product['entity_id']);
        $product['variants'] = formatProductVariants($product['attributes']);
        $product['specs'] = formatProductSpecs($product['attributes']);
    }

    return $product;
}

/**
 * Get products by category
 */
function getProductsByCategoryFromDB($categorySlug)
{
    $sql = "SELECT p.*, b.name as brand_name, b.brand_slug
            FROM catalog_product_entity p
            LEFT JOIN catalog_brand_entity b ON p.brand_id = b.entity_id
            INNER JOIN catalog_category_products cp ON p.entity_id = cp.product_id
            INNER JOIN catalog_category_entity c ON cp.category_id = c.entity_id
            WHERE c.category_slug = :slug
            ORDER BY p.entity_id";

    $products = fetchAll($sql, [':slug' => $categorySlug]);

    foreach ($products as &$product) {
        $product['attributes'] = getProductAttributes($product['entity_id']);
        $product['images'] = getProductImages($product['entity_id']);
        $product['variants'] = formatProductVariants($product['attributes']);
    }

    return $products;
}

/**
 * Get products by brand
 */
function getProductsByBrandFromDB($brandSlug)
{
    $sql = "SELECT p.*, b.name as brand_name, b.brand_slug
            FROM catalog_product_entity p
            INNER JOIN catalog_brand_entity b ON p.brand_id = b.entity_id
            WHERE b.brand_slug = :slug
            ORDER BY p.entity_id";

    $products = fetchAll($sql, [':slug' => $brandSlug]);

    foreach ($products as &$product) {
        $product['attributes'] = getProductAttributes($product['entity_id']);
        $product['images'] = getProductImages($product['entity_id']);
        $product['variants'] = formatProductVariants($product['attributes']);
    }

    return $products;
}

/**
 * Get featured products
 */
function getFeaturedProductsFromDB()
{
    $sql = "SELECT p.*, b.name as brand_name, b.brand_slug
            FROM catalog_product_entity p
            LEFT JOIN catalog_brand_entity b ON p.brand_id = b.entity_id
            WHERE p.is_active = TRUE
            ORDER BY p.created_at DESC
            LIMIT 10";

    $products = fetchAll($sql);

    foreach ($products as &$product) {
        $product['attributes'] = getProductAttributes($product['entity_id']);
        $product['images'] = getProductImages($product['entity_id']);
        $product['variants'] = formatProductVariants($product['attributes']);
    }

    return $products;
}

/**
 * Get product attributes
 */
function getProductAttributes($productId)
{
    $sql = "SELECT attribute_type, attribute_value
            FROM catalog_product_attribute
            WHERE product_id = :id";

    return fetchAll($sql, [':id' => $productId]);
}

/**
 * Get product images
 */
function getProductImages($productId)
{
    $sql = "SELECT image_emoji, is_primary
            FROM catalog_product_image
            WHERE product_id = :id
            ORDER BY is_primary DESC";

    return fetchAll($sql, [':id' => $productId]);
}

/**
 * Get product categories
 */
function getProductCategories($productId)
{
    $sql = "SELECT c.category_slug, c.name
            FROM catalog_category_entity c
            INNER JOIN catalog_category_products cp ON c.entity_id = cp.category_id
            WHERE cp.product_id = :id";

    return fetchAll($sql, [':id' => $productId]);
}

/**
 * Get product tags
 */
function getProductTags($productId)
{
    $sql = "SELECT attribute_value
            FROM catalog_product_attribute
            WHERE product_id = :id AND attribute_type = 'tag'";

    $tags = fetchAll($sql, [':id' => $productId]);
    return array_column($tags, 'attribute_value');
}

/**
 * Format product variants from attributes
 */
function formatProductVariants($attributes)
{
    $variants = [];

    foreach ($attributes as $attr) {
        if (in_array($attr['attribute_type'], ['color', 'size', 'storage', 'strap', 'weight', 'switch', 'capacity', 'format', 'set', 'version'])) {
            if (!isset($variants[$attr['attribute_type']])) {
                $variants[$attr['attribute_type']] = [];
            }
            $variants[$attr['attribute_type']][] = $attr['attribute_value'];
        }
    }

    return $variants;
}

/**
 * Format product specs from attributes
 */
function formatProductSpecs($attributes)
{
    $specs = [];

    foreach ($attributes as $attr) {
        if (!in_array($attr['attribute_type'], ['color', 'size', 'storage', 'strap', 'weight', 'switch', 'capacity', 'format', 'set', 'version', 'tag'])) {
            $specs[$attr['attribute_type']] = $attr['attribute_value'];
        }
    }

    return $specs;
}

/**
 * Insert a new product
 */
function insertProduct($productData)
{
    return dbInsert('catalog_product_entity', $productData);
}

/**
 * Insert product attribute
 */
function insertProductAttribute($productId, $attributeType, $attributeValue)
{
    return dbInsert('catalog_product_attribute', [
        'product_id' => $productId,
        'attribute_type' => $attributeType,
        'attribute_value' => $attributeValue
    ]);
}

/**
 * Insert product image
 */
function insertProductImage($productId, $imageEmoji, $isPrimary = false)
{
    return dbInsert('catalog_product_image', [
        'product_id' => $productId,
        'image_emoji' => $imageEmoji,
        'is_primary' => $isPrimary ? 'true' : 'false'
    ]);
}

/**
 * Link product to category
 */
function linkProductToCategory($productId, $categoryId)
{
    return dbInsert('catalog_category_products', [
        'product_id' => $productId,
        'category_id' => $categoryId
    ]);
}
