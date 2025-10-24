<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit News
                    <a href="news.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

            <form action="code.php" method="POST" enctype="multipart/form-data">

            <?php
                // Check if 'id' is valid
                $paramResult = checkParamId('id', $pdo);
                if (!is_numeric($paramResult)) {
                    echo "<h5>.$paramResult.</h5>";
                    return false;
                }

                // Fetch news by ID using PDO
                $newsId = validate($paramResult);
                $query = "SELECT * FROM news WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id', $newsId, PDO::PARAM_INT);
                $stmt->execute();

                $news = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($news) {
                    if ($news['status'] == false) {
            ?>

                <input type="hidden" name="newsId" value="<?= htmlspecialchars($news['id'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="mb-3">
                    <label>News Title</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($news['name'], ENT_QUOTES, 'UTF-8'); ?>" required class="form-control"/>
                </div>

                <div class="mb-3">
                    <label>News Content</label>
                    <textarea name="long_description" class="form-control mySummernote" rows="3"><?= htmlspecialchars($news['long_description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Upload News Image</label>
                    <input type="file" name="image" class="form-control" />
                    <img src="<?='../'.trim($news['image']); ?>" style="width:70px;height:70px;" alt="Img"/>
                </div>

                <div class="mb-3">
                    <label>Status (checked=hidden, un-checked=visible)</label>
                    <br/>
                    <input type="checkbox" name="status" <?= $news['status'] == 1 ? 'checked':'';?> style="width:30px;height:30px;"/>
                </div>

                <div class="mb-3 text-end">
                    <button type="submit" name="updateNews" class="btn btn-primary">Update News</button>
                </div>

            <?php 
                    } else {
                        echo "<h5>No such data found!</h5>";
                    }
                } else {
                    echo "<h5>Something Went Wrong!</h5>";
                }
            ?>

            </form>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
