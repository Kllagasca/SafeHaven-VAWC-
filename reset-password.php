<?php

include 'config/function.php';

// Check if the token is provided in the URL
if (!isset($_GET["token"]) || empty($_GET["token"])) {
    die("Token is required.");
}

$token = $_GET["token"];

// Hash the token
$token_hash = hash("sha256", $token);

// Include the database connection
require __DIR__ . "/config/supabase_connect.php";

try {
    // Prepare the SQL query using a named placeholder
    $sql = "SELECT * FROM users WHERE reset_token_hash = :token_hash";

    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind the parameter to the placeholder
    $stmt->bindParam(':token_hash', $token_hash, PDO::PARAM_STR);

    // Execute the statement
    $stmt->execute();

    // Fetch the user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists
    if ($user === false) {
        die("Token not found.");
    }

    // Check if the token has expired
    if (strtotime($user["reset_token_expires_at"]) <= time()) {
        die("Token has expired.");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Get the new password from the form
        $password = $_POST["password"];
        $password_confirmation = $_POST["password_confirmation"];

        // Check if passwords match
        if ($password !== $password_confirmation) {
            die("Passwords do not match.");
        }

        // Hash the new password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $sql = "UPDATE users SET password = :password_hash, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
        $stmt->execute();

        redirect('login.php','Password has been reset successfully.');
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

    <h1>Reset Password</h1>

    <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New password</label>
        <input type="password" id="password" name="password" required>

        <label for="password_confirmation">Repeat password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>

        <button type="submit">Send</button>
    </form>

</body>
</html>