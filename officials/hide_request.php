<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
    header("Location: login.php");
    exit();
}

$request_id = intval($_GET['id']);
$tab = isset($_GET['tab']) ? intval($_GET['tab']) : 0;

$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Mark as hidden for the official
$stmt = $conn->prepare("UPDATE document_requests SET hidden_for_official = TRUE WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: document_approvals.php?tab=$tab");
exit();
?>