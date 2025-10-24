<?php

require '../config/function.php';
require '../config/supabase_connect.php'; // Assuming you have a PDO connection in this file

$paraResult = checkParamId('id', $pdo);
if (is_numeric($paraResult)) {

    $serviceId = validate($paraResult);

    // Fetch the service by ID
    $service = getById('services', $serviceId);
    if ($service['status'] == 200) {

        // Prepare the delete query to remove the service from the database
        $query = "DELETE FROM services WHERE id = :id";
        $stmt = $pdo->prepare($query);

        // Bind the parameter
        $stmt->bindParam(':id', $serviceId, PDO::PARAM_INT);

        // Execute the delete query
        if ($stmt->execute()) {

            // Delete the associated image if it exists
            $deleteImage = "../" . $service['data']['image'];
            if (file_exists($deleteImage)) {
                unlink($deleteImage);
            }

            // Check if the table is empty and reset AUTO_INCREMENT if necessary
            $query = "SELECT COUNT(*) AS count FROM services";
            $stmt = $pdo->query($query);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] == 0) {
                $query = "ALTER TABLE services AUTO_INCREMENT = 1";
                $pdo->query($query);
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
