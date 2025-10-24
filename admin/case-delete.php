<?php
require '../config/function.php';
require '../config/db_connect.php';       // $conn (MySQLi for XAMPP)
require '../config/supabase_connect.php'; // $pdo  (PDO for Supabase)

if (isset($_GET['id'])) {
    $caseno = validate($_GET['id']);

    try {
        // --- Fetch case (Supabase) ---
        $query = "SELECT * FROM cases WHERE caseno = :caseno LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':caseno', $caseno);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $case = $stmt->fetch(PDO::FETCH_ASSOC);

            // --- Delete from Supabase ---
            $deleteQuery = "DELETE FROM cases WHERE caseno = :caseno";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->bindParam(':caseno', $caseno);
            $supabaseDeleted = $deleteStmt->execute();

            // --- Delete from XAMPP (MySQLi) ---
            $deleteQuery2 = "DELETE FROM `cases` WHERE `caseno` = ?";
            $stmt2 = mysqli_prepare($conn, $deleteQuery2);
            if (!$stmt2) {
                die("Prepare failed: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt2, "s", $caseno);
            $xamppDeleted = mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);

            if ($supabaseDeleted && $xamppDeleted) {
                // --- Delete image file if exists ---
                if (!empty($case['image']) && file_exists('../' . $case['image'])) {
                    unlink('../' . $case['image']);
                }
                redirect('cases.php', 'Case Deleted Successfully');
            } else {
                redirect('cases.php', 'Something Went Wrong');
            }

        } else {
            redirect('cases.php', 'Case Not Found');
        }

    } catch (Exception $e) {
        redirect('cases.php', 'Error: ' . $e->getMessage());
    }

} else {
    redirect('cases.php', 'No Case ID Found');
}