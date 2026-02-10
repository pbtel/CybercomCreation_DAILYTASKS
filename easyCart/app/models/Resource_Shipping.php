<?php

require_once __DIR__ . '/../core/Core_Resource.php';

class Resource_Shipping extends Core_Resource
{
    protected function _construct()
    {
        $this->_tableName = 'shipping_methods';
        $this->_primaryKey = 'method_id';
    }
}
