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

        $sql = "SELECT * FROM {$this->_tableName} WHERE {$field} = $1";
        $result = $this->_db->query($sql, [$value]);
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

            $fields = [];
            $values = [];
            $i = 1;
            foreach ($data as $key => $val) {
                if (is_array($val))
                    $val = json_encode($val);
                $fields[] = "{$key} = \${$i}";
                $values[] = $val;
                $i++;
            }
            $values[] = $id;

            $sql = "UPDATE {$this->_tableName} SET " . implode(', ', $fields) . " WHERE {$pk} = \${$i}";
            $this->_db->query($sql, $values);
        } else {
            // Insert
            $keys = array_keys($data);
            $values = array_values($data);
            $placeholders = [];
            foreach ($values as $idx => &$val) {
                if (is_array($val))
                    $val = json_encode($val);
                $placeholders[] = '$' . ($idx + 1);
            }

            $sql = "INSERT INTO {$this->_tableName} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $placeholders) . ") RETURNING {$pk}";
            $result = $this->_db->query($sql, $values);
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
            $sql = "DELETE FROM {$this->_tableName} WHERE {$this->_primaryKey} = $1";
            $this->_db->query($sql, [$id]);
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
