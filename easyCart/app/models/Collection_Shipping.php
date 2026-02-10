<?php

require_once __DIR__ . '/../core/Core_Collection.php';

class Collection_Shipping extends Core_Collection
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Shipping';
        $this->_modelName = 'Model_Shipping';
    }
}
