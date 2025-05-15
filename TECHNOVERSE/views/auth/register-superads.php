<?php
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $role = htmlspecialchars(trim($_POST['role']));
    $status = htmlspecialchars(trim($_POST['status']));

    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Check if the email already exists
        $checkSql = "SELECT id FROM users WHERE email = :email";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            $message = '<div class="alert alert-danger text-center mt-4">Error: Email already exists!</div>';
        } else {
            // Insert new user
            $sql = "INSERT INTO users (full_name, email, password, phone_number, role, status, created_at, updated_at)
                    VALUES (:full_name, :email, :password, :phone_number, :role, :status, NOW(), NOW())";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':phone_number', $phone_number);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':status', $status);

            $stmt->execute();

            $message = '<div class="alert alert-success text-center mt-4">Registration successful!</div>';
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger text-center mt-4">Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!-- HTML Form for Registration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<style>
    body{
            height: 100vh;
            background-image: url('Technoverse.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
    }
</style>
<body>
    
<body class="d-flex justify-content-center align-items-center">
<div class="card p-4 shadow rounded-4" style="min-width: 300px;">
<div class="container mt-5">
    <h2 class="text-center mb-4">CREATE AN ACCOUNT</h2>

    <?php if (isset($message)) echo $message; ?>

    <form method="POST" action="register.php" class="card p-4 shadow">
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name:</label>
            <input type="text" class="form-control" name="full_name" id="full_name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number:</label>
            <input type="text" class="form-control" name="phone_number" id="phone_number" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <select class="form-select" name="role" id="role" required>
                <option value="">Select Role</option>
                <option value="Super-admin">Super-admin</option>
                <option value="Admin">Admin</option>
                <option value="Job-seeker">Job-seeker</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select class="form-select" name="status" id="status" required>
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="d-grid">
        <button type="submit" class="btn" style="background-color: #0f1035; color: white;">REGISTER</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-QDgHE7vlnMAvV+f24TUNvo5iJ2Bs8LwM5Wm5jK0bV7RbBKMF6Z5b9cW1qapdLda8" crossorigin="anonymous"></script>
</body>
</html>
