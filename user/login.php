<?php
include '../config/db.php';
session_start();

$email = $password = "";
$email_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter your email.";
  } else {
    $email = trim($_POST["email"]);
  }

  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter your password.";
  } else {
    $password = trim($_POST["password"]);
  }

  if (empty($email_err) && empty($password_err)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
      $_SESSION["user_id"] = $user["user_id"];
      $_SESSION["user_name"] = $user["name"];
      $_SESSION["role"] = $user["role"];
      header("Location: ../index.php");
      exit;
    } else {
      $login_err = "Invalid email or password.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Rubik&display=swap');

    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #fff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-card {
      background-color: #1b2735;
      border-radius: 18px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
      padding: 40px 30px;
      width: 100%;
      max-width: 420px;
      animation: fadeIn 0.6s ease-in-out;
      color: #fff;
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

    input.form-control {
      background: rgba(255, 255, 255, 0.05);
      border: none;
      color: #fff;
      border-radius: 10px;
    }

    input.form-control::placeholder {
      color: #aaa;
    }

    input.form-control:focus {
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
      transition: background-color 0.3s ease;
    }

    .btn-login:hover {
      background-color: #d4ac0d;
      color: #fff;
    }

    .btn-outline-login {
      border-color: #f1c40f;
      color: #f1c40f;
      border-radius: 30px;
    }

    .btn-outline-login:hover {
      background-color: #f1c40f;
      color: #1e1e2f;
    }

    .invalid-feedback {
      color: #ff6b6b;
    }

    .alert-danger {
      background-color: #c0392b;
      color: #fff;
      border: none;
      margin-bottom: 1rem;
    }

    a {
      color: #f1c40f;
      text-decoration: none;
      font-size: 0.95rem;
    }

    a:hover {
      color: #fff;
      text-decoration: underline;
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

    .input-group-text {
      background: transparent;
      border: none;
      color: #aaa;
    }

    /* Fix input-group with button */
    .input-group>.form-control {
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
    }

    .input-group>.btn {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
    }
  </style>
</head>

<body>
  <div class="login-card">
    <h3><i class="bi bi-person-circle me-2"></i>Login to Your Account</h3>

    <?php if ($login_err): ?>
      <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $login_err; ?></div>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="input-group">
          <input
            type="email"
            name="email"
            pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
            class="form-control <?= $email_err ? 'is-invalid' : ''; ?>"
            value="<?= htmlspecialchars($email); ?>"
            placeholder="Enter email"
            required />
          <div class="invalid-feedback"><?= $email_err; ?></div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <input
            type="password"
            name="password"
            id="password"
            class="form-control <?= $password_err ? 'is-invalid' : ''; ?>"
            placeholder="Enter password"
            required />
          <button class="btn btn-outline-light" type="button" onclick="togglePassword()" tabindex="-1">
            <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
          <div class="invalid-feedback"><?= $password_err; ?></div>
        </div>
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="rememberMe" />
        <label class="form-check-label text-light" for="rememberMe">Remember Me</label>
      </div>

      <button type="submit" class="btn btn-login w-100">Login</button>

      <div class="d-grid gap-2 mt-3">
        <a href="register.php" class="btn btn-outline-login w-100">Register</a>
        <a href="forgot_password.php" class="btn btn-outline-light w-100">Forgot Password?</a>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>