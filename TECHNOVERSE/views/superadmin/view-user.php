<?php
require_once('../database/database.php'); // Adjust path if needed

// Get user ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Validate ID
if (empty($id)) {
    die('Invalid User ID');
}

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die('User not found.');
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">User Details</h1>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">User Information</h5>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($user['full_name']) ?></li>
                <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
                <li class="list-group-item"><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></li>
            </ul>
        </div>
        <div class="card-footer text-center">
            <a href="dashboardforcreate.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
