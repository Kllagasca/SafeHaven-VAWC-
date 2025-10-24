<?php
require '../config/function.php';
#UPDATED
#START

if (isset($_POST['savePost'])) {
    $name = validate($_POST['name']);
    $slug = str_replace(' ', '-', strtolower($name));
    $long_description = strip_tags(validate($_POST['long_description']));
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? '1' : '0';

    $finalImage = NULL;

    if ($_FILES['image']['size'] > 0) {
        $productImage = $_FILES['image']['name'];
        $productImage_tmpName = $_FILES['image']['tmp_name'];
        $imgFileTypes = strtolower(pathinfo($productImage, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        // Check if the file type is allowed
        if (!in_array($imgFileTypes, $allowedTypes)) {
            redirect('service-create.php', 'Sorry, only JPG, JPEG, PNG images are allowed');
        }

        // Check for upload errors
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            redirect('services.php', 'File upload error: ' . $_FILES['image']['error']);
        }

        // Create the upload directory if it doesn't exist
        $uploadDir = '../assets/uploads/services/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Check if the upload directory is writable
        if (!is_writable($uploadDir)) {
            redirect('services.php', 'Upload directory is not writable');
        }

        // Generate a new unique filename using time() and the original file extension
        $newFileName = time() . '.' . $imgFileTypes;
        $productImage_folder = $uploadDir . $newFileName;

        // Move the uploaded file to the designated folder
        if (!move_uploaded_file($productImage_tmpName, $productImage_folder)) {
            redirect('services.php', 'Failed to move the uploaded file');
        }

        $finalImage = 'assets/uploads/services/' . $newFileName; // Store the relative path to save in DB
    }

    // Automatically approve posts if the user is a focal person
    $approval_status = ($_SESSION['loggedInUserRole'] === 'fperson' || $_SESSION['loggedInUserRole'] === 'admin') ? 'approved' : 'pending';

    // Using PDO for database interaction
    $query = "INSERT INTO services (name, slug, long_description, image, status, approval_status) 
              VALUES (:name, :slug, :long_description, :image, :status, :approval_status)";
    $stmt = $pdo->prepare($query);

    // Bind parameters to the placeholders
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
    $stmt->bindParam(':long_description', $long_description, PDO::PARAM_STR);
    $stmt->bindParam(':image', $finalImage, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':approval_status', $approval_status, PDO::PARAM_STR);

    // Execute the statement and handle the result
    if ($stmt->execute()) {
        redirect('services.php', 'Post Added Successfully');
    } else {
        redirect('services.php', 'Something Went Wrong');
    }
}



#END


if (isset($_POST['updatePost'])) {
    $serviceId = validate($_POST['serviceId']);
    $name = validate($_POST['name']);
    $slug = str_replace(' ', '-', strtolower($name));
    $long_description = strip_tags(validate($_POST['long_description']));
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? '1' : '0';

    // Retrieve existing service details
    $service = getById('services', $serviceId);
    $finalImage = $service['data']['image']; // Default to the existing image

    // Handle image upload
    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        $imgFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        // Validate image type
        if (!in_array($imgFileType, ['jpg', 'jpeg', 'png'])) {
            redirect('service-edit.php', 'Sorry, only JPG, JPEG, PNG images are allowed');
        }

        $path = "../assets/uploads/services/";
        $filename = time() . '.' . $imgFileType;

        // Set the final image path
        $finalImage = 'assets/uploads/services/' . $filename;

        // Delete the old image if it exists
        $deleteImage = '../' . $service['data']['image'];
        if (file_exists($deleteImage)) {
            unlink($deleteImage);
        }

        // Move the uploaded file to the correct location
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $path . $filename)) {
            redirect('service-edit.php?id=' . $serviceId, 'Image upload failed');
        }
    }

    // Using PDO for database interaction
    $query = "UPDATE services SET 
        name = :name,
        slug = :slug,
        long_description = :long_description,
        image = :image,
        status = :status,
        approval_status = 'pending'  -- Change approval status to 'pending'
        WHERE id = :serviceId";

    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
    $stmt->bindParam(':long_description', $long_description, PDO::PARAM_STR);
    $stmt->bindParam(':image', $finalImage, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':serviceId', $serviceId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        redirect('service-edit.php?id=' . $serviceId, 'Post updated successfully and sent for re-approval.');
    } else {
        redirect('service-edit.php?id=' . $serviceId, 'Something went wrong.');
    }
}




