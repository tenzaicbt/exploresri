<?php
include '../config/db.php';

$hotel_id = $_GET['hotel_id'] ?? null;
$image = $_GET['image'] ?? null;

if (!$hotel_id || !$image) {
    echo "Invalid parameters";
    exit;
}

// Get current gallery
$stmt = $conn->prepare("SELECT image_gallery FROM hotels WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$row = $stmt->fetch();

if ($row) {
    $gallery = explode(',', $row['image_gallery']);
    $gallery = array_filter($gallery, fn($img) => $img !== $image); // remove image

    // Delete file from folder
    $filePath = "../images/" . $image;
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Update DB
    $updatedGallery = implode(',', $gallery);
    $update = $conn->prepare("UPDATE hotels SET image_gallery = ? WHERE hotel_id = ?");
    $update->execute([$updatedGallery, $hotel_id]);
}

// Redirect back
header("Location: edit_hotel.php?id=" . $hotel_id);
exit;
