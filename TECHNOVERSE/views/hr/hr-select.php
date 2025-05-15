<?php
session_start();

// Optional: Access check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Super Admin') {
    header("Location: ../error/errorpage.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Form Selector</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            margin-top: 50px;
        }
        .option-card {
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            width: 280px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .option-card h2 {
            font-size: 1.5rem;
        }
        .option-card a {
            margin-top: 10px;
            display: inline-block;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container text-center">
    <h1 class="my-4">Select HR Action</h1>
    
    <div class="container-wrapper">
        <div class="option-card bg-light">
            <h2>HR_Company Form</h2>
            <p>View or manage the HR-related company info</p>
            <a href="hr_Company.php" class="btn btn-primary">Open</a>
        </div>
        <div class="option-card bg-light">
            <h2>Job Post Form</h2>
            <p>Create and update job vacancies</p>
            <a href="HRCreateJobPost.php" class="btn btn-primary">Open</a>
        </div>
        <div class="option-card bg-light">
            <h2>Create Company Form</h2>
            <p>Register a new company profile</p>
            <a href="HR.php" class="btn btn-primary">Open</a>
        </div>
        <div class="option-card bg-light">
            <h2>List of Job Posts</h2>
            <p>View job posts that have been created</p>
            <a href="HRJobDashboard.php" class="btn btn-primary">View List</a>
        </div>
    </div>

    <div class="mt-4">
        <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
