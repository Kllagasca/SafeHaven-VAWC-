<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Events/Activity Images
                    <a href="carousel-create.php" class="btn btn-primary float-end">Add Image</a>
                </h4>
            </div>
            <div class="card-body">

                <?= alertmessage(); ?>

                <table id="myTable" class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Image Id</th>
                            <th>Image Title</th>
                            <th>Image Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody> 
                    <?php 
                    // Fetch carousel data using PDO
                    $query = "SELECT * FROM carousel";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $carousel = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($carousel) {
                        if (count($carousel) > 0) {
                            foreach ($carousel as $item) {
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['id']); ?></td>
                                        <td><?= htmlspecialchars($item['name']); ?></td>

                                        <td>
                                            <?php 
                                            if ($item['status'] == 1) {
                                                echo '<span class="badge bg-danger text-white">Hidden</span>';
                                            } else {
                                                echo '<span class="badge bg-success text-white">Visible</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="carousel-edit.php?id=<?= $item['id']; ?>" class="btn mb-0 btn-success btn-sm">Edit</a>
                                            <a href="carousel-delete.php?id=<?= $item['id']; ?>" 
                                               class="btn btn-danger btn-sm mx-2 mb-0" 
                                               onclick="return confirm('Are you sure you want to delete this data?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="4">No Record Found</td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="4">Something went wrong</td>
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