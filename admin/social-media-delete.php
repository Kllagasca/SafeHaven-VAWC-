<?php

require '../config/function.php';

$paraResult = checkParamId('id', $pdo);
if (is_numeric($paraResult)) {

    $socialMediaId = validate($paraResult);

    // Fetch social media data using PDO
    $socialMedia = getById('social_medias', $socialMediaId);
    if ($socialMedia['status'] == 200) {

        // Delete social media entry using PDO
        $socialMediaDeleteRes = deleteQuery('social_medias', $socialMediaId);
        if ($socialMediaDeleteRes) {

            // Check if table is empty using PDO
            $query = "SELECT COUNT(*) AS count FROM social_medias";
            $stmt = $pdo->query($query);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] == 0) {
                // Reset AUTO_INCREMENT only if table is empty
                $query = "ALTER TABLE social_medias AUTO_INCREMENT = 1";
                $pdo->query($query);
            }

            redirect('social-media.php', 'Social Media Deleted Successfully');

        } else {
            redirect('social-media.php', 'Something went wrong');
        }

    } else {
        redirect('social-media.php', $socialMedia['message']);
    }

} else {
    redirect('social-media.php', $paraResult);
}

?>
