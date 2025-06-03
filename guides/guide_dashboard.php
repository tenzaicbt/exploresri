<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['guide_id'])) {
    header("Location: guide_login.php");
    exit;
}

$guide_id = $_SESSION['guide_id'];
$status_filter = $_GET['status'] ?? 'all';

$sql = "SELECT 
          b.booking_id, 
          b.user_id, 
          b.guide_id, 
          b.travel_date, 
          b.duration_days,
          b.status, 
          b.check_in_date,
          b.check_out_date,
          b.payment_status,
          b.amount,
          b.notes,
          p.payment_method,
          p.payment_date,
          p.amount as paid_amount
        FROM guide_bookings b
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE b.guide_id = ?";

$params = [$guide_id];

if (in_array($status_filter, ['confirmed', 'pending', 'cancelled'])) {
    $sql .= " AND b.status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$booking_stmt = $conn->prepare("SELECT COUNT(*) as total FROM guide_bookings WHERE guide_id = ?");
$booking_stmt->execute([$guide_id]);
$total_bookings = $booking_stmt->fetch()['total'] ?? 0;

$confirmed_stmt = $conn->prepare("SELECT COUNT(*) as confirmed FROM guide_bookings WHERE guide_id = ? AND status = 'confirmed'");
$confirmed_stmt->execute([$guide_id]);
$confirmed_bookings = $confirmed_stmt->fetch()['confirmed'] ?? 0;

$pending_stmt = $conn->prepare("SELECT COUNT(*) as pending FROM guide_bookings WHERE guide_id = ? AND status = 'pending'");
$pending_stmt->execute([$guide_id]);
$pending_bookings = $pending_stmt->fetch()['pending'] ?? 0;

$cancelled_stmt = $conn->prepare("SELECT COUNT(*) as cancelled FROM guide_bookings WHERE guide_id = ? AND status = 'cancelled'");
$cancelled_stmt->execute([$guide_id]);
$cancelled_bookings = $cancelled_stmt->fetch()['cancelled'] ?? 0;

$booking_percent = $total_bookings > 0 ? round(($confirmed_bookings / $total_bookings) * 100) : 0;

$revenue_stmt = $conn->prepare("
    SELECT SUM(p.amount) as total
    FROM guide_bookings b
    JOIN payments p ON b.booking_id = p.booking_id
    WHERE b.guide_id = ? AND b.status = 'confirmed' AND p.status = 'paid'
");
$revenue_stmt->execute([$guide_id]);
$total_revenue = $revenue_stmt->fetch()['total'] ?? 0;

$revenue_target = 100000;
$revenue_percent = $revenue_target > 0 ? round(($total_revenue / $revenue_target) * 100) : 0;

$guide_stmt = $conn->prepare("SELECT * FROM guide WHERE guide_id = ?");
$guide_stmt->execute([$guide_id]);
$guide = $guide_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Guide Dashboard - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  
  <style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
    color: #fff;
    margin: 0;
    overflow-x: hidden;
  }
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 260px;
    background: #121620;
    padding-top: 1.5rem;
    box-shadow: 3px 0 10px rgba(0, 0, 0, 0.7);
    z-index: 1000;
  }
  .sidebar .text-center {
    padding: 0 1rem;
    margin-bottom: 2rem;
  }
  .sidebar .nav-link {
    padding: 12px 24px;
    margin: 6px 12px;
    border-radius: 10px;
    font-size: 1rem;
    color: white;
    transition: background 0.3s ease, transform 0.2s ease;
    display: flex;
    align-items: center;
  }
  .sidebar .nav-link i {
    font-size: 1.3rem;
    margin-right: 12px;
  }
  .sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.15);
    transform: translateX(6px);
  }
  .sidebar .nav-link.active,
  .sidebar .nav-link:focus {
    background-color: rgba(255, 255, 255, 0.25);
    font-weight: 700;
  }
  .content {
    margin-left: 260px;
    padding: 3rem;
    background: transparent;
    min-height: 100vh;
  }
  .card {
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.06);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
    color: #f1f1f1;
    transition: all 0.3s ease;
  }
  .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(241, 196, 15, 0.7);
  }
  .badge {
    font-size: 1.1rem;
  }
  h6 {
    color: #f1c40f;
    font-weight: 700;
    margin-bottom: 1.4rem;
  }
  .list-group-item {
    background: transparent;
    border: none;
    color: #ddd;
  }
  .list-group-item.d-flex {
    border-bottom: 1px solid rgba(241, 196, 15, 0.15);
  }
  .row.g-4 {
    gap: 0rem;
  }
  .col-lg-8 {
    max-width: 66.66%;
    flex: 0 0 66.66%;
  }
  .col-lg-4 {
    max-width: 33.33%;
    flex: 0 0 33.33%;
  }
  @media (min-width: 992px) {
    .col-lg-4 {
      max-width: 40%;
      flex: 0 0 40%;
    }
    .col-lg-8 {
      max-width: 55%;
      flex: 0 0 55%;
    }
  }
  @media (max-width: 991px) {
    .content {
      margin-left: 0;
      padding: 2rem 1rem;
    }
    .col-lg-8, .col-lg-4, .col-12 {
      max-width: 100% !important;
      flex: 0 0 100% !important;
    }
  }
  a.badge {
    cursor: pointer;
    text-decoration: none;
  }
  </style>
