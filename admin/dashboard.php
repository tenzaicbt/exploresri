
<?php
include '../config/db.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

try {
    $hotel_stmt = $conn->query("SELECT COUNT(*) as total FROM hotels");
    $total_hotels = $hotel_stmt->fetch()['total'];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $total_hotels = 0;
}


$total_hotel_stmt = $conn->query("SELECT COUNT(*) as total FROM hotels");
$total_hotels = $total_hotel_stmt->fetch()['total'];

$active_hotel_stmt = $conn->query("SELECT COUNT(*) as active FROM hotels WHERE status='active'");
$active_hotels = $active_hotel_stmt->fetch()['active'];
$hotel_percent = $total_hotels > 0 ? round(($active_hotels / $total_hotels) * 100) : 0;

$booking_stmt = $conn->query("SELECT COUNT(*) as total FROM bookings");
$total_bookings = $booking_stmt->fetch()['total'];

$confirmed_booking_stmt = $conn->query("SELECT COUNT(*) as confirmed FROM bookings WHERE status='confirmed'");
$confirmed_bookings = $confirmed_booking_stmt->fetch()['confirmed'];
$booking_percent = $total_bookings > 0 ? round(($confirmed_bookings / $total_bookings) * 100) : 0;

$user_stmt = $conn->query("SELECT COUNT(*) as total FROM users");
$total_users = $user_stmt->fetch()['total'];

$verified_stmt = $conn->query("SELECT COUNT(*) as verified FROM users WHERE is_verified = 1");
$verified_users = $verified_stmt->fetch()['verified'];

$pending_stmt = $conn->query("SELECT COUNT(*) as pending FROM bookings WHERE status='pending'");
$pending_bookings = $pending_stmt->fetch()['pending'];

$cancelled_stmt = $conn->query("SELECT COUNT(*) as cancelled FROM bookings WHERE status='cancelled'");
$cancelled_bookings = $cancelled_stmt->fetch()['cancelled'];

$revenue_stmt = $conn->query("SELECT SUM(amount) as total_revenue FROM payments WHERE status='paid'");
$revenue = $revenue_stmt->fetch()['total_revenue'] ?? 0;
$monthly_target = 300000;
$revenue_percent = $monthly_target > 0 ? round(($revenue / $monthly_target) * 100) : 0;

$paid_stmt = $conn->query("SELECT COUNT(*) as paid FROM bookings WHERE payment_status = 'paid'");
$paid_bookings = $paid_stmt->fetch()['paid'] ?? 0;

$unpaid_stmt = $conn->query("SELECT COUNT(*) as unpaid FROM bookings WHERE payment_status = 'unpaid'");
$unpaid_bookings = $unpaid_stmt->fetch()['unpaid'] ?? 0;

$total_payment_status = $paid_bookings + $unpaid_bookings;
$paid_percent = $total_payment_status > 0 ? round(($paid_bookings / $total_payment_status) * 100) : 0;

    $revenue_percent = round(($revenue / 300000) * 100);
    $booking_percent = $total_bookings > 0 ? round(($confirmed_bookings / $total_bookings) * 100) : 0;
    $hotel_percent = $total_hotels > 0 ? round(($active_hotels / $total_hotels) * 100) : 0;
    $user_percent = $total_users > 0 ? round(($verified_users / $total_users) * 100) : 0;
    $paid_percent = $total_bookings > 0 ? round(($paid_bookings / $total_bookings) * 100) : 0;
    $unpaid_percent = $total_bookings > 0 ? round(($unpaid_bookings / $total_bookings) * 100) : 0;
    $hotel_percent = $total_hotels > 0 ? round(($active_hotels / $total_hotels) * 100) : 0;
    $paid_percent = $total_payment_status > 0 ? round(($paid_bookings / $total_payment_status) * 100) : 0;
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
  .small-donut {
    width: 120px !important;
    height: 120px !important;
    margin: auto;
  }

.sidebar {
  background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
  min-height: 100vh;
  transition: all 0.3s ease-in-out;
  border-right: 2px solid #444;
}

.sidebar .nav-link {
  padding: 10px 20px;
  margin: 6px 10px;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: background 0.3s, transform 0.2s;
}

.sidebar .nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
  transform: translateX(5px);
}

.sidebar .nav-link.active,
.sidebar .nav-link:focus {
  background-color: rgba(255, 255, 255, 0.2);
  font-weight: bold;
}

.sidebar .text-white h4 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.4rem;
  letter-spacing: 1px;
}

</style>

