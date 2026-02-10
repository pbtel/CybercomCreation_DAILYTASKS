<?php

require_once __DIR__ . '/../core/Core_Resource.php';

class Resource_User extends Core_Resource
{
    protected function _construct()
    {
        $this->_tableName = 'customer_entity';
        $this->_primaryKey = 'entity_id';
    }
}
