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

$user_id = $_SESSION['user_id'];
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($request_id > 0) {
    // Check ownership first
    $sql_check = "SELECT id FROM document_requests WHERE id = ? AND resident_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $request_id, $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    if ($result->num_rows > 0) {
        // Delete request
        $stmt_check->close();
        $sql_del = "DELETE FROM document_requests WHERE id = ? AND resident_id = ?";
        $stmt_del = $conn->prepare($sql_del);
        $stmt_del->bind_param("ii", $request_id, $user_id);
        $stmt_del->execute();
        $stmt_del->close();
    } else {
        $stmt_check->close();
    }
}

header("Location: request_form.php");
exit();
