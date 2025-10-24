<?php

require '../config/function.php';

$paraResult = checkParamId('id', $pdo);
if (is_numeric($paraResult)) {

    $documentId = validate($paraResult);

    // Retrieve document details using PDO
    $document = getById('documents', $documentId);
    if ($document['status'] == 200) {

        // Delete the document using PDO
        $documentDeleteRes = deleteQuery('documents', $documentId);
        if ($documentDeleteRes) {

            // Delete the file from the server
            $deleteFile = "../" . $document['data']['file'];
            if (file_exists($deleteFile)) {
                unlink($deleteFile);
            }

            // Check if the table is empty and reset AUTO_INCREMENT if needed
            $query = "SELECT COUNT(*) AS count FROM documents";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] == 0) {
                // Reset AUTO_INCREMENT only if the table is empty
                $query = "ALTER TABLE documents AUTO_INCREMENT = 1";
                $pdo->exec($query);
            }

            redirect('documents.php', 'Post Deleted Successfully');

        } else {
            redirect('documents.php', 'Something went wrong while deleting the document');
        }

    } else {
        redirect('documents.php', $document['message']);
    }

} else {
    redirect('documents.php', $paraResult);
}

?>
