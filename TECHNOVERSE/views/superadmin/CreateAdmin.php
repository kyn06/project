<?php
require_once '../Database/database.php';

$swal = null; // Flag to trigger SweetAlert

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = $_POST['role']; // Allow role selection
    $status = $_POST['status']; // Allow status selection
    $phone_number = htmlspecialchars(trim($_POST['phone_number'])); // Get phone number

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
                'title' => 'Email Already Exists',
                'text' => 'An account with this email already exists!'
            ];
        } else {
            $sql = "INSERT INTO users (full_name, email, password, role, status, phone_number, created_at, updated_at)
                    VALUES (:full_name, :email, :password, :role, :status, :phone_number, NOW(), NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':phone_number', $phone_number); // Bind phone number

            $stmt->execute();

            $swal = [
                'icon' => 'success',
                'title' => 'Admin Created!',
                'text' => 'The Admin account was successfully created.'
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
    <title>Create Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex justify-content-center align-items-center" style="height: 100vh;">
<div class="card p-4 shadow rounded-4" style="min-width: 350px;">
    <div class="container mt-3">
        <h2 class="text-center mb-4">Create Admin</h2>

        <form method="POST" action="CreateAdmin.php" class="card p-3 shadow">
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
                <label for="role" class="form-label">Role</label>
                <select class="form-select" name="role" id="role" required>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Create Admin</button>
            </div>
        </form>
    </div>
</div>

<?php if ($swal): ?>
<script>
Swal.fire({
    icon: '<?= $swal['icon'] ?>',
    title: '<?= $swal['title'] ?>',
    text: '<?= $swal['text'] ?>',
    confirmButtonColor: '#3085d6'
}).then((result) => {
    if (result.isConfirmed && '<?= $swal['icon'] ?>' === 'success') {
        window.location.href = 'Dashboardforcreate.php'; // Change this to your target page after success
    }
});
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
