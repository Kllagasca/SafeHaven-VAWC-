<?php

require '../config/function.php';

// Check if 'id' is a valid numeric value
$paraResult = checkParamId('id', $pdo);
if (is_numeric($paraResult)) {

    $socialMediaId = validate($paraResult);

    // Fetch the social media record using PDO
    $query = "SELECT * FROM social_medias WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $socialMediaId, PDO::PARAM_INT);
    $stmt->execute();
    $socialMedia = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($socialMedia) {

        // Delete the social media record using PDO
        $deleteQuery = "DELETE FROM social_medias WHERE id = :id";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $socialMediaId, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {

            // Check if the table is empty and reset AUTO_INCREMENT if necessary
            $query = "SELECT COUNT(*) AS count FROM social_medias";
            $result = $pdo->query($query);
            $row = $result->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] == 0) {
                // Reset AUTO_INCREMENT only if the table is empty
                $query = "ALTER TABLE social_medias AUTO_INCREMENT = 1";
                $pdo->query($query);
            }

            redirect('social-media.php', 'Social Media Deleted Successfully');

        } else {
            redirect('social-media.php', 'Something went wrong while deleting the social media');
        }

    } else {
        redirect('social-media.php', 'Social Media not found');
    }

} else {
    redirect('social-media.php', 'Invalid ID');
}

?>
