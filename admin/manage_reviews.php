<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

include '../config/db.php';

// Handle delete based on type and id
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $reviewId = intval($_GET['delete']);
    $type = $_GET['type'];

    $tableMap = [
        'hotel' => 'reviews',
        'guide' => 'guide_reviews',
        'vehicle' => 'vehicle_reviews',
    ];

    if (isset($tableMap[$type])) {
        $table = $tableMap[$type];
        try {
            $stmt = $conn->prepare("DELETE FROM $table WHERE review_id = ?");
            $stmt->execute([$reviewId]);
            header("Location: manage_reviews.php?msg=deleted&type=$type&t=" . time());
            exit();
        } catch (PDOException $e) {
            echo "Error deleting $type review: " . $e->getMessage();
        }
    }
}

// Fetch Hotel Reviews
$hotel_reviews = [];
try {
    $sql = "SELECT r.*, h.name AS hotel_name, u.name AS user_name 
            FROM reviews r
            JOIN hotels h ON r.hotel_id = h.hotel_id
            JOIN users u ON r.user_id = u.user_id
            ORDER BY r.review_date DESC";
    $stmt = $conn->query($sql);
    $hotel_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading hotel reviews: " . $e->getMessage();
}

// Fetch Guide Reviews
$guide_reviews = [];
try {
    $sql = "SELECT gr.*, u.name AS user_name
        FROM guide_reviews gr
        JOIN users u ON gr.user_id = u.user_id
        ORDER BY gr.created_at DESC";

    $stmt = $conn->query($sql);
    $guide_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading guide reviews: " . $e->getMessage();
}

// Fetch Vehicle Reviews
$vehicle_reviews = [];
try {
    $sql = "SELECT vr.*, v.model AS vehicle_model, u.name AS user_name
            FROM vehicle_reviews vr
            JOIN vehicles v ON vr.vehicle_id = v.vehicle_id
            JOIN users u ON vr.user_id = u.user_id
            ORDER BY vr.created_at DESC";
    $stmt = $conn->query($sql);
    $vehicle_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading vehicle reviews: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Manage Reviews - Admin</title>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        <h2 class="mb-4">Manage Reviews</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success">
                <?= ucfirst(htmlspecialchars($_GET['type'])) ?> review deleted successfully.
            </div>
        <?php endif; ?>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs mb-3" id="reviewTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="hotel-tab" data-bs-toggle="tab" data-bs-target="#hotel" type="button"
                    role="tab" aria-controls="hotel" aria-selected="true">Hotel Reviews (<?= count($hotel_reviews) ?>)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="guide-tab" data-bs-toggle="tab" data-bs-target="#guide" type="button"
                    role="tab" aria-controls="guide" aria-selected="false">Guide Reviews (<?= count($guide_reviews) ?>)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vehicle-tab" data-bs-toggle="tab" data-bs-target="#vehicle" type="button"
                    role="tab" aria-controls="vehicle" aria-selected="false">Vehicle Reviews (<?= count($vehicle_reviews) ?>)</button>
            </li>
        </ul>

        <div class="tab-content" id="reviewTabsContent">
            <!-- Hotel Reviews Tab -->
            <div class="tab-pane fade show active" id="hotel" role="tabpanel" aria-labelledby="hotel-tab">
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
                            <?php if (count($hotel_reviews) > 0): ?>
                                <?php foreach ($hotel_reviews as $r): ?>
                                    <tr>
                                        <td><?= $r['review_id'] ?></td>
                                        <td><?= htmlspecialchars($r['hotel_name']) ?></td>
                                        <td><?= htmlspecialchars($r['user_name']) ?></td>
                                        <td><?= str_repeat("⭐", $r['rating']) ?> (<?= $r['rating'] ?>)</td>
                                        <td><?= nl2br(htmlspecialchars($r['comment'])) ?></td>
                                        <td><?= $r['review_date'] ?></td>
                                        <td>
                                            <a href="?delete=<?= $r['review_id'] ?>&type=hotel"
                                                onclick="return confirm('Are you sure you want to delete this hotel review?');"
                                                class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hotel reviews found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Guide Reviews Tab -->
            <div class="tab-pane fade" id="guide" role="tabpanel" aria-labelledby="guide-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Guide</th>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($guide_reviews) > 0): ?>
                                <?php foreach ($guide_reviews as $r): ?>
                                    <tr>
                                        <td><?= $r['review_id'] ?></td>
                                        <td><?= htmlspecialchars($r['guide_name']) ?></td>
                                        <td><?= htmlspecialchars($r['user_name']) ?></td>
                                        <td><?= str_repeat("⭐", $r['rating']) ?> (<?= $r['rating'] ?>)</td>
                                        <td><?= nl2br(htmlspecialchars($r['comment'])) ?></td>
                                        <td><?= $r['created_at'] ?></td>
                                        <td>
                                            <a href="?delete=<?= $r['review_id'] ?>&type=guide"
                                                onclick="return confirm('Are you sure you want to delete this guide review?');"
                                                class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No guide reviews found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Vehicle Reviews Tab -->
            <div class="tab-pane fade" id="vehicle" role="tabpanel" aria-labelledby="vehicle-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Vehicle</th>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($vehicle_reviews) > 0): ?>
                                <?php foreach ($vehicle_reviews as $r): ?>
                                    <tr>
                                        <td><?= $r['review_id'] ?></td>
                                        <td><?= htmlspecialchars($r['vehicle_model']) ?></td>
                                        <td><?= htmlspecialchars($r['user_name']) ?></td>
                                        <td><?= str_repeat("⭐", $r['rating']) ?> (<?= $r['rating'] ?>)</td>
                                        <td><?= nl2br(htmlspecialchars($r['comment'])) ?></td>
                                        <td><?= $r['created_at'] ?></td>
                                        <td>
                                            <a href="?delete=<?= $r['review_id'] ?>&type=vehicle"
                                                onclick="return confirm('Are you sure you want to delete this vehicle review?');"
                                                class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No vehicle reviews found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>