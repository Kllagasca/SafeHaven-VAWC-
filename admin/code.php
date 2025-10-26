<?php
require '../config/function.php';
include('../config/db_connect.php');       // XAMPP MySQLi connection ($conn)
include('../config/supabase_connect.php'); // Supabase PDO connection ($pdo)



if (isset($_POST['saveUser'])) {
    $fname = validate($_POST['fname']);
    $lname = validate($_POST['lname']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $is_ban = validate($_POST['is_ban']) == true ? 1 : 0;
    $role = validate($_POST['role']);

    // Check if all required fields are filled
    if ($fname != '' && $lname != '' && $email != '' && $password != '') {
        try {
            // ✅ Hash password before saving
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // ✅ 1. Save to Supabase (main remote DB)
            $query = "INSERT INTO users (fname, lname, email, password, is_ban, role)
                      VALUES (:fname, :lname, :email, :password, :is_ban, :role)";
           
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
            $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':is_ban', $is_ban, PDO::PARAM_INT);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->execute();

            // ✅ 2. Save to Localhost (XAMPP MySQL)
            include('../config/db_connect.php'); // make sure this file defines $conn

            $local_sql = "INSERT INTO users (fname, lname, email, password, is_ban, role)
                          VALUES (?, ?, ?, ?, ?, ?)";
            $local_stmt = mysqli_prepare($conn, $local_sql);
            mysqli_stmt_bind_param($local_stmt, "ssssss",
                $fname, $lname, $email, $hashedPassword, $is_ban, $role
            );
            mysqli_stmt_execute($local_stmt);

            // ✅ Redirect success
            redirect('users.php', 'Admin/Focal Person/Researcher Added Successfully');

        } catch (PDOException $e) {
            // Handle Supabase errors
            error_log("Supabase DB Error: " . $e->getMessage());
            redirect('user-create.php', 'Error: ' . $e->getMessage());
        } catch (mysqli_sql_exception $ex) {
            // Handle Local DB errors
            error_log("Localhost DB Error: " . $ex->getMessage());
            redirect('user-create.php', status: 'Local DB Error: ' . $ex->getMessage());
        }
    } else {
        redirect('user-create.php', 'Please fill all the input fields');
    }
}





if (isset($_POST['updateUser'])) {
    $fname = validate($_POST['fname']);
    $lname = validate($_POST['lname']);
    $email = validate($_POST['email']);
    $password = trim($_POST['password']); // New password (if entered)
    $is_ban = validate($_POST['is_ban']) == true ? 1 : 0;
    $role = validate($_POST['role']);
    $userId = validate($_POST['userId']);

    // Retrieve user data by ID
    $user = getById('users', $userId);

    if ($user['status'] != 200) {
        redirect('user-edit.php?id=' . $userId, 'No Search Id Found');
    }

    if ($fname != '' || $lname != '' || $email != '') {
        try {
            // ✅ Base query for Supabase (PDO)
            $query = "UPDATE users SET  
                fname = :fname,
                lname = :lname,
                email = :email,
                is_ban = :is_ban,
                role = :role";

            // If password is changed, include it
            $passwordChanged = (!empty($password) && $password !== '********');
            if ($passwordChanged) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $query .= ", password = :password";
            }

            $query .= " WHERE id = :userId";

            // Prepare Supabase statement
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
            $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':is_ban', $is_ban, PDO::PARAM_INT);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

            if ($passwordChanged) {
                $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            }

            // ✅ Execute Supabase update
            $stmt->execute();

            // ✅ Update Localhost MySQL (XAMPP)
            include('../config/db_connect.php'); // ensures $conn is available

            if ($passwordChanged) {
                // With password
                $local_sql = "UPDATE users SET 
                                fname = ?, 
                                lname = ?, 
                                email = ?, 
                                password = ?, 
                                is_ban = ?, 
                                role = ?
                              WHERE id = ?";
                $local_stmt = mysqli_prepare($conn, $local_sql);
                mysqli_stmt_bind_param($local_stmt, "ssssisi",
                    $fname, $lname, $email, $hashedPassword, $is_ban, $role, $userId
                );
            } else {
                // Without password change
                $local_sql = "UPDATE users SET 
                                fname = ?, 
                                lname = ?, 
                                email = ?, 
                                is_ban = ?, 
                                role = ?
                              WHERE id = ?";
                $local_stmt = mysqli_prepare($conn, $local_sql);
                mysqli_stmt_bind_param($local_stmt, "sssisi",
                    $fname, $lname, $email, $is_ban, $role, $userId
                );
            }

            mysqli_stmt_execute($local_stmt);

            // ✅ Redirect on success
            redirect('users.php', 'Admin/Focal Person/Researcher Updated Successfully');

        } catch (PDOException $e) {
            redirect('user-edit.php', 'Supabase Error: ' . $e->getMessage());
        } catch (mysqli_sql_exception $ex) {
            redirect('user-edit.php', 'Local DB Error: ' . $ex->getMessage());
        }
    } else {
        redirect('user-edit.php', 'Please fill all the input fields');
    }
}



