<?php
require_once '../../config/database.php';
require_once '../../models/Application.php';
require_once '../../models/JobPost.php';

$db = new Database();
$conn = $db->getConnection();

Application::setConnection($conn);
JobPost::setConnection($conn);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobId = $_POST['job_id'] ?? null;
    $letter = trim($_POST['letter'] ?? '');

    if (!$jobId) {
        $error = "Job ID is required.";
    } elseif (empty($letter)) {
        $error = "Cover letter cannot be empty.";
    } else {
        $applied = Application::applyJob($userId, $jobId, $letter);

        if ($applied) {
            $success = "Application submitted successfully!";
        } else {
            $error = "Failed to submit application. Please try again.";
        }
    }
}

if (empty($_GET['id'])) {
  echo "<div class='alert alert-danger'>Job ID not specified. Please go back and select a job.</div>";
  exit;
}

$jobId = $_GET['id'];

$job = JobPost::find($jobId);

if (!$job) {
    die("Job not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Apply for Job</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.2/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="mb-4">Application Form</h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form action="apply.php?id=<?php echo htmlspecialchars($jobId); ?>" method="POST">
    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($jobId); ?>">

    <div class="mb-3">
      <label class="form-label">Company Name</label>
      <input type="text" class="form-control" value="Technoverse" disabled>
    </div>

    <div class="mb-3">
      <label class="form-label">Job Title</label>
      <input type="text" class="form-control" value="<?php echo htmlspecialchars($job->job_title); ?>" disabled>
    </div>

    <div class="mb-3">
      <label class="form-label">Job Description</label>
      <textarea class="form-control" rows="4" disabled><?php echo htmlspecialchars($job->job_description); ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Job Type</label>
      <input type="text" class="form-control" value="<?php echo htmlspecialchars($job->job_type); ?>" disabled>
    </div>

    <div class="mb-3">
      <label for="application_date" class="form-label">Application Date</label>
      <input type="date" name="application_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
    </div>

    <div class="mb-3">
      <label for="letter" class="form-label">Cover Letter</label>
      <textarea name="letter" class="form-control" rows="5" placeholder="Write your cover letter..." required></textarea>
    </div>

    <input type="hidden" name="status" value="in-progress">

    <button type="submit" class="btn btn-primary">Submit Application</button>
    <a href="../index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.2/dist/sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  <?php if ($success): ?>
    Swal.fire({
      icon: 'success',
      title: 'Application Submitted',
      text: '<?php echo htmlspecialchars($success); ?>',
      confirmButtonText: 'OK',
      willClose: () => {
        window.location.href = '../index.php';
      }
    });
  <?php endif; ?>
</script>

</body>
</html>
