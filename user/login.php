<?php
include '../config/db.php';
session_start();

$email = $password = "";
$email_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }
    // Validate password
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
<style>
body {
  background: #f8f9fa;
}
.card {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  border-radius: 10px;
}
</style>
</head>
<body>
<div class="card shadow-sm">
    <h3 class="text-center mb-4">Login to Your Account</h3>

    <?php if ($login_err): ?>
        <div class="alert alert-danger"><?php echo $login_err; ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control <?php echo ($email_err) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" required />
            <div class="invalid-feedback"><?php echo $email_err; ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control <?php echo ($password_err) ? 'is-invalid' : ''; ?>" required />
            <div class="invalid-feedback"><?php echo $password_err; ?></div>
        </div>
        <button type="submit" class="btn btn-success w-100">Login</button>
    </form>
    <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
