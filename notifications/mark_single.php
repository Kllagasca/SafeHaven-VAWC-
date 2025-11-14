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
$id = isset($data['id']) ? intval($data['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid id']);
    exit;
}

try {
    // Ensure the current user is allowed to mark this notification (role/recipient_id)
    $role = $_SESSION['loggedInUserRole'] ?? $_SESSION['role'] ?? null;
    $uid = $_SESSION['loggedInUser']['id'] ?? $_SESSION['user']['id'] ?? null;

    $q = $pdo->prepare('SELECT recipient_role, recipient_id FROM notifications WHERE id = :id LIMIT 1');
    $q->execute([':id' => $id]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Not found']);
        exit;
    }

    $allowed = false;
    if ($role && $role === $row['recipient_role']) {
        if ($role === 'fperson') {
            // must match recipient_id
            if ($uid && $row['recipient_id'] && intval($row['recipient_id']) === intval($uid)) $allowed = true;
        } else {
            // other roles (e.g., admin) allowed if role matches
            $allowed = true;
        }
    }

    if (!$allowed) {
        echo json_encode(['status' => 'error', 'message' => 'Not allowed']);
        exit;
    }

    $u = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = :id');
    $u->execute([':id' => $id]);

    // return updated unread count for the actor (role/recipient)
    if ($role === 'fperson' && $uid) {
        $cnt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE recipient_role = :role AND (recipient_id IS NULL OR recipient_id = :rid) AND is_read = 0');
        $cnt->execute([':role' => $role, ':rid' => $uid]);
        $unread = (int) $cnt->fetchColumn();
    } else {
        $cnt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE recipient_role = :role AND is_read = 0');
        $cnt->execute([':role' => $role]);
        $unread = (int) $cnt->fetchColumn();
    }

    // clear caches so UI sees updated state immediately
    try { @unlink(__DIR__ . '/../assets/cache/notifications_admin.json'); } catch (Exception $__) {}
    try { @unlink(__DIR__ . '/../assets/cache/notifications_fperson.json'); } catch (Exception $__) {}

    echo json_encode(['status' => 'ok', 'unread' => $unread]);
    exit;
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB error']);
    exit;
}

?>
