<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-warning text-center p-3'>You must be logged in to view your bookings.</div>";
    include 'includes/footer.php';
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $cancel_id = (int) $_GET['cancel'];
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$cancel_id, $user_id]);
    echo "<div class='alert alert-success text-center'>Booking cancelled successfully.</div>";
}

$stmt = $conn->prepare("
    SELECT b.*, h.name AS hotel_name
    FROM bookings b 
    JOIN hotels h ON b.hotel_id = h.hotel_id 
    WHERE b.user_id = ?
    ORDER BY b.booking_id DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #2a2a72, #009ffd);
      color: #fff;
    }
    .container {
      margin-top: 40px;
    }
    table {
      background: #ffffff15;
      color: #fff;
      border-radius: 10px;
    }
    th, td {
      vertical-align: middle !important;
    }
    .table th {
      background: #0d6efd;
    }
    .btn-cancel {
      background-color: #ff4d4d;
      border: none;
    }
    .btn-cancel:hover {
      background-color: #e60000;
    }
  </style>
</head>
<body>
<div class="container">
  <h2 class="mb-4 text-center">My Bookings</h2>

  <?php if (count($bookings) === 0): ?>
      <div class="alert alert-light text-center text-dark">You have no bookings.</div>
  <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped text-white">
          <thead>
              <tr>
                  <th>Hotel</th>
                  <th>Check-In</th>
                  <th>Check-Out</th>
                  <th>Guests</th>
                  <th>Booked On</th>
                  <th>Action</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($bookings as $b): ?>
                  <tr>
                      <td><?= htmlspecialchars($b['hotel_name']) ?></td>
                      <td><?= htmlspecialchars($b['check_in']) ?></td>
                      <td><?= htmlspecialchars($b['check_out']) ?></td>
                      <td><?= htmlspecialchars($b['guests']) ?></td>
                      <td><?= htmlspecialchars($b['created_at']) ?></td>
                      <td>
                          <a href="my_bookings.php?cancel=<?= $b['booking_id'] ?>" class="btn btn-cancel btn-sm" onclick="return confirm('Cancel this booking?');">
                              <i class="bi bi-x-circle"></i> Cancel
                          </a>
                      </td>
                  </tr>
              <?php endforeach; ?>
          </tbody>
        </table>
      </div>
  <?php endif; ?>
</div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
