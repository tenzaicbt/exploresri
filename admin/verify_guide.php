<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guide_id'], $_POST['is_verified'])) {
    $guide_id = intval($_POST['guide_id']);
    $is_verified = intval($_POST['is_verified']) === 1 ? 1 : 0;

    try {
        $stmt = $conn->prepare("UPDATE guides SET is_verified = :is_verified WHERE guide_id = :guide_id");
        $stmt->execute([
            ':is_verified' => $is_verified,
            ':guide_id' => $guide_id
        ]);
    } catch (PDOException $e) {
        echo "Error updating verification: " . $e->getMessage();
        exit;
    }
}

header("Location: guide_manage.php");
exit;