// Reset password for a user (generate a new random password and show it once)
if (isset($_POST['resetPassword'])) {
    $userId = validate($_POST['userId']);

    // Generate a secure random password
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*?';
    $newPass = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < 10; $i++) {
        $newPass .= $chars[random_int(0, $max)];
    }

    $hashed = password_hash($newPass, PASSWORD_DEFAULT);

    // Update password in local MySQL database
    $query = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $hashed, $userId);
        $execute = mysqli_stmt_execute($stmt);

        if ($execute) {
            // Store the plaintext temporary password in session to show once
            $_SESSION['temp_password'] = $newPass;
            $_SESSION['temp_password_user'] = $userId;

            // Redirect back to the edit page so admin can see/copy the temporary password once
            redirect('user-edit.php?id=' . $userId, 'Password reset. The temporary password is shown on the edit page.');
        } else {
            redirect('user-edit.php?id=' . $userId, 'Error resetting password: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    } else {
        redirect('user-edit.php?id=' . $userId, 'Error preparing statement: ' . mysqli_error($conn));
    }
}







if (isset($_POST['saveSocialMedia'])) {
    $name = validate($_POST['name']);
    $url = validate($_POST['url']);
    $status = validate($_POST['status']) == true ? 1 : 0;


    // Check if the required fields are not empty
    if ($name != '' || $url != '') {
        try {
            // Prepare the SQL query with placeholders
            $query = "INSERT INTO social_medias (name, url, status)
                      VALUES (:name, :url, :status)";
           
            // Prepare the statement
            $stmt = $pdo->prepare($query);
           
            // Bind the parameters to the placeholders
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':url', $url, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);


            // Execute the statement
            if ($stmt->execute()) {
                redirect('social-media.php', 'Social Media Added Successfully');
            } else {
                redirect('social-media-create.php', 'Something went wrong');
            }
        } catch (PDOException $e) {
            // Handle PDO exception
            redirect('social-media-create.php', 'Error: ' . $e->getMessage());
        }
    } else {
        redirect('social-media-create.php', 'Please fill all the input fields');
    }
}




if (isset($_POST['updateSocialMedia'])) {
    $name = validate($_POST['name']);
    $url = validate($_POST['url']);
    $status = validate($_POST['status']) == true ? 1 : 0;


    $socialMediaId = validate($_POST['socialMediaId']);


    // Check if the required fields are not empty
    if ($name != '' || $url != '') {
        try {
            // Prepare the SQL query with placeholders
            $query = "UPDATE social_medias SET
                      name = :name,
                      url = :url,
                      status = :status
                      WHERE id = :socialMediaId
                      LIMIT 1";


            // Prepare the statement
            $stmt = $pdo->prepare($query);


            // Bind the parameters to the placeholders
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':url', $url, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':socialMediaId', $socialMediaId, PDO::PARAM_INT);


            // Execute the statement
            if ($stmt->execute()) {
                redirect('social-media.php', 'Social Media Updated Successfully');
            } else {
                redirect('social-media-edit.php?id=' . $socialMediaId, 'Something went wrong');
            }
        } catch (PDOException $e) {
            // Handle PDO exception
            redirect('social-media-edit.php?id=' . $socialMediaId, 'Error: ' . $e->getMessage());
        }
    } else {
        redirect('social-media-edit.php?id=' . $socialMediaId, 'Please fill all the input fields');
    }
}




#UPDATE




