<?php
require_once '../database/database.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $jobId = $_GET['id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM job_postings WHERE id = :id");
        $stmt->bindParam(':id', $jobId, PDO::PARAM_INT);
        $stmt->execute();
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
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
                <h5 class="card-title"><p><strong>Job Title: </strong><?= htmlspecialchars($job['job_title']) ?></h5>
                <p><strong>Job Description:</strong> <?= htmlspecialchars($job['job_description']) ?></p>
                <p><strong>Job Type:</strong> <?= htmlspecialchars($job['job_type']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($job['status']) ?></p>
                <p><strong>Posted At:</strong> <?= htmlspecialchars($job['posted_at']) ?></p>
                <p><strong>Created At:</strong> <?= htmlspecialchars($job['created_at']) ?></p>
                <p><strong>Updated At:</strong> <?= htmlspecialchars($job['updated_at']) ?></p>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-3">Job posting not found.</div>
    <?php endif; ?>
</div>

</body>
</html>
