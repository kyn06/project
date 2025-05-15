<?php
session_start();

// Redirect if not logged in or not HR
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'HR') {
    header("Location: authentication/login.php");
    exit;
}

require_once '../Database/database.php'; // Include your Database class

$swal = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $job_title = htmlspecialchars(trim($_POST['job_title']));
    $job_description = htmlspecialchars(trim($_POST['job_description']));
    $job_type = htmlspecialchars(trim($_POST['job_type']));
    $status = htmlspecialchars(trim($_POST['status']));
    
    // Ensure company_id is set in session
    if (!isset($_SESSION['company_id']) || empty($_SESSION['company_id'])) {
        $swal = [
            'icon' => 'error',
            'title' => 'Company ID Missing',
            'text' => 'Unable to find the company ID in your session. Please log in again.'
        ];
        exit();
    }

    $company_id = $_SESSION['company_id']; // Assign company_id from session

    try {
        // Enable error reporting for debugging
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        // Initialize Database
        $database = new Database();
        $conn = $database->getConnection();

        if ($conn === null) {
            throw new Exception("Database connection failed.");
        }

        // Set error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL query
        $sql = "INSERT INTO job_postings 
                    (user_id, company_id, job_title, job_description, job_type, status, posted_at, created_at, updated_at)
                    VALUES 
                    (:user_id, :company_id, :job_title, :job_description, :job_type, :status, NOW(), NOW(), NOW())";

        $stmt = $conn->prepare($sql);

        // Log the query and parameters for debugging
        error_log("Prepared SQL: " . $sql);
        error_log("Parameters: " . print_r([
            ':user_id' => $_SESSION['user_id'],
            ':company_id' => $company_id,
            ':job_title' => $job_title,
            ':job_description' => $job_description,
            ':job_type' => $job_type,
            ':status' => $status
        ], true));

        // Execute the query
        if (!$stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':company_id' => $company_id,
            ':job_title' => $job_title,
            ':job_description' => $job_description,
            ':job_type' => $job_type,
            ':status' => $status
        ])) {
            error_log("Failed to execute query");
            throw new Exception("Failed to insert job posting.");
        }

        // Success
        $swal = [
            'icon' => 'success',
            'title' => 'Job Posting Created!',
            'text' => 'The job posting has been added successfully.'
        ];
    } catch (PDOException $e) {
        // Log the PDO error
        error_log("Database Error: " . $e->getMessage());

        // Provide a user-friendly message
        $swal = [
            'icon' => 'error',
            'title' => 'Database Error',
            'text' => 'An error occurred while saving the job posting. Please try again later.'
        ];

        // Optionally, display the actual error for debugging
        echo "Error: " . $e->getMessage();
        exit();
    } catch (Exception $e) {
        // Log general errors
        error_log("General Error: " . $e->getMessage());

        // Provide a user-friendly message
        $swal = [
            'icon' => 'error',
            'title' => 'Error',
            'text' => $e->getMessage()
        ];

        // Optionally, display the actual error for debugging
        echo "Error: " . $e->getMessage();
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
                Welcome, <?= isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'HR Manager' ?>!
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
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
               <div class="d-flex justify-content-between mb-3">
                <button type="submit" class="btn btn-primary">Create Job Posting</button>
                <a href="HR_Select.php" class="btn btn-secondary">Back to Dashboard</a>
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