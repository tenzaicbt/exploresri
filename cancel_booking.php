<?php
include 'config/db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE booking SET status = 'Cancelled' WHERE booking_id = ? AND user_id = ?");
$stmt->execute([$booking_id, $user_id]);

header("Location: my_bookings.php?msg=cancelled");
