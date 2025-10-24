<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit Image
                    <a href="carousel.php" class="btn btn-danger float-end">Back</a>
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

// Fetch carousel data using PDO
$carousel = getById('carousel', $paramResult);

if ($carousel) {

    if ($carousel['status'] == 200) {
        ?>

                                
                <input type="hidden" name="imageId" value="<?= $carousel['data']['id']; ?>">

                <div class="mb-3">
                <label>Post Name</label>
                <input type="text" name="name" value="<?= $carousel['data']['name']; ?>" required class="form-control"/>
                </div>

                <div class="mb-3">
                <label>Upload Post Image</label>
                <input type="file" name="image" class="form-control" />
                <img src="<?='../'.trim($carousel['data']['image']); ?>" style="width:70px;height:70px;" alt="Img"/>
                </div>

                <div class="mb-3">
                <label>Status (checked=hidden, un-checked=visible)</label>
                <br/>
                <input type="checkbox" name="status" <?= $carousel['data']['status'] == 1 ? 'checked':'';?> style="width:30px;height:30px;"/>
                </div>

                <div class="mb-3 text-end">
                    <button type="submit" name="updateImage" class="btn btn-primary">Update Image</button>
                </div>
                

                <?php 
                            }
                            else{
                                echo "<h5>No such data found!</h5>";
                            }
                        }
                        else{
                            echo "<h5>Something Went Wrong!</h5>";
                        }
            ?>


            </form>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>