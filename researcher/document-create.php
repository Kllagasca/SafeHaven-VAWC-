<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Add Document
                    <a href="documents.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

            <form action="code.php" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                <label>Document Name</label>
                <input type="text" name="name" required class="form-control"/>
                </div>

                <div class="mb-3">
                <label>Upload File</label>
                <input type="file" name="file" class="form-control"/>
                </div>

                <div class="mb-3">
                <label>Status (checked=hidden, un-checked=visible)</label>
                <br/>
                <input type="checkbox" name="status" style="width:30px;height:30px;"/>
                </div>

                <div class="mb-3 text-end">
                    <button type="submit" name="saveDoc" class="btn btn-primary">Save Document</button>
                </div>
                
            </form>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
