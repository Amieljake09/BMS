<?php
session_start();

// Prevent direct access if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Login Successful</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-900 bg-opacity-70">

  <!-- Modal Background -->
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40"></div>

  <!-- Modal Box -->
  <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm text-center z-50">
    <svg class="mx-auto mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
    </svg>
    <h2 class="text-2xl font-semibold mb-2">Login Successful!</h2>
    <p class="text-gray-700 mb-4">Welcome back! Redirecting to your dashboard...</p>
    <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-12 w-12 mx-auto"></div>
  </div>

  <style>
    .loader {
      border-top-color: #10B981; /* Tailwind green-500 */
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg);}
      100% { transform: rotate(360deg);}
    }
  </style>

  <script>
    // After 2.5 seconds, redirect to resident_dashboard.php
    setTimeout(() => {
      window.location.href = "resident_dashboard.php";
    }, 2500);
  </script>
</body>
</html>
