<?php
ob_start(); // Start output buffering to fix header issues
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$hotel_id = $_GET['hotel_id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();
if (!$hotel) {
    echo "Hotel not found.";
    exit;
}
$destination_id = $hotel['destination_id'];
if (!isset($_GET['hotel_id']) || empty($_GET['hotel_id'])) {
    echo "Invalid hotel ID.";
    exit;
}

$hotel_id = $_GET['hotel_id'];

// Fetch hotel details
$stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();

if (!$hotel) {
    echo "Hotel not found.";
    exit;
}

$destination_id = $hotel['destination_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkin_date'], $_POST['checkout_date'])) {
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $user_id = $_SESSION['user_id'];

    $checkIn = new DateTime($checkin_date);
    $checkOut = new DateTime($checkout_date);
    $nights = $checkOut->diff($checkIn)->days;

    if ($nights <= 0) {
        echo "<script>alert('Check-out must be after check-in.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookings 
            (user_id, destination_id, hotel_id, check_in_date, check_out_date, nights, status, booking_date) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())");

        if ($stmt->execute([$user_id, $destination_id, $hotel_id, $checkin_date, $checkout_date, $nights])) {
            $booking_id = $conn->lastInsertId();
            echo "<script>window.location.href='payment.php?booking_id={$booking_id}';</script>";
            exit;
        } else {
            echo "Failed to book. Please try again.";
        }
    }
}

// Submit review
if (isset($_POST['submit_review'])) {
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if (!empty($rating) && !empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, hotel_id, rating, comment, review_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $hotel_id, $rating, $comment]);
        header("Location: book.php?hotel_id=" . $hotel_id);
        exit;
    }
}

// Prepare for gallery and features
$imageGallery = array_filter(array_map('trim', explode(',', $hotel['image_gallery'] ?? '')));
$facilities = array_filter(array_map('trim', explode(',', $hotel['facilities'] ?? '')));
$popular_features = array_filter(array_map('trim', explode(',', $hotel['popular_features'] ?? '')));

// Rating analysis (if you want to show progress bars)
$ratingCounts = [
    5 => 0,
    4 => 0,
    3 => 0,
    2 => 0,
    1 => 0,
    'total' => 0,
];

$reviewStmt = $conn->prepare("SELECT rating, COUNT(*) as count FROM reviews WHERE hotel_id = ? GROUP BY rating");
$reviewStmt->execute([$hotel_id]);
while ($row = $reviewStmt->fetch()) {
    $ratingCounts[$row['rating']] = $row['count'];
    $ratingCounts['total'] += $row['count'];
}

$percentages = [];
$totalReviews = $ratingCounts['total'];
for ($i = 5; $i >= 1; $i--) {
    $count = $ratingCounts[$i] ?? 0;
    $percentages[$i] = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
}

// Fetch 3 random other hotels
$otherHotelsStmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id != ? ORDER BY RAND() LIMIT 3");
$otherHotelsStmt->execute([$hotel_id]);
$otherHotels = $otherHotelsStmt->fetchAll();
?>

<!-- Your HTML content for the booking page goes here -->
<!-- Make sure no echo/HTML comes before the ob_start() section to preserve header() usage -->

