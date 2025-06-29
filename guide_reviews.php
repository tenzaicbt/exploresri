<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

$guide_id = $_GET['guide_id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM guide WHERE guide_id = ? AND status = 'active' AND is_verified = 1");
$stmt->execute([$guide_id]);
$guide = $stmt->fetch();

if (!$guide) {
  echo "<div class='container-main'><p>Guide not found.</p></div>";
  exit;
}

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

<style>
  body {
    font-family: 'Rubik', sans-serif;
    background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
    color: #ffffff;
    min-height: 100vh;
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
  .btn-gold {
    background-color: #f1c40f;
    color: #000;
    padding: 8px 20px;
    border-radius: 30px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    margin-top: 20px;
    transition: background-color 0.3s;
  }
  .btn-gold:hover {
    background-color: #d4ac0d;
    color: #000;
    text-decoration: none;
  }
</style>

<div class="container-main">
  <h2 style="color:#f1c40f; margin-bottom: 16px;">Reviews for <?= htmlspecialchars($guide['name']) ?></h2>

  <?php if (count($reviews) === 0): ?>
    <div class="review-box" style="text-align:center;">
      <p>No reviews yet for this guide.</p>
    </div>
  <?php else: ?>
    <?php foreach ($reviews as $review): ?>
      <div class="review-box">
        <h6>
          <?= htmlspecialchars($review['name'] ?? 'Anonymous') ?>
          <span class="review-date"><?= date('M j, Y', strtotime($review['created_at'])) ?></span>
        </h6>
        <div class="stars">
          <?php for ($i = 0; $i < 5; $i++): ?>
            <i class="bi <?= $i < $review['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
          <?php endfor; ?>
        </div>
        <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <a href="guide_bookings.php?guide_id=<?= $guide_id ?>" class="btn-gold">Back to Guide</a>

</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
