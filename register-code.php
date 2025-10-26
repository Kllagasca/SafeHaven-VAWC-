<?php
session_start();
include('config/db_connect.php');       // Localhost MySQL connection ($conn)
include('config/supabase_connect.php'); // Supabase PDO connection ($pdo)
include('config/function.php');         // redirect() helper

// Debug mode — show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['register'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'user';
    $is_ban = 0;
    $created_at = date('Y-m-d H:i:s');
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    echo "<h3>Step 1: Localhost Database</h3>";

    // ✅ LOCALHOST INSERT
    if (!$conn) {
        die("<p style='color:red;'>❌ Local DB connection failed: " . mysqli_connect_error() . "</p>");
    } else {
        echo "<p style='color:green;'>✅ Connected to local DB</p>";
    }

    try {
        $localQuery = "INSERT INTO users (fname, lname, email, password, is_ban, role, created_at)
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $localStmt = mysqli_prepare($conn, $localQuery);
        mysqli_stmt_bind_param($localStmt, "ssssiss",
            $fname,
            $lname,
            $email,
            $hashed_password,
            $is_ban,
            $role,
            $created_at
        );

        if (mysqli_stmt_execute($localStmt)) {
            echo "<p style='color:green;'>✅ Local insert successful!</p>";
            mysqli_commit($conn);
        } else {
            echo "<p style='color:red;'>❌ Local insert failed: " . mysqli_stmt_error($localStmt) . "</p>";
            exit;
        }

        mysqli_stmt_close($localStmt);
        mysqli_close($conn);
    } catch (Exception $e) {
        echo "<p style='color:red;'>⚠ Local DB error: " . $e->getMessage() . "</p>";
        exit;
    }

    echo "<hr><h3>Step 2: Supabase Database</h3>";

    // ✅ SUPABASE INSERT (PDO)
    try {
        // Check if email already exists
        $check_email = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $check_email->bindValue(':email', $email);
        $check_email->execute();
        $exists = $check_email->fetchColumn();

        if ($exists > 0) {
            echo "<p style='color:red;'>⚠ Email already exists in Supabase.</p>";
            exit;
        }

        // Insert user data into Supabase
        $insert_user = $pdo->prepare("INSERT INTO users (fname, lname, email, password, is_ban, role, created_at)
                                      VALUES (:fname, :lname, :email, :password, :is_ban, :role, :created_at)");
        $insert_user->bindValue(':fname', $fname);
        $insert_user->bindValue(':lname', $lname);
        $insert_user->bindValue(':email', $email);
        $insert_user->bindValue(':password', $hashed_password);
        $insert_user->bindValue(':is_ban', $is_ban);
        $insert_user->bindValue(':role', $role);
        $insert_user->bindValue(':created_at', $created_at);
        $insert_user->execute();

        echo "<p style='color:green;'>✅ Supabase insert successful!</p>";

        // Optional — log registration to notifications
        $userId = $pdo->lastInsertId();
        $activity_type = 'registration';
        $activity_details = "New user {$fname} {$lname} registered.";
        $notif_role = 'admin';

        $notif = $pdo->prepare("INSERT INTO notifications (user_id, activity_type, activity_details, role)
                                VALUES (:user_id, :activity_type, :activity_details, :role)");
        $notif->bindValue(':user_id', $userId);
        $notif->bindValue(':activity_type', $activity_type);
        $notif->bindValue(':activity_details', $activity_details);
        $notif->bindValue(':role', $notif_role);
        $notif->execute();

        echo "<p style='color:green;'>✅ Supabase notification added!</p>";

        echo "<hr><p style='color:orange;'>All steps completed successfully. (Redirect skipped for debugging)</p>";
        // redirect('login.php', 'Registration successful! Please log in.');

    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Supabase error: " . $e->getMessage() . "</p>";
    }
}
?>