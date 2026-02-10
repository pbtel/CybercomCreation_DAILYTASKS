<?php

abstract class Core_Collection
{
    protected $_resource = null;
    protected $_resourceName = null;
    protected $_modelName = null;
    protected $_db = null;
    protected $_select = null;
    protected $_items = [];
    protected $_filters = [];
    protected $_orders = [];
    protected $_limit = null;
    protected $_offset = null;
    protected $_isLoaded = false;

    public function __construct()
    {
        $this->_db = Database::getInstance();
        $this->_init();
    }

    /**
     * Define resource and model in child classes
     */
    abstract protected function _init();

    protected function getResource()
    {
        if (!$this->_resource && $this->_resourceName) {
            require_once __DIR__ . '/../models/' . $this->_resourceName . '.php';
            $this->_resource = new $this->_resourceName();
        }
        return $this->_resource;
    }

    public function addFieldToFilter($field, $value, $condition = '=')
    {
        $this->_filters[] = ['field' => $field, 'value' => $value, 'condition' => $condition];
        return $this;
    }

    public function setOrder($field, $direction = 'ASC')
    {
        $this->_orders[] = ['field' => $field, 'direction' => $direction];
        return $this;
    }

    public function setPageSize($size)
    {
        $this->_limit = $size;
        return $this;
    }

    public function setCurPage($page)
    {
        if ($this->_limit) {
            $this->_offset = ($page - 1) * $this->_limit;
        }
        return $this;
    }

    /**
     * Build and execute the query
     */
    public function load()
    {
        if ($this->_isLoaded)
            return $this;

        $tableName = $this->getResource()->getTableName();
        $sql = "SELECT * FROM {$tableName}";
        $params = [];
        $pIdx = 1;

        // Apply filters
        if (!empty($this->_filters)) {
            $whereParts = [];
            foreach ($this->_filters as $filter) {
                if ($filter['condition'] === 'LIKE') {
                    $whereParts[] = "{$filter['field']} ILIKE \${$pIdx}";
                    $params[] = $filter['value'];
                } else {
                    $whereParts[] = "{$filter['field']} {$filter['condition']} \${$pIdx}";
                    $params[] = $filter['value'];
                }
                $pIdx++;
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }

        // Apply ordering
        if (!empty($this->_orders)) {
            $orderParts = [];
            foreach ($this->_orders as $order) {
                $orderParts[] = "{$order['field']} {$order['direction']}";
            }
            $sql .= " ORDER BY " . implode(', ', $orderParts);
        }

        // Apply limit/offset
        if ($this->_limit !== null) {
            $sql .= " LIMIT " . (int) $this->_limit;
        }
        if ($this->_offset !== null) {
            $sql .= " OFFSET " . (int) $this->_offset;
        }

        $result = $this->_db->query($sql, $params);
        $rows = $this->_db->fetchAll($result);

        require_once __DIR__ . '/../models/' . $this->_modelName . '.php';
        foreach ($rows as $row) {
            $model = new $this->_modelName();
            $model->setData($row);
            if (method_exists($model, 'afterLoad')) {
                $model->afterLoad();
            }
            $this->_items[] = $model;
        }

        $this->_isLoaded = true;
        return $this;
    }

    public function getItems()
    {
        $this->load();
        return $this->_items;
    }

    public function getData()
    {
        $this->load();
        $data = [];
        foreach ($this->_items as $item) {
            $data[] = $item->getData();
        }
        return $data;
    }

    public function getSize()
    {
        $tableName = $this->getResource()->getTableName();
        $sql = "SELECT COUNT(*) FROM {$tableName}";
        $params = [];
        $pIdx = 1;

        if (!empty($this->_filters)) {
            $whereParts = [];
            foreach ($this->_filters as $filter) {
                $whereParts[] = "{$filter['field']} {$filter['condition']} \${$pIdx}";
                $params[] = $filter['value'];
                $pIdx++;
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }

        $result = $this->_db->query($sql, $params);
        $row = $this->_db->fetch($result);
        return (int) ($row['count'] ?? 0);
    }
}
