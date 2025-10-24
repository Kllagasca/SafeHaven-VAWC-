<?php
include ('../config/supabase_connect.php');
require '../config/function.php';

$paraResult = checkParamId('id', $pdo);
var_dump($paraResult);
if (is_numeric($paraResult)) {

    $imageId = validate($paraResult);

    // Fetch carousel data using PDO
    $carousel = getById('carousel', $imageId);
    if ($carousel['status'] == 200) {

        // Perform delete query using PDO
        $carouselDeleteRes = deleteQuery('carousel', $imageId);
        if ($carouselDeleteRes) {

            // Delete the image file
            $deleteImage = "../" . $carousel['data']['image'];
            if (file_exists($deleteImage)) {
                unlink($deleteImage);
            }

            // Count remaining carousel items
            $query = "SELECT COUNT(*) AS count FROM carousel";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] == 0) {
                $query = "ALTER TABLE carousel AUTO_INCREMENT = 1";
                $pdo->exec($query);
            }

            redirect('carousel.php', 'Image Deleted Successfully');

        } else {
            redirect('carousel.php', 'Something went wrong');
        }

    } else {
        redirect('carousel.php', $carousel['message']);
    }

} else {
    redirect('carousel.php', $paraResult);
}

?>
