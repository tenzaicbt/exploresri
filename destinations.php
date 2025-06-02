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
    }

    h1 {
      font-size: 2.5rem;
      font-weight: 700;
      text-align: center;
      margin-top: 50px;
      margin-bottom: 30px;
      color: #f1c40f;
    }

    .search-form {
      background: rgba(255, 255, 255, 0.05);
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 40px;
    }

    .search-form input,
    .search-form select {
      border-radius: 10px;
    }

    .search-btn {
      border-radius: 10px;
      font-weight: 600;
    }

    /* Smaller & Modern Card Styling */
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

    .card img {
      height: 140px;
      object-fit: cover;
      width: 100%;
    }

    .card-body {
      padding: 15px;
    }

    .card-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #ffe57f;
    }

    .card-subtext {
      font-size: 0.85rem;
      color: #ccc;
      margin-bottom: 5px;
    }

    .card-desc {
      font-size: 0.9rem;
      color: #ddd;
      height: 50px;
      overflow: hidden;
    }

    .btn-custom {
      background-color: #f1c40f;
      color: #000;
      border-radius: 25px;
      padding: 6px 18px;
      font-weight: 500;
      margin-top: 10px;
      font-size: 0.9rem;
    }

    .btn-custom:hover {
      background-color: #ffd166;
    }

    .pagination .page-link {
      background-color: #fff;
      color: #003049;
      border-radius: 10px;
      margin: 0 4px;
    }

    .pagination .active .page-link {
      background-color: #fcbf49;
      color: #000;
      border: none;
    }
  </style>
</head>

<body>
<div class="container">
  <h1>Explore Sri Lankan Destinations</h1>

  <form method="GET" class="search-form row g-3">
    <div class="col-md-5">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search destinations...">
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
      <button class="btn btn-warning search-btn">Search</button>
    </div>
  </form>

  <div class="row">
    <?php if ($destinations): ?>
      <?php foreach ($destinations as $dest): ?>
        <div class="col-md-4 col-sm-6 mb-4">
          <div class="card h-100">
            <img src="images/<?= htmlspecialchars($dest['image']) ?>" alt="<?= htmlspecialchars($dest['name']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($dest['name']) ?></h5>
              <div class="card-subtext"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($dest['province']) ?> | <?= htmlspecialchars($dest['category']) ?></div>
              <div class="card-desc"><?= htmlspecialchars(mb_strimwidth($dest['description'], 0, 90, "...")) ?></div>
              <a href="destination.php?id=<?= $dest['destination_id'] ?>" class="btn btn-custom">View More</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center mt-5">No destinations found.</p>
    <?php endif; ?>
  </div>

  <?php if ($totalPages > 1): ?>
    <nav class="mt-4 d-flex justify-content-center">
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
