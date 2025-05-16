<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Application.php';
include 'navbar-js.php'; 

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Only allow Job-seeker role
if ($_SESSION['role'] !== 'Job-seeker') {
    header("Location: ../errors/error.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$email = $_SESSION['email'];

Application::setConnection($conn);
$appModel = new Application();
$applications = $appModel::getApplicationStatusByEmail($email);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">My Job Applications</h2>
    
    <?php if (count($applications) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Applicant</th>
                    <th>Email</th>
                    <th>Application ID</th>
                    <th>Job Title</th>
                    <th>Application Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?= htmlspecialchars($app['full_name']) ?></td>
                        <td><?= htmlspecialchars($app['email']) ?></td>
                        <td><?= htmlspecialchars($app['application_id']) ?></td>
                        <td><?= htmlspecialchars($app['job_title'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($app['application_date']) ?></td>
                        <td><?= htmlspecialchars($app['status_label'] ?? 'Pending') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">You haven't submitted any applications yet.</p>
    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="../../public/index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
