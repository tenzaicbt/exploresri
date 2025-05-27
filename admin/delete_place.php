<?php
include '../config/db.php';
session_start();

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM destinations WHERE destination_id = ?");
    $stmt->execute([$id]);
    header("Location: manage_places.php?deleted=1");
} else {
    echo "Invalid destination ID.";
}