if (isset($_POST['savePost'])) {
    $name = validate($_POST['name']);
    $slug = str_replace(' ', '-', strtolower($name));
    $long_description = strip_tags(validate($_POST['long_description']));
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? '1' : '0';


    $finalImage = NULL;


    // Handle image upload
    if ($_FILES['image']['size'] > 0) {
        $productImage = $_FILES['image']['name'];
        $productImage_tmpName = $_FILES['image']['tmp_name'];
        $imgFileTypes = strtolower(pathinfo($productImage, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];


        if (!in_array($imgFileTypes, $allowedTypes)) {
            redirect('service-create.php', 'Sorry, only JPG, JPEG, PNG images are allowed');
        }


        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            redirect('services.php', 'File upload error: ' . $_FILES['image']['error']);
        }


        $uploadDir = '../assets/uploads/services/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }


        if (!is_writable($uploadDir)) {
            redirect('services.php', 'Upload directory is not writable');
        }


        $newFileName = time() . '.' . $imgFileTypes;
        $productImage_folder = $uploadDir . $newFileName;


        if (!move_uploaded_file($productImage_tmpName, $productImage_folder)) {
            redirect('services.php', 'Failed to move the uploaded file');
        }


        $finalImage = 'assets/uploads/services/' . $newFileName;
    }


    // Automatically approve posts for admins or focal persons
    $approval_status = ($_SESSION['loggedInUserRole'] === 'admin' || $_SESSION['loggedInUserRole'] === 'fperson') ? 'approved' : 'approved';


    // PDO query to insert post
    try {
        $query = "INSERT INTO services (name, slug, long_description, image, status, approval_status)
                  VALUES (:name, :slug, :long_description, :image, :status, :approval_status)";
        $stmt = $pdo->prepare($query);


        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':long_description', $long_description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $finalImage, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':approval_status', $approval_status, PDO::PARAM_STR);


        // Execute the statement
        if ($stmt->execute()) {
            redirect('services.php', 'Post Added Successfully');
        } else {
            redirect('services.php', 'Something Went Wrong');
        }
    } catch (PDOException $e) {
        // Handle error
        redirect('services.php', 'Error: ' . $e->getMessage());
    }
}








#END




if (isset($_POST['updatePost'])) {
    $serviceId = validate($_POST['serviceId']);
    $name = validate($_POST['name']);
    $slug = str_replace(' ', '-', strtolower($name));
    $long_description = strip_tags(string: validate($_POST['long_description']));
    $author = validate($_POST['author']);
    $status = validate($_POST['status']) == true ? '1' : '0';


    // Retrieve existing service details
    $service = getById('services', $serviceId);
    $finalImage = $service['data']['image']; // Default to the existing image


    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        $imgFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];


        if (!in_array($imgFileType, $allowedTypes)) {
            redirect('service-edit.php', 'Sorry, only JPG, JPEG, PNG images are allowed');
        }


        $uploadDir = '../assets/uploads/services/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }


        $filename = time() . '.' . $imgFileType;
        $finalImage = 'assets/uploads/services/' . $filename;


        $deleteImage = '../' . $service['data']['image'];
        if (file_exists($deleteImage)) {
            unlink($deleteImage);
        }


        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            redirect('service-edit.php?id=' . $serviceId, 'Image upload failed');
        }
    }


    // PDO query to update the post
    try {
        $query = "UPDATE services SET
                    name = :name,
                    slug = :slug,
                    long_description = :long_description,
                    author = :author,
                    image = :image,
                    status = :status
                  WHERE id = :serviceId";
        $stmt = $pdo->prepare($query);


        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':long_description', $long_description, PDO::PARAM_STR);
        $stmt->bindParam(':author', $author, PDO::PARAM_STR);
        $stmt->bindParam(':image', $finalImage, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':serviceId', $serviceId, PDO::PARAM_INT);


        // Execute the statement
        if ($stmt->execute()) {
            redirect('service-edit.php?id=' . $serviceId, 'Post Updated Successfully');
        } else {
            redirect('service-edit.php?id=' . $serviceId, 'Something Went Wrong');
        }
    } catch (PDOException $e) {
        // Handle error
        redirect('service-edit.php?id=' . $serviceId, 'Error: ' . $e->getMessage());
    }
}




