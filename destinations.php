<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-5'><h2>Invalid destination ID.</h2></div>";
    include 'includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
$stmt->execute([$id]);
$dest = $stmt->fetch();

if (!$dest) {
    echo "<div class='container mt-5'><h2>Destination not found.</h2></div>";
    include 'includes/footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($dest['name']); ?> - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #003049, #669bbc);
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 900px;
      margin-top: 50px;
    }
    .img-fluid {
      border-radius: 15px;
      max-height: 400px;
      object-fit: cover;
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    .section {
      background-color: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 12px;
      margin-top: 20px;
      animation: fadeInUp 0.6s ease;
    }
    .section h4 {
      border-bottom: 1px solid rgba(255,255,255,0.3);
      padding-bottom: 5px;
    }
    iframe {
      width: 100%;
      height: 350px;
      border-radius: 12px;
      border: 0;
    }
    .btn-warning {
      margin-top: 30px;
    }

    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<div class="container text-center">
  <h1><?php echo htmlspecialchars($dest['name']); ?></h1>
  <img src="images/<?php echo htmlspecialchars($dest['image']); ?>" class="img-fluid my-4" alt="<?php echo htmlspecialchars($dest['name']); ?>">

  <div class="section text-start">
    <h4>Description</h4>
    <p><?php echo nl2br(htmlspecialchars($dest['description'])); ?></p>
  </div>

  <div class="section text-start">
    <h4>Province</h4>
    <p><?php echo htmlspecialchars($dest['province']); ?></p>
  </div>

  <div class="section text-start">
    <h4>Location</h4>
    <p><?php echo htmlspecialchars($dest['location']); ?></p>
  </div>

  <div class="section text-start">
    <h4>Category</h4>
    <p><?php echo htmlspecialchars($dest['category']); ?></p>
  </div>

  <div class="section text-start">
    <h4>Top Attractions</h4>
    <p><?php echo nl2br(htmlspecialchars($dest['top_attractions'])); ?></p>
  </div>

  <?php if (!empty($dest['latitude']) && !empty($dest['longitude'])): ?>
    <div class="section">
      <h4>Google Map</h4>
      <iframe
        src="https://www.google.com/maps?q=<?php echo $dest['latitude']; ?>,<?php echo $dest['longitude']; ?>&output=embed">
      </iframe>
    </div>
  <?php endif; ?>

  <a href="destinations.php" class="btn btn-warning">← Back to Destinations</a>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>
