<?php
session_start();
if (!isset($_SESSION['company_id'])) {
    header("Location: company_login.php");
    exit;
}

require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    // Validate status
    $valid_statuses = ['pending', 'confirmed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        $_SESSION['error'] = "Invalid status selected.";
        header("Location: manage_vehicle_bookings.php");
        exit;
    }

    // Verify booking belongs to logged-in company (security)
    $stmtCheck = $conn->prepare("
        SELECT vb.booking_id 
        FROM vehicle_bookings vb 
        JOIN vehicles v ON vb.vehicle_id = v.vehicle_id 
        WHERE vb.booking_id = ? AND v.company_id = ?
    ");
    $stmtCheck->execute([$booking_id, $_SESSION['company_id']]);
    if (!$stmtCheck->fetch()) {
        $_SESSION['error'] = "Unauthorized action.";
        header("Location: manage_vehicle_bookings.php");
        exit;
    }

    // Update booking status
    $stmtUpdate = $conn->prepare("UPDATE vehicle_bookings SET status = ? WHERE booking_id = ?");
    if ($stmtUpdate->execute([$status, $booking_id])) {
        $_SESSION['success'] = "Booking status updated.";
    } else {
        $_SESSION['error'] = "Failed to update booking status.";
    }
}

header("Location: manage_vehicle_bookings.php");
exit;
