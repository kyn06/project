superadmin-index.php
<?php
session_start();

$username = $_SESSION['full_name'] ?? '';
$role = $_SESSION['role'] ?? 'Not Assigned';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not authenticated
    header("Location: ../views/auth/login.php");
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
            margin: 0;
            font-family: 'Helvetica', sans-serif;
            background: #fdf6ec;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fef8eb;
            padding: 20px 40px;
            color: #100e34;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        .username {
            font-weight: 600;
            font-size: 14px;
        }

        @media (max-width: 940px) {
            .greeting-card {
                flex-direction: column;
                align-items: center;
                padding: 0px 15px;
                text-align: center;
            }

            .greeting-card img {
                margin-bottom: 10px;
            }

            .greeting-text h2,
            .greeting-text p {
                display: none;
            }
        }

        .greeting-card {
            display: flex;
            align-items: center;
            background-color: #FBB72C;
            height: 294.3px;
            margin: 30px 40px;
            padding: 40px;
            border-radius: 48px;
        }

        .greeting-card img {
            width: 300px;
            height: 300px;
            margin-bottom: 5px;
            margin-right: 30px;
            object-fit: cover;
        }

        .greeting-text {
            color: #fff;
        }

        .greeting-text h2 {
            font-size: 36px;
            margin: 0;
        }

        .greeting-text p {
            font-size: 20px;
            margin-top: 15px;
        }

        /* Overview Cards */
        .overview-section {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin: 20px 40px;
        }

        .card-overview {
            flex: 1;
            min-width: 200px;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            color: white;
        }

        .card-overview i {
            font-size: 28px;
            margin-right: 15px;
        }

        .bg-applications { background-color: #f4b324; }
        .bg-complete { background-color: #4f48ec; }
        .bg-inprogress { background-color: #100e34; }
        .bg-rejected { background-color: #b95f3b; }

        /* Notification Card */
        .notification-card {
            background-color: #fcecc7;
            border-radius: 16px;
            padding: 20px;
            margin: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-size: 14px;
            color:  #747081;
        }

        .notification-card i {
            color: #FBC02D;
            font-size: 18px;
            margin-right: 10px;
        }

        .notification-left {
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .notification-date {
            color:  #747081;
            font-size: 12px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include('navbar-superadmin.php'); ?>

<!-- Main Content -->
<div style="background-color:  #fdf6ec;">
    <div class="header">
        <div>
            <h1>Dashboard</h1>
            <p><?php echo date("l, F j, Y"); ?></p>
        </div>
        <div class="username"><i class="fa-solid fa-user-tie"></i> <?php echo $username; ?> (<?php echo $role; ?>)</div>
    </div>

    <!-- Greeting Section -->
    <div class="greeting-card">
        <img src="/TECHNOVERSE/public/assets/images/js-index.png" alt="Greeting" />
        <div class="greeting-text">
            <h2>Hello, <b><?php echo htmlspecialchars($role); ?> <?php echo htmlspecialchars($username); ?></b></h2>
            <p style="color: black;">Monitor system activity and oversee platform operations.</p>
        </div>
    </div>

    <!-- Overview Section -->
    <p style="margin: 0 40px 10px 40px; color: #747081;">Overview</p>
    <div class="overview-section">
        <div class="card-overview bg-applications">
            <i class="fa-solid fa-user"></i>
            <div>
                <div style="font-size: 24px; font-weight: bold;">560</div>
                <div>Applications</div>
            </div>
        </div>

        <div class="card-overview bg-complete">
            <i class="fa-solid fa-check-circle"></i>
            <div>
                <div style="font-size: 24px; font-weight: bold;">560</div>
                <div>Complete</div>
            </div>
        </div>

        <div class="card-overview bg-inprogress">
            <i class="fa-solid fa-spinner"></i>
            <div>
                <div style="font-size: 24px; font-weight: bold;">560</div>
                <div>In-progress</div>
            </div>
        </div>

        <div class="card-overview bg-rejected">
            <i class="fa-solid fa-times-circle"></i>
            <div>
                <div style="font-size: 24px; font-weight: bold;">560</div>
                <div>Rejected</div>
            </div>
        </div>
    </div>

    <!-- 24-Hour Notification -->
    <p style="margin: 20px 40px 10px 40px; color: #747081;">Recent Activity</p>
    <div class="notification-card">
        <div class="notification-left">
            <i class="fa-solid fa-bell"></i>
            [HR Name] posted a new job application
        </div>
        <div class="notification-date">May 17, 2025</div>
    </div>
    <!-- User Overview Section -->
<p style="margin: 30px 40px 10px 40px; color: #747081;">Users</p>
<div class="overview-section">
    <div class="card-overview bg-applications">
        <i class="fa-solid fa-user-check"></i>
        <div>
            <div style="font-size: 24px; font-weight: bold;">15</div>
            <div>Active Users</div>
        </div>
    </div>

    <div class="card-overview bg-rejected">
        <i class="fa-solid fa-user-slash"></i>
        <div>
            <div style="font-size: 24px; font-weight: bold;">5</div>
            <div>Inactive Users</div>
        </div>
    </div>

    <div class="card-overview bg-inprogress">
        <i class="fa-solid fa-user-shield"></i>
        <div>
            <div style="font-size: 24px; font-weight: bold;">2</div>
            <div>Super-admins</div>
        </div>
    </div>

    <div class="card-overview bg-complete">
        <i class="fa-solid fa-user-gear"></i>
        <div>
            <div style="font-size: 24px; font-weight: bold;">6</div>
            <div>Admins</div>
        </div>
    </div>

    <div class="card-overview bg-applications">
        <i class="fa-solid fa-user-tie"></i>
        <div>
            <div style="font-size: 24px; font-weight: bold;">7</div>
            <div>HR Users</div>
        </div>
    </div>
</div>

</div>

</body>
</html>