<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = (int)$_POST['booking_id'];
    $payment_method = $_POST['payment_method'];
    $amount = (float)$_POST['amount'];

    // Validate payment method and booking ownership
    $stmt = $conn->prepare("SELECT * FROM guide_bookings WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        die("Invalid booking.");
    }

    if (!in_array($payment_method, ['Card', 'PayPal', 'Cash'])) {
        die("Invalid payment method.");
    }

    // Update booking with payment info and status
    $stmt = $conn->prepare("UPDATE guide_bookings SET payment_status = 'paid', payment_method = ?, amount = ?, status = 'confirmed' WHERE booking_id = ?");
    $stmt->execute([$payment_method, $amount, $booking_id]);

    // Redirect to booking details page
    header("Location: guide_booking_details.php?booking_id=$booking_id");
    exit;
} else {
    header("Location: guide_bookings.php");
    exit;
}
