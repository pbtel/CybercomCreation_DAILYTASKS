<?php
/**
 * Database Helper Functions - EasyCart Phase 6
 * Common database operations
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Execute a query with parameters
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return PDOStatement|false
 */
function executeQuery($sql, $params = []) {
    try {
        $pdo = getDbConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query execution failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Fetch a single row
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array|false
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    if ($stmt === false) return false;
    return $stmt->fetch();
}

/**
 * Fetch all rows
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array|false
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    if ($stmt === false) return false;
    return $stmt->fetchAll();
}

/**
 * Insert data into a table
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int|false Last insert ID or false on failure
 */
function dbInsert($table, $data) {
    $columns = array_keys($data);
    $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
    
    $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") 
            VALUES (" . implode(', ', $placeholders) . ")";
    
    $params = [];
    foreach ($data as $key => $value) {
        $params[':' . $key] = $value;
    }
    
    $stmt = executeQuery($sql, $params);
    if ($stmt === false) return false;
    
    return getLastInsertId();
}

/**
 * Update data in a table
 * @param string $table Table name
 * @param array $data Data to update
 * @param array $where WHERE conditions
 * @return bool
 */
function dbUpdate($table, $data, $where) {
    $setParts = [];
    $params = [];
    
    foreach ($data as $key => $value) {
        $setParts[] = "$key = :set_$key";
        $params[':set_' . $key] = $value;
    }
    
    $whereParts = [];
    foreach ($where as $key => $value) {
        $whereParts[] = "$key = :where_$key";
        $params[':where_' . $key] = $value;
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $setParts) . 
           " WHERE " . implode(' AND ', $whereParts);
    
    $stmt = executeQuery($sql, $params);
    return $stmt !== false;
}

/**
 * Delete data from a table
 * @param string $table Table name
 * @param array $where WHERE conditions
 * @return bool
 */
function dbDelete($table, $where) {
    $whereParts = [];
    $params = [];
    
    foreach ($where as $key => $value) {
        $whereParts[] = "$key = :$key";
        $params[':' . $key] = $value;
    }
    
    $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereParts);
    
    $stmt = executeQuery($sql, $params);
    return $stmt !== false;
}

/**
 * Get last insert ID
 * @return int
 */
function getLastInsertId() {
    $pdo = getDbConnection();
    if (!$pdo) return 0;
    return (int)$pdo->lastInsertId();
}

/**
 * Begin transaction
 * @return bool
 */
function beginTransaction() {
    $pdo = getDbConnection();
    if (!$pdo) return false;
    return $pdo->beginTransaction();
}

/**
 * Commit transaction
 * @return bool
 */
function commitTransaction() {
    $pdo = getDbConnection();
    if (!$pdo) return false;
    return $pdo->commit();
}

/**
 * Rollback transaction
 * @return bool
 */
function rollbackTransaction() {
    $pdo = getDbConnection();
    if (!$pdo) return false;
    return $pdo->rollBack();
}
?>
