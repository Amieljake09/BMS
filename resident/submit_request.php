<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database Connection
$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and assign form inputs
$resident_id = $_POST['resident_id'];
$resident_name = $conn->real_escape_string($_POST['resident_name']);
$document_type = $conn->real_escape_string($_POST['document_type']);
$purpose = $conn->real_escape_string($_POST['purpose']);
$notes = $conn->real_escape_string($_POST['notes'] ?? '');
$request_date = date("Y-m-d H:i:s");
$status = 'Pending'; // Default status

// Optional Fields
$business_name = $conn->real_escape_string($_POST['business_name'] ?? null);
$business_address = $conn->real_escape_string($_POST['business_address'] ?? null);
$tin_number = $conn->real_escape_string($_POST['tin_number'] ?? null);
$profession = $conn->real_escape_string($_POST['profession'] ?? null);

// NEW FIELDS
$dob = $conn->real_escape_string($_POST['dob'] ?? null);
$pob = $conn->real_escape_string($_POST['pob'] ?? null);
$citizenship = $conn->real_escape_string($_POST['citizenship'] ?? null);
$educ_attainment = $conn->real_escape_string($_POST['educ_attainment'] ?? null);
$course_graduated = $conn->real_escape_string($_POST['course_graduated'] ?? null);

// Insert into database
$sql = "INSERT INTO document_requests 
        (resident_id, resident_name, document_type, purpose, notes, request_date, status,
         business_name, business_address, tin_number, profession,
         dob, pob, citizenship, educ_attainment, course_graduated)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// Format string must match number of parameters: total 16
$stmt->bind_param(
    "isssssssssssssss", 
    $resident_id, 
    $resident_name, 
    $document_type, 
    $purpose, 
    $notes, 
    $request_date, 
    $status,
    $business_name, 
    $business_address, 
    $tin_number, 
    $profession,
    $dob, 
    $pob, 
    $citizenship, 
    $educ_attainment, 
    $course_graduated
);

if ($stmt->execute()) {
    echo "<script>
            alert('Request submitted successfully.');
            window.location.href='request_form.php';
          </script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>