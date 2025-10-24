<?php

require '../config/function.php';

// Validate and retrieve document ID from URL
$paraResult = checkParamId('id', $pdo);
if (is_numeric($paraResult)) {

    $documentId = validate($paraResult);

    // Fetch document details using PDO
    $query = "SELECT * FROM documents WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $documentId, PDO::PARAM_INT);
    $stmt->execute();
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($document) {
        // Delete the document from the database
        $deleteQuery = "DELETE FROM documents WHERE id = :id";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $documentId, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
            // If file exists, delete it from the server
            $deleteFile = "../" . $document['file'];
            if (file_exists($deleteFile)) {
                unlink($deleteFile);
            }

            // Check if the table is empty and reset AUTO_INCREMENT if needed
            $query = "SELECT COUNT(*) AS count FROM documents";
            $result = $pdo->query($query);
            $row = $result->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] == 0) {
                // Reset AUTO_INCREMENT only if table is empty
                $resetQuery = "ALTER TABLE documents AUTO_INCREMENT = 1";
                $pdo->query($resetQuery);
            }

            redirect('documents.php', 'Document Deleted Successfully');
        } else {
            redirect('documents.php', 'Something went wrong while deleting the document');
        }
    } else {
        redirect('documents.php', 'Document not found');
    }

} else {
    redirect('documents.php', 'Invalid request');
}

?>
