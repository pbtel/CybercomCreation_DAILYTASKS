<?php
/**
 * Database Connection Helper
 * Phase 6 - Database Integration
 */

require_once __DIR__ . '/config.php';

// Global PDO connection instance
$dbConnection = null;

/**
 * Get database connection (singleton pattern)
 */
function getDBConnection()
{
    global $dbConnection;

    if ($dbConnection === null) {
        try {
            $dbConnection = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
            // Set UTF-8 encoding for the connection
            $dbConnection->exec("SET NAMES 'UTF8'");
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please check your configuration.");
        }
    }

    return $dbConnection;
}

/**
 * Execute a query with parameters
 */
function executeQuery($sql, $params = [])
{
    try {
        $db = getDBConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query execution failed: " . $e->getMessage());
        error_log("SQL: " . $sql);
        throw new Exception("Database query failed: " . $e->getMessage());
    }
}

/**
 * Fetch all rows from a query
 */
function fetchAll($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Fetch a single row from a query
 */
function fetchOne($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Insert a record and return the inserted ID
 */
function dbInsert($table, $data)
{
    $columns = array_keys($data);
    $placeholders = array_map(function ($col) {
        return ':' . $col;
    }, $columns);

    $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") 
            VALUES (" . implode(', ', $placeholders) . ") 
            RETURNING *";

    $params = [];
    foreach ($data as $key => $value) {
        $params[':' . $key] = $value;
    }

    return fetchOne($sql, $params);
}

/**
 * Update records
 */
function dbUpdate($table, $data, $where, $whereParams = [])
{
    $setParts = [];
    $params = [];

    foreach ($data as $key => $value) {
        $setParts[] = "{$key} = :{$key}";
        $params[':' . $key] = $value;
    }

    // Merge where parameters
    $params = array_merge($params, $whereParams);

    $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE {$where}";

    executeQuery($sql, $params);
    return true;
}

/**
 * Delete records
 */
function dbDelete($table, $where, $whereParams = [])
{
    $sql = "DELETE FROM {$table} WHERE {$where}";
    executeQuery($sql, $whereParams);
    return true;
}

/**
 * Begin transaction
 */
function beginTransaction()
{
    $db = getDBConnection();
    return $db->beginTransaction();
}

/**
 * Commit transaction
 */
function commitTransaction()
{
    $db = getDBConnection();
    return $db->commit();
}

/**
 * Rollback transaction
 */
function rollbackTransaction()
{
    $db = getDBConnection();
    return $db->rollBack();
}

/**
 * Get last insert ID
 */
function getLastInsertId($sequence = null)
{
    $db = getDBConnection();
    return $db->lastInsertId($sequence);
}
