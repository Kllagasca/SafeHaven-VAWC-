<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit User
                    <a href="users.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

                <form action="code.php" method="POST">

                    <?php 
                        // Validate and sanitize the 'id' parameter
                        $paramResult = checkParamId('id', $pdo);
                        if(!is_numeric($paramResult)){
                            echo '<h5>'.$paramResult.'</h5>';
                            return false;
                        }

                        // Get user data using PDO
                        try {
                            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
                            $stmt->execute(['id' => $paramResult]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($user) {
                                ?>

                                <!-- Hidden input for user ID -->
                                <input type="hidden" name="userId" value="<?= htmlspecialchars($user['id']); ?>" required>

                                <div class="row">
                                    <!-- First Name -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label>First Name</label>
                                            <input type="text" name="fname" value="<?= htmlspecialchars($user['fname']); ?>" class="form-control" required>
                                        </div>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label>Last Name</label>
                                            <input type="text" name="lname" value="<?= htmlspecialchars($user['lname']); ?>" class="form-control" required>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label>Email</label>
                                            <input type="text" name="email" value="<?= htmlspecialchars($user['email']); ?>" class="form-control" required>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label>Password</label>
                                            <div class="input-group position-relative">
                                                                <!-- Do not show hashed password. Leave empty so admin can set a new one. -->
                                                                <?php
                                                                // If a temporary password was just generated for this user, show it once
                                                                $tempPwd = '';
                                                                if (isset($_SESSION['temp_password']) && isset($_SESSION['temp_password_user']) && $_SESSION['temp_password_user'] == $user['id']) {
                                                                    $tempPwd = $_SESSION['temp_password'];
                                                                    // clear it so it only shows once
                                                                    unset($_SESSION['temp_password'], $_SESSION['temp_password_user']);
                                                                }
                                                                ?>
                                                                <input type="password" name="password" id="passwordField" class="form-control pe-5" placeholder="Enter new password or leave blank to keep current" value="<?= htmlspecialchars($tempPwd); ?>" />
                                                                <?php if (!empty($tempPwd)): ?>
                                                                    <div class="mt-2">
                                                                        <small class="text-success">Temporary password generated: <strong><?= htmlspecialchars($tempPwd); ?></strong></small>
                                                                    </div>
                                                                <?php endif; ?>
                                                <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePasswordVisibility()" style="cursor: pointer;">
                                                    <i class="fa fa-eye" id="passwordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Role Selection -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label>Select Role</label>
                                            <select name="role" class="form-select" required>
                                                <option value="">Select Role</option>
                                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected':''; ?>>Admin</option>
                                                <option value="fperson" <?= $user['role'] == 'fperson' ? 'selected':''; ?>>Focal Person</option>
                                                <option value="researcher" <?= $user['role'] == 'researcher' ? 'selected':''; ?>>Researcher</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Ban Status -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label>Is Ban</label>
                                            <br/>
                                            <input type="checkbox" name="is_ban" <?= $user['is_ban'] == true ? 'checked':''; ?> style="width:30px; height:30px;" />
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-md-6">   
                                        <div class="mb-3 text-end">
                                            <br/>
                                            <button type="submit" name="updateUser" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">   
                                        <div class="mb-3 text-start">
                                            <br/>
                                            <form action="code.php" method="POST" onsubmit="return confirm('Reset password for this user? This will overwrite the current password.')">
                                                <input type="hidden" name="userId" value="<?= htmlspecialchars($user['id']); ?>">
                                                <button type="submit" name="resetPassword" class="btn btn-warning">Reset Password</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } else {
                                echo '<h5>User not found</h5>';
                            }
                        } catch (PDOException $e) {
                            echo '<h5>Error: '.$e->getMessage().'</h5>';
                        }
                    ?>

                </form>

            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
    // Function to toggle the visibility of the password field
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('passwordField');
        const passwordIcon = document.getElementById('passwordIcon');

        // Check the current type of the password field
        if (passwordField.type === 'password') {
            // If the password is hidden, change the type to text to show the password
            passwordField.type = 'text';
            passwordIcon.classList.remove('fa-eye'); // Remove the eye icon
            passwordIcon.classList.add('fa-eye-slash'); // Add the eye-slash icon to indicate visibility
        } else {
            // If the password is visible, change the type to password to hide it
            passwordField.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash'); // Remove the eye-slash icon
            passwordIcon.classList.add('fa-eye'); // Add the eye icon to indicate hidden password
        }
    }
</script>
