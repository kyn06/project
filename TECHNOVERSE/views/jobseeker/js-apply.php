<?php
require_once '../../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$success = ''; // Initialize success message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = 1; // Replace with actual logged-in user ID (e.g., from session)
    $jobId = $_POST['job_id'];
    $applicationDate = $_POST['application_date'];
    $statusId = 1; // Assuming 1 means "in-progress", adjust if necessary
    $letter = $_POST['letter'];
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO applications (user_id, job_posting_id, application_date, status_id, letter, created_at, updated_at)
            VALUES (:user_id, :job_posting_id, :application_date, :status_id, :letter, NOW(), NOW())
        ");
        
        $stmt->execute([
            'user_id' => $userId,
            'job_posting_id' => $jobId,
            'application_date' => $applicationDate,
            'status_id' => $statusId,
            'letter' => $letter
        ]);
        
        // Set the success message
        $success = "Application submitted successfully!";
        
    } catch (PDOException $e) {
        $success = "Application failed: " . $e->getMessage();
    }
}

// Fetch job details for form display
if (!isset($_GET['id'])) {
    die("Job ID not specified.");
}

$jobId = $_GET['id'];

try {
    $stmt = $conn->prepare("
        SELECT job_title, job_description, job_type
        FROM job_postings
        WHERE id = :jobId
    ");
    $stmt->execute(['jobId' => $jobId]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        die("Job not found.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Apply for Job</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Include SweetAlert2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.2/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="mb-4">Application Form</h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>

  <form action="apply.php?id=<?php echo htmlspecialchars($jobId); ?>" method="POST">
    <!-- Hidden Job ID -->
    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($jobId); ?>">

    <!-- Company Name (hardcoded) -->
    <div class="mb-3">
      <label class="form-label">Company Name</label>
      <input type="text" class="form-control" value="Technoverse" disabled>
    </div>

    <!-- Job Title -->
    <div class="mb-3">
      <label class="form-label">Job Title</label>
      <input type="text" class="form-control" value="<?php echo htmlspecialchars($job['job_title']); ?>" disabled>
    </div>

    <!-- Job Description -->
    <div class="mb-3">
      <label class="form-label">Job Description</label>
      <textarea class="form-control" rows="4" disabled><?php echo htmlspecialchars($job['job_description']); ?></textarea>
    </div>

    <!-- Job Type -->
    <div class="mb-3">
      <label class="form-label">Job Type</label>
      <input type="text" class="form-control" value="<?php echo htmlspecialchars($job['job_type']); ?>" disabled>
    </div>

    <!-- Application Date -->
    <div class="mb-3">
      <label for="application_date" class="form-label">Application Date</label>
      <input type="date" name="application_date" class="form-control" required>
    </div>

    <!-- Cover Letter -->
    <div class="mb-3">
      <label for="letter" class="form-label">Cover Letter</label>
      <textarea name="letter" class="form-control" rows="5" placeholder="Write your cover letter..." required></textarea>
    </div>

    <!-- Status (hidden) -->
    <input type="hidden" name="status" value="in-progress">

    <button type="submit" class="btn btn-primary">Submit Application</button>
    <a href="../index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.2/dist/sweetalert2.min.js"></script>

<script>
  <?php if ($success): ?>
    Swal.fire({
      icon: 'success',
      title: 'Application Submitted',
      text: '<?php echo htmlspecialchars($success); ?>',
      confirmButtonText: 'OK',
      willClose: () => {
        window.location.href = '../index.php'; // Redirect after confirmation
      }
    });
  <?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
