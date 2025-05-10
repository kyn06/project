<?php
// Include the database connection class
require_once '../database/database.php';

// Create an instance of the Database class
$db = new Database();

// Get the PDO connection
$conn = $db->getConnection();

// Prepare and execute the query using PDO
try {
    $stmt = $conn->prepare("SELECT * FROM job_postings");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <td><?= htmlspecialchars($job['id']) ?></td>
                            <td><?= htmlspecialchars($job['user_id']) ?></td>
                            <td><?= htmlspecialchars($job['company_id']) ?></td>
                            <td><?= htmlspecialchars($job['job_title']) ?></td>
                            <td><?= htmlspecialchars($job['job_description']) ?></td>
                            <td><?= htmlspecialchars($job['job_type']) ?></td>
                            <td>
                                <span class="badge bg-<?= $job['status'] === 'Open' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($job['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($job['posted_at']) ?></td>
                            <td><?= htmlspecialchars($job['created_at']) ?></td>
                            <td><?= htmlspecialchars($job['updated_at']) ?></td>
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
