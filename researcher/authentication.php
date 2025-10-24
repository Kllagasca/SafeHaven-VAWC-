<?php

if (isset($_SESSION['auth'])) {
    if (isset($_SESSION['loggedInUserRole'])) {
        $role = validate($_SESSION['loggedInUserRole']);
        $email = validate($_SESSION['loggedInUser']['email']);

        // Using PDO to fetch user details
        $query = "SELECT * FROM users WHERE email = :email AND role = :role LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            logoutSession();
            redirect('../login.php', 'Access Denied');
        } else {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['role'] != 'researcher') {
                logoutSession();
                redirect('../login.php', 'Access Denied');
            }

            if ($row['is_ban'] == 1) {
                logoutSession();
                redirect('../login.php', 'Your account has been banned. Please contact admin.');
            }
        }
    }
} else {
    redirect('../login.php', 'Login to continue...');
}

?>
