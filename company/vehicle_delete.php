<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $user_id = $_SESSION['user_id'];

    $conn->beginTransaction();

    try {
        $stmtPayments = $conn->prepare("DELETE FROM vehicle_payments WHERE booking_id = ? AND user_id = ?");
        $stmtPayments->execute([$booking_id, $user_id]);

        $stmtBooking = $conn->prepare("DELETE FROM vehicle_bookings WHERE booking_id = ? AND user_id = ?");
        $stmtBooking->execute([$booking_id, $user_id]);

        $conn->commit();

        // Redirect to correct URL on localhost
        header("Location: /exploresri/my_bookings.php?deleted=1");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error deleting booking: " . $e->getMessage();
        exit;
    }
} else {
    header("Location: /exploresri/my_bookings.php");
    exit;
}
