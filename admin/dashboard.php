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
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <nav class="col-md-2 sidebar">
      <h4 class="text-center">ExploreSri Admin</h4>
      <a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
      <a href="manage_hotels.php"><i class="bi bi-building"></i> Manage Hotels</a>
      <a href="manage_destinations.php"><i class="bi bi-geo-alt"></i> Manage Destinations</a>
      <a href="manage_bookings.php"><i class="bi bi-calendar-event"></i> Manage Bookings</a>
      <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>

    <main class="col-md-10 ms-sm-auto col-lg-10 content">
      <div class="header d-flex justify-content-between align-items-center">
        <h2>Dashboard</h2>
        <span>Welcome, <?= htmlspecialchars($_SESSION['admin']) ?> 👋</span>
      </div>
      <div class="mt-4">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card text-white bg-primary shadow">
              <div class="card-body">
                <h5 class="card-title">Hotels</h5>
                <p class="card-text">View and manage all hotels.</p>
                <a href="manage_hotels.php" class="btn btn-light btn-sm">Go to Hotels</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card text-white bg-success shadow">
              <div class="card-body">
                <h5 class="card-title">Destinations</h5>
                <p class="card-text">Manage tourist destinations.</p>
                <a href="manage_destinations.php" class="btn btn-light btn-sm">Go to Destinations</a>
              </div>
            </div>
          </div>
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
