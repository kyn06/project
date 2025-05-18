edit-user.php
<?php
session_start();
require_once('../../config/database.php');
require_once('../../Models/User.php');

$db = new Database();
$conn = $db->getConnection();
User::setConnection($conn);

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Super-admin' && $_SESSION['role'] !== 'Admin')) {
    header('Location: ../public/index.php');
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($id) || !is_numeric($id)) {
    die('Invalid User ID');
}

$user = User::find($id);
if (!$user) {
    die('User not found.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->full_name = $_POST['full_name'];
    $user->email = $_POST['email'];
    $user->role = $_POST['role'];
    $user->save();
    
    $_SESSION['update_success'] = true;
    header('Location: superadmin-user-manage.php');
    exit;    
}
include 'navbar-superadmin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit User</title>
  <style>
    body {
      margin: 0;
      font-family: 'Helvetica', sans-serif;
      background-color: #fdf6ec;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #fef8eb;
      padding: 20px 40px;
      color: #100e34;
    }

    .header h1 {
      margin: 0;
      font-size: 28px;
      font-weight: bold;
    }

    .header p {
      margin: 0;
      color: #777;
      font-size: 14px;
    }

    .username {
      font-weight: 600;
      font-size: 14px;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .outer-card {
      background-color: #f4b324;
      padding: 0px 30px;
      border-radius: 28px;
      width: 100%;
      max-width: 420px;
    }

    h2 {
      text-align: center;
      color: white;
      margin-bottom: 24px;
      font-size: 24px;
      font-weight: bold;
    }

    .form-group {
      margin-bottom: 18px;
    }

    input,
    select {
      width: 100%;
      padding: 14px 16px;
      font-size: 15px;
      border: none;
      border-radius: 24px;
      outline: none;
      box-sizing: border-box;
    }

    select option:first-child {
      color: #999;
    }

    .btn-submit {
      width: 100%;
      padding: 12px;
      background-color: #0a0a3c;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 24px;
      cursor: pointer;
      font-size: 15px;
      margin-top: 10px;
    }

    .btn-submit:hover {
      background-color: #07072c;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <div>
      <h1>User Management</h1>
      <p><?php date_default_timezone_set('Asia/Manila'); echo date("l, F j, Y"); ?></p>
    </div>
    <div class="username"><?php echo $username; ?> (<?php echo $role; ?>)</div>
  </div>

  <!-- Edit User Form -->
  <div class="container">
    <div class="outer-card">
      <h2><br>Edit User</h2>
      <form method="POST">
        <div class="form-group">
          <input type="text" name="id" value="<?= $user->id ?>" readonly placeholder="User ID">
        </div>

        <div class="form-group">
          <input type="text" name="full_name" value="<?= $user->full_name ?>" placeholder="Full Name" required>
        </div>

        <div class="form-group">
          <input type="email" name="email" value="<?= $user->email ?>" placeholder="Email Address" required>
        </div>

        <div class="form-group">
          <select name="role" required>
            <option value="Super-admin" <?= $user->role == 'Super-admin' ? 'selected' : '' ?>>Super-admin</option>
            <option value="Admin" <?= $user->role == 'Admin' ? 'selected' : '' ?>>Admin</option>
            <option value="HR" <?= $user->role == 'HR' ? 'selected' : '' ?>>HR</option>
            <option value="Job-seeker" <?= $user->role == 'Job-seeker' ? 'selected' : '' ?>>Job-seeker</option>
          </select>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0px; margin-top: 10px;">
          <button type="submit" class="btn-submit">Update</button>
          <a href="superadmin-user-manage.php" class="btn-submit" style="background-color: #a0762a; text-align: center; text-decoration: none;">Cancel</a>
        </div>
      </form>
    </div>
  </div>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
