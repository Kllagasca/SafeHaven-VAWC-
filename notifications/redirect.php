<?php
require_once __DIR__ . '/../config/function.php';
include __DIR__ . '/../config/supabase_connect.php'; // $pdo

// Ensure session is started via function.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    // No id, just redirect to the appropriate cases page
    $role = isset($_SESSION['loggedInUserRole']) ? $_SESSION['loggedInUserRole'] : null;
    if ($role === 'admin') header('Location: ../admin/cases.php');
    elseif ($role === 'fperson') header('Location: ../focal-person/cases.php');
    else header('Location: ../cases.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM notifications WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Only mark as read if the current user is the intended recipient (role and recipient_id when applicable)
        try {
            $role = $_SESSION['loggedInUserRole'] ?? $_SESSION['role'] ?? null;
            $uid = $_SESSION['loggedInUser']['id'] ?? $_SESSION['user']['id'] ?? null;
            $allowed = false;
            if ($role && $role === $row['recipient_role']) {
                if ($role === 'fperson') {
                    if (empty($row['recipient_id']) || intval($row['recipient_id']) === intval($uid)) $allowed = true;
                } else {
                    $allowed = true;
                }
            }

            if ($allowed) {
                try {
                    $u = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = :id');
                    $u->execute([':id' => $id]);
                } catch (Exception $e) {
                    // ignore write failures
                }
            }
        } catch (Exception $e) {
            // ignore
        }

        // If the notification has a link, redirect there and append a flag so the destination can show a toast
        $link = isset($row['link']) ? trim($row['link']) : '';
        if (!empty($link)) {
            // Determine separator for query params
            $sep = (strpos($link, '?') !== false) ? '&' : '?';

            // If the link looks relative (no scheme), prefix with ../ so we land at the right path from /notifications/
            $isAbsolute = preg_match('/^https?:\/\//i', $link);
            // Normalize param name: many case-details pages expect 'id' param but some code stored 'caseno'.
            if (!$isAbsolute) {
                // replace caseno= with id= so destination pages that expect id receive it
                if (strpos($link, 'caseno=') !== false) {
                    $link = str_replace('caseno=', 'id=', $link);
                }
            }

            if ($isAbsolute) {
                $target = $link . $sep . 'notif_marked=1';
            } else {
                // normalize leading slashes
                $trimmed = ltrim($link, '/');
                $target = '../' . $trimmed . $sep . 'notif_marked=1';
            }

            header('Location: ' . $target);
            exit;
        }

        // fallback: redirect to role's cases page
        $role = isset($_SESSION['loggedInUserRole']) ? $_SESSION['loggedInUserRole'] : null;
        if ($role === 'admin') header('Location: ../admin/cases.php');
        elseif ($role === 'fperson') header('Location: ../focal-person/cases.php');
        else header('Location: ../cases.php');
        exit;
    } else {
        // not found
        $role = isset($_SESSION['loggedInUserRole']) ? $_SESSION['loggedInUserRole'] : null;
        if ($role === 'admin') header('Location: ../admin/cases.php');
        elseif ($role === 'fperson') header('Location: ../focal-person/cases.php');
        else header('Location: ../cases.php');
        exit;
    }
} catch (Exception $e) {
    // on error, redirect to fallback
    $role = isset($_SESSION['loggedInUserRole']) ? $_SESSION['loggedInUserRole'] : null;
    if ($role === 'admin') header('Location: ../admin/cases.php');
    elseif ($role === 'fperson') header('Location: ../focal-person/cases.php');
    else header('Location: ../cases.php');
    exit;
}

?>