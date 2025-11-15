<?php
$pageName = basename($_SERVER['SCRIPT_NAME']); // Get the current script name
$pageMappings = [
    'index.php' => ['Dashboard', 'Home'],
    'documents.php' => ['Documents', 'Pages'],
    'documents-create.php' => ['Create Documents', 'Pages'],
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
            <?php
            // notification bell for focal-person pages (moved here so it appears in navbar)
            try {
                $role = $_SESSION['loggedInUserRole'] ?? $_SESSION['role'] ?? null;
                $rid = $_SESSION['loggedInUser']['id'] ?? $_SESSION['user']['id'] ?? $_SESSION['id'] ?? null;
                $navNotifications = [];
                $navNotifCount = 0;
                if ($role === 'fperson') {
                    $nq = $pdo->prepare("SELECT id, title, message, link, is_read, created_at FROM notifications WHERE recipient_role = :role AND (recipient_id IS NULL OR recipient_id = :rid) ORDER BY created_at DESC LIMIT 5");
                    $nq->execute([':role' => $role, ':rid' => $rid]);
                    $navNotifications = $nq->fetchAll(PDO::FETCH_ASSOC);
                    $cntq = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE recipient_role = :role AND (recipient_id IS NULL OR recipient_id = :rid) AND is_read = 0");
                    $cntq->execute([':role' => $role, ':rid' => $rid]);
                    $navNotifCount = (int) $cntq->fetchColumn();
                }
            } catch (Exception $e) {
                $navNotifications = [];
                $navNotifCount = 0;
            }
            ?>

            <div class="d-flex align-items-center">
                <div class="notification-wrapper me-3" style="position:relative;">
                    <button id="notifToggleFP" class="btn btn-sm btn-light" aria-expanded="false" style="position:relative;">
                        <i class="fa fa-bell"></i>
                        <?php if (!empty($navNotifCount)): ?>
                            <span class="notif-badge" style="position:absolute; top:-6px; right:-6px; background:#dc3545; color:#fff; border-radius:50%; padding:2px 6px; font-size:12px;"><?= $navNotifCount ?></span>
                        <?php endif; ?>
                    </button>
                    <div id="notifDropdownFP" class="card" style="display:none; position:absolute; right:0; width:320px; max-height:360px; overflow:auto; z-index:2000;">
                        <div class="card-body p-2">
                            <h6 class="mb-2">Notifications</h6>
                            <?php if (empty($navNotifications)): ?>
                                <div class="small text-muted">No notifications.</div>
                            <?php else: ?>
                                <?php foreach ($navNotifications as $n): ?>
                                    <a href="<?= '../notifications/redirect.php?id=' . urlencode($n['id']) ?>" class="d-block p-2 border-bottom text-dark" style="text-decoration:none;">
                                        <div class="fw-bold"><?= htmlspecialchars($n['title']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars(substr($n['message'],0,80)) ?></div>
                                        <div class="small text-muted mt-1"><?= isset($n['created_at']) ? htmlspecialchars(format_datetime($n['created_at'], 'M j, Y g:i A')) : '' ?></div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <div class="mt-2 text-end"><a href="../notifications.php">See all</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
            (function(){
                var btn = document.getElementById('notifToggleFP');
                var dd = document.getElementById('notifDropdownFP');
                var badge = btn ? btn.querySelector('.notif-badge') : null;
                var opened = false;

                async function markRead() {
                    try {
                        const res = await fetch('../notifications/mark_read.php', { method: 'POST', credentials: 'same-origin' });
                        const data = await res.json();
                        if (data && data.status === 'ok') {
                            if (badge) {
                                if (parseInt(data.unread, 10) > 0) {
                                    badge.textContent = data.unread;
                                    badge.style.display = 'inline-block';
                                } else {
                                    badge.style.display = 'none';
                                }
                            }
                            document.querySelectorAll('.case-unread').forEach(function(r){ r.classList.remove('case-unread'); });
                        }
                    } catch (e) {
                        // ignore
                    }
                }

                if (btn) btn.addEventListener('click', function(e){
                    e.preventDefault();
                    if (dd.style.display === 'none' || dd.style.display === '') {
                        dd.style.display = 'block';
                        if (!opened) {
                            opened = true;
                            markRead();
                        }
                    } else dd.style.display = 'none';
                });
                document.addEventListener('click', function(e){
                    if (!btn.contains(e.target) && !dd.contains(e.target)) dd.style.display = 'none';
                });
            })();
            </script>
        </div>
    </div>
</nav>
