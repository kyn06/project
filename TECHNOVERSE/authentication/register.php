<?php
session_start();
require_once '../Database/Database.php';
require_once '../Models/User.php';
include '../layout/header.php';

$db = new Database();
User ::setConnection($db->getConnection());
$userController = new User();

$message = ''; // Initialize message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed_roles = ['Job-seeker'];
    
    $existingUser  = User::findByEmail($_POST['email']);
    if ($existingUser ) {
        $message = '<div class="alert alert-danger text-center mt-4">Error! Email is already taken.</div>';
    } else {
        $userData = [
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'phone_number' => $_POST['phone_number'],
            'role' => $_POST['role'],
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!in_array($userData['role'], $allowed_roles)) {
            $message = '<div class="alert alert-danger text-center mt-4">Error! Invalid role selected.</div>';
        } else {
            $newUser  = User::create($userData);

            if ($newUser ) {
                $message = '<div class="alert alert-success text-center mt-4">Success! User has been created.</div>';
            } else {
                $message = '<div class="alert alert-danger text-center mt-4">Error! Failed to create user, try again.</div>';
            }
        }
    }
}
?>

<!-- HTML Form for Registration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
</head>
<style>
    body{
            height: 100vh;
            background-image: url('Technoverse.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
    }
</style>
<body>
    
<body class="d-flex justify-content-center align-items-center">
<div class="card p-4 shadow rounded-4" style="min-width: 300px;">
<div class="container mt-5">
    <h2 class="text-center mb-4">CREATE AN ACCOUNT</h2>

    <!-- Display the message if it exists -->
    <?php if (!empty($message)): ?>
        <?php echo $message; ?>
    <?php endif; ?>

    <form method="POST" action="register.php" class="card p-4 shadow">
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name:</label>
            <input type="text" class="form-control" name="full_name" id="full_name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number:</label>
            <input type="text" class="form-control" name="phone_number" id="phone_number" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <select class="form-select" name="role" id="role" required>
                <option value="">Select Role</option>
                <option value="Job-seeker">Job-seeker</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select class="form-select" name="status" id="status" required>
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="d-grid">
        <button type="submit" class="btn" style="background-color: #0f1035; color: white;">REGISTER</button>
        </div>
    </form>
</div>

</body>
</html>
