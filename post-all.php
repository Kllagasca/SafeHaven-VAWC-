<?php
$pageTitle = "Posts";
include('includes/navbar.php');
require 'config/supabase_connect.php';

try {
    $serviceQuery = "SELECT * FROM services WHERE status = FALSE AND approval_status = 'approved'";
    $stmt = $pdo->query($serviceQuery);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card img {
            max-height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
        }

        .card-text {
            font-size: 0.85rem;
        }

        .bg-custom2 {
            background: radial-gradient(circle at 0% 0%, #004aad, #cb6ce6);
        }
    </style>
</head>
<body>
<div class="py-5">
    <div class="container">
        <div class="row g-4"> <!-- Added g-4 for consistent gap -->
            <div class="col-12">
                <div class="py-3 bg-custom2 text-white mb-4">
                    <h2 class="text-center fw-bold">Posts</h2>
                </div>
            </div>
            <?php if (count($result) > 0): ?>
                <?php foreach ($result as $row): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card shadow-sm h-100">
                            <img src="<?= htmlspecialchars($row['image']) ?: 'assets/img/no-image.png'; ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($row['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text text-truncate"><?= htmlspecialchars(strip_tags($row['long_description'])); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark">
                                        <?= htmlspecialchars($row['created_at']); ?>
                                    </span>
                                    <a href="post.php?slug=<?= htmlspecialchars($row['slug']); ?>" 
                                       class="btn btn-primary btn-sm">
                                       Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <h5 class="text-center">No approved posts to display.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


