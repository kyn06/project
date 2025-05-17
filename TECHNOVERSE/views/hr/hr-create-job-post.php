<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/JobPost.php'; // Ensure this path is correct

// Redirect if not logged in or not HR
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../views/auth/login.php");
    exit;
}

$swal = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // No htmlspecialchars() — using raw input (you can add custom sanitization if needed)
    $job_title = trim($_POST['job_title']);
    $job_description = trim($_POST['job_description']);
    $job_type = trim($_POST['job_type']);
    $status_raw = trim($_POST['status']);

    // Translate status string to numeric value (you may adjust according to your DB)
    $status = strtolower($status_raw) === 'open' ? 1 : 0;

    if (!isset($_SESSION['company_id']) || empty($_SESSION['company_id'])) {
        $swal = [
            'icon' => 'error',
            'title' => 'Company ID Missing',
            'text' => 'Unable to find the company ID in your session. Please log in again.'
        ];
        exit();
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();
        JobPost::setConnection($conn);

        $jobPost = new JobPost([
            'user_id' => $_SESSION['user_id'],
            'company_id' => $_SESSION['company_id'],
            'job_title' => $job_title,
            'job_description' => $job_description,
            'job_type' => $job_type,
            'status' => $status,
            'posted_at' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $jobPost->save();

        $swal = [
            'icon' => 'success',
            'title' => 'Job Posting Created!',
            'text' => 'The job posting has been added successfully.'
        ];
    } catch (Exception $e) {
        error_log("Error creating job post: " . $e->getMessage());

        $swal = [
            'icon' => 'error',
            'title' => 'Error',
            'text' => 'Failed to create job posting. Please try again.'
        ];

        echo "Error: " . $e->getMessage(); // You can remove this line in production
        exit();
    }
}
?>

<!-- HTML BELOW UNCHANGED -->
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
                Welcome, <?= isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'HR Manager' ?>
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow rounded-4 p-4">
            <h4 class="mb-3">New Job Posting Form</h4>
            <form method="POST" action="HRCreateJobPost.php">
                <div class="mb-3">
                    <label for="job_title" class="form-label">Job Title</label>
                    <input type="text" class="form-control" name="job_title" id="job_title" required>
                </div>
                <div class="mb-3">
                    <label for="job_description" class="form-label">Job Description</label>
                    <textarea class="form-control" name="job_description" id="job_description" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="job_type" class="form-label">Job Type</label>
                    <select class="form-select" name="job_type" id="job_type" required>
                        <option value="" disabled selected>Select Job Type</option>
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Freelance">Freelance</option>
                        <option value="Internship">Internship</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status" required>
                        <option value="" disabled selected>Select Status</option>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
               <div class="d-flex justify-content-between mb-3">
                <button type="submit" class="btn btn-primary">Create Job Posting</button>
                <a href="hr-select.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>

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