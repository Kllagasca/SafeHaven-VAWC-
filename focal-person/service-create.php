<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Add Post
                    <a href="services.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

            <form action="code.php" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                <label>Post Name</label>
                <input type="text" name="name" required class="form-control"/>
                </div>

                <div class="mb-3">
                <label>Post Content</label>
                <textarea name="long_description" class="form-control mySummernote" rows="3"></textarea>
                </div>

                <div class="mb-3">
                <label>Upload Post Image</label>
                <input type="file" name="image" class="form-control"/>
                </div>

                <div class="mb-3">
                <label>Status (checked=hidden, un-checked=visible)</label>
                <br/>
                <input type="checkbox" name="status" style="width:30px;height:30px;"/>
                </div>

                <div class="mb-3 text-end">
                    <button type="submit" name="savePost" class="btn btn-primary">Save Post</button>
                </div>
                

            </form>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>