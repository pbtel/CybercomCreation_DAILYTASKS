<?php

require_once __DIR__ . '/../core/Core_Resource.php';

class Resource_Category extends Core_Resource
{
    protected function _construct()
    {
        $this->_tableName = 'catalog_category_entity';
        $this->_primaryKey = 'entity_id';
    }
}
