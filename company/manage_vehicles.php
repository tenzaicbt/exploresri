<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['company_id'])) {
  header("Location: company_login.php");
  exit;
}

$company_id = $_SESSION['company_id'];

// Toggle availability
if (isset($_GET['toggle']) && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $stmt = $conn->prepare("UPDATE vehicles SET availability = NOT availability WHERE vehicle_id = ? AND company_id = ?");
  $stmt->execute([$id, $company_id]);
  header("Location: manage_vehicles.php");
  exit;
}

// Delete vehicle
if (isset($_GET['delete']) && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $stmt = $conn->prepare("DELETE FROM vehicles WHERE vehicle_id = ? AND company_id = ?");
  $stmt->execute([$id, $company_id]);
  header("Location: manage_vehicles.php");
  exit;
}

// Fetch all vehicles for this company
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE company_id = ? ORDER BY created_at DESC");
$stmt->execute([$company_id]);
$vehicles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Manage Vehicles</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }

    .vehicle-image {
      width: 80px;
      height: 50px;
      object-fit: cover;
      border-radius: 5px;
    }

    .status-badge {
      font-size: 0.8rem;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h2 class="mb-4">Manage Your Vehicles</h2>
    <a href="add_vehicle.php" class="btn btn-success mb-3">+ Add New Vehicle</a>

    <?php if (count($vehicles) > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>Image</th>
              <th>Model</th>
              <th>Type</th>
              <th>Capacity</th>
              <th>Price (Per Day)</th>
              <th>Fuel</th>
              <th>Reg. No.</th>
              <th>Status</th>
              <th>Registered</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($vehicles as $v): ?>
              <tr>
                <td>
                  <img src="../uploads/vehicles/<?php echo htmlspecialchars($v['image']); ?>" class="vehicle-image" alt="Vehicle" />
                </td>
                <td><?php echo htmlspecialchars($v['model']); ?></td>
                <td><?php echo htmlspecialchars($v['type']); ?></td>
                <td><?php echo htmlspecialchars($v['capacity']); ?> seats</td>
                <td>$<?php echo htmlspecialchars($v['rental_price']); ?></td>
                <td><?php echo htmlspecialchars($v['fuel_type']); ?></td>
                <td><?php echo htmlspecialchars($v['registration_number']); ?></td>
                <td>
                  <span class="badge bg-<?php echo $v['availability'] ? 'success' : 'secondary'; ?> status-badge">
                    <?php echo $v['availability'] ? 'Available' : 'Unavailable'; ?>
                  </span>
                </td>
                <td><?php echo date('Y-m-d', strtotime($v['created_at'])); ?></td>
                <td>
                  <a href="?toggle=1&id=<?php echo $v['vehicle_id']; ?>" class="btn btn-sm btn-outline-primary">Toggle</a>
                  <a href="edit_vehicle.php?id=<?php echo $v['vehicle_id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                  <a href="?delete=1&id=<?php echo $v['vehicle_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this vehicle?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-warning">No vehicles found. <a href="add_vehicle.php">Add one now</a>.</div>
    <?php endif; ?>

    <a href="transport_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
  </div>
</body>

</html>