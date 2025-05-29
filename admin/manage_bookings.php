<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.booking_date,
        b.travel_date,
        b.status,
        b.payment_status,
        h.name AS hotel_name,
        u.name AS user_name
    FROM bookings b
    JOIN hotels h ON b.hotel_id = h.hotel_id
    JOIN users u ON b.user_id = u.user_id
    ORDER BY b.booking_id DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Bookings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Manage Bookings</h2>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <?php if (count($bookings) === 0): ?>
    <div class="alert alert-info text-center">No bookings found.</div>
  <?php else: ?>
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>Hotel</th>
          <th>User</th>
          <th>Booking Date</th>
          <th>Travel Date</th>
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
            <td><?= htmlspecialchars($b['travel_date']) ?></td>
            <td>
              <form method="post" action="update_booking_status.php" class="d-flex align-items-center">
                <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                <select name="status" class="form-select form-select-sm me-2">
                  <option value="pending" <?= $b['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="confirmed" <?= $b['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                  <option value="cancelled" <?= $b['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-sm btn-success">
                  <i class="bi bi-check2-circle"></i>
                </button>
              </form>
            </td>
            <td>
              <?php if ($b['payment_status'] === 'paid'): ?>
                <span class="badge bg-success">Paid</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">Unpaid</span>
              <?php endif; ?>
            </td>
            <td>
             <a href="delete_bookings.php?id=<?= $b['booking_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
