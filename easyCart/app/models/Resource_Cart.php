<?php

require_once __DIR__ . '/../core/Core_Resource.php';

class Resource_Cart extends Core_Resource
{
    protected function _construct()
    {
        $this->_tableName = 'sales_cart';
        $this->_primaryKey = 'cart_id';
    }
}
