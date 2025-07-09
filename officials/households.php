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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $house_number = $conn->real_escape_string($_POST['house_number']);
    $head_of_family = $conn->real_escape_string($_POST['head_of_family']);
    $members_input = isset($_POST['members']) ? trim($_POST['members']) : '';
    
    // Convert comma-separated string into JSON array
    $members = [];
    if (!empty($members_input)) {
        $member_list = explode(',', $members_input);
        foreach ($member_list as $member) {
            $members[] = trim($conn->real_escape_string($member));
        }
    }

    $stmt = $conn->prepare("INSERT INTO households (house_number, head_of_family, members) VALUES (?, ?, ?)");
    $json_members = json_encode($members);
    $stmt->bind_param("sss", $house_number, $head_of_family, $json_members);

    if ($stmt->execute()) {
        echo "<script>alert('Household added successfully');</script>";
    } else {
        echo "<script>alert('Error adding household');</script>";
    }
    $stmt->close();
}

// Fetch all households
$households = [];
$result = $conn->query("SELECT * FROM households ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $row['members'] = json_decode($row['members'], true); // Decode JSON to array
    $households[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Barangay Officials Dashboard</title>
    <script src="https://cdn.tailwindcss.com "></script>
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
        <h1 class="text-3xl font-bold text-white">Households Management</h1>
        <div class="relative">
            <button class="relative focus:outline-none">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2 2 0 0118 14V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C8.67 6.165 8 7.388 8 9v5c0 .386-.149.735-.405 1.001L6 17h5m4 0v1a2 2 0 11-4 0v-1m4 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="absolute top-0 right-0 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
        </div>
    </div>

    <!-- Add Household Form -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold mb-4">Add New Household</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">House Number</label>
                <input type="text" name="house_number" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Head of Family</label>
                <input type="text" name="head_of_family" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Family Members (comma-separated)</label>
                <input type="text" name="members" placeholder="e.g., Juan Dela Cruz, Maria Santos" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
            </div>
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-800 transition">
                Add Household
            </button>
        </form>
    </div>

    <!-- List of Households -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">List of Households</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">House #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Head of Family</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added On</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($households as $h): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($h['house_number']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($h['head_of_family']) ?></td>
                        <td class="px-6 py-4">
                            <ul class="list-disc pl-5">
                                <?php if (!empty($h['members'])): ?>
                                    <?php foreach ($h['members'] as $member): ?>
                                        <li><?= htmlspecialchars($member) ?></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="text-gray-500 italic">No members listed</li>
                                <?php endif; ?>
                            </ul>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= date("F j, Y", strtotime($h['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>