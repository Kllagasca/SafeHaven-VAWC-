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
        <div style="position: absolute; top: 20px; right: 20px; z-index: 1000;">
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

    <!-- Total Posts -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3"  style="background-color: #554fb0;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge bg-white rounded-pill px-2 py-1" style="color: #554fb0;">Total Posts</h6>
                    <h3 class="font-weight-bold mb-0 text-white">
                        <?= getCount('services') ?>
                    </h3>
                </div>
                <i class="fa fa-home text-white" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

    <!-- Total Approved Posts -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3 bg-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize bg-white text-success font-weight-bold badge rounded-pill px-2 py-1">Total Approved Posts</h6>
                    <h3 class="font-weight-bold text-white mb-0">
                    <?php
                        // PDO query to count approved services
                        $query = "SELECT COUNT(*) AS total_count FROM services WHERE approval_status = 'approved'";
                        $stmt = $pdo->prepare($query);

                        if ($stmt->execute()) {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } else {
                            echo "Error: " . implode(", ", $stmt->errorInfo());
                        }
                    ?>
                    </h3>
                </div>
                <i class="fa fa-check-circle text-white" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

    <!-- Total Pending Posts -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3" style="background-color: #554fb0;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge bg-white rounded-pill px-2 py-1" style="color:#554fb0;">Total Pending Posts</h6>
                    <h3 class="font-weight-bold mb-0 text-white">
                    <?php
                    // PDO query to count pending services
                        $query = "SELECT COUNT(*) AS total_count FROM services WHERE approval_status = 'pending'";
                        $stmt = $pdo->prepare($query);

                        if ($stmt->execute()) {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } else {
                            echo "Error: " . implode(", ", $stmt->errorInfo());
                        }
                    ?>
                    </h3>
                </div>
                <i class="fa fa-clock text-white" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

    <!-- Total Rejected Posts -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3 bg-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge bg-white text-danger rounded-pill px-2 py-1">Total Rejected Posts</h6>
                    <h3 class="font-weight-bold text-white mb-0">
                    <?php
                        // PDO query to count rejected services
                        $query = "SELECT COUNT(*) AS total_count FROM services WHERE approval_status = 'rejected'";
                        $stmt = $pdo->prepare($query);

                        if ($stmt->execute()) {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } else {
                            echo "Error: " . implode(", ", $stmt->errorInfo());
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
                        // PDO query to count approved documents
                        $query = "SELECT COUNT(*) AS total_count FROM documents WHERE approval_status = 'approved'";
                        $stmt = $pdo->prepare($query);

                        if ($stmt->execute()) {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } else {
                            echo "Error: " . implode(", ", $stmt->errorInfo());
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
                        // PDO query to count pending documents
                        $query = "SELECT COUNT(*) AS total_count FROM documents WHERE approval_status = 'pending'";
                        $stmt = $pdo->prepare($query);

                        if ($stmt->execute()) {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } else {
                            echo "Error: " . implode(", ", $stmt->errorInfo());
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
                        // PDO query to count rejected documents
                        $query = "SELECT COUNT(*) AS total_count FROM documents WHERE approval_status = 'rejected'";
                        $stmt = $pdo->prepare($query);

                        if ($stmt->execute()) {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $row['total_count'];
                        } else {
                            echo "Error: " . implode(", ", $stmt->errorInfo());
                        }
                    ?>
                    </h3>
                </div>
                <i class="fa fa-times-circle text-danger" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

<?php include('includes/footer.php'); ?>
