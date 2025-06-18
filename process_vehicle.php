<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = (int)$_POST['vehicle_id'];
    $booking_start = $_POST['booking_start'];
    $booking_end = $_POST['booking_end'];
    $pickup_location = $_POST['pickup_location'] ?? '';
    $notes = $_POST['notes'] ?? '';

    // Basic validation
    if (!$booking_start || !$booking_end || strtotime($booking_end) <= strtotime($booking_start)) {
        die("Invalid booking dates.");
    }

    // Insert booking with default status 'pending' and payment_status 'unpaid'
    $stmt = $conn->prepare("INSERT INTO vehicle_bookings 
        (user_id, vehicle_id, booking_start, booking_end, pickup_location, notes, status, payment_status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending', 'unpaid', NOW())");

    $stmt->execute([$user_id, $vehicle_id, $booking_start, $booking_end, $pickup_location, $notes]);

    $booking_id = $conn->lastInsertId();

    // Redirect to vehicle payment page
    header("Location: vehicle_payment.php?booking_id=$booking_id");
    exit;
} else {
    header("Location: vehicle_bookings.php");
    exit;
}
