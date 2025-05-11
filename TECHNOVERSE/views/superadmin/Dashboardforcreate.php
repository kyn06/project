<?php
require_once('../database/database.php'); // Adjust path if needed

$db = new Database();
$conn = $db->getConnection();

$users = [];
try {
    $stmt = $conn->query("SELECT * FROM users"); // Ensure you have a 'users' table
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Superadmin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f2f2f2;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto 40px auto;
            background-color: #fff;
        }
        table th, table td {
            border: 1px solid #999;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #555;
            color: white;
        }
        .container-wrapper {
            display: flex;
            gap: 40px;
        }
        .container {
            background-color: #e3e3e3;
            padding: 20px;
            width: 300px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 2px 2px 10px #aaa;
        }
        .container a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php include('navbarsuperadmin.php'); ?>

    <h1>Users Dashboard</h1>

    <?php if (count($users) > 0): ?>
        <table>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Email</th>
                <th>ACTION</th>
                <!-- Add other columns as needed -->
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                            <a class="btn btn-primary" href="view.php?id=<?= $row['id'] ?>">View</a>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                            <button class="btn btn-danger" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
                        </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>

    <div class="container-wrapper">
        <div class="container">
            <h2>Create Admin</h2>
            <a href="CreateAdmin.php">Go to Admin Creation</a>
        </div>
        <div class="container">
            <h2>Create HR</h2>
            <a href="CreateHR.php">Go to HR Creation</a>
        </div>
    </div>

</body>
</html>
