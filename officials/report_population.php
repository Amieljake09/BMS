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

// Resident Registration Statistics
$totalResidents = $conn->query("SELECT COUNT(*) AS count FROM residents")->fetch_assoc()['count'];

$registrationStats = [];

// Check if 'date_registered' exists in the residents table
$result = $conn->query("SHOW COLUMNS FROM residents LIKE 'date_registered'");
if ($result->num_rows > 0) {
    // If column exists, use it
    $resRegResult = $conn->query("
        SELECT DATE_FORMAT(date_registered, '%Y-%m') AS month, COUNT(*) AS count 
        FROM residents 
        GROUP BY month 
        ORDER BY month
    ");
} else {
    // If column does NOT exist, fallback to created_at or another column
    $resRegResult = $conn->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count 
        FROM residents 
        GROUP BY month 
        ORDER BY month
    ");
}

while ($row = $resRegResult->fetch_assoc()) {
    $registrationStats[] = $row;
}

// Age Group Demographics
$ageGroups = [
    '0-17' => "SELECT COUNT(*) AS count FROM residents WHERE age BETWEEN 0 AND 17",
    '18-35' => "SELECT COUNT(*) AS count FROM residents WHERE age BETWEEN 18 AND 35",
    '36-60' => "SELECT COUNT(*) AS count FROM residents WHERE age BETWEEN 36 AND 60",
    '60+' => "SELECT COUNT(*) AS count FROM residents WHERE age > 60"
];

$ageGroupData = [];

foreach ($ageGroups as $label => $query) {
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $ageGroupData[$label] = (int)$row['count'];
}

// Employment Status Overview
$employmentData = [];
$empQuery = "SELECT employment_status, COUNT(*) AS count FROM residents GROUP BY employment_status";
$empResult = $conn->query($empQuery);

while ($row = $empResult->fetch_assoc()) {
    $employmentData[$row['employment_status']] = (int)$row['count'];
}

// Household Composition
$totalHouseholds = $conn->query("SELECT COUNT(*) AS count FROM households")->fetch_assoc()['count'];

// Compute average members per household manually from the 'members' JSON field
$sumMembers = 0;
$result = $conn->query("SELECT members FROM households");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $members = json_decode($row['members'], true); // decode JSON array
        if (is_array($members)) {
            $sumMembers += count($members);
        }
    }
}

$avgMembersPerHousehold = $totalHouseholds > 0 ? round($sumMembers / $totalHouseholds, 2) : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Barangay Population Reports</title>
    <script src="https://cdn.tailwindcss.com "></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
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
<body class="bg-gray-100 bg-gradient-to-br from-blue-700 via-white to-blue-500">

<!-- Sidebar -->
<?php include 'officials_sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <h1 class="text-2xl font-bold mb-6">Barangay Population Reports</h1>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Resident Registration Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Resident Registration Over Time</h2>
            <canvas id="residentRegistrationChart"></canvas>
        </div>

        <!-- Age Group Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Population by Age Group</h2>
            <canvas id="ageGroupChart"></canvas>
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Employment Status Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Employment Status Overview</h2>
            <canvas id="employmentStatusChart"></canvas>
        </div>

        <!-- Household Composition Box -->
        <div class="bg-white p-6 rounded-lg shadow flex flex-col justify-center">
            <h2 class="text-xl font-semibold mb-4">Household Composition</h2>
            <p>Total Households: <strong><?= $totalHouseholds ?></strong></p>
            <p>Average Members per Household: <strong><?= $avgMembersPerHousehold ?></strong></p>
        </div>
    </div>
</div>

<script>
// Resident Registration Chart
const regCtx = document.getElementById('residentRegistrationChart').getContext('2d');
new Chart(regCtx, {
    type: 'line',
    data: {
        labels: [<?php foreach ($registrationStats as $item) echo '"' . $item['month'] . '",'; ?>],
        datasets: [{
            label: 'New Registrations',
            data: [<?php foreach ($registrationStats as $item) echo $item['count'] . ','; ?>],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Age Group Chart
const ageCtx = document.getElementById('ageGroupChart').getContext('2d');
new Chart(ageCtx, {
    type: 'bar',
    data: {
        labels: ['0-17', '18-35', '36-60', '60+'],
        datasets: [{
            label: 'Population Count',
            data: [
                <?= $ageGroupData['0-17'] ?? 0 ?>,
                <?= $ageGroupData['18-35'] ?? 0 ?>,
                <?= $ageGroupData['36-60'] ?? 0 ?>,
                <?= $ageGroupData['60+'] ?? 0 ?>
            ],
            backgroundColor: '#f59e0b'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Employment Status Chart
const empCtx = document.getElementById('employmentStatusChart').getContext('2d');
new Chart(empCtx, {
    type: 'pie',
    data: {
        labels: [<?php foreach ($employmentData as $key => $value) echo '"' . $key . '",'; ?>],
        datasets: [{
            data: [<?php foreach ($employmentData as $value) echo $value . ','; ?>],
            backgroundColor: ['#42A5F5', '#66BB6A', '#FF7043', '#FFCA28', '#9CCC65']
        }]
    }
});
</script>

</body>
</html>