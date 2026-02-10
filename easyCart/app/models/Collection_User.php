<?php

require_once __DIR__ . '/../core/Core_Collection.php';

class Collection_User extends Core_Collection
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_User';
        $this->_modelName = 'Model_User';
    }
}
