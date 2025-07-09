<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



?>

<?php
$alertMessage = '';

if (isset($_GET['approved'])) {
    $alertMessage = "Account approved successfully!";
} elseif (isset($_GET['declined'])) {
    $alertMessage = "Account declined and deleted successfully!";
}

if ($alertMessage !== '') {
    echo "<script>
            alert('$alertMessage');
            // Redirect to remove query parameters
            window.location.href = window.location.pathname;
          </script>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin page</title>
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
    <?php include 'admin_sidebar.php'; ?>
  </div>

  <!-- Main Content -->
  <div id="main-content" class="p-8">
    <!-- Header -->
             <h1 class="text-3xl font-bold text-white">Account Approval</h1><br><br>


      <!-- Pending Accounts Section -->
<div class="bg-white shadow-md rounded-lg p-4 mb-8">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Pending Account Requests</h2>
    
    <?php
    $pending_sql = "SELECT u.id, u.full_name, u.email, u.role, r.proof 
FROM users u
LEFT JOIN residents r ON u.id = r.user_id
WHERE u.status = 'pending'";
    $pending_result = $conn->query($pending_sql);

    if ($pending_result->num_rows > 0): ?>
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Role</th>
                    <th class="px-4 py-2 text-left">Proof of Residency</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $pending_result->fetch_assoc()): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?= htmlspecialchars($user['full_name']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($user['role']) ?></td>

                        <td class="px-4 py-2">
    <?php if (!empty($user['proof'])): ?>
    <a href="../login-system/<?= htmlspecialchars($user['proof']) ?>" 
       target="_blank" 
       class="text-blue-500 hover:underline">
       View / Download
    </a>
<?php else: ?>
    <span class="text-gray-400">No file uploaded</span>
<?php endif; ?>
</td>
                        <td class="px-4 py-2 flex justify-center space-x-2">
                           <button onclick="confirmAction('approve_user.php?id=<?= $user['id'] ?>', 'Are you sure you want to approve this account?')"
                           class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Approve</button>

                           <button onclick="confirmAction('decline_user.php?id=<?= $user['id'] ?>', 'Are you sure you want to decline this account?')"
                           class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Decline</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-500">No pending account requests.</p>
    <?php endif; ?>
</div>
    </div>  


    <!-- Approval Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 id="modalMessage" class="text-lg font-semibold mb-4 text-gray-800">Are you sure?</h2>
        <div class="flex justify-end space-x-4">
            <button id="confirmActionBtn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Yes</button>
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">No</button>

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

  <script>
let actionUrl = '';

function confirmAction(url, message) {
    document.getElementById('modalMessage').textContent = message;
    actionUrl = url;

    // Set the Yes button behavior
    const confirmBtn = document.getElementById('confirmActionBtn');
    confirmBtn.onclick = function () {
        window.location.href = actionUrl;
    };

    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}
</script>

</body>
</html>
