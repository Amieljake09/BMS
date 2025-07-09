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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'], $_POST['status'])) {
    $report_id = intval($_POST['report_id']);
    $status = $conn->real_escape_string($_POST['status']);

    $update_sql = "UPDATE blotter_reports SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $status, $report_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>window.location.href='blotters.php';</script>";
}
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
    <h1 class="text-3xl font-bold text-white">Blotters & Reports</h1>
    <div class="relative">
      <button class="relative focus:outline-none">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M15 17h5l-1.405-1.405A2 2 0 0118 14V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C8.67 6.165 8 7.388 8 9v5c0 .386-.149.735-.405 1.001L6 17h5m4 0v1a2 2 0 11-4 0v-1m4 0H9"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="absolute top-0 right-0 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
      </button>
    </div>
  </div>

  <!-- Blotter Reports Table -->
  <div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold mb-4">Submitted Blotter Reports</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto border-collapse">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Complainant</th>
            <th class="px-4 py-2 text-left">Reklamo</th>
            <th class="px-4 py-2 text-left">Petsa</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM blotter_reports ORDER BY date_reported DESC";
          $result = $conn->query($sql);

          if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
              // Shorten the incident_details for preview
              $preview = strlen($row['incident_details']) > 100 ? substr($row['incident_details'], 0, 100) . "..." : $row['incident_details'];
          ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2"><?= htmlspecialchars($row['complainant_name']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($preview) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars(date("F j, Y", strtotime($row['date_reported']))) ?></td>
              <td class="px-4 py-2">
                <form method="post" class="inline">
                  <input type="hidden" name="report_id" value="<?= $row['id'] ?>">
                  <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                    <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Ongoing" <?= $row['status'] === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                    <option value="Resolved" <?= $row['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                  </select>
                </form>
              </td>
              <td class="px-4 py-2">
                <a href="view_blotter.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">View Details</a>
              </td>
            </tr>
          <?php endwhile; else: ?>
            <tr>
              <td colspan="5" class="px-4 py-4 text-center text-gray-500">No blotter reports found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

</body>
</html>