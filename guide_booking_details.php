<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guide_id = $_POST['guide_id'];
    $travel_date = $_POST['travel_date'];
    $duration_days = $_POST['duration_days'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];
    $notes = $_POST['notes'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO guide_bookings (user_id, guide_id, travel_date, duration_days, payment_method, amount, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        $guide_id,
        $travel_date,
        $duration_days,
        $payment_method,
        $amount,
        $notes
    ]);

    $booking_id = $conn->lastInsertId();
} else {
    if (!isset($_GET['booking_id'])) {
        echo "<p class='text-danger text-center mt-5'>Booking not found.</p>";
        include 'includes/footer.php';
        exit;
    }
    $booking_id = (int)$_GET['booking_id'];
}

$stmt = $conn->prepare("
    SELECT gb.*, g.name AS guide_name, g.price_per_day 
    FROM guide_bookings gb 
    JOIN guide g ON gb.guide_id = g.guide_id 
    WHERE gb.booking_id = ? AND gb.user_id = ?
");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();

if (!$booking) {
    echo "<p class='text-danger text-center mt-5'>Booking not found or unauthorized.</p>";
    include 'includes/footer.php';
    exit;
}

$stmt = $conn->prepare("
    SELECT gb.*, g.name AS guide_name, g.price_per_day, g.photo AS guide_photo
    FROM guide_bookings gb
    JOIN guide g ON gb.guide_id = g.guide_id
    WHERE gb.booking_id = ? AND gb.user_id = ?
");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Guide Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Rubik', sans-serif;
            background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
            color: #fff;
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
            flex-wrap: wrap;
            overflow: hidden;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
            background-color: rgba(15, 23, 42, 0.95);
            width: 100%;
            transition: transform 0.3s ease;
        }
        .booking-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.8);
        }

        .booking-content {
            display: flex;
            padding: 20px 30px;
            width: 100%;
            gap: 30px;
            align-items: flex-start;
            z-index: 2;
        }

        .text-info {
            flex-grow: 1;
        }

        .text-info h4 {
            margin-bottom: 12px;
            font-size: 1.8rem;
            color: #fff;
        }

        .text-info p {
            margin: 6px 0;
            color: #e2e8f0;
            font-size: 1rem;
        }

        .status-line {
            font-size: 1rem;
            font-weight: 500;
            margin: 10px 0;
        }

        .badge {
            font-size: 0.9rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 12px;
        }

        .btn-cancel {
            padding: 10px 22px;
            font-size: 1rem;
            border-radius: 10px;
            background-color: #ef4444;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #dc2626;
        }

        .cancel-btn {
            align-self: start;
            margin-left: auto;
        }

        .info-section {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .info-section span {
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.07);
            padding: 6px 14px;
            border-radius: 10px;
            color: #cbd5e1;
        }
        .guide-photo {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 16px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            border: 2px solid #facc15; /* golden border to match your theme */
        }

    </style>
</head>
<body>

<div class="container mt-5">
    <h1>My Guide Bookings</h1>

    <?php if (!empty($booking)): ?>
        <div class="booking-card">
            <div class="booking-content">
                <div class="text-info">
                    <h4><?= htmlspecialchars($booking['guide_name']) ?></h4>
                    <p><strong>Travel Date:</strong> <?= htmlspecialchars($booking['travel_date']) ?></p>
                    <p><strong>Duration:</strong> <?= (int)$booking['duration_days'] ?> day(s)</p>

                    <div class="status-line">
                        <strong>Status:</strong>
                        <?php
                            $status = strtolower($booking['status'] ?? 'pending');
                            $status_class = match ($status) {
                                'confirmed' => 'badge bg-success',
                                'pending' => 'badge bg-warning text-dark',
                                'cancelled' => 'badge bg-danger',
                                default => 'badge bg-secondary',
                            };
                        ?>
                        <span class="<?= $status_class ?>"><?= ucfirst($status) ?></span>
                    </div>

                    <div class="status-line">
                        <strong>Payment Status:</strong>
                        <?php
                            $payment = strtolower($booking['payment_status'] ?? 'pending');
                            $payment_class = $payment === 'paid' ? 'badge bg-success' : 'badge bg-danger';
                        ?>
                        <span class="<?= $payment_class ?>"><?= ucfirst($payment) ?></span>
                    </div>

                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method'] ?? 'N/A') ?></p>
                    <p><strong>Amount Paid:</strong> $<?= number_format((float)$booking['amount'], 2) ?></p>
                    <p><strong>Notes:</strong> <br><small><?= nl2br(htmlspecialchars($booking['notes'] ?? 'No notes')) ?></small></p>
                </div>

                <div class="cancel-btn">
                    <form action="guide_delete.php" method="POST" onsubmit="return confirm('Cancel this booking?')">
                        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                        <button type="guide_delete" class="btn-cancel">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center mt-5">
            <h4>No bookings found</h4>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
