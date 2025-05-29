<?php
session_start();
include 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check hotel_id in URL
if (!isset($_GET['hotel_id']) || empty($_GET['hotel_id'])) {
    echo "Invalid request.";
    exit;
}

$hotel_id = $_GET['hotel_id'];

// Fetch hotel info
$stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();

if (!$hotel) {
    echo "Hotel not found.";
    exit;
}

$destination_id = $hotel['destination_id'];

// Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $travel_date = $_POST['travel_date'];
    $user_id = $_SESSION['user_id'];

    // Insert booking into bookings table
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, destination_id, hotel_id, travel_date, status, booking_date) VALUES (?, ?, ?, ?, 'Pending', NOW())");
    $stmt->execute([$user_id, $destination_id, $hotel_id, $travel_date]);

    // Get last inserted ID and redirect to payment
    $booking_id = $conn->lastInsertId();
    header("Location: payment.php?booking_id=" . $booking_id);
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Book Hotel - <?= htmlspecialchars($hotel['name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #e8f5ff, #ffffff);
      font-family: 'Rubik', sans-serif;
      padding: 60px 0;
    }
    .booking-wrapper {
      max-width: 1100px;
      margin: auto;
      background: #fff;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      display: flex;
      gap: 40px;
      animation: fadeInSlide 0.6s ease-out;
    }
    @keyframes fadeInSlide {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .hotel-image {
      flex: 1;
    }
    .hotel-image img {
      width: 100%;
      border-radius: 15px;
      height: 400px;
      object-fit: cover;
      box-shadow: 0 12px 24px rgba(0,0,0,0.15);
    }
    .hotel-details {
      flex: 1.2;
    }
    .hotel-details h2 {
      color: #003049;
      font-weight: 600;
      font-size: 2.2rem;
      margin-bottom: 20px;
    }
    .hotel-details p {
      margin-bottom: 10px;
      font-size: 1.1rem;
      color: #444;
    }
    .form-label {
      font-weight: 500;
    }
    .btn-book {
      background-color: #003049;
      color: #fff;
      border: none;
      padding: 10px 25px;
      font-weight: 500;
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    .btn-book:hover {
      background-color: #00507a;
      transform: scale(1.05);
    }
    .btn-back {
      margin-top: 15px;
      background: #666;
      color: white;
      border: none;
      padding: 8px 20px;
      border-radius: 6px;
      transition: 0.3s;
      text-decoration: none;
      display: inline-block;
    }
    .btn-back:hover {
      background: #444;
    }
    @media (max-width: 991px) {
      .booking-wrapper {
        flex-direction: column;
      }
      .hotel-image img {
        height: 280px;
      }
    }
  </style>
</head>
<body>

<div class="booking-wrapper">
  <div class="hotel-image">
    <img src="images/<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
  </div>
  <div class="hotel-details">
    <h2><?= htmlspecialchars($hotel['name']) ?></h2>
    <p><strong>📍 Location:</strong> <?= htmlspecialchars($hotel['location']) ?></p>
    <p><strong>💰 Price:</strong> Rs. <?= htmlspecialchars($hotel['price_per_night']) ?> / night</p>
    <p><strong>⭐ Rating:</strong> <?= htmlspecialchars($hotel['rating']) ?> / 5</p>

    <form method="post" class="mt-4">
      <div class="mb-3">
        <label for="travel_date" class="form-label">Select Travel Date</label>
        <input type="date" class="form-control" name="travel_date" id="travel_date" required>
      </div>
      <button type="submit" class="btn btn-book">Confirm Booking</button>
    </form>

    <a href="hotels_all.php" class="btn-back mt-3">← Back to Hotels</a>
  </div>
</div>

</body>
</html>
