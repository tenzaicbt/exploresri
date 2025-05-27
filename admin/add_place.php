<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $province = trim($_POST['province']);
    $top_attractions = trim($_POST['top_attractions']);
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $imageName = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $uploadPath = '../images/' . $imageName;

        if (move_uploaded_file($imageTmp, $uploadPath)) {
            $stmt = $conn->prepare("INSERT INTO destinations (name, description, image, province, top_attractions, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $imageName, $province, $top_attractions, $latitude, $longitude]);
            $message = "Destination added successfully!";
        } else {
            $message = "Image upload failed.";
        }
    } else {
        $message = "Please select an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Destination</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #map {
      height: 300px;
      width: 100%;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h2>Add New Destination</h2>
  <?php if ($message): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="name" class="form-label">Place Name</label>
      <input type="text" name="name" id="name" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" id="description" rows="4" required class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label for="province" class="form-label">Province</label>
      <input type="text" name="province" id="province" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="top_attractions" class="form-label">Top Attractions</label>
      <textarea name="top_attractions" id="top_attractions" rows="3" required class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Select Location on Map</label>
      <div id="map"></div>
      <input type="hidden" name="latitude" id="latitude">
      <input type="hidden" name="longitude" id="longitude">
    </div>
    <div class="mb-3">
      <label for="image" class="form-label">Image</label>
      <input type="file" name="image" id="image" accept="image/*" required class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Add Destination</button>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
  </form>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrzAEKa33t2DVHLXBkAUcdZAaFIN5tGG8"></script>
<script>
  function initMap() {
    const defaultLocation = { lat: 7.8731, lng: 80.7718 };
    const map = new google.maps.Map(document.getElementById('map'), {
      center: defaultLocation,
      zoom: 7
    });

    const marker = new google.maps.Marker({
      position: defaultLocation,
      map: map,
      draggable: true
    });

    document.getElementById('latitude').value = marker.getPosition().lat();
    document.getElementById('longitude').value = marker.getPosition().lng();

    google.maps.event.addListener(marker, 'dragend', function () {
      document.getElementById('latitude').value = marker.getPosition().lat();
      document.getElementById('longitude').value = marker.getPosition().lng();
    });
  }

  window.onload = initMap;
</script>
</body>
</html>
