<?php

/**
 * Database Connection Class
 * Handles PostgreSQL database connections
 */
class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';

        try {
            $this->connection = pg_connect(
                "host=" . DB_HOST . " " .
                "port=" . DB_PORT . " " .
                "dbname=" . DB_NAME . " " .
                "user=" . DB_USER . " " .
                "password=" . DB_PASS
            );

            if (!$this->connection) {
                throw new Exception("Database connection failed");
            }

            // Set database timezone to IST
            pg_query($this->connection, "SET timezone = 'Asia/Kolkata'");
        } catch (Exception $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get database connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Execute a query
     */
    public function query($sql, $params = [])
    {
        if (empty($params)) {
            return pg_query($this->connection, $sql);
        } else {
            return pg_query_params($this->connection, $sql, $params);
        }
    }

    /**
     * Fetch all results
     */
    public function fetchAll($result)
    {
        if ($result === false) {
            error_log("Database query failed: " . pg_last_error($this->connection));
            return [];
        }
        $data = pg_fetch_all($result);
        return $data === false ? [] : $data;
    }

    /**
     * Fetch single result
     */
    public function fetch($result)
    {
        if ($result === false) {
            error_log("Database query failed: " . pg_last_error($this->connection));
            return null;
        }
        return pg_fetch_assoc($result);
    }

    /**
     * Escape string
     */
    public function escape($string)
    {
        return pg_escape_string($this->connection, $string);
    }

    /**
     * Get last inserted ID
     */
    public function lastInsertId($tableName, $idColumn = 'id')
    {
        $result = $this->query("SELECT currval(pg_get_serial_sequence('$tableName', '$idColumn')) as id");
        $row = $this->fetch($result);
        return $row['id'];
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        return pg_query($this->connection, "BEGIN");
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        return pg_query($this->connection, "COMMIT");
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        return pg_query($this->connection, "ROLLBACK");
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
