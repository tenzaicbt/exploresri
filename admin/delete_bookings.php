<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate and sanitize booking_id from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $booking_id = (int) $_GET['id'];

    // Delete the booking (only if it belongs to the logged-in user)
    $stmt = $conn->prepare("DELETE FROM booking WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);

    // Optional: Set session message (flash message)
    $_SESSION['success'] = "Booking deleted successfully.";
}

// Redirect back to bookings page
header("Location: manage_bookings.php");
exit;
