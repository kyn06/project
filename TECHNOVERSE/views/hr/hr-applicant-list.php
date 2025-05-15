<?php
session_start();

// Access control: only HR can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../error/error404.php");
    exit;
}

require_once '../database/database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->query("
        SELECT 
            a.id AS application_id,
            a.application_date,
            a.letter,
            u.full_name,
            u.email,
            jp.job_title,
            jp.job_type,
            jp.job_description
        FROM applications a
        LEFT JOIN users u ON a.user_id = u.id
        LEFT JOIN job_postings jp ON a.job_posting_id = jp.id
        ORDER BY a.application_date DESC
    ");
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
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
                    <td><?= htmlspecialchars($app['application_id']) ?></td>
                    <td><?= htmlspecialchars($app['full_name']) ?></td>
                    <td><?= htmlspecialchars($app['email']) ?></td>
                    <td><?= htmlspecialchars($app['application_date']) ?></td>
                    <td><?= htmlspecialchars($app['job_title']) ?></td>
                    <td><?= htmlspecialchars($app['job_type']) ?></td>
                    <td><?= nl2br(htmlspecialchars($app['job_description'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($app['letter'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center mt-3">
        <a href="HrJobDashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
