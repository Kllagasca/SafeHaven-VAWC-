<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit Case
                    <a href="cases.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

            <form action="code.php" method="POST" enctype="multipart/form-data">

            <?php

                if (!isset($_GET['caseno']) || empty($_GET['caseno'])) {
                    echo "<h5>Invalid or missing case number.</h5>";
                    return;
                }

                $caseno = validate($_GET['caseno']);

                $query = "SELECT * FROM cases WHERE caseno = :caseno";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':caseno', $caseno);
                $stmt->execute();

                $case = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($case):
            ?>

            <input type="hidden" name="caseno" value="<?= htmlspecialchars($case['caseno']) ?>">
            <input type="hidden" name="old_image" value="<?= htmlspecialchars($case['image']) ?>">

            <!-- Case Details -->
            <h4>Case Details</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Case No.</label>
                    <input type="text" name="casenum_display" value="<?= htmlspecialchars($case['caseno']) ?>" class="form-control" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Case Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($case['title']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Case Status</label>
                    <select name="status" class="form-select">
                        <option value="open" <?= $case['status'] == 'open' ? 'selected' : '' ?>>Open Case</option>
                        <option value="closed" <?= $case['status'] == 'closed' ? 'selected' : '' ?>>Closed Case</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Incident Location</label>
                    <input type="text" name="barangay" value="<?= htmlspecialchars($case['brgy']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Date of Incident</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($case['date']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Contact Person</label>
                    <input type="text" name="contactp" value="<?= htmlspecialchars($case['contactp']) ?>" class="form-control" required>
                </div>
            </div>

            <!-- Complainant -->
            <h4>Complainant Details</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Name</label>
                    <input type="text" name="complainant" value="<?= htmlspecialchars($case['comp_name']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Age</label>
                    <input type="text" name="cage" value="<?= htmlspecialchars($case['comp_age']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Contact Number</label>
                    <input type="text" name="cnum" value="<?= htmlspecialchars($case['comp_num']) ?>" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label>Address</label>
                <input type="text" name="caddress" value="<?= htmlspecialchars($case['comp_address']) ?>" class="form-control" required>
            </div>

            <!-- Respondent -->
            <h4>Respondent Details</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Name</label>
                    <input type="text" name="respondent" value="<?= htmlspecialchars($case['resp_name']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Age</label>
                    <input type="text" name="rage" value="<?= htmlspecialchars($case['resp_age']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Contact Number</label>
                    <input type="text" name="rnum" value="<?= htmlspecialchars($case['resp_num']) ?>" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label>Address</label>
                <input type="text" name="raddress" value="<?= htmlspecialchars($case['resp_address']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Case Description</label>
                <textarea name="long_description" class="form-control mySummernote" rows="4"><?= htmlspecialchars($case['long_description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label>Upload Case/Evidence Image</label>
                <input type="file" name="image" class="form-control">
                <?php if (!empty($case['image'])): ?>
                    <img src="../<?= htmlspecialchars($case['image']) ?>" width="100" height="100" class="mt-2" alt="Old Image">
                <?php endif; ?>
            </div>

            <div class="mb-3 text-end">
                <button type="submit" name="updateCase" class="btn btn-primary">Update Case</button>
            </div>

            <?php else: ?>
                <h5>Case Not Found</h5>
            <?php endif; ?>

            </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>