<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Application.php';

// Access control: only HR can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../error/error404.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

Application::setConnection($conn);

$applications = Application::getAllWithUserAndJobDetails();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Applications - HR View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Job Applications (HR View)</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>Application ID</th>
                <th>Applicant Name</th>
                <th>Email</th>
                <th>Application Date</th>
                <th>Job Title</th>
                <th>Job Type</th>
                <th>Job Description</th>
                <th>Letter</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= $app['application_id'] ?></td>
                    <td><?= $app['full_name'] ?></td>
                    <td><?= $app['email'] ?></td>
                    <td><?= $app['application_date'] ?></td>
                    <td><?= $app['job_title'] ?></td>
                    <td><?= $app['job_type'] ?></td>
                    <td><?= nl2br($app['job_description']) ?></td>
                    <td><?= nl2br($app['letter']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center mt-3">
        <a href="hr-dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
