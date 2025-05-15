<?php
// HR.php - Handles Create Company Logic
session_start();

// Redirect if not logged in or not HR
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'HR') {
    header("Location: authentication/login.php");
    exit;
}

require_once '../Database/database.php';
$swal = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $about = htmlspecialchars(trim($_POST['about']));
    $address = htmlspecialchars(trim($_POST['address']));
    $contact_number = htmlspecialchars(trim($_POST['contact_number']));
    $company_size = htmlspecialchars(trim($_POST['company_size']));
    $field = htmlspecialchars(trim($_POST['field']));

    try {
        $database = new Database();
        $conn = $database->getConnection();

        $sql = "INSERT INTO companies 
                (name, about, address, contact_number, company_size, field, created_at, updated_at)
                VALUES 
                (:name, :about, :address, :contact_number, :company_size, :field, NOW(), NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':about' => $about,
            ':address' => $address,
            ':contact_number' => $contact_number,
            ':company_size' => $company_size,
            ':field' => $field
        ]);

        $swal = [
            'icon' => 'success',
            'title' => 'Company Created!',
            'text' => 'The company has been added successfully.'
        ];
    } catch (PDOException $e) {
        error_log("Insert error: " . $e->getMessage());
        $swal = [
            'icon' => 'error',
            'title' => 'Database Error',
            'text' => 'An error occurred while saving the company.'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Company</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-container { margin-left: 250px; padding: 20px; }
    </style>
</head>
<body>
<div class="form-container">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Create Company</a>
            <span class="navbar-text text-white">
                Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>!
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow rounded-4 p-4">
            <h4 class="mb-3">New Company Form</h4>
            <form method="POST" action="HR.php">
                <div class="mb-3">
                    <label for="name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" name="name" id="name" required>
                </div>
                <div class="mb-3">
                    <label for="about" class="form-label">About</label> 
                    <textarea class="form-control" name="about" id="about" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" name="address" id="address" rows="2" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" name="contact_number" id="contact_number" required>
                </div>
                <div class="mb-3">
                    <label for="company_size" class="form-label">Company Size</label>
                    <select class="form-select" name="company_size" id="company_size" required>
                        <option value="" disabled selected>Select Company Size</option>
                        <option value="Micro-enterprises">Micro-enterprises</option>
                        <option value="Small businesses">Small businesses</option>
                        <option value="Medium-sized businesses">Medium-sized businesses</option>
                        <option value="Large businesses">Large businesses</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="field" class="form-label">Field</label>
                    <input type="text" class="form-control" name="field" id="field" required>
                </div>
                <button type="submit" class="btn btn-primary">Create Company</button>
            </form>
        </div>
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