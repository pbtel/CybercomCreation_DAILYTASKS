<?php

/**
 * Category Model
 * Handles all category-related operations
 */
class CategoryModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get category by ID (slug)
     */
    public function getById($categoryId) {
        $sql = "SELECT 
                    ce.entity_id,
                    ce.category_slug as id,
                    ca.name,
                    ca.image,
                    ca.description
                FROM catalog_category_entity ce
                JOIN catalog_category_attribute ca ON ce.entity_id = ca.entity_id
                WHERE ce.category_slug = $1";
        
        $result = $this->db->query($sql, [$categoryId]);
        $category = $this->db->fetch($result);
        
        if ($category) {
            // Get product count
            $countSql = "SELECT COUNT(*) as count 
                         FROM catalog_category_products ccp
                         WHERE ccp.category_id = $1";
            $countResult = $this->db->query($countSql, [$category['entity_id']]);
            $countRow = $this->db->fetch($countResult);
            $category['product_count'] = (int)($countRow['count'] ?? 0);
            
            unset($category['entity_id']);
        }
        
        return $category;
    }

    /**
     * Get all categories
     */
    public function getAll() {
        $sql = "SELECT 
                    ce.entity_id,
                    ce.category_slug as id,
                    ca.name,
                    ca.image,
                    ca.description
                FROM catalog_category_entity ce
                JOIN catalog_category_attribute ca ON ce.entity_id = ca.entity_id
                ORDER BY ce.entity_id";
        
        $result = $this->db->query($sql);
        $categories = $this->db->fetchAll($result);
        
        if ($categories) {
            foreach ($categories as &$category) {
                // Get product count
                $countSql = "SELECT COUNT(*) as count 
                             FROM catalog_category_products ccp
                             WHERE ccp.category_id = $1";
                $countResult = $this->db->query($countSql, [$category['entity_id']]);
                $countRow = $this->db->fetch($countResult);
                $category['product_count'] = (int)($countRow['count'] ?? 0);
                
                unset($category['entity_id']);
            }
        }
        
        return $categories ?? [];
    }
}
