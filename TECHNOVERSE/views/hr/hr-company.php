<?php
require_once '../Database/database.php';
session_start();

$message = "";

// Check if the user is logged in and has the role of 'HR'
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'HR') {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
} else {
    $user_id = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $company_id = intval($_POST['company_id']);

    $db = new Database();
    $conn = $db->getConnection();

    try {
        // Begin transaction
        $conn->beginTransaction();

        // Insert into hr_company
        $stmt = $conn->prepare("INSERT INTO hr_company (user_id, company_id, created_at, updated_at) VALUES (:user_id, :company_id, NOW(), NOW())");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':company_id', $company_id, PDO::PARAM_INT);
        $stmt->execute();

        // Update users table to reflect company_id for this HR
        $updateStmt = $conn->prepare("UPDATE users SET company_id = :company_id WHERE id = :user_id");
        $updateStmt->bindParam(':company_id', $company_id, PDO::PARAM_INT);
        $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $updateStmt->execute();

        // Commit transaction
        $conn->commit();

        $message = '<div class="alert alert-success">✅ HR linked to company and user updated successfully.</div>';
    } catch (PDOException $e) {
        $conn->rollBack();
        $message = '<div class="alert alert-danger">❌ Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Company to HR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title mb-4">Assign Company to HR</h2>

            <?php if (!empty($message)): ?>
                <?= $message ?>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

                <div class="mb-3">
                    <label class="form-label">User ID (HR)</label>
                    <input type="number" class="form-control" value="<?= htmlspecialchars($user_id) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="company_id" class="form-label">Company ID</label>
                    <input type="number" class="form-control" id="company_id" name="company_id" required>
                </div>

                <button type="submit" class="btn btn-primary">Link HR to Company</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>