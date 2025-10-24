<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Add Image
                    <a href="carousel.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

                <?= alertmessage(); ?>

                <form action="code.php" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label>Image Name</label>
                        <input type="text" name="name" required class="form-control"/>
                    </div>

                    <div class="mb-3">
                        <label>Upload Carousel Image</label>
                        <input type="file" name="image" class="form-control" required/>
                    </div>

                    <div class="mb-3">
                        <label>Status (checked=hidden, un-checked=visible)</label>
                        <br/>
                        <input type="checkbox" name="status" style="width:30px;height:30px;"/>
                    </div>

                    <div class="mb-3 text-end">
                        <button type="submit" name="saveImage" class="btn btn-primary">Save Image</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<!-- Include Quill -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
    // Initialize Quill editor
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Write your post content here...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'], // Formatting buttons
                [{ 'list': 'ordered' }, { 'list': 'bullet' }], // Lists
                ['link', 'image'], // Add links or images
                ['clean'] // Remove formatting
            ]
        }
    });

    // Prepopulate editor content with description from PHP
    var description = `<?= isset($carousel['data']['description']) ? htmlspecialchars($carousel['data']['description'], ENT_QUOTES, 'UTF-8') : ''; ?>`;
    quill.root.innerHTML = description;

    // On form submission, transfer Quill content to the hidden textarea
    document.querySelector('form').onsubmit = function () {
        document.querySelector('#editor-content').value = quill.root.innerHTML; // Get editor content as HTML
    };
</script>

