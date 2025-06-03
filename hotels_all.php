<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

$is_logged_in = isset($_SESSION['user_id']);

$stmt = $conn->query("SELECT * FROM hotels ORDER BY hotel_id DESC");
$hotels = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Hotels - ExploreSri</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #ffffff;
      min-height: 100vh;
      overflow-x: hidden;
    }

    h1 {
      font-size: 2.5rem;
      font-weight: 600;
      margin: 40px 0;
      text-align: center;
      color: #f1c40f;
    }

    .hotel-card-dark {
      background: rgba(255, 255, 255, 0.06);
      border-radius: 16px;
      overflow: hidden;
      backdrop-filter: blur(10px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
      transition: transform 0.3s ease;
      padding: 0;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .hotel-card-dark:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.6);
    }

    .hotel-card-dark img {
      height: 150px;
      object-fit: cover;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      width: 100%;
      flex-shrink: 0;
    }

    .hotel-card-dark .card-body {
      padding: 1rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .card-title {
      color: #fff;
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 0.3rem;
    }

    .card-text {
      font-size: 0.85rem;
      color: #bbbbbb;
      margin-bottom: 0.75rem;
    }

    .btn-outline-light {
      font-size: 0.85rem;
      padding: 6px 14px;
      border-radius: 30px;
      margin-top: auto;
    }

    .badge {
      font-size: 0.75rem;
      padding: 0.3em 0.6em;
    }

    .no-hotels {
      text-align: center;
      font-size: 1.2rem;
      color: #ccc;
      margin-top: 50px;
    }

    .rating-stars i {
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>FIND COMFORTABLE HOTELS</h1>

  <div class="row g-4">
    <?php if (count($hotels) > 0): ?>
      <?php foreach ($hotels as $hotel): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card hotel-card-dark position-relative h-100">
            <img src="images/<?php echo htmlspecialchars($hotel['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($hotel['name']); ?>" />

            <div class="card-body text-start d-flex flex-column">
              <h5 class="card-title"><?php echo htmlspecialchars($hotel['name']); ?></h5>
              <p class="card-text"><i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>

              <div class="d-flex align-items-center mb-2">
                <span class="badge bg-warning text-dark me-2"><?php echo number_format($hotel['rating'], 1); ?></span>
                <div class="text-warning rating-stars">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi <?= $i <= round($hotel['rating']) ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                  <?php endfor; ?>
                </div>
              </div>

              <p class="text-light fw-semibold mb-2">Rs. <?php echo htmlspecialchars($hotel['price_per_night']); ?> / night</p>

              <?php if ($is_logged_in): ?>
                <a href="book.php?hotel_id=<?= $hotel['hotel_id']; ?>" class="btn btn-outline-light w-100">Book Now</a>
              <?php else: ?>
                <a href="/exploresri/user/login.php" class="btn btn-outline-light w-100">Login to Book</a>
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
