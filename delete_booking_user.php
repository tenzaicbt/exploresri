<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM booking WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);

    header("Location: my_bookings.php?deleted=1");
    exit;
} else {
    header("Location: my_bookings.php");
    exit;
}

if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success text-center">Booking deleted successfully.</div>
<?php endif; ?>

