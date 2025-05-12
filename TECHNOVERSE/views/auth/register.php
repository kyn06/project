<?php
session_start();
include '../layouts/header.php';
require '../../config/database.php';
require '../../models/User.php';

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
    * {
      box-sizing: border-box;
      font-family: 'Helvetica', sans-serif;
      margin: 0;
      padding: 0;
    }

    body, html {
      height: 100%;
      background-color: #ffffff;
    }
    body.fade-out {
        opacity: 0;
        transition: opacity 0.5s ease-out;
    }

    .register-wrapper {
      display: flex;
      height: 100vh;
    }

    h1 {
      font-size: 28.3px;
      font-family: 'Helvetica', sans-serif;
      position: absolute;
      top: 0;
      left: 0;
      right: 45%;
      margin-left: 100px;
      margin-top: 20px;
    }

    h1 span {
      color: #4f48ec;
    }

    .register-left {
      flex: 1;
      padding: 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .register-left h2 {
      color: #0f1035;
      font-size: 36.4px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .register-left p.des {
      color: #555;
      font-size: 14.2px;
      margin-bottom: 40px;
    }
    .register-form input {
      margin-bottom: 15px;
      border-radius: 20px;
      border: 3px solid #100e34;
      padding: 10px 20px;
      width: 460px;
      display: block;
      margin-left: auto;
      margin-right: auto;
      font-size: 14px;
    }

    .register-form button {
      border-radius: 20px;
      padding: 10px;
      background-color: #0f1035;
      color: white;
      border: none;
      width: 460px;
      display: block;
      margin-left: auto;
      margin-right: auto;
      font-size: 14px;
      cursor: pointer;

      transition-property: transform;
      transition-duration: 0.3s;
      transition-timing-function: ease;
      transition-delay: 0s;
    }
    .register-form button:hover {
      transform: translateY(-2px);
    }
    .login-container {
      display: flex;
      justify-content: center;
      margin-top: 20px;
      font-size: 14px;
    }

    .login-container a {
      color: #ffbf18;
      text-decoration: none;
      font-weight: bold;
    }

    .register-right {
      flex: 0 0 auto;
      background-color: #ffbf18;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 550px;
      height: 690px;
      margin-top: 30px;
      margin-right: 90px;
      border-radius: 48px;
    }

    .character-image {
      width: 100%;
      max-width: 600px;
      height: auto;
    }
    
  </style>
<body>

<div class="register-wrapper">
  <h1><span><b>Techno</span>verse</b></h1>
  <div class="register-left">
    <h2 align="center">Create Account</h2>
    <p align="center" class="des">Create your profile and apply with ease.</p>

    <form method="POST" action="register.php" class="register-form">
      <input type="text" name="full_name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="phone_number" placeholder="Phone Number" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>

    <div class="login-container">
      <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>

  <div class="register-right">
    <img src="../../public/assets/images/logo.png" alt="Character Illustration" class="character-image">
  </div>
</div>

</body>
</html>
