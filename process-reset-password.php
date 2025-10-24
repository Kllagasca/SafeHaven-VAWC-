<?php
// Retrieve and validate the token from the POST request
if (!isset($_POST["token"])) {
    die("Token is required.");
}

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

// Include the PDO database connection
require __DIR__ . "/config/supabase_connect.php";

try {
    // Prepare the SQL query to find the user by the reset token hash
    $sql = "SELECT * FROM users WHERE reset_token_hash = :token_hash";
    $stmt = $pdo->prepare($sql);

    // Bind the token hash parameter
    $stmt->bindParam(':token_hash', $token_hash, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch the user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists
    if (!$user) {
        die("Token not found or invalid.");
    }

    // Check if the token has expired
    if (strtotime($user["reset_token_expires_at"]) <= time()) {
        die("Token has expired.");
    }

    // Validate the new password
    $password = $_POST["password"] ?? '';
    $password_confirmation = $_POST["password_confirmation"] ?? '';

    if (strlen($password) < 8) {
        die("Password must be at least 8 characters long.");
    }

    if (!preg_match("/[a-z]/i", $password)) {
        die("Password must contain at least one letter.");
    }

    if (!preg_match("/[0-9]/", $password)) {
        die("Password must contain at least one number.");
    }

    if ($password !== $password_confirmation) {
        die("Passwords do not match.");
    }

    // Update the user's password directly (without hashing)
    $sql = "UPDATE users
            SET password = :password,  -- Update to 'password' column
                reset_token_hash = NULL,
                reset_token_expires_at = NULL
            WHERE id = :user_id";

    $stmt = $pdo->prepare($sql);

    // Bind the parameters
    $stmt->bindParam(':password', $password, PDO::PARAM_STR); // Use plain password
    $stmt->bindParam(':user_id', $user["id"], PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->rowCount() === 0) {
        die("Failed to update password. Please try again.");
    }

    // Optionally, you can redirect to the login page or display a success message
    echo "Password updated successfully. You can now log in.";

} catch (PDOException $e) {
    // Handle PDO exceptions
    die("Error: " . $e->getMessage());
}
?>
