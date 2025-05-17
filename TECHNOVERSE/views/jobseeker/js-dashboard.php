<?php
require_once '../../config/database.php';
require_once '../../models/JobPost.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
JobPost::setConnection($conn);

// Fetch all job postings using JobPost model
$jobPostModel = new JobPost();
$jobPosts = $jobPostModel->getJobPosts();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job_id'])) {
    $success = Application::applyJob($_SESSION['user_id'], $_POST['apply_job_id'], $_POST['letter'])
        ? "Application submitted successfully!"
        : "Failed to submit your application.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Job Seeker Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="text-center mb-4">Available Job Postings</h1>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($jobPosts)): ?>
        <?php foreach ($jobPosts as $job): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <strong><?php echo htmlspecialchars($job['job_title']); ?></strong>
                </div>
                <div class="card-body">
                    <p><?php echo nl2br(htmlspecialchars($job['job_description'])); ?></p>
                    <form method="POST" novalidate>
                        <input type="hidden" name="apply_job_id" value="<?php echo (int)$job['id']; ?>" />
                        <div class="form-group">
                            <label for="letter_<?php echo (int)$job['id']; ?>">Cover Letter</label>
                            <textarea 
                                name="letter" 
                                id="letter_<?php echo (int)$job['id']; ?>" 
                                class="form-control" 
                                rows="3" 
                                required 
                                aria-required="true"
                                placeholder="Write your cover letter here..."
                            ></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">No job postings available at this time.</p>
        <p class="text-center"> Go back to <a href="js-home.php">Home</a></p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
