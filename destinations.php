<?php
include 'config/db.php';
include 'includes/header.php';

// Fetch all destinations
$stmt = $conn->prepare("SELECT * FROM destinations ORDER BY destination_id DESC");
$stmt->execute();
$destinations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Destinations - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #003049, #669bbc);
      color: #fff;
    }
    .card {
      background-color: rgba(255, 255, 255, 0.1);
      border: none;
    }
    .card img {
      height: 200px;
      object-fit: cover;
    }
    .card-title {
      color: #fff;
    }
    .btn-primary {
      background-color: #fcbf49;
      border: none;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <h1 class="mb-4 text-center">Popular Destinations in Sri Lanka</h1>

  <div class="row">
    <?php if (count($destinations) > 0): ?>
      <?php foreach ($destinations as $dest): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="images/<?php echo htmlspecialchars($dest['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($dest['name']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($dest['name']); ?></h5>
              <a href="destination.php?id=<?php echo $dest['destination_id']; ?>" class="btn btn-primary">View</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No destinations found.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>
