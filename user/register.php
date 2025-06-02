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
    <meta charset="UTF-8">
    <title>Register - ExploreSri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css">
    <style>
        body {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            background-color: rgba(255,255,255,0.05);
            border: none;
            border-radius: 15px;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s ease-in-out;
        }
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
        }
        .form-control::placeholder { color: #ccc; }
        .form-control:focus {
            background-color: rgba(255,255,255,0.15);
            border-color: #28a745;
            color: #fff;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
            font-weight: bold;
        }
        .btn-success:hover { background-color: #218838; }
        .form-label { color: #ddd; }
        .invalid-feedback { color: #ff6b6b; }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(30px);}
            to {opacity: 1; transform: translateY(0);}
        }
        /* Fix for country dropdown */
.form-select {
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
  border: none;
}

.form-select option {
  background-color: #2c5364;
  color: #fff;
}

/* Fix for intl-tel-input input field */
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

/* Ensure phone number input matches theme */
#contactNumber {
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
}

    </style>
</head>
<body>

<div class="card">
    <h3 class="text-center mb-4"><i class=""></i> CREATE YOUR ACCOUNT</h3>

    <?php if ($register_err): ?>
        <div class="alert alert-danger"><?php echo $register_err; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control <?= $name_err ? 'is-invalid' : ''; ?>" placeholder="John Doe" value="<?= htmlspecialchars($name) ?>">
            <div class="invalid-feedback"><?= $name_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control <?= $email_err ? 'is-invalid' : ''; ?>" placeholder="example@mail.com" value="<?= htmlspecialchars($email) ?>">
            <div class="invalid-feedback"><?= $email_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control <?= $password_err ? 'is-invalid' : ''; ?>" placeholder="Enter password">
            <div class="invalid-feedback"><?= $password_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Re-enter Password</label>
            <input type="password" name="re_password" class="form-control <?= $re_password_err ? 'is-invalid' : ''; ?>" placeholder="Confirm password">
            <div class="invalid-feedback"><?= $re_password_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Country</label>
            <select id="country" name="country" class="form-select" required>
                <option value="">Select your country</option>
                <option value="Sri Lanka" <?= $country == "Sri Lanka" ? "selected" : "" ?>>Sri Lanka</option>
                <option value="India" <?= $country == "India" ? "selected" : "" ?>>India</option>
                <option value="USA" <?= $country == "USA" ? "selected" : "" ?>>USA</option>
                <option value="UK" <?= $country == "UK" ? "selected" : "" ?>>UK</option>
                <!-- Add more as needed -->
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="tel" id="contactNumber" name="contact_number" class="form-control <?= $contact_err ? 'is-invalid' : ''; ?>" placeholder="712345678" value="<?= htmlspecialchars($contact_number) ?>" required>
            <div class="invalid-feedback"><?= $contact_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" name="profile_pic" class="form-control">
        </div>

        <button type="submit" class="btn btn-success w-100">Register</button>
        <div class="mt-3 text-center">
            Already have an account? <a href="login.php" class="text-info">Login</a>
        </div>
    </form>
</div>

<!-- Scripts -->
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

    // Optional: restrict to digits only
    phoneInput.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, '');
    });
</script>

</body>
</html>
