<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['guide_id'])) {
    header("Location: guide_login.php");
    exit;
}

$guide_id = $_SESSION['guide_id'];

// Delete guide bookings first (if needed)
$stmt1 = $conn->prepare("DELETE FROM guide_bookings WHERE guide_id = ?");
$stmt1->execute([$guide_id]);

// Delete guide profile
$stmt2 = $conn->prepare("DELETE FROM guide WHERE guide_id = ?");
$stmt2->execute([$guide_id]);

// Clear session and redirect
session_destroy();
header("Location: ../index.php?deleted=1");
exit;
?>
