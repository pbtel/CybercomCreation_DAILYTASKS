<?php

require_once __DIR__ . '/../core/Core_Resource.php';

class Resource_Coupon extends Core_Resource
{
    protected function _construct()
    {
        $this->_tableName = 'sales_coupon';
        $this->_primaryKey = 'coupon_id';
    }
}
