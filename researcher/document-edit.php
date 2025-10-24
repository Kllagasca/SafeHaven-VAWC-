<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit Document
                    <a href="documents.php" class="btn btn-danger float-end">Back</a>
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

                // Retrieve document details using PDO
                $document = getById('documents', $paramResult);
                if ($document) {

                    if ($document['status'] == 200) {
                        ?>

                        <input type="hidden" name="documentId" value="<?= $document['data']['id']; ?>">

                        <div class="mb-3">
                            <label>Document Name</label>
                            <input type="text" name="name" value="<?= $document['data']['name']; ?>" required class="form-control"/>
                        </div>

                        <div class="mb-3">
                            <label>Upload File</label>
                            <input type="file" name="file" class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label>Status (checked=hidden, un-checked=visible)</label>
                            <br/>
                            <input type="checkbox" name="status" <?= $document['data']['status'] == 1 ? 'checked':'';?> style="width:30px;height:30px;"/>
                        </div>

                        <div class="mb-3 text-end">
                            <button type="submit" name="updateDoc" class="btn btn-primary">Update Document</button>
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
