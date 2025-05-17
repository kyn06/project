<?php
// Include the database connection class
require_once '../../config/database.php';
require_once '../../models/JobPost.php';

$db = new Database();
$conn = $db->getConnection();

JobPost::setConnection((new Database())->getConnection());

try {
    $jobs = JobPost::all();
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Job Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 text-primary">HR Job Dashboard</h2>

    <?php if (count($jobs) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Company ID</th>
                        <th>Job Title</th>
                        <th>Job Description</th>
                        <th>Job Type</th>
                        <th>Status</th>
                        <th>Posted At</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?= $job->id ?></td>
                            <td><?= $job->user_id ?></td>
                            <td><?= $job->company_id ?></td>
                            <td><?= $job->job_title ?></td>
                            <td><?= $job->job_description ?></td>
                            <td><?= $job->job_type ?></td>
                            <td>
                                <span class="badge bg-<?= $job->status == 1 ? 'success' : 'secondary' ?>">
                                    <?= $job->status == 1 ? 'Open' : 'Closed' ?>
                                </span>
                            </td>
                            <td><?= $job->posted_at ?></td>
                            <td><?= $job->created_at ?></td>
                            <td><?= $job->updated_at ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No job listings found.</div>
    <?php endif; ?>
</div>

</body>
</html>
