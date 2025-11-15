<?php include('includes/header.php'); ?>

<h5?php
date_default_timezone_set("Asia/HongKong");
?>

<?= alertmessage(); ?>

<?php
$sessionBrgy = null;
if (isset($_SESSION['loggedInUser']['barangay'])) {
    $sessionBrgy = $_SESSION['loggedInUser']['barangay'];
} elseif (isset($_SESSION['barangay'])) {
    $sessionBrgy = $_SESSION['barangay'];
} elseif (isset($_SESSION['user']['barangay'])) {
    $sessionBrgy = $_SESSION['user']['barangay'];
}
?>

<div class="col-md-12 mb-4">
    <div class="card card-body text-capitalize">
        <h5 class="font-weight-bold">
            <span class="d-inline-flex align-items-center text-white border rounded-pill px-3 py-2" style="gap: 10px; background-color:#554fb0;">
                <i class="fa-solid fa-user"></i>
                Hello, <?php echo htmlspecialchars($_SESSION['fname']); ?>!
            </span>
        </h5>

        <div style="position: absolute; top: 20px; right: 20px; z-index: 1000; display:flex; gap:12px; align-items:center;">
            <a href="../index.php" style="text-decoration: none; color: #9953ed; font-weight: bold;">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back Home
            </a>
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
                        <?php
                        try {
                            // prefer counts of cases created by this focal-person in their barangay
                            $currentUserId = isset($_SESSION['loggedInUser']['id']) ? $_SESSION['loggedInUser']['id'] : (isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null);
                            if (!empty($sessionBrgy) && !empty($currentUserId)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE brgy = :brgy AND created_by = :created_by");
                                $stmt->execute([':brgy' => $sessionBrgy, ':created_by' => $currentUserId]);
                            } elseif (!empty($currentUserId)) {
                                // fallback: count cases created by this user (owner-scoped)
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE created_by = :created_by");
                                $stmt->execute([':created_by' => $currentUserId]);
                            } elseif (!empty($sessionBrgy)) {
                                // fallback: count all cases in barangay
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE brgy = :brgy");
                                $stmt->execute([':brgy' => $sessionBrgy]);
                            } else {
                                // final fallback: global count
                                $stmt = $pdo->query("SELECT COUNT(*) AS total_count FROM cases");
                            }
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                        ?>
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
                            $currentUserId = isset($_SESSION['loggedInUser']['id']) ? $_SESSION['loggedInUser']['id'] : (isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null);
                            if (!empty($sessionBrgy) && !empty($currentUserId)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE status = '1' AND brgy = :brgy AND created_by = :created_by");
                                $stmt->execute([':brgy' => $sessionBrgy, ':created_by' => $currentUserId]);
                            } elseif (!empty($currentUserId)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE status = '1' AND created_by = :created_by");
                                $stmt->execute([':created_by' => $currentUserId]);
                            } elseif (!empty($sessionBrgy)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE status = '1' AND brgy = :brgy");
                                $stmt->execute([':brgy' => $sessionBrgy]);
                            } else {
                                $stmt = $pdo->query("SELECT COUNT(*) AS total_count FROM cases WHERE status = '1'");
                            }
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
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
                            $currentUserId = isset($_SESSION['loggedInUser']['id']) ? $_SESSION['loggedInUser']['id'] : (isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null);
                            if (!empty($sessionBrgy) && !empty($currentUserId)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE status = 'pending' AND brgy = :brgy AND created_by = :created_by");
                                $stmt->execute([':brgy' => $sessionBrgy, ':created_by' => $currentUserId]);
                            } elseif (!empty($currentUserId)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE status = 'pending' AND created_by = :created_by");
                                $stmt->execute([':created_by' => $currentUserId]);
                            } elseif (!empty($sessionBrgy)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE status = 'pending' AND brgy = :brgy");
                                $stmt->execute([':brgy' => $sessionBrgy]);
                            } else {
                                $stmt = $pdo->query("SELECT COUNT(*) AS total_count FROM cases WHERE status = 'pending'");
                            }
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
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
                            $currentUserId = isset($_SESSION['loggedInUser']['id']) ? $_SESSION['loggedInUser']['id'] : (isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null);
                            if (!empty($sessionBrgy) && !empty($currentUserId)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE status = '0' AND brgy = :brgy AND created_by = :created_by");
                                $stmt->execute([':brgy' => $sessionBrgy, ':created_by' => $currentUserId]);
                            } elseif (!empty($currentUserId)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE status = '0' AND created_by = :created_by");
                                $stmt->execute([':created_by' => $currentUserId]);
                            } elseif (!empty($sessionBrgy)) {
                                $stmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM cases WHERE status = '0' AND brgy = :brgy");
                                $stmt->execute([':brgy' => $sessionBrgy]);
                            } else {
                                $stmt = $pdo->query("SELECT COUNT(*) AS total_count FROM cases WHERE status = '0'");
                            }
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
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
