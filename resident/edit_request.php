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

// Get request ID from GET or POST
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
if ($request_id <= 0) {
    header("Location: request_form.php");
    exit();
}

// Fetch the existing request for this user
$sql = "SELECT document_type, purpose, notes FROM document_requests WHERE id = ? AND resident_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // No such request or not owned by user
    $stmt->close();
    header("Location: request_form.php");
    exit();
}

$request = $result->fetch_assoc();
$stmt->close();

// If form submitted, update the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $document_type = $_POST['document_type'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $notes = $_POST['notes'] ?? '';

    // Basic validation
    if ($document_type && $purpose) {
        $update_sql = "UPDATE document_requests SET document_type = ?, purpose = ?, notes = ? WHERE id = ? AND resident_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssii", $document_type, $purpose, $notes, $request_id, $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: request_form.php");
            exit();
        } else {
            $error = "Failed to update request.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Document Request</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-800 via-white to-blue-600 min-h-screen p-8">
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-primary">Edit Document Request</h1>

    <?php if (!empty($error)): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_request.php?id=<?= $request_id ?>">
      <input type="hidden" name="id" value="<?= $request_id ?>">

      <label class="block mb-2 font-semibold text-gray-700">Document Type</label>
      <select name="document_type" required class="w-full border border-blue-300 rounded-lg p-3 mb-4">
        <?php
          $docTypes = ["Clearance" => "Barangay Clearance", "Business Permit" => "Business Permit", "Residency" => "Certificate of Residency", "Cedula" => "Cedula", "Indigency" => "Certificate of Indigency", "Solo Parent" => "Solo Parent Certificate", "Others" => "Others"];
          foreach ($docTypes as $value => $label) {
              $selected = ($request['document_type'] === $value) ? "selected" : "";
              echo "<option value=\"" . htmlspecialchars($value) . "\" $selected>" . htmlspecialchars($label) . "</option>";
          }
        ?>
      </select>

      <label class="block mb-2 font-semibold text-gray-700">Purpose</label>
      <textarea name="purpose" required class="w-full border border-blue-300 rounded-lg p-3 mb-4"><?= htmlspecialchars($request['purpose']) ?></textarea>

      <label class="block mb-2 font-semibold text-gray-700">Additional Notes (optional)</label>
      <textarea name="notes" class="w-full border border-blue-300 rounded-lg p-3 mb-6"><?= htmlspecialchars($request['notes']) ?></textarea>

      <div class="flex justify-between">
        <a href="request_form.php" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded">Cancel</a>
        <button type="submit" class="bg-primary hover:bg-blue-900 text-white font-bold py-2 px-6 rounded">Update</button>
      </div>
    </form>
  </div>
</body>
</html>
