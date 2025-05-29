
<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['hotel_id']) || empty($_GET['hotel_id'])) {
    echo "Invalid request.";
    exit;
}

$hotel_id = $_GET['hotel_id'];

$stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();

if (!$hotel) {
    echo "Hotel not found.";
    exit;
}

$destination_id = $hotel['destination_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $travel_date = $_POST['travel_date'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, destination_id, hotel_id, travel_date, status, booking_date) VALUES (?, ?, ?, ?, 'Pending', NOW())");
    $stmt->execute([$user_id, $destination_id, $hotel_id, $travel_date]);

    $booking_id = $conn->lastInsertId();
    header("Location: payment.php?booking_id=" . $booking_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Hotel - <?= htmlspecialchars($hotel['name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: linear-gradient(135deg, #0d1117, #1a1f2c);
      color: #f1f1f1;
    }
    .container-book {
      max-width: 1100px;
      margin: 80px auto;
      background: #161b22;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 0 30px rgba(0, 255, 255, 0.05);
      display: flex;
      gap: 40px;
      animation: fadeSlide 0.8s ease-out;
    }
    @keyframes fadeSlide {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .hotel-img {
      flex: 0.8;
    }
    .hotel-img img {
      width: 100%;
      height: 280px;
      border-radius: 15px;
      object-fit: cover;
      box-shadow: 0 12px 24px rgba(0,0,0,0.3);
    }
    .hotel-info {
      flex: 1.5;
    }
    .hotel-info h2 {
      font-size: 2.2rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: #58a6ff;
    }
    .hotel-info p {
      font-size: 1.05rem;
      margin-bottom: 10px;
      color: #d0d0d0;
    }
    .form-label {
      font-weight: 500;
      color: #ccc;
    }
    .form-control {
      width: 100%;
      max-width: 400px;
      background-color: #0d1117;
      color: #eee;
      border: 1px solid #30363d;
    }
    .form-control:focus {
      border-color: #58a6ff;
      box-shadow: 0 0 0 0.2rem rgba(88, 166, 255, 0.25);
    }
    .btn-primary-custom {
      background-color: #238636;
      color: #fff;
      border: none;
      padding: 10px 24px;
      border-radius: 10px;
      font-weight: 500;
      transition: 0.3s ease;
      float: right;
    }
    .btn-primary-custom:hover {
      background-color: #2ea043;
      transform: scale(1.05);
    }
    .btn-back {
      background: #30363d;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      text-decoration: none;
      transition: 0.3s;
      display: inline-block;
      margin-right: auto;
    }
    .btn-back:hover {
      background: #484f58;
    }
    @media (max-width: 991px) {
      .container-book {
        flex-direction: column;
      }
      .hotel-img img {
        height: 200px;
      }
    }
  </style>
</head>
<body>

<div class="container-book">
  <div class="hotel-img">
    <img src="images/<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
  </div>
  <div class="hotel-info">
    <h2><?= htmlspecialchars($hotel['name']) ?></h2>
    <p><strong>Location:</strong> <?= htmlspecialchars($hotel['location']) ?>
    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($hotel['location']) ?>" target="_blank" class="btn btn-sm btn-secondary ms-2">📍 View on Map</a></p>
    <p><strong>Price:</strong> Rs. <?= htmlspecialchars($hotel['price_per_night']) ?> / night</p>
    <p><strong>Rating:</strong> <?= htmlspecialchars($hotel['rating']) ?> / 5</p>
    <p>🛏️ Enjoy a comfortable stay with top-rated amenities.</p>

    <form method="post" class="mt-4 d-flex flex-column gap-3">
      <div>
        <label for="travel_date" class="form-label">Select Travel Date</label>
        <input type="date" class="form-control" name="travel_date" id="travel_date" required>
      </div>
      <div class="d-flex justify-content-between align-items-center mt-3">
        <a href="hotels_all.php" class="btn-back">← Back to Hotels</a>
        <button type="submit" class="btn btn-primary-custom">Confirm Booking</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
