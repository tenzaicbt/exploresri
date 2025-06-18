<?php
session_start();
include '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  die("Invalid ID");
}

$stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
$stmt->execute([$id]);
$place = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $location = trim($_POST['location']);
  $category = trim($_POST['category']);
  $province = trim($_POST['province']);
  $top_attractions = trim($_POST['top_attractions']);
  $description = trim($_POST['description']);
  $latitude = $_POST['latitude'];
  $longitude = $_POST['longitude'];
  $imageName = $place['image'];

  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageTmp = $_FILES['image']['tmp_name'];
    $imageName = basename($_FILES['image']['name']);
    $uploadPath = '../images/' . $imageName;
    move_uploaded_file($imageTmp, $uploadPath);
  }

  $stmt = $conn->prepare("UPDATE destinations SET name=?, location=?, category=?, province=?, top_attractions=?, description=?, latitude=?, longitude=?, image=? WHERE destination_id=?");
  $stmt->execute([$name, $location, $category, $province, $top_attractions, $description, $latitude, $longitude, $imageName, $id]);

  header("Location: manage_places.php?updated=1");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit Place</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #map {
      height: 300px;
      width: 100%;
    }

    .thumb {
      max-width: 200px;
      height: auto;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h2>Edit Place</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3"><label>Name</label><input class="form-control" name="name" value="<?= htmlspecialchars($place['name']) ?>" required></div>
      <div class="mb-3"><label>Location</label><input class="form-control" name="location" value="<?= htmlspecialchars($place['location']) ?>" required></div>
      <div class="mb-3"><label>Category</label><input class="form-control" name="category" value="<?= htmlspecialchars($place['category']) ?>" required></div>
      <div class="mb-3"><label>Province</label><input class="form-control" name="province" value="<?= htmlspecialchars($place['province']) ?>" required></div>
      <div class="mb-3"><label>Top Attractions</label><textarea class="form-control" name="top_attractions"><?= htmlspecialchars($place['top_attractions']) ?></textarea></div>
      <div class="mb-3"><label>Description</label><textarea class="form-control" name="description"><?= htmlspecialchars($place['description']) ?></textarea></div>
      <div class="mb-3">
        <label>Select Location on Map</label>
        <div id="map"></div>
        <input type="hidden" name="latitude" id="latitude" value="<?= htmlspecialchars($place['latitude']) ?>">
        <input type="hidden" name="longitude" id="longitude" value="<?= htmlspecialchars($place['longitude']) ?>">
      </div>
      <div class="mb-3">
        <label>Current Image</label><br>
        <img src="../images/<?= htmlspecialchars($place['image']) ?>" class="thumb"><br>
        <label>Change Image (optional)</label>
        <input type="file" name="image" accept="image/*" class="form-control">
      </div>
      <button class="btn btn-success">Update</button>
      <a href="manage_places.php" class="btn btn-secondary">Back</a>
    </form>
  </div>

  <script src="https://maps.googleapis.com/maps/api/js?key=GOOGLEAPK"></script>
  <script>
    function initMap() {
      const lat = parseFloat(document.getElementById('latitude').value) || 7.8731;
      const lng = parseFloat(document.getElementById('longitude').value) || 80.7718;
      const currentLocation = {
        lat: lat,
        lng: lng
      };

      const map = new google.maps.Map(document.getElementById('map'), {
        center: currentLocation,
        zoom: 7
      });

      const marker = new google.maps.Marker({
        position: currentLocation,
        map: map,
        draggable: true
      });

      google.maps.event.addListener(marker, 'dragend', function() {
        document.getElementById('latitude').value = marker.getPosition().lat();
        document.getElementById('longitude').value = marker.getPosition().lng();
      });
    }
    window.onload = initMap;
  </script>
</body>

</html>