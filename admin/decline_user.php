<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Step 1: Delete from child tables first
        $stmt = $conn->prepare("DELETE FROM residents WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM officials WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM staff WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Step 2: Delete from users table
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        // Redirect with success
        header("Location: account_approval.php?declined=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error declining account: " . $e->getMessage());
    }
}
?>