<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

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
    FROM bookings b
    JOIN hotels h ON b.hotel_id = h.hotel_id
    LEFT JOIN destinations d ON b.destination_id = d.destination_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");

$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - ExploreSri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Rubik', sans-serif;
            background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
            color: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            }
        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            text-align: center;
            margin-top: 50px;
            margin-bottom: 30px;
            color: #f1c40f;
            position: relative;
            z-index: 1;
            }
        .booking-card {
            background-color: #1f2a38;
            border: none;
            border-radius: 16px;
            margin-bottom: 25px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.35);
            animation: fadeInUp 0.6s ease-in-out;
            transition: transform 0.3s ease;
        }
        .booking-card:hover {
            transform: translateY(-5px);
        }
        .booking-card img {
            height: 200px;
            object-fit: cover;
            border-radius: 16px 16px 0 0;
            width: 100%;
        }
        .card-body {
            padding: 20px;
            color: #f1f1f1;
        }
        .card-body h4 {
            font-weight: 600;
            color: #ffffff;
        }
        .card-body p,
        .card-body small,
        .card-body strong {
            color: #dcdcdc;
        }
        .status {
            font-weight: bold;
            text-transform: capitalize;
        }
        .status.pending {
            color: #fbbf24;
        }
        .status.confirmed {
            color: #22c55e;
        }
        .status.cancelled {
            color: #ef4444;
        }
        .text-success {
            color: #10b981 !important;
        }
        .text-warning {
            color: #facc15 !important;
        }
        .btn-danger {
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 8px;
        }
        @keyframes fadeInUp {
            from {opacity: 0; transform: translateY(30px);}
            to {opacity: 1; transform: translateY(0);}
        }
        @keyframes fadeInDown {
            from {opacity: 0; transform: translateY(-30px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h1 class="text-center mb-4">MY BOOKINGS</h1>

    <?php if (count($bookings) > 0): ?>
        <div class="row">
            <?php foreach ($bookings as $booking): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card booking-card">
                        <img src="images/<?= htmlspecialchars($booking['hotel_image']) ?>" alt="Hotel Image">
                        <div class="card-body">
                            <h4><?= htmlspecialchars($booking['hotel_name']) ?></h4>
                            <p><strong>Destination:</strong> <?= htmlspecialchars($booking['destination_name']) ?></p>
                            <p><strong>Travel Date:</strong> <?= htmlspecialchars($booking['travel_date']) ?></p>
                            <p><strong>Status:</strong> 
                                <span class="status <?= strtolower($booking['status']) ?>">
                                    <?= htmlspecialchars($booking['status']) ?>
                                </span>
                            </p>
                            <p><strong>Payment:</strong> 
                                <span class="<?= $booking['payment_status'] === 'paid' ? 'text-success' : 'text-warning' ?>">
                                    <?= ucfirst($booking['payment_status']) ?>
                                </span>
                            </p>
                            <p><small>Booked on <?= date('F j, Y', strtotime($booking['booking_date'])) ?></small></p>
                            <form action="delete_booking_user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this booking?')">
                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger mt-2">
                                    <i class="bi bi-trash"></i> Cancel Booking
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center mt-5">
            <h4>No bookings found</h4>
            <p>Start exploring and make your first booking today!</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
