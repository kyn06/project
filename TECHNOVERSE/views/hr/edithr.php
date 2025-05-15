<?php
// Include the database connection class
require_once '../database/database.php';

// Create an instance of the Database class
$db = new Database();
$conn = $db->getConnection();

// Fetch job data based on ID
if (!isset($_GET['id'])) {
    die("Job ID not provided.");
}

$id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM job_postings WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        die("Job not found.");
    }
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Update job if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_title = $_POST['job_title'];
    $job_description = $_POST['job_description'];
    $job_type = $_POST['job_type'];
    $status = $_POST['status'];

    try {
        $updateStmt = $conn->prepare("
            UPDATE job_postings 
            SET job_title = :job_title,
                job_description = :job_description,
                job_type = :job_type,
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ");
        $updateStmt->bindParam(':job_title', $job_title);
        $updateStmt->bindParam(':job_description', $job_description);
        $updateStmt->bindParam(':job_type', $job_type);
        $updateStmt->bindParam(':status', $status);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $updateStmt->execute();

        // Redirect back to same page with success flag
        header("Location: edithr.php?id=$id&updated=1");
        exit;
    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Job Posting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 text-success">Edit Job Posting</h2>

    <form method="POST">
        <!-- Job Title -->
        <div class="mb-3">
            <label for="job_title" class="form-label">Job Title</label>
            <input type="text" name="job_title" id="job_title" class="form-control" value="<?= htmlspecialchars($job['job_title']) ?>" required>
        </div>

        <!-- Job Description -->
        <div class="mb-3">
            <label for="job_description" class="form-label">Job Description</label>
            <textarea name="job_description" id="job_description" class="form-control" rows="4" required><?= htmlspecialchars($job['job_description']) ?></textarea>
        </div>

        <!-- Job Type -->
        <div class="mb-3">
            <label for="job_type" class="form-label">Job Type</label>
            <select name="job_type" id="job_type" class="form-select" required>
                <?php
                $jobTypes = ['Full-time', 'Part-time', 'Gig'];
                foreach ($jobTypes as $type): ?>
                    <option value="<?= $type ?>" <?= $job['job_type'] === $type ? 'selected' : '' ?>>
                        <?= $type ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="Open" <?= $job['status'] === 'Open' ? 'selected' : '' ?>>Open</option>
                <option value="Closed" <?= $job['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <a href="HrJobDashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Job</button>
        </div>
    </form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Job post updated successfully.',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    }).then(() => {
        // Clean URL: remove ?updated=1 from address bar
        window.history.replaceState({}, document.title, window.location.pathname + "?id=<?= $id ?>");
    });
</script>
<?php endif; ?>

</body>
</html>
