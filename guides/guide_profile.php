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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css"/>
  <style>
    body {
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
      padding: 50px 0;
    }
    .profile-card {
      background-color: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      padding: 30px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    }
    .form-control, .form-select {
      background-color: rgba(255,255,255,0.1);
      border: none;
      color: #fff;
    }
    .form-control::placeholder {
      color: #bbb;
    }
    .form-control:focus {
      background-color: rgba(255,255,255,0.15);
      box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    .profile-pic {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #28a745;
      margin-bottom: 15px;
    }
    .btn-success {
      background-color: #28a745;
      border: none;
    }
    .btn-success:hover {
      background-color: #218838;
    }
    label {
      color: #ccc;
    }

    .form-select option {
      background-color: #2c5364;
      color: #fff;
    }

    .iti__country-list {
      background-color: #2c5364;
      color: #fff;
    }

    .iti__country-list .iti__country {
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
      <div class="d-flex justify-content-between">
        <a href="guide_dashboard.php" class="btn btn-success w-50 me-2">← Dashboard</a>
        <button type="submit" class="btn btn-success w-50">Update</button>
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

    phoneInput.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, '');
    });
  </script>
</body>
</html>
