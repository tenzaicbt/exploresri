<?php
session_start();
include '../config/db.php';  // Adjust path if needed

if (!isset($_SESSION['company_id'])) {
    header("Location: ../login.php");
    exit;
}

$company_id = $_SESSION['company_id'];

if (isset($_POST['delete_vehicle'])) {
    $vehicle_id = $_POST['vehicle_id'];

    $delete_stmt = $conn->prepare("DELETE FROM vehicles WHERE vehicle_id = ? AND company_id = ?");
    $delete_stmt->execute([$vehicle_id, $company_id]);

    header("Location: transport_dashboard.php");  // Adjust if in different folder
    exit;
}
?>
