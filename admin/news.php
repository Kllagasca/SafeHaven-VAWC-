<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">
                        <h4> Relevant News </h4>
                    </div>
                    <div class="col-md-7">
                        <!-- Add Post Button -->
                        <a href="news-create.php" class="btn btn-primary float-end">Add Relevant News</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
    <?= alertmessage(); ?>

    <table id="myTable" class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>News Id</th>
                <th>News Title</th>
                <th class="doc-title">News Content</th>
                <th>News Status</th>
                <th>News Action</th>
            </tr>
        </thead>
        <tbody>
            <?php

$query = "SELECT * FROM news";
$stmt = $pdo->prepare($query);
$stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($news as $item) {
            ?>
            <tr>
                <td><?= htmlspecialchars($item['id']); ?></td> <!-- This line could cause the warning -->
                <td><?= htmlspecialchars($item['name']); ?></td>
                <td class="doc-title"><?= htmlspecialchars(strip_tags($item['long_description'])); ?></td>
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
                    <a href="news-edit.php?id=<?= $item['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="news-delete.php?id=<?= $item['id']; ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this news?')">Delete</a>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

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