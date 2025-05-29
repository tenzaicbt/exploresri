<?php
include '../config/db.php';
session_start();

$name = $email = $password = $confirm_password = "";
$name_err = $email_err = $password_err = $confirm_password_err = $register_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $email_err = "This email is already registered.";
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must be at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password !== $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
        }
    }

    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        if ($stmt->execute([$name, $email, $hashed_password])) {
            $register_success = "Registration successful! You can <a href='login.php'>login now</a>.";
            $name = $email = $password = $confirm_password = "";
        } else {
            echo "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
    .invalid-feedback {
        color: #ff6b6b;
    }
    .alert-danger {
        background-color: #dc3545;
        border: none;
        color: white;
    }
    a {
        color: #00d4ff;
        text-decoration: none;
    }
    a:hover {
        color: #fff;
        text-decoration: underline;
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
  <div class="card shadow-sm">
    <h3 class="text-center mb-4">Create an Account</h3>

    <?php if ($register_success): ?>
      <div class="alert alert-success text-center"><?php echo $register_success; ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control <?php echo ($name_err) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($name); ?>" placeholder="Enter name" required />
        <div class="invalid-feedback"><?php echo $name_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control <?php echo ($email_err) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter email" required />
        <div class="invalid-feedback"><?php echo $email_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <input type="password" name="password" id="password" class="form-control <?php echo ($password_err) ? 'is-invalid' : ''; ?>" placeholder="Enter password" required />
          <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">Show</button>
        </div>
        <div class="invalid-feedback d-block"><?php echo $password_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <div class="input-group">
          <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo ($confirm_password_err) ? 'is-invalid' : ''; ?>" placeholder="Confirm password" required />
          <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password', this)">Show</button>
        </div>
        <div class="invalid-feedback d-block"><?php echo $confirm_password_err; ?></div>
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-2">Register</button>
    </form>

    <p class="mt-2 text-center">Already have an account? <a href="login.php">Login here</a></p>
  </div>

  <script>
    function togglePassword(fieldId, btn) {
      const input = document.getElementById(fieldId);
      if (input.type === "password") {
        input.type = "text";
        btn.textContent = "Hide";
      } else {
        input.type = "password";
        btn.textContent = "Show";
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
