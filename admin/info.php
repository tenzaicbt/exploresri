<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

// Count total records in major tables
function getTableCount($conn, $table)
{
    try {
        $stmt = $conn->query("SELECT COUNT(*) FROM $table");
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 'N/A';
    }
}

$counts = [
    'Users' => getTableCount($conn, 'users'),
    'Admins' => getTableCount($conn, 'admins'),
    'Hotels' => getTableCount($conn, 'hotels'),
    'Guides' => getTableCount($conn, 'guide'),
    'Vehicles' => getTableCount($conn, 'vehicles'),
    'Bookings' => getTableCount($conn, 'bookings'),
    'Guide Bookings' => getTableCount($conn, 'guide_bookings'),
    'Vehicle Bookings' => getTableCount($conn, 'vehicle_bookings')
];

$displayErrors = ini_get('display_errors');
$sessionCookieParams = session_get_cookie_params();
$poweredByExposed = in_array('X-Powered-By', headers_list());
$uploadsWritable = is_writable(__DIR__ . '/../uploads'); // adjust if you have an uploads folder
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>System Info - ExploreSri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            padding-top: 50px;
            max-width: 1000px;
        }

        .card {
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: #1b2735;
            color: white;
            font-weight: bold;
        }

        .badge {
            font-size: 0.85rem;
        }

        .developer-info p {
            margin: 0;
        }

        .table-summary td {
            font-weight: 500;
        }

        .text-muted small {
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3 class="mb-4 text-center">System Information</h3>
        <!-- Developer Info -->
        <div class="card">
            <div class="card-header">Developer Info</div>
            <div class="card-body developer-info">
                <p><strong>Developer:</strong> Yohan Koshala</p>
                <p><strong>Location:</strong> Sri Lanka</p>

                <div class="mt-3">
                    <span class="badge bg-dark">ExploreSri Version 1.0</span>
                    <span class="badge bg-secondary">Last Update: June 2025</span>
                </div>
            </div>
        </div>

        <!-- System Stats -->
        <!-- <div class="card">
            <div class="card-header">Live System Statistics</div>
            <div class="card-body">
                <table class="table table-bordered table-summary text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Module</th>
                            <th>Records</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($counts as $label => $count): ?>
                            <tr>
                                <td><?= $label ?></td>
                                <td><span class="badge bg-primary"><?= $count ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div> -->

        <!-- Server Info -->
        <div class="card">
            <div class="card-header">Server & PHP Info</div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">PHP Version</dt>
                    <dd class="col-sm-8"><?= phpversion(); ?></dd>

                    <dt class="col-sm-4">MySQL Version</dt>
                    <dd class="col-sm-8"><?= $conn->getAttribute(PDO::ATTR_SERVER_VERSION); ?></dd>

                    <dt class="col-sm-4">Server Software</dt>
                    <dd class="col-sm-8"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></dd>

                    <dt class="col-sm-4">Operating System</dt>
                    <dd class="col-sm-8"><?= php_uname(); ?></dd>
                </dl>
            </div>
        </div>

        <!-- System Status -->
        <div class="card">
            <div class="card-header">System Status & Security</div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        Session Status:
                        <?= session_status() === PHP_SESSION_ACTIVE ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' ?>
                    </li>
                    <li class="list-group-item">HTTPS: <span class="badge bg-warning text-dark">Check manually on server</span></li>
                    <li class="list-group-item">Admin Auth: <span class="badge bg-success">Secured (Session)</span></li>
                    <li class="list-group-item">Database Connection: <span class="badge bg-success">Connected</span></li>
                    <li class="list-group-item">Prepared Queries: <span class="badge bg-info">Recommended</span></li>
                    <li class="list-group-item">
                        Display Errors:
                        <?= $displayErrors ? '<span class="badge bg-danger">ON</span> (Should be OFF in production)' : '<span class="badge bg-success">OFF</span>' ?>
                    </li>

                    <li class="list-group-item">
                        Session Cookies (HTTPOnly):
                        <?= $sessionCookieParams['httponly'] ? '<span class="badge bg-success">Enabled</span>' : '<span class="badge bg-danger">Disabled</span>' ?>
                    </li>

                    <li class="list-group-item">
                        Session Cookies (Secure):
                        <?= $sessionCookieParams['secure'] ? '<span class="badge bg-success">Enabled</span>' : '<span class="badge bg-warning text-dark">Not forced</span>' ?>
                    </li>

                    <li class="list-group-item">
                        X-Powered-By Header:
                        <?= $poweredByExposed ? '<span class="badge bg-warning text-dark">Exposed (Can leak PHP version)</span>' : '<span class="badge bg-success">Hidden</span>' ?>
                    </li>

                    <li class="list-group-item">
                        Uploads Directory Writable:
                        <?= $uploadsWritable ? '<span class="badge bg-warning text-dark">Writable</span> (Restrict in production)' : '<span class="badge bg-success">Safe</span>' ?>
                    </li>

                </ul>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-dark">Back to Dashboard</a>
        </div>
    </div>
</body>
<div class="text-center mt-4"></div>

</html>