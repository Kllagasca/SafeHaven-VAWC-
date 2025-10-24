<?php
  $pageName = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], "/") +1);
?>

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 my-3 fixed-start ms-3" 
       style="background-color: #8368ce; border-radius: 10px;" 
       id="sidenav-main">
<div class="sidenav-header d-flex justify-content-center">
    <a class="navbar-brand m-0 text-center" href="index.php">
        <h4>Researcher Panel</h4>
    </a>
</div>

  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">

  <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link 
        <?= $pageName == 'index.php' ? 'active':''; ?>
        " href="index.php">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-home <?= $pageName == 'index.php' ? 'text-white':'text-dark'; ?> text-lg"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manage Posts</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= $pageName == 'services.php' ? 'active':''; ?> " href="services.php">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-bullhorn <?= $pageName == 'services.php' ? 'text-white':'text-dark'; ?> text-lg"></i>
          </div>
          <span class="nav-link-text ms-1">Posts</span>
        </a>
      </li>

      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manage Documents</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= $pageName == 'documents.php' ? 'active':''; ?> " href="documents.php">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa-solid fa-clipboard-check <?= $pageName == 'documents.php' ? 'text-white':'text-dark'; ?> text-lg"></i>
          </div>
          <span class="nav-link-text ms-1">Documents</span>
        </a>
      </li>
      
    </ul>
  </div>
  <div class="sidenav-footer mx-3 ">
    <a class="btn mt-3 w-100 text-white" style="background-color: #554fb0;"
      href="logout.php">Logout</a>
  </div>
</aside>

<style>
/* Set active box background color */
.nav-link.active {
    background-color: #554fb0 !important; /* Sets the background color for the active state */
    border-radius: 5px; /* Adds rounded corners for a better look */
    color: white !important; /* Ensures text is white on active */
}

.nav-link.active .icon {
    background-color: #554fb0 !important; /* Matches the icon background color */
    color: white !important; /* Changes icon color to white */
}

/* Optional hover effect for active links */
.nav-link.active:hover {
    opacity: 0.9; /* Slight transparency on hover */
}

/* Set active box background color for the icon */
.nav-link.active .icon {
    background-color: #554fb0 !important; /* Sets the background color for the active icon box */
    color: white !important; /* Changes icon color to white */
    border-radius: 5px; /* Adds rounded corners for the icon box */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Optional shadow for a polished look */
}

/* Optional: Hover effect for the active icon box */
.nav-link.active:hover .icon {
    opacity: 0.9; /* Slight transparency on hover for the icon box */
    transform: scale(1.05); /* Enlarge icon box slightly on hover */
    transition: all 0.3s ease; /* Smooth transition for hover effects */
}

/* Change the active icon box color */
.nav-link.active .icon {
    background-color: #554fb0 !important; /* Set violet color */
    color: white !important; /* Change icon color to white for contrast */
    border-radius: 5px; /* Optional: Add rounded corners */
    transition: all 0.3s ease; /* Add smooth transition effect */
}

/* Hover effect for the active icon box */
.nav-link.active:hover .icon {
    opacity: 0.9; /* Slight transparency on hover */
    transform: scale(1.05); /* Slight enlargement on hover */
}

/* Default icon styling (non-active state) */
.nav-link .icon {
    background-color: white !important; /* Default white background */
    color: black !important; /* Default black icon */
    transition: all 0.3s ease; /* Smooth transitions for hover/active state */
}

</style>