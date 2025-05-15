<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not authenticated
    header("Location: authentication/login.php");
    exit;
}



if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Super-admin') {
    // Redirect all non-super-admin users to a separate page
    header("Location: ../error/error404.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #0f1035; /* Dark background color */
            color: white;
            height: 100vh; /* Full height of the screen */
            padding-top: 20px;
            position: fixed;
            left: 0;
        }

        .sidebar h3 {
            color: white;
            padding-bottom: 20px;
        }

        .sidebar a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #575757; /* Highlight color on hover */
        }

        /* Content area should take up remaining space */
        .dashboard-content {
            margin-left: 250px; /* Prevent overlap with sidebar */
            padding: 20px;
            flex-grow: 1;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .card {
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }
        .dashboard-content h1 {
            text-align: center;
        }
      
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include('navbarsuperadmin.php'); ?>

<!-- Main Content -->
<div class="dashboard-content">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Job Application Tracker</a>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8'); ?>!
                </span>
                
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container">
        <h1 class="mb-4">Dashboard</h1>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card text-white bg-primary shadow">
                    <div class="card-body">
                        <h5 class="card-title">Application</h5>
                        <p class="card-text">Total Number of Applications.</p>
                        <a href="viewapplications.php" class="btn btn-light btn-sm">View</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-secondary shadow">
                    <div class="card-body">
                        <h5 class="card-title">IN-PROGRESS</h5>
                        <p class="card-text">Total Number of In-progress Applications.</p>
                        <a href="#" class="btn btn-light btn-sm">View</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-success shadow">
                    <div class="card-body">
                        <h5 class="card-title">COMPLETED</h5>
                        <p class="card-text">Total Number of Completed Applications.</p>
                        <a href="#" class="btn btn-light btn-sm">View</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-danger shadow">
                    <div class="card-body">
                        <h5 class="card-title">REJECTED</h5>
                        <p class="card-text">Total Number of Rejected Applications.</p>
                        <a href="#" class="btn btn-light btn-sm">View</a> 
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-white bg-warning shadow">
                    <div class="card-body">
                        <h5 class="card-title">NEW APPLICATION (24 HOURS)</h5>
                        <p class="card-text">Recent activity within 24 hours.</p>
                        <a href="#" class="btn btn-light btn-sm">View</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-info shadow">
                    <div class="card-body">
                        <h5 class="card-title">NEW JOB POSTING </h5>
                        <p class="card-text">Recent activity of job posting.</p>
                        <a href="#" class="btn btn-light btn-sm">View</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

</body>
</html>
