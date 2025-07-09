<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define age group queries across both tables
$age_groups = [
    '0-17' => "SELECT 
                  (SELECT COUNT(*) FROM residents WHERE age BETWEEN 0 AND 17) +
                  (SELECT COUNT(*) FROM officials WHERE age BETWEEN 0 AND 17) AS count",
    '18-35' => "SELECT 
                  (SELECT COUNT(*) FROM residents WHERE age BETWEEN 18 AND 35) +
                  (SELECT COUNT(*) FROM officials WHERE age BETWEEN 18 AND 35) AS count",
    '36-60' => "SELECT 
                  (SELECT COUNT(*) FROM residents WHERE age BETWEEN 36 AND 60) +
                  (SELECT COUNT(*) FROM officials WHERE age BETWEEN 36 AND 60) AS count",
    '60+' => "SELECT 
                  (SELECT COUNT(*) FROM residents WHERE age > 60) +
                  (SELECT COUNT(*) FROM officials WHERE age > 60) AS count"
];

$population_data = [];

foreach ($age_groups as $label => $query) {
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $population_data[$label] = (int)$row['count'];
}

$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

$conn->close();
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
    <?php include 'sidebar.php'; ?>
  </div>

  <!-- Main Content -->
  <div id="main-content" class="p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-white">Resident Dashboard</h1>
      <div class="relative">
        <button class="relative focus:outline-none">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C8.67 6.165 8 7.388 8 9v5c0 .386-.149.735-.405 1.001L6 17h5m4 0v1a2 2 0 11-4 0v-1m4 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="absolute top-0 right-0 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-bold text-blue-800 mb-4">📢 Barangay Announcements</h2>
    <?php while ($row = $announcements->fetch_assoc()): ?>
        <div class="border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($row['title']) ?></h3>
            <p class="text-gray-700"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <small class="text-gray-500"><?= $row['created_at'] ?></small>
        </div>
    <?php endwhile; ?>
</div>


    <!-- Charts & Officials -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Certificate Requests Chart -->
      <div class="bg-white rounded-2xl shadow-lg p-6 col-span-2 border-l-4 border-primary">
        <h3 class="text-lg font-semibold text-primary mb-4">📈 Certificate Requests (Monthly)</h3>
        <canvas id="requestChart"></canvas>
      </div>

      <!-- Population Chart -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-secondary">
        <h3 class="text-lg font-semibold text-secondary mb-4">👨‍👩‍👧‍👦 Population by Age Group</h3>
        <canvas id="populationChart"></canvas>
      </div>

    </div>

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
