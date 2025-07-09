<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$request_id = intval($_POST['request_id']);
$document_type = $conn->real_escape_string($_POST['document_type']);
$purpose = $conn->real_escape_string($_POST['purpose']);

$business_name = isset($_POST['business_name']) ? $conn->real_escape_string($_POST['business_name']) : null;
$business_address = isset($_POST['business_address']) ? $conn->real_escape_string($_POST['business_address']) : null;
$tin_number = isset($_POST['tin_number']) ? $conn->real_escape_string($_POST['tin_number']) : null;
$profession = isset($_POST['profession']) ? $conn->real_escape_string($_POST['profession']) : null;

$sql = "UPDATE document_requests SET 
            document_type = ?, 
            purpose = ?, 
            business_name = ?, 
            business_address = ?, 
            tin_number = ?, 
            profession = ? 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssi", $document_type, $purpose, $business_name, $business_address, $tin_number, $profession, $request_id);
$stmt->execute();
$stmt->close();
$conn->close();

// Redirect with success flag
header("Location: request_form.php?edit_success=1");
exit();
?>