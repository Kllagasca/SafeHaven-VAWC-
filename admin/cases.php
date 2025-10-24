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


                        foreach ($cases as $item) {
                        ?>
                        <tr>
                            <td class="doc-title"><?= htmlspecialchars($item['caseno']); ?></td>
                            <td class="doc-title"><?= htmlspecialchars($item['title']); ?></td>
                            <td class="doc-title"><?= htmlspecialchars($item['brgy']); ?></td>
                            <td class="doc-title"><?= htmlspecialchars($item['date']); ?></td>
                            <td class="doc-title"><?= htmlspecialchars($item['comp_name']); ?></td>
                            <td>
                                <a href="case-details.php?id=<?= urlencode($item['caseno']); ?>" class="btn btn-primary btn-sm">
                                    View Details
                                </a>
                            </td>


                            <td><?= $item['status'] == 0 ? "Open" : "Closed"; ?></td>
                            <td>
                            <a href="case-edit.php?caseno=<?= urlencode($item['caseno']); ?>" class="btn btn-primary btn-sm">Edit</a>

                                <a href="case-delete.php?id=<?= $item['caseno']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this case?')">Delete</a>
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

<?php include('includes/footer.php'); ?>

<style>
    #myTable th, #myTable td {
        white-space: nowrap;
    }
    #myTable .doc-title {
        max-width: 100px;
        white-space: normal;
        word-wrap: break-word;
    }
</style>
