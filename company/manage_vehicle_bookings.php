<?php
session_start();
if (!isset($_SESSION['company_id'])) {
  header("Location: company_login.php");
  exit;
}

include '../config/db.php';

$company_id = $_SESSION['company_id'];

// Status filter logic
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Check for success notification flag
$show_success = isset($_GET['updated']) && $_GET['updated'] === 'success';

$sql = "
    SELECT 
        vb.*, 
        v.model, 
        v.image, 
        vp.amount, 
        vp.payment_method, 
        vp.payment_date, 
        vp.status AS payment_status,
        u.name AS name
    FROM vehicle_bookings vb
    JOIN vehicles v ON vb.vehicle_id = v.vehicle_id
    LEFT JOIN vehicle_payments vp ON vb.booking_id = vp.booking_id
    JOIN users u ON vb.user_id = u.user_id
    WHERE v.company_id = ?
";


$params = [$company_id];

if ($status_filter !== 'all') {
  $sql .= " AND vb.status = ?";
  $params[] = $status_filter;
}

$sql .= " ORDER BY vb.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Manage Vehicle Bookings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .truncate {
      max-width: 200px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .modal img {
      max-height: 100px;
      margin: 5px;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h2 class="mb-4">Manage Vehicle Bookings</h2>

    <!-- Success Notification -->
    <?php if ($show_success): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
        Booking status updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Filter Dropdown -->
    <form method="GET" class="mb-3">
      <div class="row g-2 align-items-center">
        <div class="col-auto">
          <label for="status" class="col-form-label">Filter by Status:</label>
        </div>
        <div class="col-auto">
          <select name="status" id="status" class="form-select" onchange="this.form.submit()">
            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All</option>
            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $status_filter == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
          </select>
        </div>
      </div>
    </form>

    <!-- Booking Table -->
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <!-- <th>Booking ID</th> -->
          <th>Vehicle Model</th>
          <th>User Name</th>
          <th>Booking Status</th>
          <th>Payment Status</th>
          <th>Amount</th>
          <th>Payment Method</th>
          <th>Payment Date</th>
          <th>Booking Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($bookings): ?>
          <?php foreach ($bookings as $booking): ?>
            <tr>
              <!-- <td><?= htmlspecialchars($booking['booking_id']) ?></td> -->
              <td class="truncate" title="<?= htmlspecialchars($booking['model']) ?>"><?= htmlspecialchars($booking['model']) ?></td>
              <td><?= htmlspecialchars($booking['name']) ?></td>
              <td>
                <!-- Editable booking status form -->
                <form method="post" action="update_booking_status.php" class="d-flex align-items-center gap-2">
                  <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>">
                  <select name="status" class="form-select form-select-sm" style="width:120px;">
                    <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                  </select>
                  <button type="submit" class="btn btn-sm btn-success" title="Update status">
                    <i class="bi bi-check2-circle"></i>
                  </button>
                </form>
              </td>
              <td>
                <span class="badge 
                <?= strtolower($booking['payment_status']) === 'paid' ? 'bg-success' : (strtolower($booking['payment_status']) === 'pending' ? 'bg-warning' : 'bg-secondary') ?>">
                  <?= htmlspecialchars($booking['payment_status'] ?? 'Unpaid') ?>
                </span>
              </td>
              <td>$<?= number_format($booking['amount'] ?? 0, 2) ?></td>
              <td><?= htmlspecialchars($booking['payment_method'] ?? '-') ?></td>
              <td><?= htmlspecialchars($booking['payment_date'] ?? '-') ?></td>
              <td><?= htmlspecialchars($booking['created_at']) ?></td>
              <td>
                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= $booking['booking_id'] ?>">View</button>
              </td>
            </tr>

            <!-- View Modal -->
            <div class="modal fade" id="viewModal<?= $booking['booking_id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $booking['booking_id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content text-dark">
                  <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel<?= $booking['booking_id'] ?>">Booking Details - ID <?= htmlspecialchars($booking['booking_id']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p><strong>Vehicle Model:</strong> <?= htmlspecialchars($booking['model']) ?></p>
                    <p><strong>User Name:</strong> <?= htmlspecialchars($booking['name']) ?></p>
                    <p><strong>Booking Status:</strong> <?= ucfirst($booking['status']) ?></p>
                    <p><strong>Payment Status:</strong> <?= htmlspecialchars($booking['payment_status'] ?? 'Unpaid') ?></p>
                    <p><strong>Amount:</strong> $<?= number_format($booking['amount'] ?? 0, 2) ?></p>
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method'] ?? '-') ?></p>
                    <p><strong>Payment Date:</strong> <?= htmlspecialchars($booking['payment_date'] ?? '-') ?></p>
                    <p><strong>Booking Created At:</strong> <?= htmlspecialchars($booking['created_at']) ?></p>
                    <p><strong>Vehicle Image:</strong><br>
                      <?php if (!empty($booking['image'])): ?>
                        <img src="../uploads/vehicles/<?= htmlspecialchars($booking['image']) ?>" alt="Vehicle Image" class="img-fluid rounded" />
                      <?php else: ?>
                        <em>No image available</em>
                      <?php endif; ?>
                    </p>
                  </div>
                </div>
              </div>
            </div>

          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="text-center">No bookings found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <a href="transport_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
  </div>

  <!-- Bootstrap Icons CDN for icons used -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <script>
    // Auto-dismiss success alert after 5 seconds
    setTimeout(() => {
      const alert = document.getElementById('successAlert');
      if (alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }
    }, 5000);
  </script>
</body>

</html>