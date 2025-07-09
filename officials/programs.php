<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Barangay Officials Dashboard</title>
    <script src="https://cdn.tailwindcss.com "></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e3a8a',
                        secondary: '#10b981',
                        accent: '#f59e0b'
                    },
                },
            },
        };
    </script>
</head>
<body class="flex min-h-screen bg-gradient-to-br from-blue-700 via-white to-blue-500">

<!-- Sidebar -->
<?php include 'officials_sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-1 p-8 overflow-y-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-white">Programs & Projects</h1>
        <div class="relative">
            <button class="relative focus:outline-none">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2 2 0 0118 14V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C8.67 6.165 8 7.388 8 9v5c0 .386-.149.735-.405 1.001L6 17h5m4 0v1a2 2 0 11-4 0v-1m4 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="absolute top-0 right-0 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
        </div>
    </div>

   

    


</body>
</html>