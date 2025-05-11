<?php
include '../layouts/header.php';
require '../../config/database.php';
require '../../models/User.php';

session_start([
    'cookie_lifetime' => 86400,
]);

//Create database connection
$database = new Database();
$db = $database->getConnection();

// Set database connection for User model
User::setConnection($db);

//Redirect if user is already logged in
if (isset($_SESSION['email'])) {
    header('Location: ../../public/index.php');
    exit;
}

//Handle form submission
$login_failed = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (User ::login($email, $password)) {
        header('Location: ../../public/index.php');
        exit;
    }else{
        $login_failed = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Technoverse</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Helvetica', sans-serif;
            background: #ffffff;
        }
        .login-wrapper {
            display: flex;
            height: 100vh;
        }
        .login-left {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-left h1 {
            color: #0f1035;
            font-weight: bold;
        }
        .login-left p {
            color: #555;
        }
        .message-container {
            margin-bottom: 70px; 
            text-align: center;
        }
        
        .login-form input {
            margin-bottom: 15px;
            border-radius: 20px;
            border: 3px solid #100e34;
            padding: 10px 20px;
            width: 460px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .login-form button {
            border-radius: 20px;
            padding: 10px;
            background-color: #0f1035;
            color: white;
            border: none;
            width: 460px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .register-container {
            display: flex;
            justify-content: center; 
            margin-top: 20px;
            font-size: 14px;
        }

        .register-container a {
            color: #ffbf18;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            
        }

        .login-right {
            flex: 0 0 auto;
            background-color: #ffbf18;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 550px; 
            height: 690px;
            margin-top:30px;
            margin-right: 90px; 
            box-sizing: border-box; 
            border-radius: 48px; 
        }

        h2 {
            font-size: 36.4px;
        }
        h1{
            font-family: 'Helvetica', sans-serif;
            font-size: 28.3px;
            position: absolute;
            top: 0;
            left: 0;
            right: 45%;
            margin-left: 100px;
            margin-top: 20px;
        }
        .des {
            font-size: 14.2px;
        }
        .character-image {
            width: 100%; 
            height: auto; 
            max-width: 600px; 
        }
    </style>
</head>
<body>

<div class="login-wrapper">
             <h1><span style="color: #4f48ec;"><b>Techno</span>verse</b></h1>
    <div class="login-left">
        
        <!-- Message Container -->
        <div class="message-container">
            <h2><strong>Welcome back!</strong></h2>
            <p class="des">Track jobs, stay updated, and grow your career —<br>all in one place with <strong>Technoverse</strong>.</p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="login.php" class="login-form">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <div class="register-container">
            <p>Don't have an account? <a href="../auth/register.php">Register now</a></p>
        </div>
    </div>

    <!-- RIGHT SIDE: Image -->
    <div class="login-right">
        <img src="../../public/assets/images/logo.png" alt="Character with Laptop" class="character-image">
    </div>
</div>

<?php if ($login_failed) : ?>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: 'Invalid email or password.',
            confirmButtonColor: '#3085d6'
        });
    });
</script>
<?php endif; ?>


<?php require_once '../views/layouts/footer.php'; ?>

</body>
</html>
