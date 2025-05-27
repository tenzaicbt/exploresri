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
      <a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
      <a href="manage_hotels.php"><i class="bi bi-building"></i> Manage Hotels</a>
      <a href="add_hotel.php"><i class="bi bi-plus-circle"></i> Add New Hotel</a> <!-- ✅ NEW BUTTON -->
      <a href="manage_destinations.php"><i class="bi bi-geo-alt"></i> Manage Destinations</a>
      <a href="add_place.php"><i class="bi bi-pin-map"></i> Add New Place</a>
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
          <div class="col-md-3">
            <div class="card text-white bg-primary shadow">
              <div class="card-body">
                <h5 class="card-title">Hotels</h5>
                <p class="card-text">View and manage all hotels.</p>
                <a href="manage_hotels.php" class="btn btn-light btn-sm">Go to Hotels</a>
              </div>
            </div>
          </div>
          <div class="col-md-3">
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
          <div class="col-md-3">
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
          <div class="col-md-3">
            <div class="card text-white bg-info shadow">
              <div class="card-body">
                <h5 class="card-title">Bookings</h5>
                <p class="card-text">Review and control bookings.</p>
                <a href="manage_bookings.php" class="btn btn-light btn-sm">Go to Bookings</a>
              </div>
            </div>
          </div>

          <!-- ✅ Add Hotel Card -->
          <div class="col-md-3">
            <div class="card text-white bg-danger shadow">
              <div class="card-body">
                <h5 class="card-title">Add Hotel</h5>
                <p class="card-text">Create and list a new hotel.</p>
                <a href="add_hotel.php" class="btn btn-light btn-sm">Add Hotel</a>
              </div>
            </div>
          </div>
          <!-- End Add Hotel -->
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>
