<?php
// Handler for focal-person actions (saveCase/updateCase)
require_once __DIR__ . '/../config/function.php';
include('authentication.php');
include __DIR__ . '/../config/db_connect.php';       // MySQLi ($conn)
include __DIR__ . '/../config/supabase_connect.php'; // PDO ($pdo)

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

    // If current user is a focal-person, enforce their session barangay to prevent tampering
    if (isset($_SESSION['loggedInUserRole']) && $_SESSION['loggedInUserRole'] === 'fperson') {
        $sessionBrgy = isset($_SESSION['loggedInUser']['barangay']) ? $_SESSION['loggedInUser']['barangay'] : null;
        if (!empty($sessionBrgy)) {
            $barangay = $sessionBrgy;
        }
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $imageFile = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $imageFile['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            redirect('case-create.php', 'Only JPG, JPEG, PNG images are allowed');
        }

        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            redirect('cases.php', 'File upload error: ' . $imageFile['error']);
        }

        $uploadDir = __DIR__ . '/../assets/uploads/cases/';
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
    // include created_by (user id) so we can restrict visibility to owner + admin
    $createdBy = isset($_SESSION['loggedInUser']['id']) ? $_SESSION['loggedInUser']['id'] : (isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null);

    // Use RETURNING to capture the Supabase/Postgres generated id for this row
    $query = "INSERT INTO cases ( caseno, title, status, brgy, date, contactp,
                                      comp_name, comp_age, comp_num, comp_address,
                                      resp_name, resp_age, resp_num, resp_address,
                      long_description, image, created_by)
                  VALUES ( :casenum, :title, :status, :barangay, :incident_date, :contactp,
                          :complainant, :cage, :cnum, :caddress,
                          :respondent, :rage, :rnum, :raddress,
              :long_description, :image, :created_by)
                  RETURNING id";

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
            ':image'            => $finalImage,
            ':created_by'       => $createdBy
        ]);

        // Fetch the returned Supabase/Postgres id when available
        $supabaseId = null;
        try {
            $supabaseId = $stmt->fetchColumn();
        } catch (Exception $e) {
            try { $supabaseId = $pdo->lastInsertId(); } catch (Exception $__){ $supabaseId = null; }
        }

        // save to localhost (MySQLi)
        if ($finalImage === NULL) { $finalImage = ''; }

        // Check if local cases table has supabase_id column
        $hasSupabaseId = false;
        try {
            $colCheck = mysqli_query($conn, "SHOW COLUMNS FROM `cases` LIKE 'supabase_id'");
            if ($colCheck && mysqli_num_rows($colCheck) > 0) {
                $hasSupabaseId = true;
            }
        } catch (Exception $__) {
            $hasSupabaseId = false;
        }

        if ($hasSupabaseId) {
            $insertQuery = "INSERT INTO `cases` (
                `caseno`, `title`, `status`, `brgy`, `date`, `contactp`,
                `comp_name`, `comp_age`, `comp_num`, `comp_address`,
                `resp_name`, `resp_age`, `resp_num`, `resp_address`,
                `long_description`, `image`, `created_by`, `supabase_id`, `supabase_id_created_at`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $supabaseIdVal = $supabaseId !== null ? (string)$supabaseId : null;
            $supabaseIdCreated = $supabaseIdVal ? date('Y-m-d H:i:s') : null;

            $stmt2 = mysqli_prepare($conn, $insertQuery);
            if (!$stmt2) { die("Prepare failed: " . mysqli_error($conn) . "\nQuery: " . $insertQuery); }

            mysqli_stmt_bind_param(
                $stmt2,
                "ssssssssssssssssisss",
                $casenum, $title, $status, $barangay, $incident_date, $contactp,
                $complainant, $cage, $cnum, $caddress,
                $respondent, $rage, $rnum, $raddress,
                $long_description, $finalImage, $createdBy, $supabaseIdVal, $supabaseIdCreated
            );

            // mysqli may fail for many reasons (unique constraint etc.) — log instead of die
            if (!mysqli_stmt_execute($stmt2)) {
                error_log('Local MySQL insert failed (focal-person with supabase_id): ' . mysqli_stmt_error($stmt2));
            }
            mysqli_stmt_close($stmt2);
        } else {
            // Fallback: insert without supabase_id
            $insertQuery = "INSERT INTO `cases` (
                `caseno`, `title`, `status`, `brgy`, `date`, `contactp`,
                `comp_name`, `comp_age`, `comp_num`, `comp_address`,
                `resp_name`, `resp_age`, `resp_num`, `resp_address`,
                `long_description`, `image`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt2 = mysqli_prepare($conn, $insertQuery);
            if (!$stmt2) { die("Prepare failed: " . mysqli_error($conn) . "\nQuery: " . $insertQuery); }

            mysqli_stmt_bind_param(
                $stmt2,
                "ssssssssssssssssi",
                $casenum, $title, $status, $barangay, $incident_date, $contactp,
                $complainant, $cage, $cnum, $caddress,
                $respondent, $rage, $rnum, $raddress,
                $long_description, $finalImage, $createdBy
            );

            if (!mysqli_stmt_execute($stmt2)) { die("Execute failed: " . mysqli_stmt_error($stmt2)); }
            mysqli_stmt_close($stmt2);
        }

    // get local MySQL insert id to use as a stable, unique reference for links
    $localCaseId = mysqli_insert_id($conn);

        // Create a notification for admins about the new case (best-effort; non-blocking)
        $creatorName = isset($_SESSION['loggedInUser']['fname']) ? trim($_SESSION['loggedInUser']['fname'] . ' ' . ($_SESSION['loggedInUser']['lname'] ?? '')) : 'Focal Person';
        $notificationTitle = 'New case submitted';
        $notificationMessage = "Case {$casenum} was created by {$creatorName} in {$barangay}.";
        // Prefer linking by the local primary id to avoid ambiguity when caseno is not unique
        if (!empty($localCaseId)) {
            $notificationLink = 'admin/case-details.php?id=' . urlencode($localCaseId);
        } else {
            $notificationLink = 'admin/case-details.php?caseno=' . urlencode($casenum);
        }

        // Insert into Supabase/PDO notifications table (if exists)
        try {
            $notifQuery = "INSERT INTO notifications (recipient_role, title, message, link, is_read, created_at)
                           VALUES (:recipient_role, :title, :message, :link, 0, NOW())";
            $nstmt = $pdo->prepare($notifQuery);
            $nstmt->execute([
                ':recipient_role' => 'admin',
                ':title' => $notificationTitle,
                ':message' => $notificationMessage,
                ':link' => $notificationLink,
            ]);
            // Debug: log successful supabase insert (write lastInsertId if available)
            try {
                $logPath = __DIR__ . '/../assets/logs/notification_debug.log';
                if (!is_dir(dirname($logPath))) mkdir(dirname($logPath), 0755, true);
                $insertId = null;
                try { $insertId = $pdo->lastInsertId(); } catch (Exception $__) { $insertId = null; }
                $msg = date('c') . " | Supabase notification inserted" . ($insertId ? " ID={$insertId}" : "") . " | case={$casenum}\n";
                file_put_contents($logPath, $msg, FILE_APPEND | LOCK_EX);
            } catch (Exception $__) {}
        } catch (Exception $e) {
            // Log the exception to a debug file so we can inspect why Supabase insert failed
            try {
                $logPath = __DIR__ . '/../assets/logs/notification_debug.log';
                if (!is_dir(dirname($logPath))) mkdir(dirname($logPath), 0755, true);
                $msg = date('c') . " | Supabase notification ERROR: " . $e->getMessage() . " | case={$casenum}\n";
                file_put_contents($logPath, $msg, FILE_APPEND | LOCK_EX);
            } catch (Exception $__) {}
        }

        // Local MySQL notification writes removed — notifications are consolidated in Supabase only.

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
            redirect('case-edit.php?caseno=' . $caseno, 'Only JPG, JPEG, PNG images are allowed');
        }

        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            redirect('cases.php', 'File upload error: ' . $imageFile['error']);
        }

        $uploadDir = __DIR__ . '/../assets/uploads/cases/';
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
        if (!is_writable($uploadDir)) { redirect('cases.php', 'Upload directory is not writable'); }

        $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $newFileName = time() . '_' . uniqid() . '.' . $ext;
        $destination = $uploadDir . $newFileName;

        if (!move_uploaded_file($imageFile['tmp_name'], $destination)) {
            redirect('cases.php', 'Failed to move uploaded file');
        }

        // Delete old image if exists
        if (!empty($finalImage) && file_exists(__DIR__ . '/../' . $finalImage)) {
            @unlink(__DIR__ . '/../' . $finalImage);
        }

        $finalImage = 'assets/uploads/cases/' . $newFileName;
    }

    if ($finalImage === NULL) { $finalImage = ''; }

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
        if (!$stmt2) { die("Prepare failed: " . mysqli_error($conn)); }

        mysqli_stmt_bind_param(
            $stmt2,
            "ssssssssssssssss",
            $title, $status, $barangay, $incident_date, $contactp,
            $complainant, $cage, $cnum, $caddress,
            $respondent, $rage, $rnum, $raddress,
            $long_description, $finalImage, $caseno
        );

        if (!mysqli_stmt_execute($stmt2)) { die("Execute failed: " . mysqli_stmt_error($stmt2)); }
        mysqli_stmt_close($stmt2);

        redirect('cases.php', 'Case Updated Successfully');

    } catch (Exception $e) {
        redirect('cases.php', 'Error: ' . $e->getMessage());
    }

}

