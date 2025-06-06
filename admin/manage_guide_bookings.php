<?php
session_start();
include '../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require '../vendor/autoload.php'; // Make sure PhpSpreadsheet is installed via Composer

// Export to Excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="guide_bookings.xls"');
    header('Cache-Control: max-age=0');

    echo "Booking ID\tGuide Name\tUser Name\tTravel Date\tDuration\tAmount\tPayment Method\tStatus\tCreated At\n";

    $stmt = $conn->query("
        SELECT gb.booking_id, g.name AS guide_name, u.name AS user_name, gb.travel_date, gb.duration_days, 
               gp.amount, gp.payment_method, gb.status, gb.created_at
        FROM guide_bookings gb
        JOIN guide g ON gb.guide_id = g.guide_id
        JOIN users u ON gb.user_id = u.user_id
        LEFT JOIN guide_payments gp ON gb.booking_id = gp.booking_id
        ORDER BY gb.created_at DESC
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['booking_id'] . "\t" .
             $row['guide_name'] . "\t" .
             $row['user_name'] . "\t" .
             $row['travel_date'] . "\t" .
             $row['duration_days'] . " day(s)\t" .
             ($row['amount'] ?? '') . "\t" .
             ($row['payment_method'] ?? '') . "\t" .
             ucfirst($row['status']) . "\t" .
             $row['created_at'] . "\n";
    }
    exit;
}


// Update booking status inline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $stmt = $conn->prepare("UPDATE guide_bookings SET status = ? WHERE booking_id = ?");
    $stmt->execute([$_POST['status'], $_POST['booking_id']]);
    header("Location: manage_guide_bookings.php");
    exit;
}

// Filters
$filters = [];
$params = [];

if (!empty($_GET['status'])) {
    $filters[] = 'gb.status = ?';
    $params[] = $_GET['status'];
}
if (!empty($_GET['user'])) {
    $filters[] = 'u.name LIKE ?';
    $params[] = '%' . $_GET['user'] . '%';
}
if (!empty($_GET['date'])) {
    $filters[] = 'DATE(gb.travel_date) = ?';
    $params[] = $_GET['date'];
}

$whereClause = $filters ? 'WHERE ' . implode(' AND ', $filters) : '';

$stmt = $conn->prepare("
    SELECT gb.*, g.name AS guide_name, u.name AS user_name, gp.amount, gp.payment_method
    FROM guide_bookings gb
    JOIN guide g ON gb.guide_id = g.guide_id
    JOIN users u ON gb.user_id = u.user_id
    LEFT JOIN guide_payments gp ON gb.booking_id = gp.booking_id
    $whereClause
    ORDER BY gb.created_at DESC
");
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Guide Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .filter-group { margin-bottom: 20px; }
        .form-control, .form-select { min-width: 150px; }
        .status-badge { text-transform: capitalize; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Manage Guide Bookings</h2>

    <form method="GET" class="row g-3 filter-group">
        <div class="col-md-3">
            <input type="text" name="user" class="form-control" placeholder="Search by User Name" value="<?= htmlspecialchars($_GET['user'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <?php foreach (['pending', 'confirmed', 'cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= (($_GET['status'] ?? '') === $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
        <div class="col-md-3 text-end">
            <a href="?export=excel" class="btn btn-success">Export to Excel</a>
        </div>
    </form>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Guide Name</th>
                <th>Travel Date</th>
                <th>Duration</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Change</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($bookings): ?>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= $b['booking_id'] ?></td>
                    <td><?= htmlspecialchars($b['user_name']) ?></td>
                    <td><?= htmlspecialchars($b['guide_name']) ?></td>
                    <td><?= $b['travel_date'] ?></td>
                    <td><?= $b['duration_days'] ?> day(s)</td>
                    <td>$<?= number_format($b['amount'] ?? 0, 2) ?></td>
                    <td><?= htmlspecialchars($b['payment_method'] ?? '') ?></td>
                    <td>
                        <?php
                        $status = strtolower($b['status']);
                        $badge = match ($status) {
                            'confirmed' => 'success',
                            'pending' => 'warning',
                            'cancelled' => 'danger',
                            default => 'secondary'
                        };
                        ?>
                        <span class="badge bg-<?= $badge ?> status-badge"><?= $status ?></span>
                    </td>
                    <td>
                        <form method="POST" onchange="this.submit()" class="m-0">
                            <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach (['pending', 'confirmed', 'cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-center">No bookings found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
