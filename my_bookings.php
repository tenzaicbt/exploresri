<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>You must be logged in to view your bookings.</p>";
    include 'includes/footer.php';
    exit;
}

$user_id = $_SESSION['user_id'];

// Cancel booking if requested
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $cancel_id = (int) $_GET['cancel'];
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$cancel_id, $user_id]);
    echo "<div class='alert alert-success'>Booking cancelled successfully.</div>";
}

// Fetch user bookings with hotel info
$stmt = $conn->prepare("
    SELECT b.*, h.name AS hotel_name
    FROM bookings b 
    JOIN hotels h ON b.hotel_id = h.hotel_id 
    WHERE b.user_id = ?
    ORDER BY b.booking_id DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();
?>

<h2>My Bookings</h2>

<?php if (count($bookings) === 0): ?>
    <p>You have no bookings.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Hotel</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Guests</th>
                <th>Booked On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?php echo htmlspecialchars($b['hotel_name']); ?></td>
                    <td><?php echo htmlspecialchars($b['check_in']); ?></td>
                    <td><?php echo htmlspecialchars($b['check_out']); ?></td>
                    <td><?php echo htmlspecialchars($b['guests']); ?></td>
                    <td><?php echo htmlspecialchars($b['created_at']); ?></td>
                    <td>
                        <a href="my_bookings.php?cancel=<?php echo $b['booking_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this booking?');">Cancel</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