if (isset($_POST['saveDoc'])) {
    $name = validate($_POST['name']);
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? '1' : '0';


    $finalFile = NULL;


    if ($_FILES['file']['size'] > 0) {
        $uploadedFile = $_FILES['file']['name'];
        $uploadedFile_tmpName = $_FILES['file']['tmp_name'];
        $uploadedFile_folder = '../assets/files/' . $uploadedFile;


        $fileType = strtolower(pathinfo($uploadedFile, PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'docx', 'xlsx', 'txt']; // Allowed file types


        // Validate file extension
        if (!in_array($fileType, $allowedTypes)) {
            redirect('document-create.php', 'Sorry, only PDF, DOCX, XLSX, and TXT files are allowed');
        }


        // Check for file upload errors
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            redirect('documents.php', 'File upload error: ' . $_FILES['file']['error']);
        }


        // Ensure upload directory exists
        if (!is_dir('../assets/files/')) {
            mkdir('../assets/files/', 0755, true);
        }


        // Move the uploaded file
        if (!move_uploaded_file($uploadedFile_tmpName, $uploadedFile_folder)) {
            redirect('documents.php', 'Failed to move the uploaded file');
        }


        $finalFile = 'assets/files/' . $uploadedFile;
    }


    // Automatically set the approval status to 'approved'
    $approval_status = 'approved';


    // PDO query to insert the document
    try {
        $query = "INSERT INTO documents (name, file, status, approval_status)
                  VALUES (:name, :file, :status, :approval_status)";
        $stmt = $pdo->prepare($query);


        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':file', $finalFile, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':approval_status', $approval_status, PDO::PARAM_STR);


        // Execute the statement
        if ($stmt->execute()) {
            redirect('documents.php', 'Document Added Successfully.');
        } else {
            redirect('documents.php', 'Something Went Wrong');
        }
    } catch (PDOException $e) {
        // Handle error
        redirect('documents.php', 'Error: ' . $e->getMessage());
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


    // PDO query to update the document
    try {
        $query = "UPDATE documents SET
                  name = :name,
                  file = :file,
                  status = :status
                  WHERE id = :documentId";
        $stmt = $pdo->prepare($query);


        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':file', $finalFile, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);


        // Execute the statement
        if ($stmt->execute()) {
            redirect('document-edit.php?id=' . $documentId, 'Document Updated Successfully');
        } else {
            redirect('document-edit.php?id=' . $documentId, 'Something Went Wrong');
        }
    } catch (PDOException $e) {
        // Handle error
        redirect('document-edit.php?id=' . $documentId, 'Error: ' . $e->getMessage());
    }
}




if (isset($_POST['saveImage'])) {
    $name = validate($_POST['name']);
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? '1' : '0';


    $finalImage = NULL;


    if ($_FILES['image']['size'] > 0) {
        $productImage = $_FILES['image']['name'];
        $productImage_tmpName = $_FILES['image']['tmp_name'];
        $productImage_folder = '../assets/uploads/carousel/' . $productImage;


        $imgFileTypes = strtolower(pathinfo($productImage, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];


        if (!in_array($imgFileTypes, $allowedTypes)) {
            redirect('carousel-create.php', 'Sorry, only JPG, JPEG, PNG images are allowed');
        }


        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            redirect('carousel.php', 'File upload error: ' . $_FILES['image']['error']);
        }


        if (!is_dir('../assets/uploads/carousel/')) {
            mkdir('../assets/uploads/carousel/', 0755, true);
        }


        if (!is_writable('../assets/uploads/carousel/')) {
            redirect('carousel.php', 'Upload directory is not writable');
        }


        if (!move_uploaded_file($productImage_tmpName, $productImage_folder)) {
            redirect('carousel.php', 'Failed to move the uploaded file');
        }


        $finalImage = 'assets/uploads/carousel/' . $productImage;
    }


    // PDO query to insert the image
    try {
        $query = "INSERT INTO carousel (name, image, status) VALUES (:name, :image, :status)";
        $stmt = $pdo->prepare($query);


        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':image', $finalImage, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);


        // Execute the statement
        if ($stmt->execute()) {
            redirect('carousel.php', 'Image Added Successfully');
        } else {
            redirect('carousel.php', 'Something Went Wrong');
        }
    } catch (PDOException $e) {
        // Handle error
        redirect('carousel.php', 'Error: ' . $e->getMessage());
    }
}




