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
        // Load static data for rich attributes (variants, specs) if not in DB
        static $staticProducts = null;
        if ($staticProducts === null) {
            require __DIR__ . '/../../database/original_products.php';
            $staticProducts = $products; // from included file
        }

        // Handle potentially missing fields
        $product['specs'] = []; // Default empty
        $product['variants'] = []; // Default empty
        $product['tags'] = []; // Default empty

        // Merge with static data if ID matches
        foreach ($staticProducts as $sp) {
            if ($sp['id'] == $product['id']) {
                $product['variants'] = $sp['variants'] ?? [];
                $product['specs'] = $sp['specs'] ?? [];
                $product['tags'] = $sp['tags'] ?? [];
                break;
            }
        }

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
    /**
     * Create new product
     */
    public function createProduct($data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Get or Create Brand
            $brandId = null;
            if (!empty($data['brand'])) {
                $brandId = $this->getBrandIdByName($data['brand']);
            }

            // 2. Insert into catalog_product_entity
            $sql = "INSERT INTO catalog_product_entity 
                    (sku, name, brand_id, price, original_price, discount_percent, stock, description, shipping_type, is_active) 
                    VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10) 
                    RETURNING entity_id";

            $params = [
                $data['sku'],
                $data['name'],
                $brandId,
                $data['price'],
                $data['original_price'] ?? $data['price'],
                $data['discount_percent'] ?? 0,
                $data['stock'] ?? 0,
                $data['description'] ?? '',
                $data['shipping_type'] ?? 'standard',
                true // is_active
            ];

            $result = $this->db->query($sql, $params);
            $row = $this->db->fetch($result);
            $productId = $row['entity_id'];

            // 3. Handle Images
            if (!empty($data['image_url'])) {
                // Assuming image_url contains emoji or path
                $imgSql = "INSERT INTO catalog_product_image (product_id, image_emoji, is_primary) VALUES ($1, $2, true)";
                $this->db->query($imgSql, [$productId, $data['image_url']]);
            }

            // 4. Handle Categories
            if (!empty($data['category'])) {
                $categoryId = $this->getCategoryIdByName($data['category']);
                if ($categoryId) {
                    $catSql = "INSERT INTO catalog_category_products (category_id, product_id) VALUES ($1, $2)";
                    $this->db->query($catSql, [$categoryId, $productId]);
                }
            }

            $this->db->commit();
            return $productId;

        } catch (Exception $e) {
            $this->db->rollBack();
            // Log error or rethrow
            error_log("Error creating product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update existing product
     */
    public function updateProduct($productId, $data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Get or Create Brand
            $brandId = null;
            if (!empty($data['brand'])) {
                $brandId = $this->getBrandIdByName($data['brand']);
            }

            // 2. Update catalog_product_entity
            $sql = "UPDATE catalog_product_entity 
                    SET name = $1, brand_id = $2, price = $3, original_price = $4, 
                        discount_percent = $5, stock = $6, description = $7, updated_at = NOW()
                    WHERE entity_id = $8";

            $params = [
                $data['name'],
                $brandId,
                $data['price'],
                $data['original_price'] ?? $data['price'],
                $data['discount_percent'] ?? 0,
                $data['stock'] ?? 0,
                $data['description'] ?? '',
                $productId
            ];

            $this->db->query($sql, $params);

            // 3. Update Image if provided (simple replacement for primary)
            if (!empty($data['image_url'])) {
                // Check if exists
                $checkImg = $this->db->query("SELECT image_id FROM catalog_product_image WHERE product_id = $1 AND is_primary = true", [$productId]);
                if ($this->db->fetch($checkImg)) {
                    $this->db->query("UPDATE catalog_product_image SET image_emoji = $1 WHERE product_id = $2 AND is_primary = true", [$data['image_url'], $productId]);
                } else {
                    $this->db->query("INSERT INTO catalog_product_image (product_id, image_emoji, is_primary) VALUES ($1, $2, true)", [$productId, $data['image_url']]);
                }
            }

            // 4. Update Category
            if (!empty($data['category'])) {
                $categoryId = $this->getCategoryIdByName($data['category']);
                if ($categoryId) {
                    // Remove existing category associations (assuming single category ownership for simplicity in CSV flow)
                    $this->db->query("DELETE FROM catalog_category_products WHERE product_id = $1", [$productId]);

                    // Add new association
                    $catSql = "INSERT INTO catalog_category_products (category_id, product_id) VALUES ($1, $2)";
                    $this->db->query($catSql, [$categoryId, $productId]);
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get or Create Brand ID by Name
     */
    private function getBrandIdByName($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        $sql = "SELECT entity_id FROM catalog_brand_entity WHERE brand_slug = $1";
        $result = $this->db->query($sql, [$slug]);
        $row = $this->db->fetch($result);

        if ($row) {
            return $row['entity_id'];
        }

        // Create new
        $insertSql = "INSERT INTO catalog_brand_entity (brand_slug, name, is_active) VALUES ($1, $2, true) RETURNING entity_id";
        $result = $this->db->query($insertSql, [$slug, $name]);
        $row = $this->db->fetch($result);
        return $row['entity_id'];
    }

    /**
     * Get or Create Category ID by Name
     */
    private function getCategoryIdByName($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        $sql = "SELECT entity_id FROM catalog_category_entity WHERE category_slug = $1";
        $result = $this->db->query($sql, [$slug]);
        $row = $this->db->fetch($result);

        if ($row) {
            return $row['entity_id'];
        }

        // Create new
        $insertSql = "INSERT INTO catalog_category_entity (category_slug, name, is_active) VALUES ($1, $2, true) RETURNING entity_id";
        $result = $this->db->query($insertSql, [$slug, $name]);
        $row = $this->db->fetch($result);
        return $row['entity_id'];
    }

    /**
     * Get product by SKU
     */
    public function getBySku($sku)
    {
        $sql = "SELECT entity_id FROM catalog_product_entity WHERE sku = $1";
        $result = $this->db->query($sql, [$sku]);
        return $this->db->fetch($result);
    }
}

