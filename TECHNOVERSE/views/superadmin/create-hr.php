<?php
require_once '../Database/database.php';

$swal = null;

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

        // Check if email already exists
        $checkSql = "SELECT id FROM users WHERE email = :email";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            $swal = [
                'icon' => 'error',
                'title' => 'Email Exists',
                'text' => 'The email address is already registered.'
            ];
        } else {
            // Insert new HR user
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

            $swal = [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'HR account created successfully!'
            ];
        }
    } catch (PDOException $e) {
        $swal = [
            'icon' => 'error',
            'title' => 'Database Error',
            'text' => $e->getMessage()
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create HR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow rounded-4" style="min-width: 350px;">
    <div class="container mt-3">
        <h2 class="text-center mb-4">Create HR</h2>

        <form method="POST" action="createHR.php" class="card p-3 shadow">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name:</label>
                <input type="text" class="form-control" name="full_name" id="full_name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address:</label>
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
                <input type="text" class="form-control" name="role" id="role" value="HR" readonly>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select class="form-select" name="status" id="status" required>
                    <option value="">Select Status</option>
                    <option value="Activated">Activated</option>
                    <option value="Deactivated">Deactivated</option>
                </select>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Create HR</button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert Display -->
<?php if ($swal): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const swalIcon = <?= json_encode($swal['icon']) ?>;
        const swalTitle = <?= json_encode($swal['title']) ?>;
        const swalText = <?= json_encode($swal['text']) ?>;

        Swal.fire({
            icon: swalIcon,
            title: swalTitle,
            text: swalText,
            confirmButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed && swalIcon === 'success') {
                window.location.href = 'Dashboardforcreate.php';
            }
        });
    });
</script>
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
