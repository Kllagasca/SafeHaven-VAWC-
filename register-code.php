<?php
session_start();
include('config/supabase_connect.php');
include('config/function.php'); // Include utility functions like redirect()

// Handle form submission
if (isset($_POST['register'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Default role for public registrations
    $role = 'user';

    // Validate input (role is set server-side)
    if (empty($fname) || empty($lname) || empty($email) || empty($password)) {
        redirect('register.php', 'All fields are required.');
    }

    try {
        // Check if email is already registered
        $check_email_query = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $pdo->prepare($check_email_query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $emailExists = $stmt->fetchColumn();

        if ($emailExists > 0) {
            redirect('register.php', 'Email is already registered.');
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $insert_user_query = "INSERT INTO users (fname, lname, email, password, role) VALUES (:fname, :lname, :email, :password, :role)";
        $stmt = $pdo->prepare($insert_user_query);
        $stmt->bindValue(':fname', $fname, PDO::PARAM_STR);
        $stmt->bindValue(':lname', $lname, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
    // Bind role as 'user' for public registrations
    $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->execute();
        $userId = $pdo->lastInsertId(); // Get the ID of the newly created user

        // Add a registration notification for the admin
        $activity_type = 'registration';
        $activity_details = "New user {$fname} {$lname} registered as {$role}.";
        $notification_role = 'admin'; // Target the admin role

        $insert_notification_query = "INSERT INTO notifications (user_id, activity_type, activity_details, role) VALUES (:user_id, :activity_type, :activity_details, :role)";
        $notification_stmt = $pdo->prepare($insert_notification_query);
        $notification_stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $notification_stmt->bindValue(':activity_type', $activity_type, PDO::PARAM_STR);
        $notification_stmt->bindValue(':activity_details', $activity_details, PDO::PARAM_STR);
        $notification_stmt->bindValue(':role', $notification_role, PDO::PARAM_STR);
        $notification_stmt->execute();

        redirect('login.php', 'Registration successful! Please log in.');
    } catch (PDOException $e) {
        redirect('register.php', 'Registration failed. Please try again. Error: ' . $e->getMessage());
    }
}
?>
