<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Social Media Lists
                    <a href="social-media-create.php" class="btn btn-primary float-end">Add Social Media</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

                <table id="myTable" class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Social Title</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    // Fetch social media data using PDO
                    $query = "SELECT * FROM social_medias";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $socialMedias = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($socialMedias && count($socialMedias) > 0) {
                        foreach ($socialMedias as $item) {
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['id']); ?></td>
                                    <td><?= htmlspecialchars($item['name']); ?></td>
                                    <td><?= htmlspecialchars($item['url']); ?></td>

                                    <td>
                                        <?php 
                                        if ($item['status'] == 1) {
                                            echo '<span class="badge bg-danger text-white">Hidden</span>';
                                        } else {
                                            echo '<span class="badge bg-success text-white">Shown</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="social-media-edit.php?id=<?= $item['id']; ?>" class="btn mb-0 btn-success btn-sm">Edit</a>
                                        <a href="social-media-delete.php?id=<?= $item['id']; ?>" class="btn btn-danger btn-sm mx-2 mb-0" onclick="return confirm('Are you sure you want to delete this data?')">Delete</a>
                                    </td>
                                </tr>
                            <?php
                        }
                    } else {
                        ?>
                            <tr>
                                <td colspan="5">No Record Found</td>
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