?>
<?php
require '../config/function.php';


if (isset($_POST['saveUser'])) {
    $fname = validate($_POST['fname']);
    $lname = validate($_POST['lname']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $is_ban = validate($_POST['is_ban']) == true ? 1 : 0;
    $role = validate($_POST['role']);

    if ($fname != '' || $lname != '' || $email != '' || $password != '') {
        try {
            // Prepare the SQL statement with placeholders
            $query = "INSERT INTO users (fname, lname, email, password, is_ban, role) 
                      VALUES (:fname, :lname, :email, :password, :is_ban, :role)";
            
            // Prepare the statement
            $stmt = $pdo->prepare($query);
            
            // Bind parameters to the placeholders
            $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
            $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':is_ban', $is_ban, PDO::PARAM_INT);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);

            // Execute the statement
            if ($stmt->execute()) {
                redirect('users.php', 'Admin/Focal Person/Researcher Added Successfully');
            } else {
                redirect('user-create.php', 'Something went wrong');
            }
        } catch (PDOException $e) {
            // Handle PDO exception
            redirect('user-create.php', 'Error: ' . $e->getMessage());
        }
    } else {
        redirect('user-create.php', 'Please fill all the input fields');
    }
}


if (isset($_POST['updateUser'])) {
    $fname = validate($_POST['fname']);
    $lname = validate($_POST['lname']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $is_ban = validate($_POST['is_ban']) == true ? 1 : 0;
    $role = validate($_POST['role']);
    $userId = validate($_POST['userId']);

    // Retrieve user data by ID
    $user = getById('users', $userId);

    if ($user['status'] != 200) {
        redirect('user-edit.php?id=' . $userId, 'No Search Id Found');
    }

    // Check if any required fields are filled
    if ($fname != '' || $lname != '' || $email != '' || $password != '') {
        try {
            // Prepare the SQL query with placeholders
            $query = "UPDATE users SET  
                fname = :fname, 
                lname = :lname, 
                email = :email, 
                password = :password, 
                is_ban = :is_ban, 
                role = :role
                WHERE id = :userId";
            
            // Prepare the statement
            $stmt = $pdo->prepare($query);
            
            // Bind the parameters to the placeholders
            $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
            $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':is_ban', $is_ban, PDO::PARAM_INT);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

            // Execute the statement
            if ($stmt->execute()) {
                redirect('users.php', 'Admin/Focal Person/Researcher Updated Successfully');
            } else {
                redirect('user-create.php', 'Something went wrong');
            }
        } catch (PDOException $e) {
            // Handle PDO exception
            redirect('user-create.php', 'Error: ' . $e->getMessage());
        }
    } else {
        redirect('user-create.php', 'Please fill all the input fields');
    }
}


if (isset($_POST['saveSocialMedia'])) {
    // Validate and sanitize inputs
    $name = validate($_POST['name']);
    $url = validate($_POST['url']);
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? 1 : 0;

    // Check if the required fields are not empty
    if ($name != '' && $url != '') {
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
    // Sanitize and validate inputs
    $name = validate($_POST['name']);
    $url = validate($_POST['url']);
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? 1 : 0;

    $socialMediaId = validate($_POST['socialMediaId']);

    // Check if the required fields are not empty
    if ($name != '' && $url != '') {
        try {
            // Prepare the SQL query with placeholders
            $query = "UPDATE social_medias SET 
                      name = :name, 
                      url = :url, 
                      status = :status 
                      WHERE id = :socialMediaId";

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
    $approval_status = ($_SESSION['loggedInUserRole'] === 'fperson' || $_SESSION['loggedInUserRole'] === 'fperson') ? 'approved' : 'approved';

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
    $long_description = strip_tags(validate($_POST['long_description']));
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
                    image = :image, 
                    status = :status 
                  WHERE id = :serviceId";
        $stmt = $pdo->prepare($query);

        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':long_description', $long_description, PDO::PARAM_STR);
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
            redirect('news.php', 'Post Added Successfully');
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
