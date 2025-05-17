<?php
//JOB DETAILS
session_start();
require_once '../../config/database.php';
require_once '../../models/jobpost.php'; // Adjust path as needed

$db = new Database();
$conn = $db->getConnection();

$job = null;

if (isset($_GET['id'])) {
    $jobId = $_GET['id'];
    $job = JobPost::find($jobId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Job Details</h2>
    <a href="HrJobDashboard.php" class="btn btn-secondary">Back to Dashboard</a>

    <?php if ($job): ?>
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title"><strong>Job Title: </strong><?= $job->job_title ?></h5>
                <p><strong>Job Description:</strong> <?= $job->job_description ?></p>
                <p><strong>Job Type:</strong> <?= $job->job_type ?></p>
                <p><strong>Status:</strong> <?= $job->status ?></p>
                <p><strong>Posted At:</strong> <?= $job->posted_at ?></p>
                <p><strong>Created At:</strong> <?= $job->created_at ?></p>
                <p><strong>Updated At:</strong> <?= $job->updated_at ?></p>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-3">Job posting not found.</div>
    <?php endif; ?>
</div>

</body>
</html>
