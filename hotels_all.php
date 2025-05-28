<?php
include 'config/db.php';
include 'includes/header.php';

$is_logged_in = isset($_SESSION['user_id']);

$stmt = $conn->query("SELECT * FROM hotels ORDER BY hotel_id DESC");
$hotels = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hotels - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #003049, #669bbc);
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      background-color: rgba(255, 255, 255, 0.1);
      border: none;
      border-radius: 15px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    .card img {
      height: 200px;
      object-fit: cover;
    }
    .card-title {
      color: #fff;
      font-size: 1.2rem;
      font-weight: bold;
    }
    .card-text {
      color: #e0e0e0;
    }
    .btn-primary {
      background-color: #fcbf49;
      border: none;
      color: #000;
      font-weight: 500;
    }
    .btn-primary:hover {
      background-color: #f7b733;
    }
    h1 {
      font-size: 2.5rem;
      font-weight: 600;
      margin-bottom: 2rem;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <h1 class="text-center">Find Comfortable Hotels</h1>

  <div class="row">
    <?php if (count($hotels) > 0): ?>
      <?php foreach ($hotels as $hotel): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="images/<?php echo htmlspecialchars($hotel['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
            <div class="card-body text-center">
              <h5 class="card-title"><?php echo htmlspecialchars($hotel['name']); ?></h5>
              <p class="card-text"><?php echo htmlspecialchars($hotel['location']); ?></p>
              <p class="card-text">Rs. <?php echo htmlspecialchars($hotel['price_per_night']); ?> / night</p>
              <p class="card-text text-warning">★ <?php echo htmlspecialchars($hotel['rating']); ?> / 5</p>
              <?php if ($is_logged_in): ?>
                <a href="book.php?hotel_id=<?= $hotel['hotel_id']; ?>" class="btn btn-primary mt-2">Book Now</a>
              <?php else: ?>
                <a href="/exploresri/user/login.php" class="btn btn-warning mt-2">Login to Book</a>
              <?php endif; ?>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No hotels available right now.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>
