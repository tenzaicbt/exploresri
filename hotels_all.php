<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

$search = $_GET['search'] ?? '';
$province = $_GET['province'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 8;
$offset = ($page - 1) * $limit;

$is_logged_in = isset($_SESSION['user_id']);

// Fetch hotels
$stmt = $conn->query("SELECT * FROM hotels ORDER BY hotel_id DESC");
$hotels = $stmt->fetchAll();

// Fetch reviews for analysis
$reviewData = [];
$sql = "SELECT hotel_id, rating FROM reviews";
$stmt = $conn->query($sql);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($reviews as $r) {
  $hid = $r['hotel_id'];
  $rating = (int)$r['rating'];
  if (!isset($reviewData[$hid])) {
    $reviewData[$hid] = ['total' => 0, 'positive' => 0];
  }
  $reviewData[$hid]['total']++;
  if ($rating >= 4) {
    $reviewData[$hid]['positive']++;
  }
}

// Calculate percentage
foreach ($reviewData as $hid => $data) {
  $reviewData[$hid]['percentage'] = $data['total'] > 0
    ? round(($data['positive'] / $data['total']) * 100, 1)
    : 0;
}
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

    .hotel-card-modern {
      position: relative;
      border-radius: 18px;
      overflow: hidden;
      background: #1b2735;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.5);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .hotel-card-modern:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6);
    }

    .hotel-card-modern img {
      width: 100%;
      height: 170px;
      object-fit: cover;
    }

    .image-overlay {
      position: absolute;
      top: 0;
      left: 0;
      height: 170px;
      width: 100%;
      background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.8));
    }

    .hotel-card-modern .card-body {
      padding: 1rem;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .hotel-card-modern .card-title {
      color: #fff;
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.4rem;
    }

    .hotel-card-modern .card-text {
      color: #bfbfbf;
      font-size: 0.85rem;
      margin-bottom: 0.6rem;
    }

    .hotel-card-modern .price {
      color: #ffdd57;
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 0.6rem;
    }

    .hotel-card-modern .small {
      font-size: 0.8rem;
      color: #ccc;
    }

    .rating-stars i {
      font-size: 0.85rem;
    }

    .btn-book {
      font-size: 0.85rem;
      border-radius: 30px;
      background-color: transparent;
      border: 1px solid #f1c40f;
      color: rgb(255, 255, 255);
      transition: all 0.3s ease;
    }

    .btn-book:hover {
      background-color: rgb(255, 255, 255);
      color: #1e1e2f;
    }

    .no-hotels {
      text-align: center;
      font-size: 1.2rem;
      color: #ccc;
      margin-top: 50px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>FIND COMFORTABLE HOTELS</h1>

    <div class="row g-4">
      <?php if (count($hotels) > 0): ?>
        <?php foreach ($hotels as $hotel): ?>
          <?php
            $rid = $hotel['hotel_id'];
            $rev = $reviewData[$rid] ?? ['total' => 0, 'positive' => 0, 'percentage' => 0];
          ?>
          <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card hotel-card-modern h-100 position-relative">
              <img src="images/<?php echo htmlspecialchars($hotel['image']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
              <div class="image-overlay"></div>

              <div class="card-body text-start d-flex flex-column">
                <h5 class="card-title text-warning"><?php echo htmlspecialchars($hotel['name']); ?></h5>
                <p class="card-text mb-1"><i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>
                <p class="price mb-1">$ <?php echo number_format($hotel['price_per_night'], 2); ?> / night</p>

                <div class="text-light mb-2 small">
                  <strong><?= $rev['total'] ?></strong> reviews,
                  <strong><?= $rev['positive'] ?></strong> positive
                  (<span class="text-warning"><?= $rev['percentage'] ?>%</span>)
                </div>

                <!-- <div class="d-flex align-items-center mb-3">
                  <span class="badge bg-warning text-dark me-2"><?php echo number_format($hotel['rating'], 1); ?></span>
                  <div class="text-warning rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <i class="bi <?= $i <= round($hotel['rating']) ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                    <?php endfor; ?>
                  </div>
                </div> -->

                <?php if ($is_logged_in): ?>
                  <a href="book.php?hotel_id=<?= $hotel['hotel_id']; ?>" class="btn btn-book w-100 mt-auto">Book Now</a>
                <?php else: ?>
                  <a href="/exploresri/user/login.php" class="btn btn-book w-100 mt-auto">Login to Book</a>
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

  <?php include 'includes/footer.php'; ?>
</body>

</html>
