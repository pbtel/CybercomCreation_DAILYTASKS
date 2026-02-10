<?php

require_once __DIR__ . '/../core/Core_Collection.php';

class Collection_Discount extends Core_Collection
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Discount';
        $this->_modelName = 'Model_Discount';
    }
}
