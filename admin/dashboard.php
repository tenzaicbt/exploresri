<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<h2>Admin Dashboard</h2>
<ul>
    <li><a href="manage_hotels.php">Manage Hotels</a></li>
    <li><a href="manage_destinations.php">Manage Destinations</a></li>
    <li><a href="manage_bookings.php">Manage Bookings</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
