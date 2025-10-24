<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Add User
                    <a href="users.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

                <form action="code.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>First Name</label>
                                <input type="text" name="fname" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Last Name</label>
                                <input type="text" name="lname" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" required>
                            </div>
                        </div>


                        <div class="col-md-6">
                        <div class="mb-3">
                            <label>Password</label>
                            <div class="input-group position-relative">
                                <input type="password" name="password" id="passwordField" class="form-control pe-5" required>
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePasswordVisibility()" style="cursor: pointer;">
                                    <i class="fa fa-eye" id="passwordIcon"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Select Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="fperson">Focal Person</option>
                                    <option value="researcher">Researcher</option>
                                 </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Is Ban</label>
                                <br/>
                                <input type="checkbox" name="is_ban" style="width:30px; height:30px;" />
                            </div>
                        </div>

                        <div class="col-md-6">   
                            <div class="mb-3 text-end">
                                <br/>
                                <button type="submit" name="saveUser" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php
require_once '../config/function.php'; // Validation functions

if (isset($_POST['saveUser'])) {
    // Collect and validate form data
    $fname = validate($_POST['fname']);
    $lname = validate($_POST['lname']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $role = validate($_POST['role']);
    $is_ban = isset($_POST['is_ban']) ? 1 : 0; // Convert checkbox value to 1 or 0

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query to insert the new user
    $query = "INSERT INTO users (fname, lname, email, password, role, is_ban) 
              VALUES (:fname, :lname, :email, :password, :role, :is_ban)";

    // Prepare the statement using PDO
    $stmt = $pdo->prepare($query);

    // Bind parameters
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':is_ban', $is_ban, PDO::PARAM_INT);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Redirect with success message
        redirect('users.php', 'User added successfully.');
    } else {
        // Redirect with error message
        redirect('users.php', 'Failed to add user.');
    }
}
?>


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
