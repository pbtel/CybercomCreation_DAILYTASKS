<?php

require_once __DIR__ . '/../core/Core_Resource.php';

class Resource_Brand extends Core_Resource
{
    protected function _construct()
    {
        $this->_tableName = 'catalog_brand_entity';
        $this->_primaryKey = 'entity_id';
    }
}
