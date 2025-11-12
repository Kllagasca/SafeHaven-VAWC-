<?php
require 'config/function.php';
include 'config/supabase_connect.php';
include 'config/db_connect.php'; // Localhost MySQL connection ($conn)

if (isset($_POST['loginBtn'])) {
    // Sanitize and validate user input
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']); // Password doesn't require additional sanitization

    if (!empty($email) && !empty($password)) {
        try {
            // Establish PDO connection
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare and execute the query to fetch user by email
            $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':email' => $email]);

            if ($stmt->rowCount() === 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify the password using password_verify()
                if (password_verify($password, $row['password'])) {
                    // Check if user is banned
                    if ($row['is_ban'] == 1) {
                        redirect('login.php', 'Your account has been banned. Please contact admin.');
                    }

                    // Set session variables securely
                    $_SESSION['auth'] = true;
                    $_SESSION['user'] = [
                        'id' => $row['id'],
                        'fname' => $row['fname'],
                        'lname' => $row['lname'],
                        'email' => $row['email'],
                        'role' => $row['role']
                    ];

                    // Add login notification
                    $event = 'login';
                    $message = "User {$row['fname']} {$row['lname']} has logged in successfully.";

                    try {
                        // Insert a login notification
                        $notification_sql = "INSERT INTO notifications (user_id, event, message, created_at) 
                                             VALUES (:user_id, :event, :message, NOW())";
                        $notification_stmt = $pdo->prepare($notification_sql);
                        $notification_stmt->execute([
                            ':user_id' => $row['id'],
                            ':event' => $event,
                            ':message' => $message
                        ]);

                        error_log("Login notification successfully logged for user ID: " . $row['id']);
                    } catch (PDOException $e) {
                        // Log errors related to notification insertion
                        error_log("Error inserting login notification: " . $e->getMessage());
                    }

                    // Redirect based on user role
                    $redirectUrl = match ($row['role']) {
                        'admin' => 'admin/index.php',
                        'fperson' => 'focal-person/index.php',
                        'researcher' => 'researcher/index.php',
                        'user' => 'user-dashboard.php',
                        default => 'user-dashboard.php'
                    };

                    redirect($redirectUrl, 'Logged in Successfully');
                } else {
                    redirect('login.php', 'Invalid Email or Password.');
                }
            } else {
                redirect('login.php', 'Invalid Email or Password.');
            }
        } catch (PDOException $e) {
            // Handle and log database connection or query errors
            error_log("Login error: " . $e->getMessage());
            redirect('login.php', 'Something went wrong. Please try again.');
        }
    } else {
        redirect('login.php', 'All fields are mandatory.');
    }
}
?>