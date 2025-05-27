<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        b.*, 
        h.name AS hotel_name, 
        h.image AS hotel_image, 
        d.name AS destination_name
    FROM booking b
    JOIN hotels h ON b.hotel_id = h.hotel_id
    JOIN destinations d ON b.destination_id = d.destination_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings - ExploreSri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #003049, #669bbc);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }
        .booking-card {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 15px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .booking-card img {
            height: 180px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }
        .card-body {
            color: #fff;
        }
        .status {
            font-weight: bold;
        }
        .status.Pending {
            color: #fcbf49;
        }
        .status.Confirmed {
            color: #80ed99;
        }
        .status.Cancelled {
            color: #ff6b6b;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h1 class="text-center mb-4">My Bookings</h1>

    <?php if (count($bookings) > 0): ?>
        <div class="row">
            <?php foreach ($bookings as $booking): ?>
                <div class="col-md-6">
                    <div class="card booking-card">
                        <img src="images/<?= htmlspecialchars($booking['hotel_image']) ?>" alt="Hotel Image">
                        <div class="card-body">
                            <h4><?= htmlspecialchars($booking['hotel_name']) ?></h4>
                            <p><strong>Destination:</strong> <?= htmlspecialchars($booking['destination_name']) ?></p>
                            <p><strong>Travel Date:</strong> <?= htmlspecialchars($booking['travel_date']) ?></p>
                            <p><strong>Status:</strong> <span class="status <?= $booking['status'] ?>"><?= $booking['status'] ?></span></p>
                            <p><small>Booked on <?= date('F j, Y', strtotime($booking['booking_date'])) ?></small></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center">You have no bookings yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
