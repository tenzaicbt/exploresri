<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['status'], $_POST['id'])) {
        $status = $_POST['status'];
        $id = (int)$_POST['id'];
        $valid_statuses = ['active', 'inactive'];
        if (in_array($status, $valid_statuses)) {
            $stmt = $conn->prepare("UPDATE guide SET status = ? WHERE guide_id = ?");
            $stmt->execute([$status, $id]);
        }
    }
    if (isset($_POST['verify'], $_POST['id'])) {
        $verify = (int)$_POST['verify']; // 0 or 1
        $id = (int)$_POST['id'];
        if ($verify === 0 || $verify === 1) {
            $stmt = $conn->prepare("UPDATE guide SET is_verified = ? WHERE guide_id = ?");
            $stmt->execute([$verify, $id]);
        }
    }
    // Redirect to avoid form resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$guides = [];
try {
    $stmt = $conn->query("SELECT * FROM guide");
    $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Manage Tourist Guides</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mb-4">Manage Tourist Guides</h2>

        <table class="table table-bordered table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Price/Day</th>
                    <th>Verified</th>
                    <th>Verify Toggle</th>
                    <th>Status</th>
                    <th>Status Toggle</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($guides) > 0) : ?>
                    <?php foreach ($guides as $guide) : ?>
                        <tr>
                            <td>
                                <?php if (!empty($guide['photo']) && file_exists('../uploads/guides/' . $guide['photo'])) : ?>
                                    <img src="../uploads/guides/<?= htmlspecialchars($guide['photo']) ?>" class="profile-img" alt="Guide Photo" />
                                <?php else : ?>
                                    <span class="text-muted">No photo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($guide['name']) ?></td>
                            <td><?= htmlspecialchars($guide['email']) ?></td>
                            <td>$<?= number_format($guide['price_per_day'], 2) ?></td>

                            <!-- Verified Badge -->
                            <td>
                                <span class="badge <?= $guide['is_verified'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $guide['is_verified'] ? 'Verified' : 'Unverified' ?>
                                </span>
                            </td>

                            <!-- Verified Toggle Button -->
                            <td>
                                <form method="POST" action="" class="m-0 p-0">
                                    <input type="hidden" name="verify" value="<?= $guide['is_verified'] ? 0 : 1 ?>" />
                                    <input type="hidden" name="id" value="<?= (int)$guide['guide_id'] ?>" />
                                    <button type="submit" class="btn btn-sm <?= $guide['is_verified'] ? 'btn-danger' : 'btn-success' ?>">
                                        <?= $guide['is_verified'] ? 'Unverify' : 'Verify' ?>
                                    </button>
                                </form>
                            </td>

                            <!-- Status Badge -->
                            <td>
                                <?php
                                $status = $guide['status'];
                                $statusClass = match ($status) {
                                    'active' => 'bg-primary',
                                    'inactive' => 'bg-warning text-dark',
                                    'deactive' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= ucfirst($status) ?></span>
                            </td>

                            <!-- Status Toggle Button -->
                            <td>
                                <form method="POST" action="" class="m-0 p-0">
                                    <input type="hidden" name="status" value="<?= $status === 'active' ? 'inactive' : 'active' ?>" />
                                    <input type="hidden" name="id" value="<?= (int)$guide['guide_id'] ?>" />
                                    <button type="submit" class="btn btn-sm <?= $status === 'active' ? 'btn-danger' : 'btn-success' ?>">
                                        <?= $status === 'active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                            </td>

                            <!-- View Button triggers modal -->
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#guideModal<?= (int)$guide['guide_id'] ?>">
                                    View
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="guideModal<?= (int)$guide['guide_id'] ?>" tabindex="-1" aria-labelledby="guideModalLabel<?= (int)$guide['guide_id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="guideModalLabel<?= (int)$guide['guide_id'] ?>">Guide Details - <?= htmlspecialchars($guide['name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="row mb-3">
                                                    <div class="col-md-4 text-center">
                                                        <?php if (!empty($guide['photo']) && file_exists('../uploads/guides/' . $guide['photo'])) : ?>
                                                            <img src="../uploads/guides/<?= htmlspecialchars($guide['photo']) ?>" class="img-fluid rounded" alt="Guide Photo" />
                                                        <?php else : ?>
                                                            <span class="text-muted">No photo</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <p><strong>Name:</strong> <?= htmlspecialchars($guide['name']) ?></p>
                                                        <p><strong>Email:</strong> <?= htmlspecialchars($guide['email']) ?></p>
                                                        <p><strong>Languages:</strong> <?= htmlspecialchars($guide['languages']) ?></p>
                                                        <p><strong>Experience:</strong> <?= (int)$guide['experience_years'] ?> years</p>
                                                        <p><strong>Contact:</strong> <?= htmlspecialchars($guide['contact_info']) ?></p>
                                                        <p><strong>Price per Day:</strong> $<?= number_format($guide['price_per_day'], 2) ?></p>
                                                        <p><strong>Rating:</strong> <?= number_format($guide['rating'], 1) ?></p>
                                                        <p><strong>Verified:</strong> <?= $guide['is_verified'] ? 'Yes' : 'No' ?></p>
                                                        <p><strong>Status:</strong> <?= ucfirst($guide['status']) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" class="text-center">No guides found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
