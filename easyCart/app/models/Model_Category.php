<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_Category extends Core_Model
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Category';
    }

    public function afterLoad()
    {
        $data = $this->getData();
        if (isset($data['entity_id'])) {
            $db = Database::getInstance();
            $countSql = "SELECT COUNT(*) as count FROM catalog_category_products WHERE category_id = $1";
            $countResult = $db->query($countSql, [$data['entity_id']]);
            $countRow = $db->fetch($countResult);
            $data['product_count'] = (int) ($countRow['count'] ?? 0);

            // Map icon/logo to image if not already done by SQL
            if (isset($data['icon']) && !isset($data['image'])) {
                $data['image'] = $data['icon'];
            }

            // For compatibility with templates expecting 'id' instead of 'category_slug'
            if (isset($data['category_slug'])) {
                $data['id'] = $data['category_slug'];
            }
        }
        $this->setData($data);
        return $this;
    }

    /**
     * Compatibility methods
     */
    public function getAll()
    {
        require_once 'Collection_Category.php';
        return (new Collection_Category())->addFieldToFilter('is_active', true)->getData();
    }
}
