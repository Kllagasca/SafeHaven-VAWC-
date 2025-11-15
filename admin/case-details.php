<?php
include('includes/header.php');

// Get case identifier from URL. Prefer numeric local id, fallback to caseno string.
if (isset($_GET['id'])) {
    $raw = $_GET['id'];
    if (ctype_digit($raw)) {
        $id = (int)$raw;
        $stmt = $pdo->prepare("SELECT * FROM cases WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $caseno = $raw;
        $stmt = $pdo->prepare("SELECT * FROM cases WHERE caseno = :caseno LIMIT 1");
        $stmt->execute([':caseno' => $caseno]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$item) {
        die("Case not found.");
    }
} else {
    die("No case ID provided.");
}

// --- Notify the focal-person who created this case that an admin has viewed/taken action ---
try {
    $viewerRole = isset($_SESSION['loggedInUserRole']) ? $_SESSION['loggedInUserRole'] : null;
    $viewerId = isset($_SESSION['loggedInUser']['id']) ? $_SESSION['loggedInUser']['id'] : null;
    $caseCreatorId = isset($item['created_by']) ? $item['created_by'] : null;
    if ($viewerRole === 'admin' && $caseCreatorId && $caseCreatorId != $viewerId) {
    // build link for focal-person to view their case (use local id when available)
    $notificationLink = 'focal-person/case-details.php?id=' . urlencode($item['id'] ?? $item['caseno']);
        $notificationTitle = 'Admin viewed your case';
    $adminName = isset($_SESSION['loggedInUser']['fname']) ? trim($_SESSION['loggedInUser']['fname'] . ' ' . ($_SESSION['loggedInUser']['lname'] ?? '')) : 'Admin';
    $notificationMessage = "{$adminName} viewed case {$item['caseno']} and may be taking action.";

        // avoid creating duplicate identical notifications for the same case -> check recent existence
        $chk = $pdo->prepare('SELECT id, is_read FROM notifications WHERE recipient_role = :role AND recipient_id = :rid AND link = :link ORDER BY created_at DESC LIMIT 1');
        $chk->execute([':role' => 'fperson', ':rid' => $caseCreatorId, ':link' => $notificationLink]);
        $found = $chk->fetch(PDO::FETCH_ASSOC);
        if (!$found) {
            try {
                $ins = $pdo->prepare('INSERT INTO notifications (recipient_role, recipient_id, title, message, link, is_read, created_at) VALUES (:role, :rid, :title, :msg, :link, 0, NOW())');
                $ins->execute([
                    ':role' => 'fperson',
                    ':rid' => $caseCreatorId,
                    ':title' => $notificationTitle,
                    ':msg' => $notificationMessage,
                    ':link' => $notificationLink
                ]);
                // debug log
                try {
                    $logPath = __DIR__ . '/../assets/logs/notification_debug.log';
                    if (!is_dir(dirname($logPath))) mkdir(dirname($logPath), 0755, true);
                    $insertId = null;
                    try { $insertId = $pdo->lastInsertId(); } catch (Exception $__){ $insertId = null; }
                    $msg = date('c') . " | Admin-view notification inserted" . ($insertId ? " ID={$insertId}" : "") . " | case={$item['caseno']} | to={$caseCreatorId}\n";
                    file_put_contents($logPath, $msg, FILE_APPEND | LOCK_EX);
                        // clear focal-person cache so the recipient sees the notification immediately
                        try {
                            $cacheFile = __DIR__ . '/../assets/cache/notifications_fperson.json';
                            if (file_exists($cacheFile)) @unlink($cacheFile);
                        } catch (Exception $__) {}
                } catch (Exception $__) {}
            } catch (Exception $e) {
                try {
                    $logPath = __DIR__ . '/../assets/logs/notification_debug.log';
                    if (!is_dir(dirname($logPath))) mkdir(dirname($logPath), 0755, true);
                    $msg = date('c') . " | Admin-view notification ERROR: " . $e->getMessage() . " | case={$item['caseno']} | to={$caseCreatorId}\n";
                    file_put_contents($logPath, $msg, FILE_APPEND | LOCK_EX);
                } catch (Exception $__) {}
            }
        }
    }
} catch (Exception $e) {
    // swallow errors; notification is best-effort
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
        <?php if (isset($_GET['notif_marked']) && $_GET['notif_marked'] == '1'): ?>
            <div id="notifToast" class="alert alert-success" role="alert" style="position:relative; z-index:2500;">
                Notification marked as read.
            </div>
            <script>
                setTimeout(function(){
                    var t = document.getElementById('notifToast'); if (t) t.style.display = 'none';
                }, 3500);
            </script>
        <?php endif; ?>
        <h5 class="font-weight-bold">
            <span class="d-inline-flex align-items-center text-white border rounded-pill px-3 py-2" style="gap: 10px; background-color:#554fb0;">
                <i class="fa-solid fa-file"></i>
                <h4 class="text-white m-0">Case No.: <?= htmlspecialchars($item['caseno']); ?></h4>
            </span>
        </h5>

        <div style="position: absolute; top: 20px; right: 20px; z-index: 1000;">
            <a href="cases.php" style="text-decoration: none; color: #9953ed; font-weight: bold;">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back to Cases
            </a>
            <?php if (isset($_SESSION['loggedInUserRole']) && $_SESSION['loggedInUserRole'] === 'admin'): ?>
                <button id="btnNotifyCreator" class="btn btn-sm btn-success" style="margin-left:12px;">Mark as Read</button>
            <?php endif; ?>
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
                <?php
                // Show uploaded image if available.
                if (!empty($item['image'])) {
                    $raw = trim($item['image']);

                    // If the DB value is a full URL (e.g., Supabase public URL), use it directly
                    if (preg_match('#^https?://#i', $raw)) {
                        $imgUrl = htmlspecialchars($raw);
                        echo "<a href=\"{$imgUrl}\" target=\"_blank\" rel=\"noopener noreferrer\">";
                        echo "<img src=\"{$imgUrl}\" alt=\"Case evidence\" class=\"img-fluid\" style=\"max-height:480px; display:block; margin:auto;\" />";
                        echo "</a>";
                    } else {
                        // Normalize DB value to a web-relative path under assets/uploads
                        if (preg_match('#assets[\\/]+uploads[\\/]+(.+)#i', $raw, $m)) {
                            // m[1] is the path after assets/uploads/ (e.g., cases/filename.jpg)
                            $webRelative = 'assets/uploads/' . str_replace('\\', '/', $m[1]);
                        } else {
                            // Assume the DB stored a bare filename
                            $webRelative = 'assets/uploads/cases/' . ltrim(str_replace('\\', '/', $raw), '/');
                        }

                        // Front-end URL (relative from admin folder)
                        $imgUrl = htmlspecialchars('../' . $webRelative);

                        // Compute filesystem path based on project root
                        $projectRoot = realpath(__DIR__ . '/..');
                        if ($projectRoot === false) {
                            $projectRoot = __DIR__ . '/..';
                        }
                        $fileOnDisk = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $webRelative);

                        if (file_exists($fileOnDisk)) {
                            echo "<a href=\"{$imgUrl}\" target=\"_blank\" rel=\"noopener noreferrer\">";
                            echo "<img src=\"{$imgUrl}\" alt=\"Case evidence\" class=\"img-fluid\" style=\"max-height:480px; display:block; margin:auto;\" />";
                            echo "</a>";
                        } else {
                            // File missing on disk: show clickable URL and a small hint for debugging
                            echo "<p><a href=\"{$imgUrl}\" target=\"_blank\" rel=\"noopener noreferrer\">" . $imgUrl . "</a></p>";
                            // Helpful debug note (visible only to admins)
                            if (isset($_SESSION['loggedInUserRole']) && $_SESSION['loggedInUserRole'] === 'admin') {
                                $safeDisk = htmlspecialchars($fileOnDisk);
                                echo "<small class=\"text-muted\">(Checked server path: {$safeDisk})</small>";
                            }
                        }
                    }
                } else {
                    echo '<p>N/A</p>';
                }
                ?>
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

<button id="btnReadCaseFixed" 
        class="btn btn-success" 
        style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    Mark as Read
</button>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var caseno = '<?= htmlspecialchars($item['caseno']); ?>';
    var caseId = '<?= htmlspecialchars($item['id'] ?? $item['caseno']); ?>';
    var btn = document.getElementById('btnReadCaseFixed');
    if (!btn) return;

    // Check if this case was already marked as read in localStorage
    if (localStorage.getItem('caseRead_' + caseno)) {
        btn.style.display = 'none';
        return;
    }

    btn.addEventListener('click', function(e){
        e.preventDefault();
        btn.disabled = true;
        btn.textContent = 'Notifying...';

        fetch('../notifications/send_to_creator.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(caseId),
            credentials: 'same-origin' // important to send session cookie
        }).then(function(res){ return res.json(); })
        .then(function(data){
            if (data && data.status === 'ok') {
                // Mark this case as read in localStorage
                localStorage.setItem('caseRead_' + caseno, '1');

                // Optionally, clear admin unread badge
                fetch('../notifications/mark_read.php', { method: 'POST', credentials: 'same-origin' }).catch(function(){});
                
                btn.textContent = "Notified";
                setTimeout(function(){ btn.style.display = 'none'; }, 500);
            } else {
                btn.disabled = false;
                btn.textContent = "I've read this case";
                alert((data && data.message) ? data.message : 'Error sending notification');
            }
        }).catch(function(){
            btn.disabled = false;
            btn.textContent = "I've read this case";
            alert('Network error');
        });
    });
});
</script>