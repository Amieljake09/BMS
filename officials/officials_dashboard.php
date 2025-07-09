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

// Count total residents and officials
$residents_count = $conn->query("SELECT COUNT(*) AS count FROM residents")->fetch_assoc()['count'];
$officials_count = $conn->query("SELECT COUNT(*) AS count FROM officials")->fetch_assoc()['count'];

// Handle Announcement Submission
$success = $error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_announcement'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $created_at = date("Y-m-d H:i:s");

    $insert = "INSERT INTO announcements (title, description, created_at) VALUES ('$title', '$description', '$created_at')";
    if ($conn->query($insert)) {
        $success = "Announcement added successfully!";
        // Prevent form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    } else {
        $error = "Error adding announcement: " . $conn->error;
    }
}

// If redirected after success
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Announcement added successfully!";
}

// Fetch announcements
$announcementResult = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");


// Fetch document request stats
$approved = $conn->query("SELECT COUNT(*) AS count FROM document_requests WHERE status = 'Approved'")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) AS count FROM document_requests WHERE status = 'Pending'")->fetch_assoc()['count'];
$rejected = $conn->query("SELECT COUNT(*) AS count FROM document_requests WHERE status = 'Rejected'")->fetch_assoc()['count'];

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
        <h1 class="text-3xl font-bold text-white">Officials Dashboard</h1>
        <div class="relative">
            <button class="relative focus:outline-none">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2 2 0 0118 14V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C8.67 6.165 8 7.388 8 9v5c0 .386-.149.735-.405 1.001L6 17h5m4 0v1a2 2 0 11-4 0v-1m4 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="absolute top-0 right-0 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
        </div>
    </div>

    <!-- Success or Error Message -->
    <?php if ($success): ?>
        <div id="successAlert" class="fixed top-4 right-4 z-50 max-w-xs flex items-center justify-between gap-4 rounded-lg bg-gradient-to-r from-green-400 to-green-600 px-5 py-3 text-white shadow-lg font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span><?= htmlspecialchars($success) ?></span>
            <button onclick="closeSuccessAlert()" class="text-white hover:text-green-200 font-bold text-xl leading-none">&times;</button>
        </div>
        <script>
            function closeSuccessAlert() {
                const alert = document.getElementById('successAlert');
                if(alert) alert.remove();
            }
            setTimeout(() => { closeSuccessAlert(); }, 5000);
        </script>
    <?php elseif ($error): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Widgets Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">

        <!-- Document Approvals -->
        <div class="bg-gradient-to-tr from-white via-green-50 to-green-100 border-l-4 border-green-500 shadow-md rounded-3xl p-6 hover:shadow-xl transition-shadow duration-300">
            <h2 class="text-lg font-semibold text-green-700 mb-2">📂 Document Approvals</h2>
            <p class="text-sm text-gray-700">Review and approve submitted documents from residents.</p>
            <a href="document_approvals.php" class="mt-4 inline-block bg-secondary text-white py-2 px-4 rounded hover:bg-green-700 transition">Go to Approvals</a>
        </div>

        <!-- Announcements -->
        <div class="bg-gradient-to-tr from-white via-yellow-50 to-yellow-100 border-l-4 border-yellow-500 shadow-md rounded-3xl p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold text-yellow-600">📢 Announcements</h2>
                <div class="flex gap-2">
                    <button onclick="toggleModal()" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition text-sm">+ Create</button>
                    <button onclick="toggleViewModal()" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 transition text-sm">See All</button>
                </div>
            </div>
            <p class="text-sm text-gray-700">Create and manage barangay announcements and alerts.</p>
        </div>

    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Document Approval Status -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-green-700 mb-4">📄 Document Approval Status</h3>
            <canvas id="docApprovalChart"></canvas>
        </div>

        <!-- Population by Age Group -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-purple-700 mb-4">👨‍👩‍👧‍👦 Population by Age Group</h3>
            <canvas id="populationChart"></canvas>
        </div>

        <!-- Residents vs Officials -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-blue-700 mb-4">👥 Residents & Officials</h3>
            <canvas id="signupChart"></canvas>
        </div>

    </div>

