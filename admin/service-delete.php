<?php

require '../config/function.php';

$paraResult = checkParamId('id', $pdo);
if (is_numeric($paraResult)) {

    $serviceId = validate($paraResult);

    $service = getById('services', $serviceId);
    if ($service['status'] == 200) {

        $serviceDeleteRes = deleteQuery('services', $serviceId);
        if ($serviceDeleteRes) {

            $deleteImage = "../" . $service['data']['image'];
            if (file_exists($deleteImage)) {
                if (!unlink($deleteImage)) {
                    error_log("Failed to delete image: $deleteImage"); // Log if deletion fails
                }
            }

            // Use PDO for count query
            $query = "SELECT COUNT(*) AS count FROM services";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] == 0) {
                // Reset AUTO_INCREMENT using PDO (MySQL specific)
                // For PostgreSQL, you would reset the sequence instead
                $query = "ALTER TABLE services ALTER COLUMN id SET DEFAULT nextval('services_id_seq')";
                $pdo->exec($query);
            }

            redirect('services.php', 'Post Deleted Successfully');

        } else {
            redirect('services.php', 'Something went wrong');
        }

    } else {
        redirect('services.php', $service['message']);
    }

} else {
    redirect('services.php', $paraResult);
}
?>
