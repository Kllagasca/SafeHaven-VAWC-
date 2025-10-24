<?php
session_start();

// Check if a session is active before attempting to destroy it
if (session_status() === PHP_SESSION_ACTIVE) {
    // Clear all session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();
}

// Redirect to the home page
header("Location: index.php");
exit();
?>