if (isset($_POST['updateImage'])) {
    $imageId = validate($_POST['imageId']);
    $name = validate($_POST['name']);
    $status = validate($_POST['status']) == true ? '1' : '0';


    // Retrieve existing carousel details
    $carousel = getById('carousel', $imageId);
    $finalImage = $carousel['data']['image']; // Default to the existing image


    // Handle image upload
    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        $imgFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));


        // Validate image type
        if ($imgFileType != 'jpg' && $imgFileType != 'jpeg' && $imgFileType != 'png') {
            redirect('carousel-edit.php', 'Sorry, only JPG, JPEG, PNG images are allowed');
        }


        $path = "../assets/uploads/carousel/";
        $imgExt = pathinfo($image, PATHINFO_EXTENSION);
        $filename = time() . '.' . $imgExt;


        // Set the final image path
        $finalImage = 'assets/uploads/carousel/' . $filename;


        // Delete the old image if it exists
        $deleteImage = '../' . $carousel['data']['image'];
        if (file_exists($deleteImage)) {
            unlink($deleteImage);
        }


        // Move the uploaded file to the correct location
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $path . $filename)) {
            redirect('carousel-edit.php?id=' . $imageId, 'Image upload failed');
        }
    }


    // PDO query to update the image
    try {
        $query = "UPDATE carousel SET
                  name = :name,
                  image = :image,
                  status = :status
                  WHERE id = :imageId";


        $stmt = $pdo->prepare($query);


        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':image', $finalImage, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':imageId', $imageId, PDO::PARAM_INT);


        // Execute the statement
        if ($stmt->execute()) {
            redirect('carousel-edit.php?id=' . $imageId, 'Image Updated Successfully');
        } else {
            redirect('carousel-edit.php?id=' . $imageId, 'Something Went Wrong');
        }
    } catch (PDOException $e) {
        // Handle error
        redirect('carousel-edit.php?id=' . $imageId, 'Error: ' . $e->getMessage());
    }
}




if (isset($_POST['saveNews'])) {
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


        if (!in_array($imgFileTypes, $allowedTypes)) {
            redirect('news-create.php', 'Sorry, only JPG, JPEG, PNG images are allowed');
        }


        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            redirect('news.php', 'File upload error: ' . $_FILES['image']['error']);
        }


        $uploadDir = '../assets/uploads/news/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }


        if (!is_writable($uploadDir)) {
            redirect('news.php', 'Upload directory is not writable');
        }


        $newFileName = time() . '.' . $imgFileTypes;
        $productImage_folder = $uploadDir . $newFileName;


        if (!move_uploaded_file($productImage_tmpName, $productImage_folder)) {
            redirect('news.php', 'Failed to move the uploaded file');
        }


        $finalImage = 'assets/uploads/news/' . $newFileName;
    }


    // PDO query to insert the news post
    try {
        $query = "INSERT INTO news (name, slug, long_description, image, status)
                  VALUES (:name, :slug, :long_description, :image, :status)";


        $stmt = $pdo->prepare($query);


        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':long_description', $long_description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $finalImage, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);


        // Execute the statement
        if ($stmt->execute()) {
            redirect('news.php', 'News Added Successfully');
        } else {
            redirect('news.php', 'Something Went Wrong');
        }
    } catch (PDOException $e) {
        // Handle error
        redirect('news.php', 'Error: ' . $e->getMessage());
    }
}




#END


if (isset($_POST['updateNews'])) {
    $newsId = validate($_POST['newsId']);
    $name = validate($_POST['name']);
    $slug = str_replace(' ', '-', strtolower($name));
    $long_description = strip_tags(validate($_POST['long_description']));
    $status = validate($_POST['status']) == true ? '1' : '0';


    // Retrieve existing news details
    $news = getById('news', $newsId);
    $finalImage = $news['data']['image']; // Default to the existing image


    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        $imgFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];


        if (!in_array($imgFileType, $allowedTypes)) {
            redirect('news-edit.php', 'Sorry, only JPG, JPEG, PNG images are allowed');
        }


        $uploadDir = '../assets/uploads/news/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }


        $filename = time() . '.' . $imgFileType;
        $finalImage = 'assets/uploads/news/' . $filename;


        $deleteImage = '../' . $news['data']['image'];
        if (file_exists($deleteImage)) {
            unlink($deleteImage);
        }


        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            redirect('news-edit.php?id=' . $newsId, 'Image upload failed');
        }
    }


    // PDO query to update the news post
    try {
        $query = "UPDATE news SET
                  name = :name,
                  slug = :slug,
                  long_description = :long_description,
                  image = :image,
                  status = :status
                  WHERE id = :newsId";


        $stmt = $pdo->prepare($query);


        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':long_description', $long_description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $finalImage, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':newsId', $newsId, PDO::PARAM_INT);


        // Execute the statement
        if ($stmt->execute()) {
            redirect('news-edit.php?id=' . $newsId, 'Post Updated Successfully');
        } else {
            redirect('news-edit.php?id=' . $newsId, 'Something Went Wrong');
        }
    } catch (PDOException $e) {
        // Handle error
        redirect('news-edit.php?id=' . $newsId, 'Error: ' . $e->getMessage());
    }
}





