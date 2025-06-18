<?php
// destination.php

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
  <meta charset="UTF-8" />
  <title><?php echo htmlspecialchars($dest['name']); ?> - ExploreSri</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #fff;
      overflow-x: hidden;
    }

    .hero-section {
      height: 70vh;
      background-image: url('images/<?php echo htmlspecialchars($dest['image']); ?>');
      background-size: cover;
      background-position: center;
      position: relative;
      display: flex;
      align-items: center;
      color: white;
    }

    .hero-section .overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.9));
      z-index: 1;
    }

    .hero-section .container {
      position: relative;
      z-index: 2;
    }

    .nav-pills .nav-link {
      background-color: rgba(255, 255, 255, 0.05);
      color: #f1f1f1;
      border-radius: 50px;
      padding: 10px 20px;
      backdrop-filter: blur(6px);
      transition: all 0.3s ease;
      user-select: none;
    }

    .nav-pills .nav-link:hover:not(.active) {
      background-color: rgba(255, 255, 255, 0.15);
      color: #ffe57f;
    }

    .nav-pills .nav-link.active {
      background-color: #ffe57f;
      color: #000;
      font-weight: 600;
    }

    .destination-info {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      padding: 40px;
      margin-top: -80px;
      position: relative;
      z-index: 3;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(6px);
    }

    .destination-info h1 {
      font-size: 2.8rem;
      font-weight: 700;
      color: #f1c40f;
      margin-bottom: 1rem;
    }

    .destination-info p {
      color: #dcdcdc;
      font-size: 1.1rem;
      line-height: 1.5;
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

    .btn-custom {
      background-color: #f1c40f;
      color: #000;
      border: none;
      padding: 10px 24px;
      font-weight: bold;
      border-radius: 50px;
      margin-right: 10px;
      transition: all 0.3s ease;
      font-size: 0.95rem;
      user-select: none;
    }

    .btn-custom:hover {
      background-color: #ffd166;
      transform: scale(1.05);
    }

    .card {
      background: rgba(255, 255, 255, 0.05);
      border: none;
      border-radius: 16px;
      overflow: hidden;
      backdrop-filter: blur(6px);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.25);
      transition: transform 0.3s ease;
      height: 100%;
    }

    .card:hover {
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
    }

    .card-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #ffe57f;
    }

    .card-text {
      font-size: 0.95rem;
      color: #ccc;
    }
  </style>
</head>

<body>

  <div class="hero-section">
    <div class="overlay"></div>
    <div class="container text-center">
      <h1 class="display-4 fw-bold"><?php echo htmlspecialchars($dest['name']); ?></h1>
      <p class="lead"><?php echo htmlspecialchars($dest['category']); ?> · <?php echo htmlspecialchars($dest['province']); ?></p>
    </div>
  </div>

  <div class="bg-dark py-3">
    <div class="container">
      <ul class="nav nav-pills justify-content-center flex-wrap gap-2" id="sectionTabs">
        <li class="nav-item"><a class="nav-link active" href="#">Things To Do</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Best Time To Visit</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Book Your Trip</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Travel</a></li>
      </ul>
    </div>
  </div>

  <div class="container destination-info mt-4">
    <h1><?php echo htmlspecialchars($dest['name']); ?></h1>

    <!-- Paragraph 1: Intro -->
    <p class="lead mb-3">
      <?php echo nl2br(htmlspecialchars(substr($dest['description'], 0, 250))); ?>
    </p>

    <!-- Paragraph 2: Location & Province -->
    <p class="text-secondary mb-3">
      Nestled in the heart of <?php echo htmlspecialchars($dest['province']); ?> province, this destination is known for its cultural richness and stunning natural beauty. With scenic landscapes and historic relevance, it offers travelers a unique blend of relaxation and exploration.
    </p>

    <!-- Paragraph 3: Attractions -->
    <p class="mb-4">
      Top attractions include: <strong><?php echo htmlspecialchars($dest['top_attractions']); ?></strong>. Whether you're an adventure seeker, a history enthusiast, or simply looking for serenity, this place has something to offer every type of traveler.
    </p>

    <!-- Extra details -->
    <div class="row text-white mb-4">
      <div class="col-md-6 mb-3">
        <h5>Province</h5>
        <p class="text-warning"><?php echo htmlspecialchars($dest['province']); ?></p>
      </div>
      <div class="col-md-6 mb-3">
        <h5>Top Attractions</h5>
        <p class="text-info"><?php echo htmlspecialchars($dest['top_attractions']); ?></p>
      </div>
    </div>

    <!-- Action buttons -->
    <div class="action-buttons mt-4">
      <?php if (!empty($dest['latitude']) && !empty($dest['longitude'])): ?>
        <button class="btn btn-custom" onclick="toggleMap()"><i class="bi bi-map-fill"></i> View Map</button>
      <?php endif; ?>
      <a href="destinations.php" class="btn btn-custom"><i class="bi bi-arrow-left-circle-fill"></i> Back</a>
    </div>

    <!-- Map section -->
    <?php if (!empty($dest['latitude']) && !empty($dest['longitude'])): ?>
      <div id="mapSection" class="map-section mt-4">
        <iframe
          src="https://maps.google.com/maps?q=<?php echo $dest['latitude']; ?>,<?php echo $dest['longitude']; ?>&hl=en&z=14&output=embed"
          allowfullscreen>
        </iframe>
      </div>
    <?php endif; ?>
  </div>

  <div class="container mt-5">
    <h3 class="text-white mb-4">Other Destinations You May Like</h3>
    <div class="row g-4">
      <?php
      $recentStmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id != ? ORDER BY destination_id DESC LIMIT 3");
      $recentStmt->execute([$id]);
      while ($other = $recentStmt->fetch()):
      ?>
        <div class="col-md-4">
          <div class="card bg-dark text-white border-0 shadow h-100">
            <img src="images/<?php echo htmlspecialchars($other['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($other['name']); ?>">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo htmlspecialchars($other['name']); ?></h5>
              <p class="card-text"><?php echo htmlspecialchars(substr($other['description'], 0, 100)) . '...'; ?></p>
              <a href="destination.php?id=<?php echo $other['destination_id']; ?>" class="btn btn-warning text-dark mt-auto align-self-start">View</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <script>
    function toggleMap() {
      const map = document.getElementById("mapSection");
      if (map.style.display === "block") {
        map.style.display = "none";
      } else {
        map.style.display = "block";
        map.scrollIntoView({
          behavior: "smooth"
        });
      }
    }
  </script>

</body>

</html>

<?php include 'includes/footer.php'; ?>