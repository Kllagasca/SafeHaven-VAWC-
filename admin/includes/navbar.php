<?php
include '../config/supabase_connect.php';
$pageName = basename($_SERVER['SCRIPT_NAME']); // Get the current script name
$pageMappings = [
    'index.php' => ['Dashboard', 'Home'],
    'cases.php' => ['Cases', 'Pages'],
    'documents.php' => ['Documents', 'Pages'],
    'services.php' => ['Posts', 'Pages'],
    'carousel.php' => ['Events/Activity Images', 'Pages'],
    'news.php' => ['News', 'Pages'],
    'survey.php' => ['Survey', 'Pages'],
    'users.php' => ['Users', 'Pages'],
    'user-edit.php' => ['Edit User', 'Pages'],
    'social-media.php' => ['Social Media/Links', 'Pages'],
    'generate-focalperson.php' => ['Generate Focal Person', 'Pages'],
    'case-create.php' => ['Create Case', 'Pages'],
    'case-details.php' => ['Case Details', 'Pages'],
    'case-edit.php' => ['Edit Case', 'Pages']
    // Add more mappings as needed
];

// Determine the breadcrumb dynamically
$breadcrumb = isset($pageMappings[$pageName]) ? $pageMappings[$pageName] : ['Unknown', 'Pages/Unknown'];
?>

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 mt-4 shadow-lg border-radius-xl" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="javascript:;">
                        <?= htmlspecialchars($breadcrumb[1]); ?>
                    </a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                    <?= htmlspecialchars($breadcrumb[0]); ?>
                </li>
            </ol>
            <h6 class="font-weight-bolder mb-0"><?= htmlspecialchars($breadcrumb[0]); ?></h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                   

    <button class="btn btn-secondary dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        Notifications
    </button>
    <ul class="dropdown-menu" aria-labelledby="notificationDropdown">
    <?php
    try {
        // Assuming you have a PDO connection $pdo already established
        $query = "SELECT message, created_at FROM notifications ORDER BY created_at DESC"; // Example query to get notifications
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all notifications as an associative array

        if (!empty($notifications)) {
            foreach ($notifications as $notification) {
                // Sanitize the message to prevent XSS
                $message = htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8');
                
                // Sanitize the date and format it
                $createdAt = htmlspecialchars(date('F j, Y, g:i a', strtotime($notification['created_at'])), ENT_QUOTES, 'UTF-8');

                // Display the notification
                echo "<li class='dropdown-item'>
                        <strong>$message</strong><br>
                        <small>$createdAt</small>
                      </li>";
            }
        } else {
            // If no notifications are found
            echo "<li class='dropdown-item'>No notifications</li>";
        }
    } catch (PDOException $e) {
        // Handle any potential PDO errors
        echo "<li class='dropdown-item'>Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</li>";
    }
?>



    </ul>
        </div>
            </li>
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
            </div>
        </div>
    </div>
</nav> 