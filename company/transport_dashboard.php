<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['company_id'])) {
  header("Location: company_login.php");
  exit;
}

$company_id = $_SESSION['company_id'];
$status_filter = $_GET['status'] ?? 'all';

$company_stmt = $conn->prepare("SELECT * FROM transport_companies WHERE company_id = ?");
$company_stmt->execute([$company_id]);
$company = $company_stmt->fetch(PDO::FETCH_ASSOC);

// Query vehicles
$sql = "SELECT * FROM vehicles WHERE company_id = ?";
$params = [$company_id];
if (in_array($status_filter, ['available', 'unavailable'])) {
  $sql .= " AND availability = ?";
  $params[] = ($status_filter === 'available') ? 1 : 0;
}
$sql .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count stats
$total_vehicles = $conn->prepare("SELECT COUNT(*) as total FROM vehicles WHERE company_id = ?");
$total_vehicles->execute([$company_id]);
$total_vehicles = $total_vehicles->fetch()['total'] ?? 0;

$available_vehicles = $conn->prepare("SELECT COUNT(*) as available FROM vehicles WHERE company_id = ? AND availability = 1");
$available_vehicles->execute([$company_id]);
$available_vehicles = $available_vehicles->fetch()['available'] ?? 0;

$unavailable_vehicles = $conn->prepare("SELECT COUNT(*) as unavailable FROM vehicles WHERE company_id = ? AND availability = 0");
$unavailable_vehicles->execute([$company_id]);
$unavailable_vehicles = $unavailable_vehicles->fetch()['unavailable'] ?? 0;

// Toggle availability
if (isset($_POST['toggle_availability'])) {
  $vehicle_id = $_POST['vehicle_id'];
  $current_status = $_POST['current_status'];
  $new_status = $current_status == 1 ? 0 : 1;
  $conn->prepare("UPDATE vehicles SET availability = ? WHERE vehicle_id = ? AND company_id = ?")
    ->execute([$new_status, $vehicle_id, $company_id]);
  header("Location: transport_dashboard.php?status=$status_filter");
  exit;
}

// Delete vehicle
if (isset($_POST['delete_vehicle'])) {
  $vehicle_id = $_POST['vehicle_id'];
  $conn->prepare("DELETE FROM vehicles WHERE vehicle_id = ? AND company_id = ?")
    ->execute([$vehicle_id, $company_id]);
  header("Location: transport_dashboard.php?status=$status_filter");
  exit;
}

