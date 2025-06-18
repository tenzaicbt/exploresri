<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

// Update status or is_verified if POSTed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guide_id'], $_POST['status'])) {
        $guide_id = $_POST['guide_id'];
        $status = $_POST['status'];
        try {
            $stmt = $conn->prepare("UPDATE guide SET status = ? WHERE guide_id = ?");
            $stmt->execute([$status, $guide_id]);
        } catch (PDOException $e) {
            echo "Error updating status: " . $e->getMessage();
        }
    }

    if (isset($_POST['guide_id'], $_POST['is_verified'])) {
        $guide_id = $_POST['guide_id'];
        $is_verified = $_POST['is_verified'];
        try {
            $stmt = $conn->prepare("UPDATE guide SET is_verified = ? WHERE guide_id = ?");
            $stmt->execute([$is_verified, $guide_id]);
        } catch (PDOException $e) {
            echo "Error updating verification: " . $e->getMessage();
        }
    }
}

// Fetch all guides
$guides = [];
try {
    $stmt = $conn->query("SELECT * FROM guide");
    $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching guides: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Guides</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .container {
            margin-top: 60px;
        }

        .table th {
            background-color: #000;
            color: #fff;
        }

        .profile-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }

        .dropdown-form {
            min-width: 120px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="mb-4">Manage Tourist Guides</h2>

        <table class="table table-bordered table-hover text-center">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Languages</th>
                    <th>Experience</th>
                    <th>Contact</th>
                    <th>Price/Day</th>
                    <th>Rating</th>
                    <th>Verified</th>
                    <th>Status</th>
                    <th>Verify</th>
                    <th>Change Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($guides) > 0): ?>
                    <?php foreach ($guides as $guide): ?>
                        <tr>
                            <td>
                                <?php if ($guide['photo']): ?>
                                    <img src="../uploads/guides/<?= htmlspecialchars($guide['photo']) ?>" class="profile-img" alt="Guide Photo">
                                <?php else: ?>
                                    <span class="text-muted">No photo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($guide['name']) ?></td>
                            <td><?= htmlspecialchars($guide['email']) ?></td>
                            <td><?= htmlspecialchars($guide['languages']) ?></td>
                            <td><?= (int)$guide['experience_years'] ?> yrs</td>
                            <td><?= htmlspecialchars($guide['contact_info']) ?></td>
                            <td>$<?= number_format($guide['price_per_day'], 2) ?></td>
                            <td><?= number_format($guide['rating'], 1) ?></td>
                            <td>
                                <?= $guide['is_verified'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                            </td>
                            <td>
                                <span class="badge 
                                <?= $guide['status'] === 'active' ? 'bg-primary' : ($guide['status'] === 'inactive' ? 'bg-warning text-dark' : 'bg-danger') ?>">
                                    <?= ucfirst($guide['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" class="dropdown-form">
                                    <input type="hidden" name="guide_id" value="<?= $guide['guide_id'] ?>">
                                    <select name="is_verified" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="1" <?= $guide['is_verified'] ? 'selected' : '' ?>>Verified</option>
                                        <option value="0" <?= !$guide['is_verified'] ? 'selected' : '' ?>>Unverified</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form method="post" class="d-flex">
                                    <input type="hidden" name="guide_id" value="<?= $guide['guide_id'] ?>">
                                    <select name="status" class="form-select form-select-sm me-2">
                                        <option value="active" <?= $guide['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= $guide['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="deactive" <?= $guide['status'] === 'deactive' ? 'selected' : '' ?>>Deactive</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-success">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12">No guides found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

</body>

</html>