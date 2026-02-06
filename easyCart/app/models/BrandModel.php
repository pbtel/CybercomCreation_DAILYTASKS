<?php

/**
 * Brand Model
 * Handles all brand-related operations
 */
class BrandModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get brand by ID (slug)
     */
    public function getById($brandId) {
        $sql = "SELECT 
                    be.brand_id,
                    be.brand_slug as id,
                    ba.name,
                    ba.image,
                    ba.description
                FROM catalog_brand_entity be
                JOIN catalog_brand_attribute ba ON be.brand_id = ba.brand_id
                WHERE be.brand_slug = $1";
        
        $result = $this->db->query($sql, [$brandId]);
        $brand = $this->db->fetch($result);
        
        if ($brand) {
            unset($brand['brand_id']);
        }
        
        return $brand;
    }

    /**
     * Get all brands
     */
    public function getAll() {
        $sql = "SELECT 
                    be.brand_id,
                    be.brand_slug as id,
                    ba.name,
                    ba.image,
                    ba.description
                FROM catalog_brand_entity be
                JOIN catalog_brand_attribute ba ON be.brand_id = ba.brand_id
                ORDER BY be.brand_id";
        
        $result = $this->db->query($sql);
        $brands = $this->db->fetchAll($result);
        
        if ($brands) {
            foreach ($brands as &$brand) {
                unset($brand['brand_id']);
            }
        }
        
        return $brands ?? [];
    }
}
