<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

include '../config/db.php';

// Fetch all bookings with payment info
$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.booking_date,
        b.check_in_date,
        b.check_out_date,
        b.status AS booking_status,
        h.name AS hotel_name,
        u.name AS user_name,
        p.amount AS total_price,
        p.payment_method,
        p.payment_date,
        p.status AS payment_status
    FROM bookings b
    JOIN hotels h ON b.hotel_id = h.hotel_id
    JOIN users u ON b.user_id = u.user_id
    LEFT JOIN payments p ON b.booking_id = p.booking_id
    ORDER BY b.booking_id DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Correct total revenue calculation from payments table
$revenueStmt = $conn->query("
    SELECT SUM(p.amount) AS total_revenue
    FROM bookings b
    JOIN payments p ON b.booking_id = p.booking_id
    WHERE b.status = 'confirmed' AND LOWER(p.status) = 'paid'
");
$revenueRow = $revenueStmt->fetch(PDO::FETCH_ASSOC);
$totalRevenue = $revenueRow['total_revenue'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Bookings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .container {
      background: #fff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    h2 {
      color: #333;
    }

    .table thead th {
      background-color: rgb(0, 0, 0);
      color: white;
    }

    .btn-sm i {
      vertical-align: middle;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h2 class="mb-4"><i class="bi bi-card-list"></i> Manage Bookings</h2>

    <div class="alert alert-info"><strong>Total Revenue:</strong> LKR <?= number_format((float)$totalRevenue, 2) ?></div>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success'];
                                        unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error'];
                                      unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (count($bookings) === 0): ?>
      <div class="alert alert-warning text-center">No bookings found.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead>
            <tr>
              <th>Hotel</th>
              <th>User</th>
              <th>Booking Date</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Total Price</th>
              <th>Payment Method</th>
              <th>Payment Date</th>
              <th>Status</th>
              <th>Payment</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
              <tr>
                <td><?= htmlspecialchars($b['hotel_name']) ?></td>
                <td><?= htmlspecialchars($b['user_name']) ?></td>
                <td><?= htmlspecialchars($b['booking_date']) ?></td>
                <td><?= htmlspecialchars($b['check_in_date']) ?></td>
                <td><?= htmlspecialchars($b['check_out_date']) ?></td>
                <td>
                  <?= $b['total_price'] !== null ? 'LKR ' . number_format((float)$b['total_price'], 2) : '<span class="text-muted">-</span>' ?>
                </td>
                <td><?= htmlspecialchars($b['payment_method'] ?? '-') ?></td>
                <td><?= htmlspecialchars($b['payment_date'] ?? '-') ?></td>
                <td>
                  <form method="post" action="update_booking_status.php" class="d-flex align-items-center">
                    <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                    <select name="status" class="form-select form-select-sm me-2">
                      <option value="pending" <?= $b['booking_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                      <option value="confirmed" <?= $b['booking_status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                      <option value="cancelled" <?= $b['booking_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-success">
                      <i class="bi bi-check2-circle"></i>
                    </button>
                  </form>
                </td>
                <td>
                  <?php if (strtolower($b['payment_status']) === 'paid'): ?>
                    <span class="badge bg-success">Paid</span>
                  <?php elseif (strtolower($b['payment_status']) === 'unpaid'): ?>
                    <span class="badge bg-warning text-dark">Unpaid</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">N/A</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="delete_bookings.php?id=<?= $b['booking_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                    <i class="bi bi-trash-fill"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-3"><i class=""></i> Back to Dashboard</a>
  </div>
</body>

</html>