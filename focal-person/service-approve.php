<?php
require_once '../config/supabase_connect.php';
require_once '../config/function.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = validate($_GET['id']);
    $action = validate($_GET['action']);

    if ($action === 'approve') {
        $approvalStatus = 'approved';
    } elseif ($action === 'reject') {
        $approvalStatus = 'rejected';
    } else {
        redirect('services.php', 'Invalid action.');
        exit();
    }

    // Handle rejection (delete the post)
    if ($approvalStatus === 'rejected') {
        $query = "DELETE FROM services WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            redirect('services.php', 'Post Rejected and Deleted.');
        } else {
            redirect('services.php', 'Failed to delete post.');
        }
    } else {
        // Handle approval (update the approval status)
        $query = "UPDATE services SET approval_status = :approval_status WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':approval_status', $approvalStatus, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            redirect('services.php', 'Post Approved Successfully.');
        } else {
            redirect('services.php', 'Failed to update approval status.');
        }
    }
} else {
    redirect('services.php', 'Invalid request.');
}
?>
