<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    echo "Invalid booking ID.";
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        b.*, 
        h.name AS hotel_name, 
        h.price_per_night AS price 
    FROM booking b 
    JOIN hotels h ON b.hotel_id = h.hotel_id 
    WHERE b.booking_id = ? AND b.user_id = ?
");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    echo "Booking not found.";
    exit;
}

// Handle fake payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->prepare("UPDATE booking SET payment_status = 'paid' WHERE booking_id = ?")->execute([$booking_id]);
    header("Location: my_bookings.php?paid=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #0a0f29, #1e2e56);
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }
    .payment-card {
      background: #fff;
      color: #000;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
      max-width: 550px;
      margin: 80px auto;
      animation: slideUp 0.5s ease-in-out;
    }
    .payment-card h3 {
      font-weight: 700;
    }
    .form-label {
      font-weight: 600;
    }
    .btn-pay {
      background-color: #28a745;
      color: #fff;
      font-weight: bold;
      border: none;
      padding: 12px;
      font-size: 1rem;
      margin-top: 20px;
    }
    .btn-pay:hover {
      background-color: #218838;
    }
    @keyframes slideUp {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>

<div class="payment-card">
  <h3>Pay for: <?= htmlspecialchars($booking['hotel_name']) ?></h3>
  <p><strong>Travel Date:</strong> <?= htmlspecialchars($booking['travel_date']) ?></p>
  <p><strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?></p>
  <p><strong>Total Amount:</strong> Rs. <?= htmlspecialchars($booking['price']) ?></p>

  <form method="post">
    <div class="mb-3">
      <label for="method" class="form-label">Payment Method</label>
      <select id="method" name="payment_method" class="form-select" required>
        <option value="credit_card">Credit Card</option>
        <option value="debit_card">Debit Card</option>
        <option value="paypal">PayPal</option>
        <option value="bank_transfer">Bank Transfer</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="cardNumber" class="form-label">Card Number</label>
      <input type="text" id="cardNumber" class="form-control" placeholder="XXXX-XXXX-XXXX-XXXX" required>
    </div>

    <div class="mb-3">
      <label for="cardName" class="form-label">Cardholder Name</label>
      <input type="text" id="cardName" class="form-control" placeholder="Full Name" required>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="expiry" class="form-label">Expiry Date</label>
        <input type="text" id="expiry" class="form-control" placeholder="MM/YY" required>
      </div>
      <div class="col-md-6 mb-3">
        <label for="cvv" class="form-label">CVV</label>
        <input type="text" id="cvv" class="form-control" placeholder="123" required>
      </div>
    </div>

    <button type="submit" class="btn btn-pay w-100">Pay Now</button>
  </form>
</div>

</body>
</html>
