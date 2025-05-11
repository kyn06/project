<?php
require_once '../Database/database.php';

$swal = null; // Flag to trigger SweetAlert

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = "Admin"; // Auto-set to Admin
    $status = "Active"; // Default status

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
            $sql = "INSERT INTO users (full_name, email, password, role, status, created_at, updated_at)
                    VALUES (:full_name, :email, :password, :role, :status, NOW(), NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':status', $status);
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

        <form method="POST" action="createAdmin.php" class="card p-3 shadow">
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

            <!-- Hidden fields -->
            <input type="hidden" name="role" value="Admin">
            <input type="hidden" name="status" value="Active">

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
});
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
