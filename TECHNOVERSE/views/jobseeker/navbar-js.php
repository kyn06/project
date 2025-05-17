<?php
// Start session and check if the user is a Job-seeker

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Job-seeker') {
    // Redirect to another page if the user is not a Job-seeker
    header("Location: ../error/error403.php");
    exit;
}
?>

<!-- sidebar.php -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    body {
        margin: 0;
        padding-left: 250px; /* Space for sidebar */
    }

    @media (max-width: 767.98px) {
        body {
            padding-left: 0;
        }

        #sidebarMenu.fixed-sidebar {
            display: none !important;
        }
    }

    .fixed-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 200px;
        background-color: white;
        color: white;
        z-index: 1045; /* below the navbar */
        padding-top: 70px; /* space for navbar */
    }

    .sidebar-link {
        color: #100e34;
        padding: 10px 15px;
        display: block;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .sidebar-link:hover {
        background-color: #fef8eb;
        color: 100e34;
    }

    .navbar-custom {
        background-color: #fef8eb;
        position: fixed;
        width: 100%;
        z-index: 1050;
    }

    .navbar-toggler {
     
    }

    .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 30 30'%3e%3cpath stroke='%23100e34' stroke-width='3' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    .logo{
        font-weight: bold;
        text-decoration: none;
        color: #100e34;
        font-size: 25.5px;
        padding-left: 17px;
    }
    .logo1{
        color: #4f48ec;
    }
    .offcanvas{
        margin-top: 50px;
    }
    .list-unstyled{
        margin-left: 15px;
    }
    .offcanvas-header{
        margin-left: 1px;
    }
</style>

<!-- Navbar with burger toggle -->
<nav class="navbar navbar-dark navbar-custom">
    <div class="container-fluid">
        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- Fixed Sidebar (visible on md and up) -->
<div class="fixed-sidebar d-none d-md-block" id="sidebarMenu">
    <a href="index.php" class="logo"><span class="logo1">Techno</span>verse</a>
    <ul class="list-unstyled mt-4">
        <li><a href="../views/jobseeker/js-home.php" class="sidebar-link"><i class="fas fa-home me-2"></i>Home</a></li>
        <li><a href="../views/jobseeker/js-status.php" class="sidebar-link"><i class="fas fa-clipboard-list me-2"></i>Job Listings</a></li>
        <li><a href="../views/jobseeker/js-apply.php" class="sidebar-link"><i class="fas fa-file-signature me-2"></i>My Applications</a></li>
        <li><a href="#" class="sidebar-link"><i class="fas fa-user me-2"></i>Profile</a></li>
        <li><a href="#" class="sidebar-link"><i class="fas fa-info-circle me-2"></i>About</a></li>
        <li><a href="../views/auth/logout.php" class="sidebar-link" style="color: #ff1d1d;"><i class="fas fa-sign-out-alt me-2"></i><strong>Logout</strong></a></li>
    </ul>
</div>

<!-- Offcanvas Sidebar (for small screens) -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="offcanvasMenu">
    <div class="offcanvas-header">
        <a href="index.php" class="logo"><span class="logo1">Techno</span>verse</a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="list-unstyled">
            <li><a href="../views/jobseeker/js-home.php" class="sidebar-link"><i class="fas fa-home me-2"></i>Home</a></li>
            <li><a href="../views/jobseeker/js-status.php" class="sidebar-link"><i class="fas fa-clipboard-list me-2"></i>Job Listings</a></li>
            <li><a href="../views/jobseeker/js-apply.php" class="sidebar-link"><i class="fas fa-file-signature me-2"></i>My Applications</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-user me-2"></i>Profile</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-info-circle me-2"></i>About</a></li>
            <li><a href="../views/auth/logout.php" class="sidebar-link" style="color: #ff1d1d;"><i class="fas fa-sign-out-alt me-2"></i><strong>Logout</strong></a></li>
        </ul>
    </div>
</div>