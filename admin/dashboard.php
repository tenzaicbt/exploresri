
<?php
include '../config/db.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$hotel_stmt = $conn->query("SELECT COUNT(*) as total FROM hotels");
$total_hotels = $hotel_stmt->fetch()['total'];

$active_hotel_stmt = $conn->query("SELECT COUNT(*) as active FROM hotels WHERE status='active'");
$active_hotels = $active_hotel_stmt->fetch()['active'];

$hotel_percent = $total_hotels > 0 ? round(($active_hotels / $total_hotels) * 100) : 0;

$booking_stmt = $conn->query("SELECT COUNT(*) as total FROM booking");
$total_bookings = $booking_stmt->fetch()['total'];

$confirmed_booking_stmt = $conn->query("SELECT COUNT(*) as confirmed FROM booking WHERE status='confirmed'");
$confirmed_bookings = $confirmed_booking_stmt->fetch()['confirmed'];

$booking_percent = $total_bookings > 0 ? round(($confirmed_bookings / $total_bookings) * 100) : 0;

$user_stmt = $conn->query("SELECT COUNT(*) as total FROM users");
$total_users = $user_stmt->fetch()['total'];

$verified_stmt = $conn->query("SELECT COUNT(*) as verified FROM users WHERE is_verified=1");
$verified_users = $verified_stmt->fetch()['verified'];

$user_percent = $total_users > 0 ? round(($verified_users / $total_users) * 100) : 0;

$revenue_stmt = $conn->query("SELECT SUM(amount) as total_revenue FROM payments WHERE status='paid'");
$revenue = $revenue_stmt->fetch()['total_revenue'] ?? 0;
$monthly_target = 300000;
$revenue_percent = $monthly_target > 0 ? round(($revenue / $monthly_target) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; }
    .sidebar { height: 100vh; background-color: #003049; padding-top: 1rem; color: white; }
    .sidebar a { color: #ffffff; text-decoration: none; display: block; padding: 12px 20px; transition: background-color 0.3s ease; }
    .sidebar a:hover { background-color: #0077b6; }
    .content { padding: 2rem; }
    .header { background-color: #f8f9fa; padding: 1rem 2rem; border-bottom: 1px solid #ddd; }
    .card { transition: transform 0.3s ease, box-shadow 0.3s ease; border: none; border-radius: 15px; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
    .btn-light { font-weight: bold; }
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
      <div class="row mt-4">
        <div class="col-md-3">
          <div class="card text-white bg-primary">
            <div class="card-body">
              <h5>Total Hotels</h5>
              <h3><?= $total_hotels ?></h3>
              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-light" style="width: <?= $hotel_percent ?>%;"></div>
              </div>
              <small><?= $hotel_percent ?>% active listings</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-success">
            <div class="card-body">
              <h5>Bookings</h5>
              <h3><?= $total_bookings ?></h3>
              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-light" style="width: <?= $booking_percent ?>%;"></div>
              </div>
              <small><?= $booking_percent ?>% confirmed</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-warning">
            <div class="card-body">
              <h5>Users</h5>
              <h3><?= $total_users ?></h3>
              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-dark" style="width: <?= $user_percent ?>%;"></div>
              </div>
              <small><?= $user_percent ?>% verified users</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-danger">
            <div class="card-body">
              <h5>Revenue</h5>
              <h3>Rs. <?= number_format($revenue) ?></h3>
              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-light" style="width: <?= $revenue_percent ?>%;"></div>
              </div>
              <small><?= $revenue_percent ?>% of monthly target</small>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>
