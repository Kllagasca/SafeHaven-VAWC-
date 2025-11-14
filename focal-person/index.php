<?php include('includes/header.php'); ?>

<h5?php
date_default_timezone_set("Asia/HongKong");
?>

<?= alertmessage(); ?>

<div class="col-md-12 mb-4">
    <div class="card card-body text-capitalize">
        <h5 class="font-weight-bold">
            <span class="d-inline-flex align-items-center text-white border rounded-pill px-3 py-2" style="gap: 10px; background-color:#554fb0;">
                <i class="fa-solid fa-user"></i>
                Hello, <?php echo htmlspecialchars($_SESSION['fname']); ?>!
            </span>
        </h5>

        <div style="position: absolute; top: 20px; right: 20px; z-index: 1000; display:flex; gap:12px; align-items:center;">
            <?php
            // Fetch focal-person notifications from Supabase (PDO) only
            $notifCountFP = 0;
            $notificationsFP = [];
            try {
                $role = 'fperson';
                $rid = isset($_SESSION['loggedInUser']['id']) ? $_SESSION['loggedInUser']['id'] : null;
                $nq = $pdo->prepare("SELECT id, title, message, link, is_read, created_at FROM notifications WHERE recipient_role = :role AND (recipient_id IS NULL OR recipient_id = :rid) ORDER BY created_at DESC LIMIT 5");
                $nq->execute([':role' => $role, ':rid' => $rid]);
                $notificationsFP = $nq->fetchAll(PDO::FETCH_ASSOC);

                $cntq = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE recipient_role = :role AND (recipient_id IS NULL OR recipient_id = :rid) AND is_read = 0");
                $cntq->execute([':role' => $role, ':rid' => $rid]);
                $notifCountFP = (int) $cntq->fetchColumn();
            } catch (Exception $e) {
                $notificationsFP = [];
                $notifCountFP = 0;
            }
            ?>

            <div class="notification-wrapper" style="position:relative;">
                <button id="notifToggleFP" class="btn btn-sm btn-light" aria-expanded="false" style="position:relative;">
                    <i class="fa fa-bell"></i>
                    <?php if ($notifCountFP > 0): ?>
                        <span class="notif-badge" style="position:absolute; top:-6px; right:-6px; background:#dc3545; color:#fff; border-radius:50%; padding:2px 6px; font-size:12px;"><?= $notifCountFP ?></span>
                    <?php endif; ?>
                </button>
                <div id="notifDropdownFP" class="card" style="display:none; position:absolute; right:0; width:320px; max-height:360px; overflow:auto; z-index:2000;">
                    <div class="card-body p-2">
                        <h6 class="mb-2">Notifications</h6>
                        <?php if (empty($notificationsFP)): ?>
                            <div class="small text-muted">No notifications.</div>
                        <?php else: ?>
                            <?php foreach ($notificationsFP as $n): ?>
                                <a href="<?= '../notifications/redirect.php?id=' . urlencode($n['id']) ?>" class="d-block p-2 border-bottom text-dark" style="text-decoration:none;">
                                    <div class="fw-bold"><?= htmlspecialchars($n['title']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars(substr($n['message'],0,80)) ?></div>
                                    <div class="small text-muted mt-1"><?= isset($n['created_at']) ? date('M j, Y g:i A', strtotime($n['created_at'])) : '' ?></div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="mt-2 text-end"><a href="../notifications.php">See all</a></div>
                    </div>
                </div>
            </div>

            <a href="../index.php" style="text-decoration: none; color: #9953ed; font-weight: bold;">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back Home
            </a>
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

        <div class="border rounded p-3 mt-2 text-dark font-weight-bold">
            <div class="d-flex justify-content-between">
                <span style="text-transform: none;">Email: <?php echo htmlspecialchars($_SESSION['email']); ?></span>
                <span>Account Type: <?php echo htmlspecialchars($_SESSION['role']); ?></span>
                <span>Date: <?php echo date("F j, Y"); ?></span>
            </div>
        </div>
    </div>
</div>



<div class="row">

    <!-- Total Cases -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3"  style="background-color: #554fb0;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge bg-white rounded-pill px-2 py-1" style="color: #554fb0;">Total Cases</h6>
                    <h3 class="font-weight-bold mb-0 text-white">
                        <?= getCount('cases') ?>
                    </h3>
                </div>
                <i class="fa fa-home text-white" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

        <!-- Total Closed Cases -->
        <div class="col-md-3 mb-4">
        <div class="card card-body p-3 bg-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize bg-white text-success font-weight-bold badge rounded-pill px-2 py-1">Total Closed Cases</h6>
                    <h3 class="font-weight-bold text-white mb-0">
                    <?php
                        try {
                            $query = "SELECT COUNT(*) AS total_count FROM cases WHERE status = '1'";
                            $stmt = $pdo->query($query); // Execute the query directly
                            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
                            echo $row['total_count']; // Output the count
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage(); // Handle any exceptions
                        }
                    ?>

                    </h3>
                </div>
                <i class="fa fa-check-circle text-white" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

        <!-- Total Pending Cases -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3" style="background-color: #554fb0;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge bg-white rounded-pill px-2 py-1" style="color:#554fb0;">Total Pending Cases</h6>
                    <h3 class="font-weight-bold mb-0 text-white">
                    <?php
                        try {
                            $query = "SELECT COUNT(*) AS total_count FROM cases WHERE status = 'pending'";
                            $stmt = $pdo->query($query); // Execute the query directly
                            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
                            echo $row['total_count']; // Output the count
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage(); // Handle any exceptions
                        }
                    ?>

                    </h3>
                </div>
                <i class="fa fa-clock text-white" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

            <!-- Total Open Cases -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3 bg-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize bg-white text-danger font-weight-bold badge rounded-pill px-2 py-1">Total Open Cases</h6>
                    <h3 class="font-weight-bold text-white mb-0">
                    <?php
                        try {
                            $query = "SELECT COUNT(*) AS total_count FROM cases WHERE status = '0'";
                            $stmt = $pdo->query($query); // Execute the query directly
                            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
                            echo $row['total_count']; // Output the count
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage(); // Handle any exceptions
                        }
                    ?>

                    </h3>
                </div>
                <i class="fa fa-times-circle text-white" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

    <!-- Total Documents -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge rounded-pill px-2 py-1" style="background-color: #554fb0">Total Documents</h6>
                    <h3 class="font-weight-bold mb-0">
                        <?= getCount('documents') ?>
                    </h3>
                </div>
                <i class="fa fa-file-alt" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;color: #554fb0;"></i>
            </div>
        </div>
    </div>

    <!-- Total Approved Documents -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge bg-success text-white rounded-pill px-2 py-1">Total Approved Documents</h6>
                    <h3 class="font-weight-bold mb-0">
                    <?php
                        try {
                            // Prepare the PDO query
                            $query = "SELECT COUNT(*) AS total_count FROM documents WHERE approval_status = 'approved'";
                            $stmt = $pdo->prepare($query);
                            
                            // Execute the query
                            $stmt->execute();
                            
                            // Fetch the result
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            // Output the total count
                            echo $row['total_count'];
                        } catch (PDOException $e) {
                            // Handle any errors
                            echo "Error: " . $e->getMessage();
                        }
                    ?>

                    </h3>
                </div>
                <i class="fa fa-check-circle text-success" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

    <!-- Total Pending Documents -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge text-white rounded-pill px-2 py-1" style="background-color: #554fb0">Total Pending Documents</h6>
                    <h3 class="font-weight-bold mb-0">
                    <?php
                        try {
                            // Prepare the PDO query
                            $query = "SELECT COUNT(*) AS total_count FROM documents WHERE approval_status = 'pending'";
                            $stmt = $pdo->prepare($query);
                            
                            // Execute the query
                            $stmt->execute();
                            
                            // Fetch the result
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            // Output the total count
                            echo $row['total_count'];
                        } catch (PDOException $e) {
                            // Handle any errors
                            echo "Error: " . $e->getMessage();
                        }
                    ?>

                    </h3>
                </div>
                <i class="fa fa-clock" style="font-size: 60px; flex-shrink: 0; padding-right: 20px; color: #554fb0;"></i>
            </div>
        </div>
    </div>

    <!-- Total Rejected Documents -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold bg-danger badge text-white rounded-pill px-2 py-1">Total Rejected Documents</h6>
                    <h3 class="font-weight-bold mb-0">
                    <?php
                        try {
                            // Prepare the PDO query
                            $query = "SELECT COUNT(*) AS total_count FROM documents WHERE approval_status = 'rejected'";
                            $stmt = $pdo->prepare($query);
                            
                            // Execute the query
                            $stmt->execute();
                            
                            // Fetch the result
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            // Output the total count
                            echo $row['total_count'];
                        } catch (PDOException $e) {
                            // Handle any errors
                            echo "Error: " . $e->getMessage();
                        }
                    ?>

                    </h3>
                </div>
                <i class="fa fa-times-circle text-danger" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

<?php include('includes/footer.php'); ?>
