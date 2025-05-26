<?php
session_start();
include '../config/db.php';

if (!isset($_POST['hotel_id'])) {
    die("Invalid booking request.");
}

$hotel_id = (int) $_POST['hotel_id'];
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$guests = (int) $_POST['guests'];
$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    die("You must be logged in to book.");
}

$stmt = $conn->prepare("INSERT INTO bookings (user_id, hotel_id, check_in, check_out, guests) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $hotel_id, $check_in, $check_out, $guests]);

// Redirect to my_bookings.php after successful booking
header("Location: /exploresri/my_bookings.php");
exit;
?>
