<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php'; // Ensure $conn is your PDO connection

// Toggle company status if requested
if (isset($_GET['toggle_id'])) {
    $toggle_id = $_GET['toggle_id'];

    $stmt = $conn->prepare("SELECT status FROM transport_companies WHERE company_id = ?");
    $stmt->execute([$toggle_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($company) {
        $newStatus = ($company['status'] === 'active') ? 'inactive' : 'active';
        $update = $conn->prepare("UPDATE transport_companies SET status = ?, updated_at = NOW() WHERE company_id = ?");
        $update->execute([$newStatus, $toggle_id]);
        header("Location: manage_companies.php");
        exit;
    }
}

// Fetch all companies with vehicle count
$companies = [];
try {
    $stmt = $conn->query("
        SELECT 
            c.*, 
            COUNT(v.vehicle_id) AS vehicle_count 
        FROM 
            transport_companies c
        LEFT JOIN 
            vehicles v ON c.company_id = v.company_id
        GROUP BY 
            c.company_id
    ");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching companies: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Transport Companies</title>
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

        .table td,
        .table th {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.85rem;
        }

        .btn-sm {
            font-size: 0.75rem;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="mb-4">Manage Transport Companies</h2>

        <table class="table table-bordered table-hover text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Company Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Vehicles</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($companies) > 0): ?>
                    <?php foreach ($companies as $company): ?>
                        <tr>
                            <td><?= htmlspecialchars($company['company_id']) ?></td>
                            <td>
                                <?php if (!empty($company['logo']) && file_exists('../' . $company['logo'])): ?>
                                    <img src="../<?= htmlspecialchars($company['logo']) ?>" alt="Logo" style="width: 60px; height: 40px; object-fit: contain; border-radius: 4px;">
                                <?php else: ?>
                                    <span class="text-muted small">No Logo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($company['company_name']) ?></td>
                            <td><?= htmlspecialchars($company['email']) ?></td>
                            <td><?= htmlspecialchars($company['phone']) ?></td>
                            <td><?= htmlspecialchars($company['address']) ?></td>
                            <td>
                                <?php
                                $status = $company['status'];
                                $badgeClass = match ($status) {
                                    'active' => 'bg-success',
                                    'inactive' => 'bg-secondary',
                                    'suspended' => 'bg-danger',
                                    default => 'bg-light text-dark'
                                };
                                echo "<span class='badge $badgeClass me-2'>" . ucfirst($status) . "</span>";
                                ?>
                            </td>
                            <td><span class="badge bg-info text-dark"><?= $company['vehicle_count'] ?></span></td>
                            <td><?= htmlspecialchars($company['created_at']) ?></td>
                            <td><?= htmlspecialchars($company['updated_at']) ?></td>
                            <td>
                                <a href="?toggle_id=<?= $company['company_id'] ?>"
                                   class="btn btn-sm <?= $company['status'] === 'active' ? 'btn-danger' : 'btn-success' ?>"
                                   onclick="return confirm('Are you sure you want to change the status of this company?')">
                                    <?= $company['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center">No transport companies found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mb-3">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

</body>

</html>
