<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit Post
                    <a href="services.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

            <form action="code.php" method="POST" enctype="multipart/form-data">

            <?php
                        $paramResult = checkParamId('id', $pdo);
                        if (!is_numeric($paramResult)) {
                            echo "<h5>.$paramResult.</h5>";
                            return false;
                        }

                        // Get service data using PDO
                        $serviceId = validate($paramResult);
                        $query = "SELECT * FROM services WHERE id = :id";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':id', $serviceId, PDO::PARAM_INT);
                        $stmt->execute();
                        $service = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($service) {
                            if ($service['status'] == 200) {
                                ?>

                <input type="hidden" name="serviceId" value="<?= htmlspecialchars($service['id']); ?>">

                <div class="mb-3">
                    <label>Post Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($service['name']); ?>" required class="form-control"/>
                </div>

                <div class="mb-3">
                    <label>Post Content</label>
                    <textarea name="long_description" class="form-control mySummernote" rows="3"><?= htmlspecialchars($service['long_description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Upload Post Image</label>
                    <input type="file" name="image" class="form-control" />
                    <img src="<?='../'.trim($service['image']); ?>" style="width:70px;height:70px;" alt="Img"/>
                </div>

                <div class="mb-3">
                    <label>Status (checked=hidden, un-checked=visible)</label>
                    <br/>
                    <input type="checkbox" name="status" <?= $service['status'] == 1 ? 'checked':'';?> style="width:30px;height:30px;"/>
                </div>

                <div class="mb-3 text-end">
                    <button type="submit" name="updatePost" class="btn btn-primary">Update Post</button>
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
