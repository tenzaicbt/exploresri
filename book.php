<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['hotel_id'])) {
    echo "Invalid request.";
    exit;
}

$hotel_id = $_GET['hotel_id'];

// Fetch hotel and its destination_id
$stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();

if (!$hotel) {
    echo "Hotel not found.";
    exit;
}

$destination_id = $hotel['destination_id']; // Get correct destination_id

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $travel_date = $_POST['travel_date'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO booking (user_id, destination_id, hotel_id, travel_date, status, booking_date) VALUES (?, ?, ?, ?, 'Pending', NOW())");
    $stmt->execute([$user_id, $destination_id, $hotel_id, $travel_date]);

    header('Location: my_bookings.php?success=1');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Book Hotel - <?= htmlspecialchars($hotel['name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f0f0;
      font-family: 'Segoe UI', sans-serif;
    }
    .booking-card {
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      max-width: 600px;
      margin: 50px auto;
    }
    .hotel-img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-radius: 12px;
    }
  </style>
</head>
<body>

<div class="booking-card">
  <h2 class="mb-3">Book: <?= htmlspecialchars($hotel['name']) ?></h2>
  <img src="images/<?= htmlspecialchars($hotel['image']) ?>" class="hotel-img mb-3" alt="<?= htmlspecialchars($hotel['name']) ?>">
  <p><strong>Location:</strong> <?= htmlspecialchars($hotel['location']) ?></p>
  <p><strong>Price:</strong> Rs. <?= htmlspecialchars($hotel['price_per_night']) ?> / night</p>
  <p><strong>Rating:</strong> ★ <?= htmlspecialchars($hotel['rating']) ?> / 5</p>

  <form method="post">
    <div class="mb-3">
      <label for="travel_date" class="form-label">Select Travel Date</label>
      <input type="date" class="form-control" name="travel_date" id="travel_date" required>
    </div>
    <button type="submit" class="btn btn-primary">Confirm Booking</button>
  </form>
</div>

</body>
</html>
