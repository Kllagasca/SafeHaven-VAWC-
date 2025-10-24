<?php
require_once '../config/function.php'; // Validation functions

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = validate($_GET['id']);
    $action = validate($_GET['action']);

    if ($action === 'approve') {
        $approvalStatus = 'approved';
        $query = "UPDATE documents SET approval_status = :approvalStatus WHERE id = :id";
    } elseif ($action === 'reject') {
        $approvalStatus = 'rejected';
        $query = "UPDATE documents SET approval_status = :approvalStatus WHERE id = :id";
    } else {
        redirect('documents.php', 'Invalid action.');
        exit();
    }

    // Use PDO to prepare and execute the query
    $stmt = $pdo->prepare($query);

    if ($stmt) {
        // Bind parameters securely
        $stmt->bindParam(':approvalStatus', $approvalStatus, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($action === 'reject') {
                // Display rejection message
                $message = "The post with ID $id has been rejected. Please inform the researcher manually if needed.";
            } else {
                $message = 'Post Approved Successfully.';
            }
            redirect('documents.php', $message);
        } else {
            redirect('documents.php', 'Failed to process the request.');
        }
    } else {
        redirect('documents.php', 'Failed to prepare the statement.');
    }
} else {
    redirect('documents.php', 'Invalid request.');
}
?>
