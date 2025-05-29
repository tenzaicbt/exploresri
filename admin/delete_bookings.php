<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $booking_id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    if ($stmt->execute([$booking_id])) {
        $_SESSION['success'] = "Booking deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete booking.";
    }
} else {
    $_SESSION['error'] = "Invalid booking ID.";
}

header("Location: manage_bookings.php");
exit;