if (isset($_POST['saveCase'])) {

    $casenum = validate($_POST['casenum']);
    $title = validate($_POST['title']);
    $status = isset($_POST['status']) ? $_POST['status'] : '0';
    $barangay = validate($_POST['barangay']);
    $incident_date = validate($_POST['date']);
    $contactp = validate($_POST['contactp']);

    $complainant = validate($_POST['complainant']);
    $cage = validate($_POST['cage']);
    $cnum = validate($_POST['cnum']);
    $caddress = validate($_POST['caddress']);

    $respondent = validate($_POST['respondent']);
    $rage = validate($_POST['rage']);
    $rnum = validate($_POST['rnum']);
    $raddress = validate($_POST['raddress']);

    $raw_description = $_POST['long_description'];
    $sanitized_description = strip_tags($raw_description); 
    $long_description = validate($sanitized_description);
    $finalImage = NULL;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $imageFile = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $imageFile['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            redirect('cases-create.php', 'Only JPG, JPEG, PNG images are allowed');
        }

        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            redirect('cases.php', 'File upload error: ' . $imageFile['error']);
        }

        $uploadDir = '../assets/uploads/cases/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!is_writable($uploadDir)) {
            redirect('cases.php', 'Upload directory is not writable');
        }

        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $newFileName = time() . '_' . uniqid() . '.' . $ext;
        $destination = $uploadDir . $newFileName;

        if (!move_uploaded_file($imageFile['tmp_name'], $destination)) {
            redirect('cases.php', 'Failed to move uploaded file');
        }

        $finalImage = 'assets/uploads/cases/' . $newFileName;
    }

    try {
        // --- Save to Supabase (PDO) ---
        $query = "INSERT INTO cases ( caseno, title, status, brgy, date, contactp,
                                      comp_name, comp_age, comp_num, comp_address,
                                      resp_name, resp_age, resp_num, resp_address,
                                      long_description, image)
                  VALUES ( :casenum, :title, :status, :barangay, :incident_date, :contactp,
                          :complainant, :cage, :cnum, :caddress,
                          :respondent, :rage, :rnum, :raddress,
                          :long_description, :image)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':casenum'          => $casenum,
            ':title'            => $title,
            ':status'           => $status,
            ':barangay'         => $barangay,
            ':incident_date'    => $incident_date,
            ':contactp'         => $contactp,
            ':complainant'      => $complainant,
            ':cage'             => $cage,
            ':cnum'             => $cnum,
            ':caddress'         => $caddress,
            ':respondent'       => $respondent,
            ':rage'             => $rage,
            ':rnum'             => $rnum,
            ':raddress'         => $raddress,
            ':long_description' => $long_description,
            ':image'            => $finalImage
        ]);

        // save to localhost
$insertQuery = "INSERT INTO `cases` (
    `caseno`, `title`, `status`, `brgy`, `date`, `contactp`,
    `comp_name`, `comp_age`, `comp_num`, `comp_address`,
    `resp_name`, `resp_age`, `resp_num`, `resp_address`,
    `long_description`, `image`
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

if ($finalImage === NULL) {
    $finalImage = '';
}


$stmt2 = mysqli_prepare($conn, $insertQuery);
if (!$stmt2) {
    die("Prepare failed: " . mysqli_error($conn) . "\nQuery: " . $insertQuery);
}

if (!mysqli_stmt_bind_param(
    $stmt2,
    "ssssssssssssssss",
    $casenum, $title, $status, $barangay, $incident_date, $contactp,
    $complainant, $cage, $cnum, $caddress,
    $respondent, $rage, $rnum, $raddress,
    $long_description, $finalImage
)) {
    die("Bind failed: " . mysqli_stmt_error($stmt2));
}

if (!mysqli_stmt_execute($stmt2)) {
    die("Execute failed: " . mysqli_stmt_error($stmt2));
}

mysqli_stmt_close($stmt2);