// Total booking price
$total_price_stmt = $conn->prepare("
    SELECT SUM(rental_price) as total_price 
    FROM vehicle_bookings 
    JOIN vehicles ON vehicle_bookings.vehicle_id = vehicles.vehicle_id 
    WHERE vehicles.company_id = ?
");
$total_price_stmt->execute([$company_id]);
$total_booking_price = $total_price_stmt->fetch()['total_price'] ?? 0;

// Total number of bookings for this company's vehicles
$total_bookings_stmt = $conn->prepare("
    SELECT COUNT(*) AS total_bookings 
    FROM vehicle_bookings vb
    JOIN vehicles v ON vb.vehicle_id = v.vehicle_id
    WHERE v.company_id = ?
");
$total_bookings_stmt->execute([$company_id]);
$total_bookings = $total_bookings_stmt->fetch()['total_bookings'] ?? 0;

// Total booking value (sum of total_price for bookings of this company's vehicles)
$total_booking_price_stmt = $conn->prepare("
    SELECT SUM(total_price) AS total_booking_price
    FROM vehicle_bookings vb
    JOIN vehicles v ON vb.vehicle_id = v.vehicle_id
    WHERE v.company_id = ?
");
$total_booking_price_stmt->execute([$company_id]);
$total_booking_price = $total_booking_price_stmt->fetch()['total_booking_price'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Transport Dashboard - <?= htmlspecialchars($company['company_name']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #f8f9fa;
      margin: 0;
      overflow-x: hidden;
    }

    .list-group-item {
      background-color: rgba(255, 255, 255, 0.05);
      color: #f8f9fa;
      border-color: rgba(255, 255, 255, 0.1);
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
      color: #fff;
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

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: rgba(255, 255, 255, 0.15);
      transform: translateX(6px);
      font-weight: bold;
    }

    .content {
      margin-left: 260px;
      padding: 2.5rem 2rem;
      min-height: 100vh;
      background: transparent;
    }

    .card {
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.06);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
      color: #f1f1f1;
    }

    .vehicle-card {
      background-color: rgba(255, 255, 255, 0.06);
      border-radius: 12px;
      padding: 1.25rem;
      display: flex;
      gap: 1.25rem;
      margin-bottom: 1.5rem;
      align-items: flex-start;
    }

    .vehicle-card img {
      width: 160px;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
    }

    .vehicle-details {
      flex: 1;
      color: #e0e0e0;
    }

    .vehicle-details h6 {
      font-weight: 600;
      color: #ffffff;
    }

    .btn-sm {
      font-size: 0.85rem;
      padding: 0.3rem 0.75rem;
    }

    .badge {
      font-size: 0.95rem;
      padding: 0.4em 0.6em;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <div class="text-center text-white mb-4">
      <h5>Transport Dashboard</h5>
      <p class="small"><?= htmlspecialchars($company['company_name']) ?></p>
    </div>
    <ul class="nav flex-column px-3">
      <li class="nav-item"><a href="transport_dashboard.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
      <li class="nav-item"><a href="manage_vehicles.php" class="nav-link"><i class="bi bi-plus-circle me-2"></i> Manage vehicles</a></li>
      <li class="nav-item"><a href="manage_vehicle_bookings.php" class="nav-link"><i class="bi bi-card-checklist me-2"></i> Manage Bookings</a></li>
      <li class="nav-item"><a href="profile.php" class="nav-link"><i class="bi bi-person-circle me-2"></i> Profile</a></li>
      <li class="nav-item"><a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
    </ul>
  </div>

  <main class="content">
    <div class="container-fluid">
      <!-- Stats Cards -->
      <div class="row mb-4 text-white">
        <div class="col-md-4">
          <div class="card p-3 d-flex flex-column align-items-start">
            <h6 class="mb-1"><i class="bi bi-truck me-2"></i>Total Vehicles</h6>
            <h3><?= $total_vehicles ?></h3>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 d-flex flex-column align-items-start">
            <h6 class="mb-1"><i class="bi bi-check-circle me-2 text-success"></i>Available</h6>
            <h3><?= $available_vehicles ?></h3>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 d-flex flex-column align-items-start">
            <h6 class="mb-1"><i class="bi bi-x-circle me-2 text-danger"></i>Unavailable</h6>
            <h3><?= $unavailable_vehicles ?></h3>
          </div>
        </div>
      </div>

      <!-- Booking Stats Row -->
      <div class="row mb-4 text-white">
        <div class="col-md-6">
          <div class="card p-3 d-flex flex-column align-items-start">
            <h6 class="mb-1"><i class="bi bi-card-list me-2 text-info"></i>Total Bookings</h6>
            <h3><?= $total_bookings ?></h3>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card p-3 d-flex flex-column align-items-start">
            <h6 class="mb-1"><i class="bi bi-currency-dollar me-2 text-warning"></i>Total Booking Value</h6>
            <h3>$<?= number_format($total_booking_price, 2) ?></h3>
          </div>
        </div>
      </div>

      <!-- Filter Buttons -->
      <div class="card p-4 mb-4">
        <h5 class="mb-3">Vehicles (<?= ucfirst($status_filter) ?>)</h5>
        <div class="mb-3">
          <a href="?status=all" class="btn btn-sm <?= $status_filter == 'all' ? 'btn-primary' : 'btn-outline-light' ?>">All</a>
          <a href="?status=available" class="btn btn-sm <?= $status_filter == 'available' ? 'btn-success' : 'btn-outline-success' ?>">Available</a>
          <a href="?status=unavailable" class="btn btn-sm <?= $status_filter == 'unavailable' ? 'btn-danger' : 'btn-outline-danger' ?>">Unavailable</a>
        </div>

        <?php if ($vehicles): ?>
          <?php foreach ($vehicles as $vehicle): ?>
            <div class="vehicle-card">
              <div class="vehicle-details">
                <h6><?= htmlspecialchars($vehicle['model']) ?> (ID: <?= $vehicle['vehicle_id'] ?>)</h6>
                <p><strong>Type:</strong> <?= htmlspecialchars($vehicle['type']) ?> |
                  <strong>Capacity:</strong> <?= htmlspecialchars($vehicle['capacity']) ?> persons
                </p>
                <p><strong>Rental Price:</strong> $<?= number_format($vehicle['rental_price'], 2) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($vehicle['description'] ?? 'N/A') ?></p>
                <p><strong>Features:</strong> <?= htmlspecialchars($vehicle['features'] ?? 'N/A') ?></p>
                <p>Status:
                  <span class="badge <?= $vehicle['availability'] ? 'bg-success' : 'bg-danger' ?>">
                    <?= $vehicle['availability'] ? 'Available' : 'Unavailable' ?>
                  </span>
                </p>
                <p class="small" style="color: #ffffff;">Created at: <?= date('d M Y, h:i A', strtotime($vehicle['created_at'])) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">No vehicles found for the selected filter.</p>
        <?php endif; ?>
      </div>
    </div>
  </main>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>