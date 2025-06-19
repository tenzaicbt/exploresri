<?php
ob_start();
session_start();
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$vehicle_id = $_GET['vehicle_id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
  echo "Vehicle not found.";
  exit;
}

// Fetch company info for the vehicle
$company_id = $vehicle['company_id'];
$stmt = $conn->prepare("SELECT * FROM transport_companies WHERE company_id = ?");
$stmt->execute([$company_id]);
$company = $stmt->fetch();

// Process booking
$bookingError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_vehicle'])) {
  $start = $_POST['rental_start_date'] ?? '';
  $end = $_POST['rental_end_date'] ?? '';
  $user_id = $_SESSION['user_id'];

  if (!$start || !$end) {
    $bookingError = "Please select both start and end dates.";
  } elseif ($end <= $start) {
    $bookingError = "End date must be after start date.";
  } elseif ($start < date('Y-m-d')) {
    $bookingError = "Start date cannot be in the past.";
  } else {
    $days = (new DateTime($start))->diff(new DateTime($end))->days;
    $total_price = $vehicle['rental_price'] * max(1, $days);

    $stmt = $conn->prepare("INSERT INTO vehicle_bookings (vehicle_id, user_id, booking_start, booking_end, total_price) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$vehicle_id, $user_id, $start, $end, $total_price])) {
      $booking_id = $conn->lastInsertId();
      header("Location: vehicle_payment.php?booking_id=$booking_id");
      exit;
    } else {
      $bookingError = "Failed to book. Please try again.";
    }
  }
}

