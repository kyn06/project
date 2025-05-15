<?php
session_start();

// Auth check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Super-admin') {
    header("Location: ../error/error404.php");
    exit;
}

require_once '../database/database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    // LEFT JOIN to ensure we get applications even if the user is missing
    $stmt = $conn->query("
        SELECT a.id, a.application_date, a.status_id, u.full_name, u.email
        FROM applications a
        LEFT JOIN users u ON a.user_id = u.id
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
                    <td><?= htmlspecialchars($app['id']) ?></td>
                    <td><?= htmlspecialchars($app['full_name']) ?></td>
                    <td><?= htmlspecialchars($app['email']) ?></td>
                    <td><?= htmlspecialchars($app['application_date']) ?></td>
                    <td><?= htmlspecialchars($app['status_id']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center mt-3">
        <a href="superadminindex.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
