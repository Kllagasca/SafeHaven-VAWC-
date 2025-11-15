<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">
                        <h4>Cases</h4>
                    </div>
                    <div class="col-md-7">
                        <!-- Add Case Button -->
                        <a href="case-create.php" class="btn btn-primary float-end">Add New Case</a>

                        <form action="" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="date" name="date" required value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <select name="status" required class="form-select">
                                        <option value="">Select Status</option>
                                        <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : '' ?>>Open</option>
                                        <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Closed</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="cases.php" class="btn btn-danger">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= alertmessage(); ?>

                <table id="myTable" class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Case Number </th>
                            <th>Case Title</th>
                            <th>Incident Location</th>
                            <th>Date of Incident</th>
                            <th>Complainant Name</th>
                            <th>Case Details</th>
                            <th>Case Status</th>
                            <th>Case Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // ✅ Build status filter if set
                            $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' 
                                ? "WHERE status = :status" 
                                : "";

                            // ✅ Also filter by date if provided
                            $dateFilter = isset($_GET['date']) && $_GET['date'] !== '' 
                                ? ( $statusFilter ? " AND date = :date" : "WHERE date = :date" ) 
                                : "";

                            // ✅ Final query
                            $query = "SELECT * FROM cases $statusFilter $dateFilter ORDER BY date DESC";
                            $stmt = $pdo->prepare($query);

                            // ✅ Bind parameters if filters are used
                            if ($statusFilter) {
                                $stmt->bindValue(':status', intval($_GET['status']), PDO::PARAM_INT);
                            }
                            if ($dateFilter) {
                                $stmt->bindValue(':date', $_GET['date']);
                            }

                            $stmt->execute();
                            $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Build a lookup of caseno values that have unread notifications for admin
                            // We'll also store the notification id for each caseno so we can link directly
                            $unreadCases = [];
                            $unreadCasesIds = [];
                            try {
                                // Simple file-cache to reduce DB load for notifications (TTL 30s)
                                $cacheDir = __DIR__ . '/../assets/cache';
                                if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);
                                $cacheFile = $cacheDir . '/notifications_admin.json';
                                $useCache = false;
                                $ttl = 30; // seconds
                                if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $ttl)) {
                                    $rawNotifs = json_decode(file_get_contents($cacheFile), true);
                                    if (!is_array($rawNotifs)) $rawNotifs = [];
                                    $useCache = true;
                                }
                                if (!$useCache) {
                                    $nq = $pdo->prepare("SELECT id, link FROM notifications WHERE recipient_role = :role AND is_read = 0");
                                    $nq->execute([':role' => 'admin']);
                                    $rawNotifs = $nq->fetchAll(PDO::FETCH_ASSOC);
                                    // write cache
                                    file_put_contents($cacheFile, json_encode($rawNotifs));
                                }

                                foreach ($rawNotifs as $rn) {
                                    $nid = isset($rn['id']) ? $rn['id'] : null;
                                    $link = isset($rn['link']) ? $rn['link'] : '';
                                    if (!$link) continue;
                                    // try to extract caseno or id from the stored link
                                    $parts = parse_url($link);
                                    if (isset($parts['query'])) {
                                        parse_str($parts['query'], $qs);
                                        if (!empty($qs['caseno'])) {
                                            $k = (string)$qs['caseno'];
                                            $unreadCases[$k] = true;
                                            if ($nid) $unreadCasesIds[$k] = $nid;
                                        } elseif (!empty($qs['id'])) {
                                            $k = (string)$qs['id'];
                                            $unreadCases[$k] = true;
                                            if ($nid) $unreadCasesIds[$k] = $nid;
                                        }
                                    } else {
                                        if (preg_match('/caseno=([^&]+)/', $link, $m)) {
                                            $k = (string)$m[1];
                                            $unreadCases[$k] = true;
                                            if ($nid) $unreadCasesIds[$k] = $nid;
                                        } elseif (preg_match('/id=([^&]+)/', $link, $m2)) {
                                            $k = (string)$m2[1];
                                            $unreadCases[$k] = true;
                                            if ($nid) $unreadCasesIds[$k] = $nid;
                                        }
                                    }
                                }
                            } catch (Exception $e) {
                                // ignore notification lookup failures
                                $unreadCases = [];
                                $unreadCasesIds = [];
                            }

                        foreach ($cases as $item) {
                        ?>
                        <tr class="<?= (isset($unreadCases[(string)($item['id'] ?? '')]) || isset($unreadCases[(string)$item['caseno']])) ? 'case-unread' : '' ?>">
                            <td class="doc-title"><?= htmlspecialchars($item['caseno']); ?></td>
                            <td class="doc-title">
                                <?php
                                    $itemKey = isset($item['id']) ? (string)$item['id'] : (string)$item['caseno'];
                                    if (isset($unreadCasesIds[$itemKey])): ?>
                                    <a href="<?= '../notifications/redirect.php?id=' . urlencode($unreadCasesIds[$itemKey]) ?>" class="text-dark fw-bold case-unread-link"><?= htmlspecialchars($item['title']); ?></a>
                                <?php else: ?>
                                    <?= htmlspecialchars($item['title']); ?>
                                <?php endif; ?>
                            </td>
                            <td class="doc-title"><?= htmlspecialchars($item['brgy']); ?></td>
                            <td class="doc-title"><?= htmlspecialchars($item['date']); ?></td>
                            <td class="doc-title"><?= htmlspecialchars($item['comp_name']); ?></td>
                            <td>
                                <a href="case-details.php?id=<?= urlencode($item['id'] ?? $item['caseno']); ?>" class="btn btn-primary btn-sm" <?= isset($unreadCasesIds[(string)($item['id'] ?? $item['caseno'])]) ? 'data-notif-id="' . htmlspecialchars($unreadCasesIds[(string)($item['id'] ?? $item['caseno'])]) . '"' : '' ?>>
                                    View Details
                                </a>
                            </td>


                            <td><?= $item['status'] == 0 ? "Open" : "Closed"; ?></td>
                            <td>
                                <a href="case-edit.php?id=<?= urlencode($item['id'] ?? $item['caseno']); ?>" class="btn btn-primary btn-sm">Edit</a>

                                <a href="case-delete.php?id=<?= $item['id'] ?? $item['caseno']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this case?')">Delete</a>
                                <!-- Unread badge removed; row will be bolded when unread -->
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    #myTable th, #myTable td {
        white-space: nowrap;
    }
    #myTable .doc-title {
        max-width: 100px;
        white-space: normal;
        word-wrap: break-word;
    }
    /* Make entire row bold from case number through case status (all cells except action) when unread */
    .case-unread td:not(:last-child) {
        font-weight: 700 !important;
    }
    .case-unread-link {
        text-decoration: none;
    }
    /* highlight for redirected notifications */
    .notify-highlight {
        animation: notifyFlash 3s ease-in-out;
        background: rgba(255, 255, 0, 0.4) !important;
    }
    @keyframes notifyFlash {
        0% { background: rgba(255,255,0,0.9); }
        50% { background: rgba(255,255,0,0.4); }
        100% { background: transparent; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    try {
        const params = new URLSearchParams(window.location.search);
        const highlight = params.get('highlight');
        if (highlight) {
            const rows = document.querySelectorAll('#myTable tbody tr');
            for (const r of rows) {
                const casenoCell = r.querySelector('td.doc-title');
                if (casenoCell && casenoCell.textContent.trim() === highlight) {
                    r.classList.add('notify-highlight');
                    r.scrollIntoView({behavior:'smooth', block:'center'});
                    // remove highlight after 4s
                    setTimeout(() => r.classList.remove('notify-highlight'), 4000);
                    break;
                }
            }
        }
        // Immediate un-bold: when admin clicks a bolded case title (notification link), remove the bold class from the row before navigation
        try {
            document.querySelectorAll('.case-unread-link').forEach(function(el){
                el.addEventListener('click', function(){
                    var tr = el.closest('tr');
                    if (tr && tr.classList.contains('case-unread')) {
                        tr.classList.remove('case-unread');
                    }
                });
            });
        } catch (e) {
            // ignore
        }
        // When clicking View Details (or any element with data-notif-id), immediately un-bold and mark that notification as read via AJAX
        try {
            document.querySelectorAll('[data-notif-id]').forEach(function(el){
                el.addEventListener('click', function(e){
                    var id = el.getAttribute('data-notif-id');
                    var tr = el.closest('tr');
                    if (tr && tr.classList.contains('case-unread')) tr.classList.remove('case-unread');
                    if (id) {
                        // fire-and-forget POST to mark single notification
                        fetch('../notifications/mark_single.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id=' + encodeURIComponent(id),
                            credentials: 'same-origin'
                        }).catch(function(){});
                    }
                    // allow navigation to continue
                });
            });
        } catch (e) {}

        // When clicking any sidebar link, mark all unread as read (so returning to cases will show nothing bold)
        try {
            var sidebar = document.getElementById('sidebar');
            var sidebarLinks = [];
            if (sidebar) sidebarLinks = sidebar.querySelectorAll('a');
            else sidebarLinks = document.querySelectorAll('.sidebar a, .nav-link');
            sidebarLinks.forEach(function(a){
                a.addEventListener('click', function(){
                    // mark all unread
                    fetch('../notifications/mark_read.php', { method: 'POST', credentials: 'same-origin' }).catch(function(){});
                    // optimistically remove bolding
                    document.querySelectorAll('.case-unread').forEach(function(r){ r.classList.remove('case-unread'); });
                });
            });
        } catch (e) {}
    } catch (e) {
        // ignore
    }
});
</script>

<?php include('includes/footer.php'); ?>
