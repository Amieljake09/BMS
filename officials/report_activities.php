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

// Fetch recent activities
$recentActivities = [];
$res = $conn->query("SELECT * FROM activities ORDER BY date_held DESC LIMIT 5");

while ($row = $res->fetch_assoc()) {
    $recentActivities[] = $row;
}

// Monthly Activity Summary (Count by Month)
$monthlySummary = [];
$res = $conn->query("
    SELECT DATE_FORMAT(date_held, '%Y-%m') AS month, COUNT(*) AS count 
    FROM activities 
    GROUP BY month 
    ORDER BY month
");

while ($row = $res->fetch_assoc()) {
    $monthlySummary[] = $row;
}

// Top Participated Events
$topEvents = [];
$res = $conn->query("
    SELECT title, attendees_count 
    FROM activities 
    WHERE attendees_count > 0 
    ORDER BY attendees_count DESC 
    LIMIT 5
");

while ($row = $res->fetch_assoc()) {
    $topEvents[] = $row;
}



$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Barangay Population Reports</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <style>
        canvas {
            max-width: 100%;
        }

        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 50;
        }

        .main-content {
            margin-left: 16rem;
            padding: 2rem;
            overflow-y: auto;
            height: 100vh;
        }
    </style>
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
<div class="p-6 ml-64 mt-16">
    <h1 class="text-2xl font-bold mb-6">Activity Reports</h1>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Monthly Activity Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Monthly Activity Summary</h2>
            <canvas id="monthlyActivityChart" height="100"></canvas>
        </div>

        <!-- Top Participated Events -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Top Participated Events</h2>
            <canvas id="topEventsChart" height="150"></canvas>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h2 class="text-xl font-semibold mb-4">Recent Barangay Activities</h2>
        <div class="space-y-4">
            <?php if (!empty($recentActivities)): ?>
                <?php foreach ($recentActivities as $activity): ?>
                    <div class="border-b pb-3">
                        <h3 class="font-semibold"><?= htmlspecialchars($activity['title']) ?></h3>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($activity['description']) ?></p>
                        <div class="mt-1 text-xs text-gray-500">
                            <?= date('F j, Y', strtotime($activity['date_held'])) ?> | <?= htmlspecialchars($activity['location']) ?> | <?= $activity['attendees_count'] ?> attendees
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500">No activities found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const monthlyCtx = document.getElementById('monthlyActivityChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($monthlySummary as $item) echo '"' . $item['month'] . '",'; ?>],
        datasets: [{
            label: 'Number of Activities',
            data: [<?php foreach ($monthlySummary as $item) echo $item['count'] . ','; ?>],
            backgroundColor: '#3b82f6'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Top Events Chart
const topEventsCtx = document.getElementById('topEventsChart').getContext('2d');
new Chart(topEventsCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($topEvents as $item) echo '"' . $item['title'] . '",'; ?>],
        datasets: [{
            label: 'Attendees',
            data: [<?php foreach ($topEvents as $item) echo $item['attendees_count'] . ','; ?>],
            backgroundColor: '#10b981'
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        scales: {
            x: { beginAtZero: true }
        }
    }
});
// Monthly Activity Cha
</script>



</body>
</html>