</div>

<!-- Create Announcement Modal -->
<div id="announcementModal" class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white/90 backdrop-blur-sm p-6 rounded-xl shadow-2xl w-full max-w-md">
        <h3 class="text-xl font-semibold text-yellow-600 mb-4">Create Announcement</h3>
        <form method="POST" action="">
            <input type="text" name="title" required placeholder="Title" class="w-full border border-yellow-300 p-2 rounded mb-3 focus:ring focus:ring-yellow-200" />
            <textarea name="description" required placeholder="Message" rows="4" class="w-full border border-yellow-300 p-2 rounded mb-4 focus:ring focus:ring-yellow-200"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleModal()" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" name="submit_announcement" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- View Announcements Modal -->
<div id="viewAnnouncementsModal" class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white/90 backdrop-blur-sm p-6 rounded-xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-y-auto">
        <h3 class="text-xl font-semibold text-yellow-600 mb-4">Recent Announcements</h3>
        <?php if ($announcementResult->num_rows > 0): ?>
            <ul class="space-y-4">
                <?php while($announcement = $announcementResult->fetch_assoc()): ?>
                    <li class="border border-yellow-200 rounded p-4 relative group">
                        <h4 class="font-bold text-lg text-yellow-700"><?= htmlspecialchars($announcement['title']) ?></h4>
                        <p class="text-gray-700 mt-1 whitespace-pre-wrap"><?= htmlspecialchars($announcement['description']) ?></p>
                        <small class="text-gray-500 block mt-2"><?= date('F j, Y h:i A', strtotime($announcement['created_at'])) ?></small>
                        <!-- Delete Button -->
                        <button onclick="confirmDelete(<?= $announcement['id'] ?>)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-sm opacity-0 group-hover:opacity-100 transition">×</button>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600">No announcements available.</p>
        <?php endif; ?>
        <div class="flex justify-end mt-6">
            <button onclick="toggleViewModal()" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">Close</button>
        </div>
    </div>
</div>

<script>
    // Chart 1 - Document Approval Status
    const docApprovalCtx = document.getElementById('docApprovalChart').getContext('2d');
    new Chart(docApprovalCtx, {
        type: 'doughnut',
        data: {
    labels: ['Approved', 'Pending', 'Rejected'],
    datasets: [{
        label: 'Documents',
        data: [
            <?= $approved ?>,
            <?= $pending ?>,
            <?= $rejected ?>
        ],
        backgroundColor: ['#10b981', '#3b82f6', '#ef4444'],
        borderColor: ['#10b981', '#3b82f6', '#ef4444'],
        borderWidth: 1
    }]
},

        options: {
            responsive: true,
        }
    });

    // Chart 2 - Population by Age Group
    const populationCtx = document.getElementById('populationChart').getContext('2d');
    new Chart(populationCtx, {
        type: 'bar',
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
                backgroundColor: ['#a78bfa', '#818cf8', '#7c3aed', '#5b21b6']
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Chart 3 - Residents vs Officials
    const signupCtx = document.getElementById('signupChart').getContext('2d');
    new Chart(signupCtx, {
        type: 'pie',
        data: {
            labels: ['Residents', 'Officials'],
            datasets: [{
                label: 'Count',
                data: [<?= $residents_count ?>, <?= $officials_count ?>],
                backgroundColor: ['#3b82f6', '#6366f1']
            }]
        },
        options: {
            responsive: true
        }
    });

    // Modal Functions
    function toggleModal() {
        const modal = document.getElementById('announcementModal');
        modal.classList.toggle('hidden');
    }

    function toggleViewModal() {
        const modal = document.getElementById('viewAnnouncementsModal');
        modal.classList.toggle('hidden');
    }

    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this announcement?")) {
            window.location.href = 'delete_announcement.php?id=' + id;
        }
    }

    // Remove ?success=1 from URL after reload
    if (window.location.search.includes('success=1')) {
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url.toString());
    }
</script>

</body>
</html>