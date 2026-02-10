<?php

require_once __DIR__ . '/../core/Core_Collection.php';

class Collection_Category extends Core_Collection
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Category';
        $this->_modelName = 'Model_Category';
    }

    public function load()
    {
        parent::load();

        // Post-process to add product counts if needed
        foreach ($this->_items as $item) {
            $item->afterLoad();
        }

        return $this;
    }
}
