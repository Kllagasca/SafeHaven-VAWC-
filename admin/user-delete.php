<?php

require '../config/function.php';

$paraResult = checkParamId('id', $pdo);
if (is_numeric($paraResult)) {

    $userId = validate($paraResult);

    // Fetch user data using PDO
    $user = getById('users', $userId);
    if ($user['status'] == 200) {

        // Delete the user
        $userDeleteRes = deleteQuery('users', $userId);
        if ($userDeleteRes) {

            // Check if the users table is empty
            $query = "SELECT COUNT(*) AS count FROM users";
            $stmt = $pdo->query($query);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] == 0) {
                // Reset AUTO_INCREMENT if the table is empty
                $query = "ALTER TABLE users AUTO_INCREMENT = 1";
                $pdo->query($query);
            }

            redirect('users.php', 'User Deleted Successfully');

        } else {
            redirect('users.php', 'Something went wrong');
        }

    } else {
        redirect('users.php', $user['message']);
    }

} else {
    redirect('users.php', $paraResult);
}

?>
