<?php
session_start();
include 'config/db.php';

// Assuming user is logged in and user_id stored in session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guide_id = (int)$_POST['guide_id'];
    $travel_date = $_POST['travel_date'];
    $duration_days = (int)$_POST['duration_days'];
    $notes = $_POST['notes'] ?? '';

    // Validate data (basic example)
    if (!$travel_date || $duration_days < 1) {
        die("Invalid travel date or duration.");
    }

    // Insert booking with default status 'pending' and payment_status 'unpaid'
    $stmt = $conn->prepare("INSERT INTO guide_bookings (user_id, guide_id, travel_date, duration_days, status, payment_status, notes, created_at) VALUES (?, ?, ?, ?, 'pending', 'unpaid', ?, NOW())");
    $stmt->execute([$user_id, $guide_id, $travel_date, $duration_days, $notes]);

    $booking_id = $conn->lastInsertId();

    // Redirect to payment page with booking_id
    header("Location: guide_payment.php?booking_id=$booking_id");
    exit;
} else {
    header("Location: guide_bookings.php");
    exit;
}
