<?php
$pageTitle = "Posts";
include('includes/navbar.php');
include('post-bg.php');

if (isset($_GET['slug'])) {
    if ($_GET['slug'] == null) {
        redirect('posts.php', '');
    }
} else {
    redirect('posts.php', '');
}

$slug = validate($_GET['slug']);

// Fetch the service using PDO
try {
    $query = "SELECT * FROM services WHERE status = :status AND slug = :slug LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':status', 0, PDO::PARAM_INT);
    $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
    $stmt->execute();
    $rowData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rowData) {
        redirect('posts.php', '');
    }
} catch (PDOException $e) {
    die('Query Failed: ' . $e->getMessage()); // Debugging
}
?>

<div class="py-4 mt-3 bg-light">
    <div class="container">
        <h2 class="text-dark text-center"><?= htmlspecialchars($rowData['name']); ?></h2>
        <p class="text-dark text-center"> 
            <span>Posted at: <?= htmlspecialchars($rowData['created_at']); ?></span>
        </p>
    </div>
</div>

<div class="py-4 mb-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-body shadow-lg">
                    <div class="underline"></div>
                    <div class="mb-3">
                        <?php if (!empty($rowData['image'])) : ?>
                            <img src="<?= htmlspecialchars($rowData['image']); ?>" class="w-100 rounded" alt="Img" style="min-height:200px; max-height:500px;" />
                        <?php else : ?>
                            <img src="assets/img/no-image.png" class="w-100 rounded" alt="Img" style="min-height:200px; max-height:500px;" />
                        <?php endif; ?>
                    </div>

                    <p class="text-dark">
                        <?= htmlspecialchars($rowData['long_description']); ?>
                    </p>

                    <div class="d-flex justify-content-end">
                        <a href="index.php" class="btn btn-danger" style="width: 80px;">Back</a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
