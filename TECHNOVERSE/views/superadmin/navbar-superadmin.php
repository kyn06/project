<!-- sidebar.php -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

<style>
    body {
        margin: 0;
    }

    .offcanvas-start {
        background-color: #0f1035;
        color: white;
    }

    .sidebar-link {
        color: white;
        padding: 10px 15px;
        display: block;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .sidebar-link:hover {
        background-color: #1a1a4f;
        color: #ffffff;
    }

    .navbar-custom {
        background-color: #0f1035;
    }

    .navbar-toggler {
        border-color: white;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
</style>

<!-- Navbar with burger toggle -->
<nav class="navbar navbar-dark navbar-custom">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand ms-3" href="#">Job App Tracker</a>
    </div>
</nav>

<!-- Sidebar content (offcanvas) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="list-unstyled">
        <li><a href="/Job_application_tracker/index.php" class="sidebar-link">🏠 HOME</a></li><br>
            <li><a href="/JOB_APPLICATION_TRACKER/Superadmin/Superadminindex.php" class="sidebar-link">⚒ SUPER ADMIN DASHBOARDdddd</a></li><br>
            <li><a href="Dashboardforcreate.php" class="sidebar-link">🔎 USERS</a></li> <br>
            <li><a href="index.php" class="sidebar-link">🔎 ABOUT</a></li> <br>
            <li><a href="/JOB_APPLICATION_TRACKER/authentication/logout.php" class="sidebar-link"> <strong> LOGOUT </strong></a></li> <br>
        </ul>
    </div>
</div>
