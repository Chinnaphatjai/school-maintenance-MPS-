<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $action_details = $_POST['action_details'];

    // Update status and action details
    $stmt = $conn->prepare("UPDATE maintenance_requests SET status = ?, action_details = ? WHERE request_id = ?");
    $stmt->execute([$status, $action_details, $request_id]);

    header("Location: dashboard.php");
    exit();
}
?>