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
        h.location AS hotel_location,
        p.amount,
        p.payment_method,
        p.payment_date
    FROM bookings b
    JOIN hotels h ON b.hotel_id = h.hotel_id
    LEFT JOIN payments p ON b.booking_id = p.booking_id
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
    <title>MY BOKINGS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #ffffff;
      min-height: 100vh;
      overflow-x: hidden;
    }


        h1 {
            font-size: 2.4rem;
            font-weight: 600;
            text-align: center;
            margin: 30px 0;
            color: #facc15;
        }

        .booking-card {
            position: relative;
            display: flex;
            overflow: hidden;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
            background-color: rgba(15, 23, 42, 0.9);
            height: auto;
            width: 100%;
        }

        .booking-bg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-size: cover;
            background-position: center;
            opacity: 0.12;
            filter: blur(12px);
            z-index: 1;
        }

        .booking-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-start;
            padding: 20px 30px;
            width: 100%;
            gap: 30px;
        }

        .hotel-img {
            width: 230px;
            height: 160px;
            object-fit: cover;
            border-radius: 14px;
            flex-shrink: 0;
        }

        .text-info {
            flex-grow: 1;
        }

        .text-info h5 {
            margin: 0 0 12px;
            color: #fff;
            font-size: 1.6rem;
        }

        .text-info p {
            margin: 6px 0;
            color: #e2e8f0;
            font-size: 1rem;
        }

        .status-line {
            font-size: 1rem;
            font-weight: 500;
            margin: 8px 0;
        }

        .status-text {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-confirmed { background-color: #16a34a; color: #fff; }
        .status-pending   { background-color: #facc15; color: #000; }
        .status-cancelled { background-color: #dc2626; color: #fff; }
        .status-paid      { background-color: #22c55e; color: #fff; }
        .status-unpaid    { background-color: #b91c1c; color: #fff; }

        .cancel-btn {
            text-align: right;
        }

        .btn-cancel {
            padding: 8px 18px;
            font-size: 14px;
            border-radius: 8px;
            background-color: #ef4444;
            color: #fff;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #dc2626;
        }

        .info-section {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .info-section span {
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.07);
            padding: 6px 12px;
            border-radius: 8px;
            color: #cbd5e1;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>MY BOKINGS</h1>
    <?php if (count($bookings) > 0): ?>
        <?php foreach ($bookings as $booking): ?>
            <div class="booking-card">
                <div class="booking-bg" style="background-image: url('images/<?= htmlspecialchars($booking['hotel_image']) ?>');"></div>
                <div class="booking-content">
                    <img src="images/<?= htmlspecialchars($booking['hotel_image']) ?>" class="hotel-img" alt="Hotel Image">
                    <div class="text-info">
                        <h5><?= htmlspecialchars($booking['hotel_name']) ?></h5>
                        <p><strong>Location:</strong> <?= htmlspecialchars($booking['hotel_location']) ?></p>

                        <div class="status-line">
                            Booking Status:
                            <?php
                                $status = strtolower($booking['status']);
                                $status_class = match ($status) {
                                    'confirmed' => 'status-confirmed',
                                    'pending' => 'status-pending',
                                    'cancelled' => 'status-cancelled',
                                    default => 'status-pending',
                                };
                                echo "<span class='status-text $status_class'>" . ucfirst($status) . "</span>";
                            ?>
                        </div>

                        <div class="status-line">
                            Payment Status:
                            <?php
                                $payment = strtolower($booking['payment_status']);
                                $payment_class = $payment === 'paid' ? 'status-paid' : 'status-unpaid';
                                echo "<span class='status-text $payment_class'>" . ucfirst($payment) . "</span>";
                            ?>
                        </div>

                        <div class="info-section">
                            <span><strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in_date']) ?></span>
                            <span><strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out_date']) ?></span>
                            <span><strong>Nights:</strong> <?= htmlspecialchars($booking['nights']) ?></span>
                            <span><strong>Total Price:</strong> $<?= htmlspecialchars(number_format($booking['amount'], 2)) ?></span>
                            <?php if ($booking['payment_method']): ?>
                                <span><strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="cancel-btn">
                        <form action="delete_booking_user.php" method="POST" onsubmit="return confirm('Cancel this booking?')">
                            <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                            <button type="submit" class="btn-cancel"><i class=""></i> Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center text-white mt-5">
            <h4>No bookings found</h4>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
