<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-5'><h2 class='text-white'>Invalid destination ID.</h2></div>";
    include 'includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
$stmt->execute([$id]);
$dest = $stmt->fetch();

if (!$dest) {
    echo "<div class='container mt-5'><h2 class='text-white'>Destination not found.</h2></div>";
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #ffffff;
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
    }

    .destination-card {
      max-width: 1200px;
      margin: 60px auto;
      background: rgba(255, 255, 255, 0.05);
      padding: 40px;
      border-radius: 25px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(15px);
      animation: slideInUp 0.9s ease;
    }

    @keyframes slideInUp {
      from { opacity: 0; transform: translateY(80px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .destination-header {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
    }

    .destination-image {
      flex: 1;
      min-width: 300px;
    }

    .destination-image img {
      width: 100%;
      border-radius: 18px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
      object-fit: cover;
    }

    .destination-info {
      flex: 1.5;
    }

    .destination-info h1 {
      font-size: 2.8rem;
      font-weight: 600;
      color: #ffe57f;
    }

    .destination-info h5 {
      color: #cccccc;
      margin-top: 20px;
    }

    .destination-info p {
      font-size: 1.1rem;
      line-height: 1.7;
      color: #e0e0e0;
    }

    .action-buttons {
      margin-top: 30px;
    }

    .btn-custom {
      background-color: #f1c40f;
      color: #000;
      border: none;
      padding: 10px 24px;
      font-weight: bold;
      border-radius: 50px;
      margin-right: 10px;
      transition: all 0.3s ease;
    }

    .btn-custom:hover {
      background-color: #ffe57f;
      transform: scale(1.05);
    }

    .map-section {
      margin-top: 30px;
      display: none;
    }

    iframe {
      border: none;
      border-radius: 12px;
      width: 100%;
      height: 350px;
    }

    @media (max-width: 992px) {
      .destination-header {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

<div class="container destination-card">
  <div class="destination-header">
    <div class="destination-image">
      <img src="images/<?php echo htmlspecialchars($dest['image']); ?>" alt="<?php echo htmlspecialchars($dest['name']); ?>">
    </div>
    <div class="destination-info">
      <h1><i class=""></i> <?php echo htmlspecialchars($dest['name']); ?></h1>
      <p><?php echo nl2br(htmlspecialchars($dest['description'])); ?></p>

      <h5><i class="bi bi-pin-map-fill"></i> Province</h5>
      <p><?php echo htmlspecialchars($dest['province']); ?></p>

      <h5><i class=""></i> Top Attractions</h5>
      <p><?php echo htmlspecialchars($dest['top_attractions']); ?></p>

      <div class="action-buttons">
        <?php if (!empty($dest['latitude']) && !empty($dest['longitude'])): ?>
          <button class="btn btn-custom" onclick="toggleMap()"><i class="bi bi-map-fill"></i> View Map</button>
        <?php endif; ?>
        <a href="destinations.php" class="btn btn-custom"><i class="bi bi-arrow-left-circle-fill"></i> Back</a>
      </div>
    </div>
  </div>

  <?php if (!empty($dest['latitude']) && !empty($dest['longitude'])): ?>
    <div id="mapSection" class="map-section">
      <iframe 
        src="https://maps.google.com/maps?q=<?php echo $dest['latitude']; ?>,<?php echo $dest['longitude']; ?>&hl=en&z=14&output=embed"
        allowfullscreen>
      </iframe>
    </div>
  <?php endif; ?>
</div>

<script>
  function toggleMap() {
    const map = document.getElementById("mapSection");
    if (map.style.display === "block") {
      map.style.display = "none";
    } else {
      map.style.display = "block";
      map.scrollIntoView({ behavior: "smooth" });
    }
  }
</script>

</body>
</html>

<?php include 'includes/footer.php'; ?>
