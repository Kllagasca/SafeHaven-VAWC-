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
                    echo "<h5>" . htmlspecialchars($paramResult, ENT_QUOTES, 'UTF-8') . "</h5>";
                    return false;
                }

                // Fetch social media data using PDO
                $socialMedia = getById('social_medias', $paramResult);
                if ($socialMedia) {
                    if ($socialMedia['status'] == 200) {
            ?>

                    <input type="hidden" name="socialMediaId" value="<?= htmlspecialchars($socialMedia['data']['id'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label>Social Media Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($socialMedia['data']['name'], ENT_QUOTES, 'UTF-8') ?>" required class="form-control"/>
                    </div>

                    <div class="mb-3">
                        <label>Social Media URL/Links</label>
                        <input type="text" name="url" value="<?= htmlspecialchars($socialMedia['data']['url'], ENT_QUOTES, 'UTF-8') ?>" required class="form-control"/>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <br/>
                        <input type="checkbox" name="status" 
                            <?= $socialMedia['data']['status'] == 1 ? 'checked' : ''; ?>
                            style="width: 30px;height:30px;"
                        />
                    </div>

                    <div class="mb-3 text-end">
                        <button type="submit" name="updateSocialMedia" class="btn btn-primary">Update</button>
                    </div>

            <?php
                    } else {
                        echo "<h5>" . htmlspecialchars($socialMedia['message'], ENT_QUOTES, 'UTF-8') . "</h5>";
                    }
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