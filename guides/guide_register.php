<?php
include '../config/db.php';
session_start();

$name = $email = $password = $re_password = $languages = $experience_years = $bio = $contact_info = $price_per_day = "";
$name_err = $email_err = $password_err = $re_password_err = $languages_err = $experience_err = $bio_err = $contact_err = $price_err = $register_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST["name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $password = $_POST["password"] ?? "";
  $re_password = $_POST["re_password"] ?? "";
  $languages = trim($_POST["languages"] ?? "");
  $experience_years = trim($_POST["experience_years"] ?? "");
  $bio = trim($_POST["bio"] ?? "");
  $contact_info = trim($_POST["contact_info"] ?? "");
  $price_per_day = trim($_POST["price_per_day"] ?? "");

  // Validations
  if (empty($name)) $name_err = "Name is required.";
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $email_err = "Invalid email.";
  if (empty($password)) $password_err = "Password is required.";
  if ($password !== $re_password) $re_password_err = "Passwords do not match.";
  if (empty($languages)) $languages_err = "Languages field is required.";
  if (!is_numeric($experience_years) || $experience_years < 0) $experience_err = "Experience must be a positive number.";
  if (empty($bio)) $bio_err = "Bio is required.";
  if (empty($contact_info)) $contact_err = "Contact info is required.";
  if (!is_numeric($price_per_day) || $price_per_day < 0) $price_err = "Price per day must be a positive number.";

  if (empty($name_err) && empty($email_err) && empty($password_err) && empty($re_password_err) && empty($languages_err) && empty($experience_err) && empty($bio_err) && empty($contact_err) && empty($price_err)) {
    $stmt = $conn->prepare("SELECT * FROM guide WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
      $email_err = "Email is already registered.";
    } else {
      $photo = null;
      if (!empty($_FILES["photo"]["name"])) {
        $target_dir = "../uploads/guides/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
          $photo = $filename;
        } else {
          $register_err = "Failed to upload photo.";
        }
      }

      if (!$register_err) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO guide (name, email, password, languages, experience_years, bio, photo, contact_info, price_per_day, rating, is_verified, created_at, status)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW(), 'active')");
        $result = $stmt->execute([$name, $email, $hashed_password, $languages, $experience_years, $bio, $photo, $contact_info, $price_per_day]);

        if ($result) {
          header("Location: guide_login.php?success=1");
          exit;
        } else {
          $register_err = "Something went wrong. Please try again.";
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Guide Registration - ExploreSri</title>
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
      max-width: 640px;
      animation: fadeIn 0.6s ease-in-out;
    }

    .register-card h3 {
      text-align: center;
      color: #f1c40f;
      margin-bottom: 1.5rem;
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

    textarea.form-control {
      resize: vertical;
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
    <h3><i class="bi bi-person-plus-fill me-2"></i>Register as a Guide</h3>

    <?php if ($register_err): ?>
      <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-triangle me-2"></i><?= $register_err; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control <?= $name_err ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($name) ?>" placeholder="John Doe" />
        <div class="invalid-feedback"><?= $name_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control <?= $email_err ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($email) ?>" placeholder="example@mail.com" />
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
        <label class="form-label">Languages (comma separated)</label>
        <input type="text" name="languages" class="form-control <?= $languages_err ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($languages) ?>" placeholder="English, Sinhala, Tamil" />
        <div class="invalid-feedback"><?= $languages_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Years of Experience</label>
        <input type="number" name="experience_years" class="form-control <?= $experience_err ? 'is-invalid' : ''; ?>" min="0" value="<?= htmlspecialchars($experience_years) ?>" placeholder="3" />
        <div class="invalid-feedback"><?= $experience_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Bio</label>
        <textarea name="bio" class="form-control <?= $bio_err ? 'is-invalid' : ''; ?>" rows="4" placeholder="Write about yourself..."><?= htmlspecialchars($bio) ?></textarea>
        <div class="invalid-feedback"><?= $bio_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Contact Info</label>
        <input type="text" name="contact_info" class="form-control <?= $contact_err ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($contact_info) ?>" placeholder="Phone, email or other contact" />
        <div class="invalid-feedback"><?= $contact_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Price per Day (USD)</label>
        <input type="number" name="price_per_day" step="0.01" class="form-control <?= $price_err ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($price_per_day) ?>" placeholder="50.00" />
        <div class="invalid-feedback"><?= $price_err; ?></div>
      </div>

      <div class="mb-3">
        <label class="form-label">Profile Photo</label>
        <input type="file" name="photo" class="form-control" accept="image/*" />
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-register">Register</button>
      </div>

      <div class="text-center mt-3">
        Already registered? <a href="guide_login.php">Login here</a>
      </div>
    </form>
  </div>
</body>

</html>