<?php

require_once __DIR__ . '/../core/Core_Resource.php';

class Resource_Product extends Core_Resource
{
    protected function _construct()
    {
        $this->_tableName = 'catalog_product_entity';
        $this->_primaryKey = 'entity_id';
    }

    /**
     * Custom load by slug
     */
    public function loadBySlug(Model_Product $model, $slug)
    {
        return $this->load($model, $slug, 'url_slug');
    }

    /**
     * Fetch all attributes for a product
     */
    public function getAttributes($productId)
    {
        $db = Database::getInstance();
        $sql = "SELECT attribute_type, attribute_value FROM catalog_product_attribute WHERE product_id = $1";
        $result = $db->query($sql, [$productId]);
        return $db->fetchAll($result) ?? [];
    }

    /**
     * Fetch all images for a product
     */
    public function getImages($productId)
    {
        $db = Database::getInstance();
        $sql = "SELECT image_emoji as image FROM catalog_product_image WHERE product_id = $1 ORDER BY image_id ASC";
        $result = $db->query($sql, [$productId]);
        return $db->fetchAll($result) ?? [];
    }
}
