<?php
$servername = "localhost:3307";  
$username = "root";         
$password = "";             
$database = "barangay_management_system";  

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// comment out nyo na lang if hindi nyo need yung echo lagyan nyo ng ganto sa una //
echo ""
?>