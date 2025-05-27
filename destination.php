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
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: #f8f9fa;
      color: #333;
    }
    .destination-container {
      max-width: 1200px;
      margin: 60px auto;
      padding: 30px;
      border-radius: 20px;
      background-color: #ffffff;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      animation: fadeSlide 0.6s ease-out;
    }
    @keyframes fadeSlide {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .destination-header {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    .destination-header img {
      width: 100%;
      max-width: 600px;
      border-radius: 15px;
      object-fit: cover;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    }
    .destination-details {
      flex: 1;
      padding-left: 40px;
    }
    .destination-details h1 {
      font-size: 2.8rem;
      color: #003049;
      font-weight: 600;
    }
    .destination-details h5 {
      color: #555;
      margin-top: 20px;
    }
    .destination-details p {
      font-size: 1.1rem;
      line-height: 1.7;
      margin-bottom: 15px;
    }
    .action-buttons {
      margin-top: 30px;
    }
    .btn-custom {
      background-color: #003049;
      color: #fff;
      border: none;
      padding: 10px 22px;
      font-weight: 500;
      border-radius: 8px;
      margin-right: 10px;
      transition: all 0.3s ease;
    }
    .btn-custom:hover {
      background-color: #00507a;
      transform: scale(1.05);
    }
    .map-container {
      display: none;
      margin-top: 30px;
      transition: max-height 0.4s ease;
    }
    iframe {
      border: 0;
      border-radius: 12px;
      width: 100%;
      height: 350px;
    }
    @media (max-width: 992px) {
      .destination-header {
        flex-direction: column;
      }
      .destination-details {
        padding-left: 0;
        margin-top: 30px;
      }
    }
  </style>
</head>
<body>

<div class="container destination-container">
  <div class="destination-header">
    <img src="images/<?php echo htmlspecialchars($dest['image']); ?>" alt="<?php echo htmlspecialchars($dest['name']); ?>">
    <div class="destination-details">
      <h1><?php echo htmlspecialchars($dest['name']); ?></h1>
      <p><?php echo nl2br(htmlspecialchars($dest['description'])); ?></p>

      <h5>Province</h5>
      <p><?php echo htmlspecialchars($dest['province']); ?></p>

      <h5>Top Attractions</h5>
      <p><?php echo htmlspecialchars($dest['top_attractions']); ?></p>

      <div class="action-buttons">
        <?php if (!empty($dest['latitude']) && !empty($dest['longitude'])): ?>
          <button class="btn btn-custom" onclick="toggleMap()">📍 View Map</button>
        <?php endif; ?>
        <a href="destinations.php" class="btn btn-custom">← Back</a>
      </div>
    </div>
  </div>

  <?php if (!empty($dest['latitude']) && !empty($dest['longitude'])): ?>
    <div id="mapSection" class="map-container">
      <iframe 
        src="https://maps.google.com/maps?q=<?php echo $dest['latitude']; ?>,<?php echo $dest['longitude']; ?>&hl=es;z=14&amp;output=embed"
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
