<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['destination_id'])) {
    echo "<p>Please select a destination.</p>";
    include 'includes/footer.php';
    exit;
}

$destination_id = (int) $_GET['destination_id'];

// Fetch destination info
$stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
$stmt->execute([$destination_id]);
$destination = $stmt->fetch();

if (!$destination) {
    echo "<p>Destination not found.</p>";
    include 'includes/footer.php';
    exit;
}

// Fetch hotels in this destination
$stmt = $conn->prepare("SELECT * FROM hotels WHERE destination_id = ?");
$stmt->execute([$destination_id]);
$hotels = $stmt->fetchAll();
?>

<h1>Hotels in <?php echo htmlspecialchars($destination['name']); ?></h1>
<div class="row">
  <?php if (count($hotels) == 0): ?>
    <p>No hotels found for this destination.</p>
  <?php endif; ?>

  <?php foreach ($hotels as $hotel): ?>
    <div class="col-md-6 mb-4">
      <div class="card shadow-sm">
        <img src="images/<?php echo htmlspecialchars($hotel['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
        <div class="card-body">
          <h5 class="card-title"><?php echo htmlspecialchars($hotel['name']); ?></h5>
          <p class="card-text"><?php echo htmlspecialchars($hotel['description']); ?></p>
          <p><strong>Price per night:</strong> $<?php echo number_format($hotel['price'], 2); ?></p>
          <p><strong>Address:</strong> <?php echo htmlspecialchars($hotel['address']); ?></p>
          <p><strong>Contact:</strong> <?php echo htmlspecialchars($hotel['contact']); ?></p>
          <a href="booking_form.php?hotel_id=<?php echo $hotel['hotel_id']; ?>" class="btn btn-success">Book Now</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>


