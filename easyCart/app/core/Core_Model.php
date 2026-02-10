<?php

abstract class Core_Model
{
    protected $_data = [];
    protected $_resource = null;
    protected $_resourceName = null;

    public function __construct()
    {
        $this->_init();
    }

    /**
     * Set resource name in child classes
     */
    abstract protected function _init();

    /**
     * Get the resource instance
     */
    protected function getResource()
    {
        if (!$this->_resource && $this->_resourceName) {
            require_once __DIR__ . '/../models/' . $this->_resourceName . '.php';
            $this->_resource = new $this->_resourceName();
        }
        return $this->_resource;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    public function getData($key = null)
    {
        if ($key === null) {
            return $this->_data;
        }
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Load model by primary key or specific field
     */
    public function load($value, $field = null)
    {
        $this->getResource()->load($this, $value, $field);
        return $this;
    }

    /**
     * Save model to database
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }

    /**
     * Delete model from database
     */
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }

    public function getId()
    {
        return $this->getData($this->getResource()->getPrimaryKey());
    }

    /**
     * Magic set/get
     */
    public function __call($method, $args)
    {
        $type = substr($method, 0, 3);
        $key = $this->_underscore(substr($method, 3));

        if ($type == 'get') {
            return $this->getData($key);
        } elseif ($type == 'set') {
            return $this->setData($key, isset($args[0]) ? $args[0] : null);
        }
        return null;
    }

    protected function _underscore($name)
    {
        return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
    }
}
