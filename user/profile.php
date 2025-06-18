<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";
$contact_err = "";
$upload_err = "";

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$name = $user['name'];
$email = $user['email'];
$country = $user['country'] ?? '';
$profile_pic = $user['profile_pic'];

// Default country code
$country_code = '+94';
$contact_number = '';

// Extract country code and local number from stored contact_number if possible
if (!empty($user['contact_number'])) {
    if (preg_match('/^(\+\d{1,3})(\d{7,15})$/', $user['contact_number'], $matches)) {
        $country_code = $matches[1];
        $contact_number = $matches[2];
    } else {
        $contact_number = $user['contact_number']; // fallback
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $contact_number = trim($_POST["contact_number"]);
    $country_code = trim($_POST["country_code"]);
    $country = trim($_POST["country"]);

    // Validate contact number digits only (without country code)
    if (!preg_match("/^[0-9]{7,15}$/", $contact_number)) {
        $contact_err = "Enter a valid contact number (7-15 digits)";
    }

    if (empty($contact_err)) {
        // Combine country code and contact number for saving
        $full_contact_number = $country_code . $contact_number;

        if (!empty($_FILES["profile_picture"]["name"])) {
            $target_dir = __DIR__ . "/uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            $image_name = basename($_FILES["profile_picture"]["name"]);
            $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($image_ext, $allowed_ext)) {
                $upload_err = "Only JPG, PNG, GIF files are allowed.";
            } else if ($_FILES["profile_picture"]["size"] > 2 * 1024 * 1024) {
                $upload_err = "File size must be less than 2MB.";
            } else {
                $new_file_name = time() . "_" . uniqid() . "." . $image_ext;
                $target_file = $target_dir . $new_file_name;

                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    $db_file_path = "uploads/" . $new_file_name;

                    $stmt = $conn->prepare("UPDATE users SET name=?, contact_number=?, country=?, profile_pic=? WHERE user_id=?");
                    $stmt->execute([$name, $full_contact_number, $country, $db_file_path, $user_id]);
                    $profile_pic = $db_file_path;
                    $message = "Profile updated successfully!";
                } else {
                    $upload_err = "Failed to upload the profile picture.";
                }
            }
        }

        if (empty($upload_err) && empty($_FILES["profile_picture"]["name"])) {
            $stmt = $conn->prepare("UPDATE users SET name=?, contact_number=?, country=? WHERE user_id=?");
            $stmt->execute([$name, $full_contact_number, $country, $user_id]);
            $message = "Profile updated successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>My Profile - ExploreSri</title>
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

        .form-label {
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
            display: inline-block;
            text-align: center;
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

        .alert-danger {
            background-color: #c0392b;
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
    </style>
</head>

<body>
    <div class="profile-card">
        <h3><i class="bi bi-person-circle"></i>My Profile</h3>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($upload_err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($upload_err) ?></div>
        <?php endif; ?>

        <img src="<?= !empty($profile_pic) ? htmlspecialchars($profile_pic) : 'https://via.placeholder.com/150x150.png?text=Profile' ?>" alt="Profile Picture" class="profile-pic" />

        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="mb-3 text-start">
                <label for="name" class="form-label">Name</label>
                <input id="name" type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required />
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Email (Read-only)</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly />
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <div class="d-flex gap-2">
                    <select name="country_code" id="country_code" class="form-select w-auto" required>
                        <option value="+94" <?= ($country_code == '+94') ? 'selected' : ''; ?>>+94 (Sri Lanka)</option>
                        <option value="+91" <?= ($country_code == '+91') ? 'selected' : ''; ?>>+91 (India)</option>
                        <option value="+1" <?= ($country_code == '+1') ? 'selected' : ''; ?>>+1 (USA)</option>
                        <option value="+44" <?= ($country_code == '+44') ? 'selected' : ''; ?>>+44 (UK)</option>
                    </select>
                    <input type="tel" id="contactNumber" name="contact_number" class="form-control <?= $contact_err ? 'is-invalid' : ''; ?>" placeholder="712345678" value="<?= htmlspecialchars($contact_number); ?>" required>
                </div>
                <?php if ($contact_err): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($contact_err); ?></div><?php endif; ?>
            </div>

            <div class="mb-3 text-start">
                <label for="country" class="form-label">Country</label>
                <select id="country" name="country" class="form-select" required>
                    <option value="">Select your country</option>
                    <option value="Sri Lanka" <?= $country == "Sri Lanka" ? "selected" : "" ?>>Sri Lanka</option>
                    <option value="India" <?= $country == "India" ? "selected" : "" ?>>India</option>
                    <option value="USA" <?= $country == "USA" ? "selected" : "" ?>>USA</option>
                    <option value="UK" <?= $country == "UK" ? "selected" : "" ?>>UK</option>
                </select>
            </div>

            <div class="mb-4 text-start">
                <label class="form-label">Change Profile Picture</label>
                <input type="file" name="profile_picture" class="form-control" accept=".jpg,.jpeg,.png,.gif" />
            </div>

            <div class="d-flex gap-3">
                <a href="../destinations.php" class="btn btn-update flex-grow-1">← Back</a>
                <button type="submit" class="btn btn-update flex-grow-1">Update</button>
            </div>
        </form>
    </div>
</body>

</html>


<script>
    // Allow only digits in contact number input
    const phoneInput = document.getElementById("contactNumber");
    phoneInput.addEventListener("input", function() {
        this.value = this.value.replace(/\D/g, '');
    });
</script>
</body>

</html>