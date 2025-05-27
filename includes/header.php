<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container">
    <a class="navbar-brand" href="/exploresri/index.php">ExploreSri</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/exploresri/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/exploresri/destinations.php">Destinations</a></li>
        <li class="nav-item"><a class="nav-link" href="/exploresri/hotels.php">Hotels</a></li>
        <?php 
        session_start(); 
        if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="/exploresri/my_bookings.php">My Bookings</a></li>
          <li class="nav-item"><a class="nav-link" href="/exploresri/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/exploresri/user/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/exploresri/user/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <header><h1>ExploreSri</h1><hr></header>
