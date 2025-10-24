<?php
/**
 * Database Connection File
 * This file establishes connection to MySQL database
 * Used throughout the application for database operations
 */

// Database configuration
$host = 'localhost';
$dbname = 'expense_manager';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password (empty)

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Display error message and stop execution
    die("Connection failed: " . $e->getMessage());
}

/**
 * Helper function to execute prepared statements
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return PDOStatement|false
 */
function executeQuery($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

/**
 * Helper function to get single row
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array|false
 */
function fetchRow($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetch() :  false;
}

/**
 * Helper function to get all rows
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array|false
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetchAll() : false;
}
?>
