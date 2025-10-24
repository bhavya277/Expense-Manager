<?php
/**
 * AJAX endpoint for adding custom categories
 * Returns JSON response for JavaScript consumption
 */

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Include database connection
require_once '../includes/db.php';

$userId = $_SESSION['user_id'];

// Set content type to JSON
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get form data
$name = trim($_POST['name'] ?? '');
$type = $_POST['type'] ?? '';

// Validation
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Category name is required']);
    exit();
}

if (!in_array($type, ['income', 'expense'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid category type']);
    exit();
}

// Check if category already exists for this user
$existing = fetchRow("SELECT id FROM categories WHERE name = ? AND (user_id = ? OR user_id IS NULL)", [$name, $userId]);

if ($existing) {
    echo json_encode(['success' => false, 'message' => 'Category already exists']);
    exit();
}

// Insert new category
$result = executeQuery(
    "INSERT INTO categories (name, type, user_id) VALUES (?, ?, ?)",
    [$name, $type, $userId]
);

if ($result) {
    $categoryId = $GLOBALS['pdo']->lastInsertId();
    echo json_encode([
        'success' => true, 
        'message' => 'Category added successfully',
        'category_id' => $categoryId
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add category']);
}
?>
