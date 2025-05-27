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
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      background-color: rgba(255, 255, 255, 0.1);
      border: none;
      border-radius: 15px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    .card img {
      height: 200px;
      object-fit: cover;
    }
    .card-title {
      color: #fff;
      font-size: 1.2rem;
      font-weight: bold;
    }
    .btn-primary {
      background-color: #fcbf49;
      border: none;
      color: #000;
      font-weight: 500;
    }
    .btn-primary:hover {
      background-color: #f7b733;
    }
    h1 {
      font-size: 2.5rem;
      font-weight: 600;
      margin-bottom: 2rem;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <h1 class="text-center">Popular Destinations in Sri Lanka</h1>

  <div class="row">
    <?php if (count($destinations) > 0): ?>
      <?php foreach ($destinations as $dest): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="images/<?php echo htmlspecialchars($dest['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($dest['name']); ?>">
            <div class="card-body text-center">
              <h5 class="card-title"><?php echo htmlspecialchars($dest['name']); ?></h5>
              <a href="destination.php?id=<?php echo $dest['destination_id']; ?>" class="btn btn-primary mt-2">View</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">No destinations found.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>
