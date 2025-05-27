<?php
include '../config/db.php';

$stmt = $conn->query("SELECT hotels.*, destinations.name AS destination FROM hotels JOIN destinations ON hotels.destination_id = destinations.destination_id ORDER BY hotel_id DESC");
$hotels = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Hotels</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2>All Hotels</h2>
  <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Hotel deleted successfully.</div>
  <?php endif; ?>
  <a href="add_hotel.php" class="btn btn-success mb-3">+ Add Hotel</a>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Location</th>
        <th>Destination</th>
        <th>Price</th>
        <th>Rating</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($hotels as $hotel): ?>
        <tr>
          <td><?= htmlspecialchars($hotel['name']) ?></td>
          <td><?= htmlspecialchars($hotel['location']) ?></td>
          <td><?= htmlspecialchars($hotel['destination']) ?></td>
          <td>Rs. <?= htmlspecialchars($hotel['price_per_night']) ?></td>
          <td><?= htmlspecialchars($hotel['rating']) ?></td>
          <td>
            <a href="edit_hotel.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
            <a href="delete_hotel.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this hotel?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
