<?php

require '../config/function.php';

// Logout from the admin session
if (isset($_SESSION['auth'])) {
    logoutSession();
}

// Additionally, clear the main session by calling the global logout
include_once '../logout.php'; // Ensure this path is correct based on deployment structure

// Redirect to the login page with a success message
redirect('../login.php', 'Logged out successfully!');

?>
