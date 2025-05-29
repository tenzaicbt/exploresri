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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #ffffff;
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
    }

    h1 {
      font-size: 2.5rem;
      font-weight: 600;
      margin: 40px 0;
      text-align: center;
      color: #f1c40f;
    }

    .hotel-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(10px);
      overflow: hidden;
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      animation: fadeInUp 0.7s ease;
    }

    .hotel-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
    }

    .hotel-card img {
      height: 220px;
      object-fit: cover;
      border-bottom: 2px solid #f1c40f;
    }

    .hotel-card .card-body {
      text-align: center;
    }

    .card-title {
      color: #fff;
      font-size: 1.3rem;
      font-weight: 600;
    }

    .card-text {
      color: #dcdcdc;
    }

    .btn-custom {
      background-color: #f1c40f;
      color: #000;
      border: none;
      padding: 8px 20px;
      font-weight: bold;
      border-radius: 50px;
      margin-top: 10px;
      transition: all 0.3s ease;
    }

    .btn-custom:hover {
      background-color: #ffe57f;
      transform: scale(1.05);
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(60px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .no-hotels {
      text-align: center;
      font-size: 1.2rem;
      color: #ccc;
    }
  </style>
</head>
<body>

<div class="container">
  <h1 class="fade-up"><i class=""></i> FIND COMFORTABLE HOTELS</h1>

  <div class="row g-4">
    <?php if (count($hotels) > 0): ?>
      <?php foreach ($hotels as $hotel): ?>
        <div class="col-md-4">
          <div class="card hotel-card h-100">
            <img src="images/<?php echo htmlspecialchars($hotel['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($hotel['name']); ?></h5>
              <p class="card-text"><i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>
              <p class="card-text">Rs. <?php echo htmlspecialchars($hotel['price_per_night']); ?> / night</p>
              <p class="card-text text-warning">★ <?php echo htmlspecialchars($hotel['rating']); ?> / 5</p>

              <?php if ($is_logged_in): ?>
                <a href="book.php?hotel_id=<?= $hotel['hotel_id']; ?>" class="btn btn-custom">Book Now</a>
              <?php else: ?>
                <a href="/exploresri/user/login.php" class="btn btn-custom">Login to Book</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="no-hotels">No hotels available right now.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>
