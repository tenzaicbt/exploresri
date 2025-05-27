<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/exploresri/index.php">
      <i class="bi bi-globe2"></i> ExploreSri
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="/exploresri/index.php"><i class="bi bi-house-door"></i> Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/exploresri/destinations.php"><i class="bi bi-geo-alt-fill"></i> Destinations</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/exploresri/hotels_all.php"><i class="bi bi-building"></i> Hotels</a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="/exploresri/my_bookings.php"><i class="bi bi-bookmark-check"></i> My Bookings</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/exploresri/logout.php">
              <i class="bi bi-box-arrow-right"></i> Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/exploresri/user/login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/exploresri/user/register.php"><i class="bi bi-person-plus"></i> Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
