<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE booking SET status = :status WHERE booking_id = :booking_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':booking_id', $booking_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Booking status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update booking status.";
    }
}

header("Location: manage_bookings.php");
exit;
