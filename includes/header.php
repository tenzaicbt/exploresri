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
  <style>
    body {
      background: linear-gradient(to right, #003049, #669bbc);
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
      animation: fadeInBody 0.8s ease-in;
    }

    @keyframes fadeInBody {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    nav.navbar {
      background-color: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      animation: slideDown 0.7s ease-out;
    }

    @keyframes slideDown {
      from {
        transform: translateY(-100px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .navbar-nav .nav-link {
      color: #fff !important;
      transition: color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
      color: #ffd60a !important;
      transform: scale(1.05);
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: bold;
      color: #fff !important;
      transition: transform 0.3s;
    }

    .navbar-brand:hover {
      transform: scale(1.1);
      color: #ffd60a !important;
    }

    .navbar .btn {
      transition: all 0.3s ease;
    }

    .navbar .btn:hover {
      transform: scale(1.05);
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container">
      <a class="navbar-brand" href="/exploresri/index.php">
        <i class="bi bi-globe2"></i> ExploreSri
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
          <li class="nav-item">
            <a class="nav-link" href="/exploresri/index.php"><i class=""></i> Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/exploresri/destinations.php"><i class=""></i> Destinations</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/exploresri/hotels_all.php"><i class=""></i> Hotels</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/exploresri/guides.php"><i class=""></i> Guide Rent</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/exploresri/transport.php"><i class=""></i> Transport Service</a>
          </li>

          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="/exploresri/my_bookings.php"><i class=""></i> My Bookings</a>
            </li>

            <!-- Profile Icon -->
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center" href="/exploresri/user/profile.php" title="Profile">
                <i class="bi bi-person-circle fs-5"></i>
              </a>
            </li>

            <!-- Logout Button -->
            <li class="nav-item">
              <a class="btn btn-outline-light btn-sm" href="/exploresri/logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout (<?= htmlspecialchars($_SESSION['user_name']); ?>)
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="btn btn-warning btn-sm" href="/exploresri/user/login.php">
                <i class="bi bi-box-arrow-in-right"></i> Login
              </a>
            </li>
            <li class="nav-item">
              <a class="btn btn-light btn-sm" href="/exploresri/user/register.php">
                <i class="bi bi-person-plus"></i> Register
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">