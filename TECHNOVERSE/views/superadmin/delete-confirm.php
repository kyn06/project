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
    <title>Delete User</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
if ($id > 0) {
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Action Denied',
                text: 'You cannot delete your own account.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'superadmin-user-manage.php';
            });
        </script>";
        exit;
    }

    $user = User::find($id);

    if (!$user) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'User Not Found',
                text: 'The specified user does not exist.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'superadmin-user-manage.php';
            });
        </script>";
        exit;
    }

    if ($user->delete()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'User has been successfully deleted.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'superadmin-user-manage.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Deletion Failed',
                text: 'There was a problem deleting the user.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'superadmin-user-manage.php';
            });
        </script>";
    }
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid ID',
            text: 'The user ID is invalid.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'superadmin-user-manage.php';
        });
    </script>";
}
?>
</body>
</html>
