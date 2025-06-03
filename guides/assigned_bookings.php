<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['guide_id'])) {
    header("Location: guide_login.php");
    exit;
}

$guide_id = $_SESSION['guide_id'];

// Handle Confirm or Cancel POST request from guide
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($booking_id > 0 && in_array($action, ['confirm', 'cancel'])) {
        $new_status = ($action === 'confirm') ? 'confirmed' : 'cancelled';

        $stmt = $conn->prepare("UPDATE guide_bookings SET status = ? WHERE booking_id = ? AND guide_id = ? AND status = 'pending'");
        $stmt->execute([$new_status, $booking_id, $guide_id]);

        header("Location: assigned_bookings.php");
        exit;
    }
}

// Fetch bookings for this guide
$stmt = $conn->prepare("SELECT * FROM guide_bookings WHERE guide_id = ? ORDER BY created_at DESC");
$stmt->execute([$guide_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle booking status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $updateStmt = $conn->prepare("UPDATE guide_bookings SET status = ? WHERE booking_id = ? AND guide_id = ?");
    $updateStmt->execute([$_POST['status'], $_POST['booking_id'], $guide_id]);
    header("Location: assigned_bookings.php");
    exit;
}

$bookings = [];

try {
    $stmt = $conn->prepare("
    SELECT 
        gb.booking_id,
        gb.travel_date,
        gb.duration_days,
        gb.status AS booking_status,
        gb.created_at,
        gb.payment_method,
        gb.amount,
        gb.check_in_date,
        gb.check_out_date,
        gb.notes,
        gb.guide_id,
        u.name AS user_name,
        u.email AS user_email,
        u.contact_number AS user_contact_number
    FROM guide_bookings gb
    LEFT JOIN users u ON gb.user_id = u.user_id
    WHERE gb.guide_id = ?
    ORDER BY gb.created_at DESC
");
    $stmt->execute([$guide_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $bookings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Assigned Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .truncate {
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .status-badge {
            text-transform: capitalize;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Assigned Guide Bookings</h2>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Booking ID</th>
                <th>Guide ID</th>
                <th>User Name</th>
                <th>User Email</th>
                <th>User Contact</th>
                <th>Travel Date</th>
                <th>Duration</th>
                <th>Booking Amount</th>
                <th>Payment Method</th>
                <th>Booking Status</th>
                <th>Change Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['booking_id']) ?></td>
                        <td><?= htmlspecialchars($b['guide_id']) ?></td>
                        <td class="truncate" title="<?= htmlspecialchars($b['user_name']) ?>"><?= htmlspecialchars($b['user_name']) ?></td>
                        <td><?= htmlspecialchars($b['user_email']) ?></td>
                        <td><?= htmlspecialchars($b['user_contact_number'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($b['travel_date']) ?></td>
                        <td><?= htmlspecialchars($b['duration_days']) ?> days</td>
                        <td>$<?= number_format($b['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($b['payment_method'] ?? 'N/A') ?></td>
                        <td>
                            <?php 
                                $bstatus = strtolower($b['booking_status'] ?? 'pending');
                                $badgeColor = match ($bstatus) {
                                    'confirmed' => 'success',
                                    'pending' => 'warning',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            ?>
                            <span class="badge bg-<?= $badgeColor ?> status-badge"><?= htmlspecialchars($bstatus) ?></span>
                        </td>
                        <td>
                            <form method="POST" onchange="this.submit()" class="m-0 p-0">
                                <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="pending" <?= $bstatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmed" <?= $bstatus === 'confirmed' ? 'selected' : '' ?>>Confirm</option>
                                    <option value="cancelled" <?= $bstatus === 'cancelled' ? 'selected' : '' ?>>Cancel</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="11" class="text-center">No bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="guide_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>