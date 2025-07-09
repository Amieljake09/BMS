<?php
session_start();

// Check if user is logged in and is an Official
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle approve or reject
if (isset($_POST['request_id']) && isset($_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    $action = $conn->real_escape_string($_POST['action']);
    $tab = isset($_POST['tab']) ? intval($_POST['tab']) : 0;

    // Determine status based on action
    $status = ($action === 'approve') ? 'Approved' : 'Rejected';

    if ($action === 'approve') {
        // Approve: update status + approved_by
        $stmt = $conn->prepare("UPDATE document_requests SET status = ?, approved_by = ? WHERE id = ?");
        $stmt->bind_param("sii", $status, $_SESSION['user_id'], $request_id);
    } else {
        // Reject: update status lang
        $stmt = $conn->prepare("UPDATE document_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $request_id);
    }

    if ($stmt->execute()) {
        // Redirect back with success or reject flag
        $redirect = "Location: document_approvals.php?tab=$tab&";
        if ($action === 'approve') {
            $redirect .= "success=1";
        } else {
            $redirect .= "reject=1";
        }
        header($redirect);
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
exit();
?>