<?php
include '../config/db.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid ID");
}

$stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
$stmt->execute([$id]);
$place = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE destinations SET name=?, description=?, location=?, category=?, province=?, top_attractions=?, latitude=?, longitude=? WHERE destination_id=?");
    $stmt->execute([
        $_POST['name'], $_POST['description'], $_POST['location'], $_POST['category'],
        $_POST['province'], $_POST['top_attractions'], $_POST['latitude'], $_POST['longitude'], $id
    ]);
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
</head>
<body>
<div class="container mt-5">
  <h2>Edit Place</h2>
  <form method="POST">
    <div class="mb-3"><label>Name</label><input class="form-control" name="name" value="<?= htmlspecialchars($place['name']) ?>"></div>
    <div class="mb-3"><label>Description</label><textarea class="form-control" name="description"><?= htmlspecialchars($place['description']) ?></textarea></div>
    <div class="mb-3"><label>Location</label><input class="form-control" name="location" value="<?= htmlspecialchars($place['location']) ?>"></div>
    <div class="mb-3"><label>Category</label><input class="form-control" name="category" value="<?= htmlspecialchars($place['category']) ?>"></div>
    <div class="mb-3"><label>Province</label><input class="form-control" name="province" value="<?= htmlspecialchars($place['province']) ?>"></div>
    <div class="mb-3"><label>Top Attractions</label><input class="form-control" name="top_attractions" value="<?= htmlspecialchars($place['top_attractions']) ?>"></div>
    <div class="mb-3"><label>Latitude</label><input class="form-control" name="latitude" value="<?= htmlspecialchars($place['latitude']) ?>"></div>
    <div class="mb-3"><label>Longitude</label><input class="form-control" name="longitude" value="<?= htmlspecialchars($place['longitude']) ?>"></div>
    <button class="btn btn-success">Update</button>
    <a href="manage_places.php" class="btn btn-secondary">Back</a>
  </form>
</div>
</body>
</html>
