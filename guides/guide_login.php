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
        $stmt = $conn->prepare("SELECT * FROM guide WHERE email = ?");
        $stmt->execute([$email]);
        $guide = $stmt->fetch();

        if ($guide && password_verify($password, $guide["password"])) {
            if ($guide["is_verified"] && $guide["status"] == 'active') {
                $_SESSION["guide_id"] = $guide["guide_id"];
                $_SESSION["guide_name"] = $guide["name"];
                $_SESSION["guide_email"] = $guide["email"];
                $_SESSION["guide_role"] = "guide";
                header("Location: ../guides/guide_dashboard.php");
                exit;
            } else {
                $login_err = "Your account is not verified or inactive. Please contact support.";
            }
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
  <title>Guide Login - ExploreSri</title>
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
      box-shadow: 0 8px 30px rgba(0,0,0,0.5);
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

    .btn-outline-custom {
      border: 1px solid #f1c40f;
      color: #f1c40f;
    }

    .btn-outline-custom:hover {
      background-color: #f1c40f;
      color: #000;
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

    .invalid-feedback {
      color: #ff6b6b;
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
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h3><i class="bi bi-person-circle me-2"></i>Guide Login</h3>

    <?php if ($login_err): ?>
      <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-triangle me-2"></i><?php echo $login_err; ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" name="email" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" 
              class="form-control <?php echo ($email_err) ? 'is-invalid' : ''; ?>" 
              value="<?php echo htmlspecialchars($email); ?>" 
              placeholder="Enter email" required />
        </div>
        <div class="invalid-feedback"><?php echo $email_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" id="password" 
              class="form-control <?php echo ($password_err) ? 'is-invalid' : ''; ?>" 
              placeholder="Enter password" required />
          <button class="btn btn-outline-light" type="button" onclick="togglePassword()" tabindex="-1">
              <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
        </div>
        <div class="invalid-feedback"><?php echo $password_err; ?></div>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-login">Login</button>
      </div>

      <div class="d-grid gap-2 mt-3">
        <a href="guide_register.php" class="btn btn-outline-custom">Register as Guide</a>
        <a href="guide_forgot_password.php" class="btn btn-outline-light">Forgot Password?</a>
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
