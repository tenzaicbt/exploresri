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

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $price = $_POST['price_per_night'];
    $contact = $_POST['contact_info'];
    $rating = $_POST['rating'];
    $address = $_POST['address'];
    $destination_id = $_POST['destination_id'];

    // Image update (optional)
    $image = $hotel['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = uniqid() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../images/' . $image);
    }

    $stmt = $conn->prepare("UPDATE hotels SET name=?, location=?, description=?, price_per_night=?, contact_info=?, rating=?, address=?, destination_id=?, image=? WHERE hotel_id=?");
    $stmt->execute([$name, $location, $description, $price, $contact, $rating, $address, $destination_id, $image, $hotel_id]);

    header("Location: manage_hotels.php?updated=1");
    exit;
}
?>

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
    <div class="alert alert-success"><?php echo $message; ?></div>
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
    <div class="mb-3"><label>Change Image (optional)</label><input type="file" name="image" class="form-control"></div>
    <button class="btn btn-success">Update</button>
    <a href="manage_hotels.php" class="btn btn-secondary">Back to Dashboard</a>
  </form>
</div>
</body>
</html>
