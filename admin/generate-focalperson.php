<?php
// Admin-only page to generate focal person accounts (email + password)
include('authentication.php'); // ensures $pdo and session/auth checks
require_once '../config/function.php';

// Helpers
function randomString($length = 8) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
    $str = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[random_int(0, $max)];
    }
    return $str;
}

function generatePassword($length = 10) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*?';
    $pass = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $pass .= $chars[random_int(0, $max)];
    }
    return $pass;
}

$generated = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $count = isset($_POST['count']) ? (int) $_POST['count'] : 1;
    // Get barangay from form and build a domain from it
    $barangay = isset($_POST['barangay']) ? trim($_POST['barangay']) : '';
    if ($barangay === '') {
        $errors[] = 'Barangay is required.';
    }

    // Get optional date and contact person
    $date = isset($_POST['date']) && trim($_POST['date']) !== '' ? trim($_POST['date']) : date('Y-m-d');
    $contact_person = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '';

    // create a safe slug for the barangay to use as domain prefix
    $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $barangay)));
    if ($slug === '') $slug = 'barangay';
    $domain = $slug . '.example.com';
    if ($count < 1) $count = 1;
    if ($count > 100) $count = 100; // limit to 100 at a time

    if (empty($errors)) {
        for ($i = 0; $i < $count; $i++) {
        // build a unique email
        $unique = false;
        $attempts = 0;
        do {
            $local = 'fperson' . randomString(4) . rand(10,99);
            $email = $local . '@' . $domain;
            $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
            $check->execute([':email' => $email]);
            $exists = (int) $check->fetchColumn();
            $attempts++;
            if ($exists === 0) $unique = true;
        } while (!$unique && $attempts < 10);

        if (!$unique) {
            $errors[] = 'Could not generate a unique email after multiple attempts.';
            continue;
        }

        // generate password
        $plain = generatePassword(10);
        $hashed = password_hash($plain, PASSWORD_DEFAULT);

    // Use specified names for focal person accounts
    // First name should be "FocalPerson" (as requested) and last name is the barangay
    $fname = 'FocalPerson';
    $lname = $barangay;
        $role = 'fperson';
        $is_ban = 0;

        try {
            $query = "INSERT INTO users (fname, lname, email, password, role, is_ban) VALUES (:fname, :lname, :email, :password, :role, :is_ban)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':fname' => $fname,
                ':lname' => $lname,
                ':email' => $email,
                ':password' => $hashed,
                ':role' => $role,
                ':is_ban' => $is_ban,
            ]);

            $generated[] = [
                'email' => $email,
                'password' => $plain,
                'barangay' => $barangay,
                'date' => $date,
                'contact_person' => $contact_person
            ];
        } catch (PDOException $e) {
            $errors[] = 'DB error: ' . $e->getMessage();
        }
        }
    }
}

?>
<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Generate Focal Person Accounts
                    <a href="users.php" class="btn btn-secondary float-end">Back to Users</a>
                </h4>
            </div>
            <div class="card-body">
                <?= alertMessage(); ?>

                <form method="post" id="generateForm">
                    <div class="row">
                        <div class="col-md-2">
                            <label>No. of Focal Persons:</label>
                            <input type="number" name="count" class="form-control" value="1" min="1" max="100">
                        </div>
                        <div class="col-md-4">
                            <label>Barangay (used as last name and in email account):</label>
                            <input type="text" name="barangay" class="form-control" placeholder="e.g., San Isidro" required>
                        </div>
                        <div class="col-md-3">
                            <label>Date:</label>
                            <input type="date" name="date" class="form-control" value="<?= date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Contact Person:</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="e.g., Juan Dela Cruz">
                        </div>
                    </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" name="generate" class="btn btn-primary float-end">Generate</button>
                            </div>
                        </div>
                </form>

                <hr/>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($generated)): ?>
                    <h5>Generated Accounts (save these credentials now — passwords are shown only once)</h5>
                    <table class="table table-sm table-bordered mt-2">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Barangay</th>
                                <th>Date</th>
                                <th>Contact Person</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($generated as $g): ?>
                                <tr>
                                    <td><?= htmlspecialchars($g['email']); ?></td>
                                    <td><?= htmlspecialchars($g['barangay'] ?? $barangay); ?></td>
                                    <td><?= htmlspecialchars($g['date'] ?? ''); ?></td>
                                    <td><?= htmlspecialchars($g['contact_person'] ?? ''); ?></td>
                                    <td><code><?= htmlspecialchars($g['password']); ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
