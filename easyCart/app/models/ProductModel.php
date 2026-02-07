<?php

/**
 * Product Model
 * Handles all product-related database operations
 */
class ProductModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get product by ID
     */
    public function getById($productId)
    {
        $sql = "SELECT 
                    pe.entity_id as id,
                    pe.sku,
                    pe.name,
                    pe.price,
                    pe.original_price,
                    pe.discount_percent,
                    pe.shipping_type,
                    pe.rating,
                    pe.reviews_count,
                    pe.stock,
                    pi.image_emoji as image,
                    pe.description,
                    be.brand_slug as brand,
                    ce.category_slug as category,
                    pe.is_active as featured
                FROM catalog_product_entity pe
                LEFT JOIN catalog_product_image pi ON pe.entity_id = pi.product_id AND pi.is_primary = true
                LEFT JOIN catalog_category_products ccp ON pe.entity_id = ccp.product_id
                LEFT JOIN catalog_category_entity ce ON ccp.category_id = ce.entity_id
                LEFT JOIN catalog_brand_entity be ON pe.brand_id = be.entity_id
                WHERE pe.entity_id = $1";

        $result = $this->db->query($sql, [$productId]);
        $product = $this->db->fetch($result);

        if ($product) {
            return $this->formatProduct($product);
        }

        return null;
    }

    /**
     * Get all products
     */
    public function getAll()
    {
        $sql = "SELECT 
                    pe.entity_id as id,
                    pe.sku,
                    pe.name,
                    pe.price,
                    pe.original_price,
                    pe.discount_percent,
                    pe.shipping_type,
                    pe.rating,
                    pe.reviews_count,
                    pe.stock,
                    pi.image_emoji as image,
                    pe.description,
                    be.brand_slug as brand,
                    ce.category_slug as category,
                    pe.is_active as featured
                FROM catalog_product_entity pe
                LEFT JOIN catalog_product_image pi ON pe.entity_id = pi.product_id AND pi.is_primary = true
                LEFT JOIN catalog_category_products ccp ON pe.entity_id = ccp.product_id
                LEFT JOIN catalog_category_entity ce ON ccp.category_id = ce.entity_id
                LEFT JOIN catalog_brand_entity be ON pe.brand_id = be.entity_id
                ORDER BY pe.entity_id";

        $result = $this->db->query($sql);
        $products = $this->db->fetchAll($result);

        if ($products) {
            return array_map([$this, 'formatProduct'], $products);
        }

        return [];
    }

    /**
     * Get products by category
     */
    public function getByCategory($categoryId)
    {
        $categorySql = "SELECT entity_id FROM catalog_category_entity WHERE category_slug = $1";
        $catResult = $this->db->query($categorySql, [$categoryId]);
        $catRow = $this->db->fetch($catResult);

        if (!$catRow)
            return [];
        $catId = $catRow['entity_id'];

        $sql = "SELECT 
                    pe.entity_id as id,
                    pe.sku,
                    pe.name,
                    pe.price,
                    pe.original_price,
                    pe.discount_percent,
                    pe.shipping_type,
                    pe.rating,
                    pe.reviews_count,
                    pe.stock,
                    pi.image_emoji as image,
                    pe.description,
                    be.brand_slug as brand,
                    ce.category_slug as category,
                    pe.is_active as featured
                FROM catalog_product_entity pe
                JOIN catalog_category_products ccp ON pe.entity_id = ccp.product_id
                LEFT JOIN catalog_product_image pi ON pe.entity_id = pi.product_id AND pi.is_primary = true
                LEFT JOIN catalog_category_entity ce ON ccp.category_id = ce.entity_id
                LEFT JOIN catalog_brand_entity be ON pe.brand_id = be.entity_id
                WHERE ccp.category_id = $1
                ORDER BY pe.entity_id";

        $result = $this->db->query($sql, [$catId]);
        $products = $this->db->fetchAll($result);

        if ($products) {
            return array_map([$this, 'formatProduct'], $products);
        }

        return [];
    }

    /**
     * Get products by brand
     */
    public function getByBrand($brandSlug)
    {
        $brandSql = "SELECT entity_id FROM catalog_brand_entity WHERE brand_slug = $1";
        $brandResult = $this->db->query($brandSql, [$brandSlug]);
        $brandRow = $this->db->fetch($brandResult);

        if (!$brandRow)
            return [];
        $brandId = $brandRow['entity_id'];

        $sql = "SELECT 
                    pe.entity_id as id,
                    pe.sku,
                    pe.name,
                    pe.price,
                    pe.original_price,
                    pe.discount_percent,
                    pe.shipping_type,
                    pe.rating,
                    pe.reviews_count,
                    pe.stock,
                    pi.image_emoji as image,
                    pe.description,
                    be.brand_slug as brand,
                    ce.category_slug as category,
                    pe.is_active as featured
                FROM catalog_product_entity pe
                LEFT JOIN catalog_product_image pi ON pe.entity_id = pi.product_id AND pi.is_primary = true
                LEFT JOIN catalog_category_products ccp ON pe.entity_id = ccp.product_id
                LEFT JOIN catalog_category_entity ce ON ccp.category_id = ce.entity_id
                LEFT JOIN catalog_brand_entity be ON pe.brand_id = be.entity_id
                WHERE pe.brand_id = $1
                ORDER BY pe.entity_id";

        $result = $this->db->query($sql, [$brandId]);
        $products = $this->db->fetchAll($result);

        if ($products) {
            return array_map([$this, 'formatProduct'], $products);
        }

        return [];
    }

    /**
     * Get featured products
     */
    public function getFeatured()
    {
        // Assuming 'is_active' or checking a specific attribute for 'featured'. 
        // Schema doesn't have explicit 'featured' column, but migrate.php uses 'is_active' or attribute.
        // migrate.php line 189: "WHERE pa.featured = true" implies attribute.
        // Since we migrated from Phase 9, let's assume all active products are "featured" for now to simplify,
        // or just return first 8.
        $sql = "SELECT 
                    pe.entity_id as id,
                    pe.sku,
                    pe.name,
                    pe.price,
                    pe.original_price,
                    pe.discount_percent,
                    pe.shipping_type,
                    pe.rating,
                    pe.reviews_count,
                    pe.stock,
                    pi.image_emoji as image,
                    pe.description,
                    be.brand_slug as brand,
                    ce.category_slug as category,
                    pe.is_active as featured
                FROM catalog_product_entity pe
                LEFT JOIN catalog_product_image pi ON pe.entity_id = pi.product_id AND pi.is_primary = true
                LEFT JOIN catalog_category_products ccp ON pe.entity_id = ccp.product_id
                LEFT JOIN catalog_category_entity ce ON ccp.category_id = ce.entity_id
                LEFT JOIN catalog_brand_entity be ON pe.brand_id = be.entity_id
                WHERE pe.is_active = true
                LIMIT 8";

        $result = $this->db->query($sql);
        $products = $this->db->fetchAll($result);

        if ($products) {
            return array_map([$this, 'formatProduct'], $products);
        }

        return [];
    }

    /**
     * Search products
     */
    public function search($query)
    {
        $sql = "SELECT 
                    pe.entity_id as id,
                    pe.sku,
                    pe.name,
                    pe.price,
                    pe.original_price,
                    pe.discount_percent,
                    pe.shipping_type,
                    pe.rating,
                    pe.reviews_count,
                    pe.stock,
                    pi.image_emoji as image,
                    pe.description,
                    pe.brand_id as brand,
                    ccp.category_id as category,
                    pe.is_active as featured
                FROM catalog_product_entity pe
                LEFT JOIN catalog_product_image pi ON pe.entity_id = pi.product_id AND pi.is_primary = true
                LEFT JOIN catalog_category_products ccp ON pe.entity_id = ccp.product_id
                LEFT JOIN catalog_category_entity ce ON ccp.category_id = ce.entity_id
                LEFT JOIN catalog_brand_entity be ON pe.brand_id = be.entity_id
                WHERE LOWER(pe.name) LIKE LOWER($1) 
                   OR LOWER(pe.description) LIKE LOWER($1)
                ORDER BY pe.entity_id";

        $searchQuery = '%' . $query . '%';
        $result = $this->db->query($sql, [$searchQuery]);
        $products = $this->db->fetchAll($result);

        if ($products) {
            return array_map([$this, 'formatProduct'], $products);
        }

        return [];
    }

    /**
     * Get product images
     */
    public function getImages($productId)
    {
        $sql = "SELECT image_emoji as image FROM catalog_product_image WHERE product_id = $1 ORDER BY image_id ASC";
        $result = $this->db->query($sql, [$productId]);
        return $this->db->fetchAll($result) ?? [];
    }

    /**
     * Get product variants
     */
    public function getVariants($productId)
    {
        // Placeholder for variants - can be expanded later
        return [];
    }

    /**
     * Format product data
     */
    private function formatProduct($product)
    {
        // Handle potentially missing fields
        $product['specs'] = []; // Default empty
        $product['variants'] = []; // Default empty
        $product['tags'] = []; // Default empty

        // Convert numeric fields
        $product['id'] = (int) $product['id'];
        $product['price'] = (float) $product['price'];
        $product['original_price'] = (float) ($product['original_price'] ?? $product['price']);
        $product['discount_percent'] = (int) ($product['discount_percent'] ?? 0);
        $product['rating'] = (float) ($product['rating'] ?? 0);
        $product['reviews_count'] = (int) ($product['reviews_count'] ?? 0);
        $product['stock'] = (int) ($product['stock'] ?? 0);

        return $product;
    }
}
