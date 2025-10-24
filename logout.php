<?php
/**
 * Logout Page
 * Destroys user session and redirects to login
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destroy all session data
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>
