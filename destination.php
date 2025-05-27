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
      margin-top: 3rem;
    }
    .card {
      background-color: rgba(255, 255, 255, 0.1);
      border: none;
      border-radius: 15px;
      padding: 2rem;
    }
    .card img {
      max-height: 400px;
      object-fit: cover;
      border-radius: 15px;
      margin-bottom: 20px;
    }
    h1, h5 {
      color: #fcbf49;
    }
    iframe {
      border-radius: 15px;
      width: 100%;
      height: 300px;
      margin-top: 20px;
    }
    .btn-back {
      background-color: #fcbf49;
      border: none;
      color: #000;
    }
    .btn-back:hover {
      background-color: #f7b733;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card">
    <h1><?php echo htmlspecialchars($dest['name']); ?></h1>
    <img src="images/<?php echo htmlspecialchars($dest['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($dest['name']); ?>">
    <p><?php echo nl2br(htmlspecialchars($dest['description'])); ?></p>

    <h5>Province:</h5>
    <p><?php echo htmlspecialchars($dest['province']); ?></p>

    <h5>Top Attractions:</h5>
    <p><?php echo htmlspecialchars($dest['top_attractions']); ?></p>

    <?php if (!empty($dest['latitude']) && !empty($dest['longitude'])): ?>
      <h5>Map Location:</h5>
      <iframe 
        src="https://maps.google.com/maps?q=<?php echo $dest['latitude']; ?>,<?php echo $dest['longitude']; ?>&hl=es;z=14&amp;output=embed"
        allowfullscreen>
      </iframe>
    <?php endif; ?>

    <a href="destinations.php" class="btn btn-back mt-4">← Back to Destinations</a>
  </div>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>