if (isset($_POST['saveDoc'])) {
    $name = validate($_POST['name']);
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? '1' : '0';

    $finalFile = NULL;

    if ($_FILES['file']['size'] > 0) {
        $uploadedFile = $_FILES['file']['name'];
        $uploadedFile_tmpName = $_FILES['file']['tmp_name'];
        $fileType = strtolower(pathinfo($uploadedFile, PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'docx', 'xlsx', 'txt'];

        if (!in_array($fileType, $allowedTypes)) {
            redirect('document-create.php', 'Sorry, only PDF, DOCX, XLSX, and TXT files are allowed');
        }

        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            redirect('documents.php', 'File upload error: ' . $_FILES['file']['error']);
        }

        // Create the upload directory if it doesn't exist
        $uploadDir = '../assets/files/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!is_writable($uploadDir)) {
            redirect('documents.php', 'Upload directory is not writable');
        }

        $newFileName = time() . '.' . $fileType;
        $uploadedFilePath = $uploadDir . $newFileName;

        if (!move_uploaded_file($uploadedFile_tmpName, $uploadedFilePath)) {
            redirect('documents.php', 'Failed to move the uploaded file');
        }

        $finalFile = 'assets/files/' . $newFileName;
    }

    // Automatically approve documents if the user is a focal person
    $approval_status = ($_SESSION['loggedInUserRole'] === 'fperson' || $_SESSION['loggedInUserRole'] === 'admin') ? 'approved' : 'pending';

    // Using PDO for database interaction
    $query = "INSERT INTO documents (name, file, status, approval_status) 
              VALUES (:name, :file, :status, :approval_status)";
    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':file', $finalFile, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':approval_status', $approval_status, PDO::PARAM_STR);

    if ($stmt->execute()) {
        redirect('documents.php', 'Document Added Successfully');
    } else {
        redirect('documents.php', 'Something Went Wrong');
    }
}


if (isset($_POST['updateDoc'])) {
    $documentId = validate($_POST['documentId']);
    $name = validate($_POST['name']);
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? '1' : '0';

    // Retrieve existing document details
    $document = getById('documents', $documentId);
    $finalFile = $document['data']['file']; // Default to the existing file

    // Handle file upload
    if ($_FILES['file']['size'] > 0) {
        $uploadedFile = $_FILES['file']['name'];
        $fileType = strtolower(pathinfo($uploadedFile, PATHINFO_EXTENSION));

        // Validate file type
        $allowedTypes = ['pdf', 'docx', 'xlsx', 'txt'];
        if (!in_array($fileType, $allowedTypes)) {
            redirect('document-edit.php', 'Sorry, only PDF, DOCX, XLSX, and TXT files are allowed');
        }

        $path = "../assets/files/";
        $filename = time() . '.' . $fileType;

        // Set the final file path
        $finalFile = 'assets/files/' . $filename;

        // Delete the old file if it exists
        $deleteFile = '../' . $document['data']['file'];
        if (file_exists($deleteFile)) {
            unlink($deleteFile);
        }

        // Move the uploaded file to the correct location
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $path . $filename)) {
            redirect('document-edit.php?id=' . $documentId, 'File upload failed');
        }
    }

    // Update the database using PDO
    $query = "UPDATE documents SET 
        name = :name,
        file = :file,
        status = :status,
        approval_status = 'pending' -- Change approval status to 'pending'
        WHERE id = :documentId";

    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':file', $finalFile, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        redirect('document-edit.php?id=' . $documentId, 'Document updated successfully and sent for re-approval.');
    } else {
        redirect('document-edit.php?id=' . $documentId, 'Something went wrong.');
    }
}
?>
