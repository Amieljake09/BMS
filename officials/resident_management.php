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

// Fetch all residents from users and residents table
$sql = "
    SELECT u.full_name, u.email, r.dob, r.pob, r.age, r.gender, r.civil_status,
           r.nationality, r.religion, r.address, r.phone, r.res_email,
           r.resident_type, r.stay_length, r.date_registered, r.employment_status
    FROM users u
    JOIN residents r ON u.id = r.user_id
    WHERE u.role = 'Resident'
";

$result = $conn->query($sql);
$residents = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['age'] = date_diff(date_create($row['dob']), date_create('today'))->y;
        $residents[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Barangay Resident Management</title>
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
        <h1 class="text-3xl font-bold text-white">Resident Management</h1>
    </div>

    <!-- Residents Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-700">List of Residents</h2>
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by name or email..." class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div class="overflow-x-auto">
            <table id="residentsTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">POB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Civil Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nationality</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Religion</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Res Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stay Length</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Registered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($residents as $resident): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['full_name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['email']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['dob']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['pob']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['age']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['gender']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['civil_status']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['nationality']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['religion']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['address']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['phone']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['res_email']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['resident_type']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['stay_length']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['date_registered']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($resident['employment_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($residents)): ?>
                        <tr>
                            <td colspan="16" class="px-6 py-4 text-center text-gray-500">No residents found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterTable() {
    var input = document.getElementById("searchInput");
    var filter = input.value.toLowerCase();
    var table = document.getElementById("residentsTable");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) { // Skip header row
        var tdName = tr[i].getElementsByTagName("td")[0];
        var tdEmail = tr[i].getElementsByTagName("td")[1];

        if (tdName || tdEmail) {
            var txtValueName = tdName.textContent || tdName.innerText;
            var txtValueEmail = tdEmail.textContent || tdEmail.innerText;

            if (txtValueName.toLowerCase().indexOf(filter) > -1 ||
                txtValueEmail.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>

</body>
</html>