<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../config/db.php';

// ✅ Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE booking SET status = ? WHERE booking_id = ?");
    $stmt->execute([$new_status, $booking_id]);
}

// ✅ Fetch all bookings with hotel & user details
$stmt = $conn->query("SELECT b.*, u.name AS user_name, h.name AS hotel_name 
                      FROM booking b 
                      JOIN users u ON b.user_id = u.user_id 
                      JOIN hotels h ON b.hotel_id = h.hotel_id 
                      ORDER BY b.booking_date DESC");
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Bookings - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f1f3f5;
    }
    .container {
      margin-top: 40px;
    }
    .table th {
      background-color: #003049;
      color: white;
    }
    .form-select {
      min-width: 120px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4">Manage Bookings</h2>

  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Booking ID</th>
        <th>User</th>
        <th>Hotel</th>
        <th>Travel Date</th>
        <th>Booking Date</th>
        <th>Status</th>
        <th>Update</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $booking): ?>
        <tr>
          <td><?= $booking['booking_id'] ?></td>
          <td><?= htmlspecialchars($booking['user_name']) ?></td>
          <td><?= htmlspecialchars($booking['hotel_name']) ?></td>
          <td><?= $booking['travel_date'] ?></td>
          <td><?= $booking['booking_date'] ?></td>
          <td>
            <form method="POST" class="d-flex align-items-center gap-2">
              <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
              <select name="status" class="form-select form-select-sm">
                <?php foreach (['Pending', 'Confirmed', 'Cancelled', 'Completed'] as $status): ?>
                  <option value="<?= $status ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>>
                    <?= $status ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-sm btn-primary">Update</button>
            </form>
          </td>
          <td></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
