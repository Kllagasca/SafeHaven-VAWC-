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
                                <label>Barangay (optional)</label>
                                <select name="barangay" class="form-select">
                                    <option value="">Select Barangay</option>
                                    <option value="I-A (Sambat)">I-A (Sambat)</option>
                                    <option value="I-B (City Sub Riverside)">I-B (City Sub Riverside)</option>
                                    <option value="I-C (Bagong Bayan)">I-C (Bagong Bayan)</option>
                                    <option value="II-A (Triangulo/ Guadalupe 2)">II-A (Triangulo/ Guadalupe 2)</option>
                                    <option value="II-B (Guadalupe 1)">II-B (Guadalupe 1)</option>
                                    <option value="II-C (Unson)">II-C (Unson)</option>
                                    <option value="II-D (Bulante)">II-D (Bulante)</option>
                                    <option value="II-E (San Anton)">II-E (San Anton)</option>
                                    <option value="II-F (Villa Rey)">II-F (Villa Rey)</option>
                                    <option value="III-A (Hermanos Belen)">III-A (Hermanos Belen)</option>
                                    <option value="III-B">III-B</option>
                                    <option value="III-C (Labak/De Roma)">III-C (Labak/De Roma)</option>
                                    <option value="III-D (Villongco)">III-D (Villongco)</option>
                                    <option value="III-E">III-E</option>
                                    <option value="III-F (Balagtas)">III-F (Balagtas)</option>
                                    <option value="IV-A">IV-A</option>
                                    <option value="IV-B">IV-B</option>
                                    <option value="IV-C">IV-C</option>
                                    <option value="V-A">V-A</option>
                                    <option value="V-B">V-B</option>
                                    <option value="V-C">V-C</option>
                                    <option value="V-D">V-D</option>
                                    <option value="VI-A (Mavenida)">VI-A (Mavenida)</option>
                                    <option value="VI-B (Sabang Mabini)">VI-B (Sabang Mabini)</option>
                                    <option value="VI-C (Bagong Pook)">VI-C (Bagong Pook)</option>
                                    <option value="VI-D (Lakeside)">VI-D (Lakeside)</option>
                                    <option value="VI-E (YMCA)">VI-E (YMCA)</option>
                                    <option value="VII-A (P.Alcantara)">VII-A (P.Alcantara)</option>
                                    <option value="VII-B">VII-B</option>
                                    <option value="VII-C">VII-C</option>
                                    <option value="VII-D">VII-D</option>
                                    <option value="VII-E">VII-E</option>
                                    <option value="Atisan">Atisan</option>
                                    <option value="Bautista">Bautista</option>
                                    <option value="Concepcion (Bunot)">Concepcion (Bunot)</option>
                                    <option value="Del Remedio (Wawa)">Del Remedio (Wawa)</option>
                                    <option value="Dolores">Dolores</option>
                                    <option value="San Antonio 1 (Balanga)">San Antonio 1 (Balanga)</option>
                                    <option value="San Antonio 2 (Sapa)">San Antonio 2 (Sapa)</option>
                                    <option value="San Bartolome (Matang-ag)">San Bartolome (Matang-ag)</option>
                                    <option value="San Buenaventura (Palakpakin)">San Buenaventura (Palakpakin)</option>
                                    <option value="San Crispin (Lumbangan)">San Crispin (Lumbangan)</option>
                                    <option value="San Cristobal">San Cristobal</option>
                                    <option value="San Diego (Tiim)">San Diego (Tiim)</option>
                                    <option value="San Francisco (Calihan)">San Francisco (Calihan)</option>
                                    <option value="San Gabriel (Butucan)">San Gabriel (Butucan)</option>
                                    <option value="San Gregorio">San Gregorio</option>
                                    <option value="San Ignacio">San Ignacio</option>
                                    <option value="San Isidro (Balagbag)">San Isidro (Balagbag)</option>
                                    <option value="San Joaquin">San Joaquin</option>
                                    <option value="San Jose (Malamig)">San Jose (Malamig)</option>
                                    <option value="San Juan (Putol)">San Juan (Putol)</option>
                                    <option value="San Lorenzo (Saluyan)">San Lorenzo (Saluyan)</option>
                                    <option value="San Lucas 1 (Malinaw)">San Lucas 1 (Malinaw)</option>
                                    <option value="San Lucas 2 (Malinaw)">San Lucas 2 (Malinaw)</option>
                                    <option value="San Marcos (Tikew)">San Marcos (Tikew)</option>
                                    <option value="San Mateo (Imok)">San Mateo (Imok)</option>
                                    <option value="San Miguel (Balatuin)">San Miguel (Balatuin)</option>
                                    <option value="San Nicolas (Mag-ampon)">San Nicolas (Mag-ampon)</option>
                                    <option value="San Pedro">San Pedro</option>
                                    <option value="San Rafael (Buluburan)">San Rafael (Buluburan)</option>
                                    <option value="San Roque (Sambat)">San Roque (Sambat)</option>
                                    <option value="San Vicente">San Vicente</option>
                                    <option value="Santa Ana">Santa Ana</option>
                                    <option value="Santa Catalina (Sandig)">Santa Catalina (Sandig)</option>
                                    <option value="Santa Cruz (Putol)">Santa Cruz (Putol)</option>
                                    <option value="Santa Elena">Santa Elena</option>
                                    <option value="Santa Filomena (Banlagin)">Santa Filomena (Banlagin)</option>
                                    <option value="Santa Isabel">Santa Isabel</option>
                                    <option value="Santa Maria">Santa Maria</option>
                                    <option value="Santa Maria Magdalena (Boe / Kuba)">Santa Maria Magdalena (Boe / Kuba)</option>
                                    <option value="Santa Monica">Santa Monica</option>
                                    <option value="Santa Veronica (Bae)">Santa Veronica (Bae)</option>
                                    <option value="Santiago I (Bulaho)">Santiago I (Bulaho)</option>
                                    <option value="Santiago II (Bulaho)">Santiago II (Bulaho)</option>
                                    <option value="Santisimo Rosario (Balagbag)">Santisimo Rosario (Balagbag)</option>
                                    <option value="Santo Angel (Ilog)">Santo Angel (Ilog)</option>
                                    <option value="Santo Cristo">Santo Cristo</option>
                                    <option value="Santo Niño (Arsum)">Santo Niño (Arsum)</option>
                                    <option value="Soledad (Macopa)">Soledad (Macopa)</option>
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
    $barangay = isset($_POST['barangay']) && $_POST['barangay'] !== '' ? validate($_POST['barangay']) : null;

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query to insert the new user (include barangay if provided)
    if ($barangay !== null) {
        $query = "INSERT INTO users (fname, lname, email, password, role, is_ban, barangay) 
                  VALUES (:fname, :lname, :email, :password, :role, :is_ban, :barangay)";
    } else {
        $query = "INSERT INTO users (fname, lname, email, password, role, is_ban) 
                  VALUES (:fname, :lname, :email, :password, :role, :is_ban)";
    }

    // Prepare the statement using PDO
    $stmt = $pdo->prepare($query);

    // Bind parameters
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':is_ban', $is_ban, PDO::PARAM_INT);
    if ($barangay !== null) {
        $stmt->bindParam(':barangay', $barangay);
    }

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
