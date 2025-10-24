<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <title>SafeHaven</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <link rel="shortcut icon" href="assets/img/logo.png" type="image/png">
  <meta name="theme-color" content="#7c2aa6">
  <!-- Include Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      font-family: 'Poppins', sans-serif;
    }
    /* Custom hover effect for navbar links */
    .navbar-nav .nav-link {
      position: relative;
      margin-right: 20px; /* Add space between navbar links */

     
    }


    .navbar-nav {
      align-items: center;
    }


    .navbar-brand {
            color: white;
            font-family: 'Poppins', sans-serif; /* Apply Poppins font */
            font-size: 20px;
            font-weight: 200; /* Adjust weight for emphasis */
            text-decoration: none; /* Optional: remove underline */
        }


  </style>
</head>
<body>
<?php
require_once 'config/function.php';
?>
<nav class="navbar navbar-expand-lg shadow-sm sticky-top" id="navigationBar" style="background-color: #7c2aa6">
  <div class="container">
  <img src="assets/img/logo.png" alt="Logo" style="width: 50px; margin-right:10px">
  <a class="navbar-brand" style="color: white; display: flex; flex-direction: column; align-items: left; text-decoration: none;">
  <div style="font-weight: bold; font-size: 20px; line-height: 1;">SafeHaven</div>
  <div style="font-weight: 300px; font-size: 15px; color: white; margin-top: 0;">Honey Bunch Sugar Plum</div>
</a>




    <!-- Navbar Toggler Button -->


    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" >
      <span class="navbar-toggler-icon"></span>
    </button>




    <!-- Navbar Collapse -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mt-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" style="color: white;" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" style="color: white;" href="about-us.php">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" style="color: white;" href="contact.php">Contact Us</a>
        </li>


    <?php if (isset($_SESSION['fname'])): ?>
              
        <li class="nav-item text-capitalize">
        <?php
          $role = $_SESSION['role']; // Get the user role from the session
          $fname = htmlspecialchars($_SESSION['fname']); // Sanitize the user's first name
    
          // Determine the redirect URL based on the user's role
          switch ($role) {
              case 'admin':
                  $redirectUrl = 'admin/index.php';
                  break;
              case 'fperson':
                  $redirectUrl = 'focal-person/index.php'; // Change to the appropriate path for focal person
                  break;
              case 'researcher':
                  $redirectUrl = 'researcher/index.php'; // Change to the appropriate path for researcher
                  break;
              case 'user':
                  $redirectUrl = 'user-dashboard.php'; // Change to the appropriate path for user
                  break;
              default:
                  $redirectUrl = 'default/index.php'; // Fallback URL if role is not recognized
                  break;
          }
        ?>
        </li>
    <?php endif; ?>

<?php if (isset($fname)): ?>
    <li class="nav-item">
        <a class="nav-link" style="color:  white;;" href="<?php echo $redirectUrl; ?>">
            Hello, <?php echo $fname; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="btn ms-2" style="background-color: white;" href="logout.php">
            Logout
        </a>
    </li>
<?php else: ?>
    <li class="nav-item">
        <a href="login.php" class="btn ms-2" style="background-color: white; color: #7c2aa6;">
            Login/Signup
        </a>
    </li>
<?php endif; ?>





      </ul>
    </div>
  </div>
</nav>




<!-- Include Bootstrap JavaScript -->
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</script>
</body>
</html>