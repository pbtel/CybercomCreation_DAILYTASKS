<?php

require_once __DIR__ . '/../core/Core_Collection.php';

class Collection_Order extends Core_Collection
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Order';
        $this->_modelName = 'Model_Order';
    }
}
