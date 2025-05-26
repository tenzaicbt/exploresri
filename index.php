<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ExploreSri - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #056676, #489fb5);
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }
    .hero {
      padding: 100px 20px;
      text-align: center;
    }
    .hero h1 {
      font-size: 3rem;
      margin-bottom: 20px;
    }
    .hero p {
      font-size: 1.25rem;
      margin-bottom: 40px;
    }
    .btn-custom {
      font-weight: bold;
      padding: 12px 30px;
      font-size: 1.1rem;
    }
  </style>
</head>
<body>
<div class="hero">
  <h1>Welcome to ExploreSri</h1>
  <p>Your gateway to travel and explore the beauty of Sri Lanka</p>
  <div class="d-flex justify-content-center gap-3 flex-wrap">
    <a href="destinations.php" class="btn btn-warning btn-custom"><i class="bi bi-geo-alt-fill"></i> Explore Destinations</a>
    <a href="hotels.php" class="btn btn-light btn-custom"><i class="bi bi-building"></i> Find Hotels</a>
  </div>
</div>
</body>
</html>
<?php include 'includes/footer.php'; ?>
