<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Company.php';
require_once '../../models/JobPost.php';
require_once '../../models/Application.php';
require_once '../../models/Review.php';

$db = new Database();
$conn = $db->getConnection();

Company::setConnection($conn);
JobPost::setConnection($conn);
Application::setConnection($conn);
Review::setConnection($conn);

$company_id = $_SESSION['company_id'] ?? 1;

$companyModel = new Company();
$jobPostModel = new JobPost();
$applicationModel = new Application();
$reviewModel = new Review();

// Handle POST update for company profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyModel->updateCompanyProfile(
        $company_id,
        trim($_POST['company_name']),
        trim($_POST['company_description'])
    );

    // Set session flag for success alert
    $_SESSION['profile_updated'] = true;

    // Redirect to avoid form resubmission
    header("Location: hr-dashboard.php");
    exit();
}

// Fetch data for display
$company = $companyModel->getCompanyProfile($company_id);
$totalJobPosts = $jobPostModel->getTotalJobPosts();
$applications = $applicationModel->getApplicationsCountPerJobPost();
$appStatus = $applicationModel->getApplicationsCountByStatus();
$pendingReviews = $reviewModel->getPendingReviewsCount();
$approvedReviews = $reviewModel->getApprovedReviewsCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>HR Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
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
                <form id="companyProfileForm" action="hr-dashboard.php" method="POST">
                    <div class="form-group">
                        <label for="company_name">Company Name:</label>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                               value="<?php echo htmlspecialchars($company['name'] ?? ''); ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="company_description">Company Description:</label>
                        <textarea class="form-control" id="company_description" name="company_description" required><?php echo htmlspecialchars($company['about'] ?? ''); ?></textarea>
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

    <!-- SweetAlert confirmation on form submit -->
    <script>
        document.getElementById('companyProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Confirm Update',
                text: "Are you sure you want to update the company profile?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        });
    </script>

    <?php if (isset($_SESSION['profile_updated']) && $_SESSION['profile_updated']): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Updated!',
            text: 'Company profile has been successfully updated.',
            confirmButtonColor: '#3085d6'
        });
    </script>
    <?php unset($_SESSION['profile_updated']); endif; ?>
</body>
</html>
