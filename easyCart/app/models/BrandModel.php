<?php

/**
 * Brand Model
 * Handles all brand-related operations
 */
class BrandModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get brand by ID (slug)
     */
    public function getById($brandId)
    {
        $sql = "SELECT 
                    entity_id,
                    brand_slug as id,
                    name,
                    logo as image,
                    description
                FROM catalog_brand_entity
                WHERE brand_slug = $1";

        $result = $this->db->query($sql, [$brandId]);
        $brand = $this->db->fetch($result);

        if ($brand) {
            unset($brand['entity_id']);
        }

        return $brand;
    }

    /**
     * Get all brands
     */
    public function getAll()
    {
        $sql = "SELECT 
                    entity_id,
                    brand_slug as id,
                    name,
                    logo as image,
                    description
                FROM catalog_brand_entity
                WHERE is_active = true
                ORDER BY entity_id";

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
