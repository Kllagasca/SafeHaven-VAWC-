<?php
// Admin-only page to generate focal person accounts (email + password)
include('authentication.php'); // ensures $pdo and session/auth checks
require_once '../config/function.php';
include('../config/db_connect.php'); // ✅ Localhost MySQL connection ($conn)

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
    $barangay = isset($_POST['barangay']) ? trim($_POST['barangay']) : '';
    if ($barangay === '') {
        $errors[] = 'Barangay is required.';
    }

    $date = isset($_POST['date']) && trim($_POST['date']) !== '' ? trim($_POST['date']) : date('Y-m-d');
    $contact_person = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '';

    $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $barangay)));
    if ($slug === '') $slug = 'barangay';
    $domain = $slug . '.safehaven.com';
    if ($count < 1) $count = 1;
    if ($count > 100) $count = 100;

    if (empty($errors)) {
        for ($i = 0; $i < $count; $i++) {
            // Generate unique email (check Supabase)
            $unique = false;
            $attempts = 0;
            // Build a cleaned, lowercase contact-person token for email local-part (remove spaces)
            $cp_token = $contact_person !== '' ? strtolower(preg_replace('/\s+/', '', $contact_person)) : 'focalperson';
            do {
                $local = $cp_token . randomString(4) . rand(10, 99);
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

            // Password
            $plain = generatePassword(10);
            $hashed = password_hash($plain, PASSWORD_DEFAULT);

            // Use contact person name as first name if provided, otherwise default
            $fname = $contact_person !== '' ? $contact_person : 'FocalPerson';
            $lname = $barangay;
            $role = 'fperson';
            $is_ban = 0;

            try {
                // ✅ 1. Insert into Supabase (main DB using $pdo)
                if ($barangay !== '') {
                    $query = "INSERT INTO users (fname, lname, email, password, role, is_ban, barangay)
                              VALUES (:fname, :lname, :email, :password, :role, :is_ban, :barangay)";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([
                        ':fname' => $fname,
                        ':lname' => $lname,
                        ':email' => $email,
                        ':password' => $hashed,
                        ':role' => $role,
                        ':is_ban' => $is_ban,
                        ':barangay' => $barangay,
                    ]);
                } else {
                    $query = "INSERT INTO users (fname, lname, email, password, role, is_ban)
                              VALUES (:fname, :lname, :email, :password, :role, :is_ban)";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([
                        ':fname' => $fname,
                        ':lname' => $lname,
                        ':email' => $email,
                        ':password' => $hashed,
                        ':role' => $role,
                        ':is_ban' => $is_ban,
                    ]);
                }

                // ✅ 2. Also insert into Localhost MySQL (XAMPP)
                if ($barangay !== '') {
                    $local_sql = "INSERT INTO users (fname, lname, email, password, role, is_ban, barangay)
                                  VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $local_stmt = mysqli_prepare($conn, $local_sql);
                    // types: fname(s), lname(s), email(s), password(s), role(s), is_ban(i), barangay(s)
                    mysqli_stmt_bind_param($local_stmt, "sssssis",
                        $fname, $lname, $email, $hashed, $role, $is_ban, $barangay
                    );
                } else {
                    $local_sql = "INSERT INTO users (fname, lname, email, password, role, is_ban)
                                  VALUES (?, ?, ?, ?, ?, ?)";
                    $local_stmt = mysqli_prepare($conn, $local_sql);
                    mysqli_stmt_bind_param($local_stmt, "sssssi",
                        $fname, $lname, $email, $hashed, $role, $is_ban
                    );
                }
                mysqli_stmt_execute($local_stmt);

                // Track generated
                $generated[] = [
                    'email' => $email,
                    'password' => $plain,
                    'barangay' => $barangay,
                    'date' => $date,
                    'contact_person' => $contact_person
                ];
            } catch (PDOException $e) {
                $errors[] = 'DB error: ' . $e->getMessage();
            } catch (mysqli_sql_exception $ex) {
                $errors[] = 'Local DB error: ' . $ex->getMessage();
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
                            <label>Barangay:</label>
                            <input type="text" name="barangay" class="form-control" placeholder="Enter Barangay" required>
                        </div>
                        <div class="col-md-3">
                            <label>Date:</label>
                            <input name="date" class="form-control" value="<?= date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Focal Person:</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="Enter Focal Person Name">
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
                                <th>Focal Person</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($generated as $g): ?>
                                <tr>
                                    <td><?= htmlspecialchars($g['email']); ?></td>
                                    <td><?= htmlspecialchars($g['barangay']); ?></td>
                                    <td><?= htmlspecialchars($g['date']); ?></td>
                                    <td><?= htmlspecialchars($g['contact_person']); ?></td>
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