<?php 
$pageTitle = "Register";
include('includes/navbar.php');

// Check if the user is already logged in
if (isset($_SESSION['auth'])) {
    redirect('index.php', 'You are already registered and logged in');
}
?>
<div class="py-5 mt-5 d-flex align-items-center" style="height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>Register</h4>
                    </div>
                    <div class="card-body">

                        <?= alertmessage() ?>

                        <form action="register-code.php" method="POST">

                            <div class="mb-3">
                                <label for="fname">First Name</label>
                                <input type="text" id="fname" name="fname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="lname">Last Name</label>
                                <input type="text" id="lname" name="lname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
