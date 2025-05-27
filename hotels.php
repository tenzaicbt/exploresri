<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['destination_id']) || !is_numeric($_GET['destination_id'])) {
    echo "<div class='container mt-5'><h2>Invalid destination ID.</h2></div>";
    include 'includes/footer.php';
    exit;
}

$destination_id = (int)$_GET['destination_id'];

$stmt = $conn->prepare("SELECT * FROM hotels WHERE destination_id = ?");
$stmt->execute([$destination_id]);
$hotels = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hotels - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      border-radius: 20px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: none;
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .card img {
      height: 220px;
      object-fit: cover;
    }
    .card-body {
      background: #fff;
      padding: 1.5rem;
    }
    h1 {
      font-weight: 700;
      color: #343a40;
    }
    .rating {
      color: #fcbf49;
      font-size: 1.1rem;
    }
    .btn-book {
      background-color: #198754;
      color: white;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 8px;
    }
    .btn-book:hover {
      background-color: #157347;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <h1 class="text-center mb-4">Available Hotels</h1>

  <div class="row">
    <?php if (count($hotels) > 0): ?>
      <?php foreach ($hotels as $hotel): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="images/<?php echo htmlspecialchars($hotel['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($hotel['name']); ?></h5>
              <p class="text-muted mb-1"><?php echo htmlspecialchars($hotel['location']); ?></p>
              <p class="mb-1">Rs. <?php echo htmlspecialchars($hotel['price_per_night']); ?> / night</p>
              <p class="rating mb-2">★ <?php echo htmlspecialchars($hotel['rating']); ?> / 5</p>
              <a href="book.php?hotel_id=<?php echo $hotel['hotel_id']; ?>&destination_id=<?php echo $destination_id; ?>" class="btn btn-book w-100">Book Now</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning text-center">No hotels found for this destination.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>
