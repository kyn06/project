create-admin.php
<?php
require_once '../../config/database.php';
require_once '../../models/User.php';



$swal = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); 
    $role = trim($_POST['role']);
    $status = trim($_POST['status']);
    $phone_number = trim($_POST['phone_number']);

    try {
        $database = new Database();
        $conn = $database->getConnection();
        User::setConnection($conn);

        $existingUser = User::findByEmail($email);

        if ($existingUser) {
            $swal = [
                'icon' => 'error',
                'title' => 'Email Already Exists',
                'text' => 'An account with this email already exists!'
            ];
        } else {
            $user = new User([
                'full_name' => $full_name,
                'email' => $email,
                'password' => $password, 
                'role' => $role,
                'status' => $status,
                'phone_number' => $phone_number,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $user->save();

            $swal = [
                'icon' => 'success',
                'title' => 'Admin Created!',
                'text' => 'The Admin account was successfully created.'
            ];
        }
    } catch (PDOException $e) {
        $swal = [
            'icon' => 'error',
            'title' => 'Database Error',
            'text' => $e->getMessage()
        ];
    }
}

$username = $_SESSION['full_name'] ?? '';
$role = $_SESSION['role'] ?? 'Not Assigned';

include 'navbar-superadmin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Admin</title>
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
      <p><?php date_default_timezone_set('Asia/Manila');
            echo date("l, F j, Y"); ?></p>
    </div>
    <div class="username"><?php echo $username; ?> (<?php echo $role; ?>)</div>
  </div>

  <!-- Create Admin Form -->
  <div class="container">
    <div class="outer-card">
      <h2><br>Create Admin</h2>
      <form method="POST" action="create-admin.php">
        <div class="form-group">
          <input type="text" id="full_name" name="full_name" placeholder="Full Name" required>
        </div>

        <div class="form-group">
          <input type="email" id="email" name="email" placeholder="Email Address" required>
        </div>

        <div class="form-group">
          <input type="password" id="password" name="password" placeholder="Password" required>
        </div>

        <div class="form-group">
          <input type="text" id="phone_number" name="phone_number" placeholder="Phone Number" required>
        </div>

        <input type="hidden" name="role" value="Admin">

        <div class="form-group">
          <select id="status" name="status" required>
            <option value="">Select Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>  
            <div style="display: flex; flex-direction: column; gap: 0px; margin-top: 10px;">
        <button type="submit" class="btn-submit">Create Admin</button>
        <a href="../public/index.php" class="btn-submit" style="background-color: #a0762a; text-align: center; text-decoration: none;">Cancel</a>
        </div>

      </form>
    </div>
  </div>





<?php if ($swal): ?>
<script>
Swal.fire({
    icon: '<?= $swal['icon'] ?>',
    title: '<?= $swal['title'] ?>',
    text: '<?= $swal['text'] ?>',
    confirmButtonColor: '#3085d6'
}).then((result) => {
    if (result.isConfirmed && '<?= $swal['icon'] ?>' === 'success') {
        window.location.href = 'create-admin.php';
    }
});
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>