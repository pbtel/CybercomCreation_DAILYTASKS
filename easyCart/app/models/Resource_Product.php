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
        $query = (new Query())
            ->select(['attribute_type', 'attribute_value'])
            ->from('catalog_product_attribute')
            ->where('product_id', $productId);

        $result = $this->_db->query((string) $query, $query->getParams());
        return $this->_db->fetchAll($result) ?? [];
    }

    /**
     * Fetch all images for a product
     */
    public function getImages($productId)
    {
        $query = (new Query())
            ->select('image_emoji as image')
            ->from('catalog_product_image')
            ->where('product_id', $productId)
            ->orderBy('image_id', 'ASC');

        $result = $this->_db->query((string) $query, $query->getParams());
        return $this->_db->fetchAll($result) ?? [];
    }
}
