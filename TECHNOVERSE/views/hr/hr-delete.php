<?php
// Only handle deletion if confirmed via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    require_once '../database/database.php';

    $id = $_POST['id'];

    $db = new Database();
    $conn = $db->getConnection();

    try {
        $stmt = $conn->prepare("DELETE FROM job_postings WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $success = true;
    } catch (PDOException $e) {
        $success = false;
        $errorMessage = $e->getMessage();
    }
} else {
    // If no POST data, fallback or redirect
    header("Location: HrJobDashboard.php");
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
                window.location.href = 'HrJobDashboard.php';
            });
        <?php else: ?>
            Swal.fire({
                title: 'Error!',
                text: '<?= addslashes($errorMessage ?? "Unknown error.") ?>',
                icon: 'error',
                confirmButtonText: 'Back'
            }).then(() => {
                window.location.href = 'HrJobDashboard.php';
            });
        <?php endif; ?>
    });
</script>

</body>
</html>
