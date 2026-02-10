<?php

require_once __DIR__ . '/../core/Core_Collection.php';

class Collection_Coupon extends Core_Collection
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Coupon';
        $this->_modelName = 'Model_Coupon';
    }
}
