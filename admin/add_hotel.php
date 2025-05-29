<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $price_per_night = $_POST['price_per_night'];
    $contact_info = $_POST['contact_info'];
    $rating = $_POST['rating'];
    $address = $_POST['address'];
    $destination_id = $_POST['destination_id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $target_path = '../images/' . basename($image_name);

    if (move_uploaded_file($image_tmp, $target_path)) {
    $stmt = $conn->prepare("INSERT INTO hotels (name, location, description, price_per_night, contact_info, rating, address, destination_id, latitude, longitude, image)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $location, $description, $price_per_night, $contact_info, $rating, $address, $destination_id, $latitude, $longitude, $image_name]);
    $message = "Hotel added successfully!";
    } else {
        $message = "Failed to upload image.";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Hotel</title>
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
  <h2>Add New Hotel</h2>
  <?php if ($message): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="name" class="form-label">Hotel Name</label>
      <input type="text" name="name" id="name" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="location" class="form-label">Location</label>
      <input type="text" name="location" id="location" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" id="description" rows="3" required class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label for="price_per_night" class="form-label">Price Per Night</label>
      <input type="number" name="price_per_night" id="price_per_night" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="contact_info" class="form-label">Contact Info</label>
      <input type="text" name="contact_info" id="contact_info" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="rating" class="form-label">Rating</label>
      <input type="number" step="0.1" max="5" name="rating" id="rating" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="address" class="form-label">Address</label>
      <input type="text" name="address" id="address" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="destination_id" class="form-label">Destination ID</label>
      <input type="number" name="destination_id" id="destination_id" required class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Select Hotel Location on Map</label>
      <div id="map"></div>
      <input type="hidden" name="latitude" id="latitude">
      <input type="hidden" name="longitude" id="longitude">
    </div>
    <div class="mb-3">
      <label for="image" class="form-label">Hotel Image</label>
      <input type="file" name="image" id="image" accept="image/*" required class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Add Hotel</button>
    <a href="manage_hotels.php" class="btn btn-secondary">Back to Dashboard</a>
  </form>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=GOOGLEMAP_API_KEY"></script>
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
