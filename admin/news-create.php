<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Add News
                    <a href="news.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

                <?= alertmessage(); ?>

                <form action="code.php" method="POST" enctype="multipart/form-data">
                    <!-- News Title -->
                    <div class="mb-3">
                        <label for="name">News Title</label>
                        <input type="text" name="name" id="name" required class="form-control" placeholder="Enter news title" />
                    </div>

                    <!-- News Content -->
                    <div class="mb-3">
                        <label for="long_description">News Content</label>
                        <textarea name="long_description" id="long_description" class="form-control mySummernote" rows="3" placeholder="Enter news content"></textarea>
                    </div>

                    <!-- Upload News Image -->
                    <div class="mb-3">
                        <label for="image">Upload News Image</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*" />
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status">Status</label>
                        <br />
                        <input type="checkbox" name="status" id="status" style="width: 30px; height: 30px;" />
                        <span>Checked = Hidden, Unchecked = Visible</span>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-3 text-end">
                        <button type="submit" name="saveNews" class="btn btn-primary">Save News</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
