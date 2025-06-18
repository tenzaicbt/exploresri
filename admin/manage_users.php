<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php'; // Ensure $conn (PDO)

// Fetch users
$users = [];
try {
    $stmt = $conn->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .container {
            margin-top: 60px;
        }

        .table th {
            background-color: rgb(0, 0, 0);
            color: white;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .profile-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="mb-4">Manage Users</h2>

        <table class="table table-bordered table-hover text-center">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Country</th>
                    <th>Verified</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['contact_number']) ?></td>
                            <td><?= htmlspecialchars($user['country']) ?></td>
                            <td>
                                <?= $user['is_verified'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                            </td>
                            <td>
                                <?= $user['status'] == 'active' ? '<span class="badge bg-primary">Active</span>' : '<span class="badge bg-danger">Inactive</span>' ?>
                            </td>
                            <td>
                                <?= $user['role'] == 'admin' ? '<span class="badge bg-warning text-dark">Admin</span>' : '<span class="badge bg-info text-dark">User</span>' ?>
                            </td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No users found.</td>
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