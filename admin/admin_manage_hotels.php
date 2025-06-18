<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$stmt = $conn->query("SELECT h.*, d.name AS destination_name FROM hotels h LEFT JOIN destinations d ON h.destination_id = d.destination_id ORDER BY h.hotel_id DESC");
$hotels = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Hotels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Manage Hotels</h2>
        <div class="d-flex justify-content-end mb-3">
            <a href="add_hotel.php" class="btn btn-success">+ Add Hotel</a>
            <a href="dashboard.php" class="btn btn-secondary ms-2">Back to Dashboard</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Hotel Name</th>
                        <th>Destination</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hotels as $hotel): ?>
                        <tr>
                            <td><?= $hotel['hotel_id'] ?></td>
                            <td><?= htmlspecialchars($hotel['name']) ?></td>
                            <td><?= htmlspecialchars($hotel['destination_name']) ?></td>
                            <td>$<?= number_format($hotel['price'], 2) ?></td>
                            <td><img src="../images/<?= $hotel['image'] ?>" width="80" height="60" style="object-fit:cover;"></td>
                            <td><?= htmlspecialchars($hotel['contact']) ?></td>
                            <td>
                                <a href="edit_hotel.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete_hotel.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this hotel?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>