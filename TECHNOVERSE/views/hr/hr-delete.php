<?php
// Only handle deletion if confirmed via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    require_once '../../config/database.php';
    require_once '../../models/JobPost.php';

    // Set DB connection for the model
    $db = new Database();
    JobPost::setConnection($db->getConnection());

    $id = (int)$_POST['id'];
    $success = false;

    try {
        $job = JobPost::find($id);

        if ($job) {
            $success = $job->delete();
        } else {
            $errorMessage = "Job post not found.";
        }
    } catch (PDOException $e) {
        $errorMessage = $e->getMessage();
    }
} else {
    header("Location: hr-job-dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Job</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        <?php if (!empty($success)): ?>
        Swal.fire({
            title: 'Deleted!',
            text: 'The job posting has been successfully deleted.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'hr-job-dashboard.php';
        });
        <?php else: ?>
        Swal.fire({
            title: 'Error!',
            text: '<?= addslashes($errorMessage ?? "Unknown error.") ?>',
            icon: 'error',
            confirmButtonText: 'Back'
        }).then(() => {
            window.location.href = 'hr-job-dashboard.php';
        });
        <?php endif; ?>
    });
</script>
</body>
</html>
