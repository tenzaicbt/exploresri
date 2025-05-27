<?php
include 'config/db.php';
include 'includes/header.php';

$stmt = $conn->prepare("SELECT * FROM destinations");
$stmt->execute();
$destinations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Explore Destinations</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #003049, #669bbc);
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
    }
    h1 {
      margin-top: 2rem;
      text-align: center;
      font-size: 2.5rem;
    }
    .card {
      background: #ffffff10;
      border: none;
      color: white;
      backdrop-filter: blur(10px);
    }
    .card img {
      height: 200px;
      object-fit: cover;
      border-top-left-radius: .5rem;
      border-top-right-radius: .5rem;
    }
    .card-body {
      background-color: rgba(255,255,255,0.05);
      padding: 1rem;
    }
    .btn-primary {
      background-color: #ffc107;
      color: #000;
      border: none;
    }
    .btn-primary:hover {
      background-color: #e0a800;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h1 class="mb-4">Explore Destinations in Sri Lanka</h1>
  <div class="row">
    <?php foreach ($destinations as $dest): ?>
      <div class="col-md-6 mb-4">
        <div class="card shadow">
          <img src="images/<?php echo htmlspecialchars($dest['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($dest['name']); ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($dest['name']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($dest['description']); ?></p>

              <?php
                $destinationName = strtolower($dest['name']);
                if ($destinationName === 'colombo') {
                    echo '<a href="places/colombo.php" class="btn btn-primary">View</a>';
                } else {
                    echo '<a href="destination.php?id=' . $dest['destination_id'] . '" class="btn btn-primary">View</a>';
                }
              ?>


          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
