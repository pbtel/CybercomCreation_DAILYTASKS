<?php

/**
 * Common Query Builder System
 * Centralized class for dynamic SQL generation
 */
class Query
{
    protected $type = 'SELECT';
    protected $columns = [];
    protected $table = null;
    protected $where = [];
    protected $orderBy = [];
    protected $groupBy = [];
    protected $limit = null;
    protected $offset = null;
    protected $data = [];
    protected $returning = null;

    /**
     * Set query type to SELECT
     */
    public function select($columns = '*')
    {
        $this->type = 'SELECT';
        $this->columns = is_array($columns) ? $columns : [$columns];
        return $this;
    }

    /**
     * Set query type to INSERT
     */
    public function insert($table, array $data)
    {
        $this->type = 'INSERT';
        $this->table = $table;
        $this->data = $data;
        return $this;
    }

    /**
     * Set query type to UPDATE
     */
    public function update($table, array $data)
    {
        $this->type = 'UPDATE';
        $this->table = $table;
        $this->data = $data;
        return $this;
    }

    /**
     * Set query type to DELETE
     */
    public function delete($table)
    {
        $this->type = 'DELETE';
        $this->table = $table;
        return $this;
    }

    /**
     * Specify the table for SELECT queries
     */
    public function from($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Specify RETURNING clause (PostgreSQL specific)
     */
    public function returning($column)
    {
        $this->returning = $column;
        return $this;
    }

    /**
     * Add WHERE condition (default AND)
     */
    public function where($field, $value, $condition = '=', $operator = 'AND')
    {
        $this->where[] = [
            'type' => 'basic',
            'field' => $field,
            'value' => $value,
            'condition' => $condition,
            'operator' => $operator
        ];
        return $this;
    }

    /**
     * Add OR WHERE condition
     */
    public function orWhere($field, $value, $condition = '=')
    {
        return $this->where($field, $value, $condition, 'OR');
    }

    /**
     * Add raw WHERE condition
     */
    public function whereRaw($sql, $params = [], $operator = 'AND')
    {
        $this->where[] = [
            'type' => 'raw',
            'sql' => $sql,
            'params' => $params,
            'operator' => $operator
        ];
        return $this;
    }

    /**
     * Add ORDER BY clause
     */
    public function orderBy($field, $direction = 'ASC')
    {
        $this->orderBy[] = "{$field} {$direction}";
        return $this;
    }

    /**
     * Add GROUP BY clause
     */
    public function groupBy($field)
    {
        $this->groupBy[] = $field;
        return $this;
    }

    /**
     * Set LIMIT
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set OFFSET
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get parameters for pg_query_params
     */
    public function getParams()
    {
        $params = [];

        // Add data params for INSERT/UPDATE first
        if ($this->type === 'INSERT' || $this->type === 'UPDATE') {
            foreach ($this->data as $value) {
                $params[] = $this->prepareValue($value);
            }
        }

        // Add WHERE params
        foreach ($this->where as $w) {
            if ($w['type'] === 'basic') {
                $params[] = $w['value'];
            } elseif ($w['type'] === 'raw') {
                foreach ($w['params'] as $p) {
                    $params[] = $p;
                }
            }
        }

        return $params;
    }

    /**
     * Prepare value for database (handle arrays/objects as JSON)
     */
    protected function prepareValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        return $value;
    }

    /**
     * Dynamic query building using __toString()
     */
    public function __toString()
    {
        switch ($this->type) {
            case 'SELECT':
                return $this->buildSelect();
            case 'INSERT':
                return $this->buildInsert();
            case 'UPDATE':
                return $this->buildUpdate();
            case 'DELETE':
                return $this->buildDelete();
        }
        return '';
    }

    protected function buildSelect()
    {
        $cols = empty($this->columns) ? '*' : implode(', ', $this->columns);
        $sql = "SELECT {$cols} FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= $this->buildWhereClause();
        }

        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY " . implode(', ', $this->groupBy);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT " . (int) $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET " . (int) $this->offset;
        }

        return $sql;
    }

    protected function buildInsert()
    {
        $keys = array_keys($this->data);
        $placeholders = [];
        for ($i = 1; $i <= count($keys); $i++) {
            $placeholders[] = '$' . $i;
        }

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $placeholders) . ")";

        if ($this->returning) {
            $sql .= " RETURNING {$this->returning}";
        }

        return $sql;
    }

    protected function buildUpdate()
    {
        $fields = [];
        $i = 1;
        foreach (array_keys($this->data) as $key) {
            $fields[] = "{$key} = \${$i}";
            $i++;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields);

        if (!empty($this->where)) {
            $sql .= $this->buildWhereClause($i);
        }

        return $sql;
    }

    protected function buildDelete()
    {
        $sql = "DELETE FROM {$this->table}";
        if (!empty($this->where)) {
            $sql .= $this->buildWhereClause();
        }
        return $sql;
    }

    protected function buildWhereClause($startIndex = 1)
    {
        $sql = " WHERE ";
        $i = $startIndex;

        foreach ($this->where as $index => $w) {
            $prefix = ($index === 0) ? "" : " " . $w['operator'] . " ";

            if ($w['type'] === 'basic') {
                $cond = strtoupper($w['condition']);
                $sql .= $prefix . "{$w['field']} {$cond} \${$i}";
                $i++;
            } elseif ($w['type'] === 'raw') {
                $rawSql = $w['sql'];
                // Replace ? or placeholders in raw SQL if needed, but for now we assume they use $N correctly or we handle it
                // A better way is to handle placeholders in raw SQL
                foreach ($w['params'] as $p) {
                    $pos = strpos($rawSql, '?');
                    if ($pos !== false) {
                        $rawSql = substr_replace($rawSql, '$' . $i, $pos, 1);
                        $i++;
                    }
                }
                $sql .= $prefix . "(" . $rawSql . ")";
            }
        }
        return $sql;
    }
}
