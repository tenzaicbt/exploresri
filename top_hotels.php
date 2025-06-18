<?php
include 'config/db.php';
include 'includes/header.php';

$stmt = $conn->query("SELECT * FROM hotels ORDER BY rating DESC LIMIT 10");
$hotels = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Top 10 Hotels in Sri Lanka</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #fff;
    }

    .card {
      background: #ffffff10;
      color: #fff;
      border: none;
      backdrop-filter: blur(6px);
    }

    .card img {
      height: 200px;
      object-fit: cover;
    }

    .btn-book {
      background-color: #ffc107;
      border: none;
      color: #000;
    }

    .btn-book:hover {
      background-color: #e0a800;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h1 class="text-center mb-4">Top 10 Hotels in Sri Lanka</h1>
    <div class="row">
      <?php foreach ($hotels as $hotel): ?>
        <div class="col-md-6 mb-4">
          <div class="card shadow">
            <img src="images/<?= htmlspecialchars($hotel['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($hotel['name']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($hotel['name']); ?></h5>
              <p class="card-text"><?= htmlspecialchars($hotel['description']); ?></p>
              <p><strong>Price:</strong> $<?= number_format($hotel['price'], 2); ?></p>
              <p><strong>Location:</strong> <?= htmlspecialchars($hotel['address']); ?></p>
              <div class="d-flex justify-content-between mt-3">
                <a href="hotel_detail.php?hotel_id=<?= $hotel['hotel_id']; ?>" class="btn btn-outline-light btn-sm">View Details</a>
                <a href="booking_form.php?hotel_id=<?= $hotel['hotel_id']; ?>" class="btn btn-book btn-sm">Book Now</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>

</html>

<?php include 'includes/footer.php'; ?>