<?php

/**
 * Product Model
 * Handles all product-related database operations
 */
class ProductModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get product by ID
     */
    public function getById($productId) {
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
                WHERE pe.product_id = $1";
        
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
    public function getAll() {
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
    public function getByCategory($categoryId) {
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
                WHERE pa.category_id = $1
                ORDER BY pe.product_id";
        
        $result = $this->db->query($sql, [$categoryId]);
        $products = $this->db->fetchAll($result);
        
        if ($products) {
            return array_map([$this, 'formatProduct'], $products);
        }
        
        return [];
    }

    /**
     * Get products by brand
     */
    public function getByBrand($brandId) {
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
                WHERE LOWER(pa.brand_id) = LOWER($1)
                ORDER BY pe.product_id";
        
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
    public function getFeatured() {
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
    public function search($query) {
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
                WHERE LOWER(pe.name) LIKE LOWER($1) 
                   OR LOWER(pa.description) LIKE LOWER($1)
                ORDER BY pe.product_id";
        
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
    public function getImages($productId) {
        $sql = "SELECT * FROM product_images WHERE product_id = $1 ORDER BY position ASC";
        $result = $this->db->query($sql, [$productId]);
        return $this->db->fetchAll($result) ?? [];
    }

    /**
     * Format product data
     */
    private function formatProduct($product) {
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
        
        return $product;
    }
}
