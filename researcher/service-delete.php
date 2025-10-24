<?php

require '../config/function.php';

$paraResult = checkParamId('id', $pdo);
if (is_numeric($paraResult)) {

    $serviceId = validate($paraResult);

    $service = getById('services', $serviceId);
    if ($service['status'] == 200) {

        $serviceDeleteRes = deleteQuery('services', $serviceId);
        if ($serviceDeleteRes) {

            // Delete the image if it exists
            $deleteImage = "../" . $service['data']['image'];
            if (file_exists($deleteImage)) {
                unlink($deleteImage);
            }

            // Count the number of services
            $query = "SELECT COUNT(*) AS count FROM services";
            $stmt = $pdo->prepare($query);
            if ($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row['count'] == 0) {
                    // Reset the AUTO_INCREMENT if no services are left
                    $query = "ALTER TABLE services AUTO_INCREMENT = 1";
                    $pdo->exec($query);
                }
            } else {
                echo "Error: " . implode(", ", $stmt->errorInfo());
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
