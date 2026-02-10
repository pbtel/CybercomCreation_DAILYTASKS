<?php

require_once __DIR__ . '/../core/Core_Collection.php';

class Collection_Product extends Core_Collection
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Product';
        $this->_modelName = 'Model_Product';
    }

    /**
     * Re-implementing the rich fetch logic with joins
     */
    public function load()
    {
        if ($this->_isLoaded)
            return $this;

        $sql = "SELECT 
                    pe.entity_id as id,
                    pe.url_slug as slug,
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
                    pe.is_active as featured,
                    pe.is_featured
                FROM catalog_product_entity pe
                LEFT JOIN catalog_product_image pi ON pe.entity_id = pi.product_id AND pi.is_primary = true
                LEFT JOIN catalog_category_products ccp ON pe.entity_id = ccp.product_id
                LEFT JOIN catalog_category_entity ce ON ccp.category_id = ce.entity_id
                LEFT JOIN catalog_brand_entity be ON pe.brand_id = be.entity_id";

        $params = [];
        $pIdx = 1;

        // Apply filters (simple mapping for now)
        if (!empty($this->_filters)) {
            $whereParts = [];
            foreach ($this->_filters as $filter) {
                // Map fields to table aliases if needed
                $field = $filter['field'];
                if ($field == 'entity_id')
                    $field = 'pe.entity_id';
                if ($field == 'url_slug')
                    $field = 'pe.url_slug';
                if ($field == 'is_active')
                    $field = 'pe.is_active';
                if ($field == 'is_featured')
                    $field = 'pe.is_featured';
                if ($field == 'category_id')
                    $field = 'ccp.category_id';
                if ($field == 'category')
                    $field = 'ce.category_slug';
                if ($field == 'brand_id')
                    $field = 'pe.brand_id';
                if ($field == 'brand')
                    $field = 'be.brand_slug';
                if ($field == 'name')
                    $field = 'pe.name';
                if ($field == 'description')
                    $field = 'pe.description';

                if ($filter['condition'] === 'LIKE') {
                    $whereParts[] = "({$field} ILIKE \${$pIdx})";
                    $params[] = $filter['value'];
                } else {
                    $whereParts[] = "{$field} {$filter['condition']} \${$pIdx}";
                    $params[] = $filter['value'];
                }
                $pIdx++;
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }

        if (!empty($this->_orders)) {
            $orderParts = [];
            foreach ($this->_orders as $order) {
                $orderParts[] = "{$order['field']} {$order['direction']}";
            }
            $sql .= " ORDER BY " . implode(', ', $orderParts);
        } else {
            $sql .= " ORDER BY pe.entity_id";
        }

        if ($this->_limit !== null) {
            $sql .= " LIMIT " . (int) $this->_limit;
        }
        if ($this->_offset !== null) {
            $sql .= " OFFSET " . (int) $this->_offset;
        }

        $result = $this->_db->query($sql, $params);
        $rows = $this->_db->fetchAll($result);

        require_once __DIR__ . '/Model_Product.php';
        foreach ($rows as $row) {
            $model = new Model_Product();
            $model->setData($row);
            // In the old model, formatProduct was called. 
            // We can call a method on the model to post-process.
            $model->afterLoad();
            $this->_items[] = $model;
        }

        $this->_isLoaded = true;
        return $this;
    }
}
