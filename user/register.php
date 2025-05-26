<?php
include '../config/db.php';
session_start();

$name = $email = $password = $confirm_password = "";
$name_err = $email_err = $password_err = $confirm_password_err = $register_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $email_err = "This email is already registered.";
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must be at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
        }
    }

    // If no errors, insert new user
    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        if ($stmt->execute([$name, $email, $hashed_password])) {
            $register_success = "Registration successful! You can <a href='login.php'>login now</a>.";
            // Clear form data
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
  background: #f8f9fa;
}
.card {
  max-width: 450px;
  margin: 50px auto;
  padding: 20px;
  border-radius: 10px;
}
</style>
</head>
<body>
<div class="card shadow-sm">
    <h3 class="text-center mb-4">Create an Account</h3>

    <?php if ($register_success): ?>
        <div class="alert alert-success"><?php echo $register_success; ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control <?php echo ($name_err) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($name); ?>" required />
            <div class="invalid-feedback"><?php echo $name_err; ?></div>
        </div>
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
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control <?php echo ($confirm_password_err) ? 'is-invalid' : ''; ?>" required />
            <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
