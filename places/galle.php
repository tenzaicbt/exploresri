<?php include '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Explore Galle</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #e3f2fd, #ffffff);
      color: #333;
    }
    .hero {
      position: relative;
      background: url('../images/galle.jpg') center center / cover no-repeat;
      height: 60vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .hero h1 {
      font-size: 4rem;
      color: white;
      background: rgba(0, 0, 0, 0.5);
      padding: 20px;
      border-radius: 10px;
    }
    .container {
      margin-top: 40px;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      background: #ffffff;
    }
    .section-title {
      font-size: 1.8rem;
      font-weight: 600;
      color: #004d7a;
    }
    .map-container {
      height: 250px;
      width: 100%;
      border-radius: 15px;
      overflow: hidden;
      margin-top: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    iframe {
      width: 100%;
      height: 100%;
      border: 0;
    }
    ul {
      padding-left: 1.2rem;
    }
    ul li::marker {
      color: #0077b6;
    }
    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.5rem;
        padding: 15px;
      }
    }
  </style>
</head>
<body>

<section class="hero" data-aos="fade-in">
  <h1>Discover Galle</h1>
</section>

<div class="container">
  <div class="card p-4" data-aos="fade-up">
    <h2 class="section-title">About Galle</h2>
    <p><strong>Province:</strong> Sri Lanka</p>
    <p>Galle is one of the most popular tourist destinations in Sri Lanka, known for its beauty and unique experiences.</p>

    <h2 class="section-title mt-4" data-aos="fade-right">Top Attractions</h2>
    <ul>
      <li>Local Attraction 1</li>
      <li>Local Attraction 2</li>
      <li>Local Attraction 3</li>
    </ul>

    <h2 class="section-title mt-4" data-aos="zoom-in">Galle on the Map</h2>
    <div class="map-container">
      <iframe src="https://www.google.com/maps?q=Sri+Lanka+Galle&output=embed" allowfullscreen="" loading="lazy"></iframe>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    once: true
  });
</script>

</body>
</html>

<?php include '../includes/footer.php'; ?>
