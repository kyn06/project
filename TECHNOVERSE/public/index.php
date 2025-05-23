<?php
session_start();
require_once '../models/Application.php';
require_once '../models/User.php';
require_once '../config/database.php';

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../views/auth/login.php");
    exit();
}

$username = $_SESSION['full_name'] ?? '';
$role = $_SESSION['role'] ?? 'Not Assigned';

// Set up database connection
$db = new Database();
$conn = $db->getConnection();
Application::setConnection($conn);

// Create Application object
$applicationModel = new Application();

// Get applications
$applications = $applicationModel->getApplications();

// Get job postings (new static method)
$jobPostings = Application::getJobPostings();

// Get application stats
$stats = $applicationModel->fetchApplicationStatsByLabel();
$totalApplicants = $stats['total'];
$complete = $stats['complete'];
$inprogress = $stats['inprogress'];
$rejected = $stats['rejected'];

// Get total applications
$totalApplications = $applicationModel->fetchTotalApplications();

// Include appropriate navbar
include User::getNavbarFile();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-cream text-gray-800 font-[Helvetica]">

  <!-- Top Bar -->
  <div class="flex justify-between items-center p-6 bg-white shadow rounded-2xl">
    <div>
      <h1 class="text-3xl font-bold">Dashboard</h1>
      <p class="text-sm text-gray-500"><?= date("l, F j, Y"); ?></p>
    </div>
    <div class="flex items-center gap-2">
      <span class="text-md font-semibold"><?= htmlspecialchars($username); ?> (<?= htmlspecialchars($role); ?>)</span>
      <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M5.121 17.804A13.937 13.937 0 0 1 12 15c2.21 0 4.29.535 6.121 1.481M15 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
      </svg>
    </div>
  </div>

  <!-- Greeting Card -->
  <div class="p-6 sm:p-10">
    <div class="bg-[#FBB72C] rounded-[30px] flex sm:flex-row flex-col overflow-hidden h-[400px] sm:h-[450px]">
      
      <!-- Left Image -->
      <div class="flex items-end justify-center w-full sm:w-1/2">
        <img src="/technoverse/public/assets/images/Picture1.png" 
             alt="Greeting" 
             class="object-contain h-[90%] sm:h-[95%]" />
      </div>

      <!-- Greeting Text -->
      <div class="flex flex-col justify-center text-center sm:text-right sm:p-10 w-full sm:w-1/2">
        <p class="text-6xl sm:text-6xl font-semibold">
          Hello, <span class="font-bold text-white"><?= htmlspecialchars($role . ' ' . $username); ?></span>
        </p>
        <p class="text-3xl sm:text-2xl">
          Monitor system activity and oversee platform operations.
        </p>
      </div>
    </div>
  </div>

  <!-- Overview Cards -->
  <div class="px-6 sm:px-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
    <div class="bg-green-400 rounded-xl text-white p-6 shadow">
      <div class="text-3xl font-bold"><?= $totalApplicants ?? 0; ?></div>
      <div class="text-base mt-2">Applicants</div>
    </div>
    <div class="bg-indigo-500 rounded-xl text-white p-6 shadow">
      <div class="text-3xl font-bold"><?= $complete ?? 0; ?></div>
      <div class="text-base mt-2">Complete</div>
    </div>
    <div class="bg-gray-900 rounded-xl text-white p-6 shadow">
      <div class="text-3xl font-bold"><?= $inprogress ?? 0; ?></div>
      <div class="text-base mt-2">In-progress</div>
    </div>
    <div class="bg-red-500 rounded-xl text-white p-6 shadow">
      <div class="text-3xl font-bold"><?= $rejected ?? 0; ?></div>
      <div class="text-base mt-2">Rejected</div>
    </div>
  </div>

  <!-- Job Postings -->
  <div class="px-6 mt-10">
    <h2 class="text-xl font-semibold mb-4">Job Listings</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if (count($jobPostings) > 0): ?>
        <?php foreach ($jobPostings as $job): ?>
          <a href="../views/jobseeker/js-apply.php?id=<?= $job['id']; ?>" class="block">

            <div class="bg-white rounded-xl shadow p-4">
              <h3 class="text-lg font-bold mb-1"><?= htmlspecialchars($job['job_title']); ?></h3>
              <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($job['job_type']); ?></p>
              <p class="text-sm"><?= nl2br(htmlspecialchars(substr($job['job_description'], 0, 100))) . '...'; ?></p>
              <p class="text-xs text-gray-400 mt-3">Posted at: <?= date("F j, Y", strtotime($job['posted_at'])); ?></p>
              <?php if ($role === 'Job-seeker'): ?>
                <div class="mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                  Apply Now
                </div>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-600 col-span-full">No job postings available at the moment.</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
