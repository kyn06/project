<?php
require_once '../../config/database.php';
require_once '../../models/Application.php'; 
include 'navbar-hr.php'; 

$hr_id = 1;

$db = new Database();
$conn = $db->getConnection();

Application::setConnection($conn);

$applications = Application::getApplications();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appId = $_POST['application_id'];
    $action = $_POST['action'];

    $statusId = $action === 'approve' ? 2 : 3;

    $application = Application::find($appId);

    if ($application) {
        $application->status_id = $statusId;
        $application->updated_at = date('Y-m-d H:i:s');
        if ($application->save()) {
            $stmt = $conn->prepare("INSERT INTO status_history (application_id, status_id, update_date) VALUES (:application_id, :status_id, NOW())");
            $stmt->execute([
                'application_id' => $appId,
                'status_id' => $statusId
            ]);
        }
    }

    header("Location: hr-manage-applications.php");
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
                <tr>
                    <td><?= htmlspecialchars($app['full_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($app['job_title'] ?? 'N/A') ?></td>
                    <td style="max-width: 250px;"><?= nl2br(htmlspecialchars($app['letter'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($app['application_date'] ?? 'N/A') ?></td>
                    <td>
                        <span class="badge 
                            <?= $app['status'] === 'Submitted' ? 'bg-secondary' : 
                               ($app['status'] === 'Approved' ? 'bg-success' : 
                               ($app['status'] === 'Rejected' ? 'bg-danger' : 'bg-secondary')) ?>">
                            <?= htmlspecialchars($app['status'] ?? 'N/A') ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($app['status'] === 'Submitted'): ?>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                                <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">No Action</span>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No applications found.</div>
    <?php endif ?>
</div>
</body>
</html>
