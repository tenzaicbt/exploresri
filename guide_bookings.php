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

// Handle review submission
$guide_id = isset($_GET['guide_id']) ? (int)$_GET['guide_id'] : 0;

$review_error = '';
$review_success = '';

if (isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating >= 1 && $rating <= 5 && strlen($comment) > 0) {
        $insertStmt = $conn->prepare("
            INSERT INTO guide_reviews (guide_id, user_id, rating, comment, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $insertStmt->execute([$guide_id, $_SESSION['user_id'], $rating, $comment]);
        $review_success = "Thank you for your review!";
    } else {
        $review_error = "Please provide a valid rating and comment.";
    }
}

// Fetch updated reviews after submission or page load
$reviewStmt = $conn->prepare("
  SELECT gr.*, u.name 
  FROM guide_reviews gr
  JOIN users u ON gr.user_id = u.user_id
  WHERE gr.guide_id = ?
  ORDER BY gr.created_at DESC
");
$reviewStmt->execute([$guide_id]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
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

    header,
    nav {
      margin-bottom: 0 !important;
    }

    .guide-card,
    .booking-card,
    .review-card {
      background: rgba(255, 255, 255, 0.05);
      padding: 30px;
      border-radius: 20px;
      backdrop-filter: blur(6px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      margin-bottom: 30px;
    }

    h2,
    h4 {
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

    .form-control,
    textarea,
    input[type="date"],
    input[type="number"] {
      background-color: rgba(255, 255, 255, 0.1);
      color: #fff;
      border: none;
      border-radius: 12px;
    }

    .form-control::placeholder,
    textarea::placeholder {
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

    /* Review Box */
    .review-box {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      padding: 15px 20px;
      border-radius: 12px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
      margin-top: 12px;
      font-size: 0.9rem;
    }

    .review-box h6 {
      color: #f1c40f;
      font-weight: 600;
      font-size: 1rem;
      margin-bottom: 6px;
    }

    .review-box p {
      color: #ddd;
      margin: 0;
      white-space: pre-wrap;
    }

    .review-date {
      float: right;
      color: #bbb;
      font-size: 0.75rem;
    }

    .stars i {
      font-size: 1rem;
      color: #f1c40f;
      margin-right: 2px;
    }

    /* Review form style */
    .rating-label {
      color: #ffe57f;
      font-weight: 600;
    }

    .rating-select {
      background-color: rgba(255, 255, 255, 0.1);
      color: #fff;
      border: none;
      border-radius: 12px;
    }

    .rating-select option {
      color: #000;
    }

    .btn-animated-sm {
      background-color: #f1c40f;
      color: #000;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      padding: 6px 14px;
      transition: background-color 0.3s ease;
    }

    .btn-animated-sm:hover {
      background-color: #d4ac0d;
      color: #000;
    }

    .review-card {
  font-family: 'Rubik', sans-serif;
  color: #fff;
}

    .rating-label {
      font-size: 0.75rem;
      color: #f1c40f;
      font-weight: 500;
    }

    .rating-select {
      background: rgba(255, 255, 255, 0.05);
      color: #fff;
      border: 1px solid #f1c40f;
      border-radius: 8px;
      padding: 6px 10px;
      font-size: 0.85rem;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .rating-select:focus {
      outline: none;
      border-color: #ffe57f;
      box-shadow: 0 0 8px rgba(241, 196, 15, 0.4);
    }

    .rating-select option {
      background: #1b2735;
      color: #fff;
    }

textarea.form-control-sm {
  font-size: 0.9rem;
  padding: 6px 8px;
  resize: vertical;
  min-height: 36px;
}

.btn-animated-sm {
  font-size: 0.9rem;
  padding: 6px 12px;
}

.row.g-2.align-items-end > .col-4,
.row.g-2.align-items-end > .col-6,
.row.g-2.align-items-end > .col-2 {
  display: flex;
  flex-direction: column;
}

.row.g-2.align-items-end .form-label {
  margin-bottom: 4px;
}

  </style>
</head>

<body>
  <div class="container mt-4">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="guide-card">
          <?php if (!empty($guide['photo'])): ?>
            <img src="uploads/guides/<?= htmlspecialchars($guide['photo']) ?>" class="guide-img" alt="Guide Photo" />
          <?php endif; ?>
          <h2><?= htmlspecialchars($guide['name']) ?></h2>

          <p><span class="info-label">Email:</span> <?= htmlspecialchars($guide['email']) ?></p>
          <p><span class="info-label">Phone:</span> <?= !empty($guide['contact_info']) ? htmlspecialchars($guide['contact_info']) : 'N/A' ?></p>
          <p><span class="info-label">Location:</span> <?= !empty($guide['country']) ? htmlspecialchars($guide['country']) : 'N/A' ?></p>
          <p><span class="info-label">Languages:</span> <?= !empty($guide['languages']) ? htmlspecialchars($guide['languages']) : 'N/A' ?></p>
          <p><span class="info-label">Experience:</span> <?= isset($guide['experience_years']) && $guide['experience_years'] > 0 ? htmlspecialchars($guide['experience_years']) . ' years' : 'N/A' ?></p>
          <p><span class="info-label">Bio:</span> <?= !empty($guide['bio']) ? nl2br(htmlspecialchars($guide['bio'])) : 'N/A' ?></p>

          <hr class="text-secondary" />

          <h3>
            <p><span class="info-label">Price Per Day:</span> <?= number_format($guide['price_per_day'], 2) ?> USD</p>
          </h3>
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
          <h4> BOOK NOW </h4>

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

        <div class="review-card">

          <a href="guide_reviews.php?guide_id=<?= $guide_id ?>" class="btn btn-animated-sm btn-sm mb-3">View All Reviews</a>

          <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (!empty($review_error)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($review_error) ?></div>
            <?php endif; ?>
            <form method="post">
              <div class="row g-2 align-items-end">
                <div class="col-4">
                  <label for="rating" class="form-label rating-label mb-1">Rating</label>
                  <select name="rating" id="rating" class="form-select rating-select" required>
                    <option value="">Select</option>
                    <option value="5">★★★★★ Excellent</option>
                    <option value="4">★★★★☆ Good</option>
                    <option value="3">★★★☆☆ Average</option>
                    <option value="2">★★☆☆☆ Poor</option>
                    <option value="1">★☆☆☆☆ Terrible</option>
                  </select>
                </div>
                <div class="col-6">
                  <label for="comment" class="form-label small mb-1">Comment</label>
                  <textarea name="comment" id="comment" class="form-control form-control-sm" rows="1" required></textarea>
                </div>
                <div class="col-2">
                  <button type="submit" name="submit_review" class="btn btn-animated-sm w-150">Submit</button>
                </div>
              </div>
            </form>
          <?php else: ?>
            <p class="text-warning mt-3">Please <a href="login.php" class="text-info">login</a> to add a review.</p>
          <?php endif; ?>

        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php include 'includes/footer.php'; ?>
