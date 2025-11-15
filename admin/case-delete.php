<?php
require '../config/function.php';
require '../config/db_connect.php';       // $conn (MySQLi for XAMPP)
require '../config/supabase_connect.php'; // $pdo  (PDO for Supabase)

if (isset($_GET['id'])) {
    $raw = validate($_GET['id']);

    try {
        // Decide whether the provided identifier is a numeric local id or a caseno string
        if (ctype_digit($raw)) {
            $id = (int)$raw;
            $q = $pdo->prepare("SELECT * FROM cases WHERE id = :id LIMIT 1");
            $q->execute([':id' => $id]);
        } else {
            $caseno = $raw;
            $q = $pdo->prepare("SELECT * FROM cases WHERE caseno = :caseno LIMIT 1");
            $q->execute([':caseno' => $caseno]);
        }

        if ($q->rowCount() > 0) {
            $case = $q->fetch(PDO::FETCH_ASSOC);

            // --- Delete from Supabase ---
            if (isset($id)) {
                $deleteQuery = "DELETE FROM cases WHERE id = :id";
                $deleteStmt = $pdo->prepare($deleteQuery);
                $deleteStmt->bindParam(':id', $id);
            } else {
                $deleteQuery = "DELETE FROM cases WHERE caseno = :caseno";
                $deleteStmt = $pdo->prepare($deleteQuery);
                $deleteStmt->bindParam(':caseno', $caseno);
            }
            try {
                $supabaseDeleted = (bool)$deleteStmt->execute();
            } catch (Exception $e) {
                // Best-effort: log and continue to attempt local delete
                error_log('Supabase delete error in admin case-delete: ' . $e->getMessage());
                $supabaseDeleted = false;
            }

            // --- Delete from XAMPP (MySQLi) ---
            if (isset($id)) {
                $deleteQuery2 = "DELETE FROM `cases` WHERE `id` = ?";
                $stmt2 = mysqli_prepare($conn, $deleteQuery2);
                mysqli_stmt_bind_param($stmt2, "i", $id);
            } else {
                $deleteQuery2 = "DELETE FROM `cases` WHERE `caseno` = ?";
                $stmt2 = mysqli_prepare($conn, $deleteQuery2);
                mysqli_stmt_bind_param($stmt2, "s", $caseno);
            }
            if (!$stmt2) {
                die("Prepare failed: " . mysqli_error($conn));
            }
            $xamppDeleted = mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);

            // If local deletion succeeded, attempt to delete the local image file
            if ($xamppDeleted) {
                $imageVal = isset($case['image']) ? trim($case['image']) : '';
                if ($imageVal !== '') {
                    // If image is a remote URL, skip local unlink
                    if (!preg_match('#^https?://#i', $imageVal)) {
                        // Normalize path: prefer paths containing the known uploads folder
                        $rel = ltrim($imageVal, '/\\');
                        if (stripos($rel, 'assets/uploads/cases/') !== false) {
                            $imagePath = __DIR__ . '/../' . $rel;
                        } elseif (basename($rel) === $rel) {
                            // Just a filename — assume it lives in assets/uploads/cases
                            $imagePath = __DIR__ . '/../assets/uploads/cases/' . $rel;
                        } else {
                            // Generic relative path
                            $imagePath = __DIR__ . '/../' . $rel;
                        }

                        if (!empty($imagePath) && file_exists($imagePath)) {
                            @unlink($imagePath);
                        }
                    }
                }
            }

            // Consider delete successful if either Supabase or local XAMPP delete succeeded
            if ($supabaseDeleted || $xamppDeleted) {
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