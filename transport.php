<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

$is_logged_in = isset($_SESSION['user_id']);

// Join vehicles with company data where company is active
$sql = "SELECT v.*, c.company_name, c.logo
        FROM vehicles v
        JOIN transport_companies c ON v.company_id = c.company_id
        WHERE c.status = 'active'
        ORDER BY v.vehicle_id DESC";
$stmt = $conn->query($sql);
$vehicles = $stmt->fetchAll();

// Fetch reviews
$reviewData = [];
$reviewStmt = $conn->query("SELECT vehicle_id, rating FROM vehicle_reviews");
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($reviews as $r) {
  $vid = $r['vehicle_id'];
  $rating = (int)$r['rating'];
  if (!isset($reviewData[$vid])) {
    $reviewData[$vid] = ['total' => 0, 'positive' => 0];
  }
  $reviewData[$vid]['total']++;
  if ($rating >= 4) {
    $reviewData[$vid]['positive']++;
  }
}

foreach ($reviewData as $vid => $data) {
  $reviewData[$vid]['percentage'] = $data['total'] > 0
    ? round(($data['positive'] / $data['total']) * 100, 1)
    : 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Transport - ExploreSri</title>
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

    .vehicle-card-modern {
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

    .vehicle-card-modern:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6);
    }

    .vehicle-card-modern img.vehicle-image {
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
      pointer-events: none;
    }

    .vehicle-card-modern .card-body {
      padding: 1rem;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .vehicle-card-modern .card-title {
      color: #f1c40f;
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.4rem;
    }

    .vehicle-card-modern .card-text {
      color: #bfbfbf;
      font-size: 0.85rem;
      margin-bottom: 0.6rem;
    }

    .price {
      color: #ffdd57;
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 0.6rem;
    }

    .btn-rent {
      font-size: 0.85rem;
      border-radius: 30px;
      background-color: transparent;
      border: 1px solid #f1c40f;
      color: #fff;
      transition: all 0.3s ease;
    }

    .btn-rent:hover {
      background-color: #f1c40f;
      color: #1e1e2f;
    }

    .company-logo-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 50px;
      height: 50px;
      overflow: hidden;
      border-radius: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .company-logo-badge img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .no-vehicles {
      text-align: center;
      font-size: 1.2rem;
      color: #ccc;
      margin-top: 50px;
    }

    .review-info {
      font-size: 0.8rem;
      color: #ccc;
    }

    .review-info span {
      color: #f1c40f;
    }
  </style>
</head>

<body>

  <div class="container">
    <h1>RENT TRANSPORT VEHICLES</h1>

    <div class="row g-4">
      <?php if (count($vehicles) > 0): ?>
        <?php foreach ($vehicles as $vehicle):
          $vid = $vehicle['vehicle_id'];
          $rev = $reviewData[$vid] ?? ['total' => 0, 'positive' => 0, 'percentage' => 0];

          $vehicle_img_path = "uploads/vehicles/" . $vehicle['image'];
          $vehicle_img = (!empty($vehicle['image']) && file_exists(__DIR__ . '/' . $vehicle_img_path))
            ? "/exploresri/" . $vehicle_img_path
            : "/exploresri/assets/default-vehicle.jpg";

          $company_logo_path = $vehicle['logo'];
          $company_logo = (!empty($company_logo_path) && file_exists(__DIR__ . '/' . $company_logo_path))
            ? "/exploresri/" . $company_logo_path
            : "/exploresri/assets/default-company.png";
        ?>
          <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card vehicle-card-modern h-100 position-relative">
              <img src="<?= htmlspecialchars($vehicle_img) ?>" class="vehicle-image" alt="<?= htmlspecialchars($vehicle['model']) ?>" />
              <div class="image-overlay"></div>

              <div class="company-logo-badge" title="<?= htmlspecialchars($vehicle['company_name']) ?>">
                <img src="<?= htmlspecialchars($company_logo) ?>" alt="<?= htmlspecialchars($vehicle['company_name']) ?>" />
              </div>

              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?= htmlspecialchars($vehicle['model']) ?></h5>
                <p class="card-text"><i class="bi bi-truck-front-fill"></i> Type: <?= htmlspecialchars($vehicle['type']) ?></p>
                <p class="card-text"><i class="bi bi-people-fill"></i> Capacity: <?= htmlspecialchars($vehicle['capacity']) ?> persons</p>
                <p class="price">USD <?= htmlspecialchars($vehicle['rental_price']) ?> / day</p>

                <div class="review-info mb-2">
                  <strong><?= $rev['total'] ?></strong> reviews,
                  <strong><?= $rev['positive'] ?></strong> positive
                  (<span><?= $rev['percentage'] ?>%</span>)
                </div>

                <?php if ($is_logged_in): ?>
                  <a href="vehicle_details.php?vehicle_id=<?= (int)$vehicle['vehicle_id'] ?>" class="btn btn-rent mt-auto">Rent Now</a>
                <?php else: ?>
                  <a href="/exploresri/user/login.php" class="btn btn-rent mt-auto">Login to Rent</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="no-vehicles">No vehicles available at the moment.</p>
      <?php endif; ?>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
</body>

</html>
