<?php
include 'config/db.php';
include 'includes/header.php';

$search = $_GET['search'] ?? '';
$province = $_GET['province'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 6;
$offset = ($page - 1) * $limit;

$filters = [];
$params = [];

if ($search) {
  $filters[] = "(name LIKE ? OR description LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
}
if ($province) {
  $filters[] = "province = ?";
  $params[] = $province;
}

$where = $filters ? "WHERE " . implode(" AND ", $filters) : "";

$countStmt = $conn->prepare("SELECT COUNT(*) FROM destinations $where");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

$query = "SELECT * FROM destinations $where ORDER BY destination_id DESC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$destinations = $stmt->fetchAll();

$provStmt = $conn->query("SELECT DISTINCT province FROM destinations");
$provinces = $provStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Destinations - ExploreSri</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: url('https://www.transparenttextures.com/patterns/stardust.png');
      opacity: 0.08;
      z-index: 0;
      pointer-events: none;
    }

    h1 {
      font-size: 2.8rem;
      font-weight: 700;
      text-align: center;
      margin-top: 50px;
      margin-bottom: 30px;
      color: #f1c40f;
      position: relative;
      z-index: 1;
    }

    .search-form {
      animation: fadeUp 0.8s ease-in-out;
      background: rgba(255,255,255,0.05);
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 40px;
      position: relative;
      z-index: 1;
      transition: all 0.3s ease;
    }

    .search-form:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
    }

    .search-form input,
    .search-form select {
      border-radius: 10px;
      box-shadow: none;
      transition: box-shadow 0.2s ease;
    }

    .search-form input:focus,
    .search-form select:focus {
      box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }

    .search-btn {
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.2s ease-in-out;
    }

    .search-btn:hover {
      transform: scale(1.03);
      background-color: #ffca2c;
    }

    .card {
      background: rgba(255, 255, 255, 0.06);
      border: none;
      border-radius: 18px;
      overflow: hidden;
      backdrop-filter: blur(6px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 20px 30px rgba(0, 0, 0, 0.4);
    }

    .card img {
      height: 200px;
      object-fit: cover;
      border-top-left-radius: 18px;
      border-top-right-radius: 18px;
    }

    .card-body {
      padding: 20px;
    }

    .card-title {
      font-size: 1.4rem;
      font-weight: 600;
      color: #ffe57f;
    }

    .card-subtext {
      font-size: 0.9rem;
      color: #ccc;
      margin-bottom: 10px;
    }

    .card-desc {
      font-size: 0.95rem;
      color: #ddd;
      height: 60px;
      overflow: hidden;
    }

    .btn-custom {
      background-color:#f1c40f;
      color: #000;
      border-radius: 30px;
      padding: 8px 22px;
      font-weight: 500;
      margin-top: 12px;
      transition: background-color 0.3s ease;
    }

    .btn-custom:hover {
      background-color: #ffd166;
    }

    .pagination .page-link {
      background-color: #fff;
      color: #003049;
      border-radius: 10px;
      margin: 0 5px;
    }

    .pagination .active .page-link {
      background-color: #fcbf49;
      color: #000;
      border: none;
    }

    .fade-up {
      animation: fadeUp 0.6s ease forwards;
      opacity: 0;
    }

    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>
<div class="container">
  <h1 class="fade-up">EXPLORE SRI LANKAN DESTINATIONS</h1>

  <!-- Creative Animated Filter UI -->
  <form method="GET" class="search-form row g-3 fade-up">
    <div class="col-md-5">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder=" Search destinations...">
    </div>
    <div class="col-md-4">
      <select name="province" class="form-select">
        <option value="">All Provinces</option>
        <?php foreach ($provinces as $prov): ?>
          <option value="<?= htmlspecialchars($prov) ?>" <?= $province === $prov ? 'selected' : '' ?>><?= htmlspecialchars($prov) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 d-grid">
      <button class="btn btn-warning search-btn"><i class=""></i> Search</button>
    </div>
  </form>

  <!-- Cards -->
  <div class="row mt-4 fade-up">
    <?php if ($destinations): ?>
      <?php foreach ($destinations as $dest): ?>
        <div class="col-md-4 col-sm-6 mb-4">
          <div class="card h-100">
            <img src="images/<?= htmlspecialchars($dest['image']) ?>" alt="<?= htmlspecialchars($dest['name']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($dest['name']) ?></h5>
              <div class="card-subtext"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($dest['province']) ?> | <?= htmlspecialchars($dest['category']) ?></div>
              <div class="card-desc"><?= htmlspecialchars(mb_strimwidth($dest['description'], 0, 100, "...")) ?></div>
              <a href="destination.php?id=<?= $dest['destination_id'] ?>" class="btn btn-custom">View More</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center mt-5">No destinations found.</p>
    <?php endif; ?>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <nav class="mt-4 d-flex justify-content-center fade-up">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="?search=<?= urlencode($search) ?>&province=<?= urlencode($province) ?>&page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
