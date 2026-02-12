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
        $query = (new Query())
            ->select('*')
            ->from($tableName);

        // Apply filters
        foreach ($this->_filters as $filter) {
            $query->where($filter['field'], $filter['value'], $filter['condition']);
        }

        // Apply ordering
        foreach ($this->_orders as $order) {
            $query->orderBy($order['field'], $order['direction']);
        }

        // Apply limit/offset
        if ($this->_limit !== null) {
            $query->limit($this->_limit);
        }
        if ($this->_offset !== null) {
            $query->offset($this->_offset);
        }

        $result = $this->_db->query((string) $query, $query->getParams());
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
        $query = (new Query())
            ->select('COUNT(*) as count')
            ->from($tableName);

        foreach ($this->_filters as $filter) {
            $query->where($filter['field'], $filter['value'], $filter['condition']);
        }

        $result = $this->_db->query((string) $query, $query->getParams());
        $row = $this->_db->fetch($result);
        return (int) ($row['count'] ?? 0);
    }
}
