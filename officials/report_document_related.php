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

// Fetch Document Request Stats
$approved = $conn->query("SELECT COUNT(*) AS count FROM document_requests WHERE status = 'Approved'")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) AS count FROM document_requests WHERE status = 'Pending'")->fetch_assoc()['count'];
$rejected = $conn->query("SELECT COUNT(*) AS count FROM document_requests WHERE status = 'Rejected'")->fetch_assoc()['count'];

// Fetch Most Requested Document Types
$docTypeResult = $conn->query("SELECT document_type, COUNT(*) as count FROM document_requests GROUP BY document_type ORDER BY count DESC LIMIT 5");
$mostRequestedLabels = [];
$mostRequestedCounts = [];

while ($row = $docTypeResult->fetch_assoc()) {
    $mostRequestedLabels[] = $row['document_type'];
    $mostRequestedCounts[] = $row['count'];
}

// Fetch Monthly Summary
$monthlyData = [];
$monthlyResult = $conn->query("
    SELECT 
        DATE_FORMAT(request_date, '%Y-%m') AS month,
        SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected
    FROM document_requests
    GROUP BY month
    ORDER BY month
");

while ($row = $monthlyResult->fetch_assoc()) {
    $monthlyData[] = $row;
}

// Average Processing Time
$avgProcessingTime = 0;

$processingResult = $conn->query("
    SELECT 
        AVG(DATEDIFF(NOW(), request_date)) AS avg_days
    FROM document_requests
    WHERE status = 'Approved'
");

if ($processingRow = $processingResult->fetch_assoc()) {
    $avgProcessingTime = round($processingRow['avg_days'] ?? 0, 2);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Document-Related Reports</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <style>
        canvas {
            max-width: 100%;
        }

        /* Make sidebar fixed */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 50;
        }

        /* Push main content to the right of the sidebar */
        .main-content {
            margin-left: 16rem; /* Same as sidebar width */
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
<div class="main-content">
    <h1 class="text-2xl font-bold mb-6">Document-Related Reports</h1>

    <!-- Two Column Chart Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Monthly Summary Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Monthly Document Status Summary</h2>
            <canvas id="monthlySummaryChart"></canvas>
        </div>

        <!-- Most Requested Documents Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Most Requested Document Types</h2>
            <canvas id="mostRequestedChart"></canvas>
        </div>
    </div>

    <!-- Single Column Below -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Processing Time -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Average Processing Time</h2>
            <p class="text-lg">Average processing time: <strong><?= $avgProcessingTime ?> days</strong></p>
        </div>
    </div>
</div>

<script>
// Monthly Summary Chart
const monthlyCtx = document.getElementById('monthlySummaryChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($monthlyData as $item) echo '"' . $item['month'] . '",'; ?>],
        datasets: [
            { label: 'Approved', data: [<?php foreach ($monthlyData as $item) echo $item['approved'] . ','; ?>], backgroundColor: '#4CAF50' },
            { label: 'Pending', data: [<?php foreach ($monthlyData as $item) echo $item['pending'] . ','; ?>], backgroundColor: '#FFA726' },
            { label: 'Rejected', data: [<?php foreach ($monthlyData as $item) echo $item['rejected'] . ','; ?>], backgroundColor: '#EF5350' }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Count' } }
        }
    }
});

// Most Requested Chart
const mostRequestedCtx = document.getElementById('mostRequestedChart').getContext('2d');
new Chart(mostRequestedCtx, {
    type: 'pie',
    data: {
        labels: [<?php foreach ($mostRequestedLabels as $label) echo '"' . $label . '",'; ?>],
        datasets: [{
            data: [<?php foreach ($mostRequestedCounts as $count) echo $count . ','; ?>],
            backgroundColor: ['#42A5F5', '#66BB6A', '#FF7043', '#FFCA28', '#9CCC65']
        }]
    }
});
</script>

</body>
</html>