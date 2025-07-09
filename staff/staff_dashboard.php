<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Resident Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1e3a8a',
            secondary: '#10b981',
            accent: '#f59e0b'
          }
        }
      }
    };
  </script>
  <style>
    #sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 16rem;
      overflow-y: auto;
      z-index: 50;
    }
    #main-content {
      margin-left: 16rem;
      height: 100vh;
      overflow-y: auto;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-700 via-white to-blue-500">

  <!-- Sidebar -->
  <div id="sidebar">
    <?php include 'staff_sidebar.php'; ?>
  </div>

  <!-- Main Content -->
  <div id="main-content" class="p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-white">Staff Dashboard</h1>
      
    </div>

    


  <!-- Chart.js Scripts -->
  <script>
    const requestCtx = document.getElementById('requestChart').getContext('2d');
    new Chart(requestCtx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
          label: 'Requests',
          data: [5, 7, 9, 4, 6],
          backgroundColor: '#1e3a8a'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    const populationCtx = document.getElementById('populationChart').getContext('2d');
new Chart(populationCtx, {
  type: 'pie',
  data: {
    labels: ['0-17', '18-35', '36-60', '60+'],
    datasets: [{
      label: 'Population',
      data: [
        <?= $population_data['0-17'] ?>,
        <?= $population_data['18-35'] ?>,
        <?= $population_data['36-60'] ?>,
        <?= $population_data['60+'] ?>
      ],
      backgroundColor: ['#10b981', '#3b82f6', '#fbbf24', '#ef4444']
    }]
  },
  options: {
    responsive: true
  }
});

  </script>
</body>
</html>
