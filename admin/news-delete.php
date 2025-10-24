<?php

require '../config/function.php';

// Assuming $pdo is the PDO instance
$paraResult = checkParamId('id', $pdo);
if (is_numeric($paraResult)) {

    $newsId = validate($paraResult);

    // Fetch news by ID using PDO
    $query = "SELECT * FROM news WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $newsId, PDO::PARAM_INT);
    $stmt->execute();

    $news = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($news) {

        // Delete the news post using PDO
        $deleteQuery = "DELETE FROM news WHERE id = :id";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $newsId, PDO::PARAM_INT);
        $deleteRes = $deleteStmt->execute();

        if ($deleteRes) {

            // Delete the associated image if it exists
            $deleteImage = "../" . $news['image'];
            if (file_exists($deleteImage)) {
                unlink($deleteImage);
            }

            // Check if table is empty and reset AUTO_INCREMENT if necessary
            $countQuery = "SELECT COUNT(*) AS count FROM news";
            $countStmt = $pdo->prepare($countQuery);
            $countStmt->execute();
            $row = $countStmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] == 0) {
                $resetQuery = "ALTER TABLE news AUTO_INCREMENT = 1";
                $pdo->exec($resetQuery);
            }

            redirect('news.php', 'News Deleted Successfully');

        } else {
            redirect('news.php', 'Something went wrong');
        }

    } else {
        redirect('news.php', 'News not found');
    }

} else {
    redirect('news.php', 'Invalid ID');
}
?>