<?php ob_end_flush(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($hotel['name']) ?> - Book Now</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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

  .info-card, .review-box, .sidebar-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(12px);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    margin-top: 20px;
    transition: transform 0.3s ease;
  }

  .info-card:hover, .review-box:hover, .sidebar-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
  }

  .features span {
    background-color: rgba(241, 196, 15, 0.1);
    color: #f1c40f;
    padding: 6px 14px;
    margin: 5px;
    border-radius: 30px;
    display: inline-block;
    font-size: 14px;
    font-weight: 500;
  }

  .sidebar-card {
    background: rgba(33, 33, 33, 0.5);
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

  .form-control, .btn-secondary {
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
        background: linear-gradient(145deg, #1f1f1f, #2a2a2a);
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
</style>

</head>
<body>

<div class="container container-main">
  <div class="row">
    <div class="col-md-8">
      <!-- Image Carousel -->
      <div id="hotelCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php foreach ($imageGallery as $index => $img): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
              <img src="images/<?= htmlspecialchars($img) ?>" class="d-block w-100" alt="Hotel Image">
            </div>
          <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#hotelCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#hotelCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>

      <!-- Hotel Info -->
      <div class="info-card">
        <h2><?= htmlspecialchars($hotel['name']) ?></h2>
        <p><strong>Location:</strong> <?= htmlspecialchars($hotel['location']) ?></p>
        <p><strong>Price:</strong> Rs. <?= htmlspecialchars($hotel['price_per_night']) ?> / night</p>
        <p><strong>Rating:</strong> <?= htmlspecialchars($hotel['rating']) ?> / 5</p>
        <p><?= nl2br(htmlspecialchars($hotel['description'])) ?></p>

        <div class="features mt-3 mb-3">
          <h6>Facilities:</h6>
          <?php foreach ($facilities as $f): ?>
            <span><?= htmlspecialchars($f) ?></span>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Guest Reviews -->
      <div class="review-box">
        <h5>Guest Reviews</h5>
        <?php
          $stmt = $conn->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.hotel_id = ? ORDER BY r.review_date DESC");
          $stmt->execute([$hotel_id]);
          $reviews = $stmt->fetchAll();
          if ($reviews):
            foreach ($reviews as $rev):
        ?>
          <div class="mb-3">
            <strong><?= htmlspecialchars($rev['name']) ?></strong>
            <div>Rating: <?= str_repeat("⭐", (int)$rev['rating']) ?></div>
            <p><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
            <small class="text-muted"><?= date("F j, Y", strtotime($rev['review_date'])) ?></small>
          </div>
          <hr>
        <?php endforeach; else: ?>
          <p>No reviews yet.</p>
        <?php endif; ?>
      </div>

      <!-- Add Review -->
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

 <!-- Right Sidebar -->
<div class="col-md-4">
  <div class="sidebar-card mb-4">
    <div class="booking-container">
        <h2>Book Hotel</h2> 
        <h2><?= htmlspecialchars($hotel['name']) ?></h2>
        <form method="POST" action="">
            <label for="checkin_date">Check-in Date:</label>
            <input type="date" name="checkin_date" id="checkin_date" required>

            <label for="checkout_date">Check-out Date:</label>
            <input type="date" name="checkout_date" id="checkout_date" required>

            <button type="submit">Book Now</button>
        </form>
    </div>

  <div class="sidebar-card mb-4">
    <h5>Property Highlights</h5>
    <p><strong>Top Location:</strong> <?= htmlspecialchars($hotel['location']) ?></p>
    <p><strong>Rating:</strong> <?= htmlspecialchars($hotel['rating']) ?> / 5</p>
    <p><strong>Popular Features:</strong></p>
    <?php foreach ($popular_features as $feature): ?>
      <span class="badge bg-info text-dark mb-1"><?= htmlspecialchars($feature) ?></span><br>
    <?php endforeach; ?>
  </div>

  <div class="sidebar-card bg-dark text-light mb-4 p-3">
    <h5>Hotel Contact Info</h5>
    <p><strong>Phone:</strong> <?= htmlspecialchars($hotel['contact_info']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($hotel['address']) ?></p>
    <?php if (!empty($hotel['email'])): ?>
      <p><strong>Email:</strong> <?= htmlspecialchars($hotel['email']) ?></p>
    <?php endif; ?>
    <?php if (!empty($hotel['website'])): ?>
      <p><strong>Website:</strong> <a href="<?= htmlspecialchars($hotel['website']) ?>" class="text-warning" target="_blank">Visit</a></p>
    <?php endif; ?>
  </div>

  <div class="sidebar-card p-0 overflow-hidden" style="border: none;">
    <iframe
      src="<?= htmlspecialchars($hotel['map_embed_link']) ?: 'https://www.google.com/maps?q=' . urlencode($hotel['location']) . '&output=embed' ?>"
      width="100%" height="200" style="border:0;" allowfullscreen loading="lazy">
    </iframe>
  </div>
</div>


<!-- Other Hotels Section -->
<?php if ($otherHotels): ?>
<div class="container container-main">
  <div class="info-card">
    <h4>Other Hotels You May Like</h4>
    <div class="row">
      <?php foreach ($otherHotels as $oth): ?>
      <div class="col-md-4 mb-4">
        <div class="card bg-dark text-white h-100 shadow">
          <img src="images/<?= htmlspecialchars(explode(',', $oth['image_gallery'])[0]) ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Hotel Image">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($oth['name']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($oth['location']) ?></p>
            <p class="card-text"><small>Rs. <?= htmlspecialchars($oth['price_per_night']) ?> / night</small></p>
            <a href="book.php?hotel_id=<?= $oth['hotel_id'] ?>" class="btn btn-sm btn-outline-warning mt-2">View Hotel</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
