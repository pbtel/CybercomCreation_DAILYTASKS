<?php

abstract class Core_Resource
{
    protected $_tableName = null;
    protected $_primaryKey = null;
    protected $_db = null;

    public function __construct()
    {
        $this->_db = Database::getInstance();
        $this->_construct();
    }

    /**
     * Define table and primary key in child classes
     */
    abstract protected function _construct();

    /**
     * Load data into model
     */
    public function load(Core_Model $model, $value, $field = null)
    {
        if ($field === null) {
            $field = $this->_primaryKey;
        }

        $query = (new Query())
            ->select('*')
            ->from($this->_tableName)
            ->where($field, $value);

        $result = $this->_db->query((string) $query, $query->getParams());
        $data = $this->_db->fetch($result);

        if ($data) {
            $model->setData($data);
        }
        return $this;
    }

    /**
     * Save model data
     */
    public function save(Core_Model $model)
    {
        $data = $model->getData();
        $pk = $this->_primaryKey;

        if (isset($data[$pk]) && $data[$pk]) {
            // Update
            $id = $data[$pk];
            unset($data[$pk]);
            unset($data['created_at']); // Don't update creation time

            $data['updated_at'] = date('Y-m-d H:i:s');

            $query = (new Query())
                ->update($this->_tableName, $data)
                ->where($pk, $id);

            $this->_db->query((string) $query, $query->getParams());
        } else {
            // Insert
            $query = (new Query())
                ->insert($this->_tableName, $data)
                ->returning($pk);

            $result = $this->_db->query((string) $query, $query->getParams());
            $row = $this->_db->fetch($result);
            if ($row) {
                $model->setData($pk, $row[$pk]);
            }
        }
        return $this;
    }

    /**
     * Delete model data
     */
    public function delete(Core_Model $model)
    {
        $id = $model->getData($this->_primaryKey);
        if ($id) {
            $query = (new Query())
                ->delete($this->_tableName)
                ->where($this->_primaryKey, $id);
            $this->_db->query((string) $query, $query->getParams());
        }
        return $this;
    }

    public function getTableName()
    {
        return $this->_tableName;
    }
    public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }
}
