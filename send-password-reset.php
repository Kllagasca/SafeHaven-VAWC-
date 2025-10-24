<?php

// Sanitize and validate the email input
$email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

// Generate a secure token
$token = bin2hex(random_bytes(16));

// Hash the token
$token_hash = hash("sha256", $token);

// Set the expiry time (30 minutes from now)
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

// Include the PDO database connection
require __DIR__ . "/config/supabase_connect.php";

try {
    // Prepare the SQL query with named placeholders
    $sql = "UPDATE users 
            SET reset_token_hash = :token_hash, 
                reset_token_expires_at = :expiry 
            WHERE email = :email";

    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind the parameters to the placeholders
    $stmt->bindParam(':token_hash', $token_hash, PDO::PARAM_STR);
    $stmt->bindParam(':expiry', $expiry, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    // Execute the statement
    $stmt->execute();

    // Check if any rows were updated
    if ($stmt->rowCount() > 0) {
        // Include the mailer
        $mail = require __DIR__ . "/mailer.php";

        // Set email details
        $mail->setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END
        Click <a href="http://localhost/GenderDev2/reset-password.php?token=$token">here</a> 
        to reset your password.
        END;

        try {
            // Attempt to send the email
            $mail->send();
            echo "Message sent, please check your inbox.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No user found with that email address.";
    }
} catch (PDOException $e) {
    // Handle PDO exception
    die("Database error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>