redirect('cases.php', 'Case Saved Successfully');

    } catch (Exception $e) {
        redirect('cases.php', 'Error: ' . $e->getMessage());
    }
}



if (isset($_POST['updateCase'])) {

    $caseno = validate($_POST['caseno']);
    $title = validate($_POST['title']);
    $status = validate($_POST['status']);
    $barangay = validate($_POST['barangay']);
    $incident_date = validate($_POST['date']);
    $contactp = validate($_POST['contactp']);

    $complainant = validate($_POST['complainant']);
    $cage = validate($_POST['cage']);
    $cnum = validate($_POST['cnum']);
    $caddress = validate($_POST['caddress']);

    $respondent = validate($_POST['respondent']);
    $rage = validate($_POST['rage']);
    $rnum = validate($_POST['rnum']);
    $raddress = validate($_POST['raddress']);

    $long_description = strip_tags(validate($_POST['long_description']));
    $finalImage = validate($_POST['old_image']);

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $imageFile = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $imageFile['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            redirect('cases-edit.php?caseno=' . $caseno, 'Only JPG, JPEG, PNG images are allowed');
        }

        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            redirect('cases.php', 'File upload error: ' . $imageFile['error']);
        }

        $uploadDir = '../assets/uploads/cases/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!is_writable($uploadDir)) {
            redirect('cases.php', 'Upload directory is not writable');
        }

        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $newFileName = time() . '_' . uniqid() . '.' . $ext;
        $destination = $uploadDir . $newFileName;

        if (!move_uploaded_file($imageFile['tmp_name'], $destination)) {
            redirect('cases.php', 'Failed to move uploaded file');
        }

        // Delete old image if exists
        if (!empty($finalImage) && file_exists('../' . $finalImage)) {
            unlink('../' . $finalImage);
        }

        $finalImage = 'assets/uploads/cases/' . $newFileName;
    }

    // prevent NULL issue
    if ($finalImage === NULL) {
        $finalImage = '';
    }

    try {
        // --- Update Supabase (PDO) ---
        $query = "UPDATE cases SET 
            title = :title,
            status = :status,
            brgy = :barangay,
            date = :incident_date,
            contactp = :contactp,
            comp_name = :complainant,
            comp_age = :cage,
            comp_num = :cnum,
            comp_address = :caddress,
            resp_name = :respondent,
            resp_age = :rage,
            resp_num = :rnum,
            resp_address = :raddress,
            long_description = :long_description,
            image = :image
        WHERE caseno = :caseno";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':title'            => $title,
            ':status'           => $status,
            ':barangay'         => $barangay,
            ':incident_date'    => $incident_date,
            ':contactp'         => $contactp,
            ':complainant'      => $complainant,
            ':cage'             => $cage,
            ':cnum'             => $cnum,
            ':caddress'         => $caddress,
            ':respondent'       => $respondent,
            ':rage'             => $rage,
            ':rnum'             => $rnum,
            ':raddress'         => $raddress,
            ':long_description' => $long_description,
            ':image'            => $finalImage,
            ':caseno'           => $caseno
        ]);

        // --- Update XAMPP (MySQLi) ---
        $updateQuery = "UPDATE `cases` SET
            `title` = ?,
            `status` = ?,
            `brgy` = ?,
            `date` = ?,
            `contactp` = ?,
            `comp_name` = ?,
            `comp_age` = ?,
            `comp_num` = ?,
            `comp_address` = ?,
            `resp_name` = ?,
            `resp_age` = ?,
            `resp_num` = ?,
            `resp_address` = ?,
            `long_description` = ?,
            `image` = ?
        WHERE `caseno` = ?";

        $stmt2 = mysqli_prepare($conn, $updateQuery);
        if (!$stmt2) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $stmt2,
            "ssssssssssssssss",
            $title, $status, $barangay, $incident_date, $contactp,
            $complainant, $cage, $cnum, $caddress,
            $respondent, $rage, $rnum, $raddress,
            $long_description, $finalImage, $caseno
        );

        if (!mysqli_stmt_execute($stmt2)) {
            die("Execute failed: " . mysqli_stmt_error($stmt2));
        }

        mysqli_stmt_close($stmt2);

        redirect('cases.php', 'Case Updated Successfully');

    } catch (Exception $e) {
        redirect('cases.php', 'Error: ' . $e->getMessage());
    }
}