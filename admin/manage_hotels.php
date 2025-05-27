<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

$stmt = $conn->prepare("SELECT hotels.*, destinations.name AS destination_name 
                        FROM hotels 
                        LEFT JOIN destinations ON hotels.destination_id = destinations.destination_id 
                        ORDER BY hotel_id DESC");
$stmt->execute();
$hotels = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Hotels</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Manage Hotels</h2>
  <a href="add_hotel.php" class="btn btn-primary mb-3">Add New Hotel</a>
  <table class="table table-bordered table-hover">
    <thead class="table-dark">
      <tr>
        <!-- <th>Image</th> -->
        <th>Hotel Name</th>
        <th>Location</th>
        <th>Price/Night</th>
        <th>Rating</th>
        <th>Destination</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($hotels as $hotel): ?>
        <tr>
          <!-- <td>
            <img src="../uploads/<?= htmlspecialchars($hotel['image']) ?>" width="100" height="70" style="object-fit:cover; border-radius:6px;">
          </td> -->
          <td><?= htmlspecialchars($hotel['name']) ?></td>
          <td><?= htmlspecialchars($hotel['location']) ?></td>
          <td>$<?= number_format($hotel['price_per_night'], 2) ?></td>
          <td><?= htmlspecialchars($hotel['rating']) ?> ⭐</td>
          <td><?= htmlspecialchars($hotel['destination_name']) ?></td>
          <td>
            <a href="edit_hotel.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="delete_hotel.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this hotel?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
