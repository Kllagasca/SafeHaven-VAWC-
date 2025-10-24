<?php include('includes/header.php'); ?>
<?php
require_once '../config/function.php'; // Validation functions

if (isset($_POST['saveDoc'])) {
    // Collect and validate form data
    $name = validate($_POST['name']);
    $status = isset($_POST['status']) ? 1 : 0; // Convert checkbox value to 1 or 0

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        
        // Set the upload directory and file path
        $uploadDir = '../uploads/documents/';
        $filePath = $uploadDir . basename($fileName);

        // Check if the file is a valid document (you can add more checks for file types if needed)
        $allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($fileType, $allowedFileTypes)) {
            redirect('documents.php', 'Invalid file type. Only PDF and Word documents are allowed.');
            exit();
        }

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            // Prepare the SQL query to insert the new document
            $query = "INSERT INTO documents (name, file_path, approval_status, status) 
                      VALUES (:name, :file_path, 'pending', :status)";

            // Prepare the statement using PDO
            $stmt = $pdo->prepare($query);

            // Bind parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':file_path', $filePath);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);

            // Execute the statement and check for success
            if ($stmt->execute()) {
                // Redirect with success message
                redirect('documents.php', 'Document added successfully.');
            } else {
                // Redirect with error message
                redirect('documents.php', 'Failed to add document.');
            }
        } else {
            // Redirect if the file upload failed
            redirect('documents.php', 'Failed to upload file.');
        }
    } else {
        // Redirect if no file was uploaded or there was an error
        redirect('documents.php', 'No file uploaded or there was an error.');
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Add Document
                    <a href="documents.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

            <form action="code.php" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                <label>Document Name</label>
                <input type="text" name="name" required class="form-control"/>
                </div>

                <div class="mb-3">
                <label>Upload File</label>
                <input type="file" name="file" class="form-control"/>
                </div>

                <div class="mb-3">
                <label>Status (checked=hidden, un-checked=visible)</label>
                <br/>
                <input type="checkbox" name="status" style="width:30px;height:30px;"/>
                </div>

                <div class="mb-3 text-end">
                    <button type="submit" name="saveDoc" class="btn btn-primary">Save Document</button>
                </div>
                
            </form>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
