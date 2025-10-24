<?php
/**
 * Delete Transaction Page
 * Handles transaction deletion with confirmation
 * Redirects back to view transactions after deletion
 */

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once 'includes/db.php';

$userId = $_SESSION['user_id'];
$transactionId = intval($_GET['id'] ?? 0);

if (!$transactionId) {
    header('Location: view_transactions.php');
    exit();
}

// Verify transaction belongs to user and delete
$result = executeQuery(
    "DELETE FROM transactions WHERE id = ? AND user_id = ?",
    [$transactionId, $userId]
);

if ($result) {
    // Redirect with success message
    header('Location: view_transactions.php?deleted=1');
} else {
    // Redirect with error message
    header('Location: view_transactions.php?error=1');
}
exit();
?>
