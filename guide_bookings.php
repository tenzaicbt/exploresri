<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['guide_id'])) {
    echo "<p class='text-danger text-center mt-5'>Invalid guide selection.</p>";
    include 'includes/footer.php';
    exit;
}

$guide_id = (int)$_GET['guide_id'];

// Fetch guide info
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
  <title>Book Guide - ExploreSri</title>
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
      padding-top: 40px;
    }

    h2 {
      font-size: 2.2rem;
      font-weight: 700;
      color: #f1c40f;
      margin-bottom: 30px;
    }

    .booking-card {
      background: rgba(255, 255, 255, 0.05);
      padding: 30px;
      border-radius: 20px;
      backdrop-filter: blur(6px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      max-width: 600px;
      margin: auto;
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
  </style>
</head>

<body>
  <div class="container">
    <div class="booking-card">
      <h2>Book Guide: <?= htmlspecialchars($guide['name']) ?></h2>
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

        <div class="text-center">
          <button type="submit" class="btn btn-custom">Proceed to Payment</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
