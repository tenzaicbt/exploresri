<?php
include '../config/db.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

$places = $conn->query("SELECT * FROM destinations")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Places</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h2 class="mb-4">Manage Places</h2>
    <a href="add_place.php" class="btn btn-primary mb-3">Add New Place</a>
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>Name</th>
          <th>Location</th>
          <th>Category</th>
          <th>Province</th>
          <th>Top Attractions</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($places as $place): ?>
          <tr>
            <td><?= htmlspecialchars($place['name']) ?></td>
            <td><?= htmlspecialchars($place['location']) ?></td>
            <td><?= htmlspecialchars($place['category']) ?></td>
            <td><?= htmlspecialchars($place['province']) ?></td>
            <td><?= htmlspecialchars($place['top_attractions']) ?></td>
            <td>
              <a href="edit_place.php?id=<?= $place['destination_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="delete_place.php?id=<?= $place['destination_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-3">
      <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
    <div class="container mt-4"></div>
  </div>
</body>

</html>