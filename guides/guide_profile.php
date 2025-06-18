<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['guide_id'])) {
  header("Location: guide_login.php");
  exit;
}

$guide_id = $_SESSION['guide_id'];
$message = "";
$contact_err = "";

// Fetch guide data
$stmt = $conn->prepare("SELECT * FROM guide WHERE guide_id = ?");
$stmt->execute([$guide_id]);
$guide = $stmt->fetch();

if (!$guide) {
  // If guide not found, log out or redirect
  session_destroy();
  header("Location: guide_login.php");
  exit;
}

$name = $guide['name'];
$email = $guide['email'];
$contact_number = $guide['contact_info'] ?? '';
$country = $guide['country'] ?? '';
$profile_pic = $guide['photo'] ?? '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST["name"]);
  $contact_number = trim($_POST["contact_number"]);
  $country = trim($_POST["country"]);

  // Validate contact number
  if (!preg_match("/^[0-9]{7,15}$/", $contact_number)) {
    $contact_err = "Enter a valid contact number (7–15 digits)";
  }

  if (empty($contact_err)) {
    // Handle profile picture upload
    if (!empty($_FILES["profile_picture"]["name"])) {
      $target_dir = "uploads/";
      if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
      $image_name = basename($_FILES["profile_picture"]["name"]);
      $target_file = $target_dir . time() . "_" . $image_name;

      if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("UPDATE guide SET name=?, contact_info=?, country=?, photo=? WHERE guide_id=?");
        $stmt->execute([$name, $contact_number, $country, $target_file, $guide_id]);
        $profile_pic = $target_file;
      }
    } else {
      $stmt = $conn->prepare("UPDATE guide SET name=?, contact_info=?, country=? WHERE guide_id=?");
      $stmt->execute([$name, $contact_number, $country, $guide_id]);
    }

    $message = "Profile updated successfully!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Guide Profile - ExploreSri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css" />
  <style>
    body {
      font-family: 'Rubik', sans-serif;
      background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
      color: #ffffff;
      min-height: 100vh;
      padding: 30px 15px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .profile-card {
      background-color: #1b2735;
      border-radius: 18px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
      padding: 40px 30px;
      width: 100%;
      max-width: 500px;
      animation: fadeIn 0.6s ease-in-out;
      text-align: center;
    }

    .profile-card h3 {
      color: #f1c40f;
      margin-bottom: 1.5rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .profile-pic {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #f1c40f;
      margin-bottom: 1.5rem;
      background: #222a3a;
      display: inline-block;
    }

    label {
      color: #ccc;
      font-weight: 500;
      text-align: left;
    }

    input.form-control,
    select.form-select {
      background: rgba(255, 255, 255, 0.05);
      border: none;
      color: #fff;
      border-radius: 10px;
      padding: 10px 15px;
    }

    input.form-control::placeholder {
      color: #aaa;
    }

    input.form-control:focus,
    select.form-select:focus {
      background-color: rgba(255, 255, 255, 0.1);
      border-color: #f1c40f;
      box-shadow: 0 0 0 0.25rem rgba(241, 196, 15, 0.25);
      color: #fff;
      outline: none;
    }

    .btn-update {
      background-color: #f1c40f;
      border: none;
      color: #1e1e2f;
      font-weight: 600;
      border-radius: 30px;
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      transition: background-color 0.3s ease;
    }

    .btn-update:hover {
      background-color: #d4ac0d;
      color: #fff;
    }

    .btn-back {
      background: transparent;
      border: 2px solid #f1c40f;
      color: #f1c40f;
      border-radius: 30px;
      width: 100%;
      padding: 12px;
      font-weight: 600;
      transition: background-color 0.3s ease, color 0.3s ease;
      text-decoration: none;
    }

    .btn-back:hover {
      background-color: #f1c40f;
      color: #1e1e2f;
      text-decoration: none;
    }

    .d-flex.gap-3 {
      margin-top: 20px;
      gap: 15px;
    }

    .invalid-feedback {
      color: #ff6b6b;
      text-align: left;
    }

    .alert-success {
      background-color: #27ae60;
      color: #fff;
      border: none;
      margin-bottom: 15px;
      border-radius: 10px;
      padding: 10px 15px;
      text-align: center;
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

    .form-select option,
    .iti__country-list,
    .iti__country-list .iti__country {
      background-color: #2c3e50;
      color: #fff;
    }

    .iti--allow-dropdown input,
    .iti--allow-dropdown input:focus {
      background-color: rgba(255, 255, 255, 0.1) !important;
      color: #fff !important;
      border: none;
      box-shadow: none;
    }

    #contactNumber {
      background-color: rgba(255, 255, 255, 0.1);
      color: #fff;
    }
  </style>

</head>

<body>

  <div class="profile-card">
    <h3 class="text-center mb-4">My Guide Profile</h3>

    <?php if ($message): ?>
      <div class="alert alert-success text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="text-center">
      <img src="<?php echo !empty($profile_pic) ? htmlspecialchars($profile_pic) : 'https://via.placeholder.com/150x150.png?text=Profile'; ?>" class="profile-pic" alt="Profile Picture">
    </div>

    <form method="POST" enctype="multipart/form-data" novalidate>
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email (Read-only)</label>
        <input type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly>
      </div>
      <div class="mb-3">
        <label class="form-label">Contact Number</label>
        <input type="tel" id="contactNumber" name="contact_number" class="form-control <?php echo $contact_err ? 'is-invalid' : ''; ?>" placeholder="712345678" value="<?php echo htmlspecialchars($contact_number); ?>" required>
        <?php if ($contact_err): ?><div class="invalid-feedback"><?php echo $contact_err; ?></div><?php endif; ?>
      </div>
      <div class="mb-3">
        <label class="form-label">Country</label>
        <select id="country" name="country" class="form-select" required>
          <option value="">Select your country</option>
          <option value="Sri Lanka" <?php echo $country == "Sri Lanka" ? "selected" : ""; ?>>Sri Lanka</option>
          <option value="India" <?php echo $country == "India" ? "selected" : ""; ?>>India</option>
          <option value="USA" <?php echo $country == "USA" ? "selected" : ""; ?>>USA</option>
          <option value="UK" <?php echo $country == "UK" ? "selected" : ""; ?>>UK</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Change Profile Picture</label>
        <input type="file" name="profile_picture" class="form-control">
      </div>
      <div class="d-flex gap-3">
        <a href="guide_dashboard.php" class="btn btn-update flex-grow-1">← Dashboard</a>
        <button type="submit" class="btn btn-update flex-grow-1">Update</button>
      </div>
    </form>
  </div>

  <!-- Phone input plugin -->
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
  <script>
    const phoneInput = document.querySelector("#contactNumber");
    const iti = window.intlTelInput(phoneInput, {
      initialCountry: "auto",
      geoIpLookup: callback => {
        fetch('https://ipapi.co/json')
          .then(res => res.json())
          .then(data => callback(data.country_code))
          .catch(() => callback('US'));
      },
      utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
    });

    phoneInput.addEventListener("input", function() {
      this.value = this.value.replace(/\D/g, '');
    });
  </script>
</body>

</html>