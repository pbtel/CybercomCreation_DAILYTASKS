<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_Brand extends Core_Model
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Brand';
    }

    public function afterLoad()
    {
        $data = $this->getData();
        if (isset($data['brand_slug'])) {
            $data['id'] = $data['brand_slug'];
        }
        if (isset($data['logo']) && !isset($data['image'])) {
            $data['image'] = $data['logo'];
        }
        $this->setData($data);
        return $this;
    }

    /**
     * Compatibility methods
     */
    public function getAll()
    {
        require_once 'Collection_Brand.php';
        return (new Collection_Brand())->addFieldToFilter('is_active', true)->getData();
    }
}
