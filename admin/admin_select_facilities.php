<?php
include 'config/db.php';

$hotel_id = $_GET['hotel_id'] ?? null;
if (!$hotel_id) exit('Hotel ID missing.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clear old selections
    $conn->prepare("DELETE FROM hotel_facilities WHERE hotel_id = ?")->execute([$hotel_id]);

    // Insert new ticks
    foreach ($_POST['facilities'] as $facility_id) {
        $stmt = $conn->prepare("INSERT INTO hotel_facilities (hotel_id, facility_id) VALUES (?, ?)");
        $stmt->execute([$hotel_id, $facility_id]);
    }
    echo "Facilities updated successfully.";
}

// Fetch all possible facilities
$facilities = $conn->query("SELECT * FROM facilities")->fetchAll();
$selected = $conn->prepare("SELECT facility_id FROM hotel_facilities WHERE hotel_id = ?");
$selected->execute([$hotel_id]);
$selected_ids = array_column($selected->fetchAll(PDO::FETCH_ASSOC), 'facility_id');
?>

<form method="post">
  <h3>Select Facilities for Hotel #<?= $hotel_id ?></h3>
  <?php foreach ($facilities as $f): ?>
    <div>
      <label>
        <input type="checkbox" name="facilities[]" value="<?= $f['facility_id'] ?>"
            <?= in_array($f['facility_id'], $selected_ids) ? 'checked' : '' ?>>
        <?= htmlspecialchars($f['name']) ?>
      </label>
    </div>
  <?php endforeach; ?>
  <button type="submit">Save Facilities</button>
</form>
