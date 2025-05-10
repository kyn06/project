<?php
session_start();  // Start the session to access session variables

// Check if the user is logged in and has the necessary session variables
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Not Assigned';

require_once 'database/database.php'; // Make sure this path is correct

$db = new Database();
$conn = $db->getConnection();

// Fetch job posts
try {
    $stmt = $conn->prepare("SELECT * FROM job_postings ORDER BY posted_at DESC");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error retrieving jobs: " . $e->getMessage();
    $jobs = [];
}
?>

<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cream text-gray-800 font-sans">

  <!-- Top Bar -->
  <div class="flex justify-between items-center p-6 bg-white shadow">
    <div>
      <h1 class="text-3xl font-bold">Dashboard</h1>
      <p class="text-sm text-gray-500"><?php echo date("l, F j, Y"); ?></p>
    </div>
    <div class="flex items-center gap-2">
      <span class="text-md font-semibold"><?php echo $username; ?> (<?php echo $role; ?>)</span>
      <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.535 6.121 1.481M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
    </div>
  </div>

  <!-- Greeting Card -->
  <div class="p-6">
    <div class="bg-[#FBB72C] rounded-2xl flex items-center p-8 shadow-md">
      <img src="/projectdb/assets/pictures/Picture1.png" alt="greeting-img" class="w-41 h-42 mr-8" />
      <div>
        <p class="text-5xl font-semibold">
          Hello, <span class="font-bold text-white"><?php echo $role . ' ' . $username; ?></span>
        </p>
        <p class="text-2xl mt-5">Monitor system activity and oversee platform operations.</p>
      </div>
    </div>
  </div>

  <!-- Overview Cards -->
  <div class="px-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-yellow-400 rounded-xl text-white p-4 shadow">
      <div class="text-2xl font-bold">560</div>
      <div class="text-sm mt-1">Applicants</div>
    </div>
    <div class="bg-indigo-500 rounded-xl text-white p-4 shadow">
      <div class="text-2xl font-bold">560</div>
      <div class="text-sm mt-1">Complete</div>
    </div>
    <div class="bg-gray-900 rounded-xl text-white p-4 shadow">
      <div class="text-2xl font-bold">560</div>
      <div class="text-sm mt-1">In-progress</div>
    </div>
    <div class="bg-red-500 rounded-xl text-white p-4 shadow">
      <div class="text-2xl font-bold">560</div>
      <div class="text-sm mt-1">Rejected</div>
    </div>
  </div>

  <!-- Job Postings Section -->
  <div class="px-6 mt-10">
    <h2 class="text-xl font-semibold mb-4">Job Listings</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if (count($jobs) > 0): ?>
        <?php foreach ($jobs as $job): ?>
          <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-lg font-bold mb-1"><?php echo htmlspecialchars($job['job_title']); ?></h3>
            <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($job['job_type']); ?></p>
            <p class="text-sm"><?php echo nl2br(htmlspecialchars(substr($job['job_description'], 0, 100))) . '...'; ?></p>
            <p class="text-xs text-gray-400 mt-3">Posted at: <?php echo date("F j, Y", strtotime($job['posted_at'])); ?></p>

            <?php if ($role === 'Job-seeker'): ?>
              <a href="apply.php?id=<?php echo $job['id']; ?>" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                Apply Now
              </a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-600 col-span-full">No job postings available at the moment.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Placeholder Sections -->
  <div class="px-6 mt-10">
    <h3 class="text-md font-semibold mb-2">Recent Activity</h3>
    <div class="bg-yellow-100 h-16 rounded-xl mb-4"></div>  
    <div class="bg-yellow-100 h-16 rounded-xl"></div>
  </div>

  <div class="px-6 mt-8 mb-12">
    <h3 class="text-md font-semibold mb-2">Users</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
      <div class="bg-purple-100 h-24 rounded-xl"></div>
      <div class="bg-purple-100 h-24 rounded-xl"></div>
      <div class="bg-purple-100 h-24 rounded-xl"></div>
    </div>
  </div>

</body>
</html>
