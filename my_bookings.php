<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Hotel Bookings
$stmt = $conn->prepare("
    SELECT 
        b.*, 
        h.name AS hotel_name, 
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
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Guide Bookings
$guideStmt = $conn->prepare("
    SELECT 
        gb.*, 
        g.name AS guide_name, 
        g.country, 
        gp.amount, 
        gp.payment_method, 
        gp.payment_date
    FROM guide_bookings gb
    JOIN guide g ON gb.guide_id = g.guide_id
    LEFT JOIN guide_payments gp ON gb.booking_id = gp.booking_id
    WHERE gb.user_id = ?
    ORDER BY gb.created_at DESC
");
$guideStmt->execute([$user_id]);
$guideBookings = $guideStmt->fetchAll(PDO::FETCH_ASSOC);

// Vehicle Bookings
$vehicleStmt = $conn->prepare("
    SELECT 
        vb.*, 
        v.model, 
        v.type, 
        v.capacity, 
        vp.amount, 
        vp.payment_method, 
        vp.payment_date
    FROM vehicle_bookings vb
    JOIN vehicles v ON vb.vehicle_id = v.vehicle_id
    LEFT JOIN vehicle_payments vp ON vb.booking_id = vp.booking_id
    WHERE vb.user_id = ?
    ORDER BY vb.created_at DESC
");
$vehicleStmt->execute([$user_id]);
$vehicleBookings = $vehicleStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>MY BOOKINGS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Rubik', sans-serif;
            background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
            color: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        h1 {
            font-weight: 600;
            text-align: center;
            color: #facc15;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .booking-card {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            padding: 20px;
        }

        .booking-card h5 {
            font-size: 1.2rem;
            color: #fff;
            margin-bottom: 8px;
        }

        .booking-card p {
            font-size: 0.9rem;
            margin: 3px 0;
            color: #cbd5e1;
        }

        .status-line {
            font-weight: 600;
            font-size: 0.9rem;
            margin: 8px 0 5px 0;
            color: #facc15;
            /* bright yellow for captions */
        }

        .status-line span.status-text {
            font-weight: 700;
            margin-left: 8px;
            text-transform: uppercase;
        }

        .status-confirmed {
            color: #16a34a;
        }

        /* Green */
        .status-pending {
            color: #facc15;
        }

        /* Yellow */
        .status-cancelled {
            color: #dc2626;
        }

        /* Red */
        .status-paid {
            color: #22c55e;
        }

        /* Green */
        .status-unpaid {
            color: #b91c1c;
        }

        /* Dark Red */

        .info-section {
            margin-top: 10px;
        }

        .info-section span {
            display: inline-block;
            background: rgba(255, 255, 255, 0.08);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin: 5px 10px 5px 0;
            color: #cbd5e1;
        }

        .btn-cancel {
            margin-top: 15px;
            padding: 6px 14px;
            font-size: 0.85rem;
            border-radius: 6px;
            background-color: #ef4444;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #dc2626;
        }

        .category-section {
            margin-bottom: 40px;
        }
    </style>

</head>

<body>
    <div class="container">
        <h1>MY BOOKINGS</h1>

        <!-- HOTEL BOOKINGS -->
        <?php if (count($bookings) > 0): ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <h5><?= htmlspecialchars($booking['hotel_name']) ?></h5>
                    <p><strong>Location:</strong> <?= htmlspecialchars($booking['hotel_location']) ?></p>
                    <div class="status-line">
                        Booking Status:
                        <?php
                        $status = strtolower($booking['status'] ?? 'pending');
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
                        $payment = strtolower($booking['payment_status'] ?? 'unpaid');
                        $payment_class = $payment === 'paid' ? 'status-paid' : 'status-unpaid';
                        echo "<span class='status-text $payment_class'>" . ucfirst($payment) . "</span>";
                        ?>
                    </div>
                    <div class="info-section">
                        <span><strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in_date']) ?></span>
                        <span><strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out_date']) ?></span>
                        <span><strong>Nights:</strong> <?= htmlspecialchars($booking['nights']) ?></span>
                        <span><strong>Total Price:</strong> $<?= htmlspecialchars(number_format($booking['amount'] ?? 0, 2)) ?></span>
                        <?php if (!empty($booking['payment_method'])): ?>
                            <span><strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method']) ?></span>
                        <?php endif; ?>
                    </div>
                    <form action="delete_booking_user.php" method="POST" onsubmit="return confirm('Cancel this booking?')">
                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>" />
                        <button type="submit" class="btn-cancel">Cancel</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- GUIDE BOOKINGS -->
        <?php if (count($guideBookings) > 0): ?>
            <?php foreach ($guideBookings as $booking): ?>
                <div class="booking-card">
                    <h5><?= htmlspecialchars($booking['guide_name']) ?> (Guide Person)</h5>
                    <p><strong>Country:</strong> <?= htmlspecialchars($booking['country']) ?></p>
                    <div class="status-line">
                        Booking Status:
                        <?php
                        $status = strtolower($booking['status'] ?? 'pending');
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
                        $payment = strtolower($booking['payment_status'] ?? 'unpaid');
                        $payment_class = $payment === 'paid' ? 'status-paid' : 'status-unpaid';
                        echo "<span class='status-text $payment_class'>" . ucfirst($payment) . "</span>";
                        ?>
                    </div>
                    <div class="info-section">
                        <span><strong>Travel Date:</strong> <?= htmlspecialchars($booking['travel_date']) ?></span>
                        <span><strong>Duration:</strong> <?= htmlspecialchars($booking['duration_days']) ?> days</span>
                        <span><strong>Total Price:</strong> $<?= htmlspecialchars(number_format($booking['amount'] ?? 0, 2)) ?></span>
                        <?php if (!empty($booking['payment_method'])): ?>
                            <span><strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method']) ?></span>
                        <?php endif; ?>
                    </div>
                    <form action="guides/guide_delete.php" method="POST" onsubmit="return confirm('Cancel this guide booking?')">
                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>" />
                        <button type="submit" class="btn-cancel">Cancel</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- VEHICLE BOOKINGS -->
        <?php if (count($vehicleBookings) > 0): ?>
            <?php foreach ($vehicleBookings as $booking): ?>
                <div class="booking-card">
                    <h5><?= htmlspecialchars($booking['model']) ?> (Vehicle)</h5>
                    <p><strong>Type:</strong> <?= htmlspecialchars($booking['type']) ?> | <strong>Capacity:</strong> <?= htmlspecialchars($booking['capacity']) ?> seats</p>
                    <div class="status-line">
                        Booking Status:
                        <?php
                        $status = strtolower($booking['status'] ?? 'pending');
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
                        $payment_class = !empty($booking['amount']) ? 'status-paid' : 'status-unpaid';
                        $payment_status = !empty($booking['amount']) ? 'Paid' : 'Unpaid';
                        echo "<span class='status-text $payment_class'>" . $payment_status . "</span>";
                        ?>
                    </div>
                    <div class="info-section">
                        <span><strong>From:</strong> <?= htmlspecialchars($booking['booking_start']) ?></span>
                        <span><strong>To:</strong> <?= htmlspecialchars($booking['booking_end']) ?></span>
                        <span><strong>Total Price:</strong> $<?= htmlspecialchars(number_format($booking['amount'] ?? 0, 2)) ?></span>
                        <?php if (!empty($booking['payment_method'])): ?>
                            <span><strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method']) ?></span>
                        <?php endif; ?>
                    </div>
                    <form action="company/vehicle_delete.php" method="POST" onsubmit="return confirm('Cancel this vehicle booking?')">
                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>" />
                        <button type="submit" class="btn-cancel">Cancel</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (count($bookings) === 0 && count($guideBookings) === 0 && count($vehicleBookings) === 0): ?>
            <div class="text-center text-white mt-5">
                <h4>No bookings found</h4>
            </div>
        <?php endif; ?>

    </div>
    <?php include 'includes/footer.php'; ?>
</body>

</html>