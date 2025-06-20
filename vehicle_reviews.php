<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

$vehicle_id = $_GET['vehicle_id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
  echo "<div class='container-main'><p>Vehicle not found.</p></div>";
  exit;
}

$reviewStmt = $conn->prepare("
  SELECT vr.*, u.name 
  FROM vehicle_reviews vr
  JOIN users u ON vr.user_id = u.user_id
  WHERE vr.vehicle_id = ?
  ORDER BY vr.created_at DESC
");
$reviewStmt->execute([$vehicle_id]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
      max-width: 900px;
      margin: 30px auto;
      padding: 0 15px;
  }
  .review-box {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      padding: 15px 20px;
      border-radius: 12px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
      margin-top: 12px;
      transition: transform 0.3s ease;
      font-size: 0.9rem;
  }
  .review-box:hover {
      transform: translateY(-3px);
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
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
  .review-box .review-date {
      float: right;
      font-weight: 400;
      color: #bbb;
      font-size: 0.75rem;
  }
  .stars i {
      font-size: 1rem;
      margin-right: 2px;
      color: #f1c40f;
  }
  a.btn-gold {
      background-color: #f1c40f;
      color: #000;
      border: none;
      padding: 8px 22px;
      border-radius: 30px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-block;
      margin-top: 20px;
      text-align: center;
      text-decoration: none;
      font-size: 0.9rem;
  }
  a.btn-gold:hover {
      background-color: #d4ac0d;
      color: #000;
      text-decoration: none;
  }
</style>

<div class="container-main">
  <h2 style="color:#f1c40f; margin-bottom: 16px; font-size: 1.5rem;">
    Reviews for <?= htmlspecialchars($vehicle['type'] . ' ' . $vehicle['model']) ?>
  </h2>

  <?php if (count($reviews) === 0): ?>
    <div class="review-box" style="text-align:center;">
      <p>No reviews yet for this vehicle.</p>
    </div>
  <?php else: ?>
    <?php foreach ($reviews as $review): ?>
      <div class="review-box">
        <h6>
          <?= !empty($review['name']) ? htmlspecialchars($review['name']) : 'Anonymous' ?>
          <span class="review-date"><?= date('M j, Y', strtotime($review['created_at'])) ?></span>
        </h6>
        <div class="stars" aria-label="Rating: <?= $review['rating'] ?> out of 5 stars">
          <?php for ($i = 0; $i < 5; $i++): ?>
            <?php if ($i < $review['rating']): ?>
              <i class="bi bi-star-fill"></i>
            <?php else: ?>
              <i class="bi bi-star"></i>
            <?php endif; ?>
          <?php endfor; ?>
        </div>
        <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

 <a href="vehicle_details.php?vehicle_id=<?= $vehicle_id ?>" class="btn-gold">Back to Vehicle Details</a>


</div>

<!-- Bootstrap Icons CDN for stars -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
