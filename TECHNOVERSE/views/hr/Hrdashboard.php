<?php
require_once '../Database/database.php';
session_start();

// Assume company_id is stored in session
$company_id = $_SESSION['company_id'] ?? 1; // fallback to 1 for demo

$db = new Database();
$conn = $db->getConnection();

// --- Handle Profile Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name']);
    $company_description = trim($_POST['company_description']);

    $updateQuery = "UPDATE company SET name = :name, description = :description WHERE id = :id";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->execute([
        ':name' => $company_name,
        ':description' => $company_description,
        ':id' => $company_id
    ]);
}

// --- Fetch Company Profile ---
$companyQuery = "SELECT name, description FROM company WHERE id = :id";
$stmtCompany = $conn->prepare($companyQuery);
$stmtCompany->execute([':id' => $company_id]);
$company = $stmtCompany->fetch(PDO::FETCH_ASSOC);

// Query to get total job posts
$query = "SELECT COUNT(*) AS total_job_posts FROM job_postings";
$stmt = $conn->prepare($query);
$stmt->execute();
$totalJobPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total_job_posts'];

// Query for total number of applications per job post
$queryApplications = "SELECT job_posting_id, COUNT(*) AS total_applications FROM applications GROUP BY job_posting_id";
$stmtApplications = $conn->prepare($queryApplications);
$stmtApplications->execute();
$applications = $stmtApplications->fetchAll(PDO::FETCH_ASSOC);

// Query for the number of completed, in-progress, and rejected applications (based on status_id)
$queryAppStatus = "SELECT status_id, COUNT(*) AS total FROM applications GROUP BY status_id";
$stmtAppStatus = $conn->prepare($queryAppStatus);
$stmtAppStatus->execute();
$appStatus = $stmtAppStatus->fetchAll(PDO::FETCH_ASSOC);

// Query for total pending reviews
$queryPendingReviews = "SELECT COUNT(*) AS pending_reviews FROM review WHERE status = 'pending'";
$stmtPendingReviews = $conn->prepare($queryPendingReviews);
$stmtPendingReviews->execute();
$pendingReviews = $stmtPendingReviews->fetch(PDO::FETCH_ASSOC)['pending_reviews'];

// Query for total approved reviews
$queryApprovedReviews = "SELECT COUNT(*) AS approved_reviews FROM review WHERE status = 'approved'";
$stmtApprovedReviews = $conn->prepare($queryApprovedReviews);
$stmtApprovedReviews->execute();
$approvedReviews = $stmtApprovedReviews->fetch(PDO::FETCH_ASSOC)['approved_reviews'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">HR Dashboard</h1>

        <!-- Company Profile Overview -->
        <div class="card mt-4">
            <div class="card-header">
                Company Profile Overview (Editable)
            </div>
            <div class="card-body">
                <form action="Hrdashboard.php" method="POST">
                    <div class="form-group">
                        <label for="company_name">Company Name:</label>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                               value="<?php echo htmlspecialchars($company['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="company_description">Company Description:</label>
                        <textarea class="form-control" id="company_description" name="company_description" required><?php echo htmlspecialchars($company['description'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Dashboard Stats -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Total Job Posts</div>
                    <div class="card-body">
                        <h4 class="text-center"><?php echo $totalJobPosts; ?></h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Applications Per Job Post</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($applications as $application): ?>
                                <li class="list-group-item">
                                    Job Post ID: <?php echo $application['job_posting_id']; ?> -
                                    Total Applications: <?php echo $application['total_applications']; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Applications by Status ID</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($appStatus as $status): ?>
                                <li class="list-group-item">
                                    Status ID <?php echo $status['status_id']; ?>: <?php echo $status['total']; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending and Approved Reviews -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Total Pending Reviews</div>
                    <div class="card-body">
                        <h4 class="text-center"><?php echo $pendingReviews; ?></h4>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Total Approved Reviews</div>
                    <div class="card-body">
                        <h4 class="text-center"><?php echo $approvedReviews; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
