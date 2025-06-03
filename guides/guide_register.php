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
    // ...

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

    // Proceed if no errors
    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($re_password_err) && empty($languages_err) && empty($experience_err) && empty($bio_err) && empty($contact_err) && empty($price_err)) {
        // Check if email already registered
        $stmt = $conn->prepare("SELECT * FROM guide WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $email_err = "Email is already registered.";
        } else {
            // Handle photo upload
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
                // Default rating is 0, is_verified = 0, status = 'active'
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
    <title>Guide Registration - ExploreSri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
            max-width: 600px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s ease-in-out;
        }
        .form-control, .form-select, textarea {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
        }
        .form-control::placeholder { color: #ccc; }
        .form-control:focus, textarea:focus {
            background-color: rgba(255,255,255,0.15);
            border-color: #28a745;
            color: #fff;
            box-shadow: none;
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
        textarea {
            resize: vertical;
        }
    </style>
</head>
<body>

<div class="card">
    <h3 class="text-center mb-4">Register as a Guide</h3>

    <?php if ($register_err): ?>
        <div class="alert alert-danger"><?php echo $register_err; ?></div>
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
            <label class="form-label">Languages (comma separated)</label>
            <input type="text" name="languages" class="form-control <?= $languages_err ? 'is-invalid' : ''; ?>" placeholder="English, Sinhala, Tamil" value="<?= htmlspecialchars($languages) ?>" />
            <div class="invalid-feedback"><?= $languages_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Years of Experience</label>
            <input type="number" name="experience_years" class="form-control <?= $experience_err ? 'is-invalid' : ''; ?>" min="0" placeholder="3" value="<?= htmlspecialchars($experience_years) ?>" />
            <div class="invalid-feedback"><?= $experience_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-control <?= $bio_err ? 'is-invalid' : ''; ?>" rows="4" placeholder="Write about yourself..."><?= htmlspecialchars($bio) ?></textarea>
            <div class="invalid-feedback"><?= $bio_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Contact Info</label>
            <input type="text" name="contact_info" class="form-control <?= $contact_err ? 'is-invalid' : ''; ?>" placeholder="Phone, email or other contact" value="<?= htmlspecialchars($contact_info) ?>" />
            <div class="invalid-feedback"><?= $contact_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Price per Day (USD)</label>
            <input type="number" step="0.01" name="price_per_day" class="form-control <?= $price_err ? 'is-invalid' : ''; ?>" min="0" placeholder="50.00" value="<?= htmlspecialchars($price_per_day) ?>" />
            <div class="invalid-feedback"><?= $price_err; ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Profile Photo</label>
            <input type="file" name="photo" class="form-control" accept="image/*" />
        </div>

        <button type="submit" class="btn btn-success w-100">Register</button>
        <div class="mt-3 text-center">
            Already registered? <a href="guide_login.php" class="text-info">Login here</a>
        </div>
    </form>
</div>

</body>
</html>
