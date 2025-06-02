<?php
include '../config/db.php';

$hotel_id = $_GET['id'] ?? null;
if (!$hotel_id) {
    echo "Invalid hotel ID";
    exit;
}

$message = "";

// Fetch existing hotel data
$stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();

if (!$hotel) {
    echo "Hotel not found";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $price = $_POST['price_per_night'];
    $contact = $_POST['contact_info'];
    $rating = $_POST['rating'];
    $address = $_POST['address'];
    $destination_id = $_POST['destination_id'];
    $facilities = $_POST['facilities'];
    $popular_features = $_POST['popular_features'];
    $map_embed_link = $_POST['map_embed_link'];

    // Handle main image (optional update)
    $image = $hotel['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = uniqid() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../images/' . $image);
    }

    // Handle image gallery upload
    $existingGallery = explode(',', $hotel['image_gallery']);
    $newImages = [];

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = time() . '_' . basename($_FILES['images']['name'][$key]);
            $target_file = '../images/' . $file_name;
            if (move_uploaded_file($tmp_name, $target_file)) {
                $newImages[] = $file_name;
            }
        }
    }

    $finalGallery = array_merge($existingGallery, $newImages);
    $image_gallery = implode(',', array_filter($finalGallery));

    $stmt = $conn->prepare("UPDATE hotels SET name=?, location=?, description=?, price_per_night=?, contact_info=?, rating=?, address=?, destination_id=?, image=?, facilities=?, popular_features=?, image_gallery=?, map_embed_link=? WHERE hotel_id=?");
    $stmt->execute([
        $name, $location, $description, $price, $contact, $rating, $address,
        $destination_id, $image, $facilities, $popular_features, $image_gallery, $map_embed_link, $hotel_id
    ]);

    header("Location: manage_hotels.php?updated=1");
    exit;
}
?>

<style>
  .position-relative img:hover {
    filter: brightness(80%);
    transition: filter 0.3s ease;
  }

  .btn-danger:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease-in-out;
  }
</style>


<!DOCTYPE html>
<html>
<head>
  <title>Edit Hotel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2>Edit Hotel</h2>
  <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($hotel['name']) ?>" required></div>
    <div class="mb-3"><label>Location</label><input type="text" name="location" class="form-control" value="<?= htmlspecialchars($hotel['location']) ?>" required></div>
    <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($hotel['description']) ?></textarea></div>
    <div class="mb-3"><label>Price Per Night</label><input type="number" name="price_per_night" class="form-control" value="<?= $hotel['price_per_night'] ?>" required></div>
    <div class="mb-3"><label>Contact Info</label><input type="text" name="contact_info" class="form-control" value="<?= htmlspecialchars($hotel['contact_info']) ?>" required></div>
    <div class="mb-3"><label>Rating</label><input type="number" name="rating" class="form-control" step="0.1" max="5" value="<?= $hotel['rating'] ?>"></div>
    <div class="mb-3"><label>Address</label><input type="text" name="address" class="form-control" value="<?= htmlspecialchars($hotel['address']) ?>"></div>
    <div class="mb-3"><label>Destination ID</label><input type="number" name="destination_id" class="form-control" value="<?= $hotel['destination_id'] ?>"></div>
    <div class="mb-3"><label>Facilities</label><input type="text" name="facilities" class="form-control" value="<?= htmlspecialchars($hotel['facilities']) ?>"></div>
    <div class="mb-3"><label>Popular Features</label><input type="text" name="popular_features" class="form-control" value="<?= htmlspecialchars($hotel['popular_features']) ?>"></div>
    <div class="mb-3"><label>Map Embed Link</label><input type="text" name="map_embed_link" class="form-control" value="<?= htmlspecialchars($hotel['map_embed_link']) ?>"></div>

    <div class="mb-3">
      <label>Main Image</label><br>
      <?php if ($hotel['image']): ?>
        <img src="../images/<?= $hotel['image'] ?>" alt="Hotel Image" style="height: 100px;"><br>
      <?php endif; ?>
      <input type="file" name="image" class="form-control">
    </div>

    <div class="mb-3">
      <label>Upload More Gallery Images</label>
      <input type="file" name="images[]" class="form-control" multiple>
    </div>

    <div class="mb-3">
      <label>Current Gallery Images</label><br>
      <?php
        $gallery = explode(',', $hotel['image_gallery']);
        foreach ($gallery as $img) {
            if (!empty($img)) {
                echo '<img src="../images/' . $img . '" alt="" style="height:80px;margin:5px;">';
            }
        }
      ?>
    </div>
   <div class="mb-3">
      <label class="form-label">Current Gallery Images</label><br>
      <div class="d-flex flex-wrap gap-3">
        <?php
          $gallery = explode(',', $hotel['image_gallery']);
          foreach ($gallery as $img) {
              if (!empty($img)) {
                  echo '<div class="position-relative border rounded shadow-sm" style="width: 140px;">';
                  echo '<img src="../images/' . $img . '" class="img-fluid rounded" style="height: 100px; object-fit: cover; width: 100%;">';
                  echo '<a href="delete_image.php?hotel_id=' . $hotel_id . '&image=' . urlencode($img) . '" 
                            onclick="return confirm(\'Delete this image?\')" 
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center"
                            style="width: 25px; height: 25px; font-size: 14px; line-height: 1;">
                            &times;
                        </a>';
                  echo '</div>';
              }
          }
        ?>
      </div>
    </div> 

    <div class="mb-3">
      <button type="submit" class="btn btn-primary">Update Hotel</button>
      <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
      <a href="manage_hotels.php" class="btn btn-danger btn-secondary">Cancel</a>
    </div>


  </form>
</div>
</body>
</html>
