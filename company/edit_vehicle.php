<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['company_id'])) {
    header("Location: company_login.php");
    exit;
}

$company_id = $_SESSION['company_id'];
$vehicle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($vehicle_id <= 0) {
    die("Invalid vehicle ID.");
}

// Fetch current vehicle data
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE vehicle_id = ? AND company_id = ?");
$stmt->execute([$vehicle_id, $company_id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    die("Vehicle not found or permission denied.");
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input data
    $model = trim($_POST['model'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $rental_price = floatval($_POST['rental_price'] ?? 0);
    $capacity = intval($_POST['capacity'] ?? 0);
    $features = trim($_POST['features'] ?? '');
    $registration_number = trim($_POST['registration_number'] ?? '');
    $fuel_type = trim($_POST['fuel_type'] ?? '');
    $availability = isset($_POST['availability']) ? 1 : 0;

    $target_dir = '../uploads/vehicles/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Use existing images if no new upload
    $main_image = $vehicle['image'];
    $gallery_images = $vehicle['image_gallery'] ? explode(',', $vehicle['image_gallery']) : [];

    // Handle main image upload (optional)
    if (!empty($_FILES['image']['name'])) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $image_path = $target_dir . $image_name;
        if (move_uploaded_file($image_tmp, $image_path)) {
            $main_image = $image_name;
        } else {
            $error = "Failed to upload main image.";
        }
    }

    // Handle gallery uploads (optional)
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
            if (!empty($_FILES['images']['name'][$index])) {
                $gallery_name = time() . '_' . basename($_FILES['images']['name'][$index]);
                $gallery_path = $target_dir . $gallery_name;
                if (move_uploaded_file($tmpName, $gallery_path)) {
                    $gallery_images[] = $gallery_name;
                }
            }
        }
    }
    $gallery_string = implode(',', $gallery_images);

    // Validate required fields
    if (!$error && ($model === '' || $type === '' || $rental_price <= 0 || $capacity <= 0)) {
        $error = "Please fill in all required fields correctly.";
    }

    if (!$error) {
        // Update database
        $updateStmt = $conn->prepare("UPDATE vehicles SET 
            model = ?, type = ?, description = ?, rental_price = ?, capacity = ?, features = ?, 
            registration_number = ?, fuel_type = ?, image = ?, image_gallery = ?, availability = ? 
            WHERE vehicle_id = ? AND company_id = ?");
        $updateStmt->execute([
            $model,
            $type,
            $description,
            $rental_price,
            $capacity,
            $features,
            $registration_number,
            $fuel_type,
            $main_image,
            $gallery_string,
            $availability,
            $vehicle_id,
            $company_id
        ]);

        $message = "Vehicle updated successfully!";
        // Refresh vehicle info from DB after update
        $stmt->execute([$vehicle_id, $company_id]);
        $vehicle = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Edit Vehicle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <h2>Edit Vehicle</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label for="model" class="form-label">Model *</label>
                <input type="text" name="model" id="model" class="form-control" required value="<?= htmlspecialchars($vehicle['model']) ?>" />
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type *</label>
                <input type="text" name="type" id="type" class="form-control" required value="<?= htmlspecialchars($vehicle['type']) ?>" />
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3" class="form-control"><?= htmlspecialchars($vehicle['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="rental_price" class="form-label">Rental Price Per Day *</label>
                <input type="number" step="0.01" min="0" name="rental_price" id="rental_price" class="form-control" required value="<?= htmlspecialchars($vehicle['rental_price']) ?>" />
            </div>

            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity (seats) *</label>
                <input type="number" min="1" name="capacity" id="capacity" class="form-control" required value="<?= htmlspecialchars($vehicle['capacity']) ?>" />
            </div>

            <div class="mb-3">
                <label for="features" class="form-label">Features</label>
                <input type="text" name="features" id="features" class="form-control" value="<?= htmlspecialchars($vehicle['features']) ?>" />
            </div>

            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" name="registration_number" id="registration_number" class="form-control" value="<?= htmlspecialchars($vehicle['registration_number']) ?>" />
            </div>

            <div class="mb-3">
                <label for="fuel_type" class="form-label">Fuel Type</label>
                <input type="text" name="fuel_type" id="fuel_type" class="form-control" value="<?= htmlspecialchars($vehicle['fuel_type']) ?>" />
            </div>

            <!-- Main Image Upload -->
            <div class="mb-4">
                <label for="image" class="form-label fw-semibold">Main Image (leave empty to keep current)</label>
                <input type="file" name="image" id="image" accept="image/*" class="form-control" />

                <?php if (!empty($vehicle['image'])): ?>
                    <div class="mt-3">
                        <img src="../uploads/vehicles/<?= htmlspecialchars($vehicle['image']) ?>" alt="Current Main Image"
                            class="img-thumbnail shadow-sm border" style="max-width: 160px;">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Gallery Image Upload -->
            <div class="mb-4">
                <label for="images" class="form-label fw-semibold">Gallery Images (add more if needed)</label>
                <input type="file" name="images[]" id="images" accept="image/*" multiple class="form-control" />
            </div>

            <!-- Display Existing Gallery Images with Delete Option -->
            <?php if (!empty($vehicle['image_gallery'])): ?>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Current Gallery</label>
                    <div class="d-flex flex-wrap gap-3">
                        <?php
                        $gallery_images = explode(',', $vehicle['image_gallery']);
                        foreach ($gallery_images as $img):
                            if (!empty($img)):
                        ?>
                                <div class="position-relative border rounded shadow-sm bg-white" style="width: 140px;">
                                    <img src="../uploads/vehicles/<?= htmlspecialchars($img) ?>"
                                        class="img-fluid rounded" style="height: 100px; object-fit: cover; width: 100%;">

                                    <!-- Delete Button -->
                                    <a href="delete_image.php?vehicle_id=<?= $vehicle_id ?>&image=<?= urlencode($img) ?>"
                                        onclick="return confirm('Delete this image?')"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center"
                                        style="width: 24px; height: 24px; font-size: 14px; line-height: 1;">&times;</a>
                                </div>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                </div>
            <?php endif; ?>


            <div class="form-check mb-3">
                <input type="checkbox" name="availability" id="availability" class="form-check-input" <?= $vehicle['availability'] ? 'checked' : '' ?> />
                <label for="availability" class="form-check-label">Available</label>
            </div>

            <div class="d-flex gap-3 mt-3">
                <button type="submit" class="btn btn-primary">Update Vehicle</button>
                <a href="manage_vehicles.php" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</body>

</html>