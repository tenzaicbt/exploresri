<?php 
include '../config/db.php';
session_start();

$name = $email = $password = $re_password = $contact_number = $country = "";
$name_err = $email_err = $password_err = $re_password_err = $contact_err = $register_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $re_password = $_POST["re_password"];
    $contact_number = trim($_POST["contact_number"]);
    $country = trim($_POST["country"]);

    if (empty($name)) $name_err = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $email_err = "Invalid email.";
    if (empty($password)) $password_err = "Password is required.";
    if ($password !== $re_password) $re_password_err = "Passwords do not match.";
    if (empty($contact_number)) $contact_err = "Contact number is required.";

    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($re_password_err) && empty($contact_err)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $email_err = "Email is already registered.";
        } else {
            $profile_pic = null;
            if (!empty($_FILES["profile_pic"]["name"])) {
                $target_dir = "../uploads/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                $filename = time() . '_' . basename($_FILES["profile_pic"]["name"]);
                $target_file = $target_dir . $filename;
                move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
                $profile_pic = $filename;
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, contact_number, country, profile_pic, is_verified, created_at, status, role)
                VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), 'active', 'user')");
            if ($stmt->execute([$name, $email, $hashed_password, $contact_number, $country, $profile_pic])) {
                header("Location: login.php?success=1");
                exit;
            } else {
                $register_err = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register - ExploreSri</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
            padding: 20px;
        }

        .register-card {
            background-color: #1b2735;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.5);
            padding: 40px 30px;
            width: 100%;
            max-width: 500px;
            animation: fadeIn 0.6s ease-in-out;
        }

        .register-card h3 {
            text-align: center;
            color: #f1c40f;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .form-label {
            color: #ccc;
            font-weight: 500;
        }

        input.form-control, select.form-select {
            background: rgba(255, 255, 255, 0.05);
            border: none;
            color: #fff;
            border-radius: 10px;
        }

        input.form-control::placeholder {
            color: #aaa;
        }

        input.form-control:focus, select.form-select:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #f1c40f;
            box-shadow: 0 0 0 0.25rem rgba(241, 196, 15, 0.25);
            color: #fff;
        }

        .btn-register {
            background-color: #f1c40f;
            border: none;
            color: #1e1e2f;
            font-weight: 600;
            border-radius: 30px;
        }

        .btn-register:hover {
            background-color: #d4ac0d;
            color: #fff;
        }

        .invalid-feedback {
            color: #ff6b6b;
        }

        .alert-danger {
            background-color: #c0392b;
            color: #fff;
            border: none;
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
    <div class="register-card">
        <h3><i class="bi bi-person-plus-fill me-2"></i>Create Your Account</h3>

        <?php if ($register_err): ?>
            <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-triangle me-2"></i><?= $register_err; ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control <?= $name_err ? 'is-invalid' : ''; ?>" placeholder="John Doe" value="<?= htmlspecialchars($name) ?>" />
                <div class="invalid-feedback"><?= $name_err; ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control <?= $email_err ? 'is-invalid' : ''; ?>" placeholder="example@mail.com" value="<?= htmlspecialchars($email) ?>" />
                <div class="invalid-feedback"><?= $email_err; ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control <?= $password_err ? 'is-invalid' : ''; ?>" placeholder="Enter password" />
                <div class="invalid-feedback"><?= $password_err; ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Re-enter Password</label>
                <input type="password" name="re_password" class="form-control <?= $re_password_err ? 'is-invalid' : ''; ?>" placeholder="Confirm password" />
                <div class="invalid-feedback"><?= $re_password_err; ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Country</label>
                <select name="country" class="form-select">
                    <option value="">Select your country</option>
                    <option value="Sri Lanka" <?= $country == "Sri Lanka" ? "selected" : "" ?>>Sri Lanka</option>
                    <option value="India" <?= $country == "India" ? "selected" : "" ?>>India</option>
                    <option value="USA" <?= $country == "USA" ? "selected" : "" ?>>USA</option>
                    <option value="UK" <?= $country == "UK" ? "selected" : "" ?>>UK</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="tel" name="contact_number" class="form-control <?= $contact_err ? 'is-invalid' : ''; ?>" placeholder="712345678" value="<?= htmlspecialchars($contact_number) ?>" />
                <div class="invalid-feedback"><?= $contact_err; ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" name="profile_pic" class="form-control" />
            </div>

            <button type="submit" class="btn btn-register w-100">Register</button>

            <div class="mt-3 text-center">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>
