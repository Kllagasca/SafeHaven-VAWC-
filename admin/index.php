<?php include('includes/header.php'); ?>

<h5?php
date_default_timezone_set("Asia/Philippines");
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
            <a href="../index.php" style="text-decoration: none; color: #9953ed; font-weight: bold;">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back Home
            </a>
        </div>
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

    <!-- Total Posts -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge rounded-pill px-2 py-1" style="background-color: #554fb0;">
                        Total Posts
                    </h6>
                    <h3 class="font-weight-bold mb-0">
                        <?= getCount('services') ?>
                    </h3>
                </div>
                <i class="fa fa-home" style="font-size: 60px; flex-shrink: 0; padding-right: 20px; color: #554fb0;"></i>
            </div>
        </div>
    </div>


    <!-- Total Approved Posts -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge bg-success text-white rounded-pill px-2 py-1">
                        Total Approved Posts
                    </h6>
                    <h3 class="font-weight-bold mb-0">
                        <?php
                            try {
                                $query = "SELECT COUNT(*) AS total_count FROM services WHERE approval_status = 'approved'";
                                $stmt = $pdo->query($query); // Execute the query directly
                                $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
                                echo $row['total_count']; // Output the count
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage(); // Handle any exceptions
                            }
                        ?>
                    </h3>
                </div>
                <i class="fa fa-check-circle text-success" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>


    <!-- Total Pending Posts -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge text-white rounded-pill px-2 py-1" style="background-color: #554fb0;">
                        Total Pending Posts
                    </h6>
                    <h3 class="font-weight-bold mb-0">
                        <?php
                            try {
                                $query = "SELECT COUNT(*) AS total_count FROM services WHERE approval_status = 'pending'";
                                $stmt = $pdo->query($query); // Execute the query directly
                                $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
                                echo $row['total_count']; // Output the count
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage(); // Handle any exceptions
                            }
                        ?>
                    </h3>
                </div>
                <i class="fa fa-clock" style="font-size: 60px; flex-shrink: 0; padding-right: 20px; color: #554fb0;"></i>
            </div>
        </div>
    </div>

    <!-- Total Rejected Posts -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold bg-danger badge text-white rounded-pill px-2 py-1">
                        Total Rejected Posts
                    </h6>
                    <h3 class="font-weight-bold mb-0">
                        <?php
                            try {
                                $query = "SELECT COUNT(*) AS total_count FROM services WHERE approval_status = 'rejected'";
                                $stmt = $pdo->query($query); // Execute the query directly
                                $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
                                echo $row['total_count']; // Output the count
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage(); // Handle any exceptions
                            }
                        ?>
                    </h3>
                </div>
                <i class="fa fa-times-circle text-danger" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
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
                            $query = "SELECT COUNT(*) AS total_count FROM documents WHERE approval_status = 'approved'";
                            $stmt = $pdo->query($query); // Execute the query directly
                            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
                            echo $row['total_count']; // Output the count
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage(); // Handle any exceptions
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
                            $query = "SELECT COUNT(*) AS total_count FROM documents WHERE approval_status = 'pending'";
                            $stmt = $pdo->query($query); // Execute the query directly
                            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
                            echo $row['total_count']; // Output the count
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage(); // Handle any exceptions
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
                            $query = "SELECT COUNT(*) AS total_count FROM documents WHERE approval_status = 'rejected'";
                            $stmt = $pdo->query($query); // Execute the query directly
                            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
                            echo $row['total_count']; // Output the count
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage(); // Handle any exceptions
                        }
                    ?>

                    </h3>
                </div>
                <i class="fa fa-times-circle text-danger" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;"></i>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold text-white badge rounded-pill px-2 py-1" style="background-color: #554fb0;">Total Users</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">
                        <?= getCount('users') ?>
                    </h3>
                </div>
                <i class="fa-solid fa-user" style="font-size: 60px; flex-shrink: 0; padding-right: 20px; color: #554fb0;"></i>
            </div>
        </div>
    </div>

        <!-- Total Images -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge text-white rounded-pill px-2 py-1" style=" background-color: #554fb0;">Total Event/Activity Images</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">
                        <?= getCount('carousel') ?>
                    </h3>
                </div>
                <i class="fa-regular fa-image" style="font-size: 60px; flex-shrink: 0; padding-right: 20px; color: #554fb0;"></i>
            </div>
        </div>
    </div>

            <!-- Total Social Media -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge text-white rounded-pill px-2 py-1" style="background-color: #554fb0;">Total Social Media/Links</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">
                        <?= getCount('social_medias') ?>
                    </h3>
                </div>
                <i class="fa-regular fa-image" style="font-size: 60px; flex-shrink: 0; padding-right: 20px; color: #554fb0;"></i>
            </div>
        </div>
    </div>

    <!-- Total News -->
    <div class="col-md-3 mb-4">
        <div class="card card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-sm mb-1 text-capitalize font-weight-bold badge text-white rounded-pill px-2 py-1" style="background-color: #554fb0;">Total News</h6>
                    <h3 class="font-weight-bold mb-0 text-dark">
                        <?= getCount('news') ?>
                    </h3>
                </div>
                <i class="fa-solid fa-newspaper" style="font-size: 60px; flex-shrink: 0; padding-right: 20px;color: #554fb0;"></i>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
