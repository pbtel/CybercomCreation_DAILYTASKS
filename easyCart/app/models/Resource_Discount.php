<?php

require_once __DIR__ . '/../core/Core_Resource.php';

class Resource_Discount extends Core_Resource
{
    protected function _construct()
    {
        $this->_tableName = 'product_discounts';
        $this->_primaryKey = 'discount_id';
    }
}
