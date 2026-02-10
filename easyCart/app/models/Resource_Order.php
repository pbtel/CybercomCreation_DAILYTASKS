<?php

require_once __DIR__ . '/../core/Core_Resource.php';

class Resource_Order extends Core_Resource
{
    protected function _construct()
    {
        $this->_tableName = 'sales_order';
        $this->_primaryKey = 'order_id';
    }
}
