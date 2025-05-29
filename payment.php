<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id || !is_numeric($booking_id)) {
    echo "Invalid booking ID.";
    exit;
}

// Fetch booking info with user validation
$stmt = $conn->prepare("
    SELECT 
        b.*, 
        h.name AS hotel_name, 
        h.price_per_night AS price 
    FROM bookings b 
    JOIN hotels h ON b.hotel_id = h.hotel_id 
    WHERE b.booking_id = ? AND b.user_id = ?
");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    echo "Booking not found.";
    exit;
}

// Handle payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['payment_method'] ?? '';

    // Basic validation for required fields
    if ($method === 'paypal') {
        $paypal_email = $_POST['paypal_email'] ?? '';
        if (!filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid PayPal email.";
            exit;
        }
        // Simulate PayPal processing...
    } else {
        $card_number = $_POST['card_number'] ?? '';
        $card_name = $_POST['card_name'] ?? '';
        $expiry = $_POST['expiry'] ?? '';
        $cvv = $_POST['cvv'] ?? '';

        if (!$card_number || !$card_name || !$expiry || !$cvv) {
            echo "Please fill in all card details.";
            exit;
        }

        // Simulate card processing...
    }

    // Update payment status
    $update = $conn->prepare("UPDATE bookings SET payment_status = 'paid' WHERE booking_id = ?");
    $update->execute([$booking_id]);

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
    }
    .hidden {
      display: none;
    }
  </style>
  <script>
    function toggleFields() {
      const method = document.getElementById('method').value;
      document.getElementById('card-fields').classList.toggle('hidden', method === 'paypal');
      document.getElementById('paypal-field').classList.toggle('hidden', method !== 'paypal');
    }
  </script>
</head>
<body>

<div class="payment-card">
  <h3>Pay for: <?= htmlspecialchars($booking['hotel_name']) ?></h3>
  <p><strong>Travel Date:</strong> <?= htmlspecialchars($booking['travel_date']) ?></p>
  <p><strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?></p>
  <p><strong>Total Amount:</strong> Rs. <?= htmlspecialchars($booking['price']) ?></p>

  <form method="post" onsubmit="return confirm('Proceed with payment?')">
    <div class="mb-3">
      <label for="method" class="form-label">Payment Method</label>
      <select id="method" name="payment_method" class="form-select" onchange="toggleFields()" required>
        <option value="credit_card">Credit Card</option>
        <option value="debit_card">Debit Card</option>
        <option value="paypal">PayPal</option>
      </select>
    </div>

    <div id="card-fields">
      <div class="mb-3">
        <label class="form-label">Card Number</label>
        <input type="text" class="form-control" name="card_number" placeholder="XXXX-XXXX-XXXX-XXXX">
      </div>
      <div class="mb-3">
        <label class="form-label">Cardholder Name</label>
        <input type="text" class="form-control" name="card_name" placeholder="Full Name">
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Expiry Date</label>
          <input type="text" class="form-control" name="expiry" placeholder="MM/YY">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">CVV</label>
          <input type="text" class="form-control" name="cvv" placeholder="123">
        </div>
      </div>
    </div>

    <div class="mb-3 hidden" id="paypal-field">
      <label class="form-label">PayPal Email</label>
      <input type="email" class="form-control" name="paypal_email" placeholder="example@paypal.com">
    </div>

    <button type="submit" class="btn btn-success w-100 mt-3">Pay Now</button>
  </form>
</div>

<script>
  toggleFields();
</script>

</body>
</html>
