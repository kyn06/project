<?php
// ASSIGN COMPANY TO HR 
session_start();

require_once '../../config/database.php';
require_once '../../models/CompanyHR.php';
require_once '../../models/User.php';

$db = new Database();
$conn = $db->getConnection();

CompanyHR::setConnection($conn);
User::setConnection($conn);

$message = "";
$user_id = 0;

// Determine which user to assign
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'HR') {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = (int)$_POST['user_id'];
    $company_id = (int)$_POST['company_id'];

    try {
        $companyHR = CompanyHR::find($user_id, 'user_id'); 

        if ($companyHR) {
            $companyHR->update([
                'company_id' => $company_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            // Create a new hr_company record
            $companyHR = new CompanyHR([
                'user_id' => $user_id,
                'company_id' => $company_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $companyHR->save();
        }

        $message = '<div class="alert alert-success">✅ HR successfully assigned to company.</div>';
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">❌ Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!-- ✅ HTML form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Company to HR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title mb-4">Assign Company to HR</h2>

            <?= $message ?>

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
</body>
</html>
