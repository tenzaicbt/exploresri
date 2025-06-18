<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['company_id'])) {
  header("Location: company_login.php");
  exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $company_id = $_SESSION['company_id'];
  $model = $_POST['model'];
  $type = $_POST['type'];
  $description = $_POST['description'];
  $rental_price = $_POST['rental_price'];
  $capacity = $_POST['capacity'];
  $features = $_POST['features'];
  $registration_number = $_POST['registration_number'];
  $fuel_type = $_POST['fuel_type'];

  $target_dir = '../uploads/vehicles/';
  if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
  }

  // Upload main image
  if (!empty($_FILES['image']['name'])) {
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $main_image = time() . '_' . basename($image_name);
    $main_image_path = $target_dir . $main_image;

    if (move_uploaded_file($image_tmp, $main_image_path)) {
      // Upload gallery images
      $gallery_paths = [];
      if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
          if (!empty($_FILES['images']['name'][$index])) {
            $gallery_name = time() . '_' . basename($_FILES['images']['name'][$index]);
            $gallery_path = $target_dir . $gallery_name;
            if (move_uploaded_file($tmpName, $gallery_path)) {
              $gallery_paths[] = $gallery_name;
            }
          }
        }
      }
      $gallery_string = implode(',', $gallery_paths);

      $stmt = $conn->prepare("INSERT INTO vehicles 
                (company_id, model, type, description, rental_price, capacity, features, registration_number, fuel_type, image, image_gallery, availability) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
      $stmt->execute([
        $company_id,
        $model,
        $type,
        $description,
        $rental_price,
        $capacity,
        $features,
        $registration_number,
        $fuel_type,
        $main_image,
        $gallery_string
      ]);

      $message = "Vehicle added successfully!";
    } else {
      $message = "Failed to upload main image.";
    }
  } else {
    $message = "Please upload a main image.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Add Vehicle</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap" rel="stylesheet" />
  <style>
    .truncate {
      max-width: 200px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .modal img {
      max-height: 100px;
      margin: 5px;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h2>Add New Vehicle</h2>
    <?php if ($message): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" novalidate>
      <div class="mb-3">
        <label class="form-label" for="model">Model</label>
        <input type="text" id="model" name="model" required class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="type">Type</label>
        <input type="text" id="type" name="type" placeholder="e.g., Car, Van, SUV" required class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="description">Description</label>
        <textarea id="description" name="description" rows="3" required class="form-control"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label" for="rental_price">Rental Price Per Day</label>
        <input type="number" id="rental_price" name="rental_price" step="0.01" required class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="capacity">Capacity (seats)</label>
        <input type="number" id="capacity" name="capacity" required class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="features">Features</label>
        <input type="text" id="features" name="features" placeholder="e.g., AC, GPS, Bluetooth" class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="registration_number">Registration Number</label>
        <input type="text" id="registration_number" name="registration_number" class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="fuel_type">Fuel Type</label>
        <input type="text" id="fuel_type" name="fuel_type" placeholder="e.g., Petrol, Diesel, Electric" class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="image">Main Image</label>
        <input type="file" id="image" name="image" accept="image/*" required class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="images">Gallery Images (optional)</label>
        <input type="file" id="images" name="images[]" accept="image/*" multiple class="form-control" />
        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn btn-success">Add Vehicle</button>
          <a href="transport_dashboard.php" class="btn btn-secondary">Back</a>
        </div>

      </div>
</body>

</html>