// Submit review
if (isset($_POST['submit_review'])) {
  $rating = $_POST['rating'];
  $comment = trim($_POST['comment']);
  $user_id = $_SESSION['user_id'];

  if (!empty($rating) && !empty($comment)) {
    $stmt = $conn->prepare("INSERT INTO vehicle_reviews (user_id, vehicle_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $vehicle_id, $rating, $comment]);
    header("Location: vehicle_book.php?vehicle_id=" . $vehicle_id);
    exit;
  }
}

// Prepare image gallery and features
$imageGallery = array_filter(array_map('trim', explode(',', $vehicle['image_gallery'] ?? '')));
$features = array_filter(array_map('trim', explode(',', $vehicle['features'] ?? '')));

// Fetch reviews
$reviewStmt = $conn->prepare("SELECT vr.*, u.name FROM vehicle_reviews vr JOIN users u ON vr.user_id = u.user_id WHERE vr.vehicle_id = ? ORDER BY vr.created_at DESC");
$reviewStmt->execute([$vehicle_id]);
$reviews = $reviewStmt->fetchAll();

// Fetch 3 other vehicles (optional UI feature)
$otherVehiclesStmt = $conn->prepare("SELECT * FROM vehicles WHERE vehicle_id != ? ORDER BY RAND() LIMIT 3");
$otherVehiclesStmt->execute([$vehicle_id]);
$otherVehicles = $otherVehiclesStmt->fetchAll();

ob_end_flush();
?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


<?php ob_end_flush(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($vehicle['model']) ?> - Rent Now</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #ffffff;
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
    }

    .container-main {
      max-width: 1200px;
      margin: 40px auto;
    }

    .carousel img {
      height: 400px;
      object-fit: cover;
      border-radius: 15px;
      border-bottom: 3px solid #f1c40f;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    }

    .info-card,
    .review-box,
    .sidebar-card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
      margin-top: 20px;
      transition: transform 0.3s ease;
    }

    .info-card:hover,
    .review-box:hover,
    .sidebar-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
    }

    .features span {
      background-color: rgba(255, 255, 255, 0.05);
      color: #f1c40f;
      padding: 6px 14px;
      margin: 5px;
      border-radius: 30px;
      display: inline-block;
      font-size: 14px;
      font-weight: 500;
    }

    .sidebar-card {
      background: rgba(255, 255, 255, 0.05);
    }

    .btn-book {
      background-color: #f1c40f;
      color: #000;
      border: none;
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .btn-book:hover {
      background-color: #ffe57f;
      transform: scale(1.05);
    }

    .badge.bg-info {
      background-color: #f1c40f !important;
      color: #000;
    }

    a {
      color: #f1c40f;
      transition: color 0.2s ease;
    }

    a:hover {
      color: #ffe57f;
      text-decoration: underline;
    }

    .form-control,
    .btn-secondary {
      background-color: rgba(27, 39, 53, 0.7);
      color: #e0e0e0;
      border: 1px solid #444;
      backdrop-filter: blur(5px);
    }

    .form-control:focus {
      border-color: #f1c40f;
      box-shadow: 0 0 0 0.2rem rgba(241, 196, 15, 0.25);
    }

    .card-title {
      font-size: 1.3rem;
      color: #f1c40f;
      font-weight: 600;
    }

    .card-text {
      color: #dcdcdc;
    }

    .info-card.bg-dark {
      background-color: rgba(30, 30, 30, 0.6);
      border: 1px solid #333;
      border-radius: 15px;
    }

    .info-card.bg-dark p {
      margin-bottom: 10px;
      color: #ddd;
    }

    .booking-container {
      max-width: 500px;
      margin: 40px auto;
      padding: 30px;
      background: linear-gradient(145deg, #1f1f1f, 255, 255, 255, 0.05);
      border-radius: 15px;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      animation: fadeInUp 0.8s ease forwards;
    }

    .booking-container h2 {
      text-align: center;
      font-weight: 600;
      font-size: 24px;
      margin-bottom: 25px;
      color: #fcbf49;
    }

    .booking-container label {
      font-weight: 500;
      display: block;
      margin-bottom: 6px;
      margin-top: 15px;
    }

    .booking-container input[type="date"] {
      width: 100%;
      padding: 10px 14px;
      border-radius: 8px;
      border: none;
      background: #3a3a3a;
      color: #fff;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    .booking-container input[type="date"]:focus {
      outline: none;
      background: #444;
    }

    .booking-container button {
      width: 100%;
      padding: 12px;
      margin-top: 25px;
      border: none;
      border-radius: 8px;
      background: #fcbf49;
      color: #000;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .booking-container button:hover {
      background: #fcbf49;
      transform: translateY(-2px);
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 600px) {
      .booking-container {
        margin: 20px;
        padding: 20px;
      }
    }

    .sidebar-card {
      background-color: #1e293b;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
      margin-top: 20px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      color: #f1c40f;
      /* Gold-ish text color for contrast */
    }

    .sidebar-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
    }

    /* Links inside sidebar-card */
    .sidebar-card a {
      color: #f1c40f;
      transition: color 0.2s ease;
      text-decoration: none;
    }

    .sidebar-card a:hover {
      color: #ffe57f;
      text-decoration: underline;
    }

    /* Buttons inside sidebar-card, if any */
    .sidebar-card .btn-book {
      background-color: #f1c40f;
      color: #000;
      border: none;
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: bold;
      transition: all 0.3s ease;
      cursor: pointer;
      display: inline-block;
    }

    .sidebar-card .btn-book:hover {
      background-color: #ffe57f;
      transform: scale(1.05);
    }

    /* Feature tags inside sidebar, if you have those */
    .sidebar-card .features span {
      background-color: rgba(241, 196, 15, 0.1);
      /* subtle gold tinted background */
      color: #f1c40f;
      padding: 6px 14px;
      margin: 5px 5px 5px 0;
      border-radius: 30px;
      display: inline-block;
      font-size: 14px;
      font-weight: 500;
    }

    .vehicle-highlights {
      background: linear-gradient(to right, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.02));
      border-radius: 16px;
      padding: 25px 22px;
      color: #eaeaea;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
      transition: transform 0.3s ease;
    }

    .vehicle-highlights-title {
      font-weight: 600;
      font-size: 1.2rem;
      border-bottom: 1px solid rgba(241, 196, 15, 0.4);
      padding-bottom: 10px;
      color: #f1c40f;
      letter-spacing: 0.5px;
      margin-bottom: 1.5rem;
    }

    .vehicle-highlights-list {
      list-style: none;
      padding-left: 0;
      margin: 0;
      font-size: 0.95rem;
    }

    .vehicle-highlights-list li {
      margin-bottom: 12px;
    }

    .vehicle-highlights-list li:last-child {
      margin-bottom: 0;
    }

    .vehicle-highlights-list .label {
      opacity: 0.8;
    }

    .vehicle-highlights-list .value {
      color: #ffffff;
      margin-left: 6px;
    }
  </style>
