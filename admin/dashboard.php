
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
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
<style>
  .card-metric {
    position: relative;
    border-radius: 15px;
    text-align: center;
    color: white;
    padding: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
  }

  .card-metric:hover {
    transform: translateY(-5px);
  }

  .circle-progress {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 6px solid rgba(255, 255, 255, 0.2);
    position: relative;
    margin: 0 auto 10px;
  }

  .circle-progress::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 6px solid white;
    top: 0;
    left: 0;
    clip-path: polygon(50% 0%, 100% 0%, 100% 100%, 50% 100%);
    transform: rotate(var(--rotation));
    transform-origin: center;
  }

  .circle-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 16px;
    font-weight: bold;
  }

  .metric-icon {
    font-size: 28px;
    margin-bottom: 10px;
  }
</style>

</head>
<body>
<div class="container-fluid">
  <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
          <div class="position-sticky pt-3">
            <div class="text-white text-center mb-4">
              <h4 class="fw-bold">ExploreSri Admin</h4>
            </div>
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link text-white" href="dashboard.php">
                  <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="admin_profile.php">
                  <i class="bi bi-person-circle me-2"></i> Admin Profile
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="settings.php">
                  <i class="bi bi-gear me-2"></i> Settings
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="user_profiles.php">
                  <i class="bi bi-people me-2"></i> User Profiles
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                  <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
              </li>
            </ul>
          </div>
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
        <div class="card-metric bg-primary">
          <div class="metric-icon"><i class="bi bi-building"></i></div>
          <div class="circle-progress" style="--rotation: <?= $hotel_percent * 3.6 ?>deg;">
            <div class="circle-value"><?= $hotel_percent ?>%</div>
          </div>
          <h5>Total Hotels</h5>
          <h3><?= $total_hotels ?></h3>
          <small><?= $hotel_percent ?>% active listings</small>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card-metric bg-success">
          <div class="metric-icon"><i class="bi bi-journal-bookmark"></i></div>
          <div class="circle-progress" style="--rotation: <?= $booking_percent * 3.6 ?>deg;">
            <div class="circle-value"><?= $booking_percent ?>%</div>
          </div>
          <h5>Bookings</h5>
          <h3><?= $total_bookings ?></h3>
          <small><?= $booking_percent ?>% confirmed</small>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card-metric bg-warning">
          <div class="metric-icon"><i class="bi bi-people-fill"></i></div>
          <div class="circle-progress" style="--rotation: <?= $user_percent * 3.6 ?>deg;">
            <div class="circle-value"><?= $user_percent ?>%</div>
          </div>
          <h5>Users</h5>
          <h3><?= $total_users ?></h3>
          <small><?= $user_percent ?>% verified users</small>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card-metric bg-danger">
          <div class="metric-icon"><i class="bi bi-cash-stack"></i></div>
          <div class="circle-progress" style="--rotation: <?= $revenue_percent * 3.6 ?>deg;">
            <div class="circle-value"><?= $revenue_percent ?>%</div>
          </div>
          <h5>Revenue</h5>
          <h3>Rs. <?= number_format($revenue) ?></h3>
          <small><?= $revenue_percent ?>% of monthly target</small>
        </div>
      </div>
    </div>
    </main>
  </div>
</div>
</body>
</html>
