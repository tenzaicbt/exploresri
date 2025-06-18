<?php
session_start();
include '../config/db.php';

$company_name = $email = $phone = $address = $website = "";
$company_name_err = $email_err = $password_err = $confirm_password_err = $register_err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $company_name = trim($_POST['company_name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);
  $website = trim($_POST['website']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if (empty($company_name)) $company_name_err = "Company name is required.";
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $email_err = "Valid email is required.";
  if (empty($password)) $password_err = "Password is required.";
  if ($password !== $confirm_password) $confirm_password_err = "Passwords do not match.";

  $logo_path = null;

  // Handle logo upload
  if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/logos/';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
    $filename = uniqid('logo_', true) . '.' . $ext;
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
      $logo_path = 'uploads/logos/' . $filename;
    }
  }

  if (!$company_name_err && !$email_err && !$password_err && !$confirm_password_err) {
    $stmt = $conn->prepare("SELECT company_id FROM transport_companies WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
      $email_err = "Email is already registered.";
    } else {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $insert = $conn->prepare("INSERT INTO transport_companies (company_name, email, phone, address, website, password, logo, status)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
      $executed = $insert->execute([
        $company_name,
        $email,
        $phone ?: null,
        $address ?: null,
        $website ?: null,
        $hashed_password,
        $logo_path
      ]);
      if ($executed) {
        $_SESSION['company_id'] = $conn->lastInsertId();
        $_SESSION['company_name'] = $company_name;
        header("Location: login.php?success=1");
        exit;
      } else {
        $register_err = "Registration failed. Please try again.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Transport Company Registration</title>
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
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
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

    input.form-control,
    textarea.form-control {
      background: rgba(255, 255, 255, 0.05);
      border: none;
      color: #fff;
      border-radius: 10px;
    }

    input.form-control::placeholder {
      color: #aaa;
    }

    input.form-control:focus,
    textarea.form-control:focus {
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
  <div class="register-card">
    <h3><i class=""></i>Company Registration</h3>

    <?php if ($register_err): ?>
      <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-triangle me-2"></i><?= $register_err; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
      <div class="mb-3">
        <label class="form-label">Company Name</label>
        <input type="text" name="company_name" class="form-control <?= $company_name_err ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($company_name) ?>" placeholder="ABC Travels Pvt Ltd" />
        <div class="invalid-feedback"><?= $company_name_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control <?= $email_err ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($email) ?>" placeholder="contact@company.com" />
        <div class="invalid-feedback"><?= $email_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" placeholder="0771234567" />
      </div>

      <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" placeholder="123, Main Street, City"><?= htmlspecialchars($address) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Website</label>
        <input type="url" name="website" class="form-control" value="<?= htmlspecialchars($website) ?>" placeholder="https://www.company.com" />
      </div>

      <div class="mb-3">
        <label class="form-label">Company Logo</label>
        <input type="file" name="logo" class="form-control" accept="image/*" />
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control <?= $password_err ? 'is-invalid' : ''; ?>" placeholder="Enter password" />
        <div class="invalid-feedback"><?= $password_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control <?= $confirm_password_err ? 'is-invalid' : ''; ?>" placeholder="Re-enter password" />
        <div class="invalid-feedback"><?= $confirm_password_err; ?></div>
      </div>

      <button type="submit" class="btn btn-register w-100">Register</button>

      <div class="mt-3 text-center">
        Already have an account? <a href="login.php">Login</a>
      </div>
    </form>
  </div>
</body>

</html>