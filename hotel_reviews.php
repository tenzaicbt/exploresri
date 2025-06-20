<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

$hotel_id = $_GET['hotel_id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();

if (!$hotel) {
  echo "<div class='container-main'><p>Hotel not found.</p></div>";
  exit;
}

$reviewStmt = $conn->prepare("
  SELECT r.*, u.name 
  FROM reviews r
  JOIN users u ON r.user_id = u.user_id
  WHERE r.hotel_id = ?
  ORDER BY r.review_date DESC
");
$reviewStmt->execute([$hotel_id]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- STYLE: Match Vehicle Reviews Page -->
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

  .review-date {
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
    Reviews for <?= htmlspecialchars($hotel['name']) ?>
  </h2>

  <?php if (count($reviews) === 0): ?>
    <div class="review-box text-center">
      <p>No reviews yet for this hotel.</p>
    </div>
  <?php else: ?>
    <?php foreach ($reviews as $review): ?>
      <div class="review-box">
        <h6>
          <?= htmlspecialchars($review['name']) ?>
          <span class="review-date"><?= date('M j, Y', strtotime($review['review_date'])) ?></span>
        </h6>
        <div class="stars mb-1">
          <?php for ($i = 0; $i < 5; $i++): ?>
            <i class="bi <?= $i < $review['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
          <?php endfor; ?>
        </div>
        <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <a href="book.php?hotel_id=<?= $hotel_id ?>" class="btn-gold">Back to Hotel Details</a>
</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
