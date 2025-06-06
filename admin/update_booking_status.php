<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    if ($stmt->execute([$status, $booking_id])) {
        $_SESSION['success'] = "Booking status updated.";
    } else {
        $_SESSION['error'] = "Failed to update booking status.";
    }
}

header("Location: manage_bookings.php");
exit;
