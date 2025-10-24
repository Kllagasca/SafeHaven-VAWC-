<?php
$pageName = basename($_SERVER['SCRIPT_NAME']); // Get the current script name
$pageMappings = [
    'index.php' => ['Dashboard', 'Home'],
    'documents.php' => ['Documents', 'Pages'],
    'services.php' => ['Posts', 'Pages'],
    'carousel.php' => ['Events/Activity Images', 'Pages'],
    'news.php' => ['News', 'Pages'],
    'cases.php' => ['Cases', 'Pages'],
    'case-create.php' => ['Create Case', 'Pages'],
    'case-details.php' => ['Case Details', 'Pages'],
    'case-edit.php' => ['Edit Case', 'Pages'],
    'users.php' => ['Users', 'Pages'],
    'social-media.php' => ['Social Media/Links', 'Pages']
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
                    <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell cursor-pointer"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                        <!-- Notification items go here -->
                    </ul>
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
