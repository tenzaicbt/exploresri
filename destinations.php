<?php
include 'config/db.php';
include 'includes/header.php';

// Fetch all destinations
$stmt = $conn->prepare("SELECT * FROM destinations");
$stmt->execute();
$destinations = $stmt->fetchAll();
?>

<h1>Explore Destinations in Sri Lanka</h1>
<div class="row">
  <?php foreach ($destinations as $dest): ?>
    <div class="col-md-6 mb-4">
      <div class="card shadow-sm">
        <img src="images/<?php echo htmlspecialchars($dest['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($dest['name']); ?>">
        <div class="card-body">
          <h5 class="card-title"><?php echo htmlspecialchars($dest['name']); ?></h5>
          <p class="card-text"><?php echo htmlspecialchars($dest['description']); ?></p>
          <a href="hotels.php?destination_id=<?php echo $dest['destination_id']; ?>" class="btn btn-primary">View Hotels</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
