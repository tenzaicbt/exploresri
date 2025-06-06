<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['guide_id'])) {
    echo "<p class='text-danger text-center mt-5'>Invalid guide selection.</p>";
    include 'includes/footer.php';
    exit;
}

$guide_id = (int)$_GET['guide_id'];

$stmt = $conn->prepare("SELECT * FROM guide WHERE guide_id = ? AND status = 'active' AND is_verified = 1");
$stmt->execute([$guide_id]);
$guide = $stmt->fetch();

if (!$guide) {
    echo "<p class='text-danger text-center mt-5'>Guide not found.</p>";
    include 'includes/footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($guide['name']) ?> - Guide Profile</title>
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
      padding-top: 0px;
    }

    header, nav {
      margin-bottom: 0 !important;
    }

    .guide-card, .booking-card {
      background: rgba(255, 255, 255, 0.05);
      padding: 30px;
      border-radius: 20px;
      backdrop-filter: blur(6px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    h2, h4 {
      color: #f1c40f;
      font-weight: 700;
    }

    .guide-img {
      width: 100%;
      max-height: 300px;
      object-fit: cover;
      border-radius: 15px;
      margin-bottom: 20px;
    }

    .form-label {
      color: #ffe57f;
      font-weight: 500;
    }

    .form-control, textarea, input[type="date"], input[type="number"] {
      background-color: rgba(255, 255, 255, 0.1);
      color: #fff;
      border: none;
      border-radius: 12px;
    }

    .form-control::placeholder, textarea::placeholder {
      color: #bbb;
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.15);
      color: #fff;
      border-color: #f1c40f;
      box-shadow: 0 0 0 0.2rem rgba(241, 196, 15, 0.25);
    }

    .btn-custom {
      background-color: #f1c40f;
      color: #000;
      border-radius: 20px;
      font-weight: 600;
      padding: 10px 18px;
    }

    .btn-custom:hover {
      background-color: #ffd166;
      color: #000;
    }

    .info-label {
      color: #ccc;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="guide-card">
          <?php if (!empty($guide['photo'])): ?>
            <img src="uploads/guides/<?= htmlspecialchars($guide['photo']) ?>" class="guide-img" alt="Guide Photo">
          <?php endif; ?>
          <h2><?= htmlspecialchars($guide['name']) ?></h2>

          <p><span class="info-label">Email:</span> <?= htmlspecialchars($guide['email']) ?></p>
          <p><span class="info-label">Phone:</span> <?= !empty($guide['contact_info']) ? htmlspecialchars($guide['contact_info']) : 'N/A' ?></p>
          <p><span class="info-label">Location:</span> <?= !empty($guide['country']) ? htmlspecialchars($guide['country']) : 'N/A' ?></p>
          <p><span class="info-label">Languages:</span> <?= !empty($guide['languages']) ? htmlspecialchars($guide['languages']) : 'N/A' ?></p>
          <p><span class="info-label">Experience:</span> <?= isset($guide['experience_years']) && $guide['experience_years'] > 0 ? htmlspecialchars($guide['experience_years']) . ' years' : 'N/A' ?></p>
          <p><span class="info-label">Bio:</span> <?= !empty($guide['bio']) ? nl2br(htmlspecialchars($guide['bio'])) : 'N/A' ?></p>

          <hr class="text-secondary" />

          <h3><p><span class="info-label">Price Per Day:</span> <?= number_format($guide['price_per_day'], 2) ?> USD</p></h3>
          <p>
            <span class="info-label">Rating:</span>
            <?php
              $rating = round($guide['rating']);
              for ($i = 1; $i <= 5; $i++) {
                  echo $i <= $rating ? '<i class="bi bi-star-fill text-warning"></i> ' : '<i class="bi bi-star text-secondary"></i> ';
              }
            ?>
          </p>

          <p><span class="info-label">Availability:</span>
            <?php if ((int)$guide['is_available'] === 1): ?>
              <span class="text-success fw-bold">Available</span>
            <?php else: ?>
              <span class="text-danger fw-bold">Not Available</span>
            <?php endif; ?>
          </p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="booking-card">
          <h4>  BOOK NOW </h4>

          <?php if ((int)$guide['is_available'] === 1): ?>
            <?php if (isset($_SESSION['user_id'])): ?>
              <form action="process_guide_booking.php" method="POST">
                <input type="hidden" name="guide_id" value="<?= $guide_id ?>" />

                <div class="mb-3">
                  <label for="travel_date" class="form-label">Travel Date</label>
                  <input type="date" name="travel_date" class="form-control" required>
                </div>

                <div class="mb-3">
                  <label for="duration_days" class="form-label">Duration (Days)</label>
                  <input type="number" name="duration_days" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                  <label for="notes" class="form-label">Notes (optional)</label>
                  <textarea name="notes" class="form-control" rows="3" placeholder="Anything you'd like to mention..."></textarea>
                </div>

                <div class="d-flex justify-content-center gap-3">
                  <a href="guides.php" class="btn btn-custom">Back</a>
                  <button type="submit" class="btn btn-custom">Proceed to Payment</button>
                </div>
              </form>
            <?php else: ?>
              <p class="text-warning text-center fw-bold mt-4">
                Please <a href="login.php" class="text-decoration-underline text-info">login</a> to book this guide.
              </p>
              <div class="text-center mt-3">
                <a href="guides.php" class="btn btn-custom">Back to Guides</a>
              </div>
            <?php endif; ?>
          <?php else: ?>
            <p class="text-warning text-center fw-bold mt-4">This guide is currently <span class="text-danger">Not Available</span> for booking.</p>
            <div class="text-center mt-3">
              <a href="guides.php" class="btn btn-custom">Back to Guides</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
