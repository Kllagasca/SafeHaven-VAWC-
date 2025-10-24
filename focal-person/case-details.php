<?php
include('includes/header.php');

// Get case ID from URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute query
    $stmt = $pdo->prepare("SELECT * FROM cases WHERE caseno = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        die("Case not found.");
    }
} else {
    die("No case ID provided.");
}

if (isset($_SESSION['title'])) {
    // Title is already set in the session
} else {
    // Set the title in the session
    $_SESSION['title'] = $item['title'];
}

if (isset($_SESSION['status'])) {
    // Status is already set in the session
} else {
    // Set the status in the session
    $_SESSION['status'] = $item['status'] == 0 ? "Open" : "Closed";
}

if (isset($_SESSION['date'])) {
    // Date is already set in the session
} else {
    // Set the date in the session
    $_SESSION['date'] = $item['date'];
}

if (isset($_SESSION['brgy'])) {
    // Barangay is already set in the session
} else {
    // Set the barangay in the session
    $_SESSION['brgy'] = $item['brgy'];
}
?>

<div class="col-md-12 mb-4">
    <div class="card card-body text-capitalize">
        <h5 class="font-weight-bold">
            <span class="d-inline-flex align-items-center text-white border rounded-pill px-3 py-2" style="gap: 10px; background-color:#554fb0;">
                <i class="fa-solid fa-file"></i>
                <h4 class="text-white m-0">Case No.: <?= htmlspecialchars($item['caseno']); ?></h4>
            </span>
        </h5>

        <div style="position: absolute; top: 20px; right: 20px; z-index: 1000;">
            <a href="../index.php" style="text-decoration: none; color: #9953ed; font-weight: bold;">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back Home
            </a>
        </div>

        <div class="border rounded p-3 mt-2 text-dark font-weight-bold">
            <div class="d-flex justify-content-between">
                <h5><span style="text-transform: none;"><strong>Case Title:</strong> <?= htmlspecialchars($_SESSION['title']); ?></span></h5>
                <h5><span><strong>Status:</strong> <?= htmlspecialchars($_SESSION['status']); ?></span></h5>
                <h5><span><strong>Barangay:</strong> <?= htmlspecialchars($_SESSION['brgy']); ?></span></h5>
                <h5>
                    <span><strong>Date of Incident:</strong> 
                    <?= date("F j, Y", strtotime($_SESSION['date'])); ?>
                    </span>
                </h5>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">

            <h5 class="font-weight-bold">
                <span class="d-inline-flex align-items-center text-white border rounded-pill px-3 py-2 mb-2" style="gap: 10px; background-color:#554fb0;">
                <i class="fa-solid fa-user"></i>
                <h5 class="text-white m-0">Complainant Details</h5>
                </span>
            </h5>
                <div class="border rounded p-3 mt-2 text-dark font-weight-bold">
                    <h5><strong>Complainant Name:</strong> <?= htmlspecialchars($item['comp_name'] ?? 'N/A'); ?></h5>
                    <h5><strong>Complainant Age:</strong> <?= htmlspecialchars($item['comp_age'] ?? 'N/A'); ?></h5>
                    <h5><strong>Complainant Number:</strong> <?= htmlspecialchars($item['comp_num'] ?? 'N/A'); ?></h5>
                    <h5><strong>Complainant Address:</strong> <?= htmlspecialchars($item['comp_address'] ?? 'N/A'); ?></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">

            <h5 class="font-weight-bold">
                <span class="d-inline-flex align-items-center text-white border rounded-pill px-3 py-2 mb-2" style="gap: 10px; background-color:#554fb0;">
                <i class="fa-solid fa-user"></i>
                <h5 class="text-white m-0">Respondent Details</h5>
                </span>
            </h5>

            <div class="border rounded p-3 mt-2 text-dark font-weight-bold">
                <h5><strong>Respondent Name:</strong> <?= htmlspecialchars($item['resp_name'] ?? 'N/A'); ?></h5>
                <h5><strong>Respondent Age:</strong> <?= htmlspecialchars($item['resp_age'] ?? 'N/A'); ?></h5>
                <h5><strong>Respondent Number:</strong> <?= htmlspecialchars($item['resp_num'] ?? 'N/A'); ?></h5>
                <h5><strong>Respondent Address:</strong> <?= htmlspecialchars($item['resp_address'] ?? 'N/A'); ?></h5>
            </div>

            </div>
        </div>
    </div>

    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">

            <h5 class="font-weight-bold">
                <span class="d-inline-flex align-items-center text-white border rounded-pill px-3 py-2 mb-2" style="gap: 10px; background-color:#554fb0;">
                <i class="fa-solid fa-pen-nib"></i>
                <h5 class="text-white m-0">Case Summary</h5>
                </span>
            </h5>

            <div class="border rounded p-3 mt-2 text-dark font-weight-bold">
                <p><?= nl2br(htmlspecialchars($item['long_description'] ?? 'N/A')); ?></p>
            </div>
            
            </div>
        </div>
    </div>

    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">

            <h5 class="font-weight-bold">
                <span class="d-inline-flex align-items-center text-white border rounded-pill px-3 py-2 mb-2" style="gap: 10px; background-color:#554fb0;">
                <i class="fa-solid fa-images"></i>
                <h5 class="text-white m-0">Evidences</h5>
                </span>
            </h5>

            <div class="border rounded p-3 mt-2 text-dark font-weight-bold">
                <p><?= nl2br(htmlspecialchars($item['image'] ?? 'N/A')); ?></p>
            </div>
            
            </div>
        </div>
    </div>
</div>

<style>
            ::-webkit-scrollbar {
            display: none;
        }
        html {
            scrollbar-width: none;
        }
</style>