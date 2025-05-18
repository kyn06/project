<?php
require_once '../../config/database.php';
require_once '../../models/Application.php'; // Make sure the path is correct
include 'navbar.php'; // Include the navbar

// Simulated HR login
$hr_id = 1;

$db = new Database();
$conn = $db->getConnection();

Application::setConnection($conn); // Set DB connection for the model
$applications = Application::getApplications(); // Use Application model to get applications

// --- ✅ Handle Approve/Reject Form ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appId = $_POST['application_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    $statusId = $action === 'approve' ? 2 : 3; // Status IDs

    // Update application status
    $update = $conn->prepare("UPDATE applications SET status_id = :status_id, updated_at = NOW() WHERE id = :id");
    $update->execute([
        'status_id' => $statusId,
        'id' => $appId
    ]);

    // Log status change
    $history = $conn->prepare("INSERT INTO status_history (application_id, status_id, update_date) VALUES (:application_id, :status_id, NOW())");
    $history->execute([
        'application_id' => $appId,
        'status_id' => $statusId
    ]);

    header("Location: manage-applications.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Applications (HR)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Manage Applications</h2>

    <?php if ($applications): ?>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Applicant</th>
                    <th>Job Title</th>
                    <th>Letter</th>
                    <th>Application Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        <tbody>
            
    <?php foreach ($applications as $app): ?>
        <pre><?php print_r($app); ?></pre>

        <tr>
            <td><?= $app -> full_name ?? 'N/A' ?></td>
            <td><?= $app -> job_title ?? 'N/A' ?></td>
            <td style="max-width: 250px;"><?= nl2br($app ->letter  ?? '') ?></td>
            <td><?= $app -> application_date ?? 'N/A' ?></td>
            <td><span class="badge bg-secondary"><?= $app -> status ?? 'N/A' ?></span></td>
            <td>
                <?php if (($app->status ?? '') === '1'): ?>
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="application_id" value="<?= $app->id ?>">
                        <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                        <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                <?php else: ?>
                    <span class="text-muted">No Action</span>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
    <?php var_dump($app->status); ?>
    </tbody>

        </table>
    <?php else: ?>
        <div class="alert alert-info">No applications found.</div>
    <?php endif ?>
</div>
</body>
</html>
