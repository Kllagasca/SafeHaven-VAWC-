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
                // Validate the document ID from the URL
                $paramResult = checkParamId('id', $pdo);
                if(!is_numeric($paramResult)){
                    echo "<h5>.$paramResult.</h5>";
                    return false;
                }

                // Fetch document details using PDO
                $query = "SELECT * FROM documents WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':id', $paramResult, PDO::PARAM_INT);
                $stmt->execute();
                $document = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($document) {
                    ?>
                    
                    <input type="hidden" name="documentId" value="<?= htmlspecialchars($document['id']); ?>">

                    <div class="mb-3">
                    <label>Document Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($document['name']); ?>" required class="form-control"/>
                    </div>

                    <div class="mb-3">
                    <label>Upload File</label>
                    <input type="file" name="file" class="form-control" />
                    </div>

                    <div class="mb-3">
                    <label>Status (checked=hidden, un-checked=visible)</label>
                    <br/>
                    <input type="checkbox" name="status" <?= $document['status'] == 1 ? 'checked':'';?> style="width:30px;height:30px;"/>
                    </div>

                    <div class="mb-3 text-end">
                        <button type="submit" name="updateDoc" class="btn btn-primary">Update Document</button>
                    </div>

                    <?php
                } else {
                    echo "<h5>No such data found!</h5>";
                }
            ?>

            </form>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
