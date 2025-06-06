<?php
session_start();
include '../config/db.php';  // <-- fix path here

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    $stmt = $conn->prepare("DELETE FROM guide_bookings WHERE booking_id = ?");
    $stmt->execute([$booking_id]);

    header("Location: ../my_bookings.php?msg=guide_booking_cancelled");
    exit;
} else {
    header("Location: ../my_bookings.php");
    exit;
}
?>
