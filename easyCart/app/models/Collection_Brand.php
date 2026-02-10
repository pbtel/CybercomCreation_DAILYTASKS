<?php

require_once __DIR__ . '/../core/Core_Collection.php';

class Collection_Brand extends Core_Collection
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Brand';
        $this->_modelName = 'Model_Brand';
    }
}
