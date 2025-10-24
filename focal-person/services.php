<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">
                        <h4> Posts </h4>
                    </div>
                    <div class="col-md-7">
                        <!-- Add Post Button -->
                        <a href="service-create.php" class="btn btn-primary float-end">Add Post</a>

                        <!-- Filter Form -->
                        <form action="" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="date" name="date" required value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <select name="status" required class="form-select">
                                        <option value="">Select Status</option>
                                        <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                        <option value="approved" <?= (isset($_GET['status']) && $_GET['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                                        <option value="rejected" <?= (isset($_GET['status']) && $_GET['status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="services.php" class="btn btn-danger">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= alertmessage(); ?>

                <?php 
                // Filtering logic
                if (isset($_GET['date']) && $_GET['date'] != '' && isset($_GET['status']) && $_GET['status'] != '') {
                    $date = validate($_GET['date']);
                    $status = validate($_GET['status']);

                    // Fetch filtered results using PDO
                    $query = "SELECT * FROM services WHERE created_at = :date AND approval_status = :status";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['date' => $date, 'status' => $status]);
                } else {
                    // Fetch all results if no filters are applied
                    $query = "SELECT * FROM services";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                }

                $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($services && count($services) > 0) {
                ?>

                <table id="myTable" class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Post Id</th>
                            <th>Post Title</th>
                            <th>Created At</th>
                            <th>Approval Status</th>
                            <th>Post Action</th>
                            <th>Approval Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($services as $item) {
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['id']); ?></td>
                            <td><?= htmlspecialchars($item['name']); ?></td>
                            <td><?= htmlspecialchars($item['created_at']); ?></td>
                            <td>
                                <?php 
                                if ($item['approval_status'] == 'pending') {
                                    echo '<span class="badge bg-warning text-dark">Pending</span>';
                                } elseif ($item['approval_status'] == 'approved') {
                                    echo '<span class="badge bg-success text-white">Approved</span>';
                                } elseif ($item['approval_status'] == 'rejected') {
                                    echo '<span class="badge bg-danger text-white">Rejected</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="service-edit.php?id=<?= $item['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="service-delete.php?id=<?= $item['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                            </td>
                            <td>
                                <?php if ($item['approval_status'] == 'pending') : ?>
                                    <a href="service-approve.php?id=<?= $item['id']; ?>&action=approve" 
                                       class="btn btn-success btn-sm">Approve</a>
                                    <a href="service-approve.php?id=<?= $item['id']; ?>&action=reject" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to reject this post?')">Reject</a>
                                <?php elseif ($item['approval_status'] == 'approved') : ?>
                                    <span class="badge bg-success text-white">Approved</span>
                                <?php elseif ($item['approval_status'] == 'rejected') : ?>
                                    <span class="badge bg-danger text-white">Rejected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>

                <?php
                } else {
                    echo '<h5>No Record Found</h5>';
                }
                ?>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
