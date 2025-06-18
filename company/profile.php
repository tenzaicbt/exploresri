<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['company_id'])) {
  header('Location: login.php');
  exit;
}

$company_id = $_SESSION['company_id'];

$company_name = $email = $phone = $address = $website = $logo = "";
$company_name_err = $phone_err = $address_err = $website_err = "";
$current_password_err = $password_err = $confirm_password_err = "";
$update_success = $update_err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $company_name = trim($_POST['company_name']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);
  $website = trim($_POST['website']);

  $current_password = $_POST['current_password'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  $new_logo_path = null;

  if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/logos/';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
    $filename = uniqid('logo_', true) . '.' . $ext;
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
      $new_logo_path = 'uploads/logos/' . $filename;
    }
  }

  if (empty($company_name)) {
    $company_name_err = "Company name is required.";
  }

  if ($password || $confirm_password || $current_password) {
    if (!$current_password) {
      $current_password_err = "Current password is required to change password.";
    }

    if ($password !== $confirm_password) {
      $confirm_password_err = "Passwords do not match.";
    } elseif (strlen($password) > 0 && strlen($password) < 6) {
      $password_err = "New password must be at least 6 characters.";
    }

    if (!$current_password_err && !$confirm_password_err && !$password_err) {
      $stmt = $conn->prepare("SELECT password FROM transport_companies WHERE company_id = ?");
      $stmt->execute([$company_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$row || !password_verify($current_password, $row['password'])) {
        $current_password_err = "Current password is incorrect.";
      }
    }
  }

  if (!$company_name_err && !$current_password_err && !$confirm_password_err && !$password_err) {
    $sql = "UPDATE transport_companies SET company_name = ?, phone = ?, address = ?, website = ?";
    $params = [$company_name, $phone ?: null, $address ?: null, $website ?: null];

    if ($new_logo_path) {
      $sql .= ", logo = ?";
      $params[] = $new_logo_path;
    }

    if ($password) {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $sql .= ", password = ?";
      $params[] = $hashed_password;
    }

    $sql .= " WHERE company_id = ?";
    $params[] = $company_id;

    $stmt = $conn->prepare($sql);
    if ($stmt->execute($params)) {
      $update_success = "Profile updated successfully.";
      $_SESSION['company_name'] = $company_name;
    } else {
      $update_err = "Update failed. Please try again.";
    }
  }
} else {
  $stmt = $conn->prepare("SELECT company_name, email, phone, address, website, logo FROM transport_companies WHERE company_id = ?");
  $stmt->execute([$company_id]);
  $company = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($company) {
    $company_name = $company['company_name'];
    $email = $company['email'];
    $phone = $company['phone'];
    $address = $company['address'];
    $website = $company['website'];
    $logo = $company['logo'];
  } else {
    session_destroy();
    header('Location: login.php');
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Company Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #fff;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px 20px;
    }

    .profile-card {
      background-color: #1b2735;
      border-radius: 18px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
      padding: 40px 30px;
      max-width: 600px;
      width: 100%;
      animation: fadeIn 0.6s ease-in-out;
    }

    h3 {
      color: #f1c40f;
      margin-bottom: 1.5rem;
      text-align: center;
      font-weight: 600;
    }

    label {
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

    input.form-control::placeholder,
    textarea.form-control::placeholder {
      color: #aaa;
    }

    input.form-control:focus,
    textarea.form-control:focus {
      background-color: rgba(255, 255, 255, 0.1);
      border-color: #f1c40f;
      box-shadow: 0 0 0 0.25rem rgba(241, 196, 15, 0.25);
      color: #fff;
    }

    .btn-update {
      background-color: #f1c40f;
      border: none;
      color: #1e1e2f;
      font-weight: 600;
      border-radius: 30px;
    }

    .btn-update:hover {
      background-color: #d4ac0d;
      color: #fff;
    }

    .invalid-feedback {
      color: #ff6b6b;
    }

    .alert-success {
      background-color: #27ae60;
      color: #fff;
      border: none;
      margin-bottom: 1rem;
    }

    .alert-danger {
      background-color: #c0392b;
      color: #fff;
      border: none;
      margin-bottom: 1rem;
    }

    .logo-preview {
      max-width: 120px;
      height: auto;
      display: block;
      margin-bottom: 1rem;
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
  <div class="profile-card">
    <h3><i class="bi bi-person-circle me-2"></i>Company Profile</h3>

    <?php if ($update_success): ?>
      <div class="alert alert-success"><?= $update_success ?></div>
    <?php endif; ?>

    <?php if ($update_err): ?>
      <div class="alert alert-danger"><?= $update_err ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
      <div class="mb-3">
        <label>Company Name</label>
        <input type="text" name="company_name" class="form-control <?= $company_name_err ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($company_name) ?>" />
        <div class="invalid-feedback"><?= $company_name_err ?></div>
      </div>

      <div class="mb-3">
        <label>Email (cannot change)</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" disabled />
      </div>

      <div class="mb-3">
        <label>Phone</label>
        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" />
      </div>

      <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($address) ?></textarea>
      </div>

      <div class="mb-3">
        <label>Website</label>
        <input type="url" name="website" class="form-control" value="<?= htmlspecialchars($website) ?>" />
      </div>

      <div class="mb-3">
        <label>Company Logo</label>
        <?php if ($logo): ?>
          <img src="../<?= htmlspecialchars($logo) ?>" class="logo-preview" alt="Current Logo" />
        <?php endif; ?>
        <input type="file" name="logo" class="form-control" accept="image/*" />
      </div>

      <hr class="my-4" />
      <h5 class="mb-3 text-warning">Change Password (optional)</h5>

      <div class="mb-3">
        <label>Current Password</label>
        <input type="password" name="current_password" class="form-control <?= $current_password_err ? 'is-invalid' : '' ?>" />
        <div class="invalid-feedback"><?= $current_password_err ?></div>
      </div>

      <div class="mb-3">
        <label>New Password</label>
        <input type="password" name="password" class="form-control <?= $password_err ? 'is-invalid' : '' ?>" />
        <div class="invalid-feedback"><?= $password_err ?></div>
      </div>

      <div class="mb-3">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control <?= $confirm_password_err ? 'is-invalid' : '' ?>" />
        <div class="invalid-feedback"><?= $confirm_password_err ?></div>
      </div>

      <div class="d-flex gap-3">
        <a href="transport_dashboard.php" class="btn btn-update flex-grow-1">← Back</a>
        <button type="submit" class="btn btn-update flex-grow-1">Update</button>
      </div>
    </form>
  </div>
</body>

</html>