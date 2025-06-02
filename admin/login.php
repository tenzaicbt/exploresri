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
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      background-color: rgba(255, 255, 255, 0.05);
      border: none;
      border-radius: 15px;
      padding: 30px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
      animation: fadeIn 0.6s ease-in-out;
      color: #fff;
    }
    .form-control {
      background-color: rgba(255, 255, 255, 0.1);
      border: none;
      color: #fff;
    }
    .form-control::placeholder {
      color: #bbb;
    }
    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.15);
      color: #fff;
      border-color: #28a745;
      box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    .form-label {
      color: #ddd;
    }
    .btn-success {
      background-color: #28a745;
      border: none;
      font-weight: bold;
    }
    .btn-success:hover {
      background-color: #218838;
    }
    .alert-danger {
      background-color: #dc3545;
      border: none;
      color: white;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .input-group-text {
      background: transparent;
      border: none;
      color: #aaa;
    }
  </style>
</head>
<body>
  <div class="card">
    <h3 class="text-center mb-4"><i class="bi bi-shield-lock-fill"></i> Admin Login</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" required>
        </div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
          <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()" tabindex="-1">
            <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
        </div>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-success">Login</button>
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
