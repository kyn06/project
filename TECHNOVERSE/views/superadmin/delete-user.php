<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/User.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Super-admin' && $_SESSION['role'] !== 'Admin')) {
    header('Location: dashboard.php');
    exit;
}

$db = new Database();
User::setConnection($db->getConnection());


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
            window.location.href = 'delete-confirm.php?id=<?= $id ?>';
        } else {
            window.location.href = 'superadmin-user-manage.php';
        }
    });
</script>
</body>
</html>
