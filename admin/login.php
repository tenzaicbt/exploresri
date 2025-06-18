<?php
session_start();
include '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
  $stmt->execute([$username]);
  $admin = $stmt->fetch();

  if ($admin) {
    if (password_verify($password, $admin['password'])) {
      $_SESSION['admin'] = $admin['username'];
      header('Location: dashboard.php');
      exit;
    } else {
      $error = "Password incorrect.";
    }
  } else {
    $error = "Username not found.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #ffffff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      background-color: #1b2735;
      border-radius: 18px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
      padding: 40px 30px;
      width: 100%;
      max-width: 420px;
      animation: fadeIn 0.6s ease-in-out;
    }

    .login-card h3 {
      text-align: center;
      color: #f1c40f;
      margin-bottom: 1.5rem;
    }

    .form-label {
      color: #ccc;
      font-weight: 500;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.05);
      border: none;
      color: #fff;
      border-radius: 10px;
    }

    .form-control::placeholder {
      color: #aaa;
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.1);
      border-color: #f1c40f;
      box-shadow: 0 0 0 0.25rem rgba(241, 196, 15, 0.25);
      color: #fff;
    }

    .btn-login {
      background-color: #f1c40f;
      border: none;
      color: #1e1e2f;
      font-weight: 600;
      border-radius: 30px;
    }

    .btn-login:hover {
      background-color: #d4ac0d;
      color: #fff;
    }

    .input-group-text {
      background: transparent;
      border: none;
      color: #ccc;
    }

    .alert-danger {
      background-color: #c0392b;
      color: #fff;
      border: none;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(40px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>
  <div class="login-card">
    <h3><i class="bi bi-shield-lock-fill me-2"></i>Admin Login</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" required>
        </div>
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
          <button type="button" class="btn btn-outline-light" onclick="togglePassword()" tabindex="-1">
            <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
        </div>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-login">Login</button>
      </div>
    </form>
  </div>

  <script>
    function togglePassword() {
      const password = document.getElementById("password");
      const toggleIcon = document.getElementById("toggleIcon");
      if (password.type === "password") {
        password.type = "text";
        toggleIcon.classList.remove("bi-eye");
        toggleIcon.classList.add("bi-eye-slash");
      } else {
        password.type = "password";
        toggleIcon.classList.remove("bi-eye-slash");
        toggleIcon.classList.add("bi-eye");
      }
    }
  </script>
</body>

</html>