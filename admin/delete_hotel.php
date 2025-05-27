<?php
include '../config/db.php';

$hotel_id = $_GET['id'] ?? null;

if ($hotel_id) {
    $stmt = $conn->prepare("DELETE FROM hotels WHERE hotel_id = ?");
    $stmt->execute([$hotel_id]);
    header("Location: manage_hotels.php?deleted=1");
    exit;
} else {
    echo "Invalid hotel ID";
}

