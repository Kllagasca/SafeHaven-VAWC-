<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">
                        <h4> Documents </h4>
                    </div>
                    <div class="col-md-7">
                        <!-- Add Post Button -->
                        <a href="document-create.php" class="btn btn-primary float-end">Add Document</a>

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
                                    <a href="documents.php" class="btn btn-danger">Reset</a>
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
        $query = "SELECT * FROM documents WHERE created_at = :date AND approval_status = :status";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Fetch all results if no filters are applied
        $query = "SELECT * FROM documents";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Debugging line to check the structure of the $documents array

    if ($documents && count($documents) > 0) {
    ?>

    <table id="myTable" class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>Document Id</th>
                <th class="doc-title">Document Title</th>
                <th>Created At</th>
                <th>Approval Status</th>
                <th>Document Action</th>
                <th>Approval Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($documents as $item) {
            ?>
            <tr>
                <td><?= htmlspecialchars($item['id']); ?></td> <!-- This line could cause the warning -->
                <td class="doc-title"><?= htmlspecialchars($item['name']); ?></td>
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
                    <a href="document-edit.php?id=<?= $item['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="document-delete.php?id=<?= $item['id']; ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                </td>
                <td>
                    <?php if ($item['approval_status'] == 'pending') : ?>
                        <a href="document-approve.php?id=<?= $item['id']; ?>&action=approve" 
                           class="btn btn-success btn-sm">Approve</a>
                        <a href="document-approve.php?id=<?= $item['id']; ?>&action=reject" 
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



<?php include('includes/footer.php'); ?>


<style>
    #myTable th, #myTable td {
        white-space: nowrap; /* Prevent text wrapping */
    }
    #myTable .doc-title {
        max-width: 200px; /* Adjust as per your needs */
        white-space: normal; /* Allow text wrapping */
        word-wrap: break-word; /* Break long words if necessary */
    }
</style>