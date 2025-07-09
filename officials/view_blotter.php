<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM blotter_reports WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='text-center'>Blotter report not found.</p>";
    exit();
}

$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Blotter Report Details</title>
  <script src="https://cdn.tailwindcss.com "></script>
</head>
<body class="bg-gray-100 p-8">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Blotter Report Details</h2>

    <div class="space-y-4">
      <p><strong>Complainant:</strong> <?= htmlspecialchars($row['complainant_name']) ?></p>
      <p><strong>Accused:</strong> <?= htmlspecialchars($row['accused_name']) ?></p>
      <p><strong>Uri ng Reklamo:</strong> <?= htmlspecialchars($row['complaint_type']) ?></p>
      <p><strong>Petsa ng Insidente:</strong> <?= htmlspecialchars($row['incident_date']) ?></p>
      <p><strong>Oras ng Insidente:</strong> <?= htmlspecialchars($row['incident_time']) ?></p>
      <p><strong>Lugar:</strong> <?= htmlspecialchars($row['incident_location']) ?></p>
      <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
      <p><strong>Salaysay:</strong></p>
      <p class="whitespace-pre-wrap"><?= htmlspecialchars($row['incident_details']) ?></p>
    </div>

    <div class="mt-6">
      <a href="blotters.php" class="text-blue-600 hover:underline">&laquo; Back to Reports</a>
    </div>
  </div>
</body>
</html>