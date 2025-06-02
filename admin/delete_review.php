<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $reviewId = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
        $stmt->execute([$reviewId]);

        header("Location: manage_reviews.php?msg=deleted");
        exit();
    } catch (PDOException $e) {
        echo "Error deleting review: " . $e->getMessage();
    }
} else {
    // Invalid ID or missing
    header("Location: manage_reviews.php?msg=invalid");
    exit();
}
