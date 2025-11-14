<?php
require_once __DIR__ . '/../config/function.php';
include __DIR__ . '/../config/supabase_connect.php'; // $pdo

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

$input = file_get_contents('php://input');
$data = [];
parse_str($input, $data);
$caseno = isset($data['caseno']) ? trim($data['caseno']) : (isset($_POST['caseno']) ? trim($_POST['caseno']) : '');

if ($caseno === '') {
    echo json_encode(['status' => 'error', 'message' => 'No caseno provided']);
    exit;
}

// get current user role/id
$role = $_SESSION['loggedInUserRole'] ?? $_SESSION['role'] ?? null;
$uid = $_SESSION['loggedInUser']['id'] ?? $_SESSION['user']['id'] ?? null;

if ($role !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit;
}

try {
    // fetch case and created_by
    $q = $pdo->prepare('SELECT caseno, created_by FROM cases WHERE caseno = :caseno LIMIT 1');
    $q->execute([':caseno' => $caseno]);
    $case = $q->fetch(PDO::FETCH_ASSOC);
    if (!$case) {
        echo json_encode(['status' => 'error', 'message' => 'Case not found']);
        exit;
    }

    $creatorId = $case['created_by'] ?? null;
    if (!$creatorId) {
        echo json_encode(['status' => 'error', 'message' => 'Case creator not found']);
        exit;
    }

    // Build notification
    $adminName = isset($_SESSION['loggedInUser']['fname']) ? trim($_SESSION['loggedInUser']['fname'] . ' ' . ($_SESSION['loggedInUser']['lname'] ?? '')) : 'Admin';
    $title = 'Admin read your case';
    $message = "{$adminName} has marked case {$caseno} as read and may be taking action.";
    $link = 'focal-person/case-details.php?id=' . urlencode($caseno);

    // Avoid exact duplicate for same case+recipient
    $chk = $pdo->prepare('SELECT id FROM notifications WHERE recipient_role = :role AND recipient_id = :rid AND link = :link LIMIT 1');
    $chk->execute([':role' => 'fperson', ':rid' => $creatorId, ':link' => $link]);
    $found = $chk->fetch(PDO::FETCH_ASSOC);
    if ($found) {
        // already exists; still clear cache and return ok
        try { @unlink(__DIR__ . '/../assets/cache/notifications_fperson.json'); } catch (Exception $__) {}
        echo json_encode(['status' => 'ok', 'message' => 'Already notified', 'id' => $found['id']]);
        exit;
    }

    $ins = $pdo->prepare('INSERT INTO notifications (recipient_role, recipient_id, title, message, link, is_read, created_at) VALUES (:role, :rid, :title, :msg, :link, 0, NOW())');
    $ins->execute([
        ':role' => 'fperson',
        ':rid' => $creatorId,
        ':title' => $title,
        ':msg' => $message,
        ':link' => $link
    ]);

    // clear focal person cache so they see it immediately
    try { @unlink(__DIR__ . '/../assets/cache/notifications_fperson.json'); } catch (Exception $__) {}

    // debug log
    try {
        $logPath = __DIR__ . '/../assets/logs/notification_debug.log';
        if (!is_dir(dirname($logPath))) mkdir(dirname($logPath), 0755, true);
        $insertId = null;
        try { $insertId = $pdo->lastInsertId(); } catch (Exception $__){ $insertId = null; }
        $msg = date('c') . " | send_to_creator inserted" . ($insertId ? " ID={$insertId}" : "") . " | case={$caseno} | to={$creatorId}\n";
        file_put_contents($logPath, $msg, FILE_APPEND | LOCK_EX);
    } catch (Exception $__) {}

    echo json_encode(['status' => 'ok', 'message' => 'Notification sent']);
    exit;
} catch (Exception $e) {
    try {
        $logPath = __DIR__ . '/../assets/logs/notification_debug.log';
        if (!is_dir(dirname($logPath))) mkdir(dirname($logPath), 0755, true);
        $msg = date('c') . " | send_to_creator ERROR: " . $e->getMessage() . " | case={$caseno}\n";
        file_put_contents($logPath, $msg, FILE_APPEND | LOCK_EX);
    } catch (Exception $__) {}
    echo json_encode(['status' => 'error', 'message' => 'DB error']);
    exit;
}

?>
