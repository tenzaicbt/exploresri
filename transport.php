<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

$is_logged_in = isset($_SESSION['user_id']);

// Join vehicles with company data
$sql = "SELECT v.*, c.company_name, c.logo
        FROM vehicles v
        JOIN transport_companies c ON v.company_id = c.company_id
        ORDER BY v.vehicle_id DESC";
$stmt = $conn->query($sql);
$vehicles = $stmt->fetchAll();
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

    .vehicle-card-dark {
      background: rgba(255, 255, 255, 0.06);
      border-radius: 16px;
      overflow: hidden;
      backdrop-filter: blur(10px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
      transition: transform 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
      position: relative;
    }

    .vehicle-card-dark:hover {
      transform: translateY(-4px);
    }

    .vehicle-card-dark img.vehicle-image {
      height: 150px;
      object-fit: cover;
      width: 100%;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .vehicle-card-dark .card-body {
      padding: 1rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .card-title {
      color: #fff;
      font-size: 1.1rem;
      font-weight: 600;
    }

    .card-text {
      font-size: 0.85rem;
      color: #bbbbbb;
    }

    .btn-outline-light {
      font-size: 0.85rem;
      padding: 6px 14px;
      border-radius: 30px;
      margin-top: auto;
    }

    .no-vehicles {
      text-align: center;
      font-size: 1.2rem;
      color: #ccc;
      margin-top: 50px;
    }

    .company-logo-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 50px;
      height: 50px;
      overflow: hidden;
      border-radius: 0;
      /* no rounding */
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .company-logo-badge img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 0;
      display: block;
    }
  </style>
</head>

<body>

  <div class="container">
    <h1>RENT TRANSPORT VEHICLES</h1>

    <div class="row g-4">
      <?php if (count($vehicles) > 0): ?>
        <?php foreach ($vehicles as $vehicle): ?>
          <?php
          // Vehicle image
          $vehicle_img_path = "uploads/vehicles/" . $vehicle['image'];
          $vehicle_img = (!empty($vehicle['image']) && file_exists(__DIR__ . '/' . $vehicle_img_path))
            ? "/exploresri/" . $vehicle_img_path
            : "/exploresri/assets/default-vehicle.jpg";

          // Company logo
          $company_logo_path = $vehicle['logo'];
          $company_logo = (!empty($company_logo_path) && file_exists(__DIR__ . '/' . $company_logo_path))
            ? "/exploresri/" . $company_logo_path
            : "/exploresri/assets/default-company.png";
          ?>
          <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card vehicle-card-dark h-100">
              <img src="<?= htmlspecialchars($vehicle_img) ?>" class="vehicle-image" alt="<?= htmlspecialchars($vehicle['model']) ?>" />

              <div class="company-logo-badge" title="<?= htmlspecialchars($vehicle['company_name']) ?>">
                <img src="<?= htmlspecialchars($company_logo) ?>" alt="<?= htmlspecialchars($vehicle['company_name']) ?>" />
              </div>

              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($vehicle['model']) ?></h5>
                <p class="card-text"><i class="bi bi-truck-front-fill"></i> Type: <?= htmlspecialchars($vehicle['type']) ?></p>
                <p class="card-text"><i class="bi bi-people-fill"></i> Capacity: <?= htmlspecialchars($vehicle['capacity']) ?> persons</p>
                <p class="text-light fw-semibold mb-2">USD. <?= htmlspecialchars($vehicle['rental_price']) ?> / day</p>

                <?php if ($is_logged_in): ?>
                  <a href="vehicle_details.php?vehicle_id=<?= (int)$vehicle['vehicle_id'] ?>" class="btn btn-outline-light w-100">Rent Now</a>
                <?php else: ?>
                  <a href="/exploresri/user/login.php" class="btn btn-outline-light w-100">Login to Rent</a>
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

</body>

</html>

<?php include 'includes/footer.php'; ?>