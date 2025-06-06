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
      margin: 0;
      padding: 0;
      background-color: #010409;
      color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
      position: relative;
      z-index: 1;
    }

    .highlighted-word {
      color: #ffd60a;
      font-weight: bold;
      animation: pulseGlow 2s ease-in-out infinite;
    }

    @keyframes pulseGlow {
      0%, 100% {
        text-shadow: 0 0 5px #ffd60a, 0 0 10px #ffd60a;
      }
      50% {
        text-shadow: 0 0 15px #ffd60a, 0 0 25px #ffd60a;
      }
    }

    .hero {
      padding: 0px 20px 80px;
      text-align: center;
      position: relative;
      z-index: 2;
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: bold;
      margin-bottom: 20px;
      text-shadow: 0 0 15px rgba(255, 255, 255, 0.15);
      animation: slideInDown 0.8s ease-out;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 40px;
      animation: fadeInBody 1.2s ease-in;
    }

    .btn-custom {
      font-weight: bold;
      padding: 12px 30px;
      font-size: 1.1rem;
      border-radius: 50px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-custom:hover {
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 8px 20px rgba(255, 255, 255, 0.2);
    }

    /* Background Animation */
    .background-animation {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      overflow: hidden;
      pointer-events: none;
      background: radial-gradient(circle at bottom, #020d18 0%, #010409 100%);
    }

    .bubble {
      position: absolute;
      bottom: -100px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.05);
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
      animation: bubbleFloat linear infinite;
      opacity: 0.7;
    }

    @keyframes bubbleFloat {
      0% {
        transform: translateY(0) scale(1);
        opacity: 0.2;
      }
      50% {
        opacity: 0.4;
      }
      100% {
        transform: translateY(-120vh) scale(1.2);
        opacity: 0;
      }
    }

    @keyframes fadeInBody {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideInDown {
      from { transform: translateY(-50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    /* Footer */
    .footer {
      background-color: #0a0f1c;
      padding: 30px 0;
      color: #ccc;
      text-align: center;
      z-index: 2;
      position: relative;
    }

    .footer p {
      margin: 0;
      font-size: 0.95rem;
    }

    .footer a {
      color: #ffd60a;
      text-decoration: none;
    }

    .auth-buttons {
      margin-top: 20px;
    }

    .auth-buttons a {
      margin: 5px;
    }
  </style>
</head>
<body>

<!-- Background bubbles -->
<div class="background-animation">
  <?php for ($i = 0; $i < 40; $i++): ?>
    <div class="bubble" style="
      left: <?= rand(0, 100) ?>%;
      width: <?= rand(10, 35) ?>px;
      height: <?= rand(10, 35) ?>px;
      animation-delay: <?= rand(0, 20) ?>s;
      animation-duration: <?= rand(12, 25) ?>s;">
    </div>
 </div>
<?php endfor; ?>



<!-- Hero Section -->
<div class="hero">
    <!-- h1 moved to bottom with animation -->
  <h1 class="mt-5">Welcome to ExploreSri</h1>
  <p>Your gateway to travel and explore the beauty of <span class="highlighted-word">Sri Lanka</span></p>

  <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
    <a href="destinations.php" class="btn btn-warning btn-custom">
      <i class=""></i> Explore Destinations
    </a>
    <a href="hotels_all.php" class="btn btn-light btn-custom">
      <i class=""></i> Find Hotels
    </a>
  </div>

  <?php if (!isset($_SESSION['user_id'])): ?>
  <div class="auth-buttons d-flex justify-content-center flex-wrap mt-4">
    <a href="/exploresri/user/login.php" class="btn btn-outline-light btn-custom">
      <i class="bi bi-box-arrow-in-right"></i> Login
    </a>
    <a href="/exploresri/user/register.php" class="btn btn-outline-warning btn-custom">
      <i class="bi bi-person-plus"></i> Register
    </a>
  </div>
  <?php endif; ?>

  <!-- 2 Column Animated Description -->
  <div class="container mt-5">
    <div class="row text-start" style="animation: fadeInBody 1.5s ease-in;">
      <div class="col-md-6 mb-4">
        <p><span class="highlighted-word">Sri Lanka</span>, officially the Democratic Socialist Republic of Sri Lanka, also known historically as Ceylon, is an island country in South Asia. It lies in the Indian Ocean, southwest of the Bay of Bengal, separated from the Indian peninsula by the Gulf of Mannar and the Palk Strait...</p>
        <p>...It shares a maritime border with the Maldives in the southwest and India in the northwest. Sri Jayawardenepura Kotte is the legislative capital of <span class="highlighted-word">Sri Lanka</span>...</p>
      </div>
      <div class="col-md-6 mb-4">
        <p>...Sri Lanka has a population of approximately 22 million and is home to several cultures, languages and ethnicities. The Sinhalese people form the majority...</p>
        <p>...Sri Lanka’s documented history goes back 3,000 years... it was already known to both East Asians and Europeans during the Anuradhapura period. <span class="highlighted-word">Sri Lanka</span> is also called the Pearl of the Indian Ocean...</p>
      </div>
          <div class="text-center mt-4">
      <img src="https://cdn.britannica.com/13/4413-050-98188B5C/Flag-Sri-Lanka.jpg" 
           alt="Sri Lanka Flag" 
           style="width:120px; height:auto;">
    </div>
    </div>
  </div>


</div>



<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p><?= date("Y") ?> ExploreSri. Crafted with to promote Sri Lankan tourism.</p>
  </div>
</footer>

</body>
</html>