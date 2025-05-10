<?php
// HRCreateJobPost.php - Handles Job Post Creation
session_start();

// Redirect if not logged in or not HR
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../authentication/login.php");
    exit;
}

$swal = null;

// Validate database file path
$databasePath = '../Database/database.php';
if (!file_exists($databasePath)) {
    die("Database connection file not found at $databasePath");
}
require_once $databasePath;

try {
    $database = new Database();
    $conn = $database->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Show DB errors

    // Fetch company_id for the logged-in HR user if not set
    if (!isset($_SESSION['company_id'])) {
        $stmt = $conn->prepare("SELECT id FROM companies WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($company) {
            $_SESSION['company_id'] = $company['id'];
        } else {
            throw new Exception("No company found for this HR user.");
        }
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Sanitize input
        $job_title = htmlspecialchars(trim($_POST['job_title'] ?? ''));
        $job_description = htmlspecialchars(trim($_POST['job_description'] ?? ''));
        $job_type = htmlspecialchars(trim($_POST['job_type'] ?? ''));
        $status = htmlspecialchars(trim($_POST['status'] ?? ''));

        // Validation
        if (empty($job_title) || empty($job_description) || empty($job_type) || empty($status)) {
            throw new Exception("Please fill in all required fields.");
        }

        // INSERT into job_postings
        $sql = "INSERT INTO job_postings (
                    user_id, company_id, job_title, job_description, job_type, status, posted_at, created_at, updated_at
                ) VALUES (
                    :user_id, :company_id, :job_title, :job_description, :job_type, :status, NOW(), NOW(), NOW()
                )";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':company_id' => $_SESSION['company_id'],
            ':job_title' => $job_title,
            ':job_description' => $job_description,
            ':job_type' => $job_type,
            ':status' => $status
        ]);

        $swal = [
            'icon' => 'success',
            'title' => 'Job Posted!',
            'text' => 'The job post has been created successfully.'
        ];
    }
} catch (Exception $e) {
    $swal = [
        'icon' => 'error',
        'title' => 'Error',
        'text' => $e->getMessage()
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Job Posting</title>
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
            <a class="navbar-brand" href="#">Create Job Posting</a>
            <span class="navbar-text text-white">
                Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>!
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow rounded-4 p-4">
            <h4 class="mb-3">New Job Post</h4>
            <form method="POST" action="HRCreateJobPost.php">
                <div class="mb-3">
                    <label for="job_title" class="form-label">Job Title</label>
                    <input type="text" class="form-control" name="job_title" id="job_title" required>
                </div>
                <div class="mb-3">
                    <label for="job_description" class="form-label">Job Description</label>
                    <textarea class="form-control" name="job_description" id="job_description" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="job_type" class="form-label">Job Type</label>
                    <select class="form-select" name="job_type" id="job_type" required>
                        <option value="" disabled selected>Select job type</option>
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Contract">Contract</option>
                        <option value="Internship">Internship</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status" required>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Post Job</button>
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
