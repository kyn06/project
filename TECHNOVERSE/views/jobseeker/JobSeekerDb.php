<?php
require_once '../Database/database.php';
session_start();

// Simulated logged-in user ID
$userId = 1; // Replace this with actual session user_id

$db = new Database();
$conn = $db->getConnection();

// Fetch all job postings
$stmt = $conn->prepare("SELECT id, job_title, job_description FROM job_postings");
$stmt->execute();
$jobPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job_id'])) {
    $jobId = $_POST['apply_job_id'];
    $letter = $_POST['letter'];

    $stmt = $conn->prepare("INSERT INTO applications 
        (user_id, job_posting_id, application_date, status_id, letter, created_at, updated_at) 
        VALUES (?, ?, CURDATE(), ?, ?, NOW(), NOW())");
    
    // Assuming status_id 1 = "Pending"
    $stmt->execute([$userId, $jobId, 1, $letter]);

    $success = "Application submitted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Seeker Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="text-center mb-4">Available Job Postings</h1>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php foreach ($jobPosts as $job): ?>
        <div class="card mb-4">
            <div class="card-header">
                <strong><?php echo htmlspecialchars($job['job_title']); ?></strong>
            </div>
            <div class="card-body">
                <p><?php echo nl2br(htmlspecialchars($job['job_description'])); ?></p>
                <form method="POST">
                    <input type="hidden" name="apply_job_id" value="<?php echo $job['id']; ?>">
                    <div class="form-group">
                        <label for="letter_<?php echo $job['id']; ?>">Cover Letter</label>
                        <textarea name="letter" id="letter_<?php echo $job['id']; ?>" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Apply</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