</head>
<body>
  <nav class="sidebar">
    <div class="text-center mb-4">
      <h4 class="fw-bold">ExploreSri Guide</h4>
      <p class="mb-0"><?= htmlspecialchars($guide['name']) ?></p>
    </div>
    <ul class="nav flex-column px-2">
      <li class="nav-item">
        <a class="nav-link active" href="guide_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="guide_profile.php"><i class="bi bi-person-circle"></i> My Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="assigned_bookings.php"><i class="bi bi-briefcase"></i> Assigned Bookings</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="guide_logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </li>
    </ul>
  </nav>

  <main class="content">
    <div class="container-fluid">
      <div class="row g-4">
        <div class="col-lg-8">
          <div class="card p-4 shadow-sm">
            <h6>Booking Details</h6>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Total Bookings
                <a href="guide_dashboard.php?status=all" class="badge rounded-pill <?= $status_filter == 'all' ? 'bg-primary' : 'bg-secondary' ?>">
                  <?= $total_bookings ?>
                </a>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Confirmed Bookings
                <a href="guide_dashboard.php?status=confirmed" class="badge rounded-pill <?= $status_filter == 'confirmed' ? 'bg-success' : 'bg-secondary' ?>">
                  <?= $confirmed_bookings ?>
                </a>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Pending Bookings
                <a href="guide_dashboard.php?status=pending" class="badge rounded-pill <?= $status_filter == 'pending' ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                  <?= $pending_bookings ?>
                </a>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Cancelled Bookings
                <a href="guide_dashboard.php?status=cancelled" class="badge rounded-pill <?= $status_filter == 'cancelled' ? 'bg-danger' : 'bg-secondary' ?>">
                  <?= $cancelled_bookings ?>
                </a>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Completion Rate
                <span class="badge bg-info text-dark rounded-pill"><?= $booking_percent ?>%</span>
              </li>
            </ul>
          </div>

          <div class="card p-4 mt-4 shadow-sm">
            <h6>Bookings List (Filtered by: <?= ucfirst($status_filter) ?>)</h6>
            <div class="table-responsive">
              <table class="table table-dark table-striped align-middle">
                <thead>
                  <tr>
                    <th>Booking ID</th>
                    <th>User ID</th>
                    <th>Travel Date</th>
                    <th>Duration (Days)</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($bookings): ?>
                    <?php foreach ($bookings as $booking): ?>
                      <tr>
                        <td><?= htmlspecialchars($booking['booking_id']) ?></td>
                        <td><?= htmlspecialchars($booking['user_id']) ?></td>
                        <td><?= htmlspecialchars($booking['travel_date']) ?></td>
                        <td><?= htmlspecialchars($booking['duration_days']) ?></td>
                        <td>
                          <?php
                          $statusClass = 'badge bg-secondary';
                          if ($booking['status'] === 'confirmed') $statusClass = 'badge bg-success';
                          elseif ($booking['status'] === 'pending') $statusClass = 'badge bg-warning text-dark';
                          elseif ($booking['status'] === 'cancelled') $statusClass = 'badge bg-danger';
                          ?>
                          <span class="<?= $statusClass ?>"><?= ucfirst($booking['status']) ?></span>
                        </td>
                        <td>
                          <?php
                          $paymentClass = ($booking['payment_status'] === 'paid') ? 'badge bg-success' : 'badge bg-warning text-dark';
                          ?>
                          <span class="<?= $paymentClass ?>"><?= ucfirst($booking['payment_status']) ?></span>
                        </td>
                        <td>₹<?= number_format($booking['amount'], 2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center">No bookings found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card p-4 shadow-sm">
            <h6>Revenue Summary</h6>
            <p>Total Revenue: <strong>₹<?= number_format($total_revenue, 2) ?></strong></p>
            <div class="progress mb-3" style="height: 24px;">
              <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $revenue_percent ?>%;" aria-valuenow="<?= $revenue_percent ?>" aria-valuemin="0" aria-valuemax="100">
                <?= $revenue_percent ?>%
              </div>
            </div>
            <p>Revenue Target: ₹<?= number_format($revenue_target, 2) ?></p>
          </div>

          <div class="card p-4 mt-4 shadow-sm text-center">
            <h6>Guide Info</h6>
            <img 
              src="<?= !empty($guide['profile_pic']) ? htmlspecialchars($guide['profile_pic']) : 'default-profile.png' ?>" 
              alt="<?= !empty($guide['name']) ? htmlspecialchars($guide['name']) : 'Guide Image' ?>" 
              class="rounded-circle mb-3" 
              style="width: 120px; height: 120px; object-fit: cover;" 
            />
            <h5><?= htmlspecialchars($guide['name']) ?></h5>
            <p>Email: <?= htmlspecialchars($guide['email']) ?></p>
            <p>Mobile: <?= isset($guide['mobile']) ? htmlspecialchars($guide['mobile']) : 'N/A' ?></p>
            <p>Location: <?= isset($guide['location']) ? htmlspecialchars($guide['location']) : 'N/A' ?></p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
