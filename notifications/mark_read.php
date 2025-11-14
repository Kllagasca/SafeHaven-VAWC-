<?php
require_once __DIR__ . '/../config/function.php';
include __DIR__ . '/../config/supabase_connect.php'; // $pdo

header('Content-Type: application/json');

$role = $_SESSION['loggedInUserRole'] ?? $_SESSION['role'] ?? null;
$uid = $_SESSION['loggedInUser']['id'] ?? $_SESSION['user']['id'] ?? null;
if (!$role) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

try {
    if ($role === 'fperson' && $uid) {
        // mark only notifications intended for this focal-person
        $u = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE recipient_role = :role AND recipient_id = :rid AND is_read = 0');
        $u->execute([':role' => $role, ':rid' => $uid]);

        $cnt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE recipient_role = :role AND recipient_id = :rid AND is_read = 0');
        $cnt->execute([':role' => $role, ':rid' => $uid]);
        $unread = (int) $cnt->fetchColumn();
    } else {
        // admin or other roles: mark all for the role
        $u = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE recipient_role = :role AND is_read = 0');
        $u->execute([':role' => $role]);

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
    // return error but don't leak sensitive info
    echo json_encode(['status' => 'error', 'message' => 'DB error']);
    exit;
}

?>
