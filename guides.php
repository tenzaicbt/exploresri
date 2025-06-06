<?php
include 'config/db.php';
include 'includes/header.php';

$stmt = $conn->query("SELECT * FROM guide WHERE status = 'active' AND is_verified = 1");
$guides = $stmt->fetchAll();

$search = $_GET['search'] ?? '';
$province = $_GET['province'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 8;  // changed to 8 cards per page
$offset = ($page - 1) * $limit;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Hotels - ExploreSri</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet" />

<style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #ffffff;
      min-height: 100vh;
      overflow-x: hidden;
    }

    h1 {
      font-size: 2.5rem;
      font-weight: 600;
      margin: 40px 0;
      text-align: center;
      color: #f1c40f;
    }

  .hotel-card-modern {
    position: relative;
    border-radius: 18px;
    overflow: hidden;
    background: #1b2735;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.5);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .hotel-card-modern:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6);
  }

  .hotel-card-modern img {
    width: 100%;
    height: 170px;
    object-fit: cover;
  }

  .image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    height: 170px;
    width: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.8));
  }

  .hotel-card-modern .card-body {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .hotel-card-modern .card-title {
    color: #fff;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.4rem;
  }

  .hotel-card-modern .card-text {
    color: #bfbfbf;
    font-size: 0.85rem;
    margin-bottom: 0.6rem;
  }

  .hotel-card-modern .price {
    color: #ffdd57;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 0.6rem;
  }

  .rating-stars i {
    font-size: 0.85rem;
  }

  .btn-book {
    font-size: 0.85rem;
    border-radius: 30px;
    background-color: transparent;
    border: 1px solid #f1c40f;
    color: #f1c40f;
    transition: all 0.3s ease;
  }

  .btn-book:hover {
    background-color: #f1c40f;
    color: #1e1e2f;
  }
</style>

</head>

<body>
  <div class="container">
    <h1>FIND YOUR GUIDES</h1>

    <div class="row g-4">
      <?php foreach ($guides as $guide): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card hotel-card-modern h-100 position-relative">
            <img src="uploads/guides/<?php echo htmlspecialchars($guide['photo']); ?>" alt="<?php echo htmlspecialchars($guide['name']); ?>">
            <div class="image-overlay"></div>

            <div class="card-body text-start d-flex flex-column">
              <h5 class="card-title text-warning"><?php echo htmlspecialchars($guide['name']); ?></h5>

              <p class="card-text mb-1"><strong>Languages:</strong> <?php echo htmlspecialchars($guide['languages']); ?></p>
              <p class="card-text mb-1"><strong>Experience:</strong> <?php echo $guide['experience_years']; ?> years</p>
              <p class="price mb-1">$<?php echo number_format($guide['price_per_day'], 2); ?> / day</p>

              <div class="d-flex align-items-center mb-3">
                <span class="badge bg-warning text-dark me-2"><?php echo number_format($guide['rating'], 1); ?></span>
                <div class="text-warning rating-stars">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi <?= $i <= round($guide['rating']) ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                  <?php endfor; ?>
                </div>
              </div>

              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/exploresri/guide_bookings.php?guide_id=<?= $guide['guide_id']; ?>" class="btn btn-book w-100 mt-auto">Book Guide</a>
              <?php else: ?>
                <a href="http://localhost/exploresri/user/login.php" class="btn btn-book w-100 mt-auto">Login to Book</a>
              <?php endif; ?>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
