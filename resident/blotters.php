<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Kunin ang full name mula sa users table
$sql = "SELECT full_name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($complainant_name);
$stmt->fetch();
$stmt->close();

$complainant_address = ""; // default empty
$complainant_contact = ""; // default empty

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accused_name = $conn->real_escape_string($_POST['accused_name']);
    $accused_address = $conn->real_escape_string($_POST['accused_address']);
    $accused_contact = $conn->real_escape_string($_POST['accused_contact']);
    $complaint_type = $conn->real_escape_string($_POST['complaint_type']);
    $incident_date = $conn->real_escape_string($_POST['incident_date']);
    $incident_time = $conn->real_escape_string($_POST['incident_time']);
    $incident_location = $conn->real_escape_string($_POST['incident_location']);
    $incident_details = $conn->real_escape_string($_POST['incident_details']);

    $date_reported = date('Y-m-d H:i:s');

    $sql = "INSERT INTO blotter_reports (
        residents_id, complainant_name, complainant_address, complainant_contact,
        accused_name, accused_address, accused_contact,
        complaint_type, incident_date, incident_time, incident_location,
        incident_details, status, date_reported
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssssssssss",
        $user_id, $complainant_name, $complainant_address, $complainant_contact,
        $accused_name, $accused_address, $accused_contact,
        $complaint_type, $incident_date, $incident_time, $incident_location,
        $incident_details, $date_reported
    );

    if ($stmt->execute()) {
        // ✅ Redirect to prevent resubmission on refresh
        $_SESSION['flash_message'] = "Blotter report successfully submitted!";
        header("Location: blotters.php");
        exit();
    } else {
        $_SESSION['flash_error'] = "Error submitting report: " . $conn->error;
        header("Location: blotters.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Resident Dashboard</title>
  <script src="https://cdn.tailwindcss.com "></script>
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
    #sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 16rem; overflow-y: auto; z-index: 50; }
    #main-content { margin-left: 16rem; height: 100vh; overflow-y: auto; }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-700 via-white to-blue-500">

<!-- Flash Messages -->
<?php if (isset($_SESSION['flash_message'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        alert("<?= addslashes($_SESSION['flash_message']) ?>");
    });
</script>
<?php unset($_SESSION['flash_message']); endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        alert("<?= addslashes($_SESSION['flash_error']) ?>");
    });
</script>
<?php unset($_SESSION['flash_error']); endif; ?>


<!-- Sidebar -->
<div id="sidebar">
  <?php include 'sidebar.php'; ?>
</div>

<!-- Main Content -->
<div id="main-content" class="p-8">
  <!-- Header -->
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-white">Blotter & Reports</h1>
    <div class="relative">
      <button class="relative focus:outline-none">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C8.67 6.165 8 7.388 8 9v5c0 .386-.149.735-.405 1.001L6 17h5m4 0v1a2 2 0 11-4 0v-1m4 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="absolute top-0 right-0 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
      </button>
    </div>
  </div>

  <!-- Blotter Report Form -->
  <div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-xl font-semibold mb-4">Submit Blotter Report</h2>
    <form method="post">
      <!-- Part A: Complainant Info -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label for="complainant_name" class="block text-gray-700 font-medium mb-2">A.1 Complainant Name</label>
          <input type="text" name="complainant_name" id="complainant_name"
                 value="<?= htmlspecialchars($complainant_name); ?>"
                 class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" readonly>
        </div>
      </div>

      <!-- Part B: Accused Info -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label for="accused_name" class="block text-gray-700 font-medium mb-2">B.1 Accused Name</label>
          <input type="text" name="accused_name" id="accused_name" required
                 class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label for="accused_address" class="block text-gray-700 font-medium mb-2">B.2 Address</label>
          <input type="text" name="accused_address" id="accused_address" required
                 class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label for="accused_contact" class="block text-gray-700 font-medium mb-2">B.3 Contact / Social Media (Optional)</label>
          <input type="text" name="accused_contact" id="accused_contact"
                 class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>

      <!-- Part C: Complaint Type -->
      <div class="mb-4">
        <label for="complaint_type" class="block text-gray-700 font-medium mb-2">C. Reklamo / Uri ng Suliranin</label>
        <input type="text" name="complaint_type" id="complaint_type" required
               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
      </div>

      <!-- Part D: Incident Details -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
          <label for="incident_date" class="block text-gray-700 font-medium mb-2">D.1 Petsa</label>
          <input type="date" name="incident_date" id="incident_date" required
                 class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label for="incident_time" class="block text-gray-700 font-medium mb-2">D.2 Oras</label>
          <input type="time" name="incident_time" id="incident_time" required
                 class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label for="incident_location" class="block text-gray-700 font-medium mb-2">D.3 Lugar</label>
          <input type="text" name="incident_location" id="incident_location" required
                 class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
      </div>

      <!-- Part E: Narrative -->
      <div class="mb-4">
        <label for="incident_details" class="block text-gray-700 font-medium mb-2">E. Salaysay ng Pangyayari</label>
        <textarea name="incident_details" id="incident_details" rows="5"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                  required></textarea>
      </div>

      <button type="submit"
              class="px-4 py-2 bg-primary text-white rounded hover:bg-blue-800 transition duration-200">
        Isumite ang Reklamo
      </button>
    </form>
  </div>

  <!-- Table of Submitted Reports -->
  <div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold mb-4">Your Submitted Reports</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Report Date</th>
            <th class="px-4 py-2 text-left">Details</th>
            <th class="px-4 py-2 text-left">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM blotter_reports WHERE residents_id = '$user_id' ORDER BY date_reported DESC";
          $result = $conn->query($sql);

          if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
          ?>
            <tr class="border-t">
              <td class="px-4 py-2"><?= htmlspecialchars($row['date_reported']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars(substr($row['incident_details'], 0, 50)) ?>...</td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['status']) ?></td>
            </tr>
          <?php endwhile; else: ?>
            <tr><td colspan="3" class="px-4 py-2 text-center text-gray-500">No reports found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>