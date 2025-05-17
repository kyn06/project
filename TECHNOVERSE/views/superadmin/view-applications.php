<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Super-admin') {
    header("Location: ../error/error404.php");
    exit;
}

require_once '../../config/database.php';
require_once '../../models/Application.php';

$db = new Database();
$conn = $db->getConnection();
Application::setConnection($conn);

try {
    $applications = Application::getAllWithUserBasic();
} catch (Exception $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Applications List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">All Applications</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>Application ID</th>
                <th>Applicant Name</th>
                <th>Email</th>
                <th>Application Date</th>
                <th>Status ID</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= $app['id'] ?></td>
                    <td><?= $app['full_name'] ?></td>
                    <td><?= $app['email'] ?></td>
                    <td><?= $app['application_date'] ?></td>
                    <td><?= $app['status_id'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center mt-3">
        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