</head>

<body>
  <div class="container container-main">
    <div class="row">
      <!-- Left content: carousel, details, features, description, reviews -->
      <div class="col-lg-8">
        <div id="vehicleCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php if (!empty($imageGallery)): ?>
              <?php foreach ($imageGallery as $index => $img): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                  <img src="uploads/vehicles/<?= htmlspecialchars($img) ?>" class="d-block w-100" alt="Vehicle Image <?= $index + 1 ?>" />
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="carousel-item active">
                <img src="assets/images/default_vehicle.jpg" class="d-block w-100" alt="Default Vehicle Image" />
              </div>
            <?php endif; ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>

        <div class="info-card mt-4">
          <h2><?= htmlspecialchars($vehicle['model']) ?></h2>
          <h5 class="text-warning fw-bold">$<?= number_format($vehicle['rental_price'], 2) ?> / day</h5>
          <p><?= nl2br(htmlspecialchars($vehicle['description'])) ?></p>

          <div class="features mt-3">
            <?php foreach ($features as $feature): ?>
              <span><?= htmlspecialchars($feature) ?></span>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="info-card mt-5">
          <h3>Customer Reviews</h3>
          <?php if (count($reviews) === 0): ?>
            <p>No reviews yet. Be the first to review!</p>
          <?php else: ?>
            <?php foreach ($reviews as $review): ?>
              <div class="review-box">
                <strong><?= htmlspecialchars($review['name']) ?></strong> - <small><?= date('F j, Y', strtotime($review['created_at'])) ?></small>
                <div>
                  <?php for ($i = 0; $i < 5; $i++): ?>
                    <?php if ($i < $review['rating']): ?>
                      <i class="bi bi-star-fill text-warning"></i>
                    <?php else: ?>
                      <i class="bi bi-star text-warning"></i>
                    <?php endif; ?>
                  <?php endfor; ?>
                </div>
                <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="review-box mt-4">
              <h5>Add Your Review</h5>
              <form method="post">
                <div class="mb-3">
                  <label for="rating" class="form-label">Rating</label>
                  <select name="rating" id="rating" class="form-select" required>
                    <option value="">Select</option>
                    <option value="5">★★★★★ - Excellent</option>
                    <option value="4">★★★★☆ - Good</option>
                    <option value="3">★★★☆☆ - Average</option>
                    <option value="2">★★☆☆☆ - Poor</option>
                    <option value="1">★☆☆☆☆ - Terrible</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="comment" class="form-label">Comment</label>
                  <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
              </form>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Right Sidebar -->
      <div class="col-md-4">
        <!-- Booking Card -->
        <div class="sidebar-card p-4 bg-dark rounded-4 shadow-lg">
          <h4 class="text-warning fw-bold mb-4"><i class=""></i>Book This Vehicle</h4>

          <?php if ($vehicle['availability'] == 0): ?>
            <div class="alert alert-danger d-flex align-items-center p-3 shadow-sm rounded-3">
              <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
              <div>
                <strong>This vehicle is unavailable.</strong><br>
                Please choose a different vehicle or check back later.
              </div>
            </div>
          <?php else: ?>
            <?php if (!empty($bookingError)): ?>
              <div class="alert alert-danger d-flex align-items-center p-3 mt-2 shadow-sm rounded-3">
                <i class="bi bi-x-circle-fill me-3 fs-5 text-danger"></i>
                <div><?= htmlspecialchars($bookingError) ?></div>
              </div>
            <?php endif; ?>

            <form method="post" action="" class="mt-3">
              <input type="hidden" name="book_vehicle" value="1" />

              <div class="mb-3">
                <label for="rental_start_date" class="form-label text-light">Rental Start Date</label>
                <input type="date" id="rental_start_date" name="rental_start_date" class="form-control bg-dark text-light border-secondary"
                  required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['rental_start_date'] ?? '') ?>" />
              </div>

              <div class="mb-3">
                <label for="rental_end_date" class="form-label text-light">Rental End Date</label>
                <input type="date" id="rental_end_date" name="rental_end_date" class="form-control bg-dark text-light border-secondary"
                  required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($_POST['rental_end_date'] ?? '') ?>" />
              </div>

              <button type="submit" class="btn btn-warning text-dark fw-semibold w-100 mt-3 shadow-sm glow-btn">
                <i class=""></i>Proceed to Payment
              </button>
            </form>
          <?php endif; ?>
        </div>

        <!-- Company Info Card -->
        <div class="sidebar-card mb-4 p-3 bg-dark rounded-4 shadow-sm text-light d-flex align-items-center" style="gap: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
          <?php if (!empty($company['logo'])): ?>
            <img
              src="<?= htmlspecialchars($company['logo']) ?>"
              alt="<?= htmlspecialchars($company['company_name']) ?> Logo"
              style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.6);"
              loading="lazy" />
          <?php else: ?>
            <div style="width: 80px; height: 80px; background: #444; display: flex; align-items: center; justify-content: center; border-radius: 12px; color: #f1c40f; font-weight: 700; font-size: 0.9rem;">
              No Logo
            </div>
          <?php endif; ?>

          <div>
            <h5 class="mb-1" style="color: #f1c40f; font-weight: 700; font-size: 1.25rem; line-height: 1.2;">
              <?= htmlspecialchars($company['company_name']) ?>
            </h5>
            <p class="mb-0" style="font-size: 0.95rem;">
              Phone:
              <a href="tel:<?= htmlspecialchars($company['phone']) ?>" class="text-warning" style="text-decoration: none; transition: color 0.3s;">
                <?= htmlspecialchars($company['phone']) ?: 'N/A' ?>
              </a>
            </p>
          </div>
        </div>

        <!-- Vehicle Highlights Card -->

        <div class="vehicle-highlights sidebar-card mb-4">
          <h5 class="vehicle-highlights-title">Vehicle Highlights</h5>

          <ul class="vehicle-highlights-list">
            <li>
              <span class="label">Model:</span>
              <strong class="value"><?= htmlspecialchars($vehicle['model']) ?></strong>
            </li>
            <li>
              <span class="label">Type:</span>
              <strong class="value"><?= htmlspecialchars($vehicle['type']) ?></strong>
            </li>
            <li>
              <span class="label">Seats:</span>
              <strong class="value"><?= (int)$vehicle['capacity'] ?></strong>
            </li>
            <li>
              <span class="label">Fuel Type:</span>
              <strong class="value"><?= htmlspecialchars($vehicle['fuel_type']) ?></strong>
            </li>
            <li>
              <span class="label">Registration #:</span>
              <strong class="value"><?= htmlspecialchars($vehicle['registration_number']) ?></strong>
            </li>
          </ul>
        </div>


        <!-- Contact Info Card -->
        <div class="sidebar-card bg-dark text-light mb-4 p-3">
          <h5>Need Help?</h5>
          <p>Call us: <a href="tel:+1234567890" class="text-warning">+1 234 567 890</a></p>
          <p>Email: <a href="mailto:support@example.com" class="text-warning">support@example.com</a></p>
        </div>
      </div>


      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>