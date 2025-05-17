<?php
session_start();
require_once('../../config/database.php');
require_once('../../Models/User.php');

$db = new Database();
$conn = $db->getConnection();
User::setConnection($conn);

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Super-admin' && $_SESSION['role'] !== 'Admin')) {
    header('Location: ../public/index.php');
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($id) || !is_numeric($id)) {
    die('Invalid User ID');
}

$user = User::find($id);
if (!$user) {
    die('User not found.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->full_name = $_POST['full_name'];
    $user->email = $_POST['email'];
    $user->role = $_POST['role'];
    $user->save();
    
    $_SESSION['update_success'] = true;
    header('Location: superadmin-user-manage.php');
    exit;    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
                <div class="mb-3">
                    <label for="id" class="form-label">User ID:</label>
                    <input type="text" class="form-control" name="id" value="<?= $user->id ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name:</label>
                    <input type="text" class="form-control" name="full_name" value="<?= $user->full_name ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" value="<?= $user->email ?>" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role:</label>
                    <select class="form-select" name="role" required>
                        <option value="Super-admin" <?= $user->role == 'Super-admin' ? 'selected' : '' ?>>Super-admin</option>
                        <option value="Admin" <?= $user->role == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="HR" <?= $user->role == 'HR' ? 'selected' : '' ?>>HR</option>
                        <option value="Job-seeker" <?= $user->role == 'Job-seeker' ? 'selected' : '' ?>>Job-seeker</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="superadmin-user-manage.php" class="btn btn-secondary">Back to Dashboard</a>
                    <input type="submit" class="btn btn-primary" value="Update User">
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>