</head>
<body>
<div class="container-fluid">
  <div class="row">
        <nav class="col-md-2 col-lg-2 d-md-block bg-dark sidebar collapse">
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
                <a class="nav-link text-white" href="manage_users.php">
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
          <div class="header d-flex justify-content-between align-items-center p-3 rounded shadow-sm bg-light mb-4" style="border-left: 6px solid #4B7BEC;">
              <div class="d-flex align-items-center gap-3">
                <i class=""></i>
                <h2 class="m-0 fw-bold text-dark">ADMIN DASHBOARD</h2>
              </div>
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-circle fs-4 text-primary"></i>
                <span class="fw-semibold text-secondary">Welcome, 
                  <span class="text-dark"><?= htmlspecialchars($_SESSION['admin']) ?></span> 
                </span>
              </div>
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
                    <a href="manage_bookings.php" class="btn btn-light btn-sm">Manage Bookings</a>
                  </div>
                </div>
              </div>

            </div>
          </div>
          <div class="card mt-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
              <div><i class="bi bi-calendar-check-fill me-2"></i> Booking Overview</div>
            </div>
            <div class="card-body">
              <div class="row">
                
                <div class="col-md-8">
                  <div class="p-3 bg-light rounded shadow-sm">
                    <h6 class="fw-bold">Booking Details</h6>
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Bookings
                        <span class="badge bg-primary rounded-pill"><?= $total_bookings ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Confirmed Bookings
                        <span class="badge bg-success rounded-pill"><?= $confirmed_bookings ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Pending Bookings
                        <span class="badge bg-warning text-dark rounded-pill"><?= $pending_bookings ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cancelled Bookings
                        <span class="badge bg-danger rounded-pill"><?= $cancelled_bookings ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Completion Rate
                        <span class="badge bg-info text-dark rounded-pill"><?= $booking_percent ?>%</span>
                      </li>
                    </ul>
                  </div>
                </div>

              
                <div class="col-md-4">
                  <div class="p-3 bg-light rounded shadow-sm">
                    <h6 class="fw-bold">User Stats</h6>
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Users
                        <span class="badge bg-primary rounded-pill"><?= $total_users ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Verified Users
                        <span class="badge bg-success rounded-pill"><?= $verified_users ?></span>
                      </li>
                    </ul>
                  </div>
                </div>
                
                <div class="row mt-4">
                <div class="row">
                  <!-- Hotel Status -->
                  <div class="col-md-4 mb-3">
                    <div class="card shadow-sm p-2">
                      <div class="card-header bg-primary text-white py-2 px-3 small">Hotel Status</div>
                      <div class="card-body text-center p-1">
                        <canvas id="hotelChart" class="small-donut"></canvas>
                      </div>
                    </div>
                  </div>

                  <!-- Payment Status -->
                  <div class="col-md-4 mb-3">
                    <div class="card shadow-sm p-2">
                      <div class="card-header bg-success text-white py-2 px-3 small">Payment Status</div>
                      <div class="card-body text-center p-1">
                        <canvas id="paymentChart" class="small-donut"></canvas>
                      </div>
                    </div>
                  </div>

                  <!-- Revenue Status -->
                  <div class="col-md-4 mb-3">
                    <div class="card shadow-sm p-2">
                      <div class="card-header bg-warning text-dark py-2 px-3 small">Revenue Status</div>
                      <div class="card-body text-center p-1">
                        <canvas id="revenueChart" class="small-donut"></canvas>
                        <div class="mt-2 small fw-bold" id="revenuePercentage"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                </div>
              </div>
            </div>
          </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
        // Hotel Chart
        new Chart(document.getElementById('hotelChart'), {
          type: 'doughnut',
          data: {
            labels: ['Active Hotels', 'Inactive Hotels'],
            datasets: [{
              data: [<?= $active_hotels ?>, <?= $total_hotels - $active_hotels ?>],
              backgroundColor: ['#198754', '#ced4da'],
              hoverOffset: 6
            }]
          },
          options: {
            plugins: {
              legend: { position: 'bottom' },
            }
          }
        });

          // Payment Chart
          new Chart(document.getElementById('paymentChart'), {
            type: 'doughnut',
            data: {
              labels: ['Paid', 'Unpaid'],
              datasets: [{
                data: [<?= $paid_bookings ?>, <?= $unpaid_bookings ?>],
                backgroundColor: ['#0d6efd', '#ffc107'],
                hoverOffset: 6
              }]
            },
            options: {
              plugins: {
                legend: { position: 'bottom' },
              }
            }
          });
        </script>
        <script>

          const actualRevenue = 65000; // actual revenue
          const targetRevenue = 100000; // target revenue

          const percentage = ((actualRevenue / targetRevenue) * 100).toFixed(1);

          const ctxRevenue = document.getElementById("revenueChart").getContext("2d");

          new Chart(ctxRevenue, {
            type: 'doughnut',
            data: {
              labels: ['Achieved', 'Remaining'],
              datasets: [{
                data: [actualRevenue, targetRevenue - actualRevenue],
                backgroundColor: ['#f1c40f', '#ecf0f1'],
                borderWidth: 1
              }]
            },
            options: {
              cutout: '70%',
              plugins: {
                legend: {
                  display: false
                }
              }
            }
          });

          document.getElementById("revenuePercentage").innerText = `${percentage}% of Target Achieved`;
        </script>
</body>
</html>
