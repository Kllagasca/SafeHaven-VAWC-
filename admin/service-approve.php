<?php
require_once '../config/function.php'; // Validation functions

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = validate($_GET['id']);
    $action = validate($_GET['action']);

    if ($action === 'approve') {
        $approvalStatus = 'approved';
        $query = "UPDATE services SET approval_status = :approval_status WHERE id = :id";
    } elseif ($action === 'reject') {
        $approvalStatus = 'rejected';
        $query = "UPDATE services SET approval_status = :approval_status WHERE id = :id";
    } else {
        redirect('services.php', 'Invalid action.');
        exit();
    }

    $stmt = $pdo->prepare($query);

    if ($stmt) {
        $stmt->bindParam(':approval_status', $approvalStatus, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($action === 'reject') {
                // Display rejection message
                $message = "The post with ID $id has been rejected. Please inform the researcher manually if needed.";
            } else {
                $message = 'Post Approved Successfully.';
            }
            redirect('services.php', $message);
        } else {
            redirect('services.php', 'Failed to process the request.');
        }
    } else {
        redirect('services.php', 'Failed to prepare the statement.');
    }
} else {
    redirect('services.php', 'Invalid request.');
}
?>
