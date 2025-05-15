<?php
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Super-admin' && $_SESSION['role'] !== 'Admin')) {
    header('Location: dashboard.php');
    exit;
}

require_once('../database/database.php');

$id = isset($_GET['id']) ? $_GET['id'] : '';

// Show confirmation dialog first
if (!isset($_GET['confirm'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Confirm Deletion</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            title: 'Are you sure?',
            text: "This user will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'delete.php?id=<?= $id ?>&confirm=1';
            } else {
                window.location.href = 'Dashboardforcreate.php';
            }
        });
    </script>
    </body>
    </html>
    <?php
    exit;
}

// Actual deletion
if (empty($id) || !is_numeric($id)) {
    die('Invalid User ID');
}

$db = new Database();
$conn = $db->getConnection();

try {
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
        die("You cannot delete your own account.");
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die('User not found.');
    }

    $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $deleteStmt->bindParam(':id', $id);
    $deleteStmt->execute();

    $_SESSION['delete_success'] = 'User successfully deleted.';
    header('Location: Dashboardforcreate.php');
    exit;

} catch (PDOException $e) {
    echo "Deletion failed: " . $e->getMessage();
}
