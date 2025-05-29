<?php
session_start();
include 'config/db.php';

$booking_id = $_SESSION['booking_id'] ?? null;
$method = $_POST['payment_method'] ?? '';
$payment_status = 'unpaid';

if (!$booking_id || !$method) {
    $_SESSION['error'] = "Invalid payment attempt.";
    header("Location: payment.php");
    exit;
}

// Simulate successful payment (you can replace with Stripe or PayPal SDK later)
if ($method === 'card') {
    if (isset($_POST['card_number'], $_POST['expiry'], $_POST['cvv'])) {
        $payment_status = 'paid';
    }
} elseif ($method === 'paypal') {
    if (!empty($_POST['paypal_email'])) {
        $payment_status = 'paid';
    }
}

// Update booking status
$stmt = $conn->prepare("UPDATE bookings SET payment_status = ? WHERE booking_id = ?");
$stmt->execute([$payment_status, $booking_id]);

$_SESSION['success'] = "Payment processed successfully!";
header("Location: my_bookings.php");
exit;
