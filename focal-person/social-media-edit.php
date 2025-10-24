<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit Social Media
                    <a href="social-media.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

            <form action="code.php" method="POST">

            <?php
                $paramResult = checkParamId('id', $pdo);
                if (!is_numeric($paramResult)) {
                    echo "<h5>.$paramResult.</h5>";
                    return false;
                }

                // Fetch social media record using PDO
                $socialMediaId = validate($paramResult);
                $query = "SELECT * FROM social_medias WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id', $socialMediaId, PDO::PARAM_INT);
                $stmt->execute();
                $socialMedia = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($socialMedia) {
                    ?>
                    
                    <input type="hidden" name="socialMediaId" value="<?= $socialMedia['id'] ?>">

                    <div class="mb-3">
                        <label>Social Media Name</label>
                        <input type="text" name="name" value="<?= $socialMedia['name'] ?>" required class="form-control"/>
                    </div>

                    <div class="mb-3">
                        <label>Social Media URL/Links</label>
                        <input type="text" name="url" value="<?= $socialMedia['url'] ?>" required class="form-control"/>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <br/>
                        <input type="checkbox" name="status" <?= $socialMedia['status'] == 1 ? 'checked' : ''; ?> style="width: 30px;height:30px;" />
                    </div>

                    <div class="mb-3 text-end">
                        <button type="submit" name="updateSocialMedia" class="btn btn-primary">Update</button>
                    </div>

                    <?php
                } else {
                    echo "<h5>Something Went Wrong</h5>";
                }
            ?>

            </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
