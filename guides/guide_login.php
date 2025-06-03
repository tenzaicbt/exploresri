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

    // If no input errors, attempt login
    if (empty($email_err) && empty($password_err)) {
        // Prepare SQL to fetch guide by email
        $stmt = $conn->prepare("SELECT * FROM guide WHERE email = ?");
        $stmt->execute([$email]);
        $guide = $stmt->fetch();

        if ($guide && password_verify($password, $guide["password"])) {
            // Check if guide is verified and active (optional)
            if ($guide["is_verified"] && $guide["status"] == 'active') {
                // Set session variables
                $_SESSION["guide_id"] = $guide["guide_id"];
                $_SESSION["guide_name"] = $guide["name"];
                $_SESSION["guide_email"] = $guide["email"];
                $_SESSION["guide_role"] = "guide";

                // Redirect to guide dashboard or homepage
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
    <div class="card">
        <h3 class="text-center mb-4"><i class="bi bi-person-circle"></i> Guide Login</h3>

        <?php if ($login_err): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> <?php echo $login_err; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <input type="email" name="email" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" 
                        class="form-control <?php echo ($email_err) ? 'is-invalid' : ''; ?>" 
                        value="<?php echo htmlspecialchars($email); ?>" 
                        placeholder="Enter email" required />
                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" 
                        class="form-control <?php echo ($password_err) ? 'is-invalid' : ''; ?>" 
                        placeholder="Enter password" required />
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()" tabindex="-1">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                    <div class="invalid-feedback"><?php echo $password_err; ?></div>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100">Login</button>

            <div class="d-grid gap-2 mt-3">
                <a href="guide_register.php" class="btn btn-outline-success w-100">Register as Guide</a>
                <a href="guide_forgot_password.php" class="btn btn-outline-secondary w-100">Forgot Password?</a>
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
