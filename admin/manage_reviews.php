<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

include '../config/db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $reviewId = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
        $stmt->execute([$reviewId]);
        header("Location: manage_reviews.php?msg=deleted&t=" . time());
        exit();
    } catch (PDOException $e) {
        echo "Error deleting review: " . $e->getMessage();
    }
}

// Fetch reviews
$reviews = [];
try {
    $sql = "SELECT r.*, h.name AS hotel_name, u.name AS user_name 
            FROM reviews r
            JOIN hotels h ON r.hotel_id = h.hotel_id
            JOIN users u ON r.user_id = u.user_id";
    $stmt = $conn->query($sql);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading reviews: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Reviews - Admin</title>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-danger {
            background-color: #dc3545;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="mb-4">Manage Hotel Reviews</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success">Review deleted successfully.</div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Hotel</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($reviews) > 0): ?>
                        <?php foreach ($reviews as $r): ?>
                            <tr>
                                <td><?= $r['review_id'] ?></td>
                                <td><?= htmlspecialchars($r['hotel_name']) ?></td>
                                <td><?= htmlspecialchars($r['user_name']) ?></td>
                                <td><?= str_repeat("⭐", $r['rating']) ?> (<?= $r['rating'] ?>)</td>
                                <td><?= nl2br(htmlspecialchars($r['comment'])) ?></td>
                                <td><?= $r['review_date'] ?></td>
                                <td>
                                    <a href="manage_reviews.php?delete=<?= $r['review_id'] ?>"
                                        onclick="return confirm('Are you sure you want to delete this review?');"
                                        class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No reviews found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="dashboard.php" class="btn btn-secondary mt-3">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        <div class="container mt-4"></div>
    </div>

</body>

</html>