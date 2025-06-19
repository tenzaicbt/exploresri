<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$booking_id = $_GET['booking_id'] ?? null;
// var_dump($booking_id, $_SESSION['user_id']);


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

// Calculate number of nights and total amount
$checkInDate = new DateTime($booking['check_in_date']);
$checkOutDate = new DateTime($booking['check_out_date']);
$interval = $checkInDate->diff($checkOutDate);
$nights = $interval->days;
$totalAmount = $booking['price'] * $nights;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_POST['payment_method'] ?? '';

  // Validate payment method and inputs
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

  // Insert into payments table
  $insertPayment = $conn->prepare("INSERT INTO payments 
        (booking_id, user_id, amount, payment_method, payment_date, status) 
        VALUES (?, ?, ?, ?, NOW(), 'Paid')");
  $insertPayment->execute([
    $booking_id,
    $_SESSION['user_id'],
    $totalAmount,
    $method
  ]);

  // Update booking status and payment status
  $updateBooking = $conn->prepare("UPDATE bookings 
        SET status = 'Paid', payment_status = 'Paid' 
        WHERE booking_id = ? AND user_id = ?");
  $updateBooking->execute([$booking_id, $_SESSION['user_id']]);



  // Redirect to confirmation
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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Rubik', sans-serif;
      background: linear-gradient(135deg, #0d1117, #1a1f2c);
      color: #f1f1f1;
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

    .payment-card h3 {
      font-weight: 600;
      color: #0a0f29;
    }

    .form-label {
      font-weight: 500;
    }

    .form-select {
      background-color: #f5f5f5;
    }

    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.25);
    }

    .hidden {
      display: none;
    }

    .payment-icons i {
      font-size: 1.4rem;
      margin-right: 12px;
      color: #444;
    }

    .payment-icons img {
      height: 24px;
      margin-right: 10px;
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

    .method-label {
      display: flex;
      align-items: center;
      gap: 10px;
    }
  </style>
</head>

<body>

  <div class="payment-card">
    <h3>Payment for <?= htmlspecialchars($booking['hotel_name']) ?></h3>
    <p><strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in_date']) ?></p>
    <p><strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out_date']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?></p>
    <p><strong>Price Per Night:</strong> $ <?= htmlspecialchars($booking['price']) ?></p>
    <p><strong>Duration:</strong> <?= $nights ?> night(s)</p>
    <p><strong>Total Amount:</strong> <span class="text-success fw-bold">$ <?= number_format($totalAmount, 2) ?></span></p>

    <form id="paymentForm" method="post">
      <div class="mb-3">
        <label for="method" class="form-label">Select Payment Method</label>
        <select id="method" name="payment_method" class="form-select" onchange="toggleFields()" required>
          <option value="credit_card">Credit Card</option>
          <option value="debit_card">Debit Card</option>
          <option value="paypal">PayPal</option>
        </select>
        <div class="payment-icons mt-2">
          <img src="https://img.icons8.com/color/48/000000/visa.png" alt="Visa">
          <img src="https://img.icons8.com/color/48/000000/mastercard.png" alt="Mastercard">
          <img src="https://img.icons8.com/color/48/000000/paypal.png" alt="PayPal">
        </div>
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

      <button type="submit" class="btn btn-pay w-100 mt-3">Pay Now</button>
    </form>
  </div>

  <script>
    function toggleFields() {
      const method = document.getElementById('method').value;
      document.getElementById('card-fields').classList.toggle('hidden', method === 'paypal');
      document.getElementById('paypal-field').classList.toggle('hidden', method !== 'paypal');
    }

    toggleFields(); // run on load

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
      const method = document.getElementById('method').value;

      if (method === 'paypal') {
        const paypalEmail = document.querySelector('[name="paypal_email"]').value.trim();
        if (!paypalEmail || !/^\S+@\S+\.\S+$/.test(paypalEmail)) {
          alert('Please enter a valid PayPal email.');
          e.preventDefault();
          return;
        }
      } else {
        const cardNumber = document.querySelector('[name="card_number"]').value.trim();
        const cardName = document.querySelector('[name="card_name"]').value.trim();
        const expiry = document.querySelector('[name="expiry"]').value.trim();
        const cvv = document.querySelector('[name="cvv"]').value.trim();

        if (!cardNumber || !cardName || !expiry || !cvv) {
          alert('Please fill in all card details.');
          e.preventDefault();
          return;
        }
      }

      if (!confirm('Proceed with payment?')) {
        e.preventDefault();
      }
    });
  </script>

</body>

</html>