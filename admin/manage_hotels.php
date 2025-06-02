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
  <h2 class="mb-4">Manage Hotels</h2>
  <a href="add_hotel.php" class="btn btn-primary mb-3">Add New Hotel</a>

  <table class="table table-bordered table-hover">
    <thead class="table-dark">
      <tr>
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
          <td><?= htmlspecialchars($hotel['name']) ?></td>
          <td><?= htmlspecialchars($hotel['location']) ?></td>
          <td>$<?= number_format($hotel['price_per_night'], 2) ?></td>
          <td><?= htmlspecialchars($hotel['rating']) ?> ⭐</td>
          <td><?= htmlspecialchars($hotel['destination_name']) ?></td>
          <td>
            <a href="edit_hotel.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-warning btn-sm mb-1">Edit</a>
            <a href="delete_hotel.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Are you sure you want to delete this hotel?')">Delete</a>
            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= $hotel['hotel_id'] ?>">View</button>
          </td>
        </tr>

        <!-- View Modal -->
        <div class="modal fade" id="viewModal<?= $hotel['hotel_id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $hotel['hotel_id'] ?>" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel<?= $hotel['hotel_id'] ?>">Hotel Details - <?= htmlspecialchars($hotel['name']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($hotel['description'])) ?></p>
                <p><strong>Facilities:</strong><br><?= nl2br(htmlspecialchars($hotel['facilities'])) ?></p>
                <p><strong>Popular Features:</strong><br><?= nl2br(htmlspecialchars($hotel['popular_features'])) ?></p>

                <p><strong>Map:</strong><br>
                  <?php if (!empty($hotel['map_embed_link'])): ?>
                    <iframe src="<?= htmlspecialchars($hotel['map_embed_link']) ?>" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                  <?php else: ?>
                    <em>No map available</em>
                  <?php endif; ?>
                </p>

                <p><strong>Gallery:</strong><br>
                  <?php
                    $gallery = explode(',', $hotel['image_gallery']);
                    foreach ($gallery as $img) {
                        if (!empty($img)) {
                            echo '<img src="../images/' . htmlspecialchars($img) . '" alt="Image">';
                        }
                    }
                  ?>
                </p>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </tbody>
  </table>

  <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
