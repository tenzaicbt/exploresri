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

$stmt = $conn->prepare("
    SELECT gb.*, g.name AS guide_name, g.price_per_day 
    FROM guide_bookings gb 
    JOIN guide g ON gb.guide_id = g.guide_id 
    WHERE gb.booking_id = ? AND gb.user_id = ?");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
  echo "Booking not found or unauthorized.";
  exit;
}

$total_price = $booking['price_per_day'] * $booking['duration_days'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_POST['payment_method'] ?? '';

  if ($method === 'paypal') {
    $paypal_email = $_POST['paypal_email'] ?? '';
    if (!filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) {
      echo "Invalid PayPal email.";
      exit;
    }
  } else {
    $card_number = $_POST['card_number'] ?? '';
    $card_name = $_POST['card_name'] ?? '';
    $expiry = $_POST['expiry'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    if (!$card_number || !$card_name || !$expiry || !$cvv) {
      echo "Please fill in all card details.";
      exit;
    }
  }

  $insert = $conn->prepare("INSERT INTO guide_payments 
        (booking_id, user_id, amount, payment_method, payment_date, status) 
        VALUES (?, ?, ?, ?, NOW(), 'Paid')");
  $insert->execute([
    $booking_id,
    $_SESSION['user_id'],
    $total_price,
    $method
  ]);

  $update = $conn->prepare("UPDATE guide_bookings 
        SET status = 'Paid', payment_status = 'Paid' 
        WHERE booking_id = ? AND user_id = ?");
  $update->execute([$booking_id, $_SESSION['user_id']]);

  header("Location: my_bookings.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Guide Payment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0d1117, #1a1f2c);
      color: #fff;
      font-family: 'Rubik', sans-serif;
    }

    .payment-card {
      background: #fff;
      color: #000;
      padding: 30px 40px;
      border-radius: 18px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
      max-width: 600px;
      margin: 80px auto;
      animation: fadeInUp 0.8s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .hidden {
      display: none;
    }

    .btn-pay {
      background-color: #238636;
      color: white;
      font-weight: 500;
      padding: 12px;
      font-size: 16px;
      border-radius: 10px;
      transition: 0.3s ease;
    }

    .btn-pay:hover {
      background-color: #2ea043;
      transform: scale(1.03);
    }
  </style>
</head>

<body>

  <div class="payment-card">
    <h3>Payment for Guide: <?= htmlspecialchars($booking['guide_name']) ?></h3>
    <p><strong>Travel Date:</strong> <?= htmlspecialchars($booking['travel_date']) ?></p>
    <p><strong>Duration:</strong> <?= $booking['duration_days'] ?> day(s)</p>
    <p><strong>Total Amount:</strong> <span class="text-success fw-bold">Rs. <?= number_format($total_price, 2) ?></span></p>

    <form method="post" id="paymentForm">
      <div class="mb-3">
        <label class="form-label">Select Payment Method</label>
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
            <label class="form-label">Expiry</label>
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
        <input type="email" class="form-control" name="paypal_email" placeholder="your@paypal.com">
      </div>

      <button type="submit" class="btn btn-pay w-100 mt-3">Pay Now</button>
    </form>
  </div>

  <script>
    function toggleFields() {
      const method = document.getElementById('method').value;
      document.getElementById('card-fields').classList.toggle('hidden', method === 'paypal');
      document.getElementById('paypal-field').classList.toggle('hidden', method !== 'paypal');
    }
    toggleFields();

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
      const method = document.getElementById('method').value;
      if (method === 'paypal') {
        const email = document.querySelector('[name="paypal_email"]').value.trim();
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
          alert('Enter a valid PayPal email.');
          e.preventDefault();
          return;
        }
      } else {
        const required = ['card_number', 'card_name', 'expiry', 'cvv'];
        for (let name of required) {
          const value = document.querySelector(`[name="${name}"]`).value.trim();
          if (!value) {
            alert('Fill in all card details.');
            e.preventDefault();
            return;
          }
        }
      }

      if (!confirm('Proceed with payment?')) {
        e.preventDefault();
      }
    });
  </script>

</body>

</html>