<?php

include '../config/supabase_connect.php';
// Include shared helper functions (starts session and defines redirect(), validate(), etc.)
require_once __DIR__ . '/../config/function.php';

try {
    // Establish a PDO connection
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

if (isset($_SESSION['auth'])) {
    if (isset($_SESSION['loggedInUserRole'])) {
        $role = validate($_SESSION['loggedInUserRole']);
        $email = validate($_SESSION['loggedInUser']['email']);

        try {
            // Use prepared statements for security
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND role = :role LIMIT 1");
            $stmt->execute(['email' => $email, 'role' => $role]);

            if ($stmt->rowCount() == 0) {
                logoutSession();
                redirect('../login.php', 'Access Denied');
            } else {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row['role'] != 'admin') {
                    logoutSession();
                    redirect('../login.php', 'Access Denied');
                }

                if ($row['is_ban'] == 1) {
                    logoutSession();
                    redirect('../login.php', 'Your account has been banned. Please contact admin.');
                }
            }
        } catch (PDOException $e) {
            logoutSession();
            redirect('../login.php', 'Something went wrong');
        }
    }
} else {
    redirect('../login.php', 'Login to continue...');
}
?>
