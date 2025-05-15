<?php
session_start(); // Start the session to access the logged-in user's info

// Check if the user is logged in and has the Super-admin or Admin role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Super-admin' && $_SESSION['role'] !== 'Admin')) {
    // Redirect to dashboard if not a Super-admin or Admin
    header('Location: dashboard.php');
    exit;
}

require_once('../database/database.php'); // Adjust path if needed

// Get user ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Validate ID
if (empty($id) || !is_numeric($id)) {
    die('Invalid User ID');
}

$db = new Database();
$conn = $db->getConnection();

try {
    // Prepare the query to fetch user details from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user is found in the database
    if (!$user) {
        die('User not found.');
    }

    // Handle form submission for editing user
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        // Update user data
        $updateStmt = $conn->prepare("UPDATE users SET full_name = :full_name, email = :email, role = :role WHERE id = :id");
        $updateStmt->bindParam(':full_name', $full_name);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->bindParam(':role', $role);
        $updateStmt->bindParam(':id', $id);
        $updateStmt->execute();

        // Redirect after update
        header('Location: Dashboardforcreate.php');
        exit;
    }

} catch (PDOException $e) {
    // Handle any errors related to the database query
    echo "Query failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Edit User</h1>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">Edit User Information</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <!-- Display User ID (read-only) -->
                <div class="mb-3">
                    <label for="id" class="form-label">User ID:</label>
                    <input type="text" class="form-control" name="id" value="<?= htmlspecialchars($user['id']) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name:</label>
                    <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role:</label>
                    <select class="form-select" name="role" required>
                        <option value="Super-admin" <?= $user['role'] == 'Super-admin' ? 'selected' : '' ?>>Super-admin</option>
                        <option value="Admin" <?= $user['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="HR" <?= $user['role'] == 'HR' ? 'selected' : '' ?>>HR</option>
                        <option value="Job-seeker" <?= $user['role'] == 'Job-seeker' ? 'selected' : '' ?>>Job-seeker</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="dashboardforcreate.php" class="btn btn-secondary">Back to Dashboard</a>
                    <input type="submit" class="btn btn-primary" value="Update User">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
