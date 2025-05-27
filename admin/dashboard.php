<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
    }
    .sidebar {
      height: 100vh;
      background-color: #003049;
      padding-top: 1rem;
      color: white;
    }
    .sidebar a {
      color: #ffffff;
      text-decoration: none;
      display: block;
      padding: 12px 20px;
      transition: background-color 0.3s ease;
    }
    .sidebar a:hover {
      background-color: #0077b6;
    }
    .content {
      padding: 2rem;
    }
    .header {
      background-color: #f8f9fa;
      padding: 1rem 2rem;
      border-bottom: 1px solid #ddd;
    }
    .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: none;
      border-radius: 15px;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .btn-light {
      font-weight: bold;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <nav class="col-md-2 sidebar">
      <h4 class="text-center">ExploreSri Admin</h4>
      <a href="admin_profile.php"><i class="bi bi-person-circle"></i> Admin Profile</a>
      <a href="settings.php"><i class="bi bi-gear"></i> Settings</a>
      <a href="user_profiles.php"><i class="bi bi-people"></i> User Profiles</a>
      <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>

    <main class="col-md-10 ms-sm-auto col-lg-10 content">
      <div class="header d-flex justify-content-between align-items-center">
        <h2>Dashboard</h2>
        <span>Welcome, <?= htmlspecialchars($_SESSION['admin']) ?> 👋</span>
      </div>

      <div class="mt-4">
        <div class="row g-4">

          <!-- Manage Hotels -->
          <div class="col-md-4">
            <div class="card text-white bg-primary shadow">
              <div class="card-body">
                <h5 class="card-title">Manage Hotels</h5>
                <p class="card-text">Edit or delete hotels from the list.</p>
                <a href="manage_hotels.php" class="btn btn-light btn-sm">
                  <i class="bi bi-pencil-square"></i> Manage Hotels
                </a>
              </div>
            </div>
          </div>

          <!-- Manage Places -->
          <div class="col-md-4">
            <div class="card text-white bg-success shadow">
              <div class="card-body">
                <h5 class="card-title">Manage Places</h5>
                <p class="card-text">Edit or delete destination places.</p>
                <a href="manage_places.php" class="btn btn-light btn-sm">
                  <i class="bi bi-map"></i> Manage Places
                </a>
              </div>
            </div>
          </div>

          <!-- Bookings -->
          <div class="col-md-4">
            <div class="card text-white bg-info shadow">
              <div class="card-body">
                <h5 class="card-title">Bookings</h5>
                <p class="card-text">Review and control bookings.</p>
                <a href="manage_bookings.php" class="btn btn-light btn-sm">Go to Bookings</a>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>
