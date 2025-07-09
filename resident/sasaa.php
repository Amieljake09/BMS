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

// Fetch resident name
$user_id = $_SESSION['user_id'];
$full_name = "";
$sql = "SELECT full_name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name);
$stmt->fetch();
$stmt->close();

// Fetch document requests for logged-in user
$requests = [];
$sql_req = "SELECT id, document_type, purpose, status, request_date FROM document_requests WHERE resident_id = ? ORDER BY request_date DESC";
$stmt_req = $conn->prepare($sql_req);
$stmt_req->bind_param("i", $user_id);
$stmt_req->execute();
$result_req = $stmt_req->get_result();
while ($row = $result_req->fetch_assoc()) {
    $requests[] = $row;
}
$stmt_req->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Resident Request Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
      top: 0; left: 0; height: 100vh; width: 16rem; overflow-y: auto; z-index: 50;
    }
    #main-content {
      margin-left: 16rem; height: 100vh; overflow-y: auto;
    }
    .step-indicator {
      width: 2.5rem; height: 2.5rem; border-radius: 9999px;
      background-color: #d1d5db; color: white; display: flex;
      align-items: center; justify-content: center; font-weight: bold;
    }
    .step-indicator.active { background-color: #1e3a8a; }
    .animate-fade-in { animation: fadeIn 0.4s ease-in-out; }
    @keyframes fadeIn { from {opacity: 0;} to {opacity: 1;} }

    /* Smooth scrollbars */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
  background: #555;
}

  </style>
</head>
<body class="bg-gradient-to-br from-blue-700 via-white to-blue-500">

  <div id="sidebar"><?php include 'sidebar.php'; ?></div>

  <div id="main-content" class="p-8">

    <div class="flex gap-8 max-w-7xl mx-auto">
      <!-- Form Container -->
       <div class="flex-1 max-w-3xl h-[400px] w-full bg-white p-4 rounded-xl shadow-lg overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6 text-primary">Request a Barangay Document</h2>
        
        <div class="flex items-center justify-between mb-6">
          <div class="flex gap-4">
            <div class="step-indicator active">1</div>
            <div class="step-indicator">2</div>
          </div>
        </div>

        <form id="requestForm" method="POST" action="submit_request.php">
          <!-- Include resident details -->
          <input type="hidden" name="resident_id" value="<?= htmlspecialchars($user_id) ?>">
          <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Resident Name</label>
            <input type="text" name="resident_name" value="<?= htmlspecialchars($full_name) ?>" readonly class="w-full border-gray-300 rounded-lg p-2 bg-gray-100 cursor-not-allowed" />
          </div>

          <!-- Step 1 -->
          <div class="step animate-fade-in">
            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-2">Document Type</label>
              <select id="documentType" name="document_type" required class="w-full border-gray-300 rounded-lg p-2">
                <option value="">-- Select Document --</option>
                <option value="Clearance">Barangay Clearance</option>
                <option value="Business Permit">Business Permit</option>
                <option value="Residency">Certificate of Residency</option>
                <option value="Cedula">Cedula</option>
                <option value="Indigency">Certificate of Indigency</option>
                <option value="Solo Parent">Solo Parent Certificate</option>
                <option value="Others">Others</option>
              </select>
            </div>

            <!-- Business Permit Fields -->
            <div id="businessFields" class="hidden">
              <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Business Name</label>
                <input type="text" name="business_name" class="w-full border-gray-300 rounded-lg p-2" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Business Address</label>
                <input type="text" name="business_address" class="w-full border-gray-300 rounded-lg p-2" />
              </div>
            </div>

            <!-- Cedula Fields -->
            <div id="cedulaFields" class="hidden">
              <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">TIN Number</label>
                <input type="text" name="tin_number" class="w-full border-gray-300 rounded-lg p-2" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Profession</label>
                <input type="text" name="profession" class="w-full border-gray-300 rounded-lg p-2" />
              </div>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="step hidden animate-fade-in">
            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-2">Purpose</label>
              <textarea name="purpose" required class="w-full border-gray-300 rounded-lg p-2 h-28"></textarea>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-2">Additional Notes (optional)</label>
              <textarea name="notes" class="w-full border-gray-300 rounded-lg p-2 h-20"></textarea>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex justify-between mt-6">
            <button type="button" id="prevBtn" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded hidden">Back</button>
            <button type="button" id="nextBtn" class="bg-primary hover:bg-blue-900 text-white font-bold py-2 px-6 rounded">Next</button>
          </div>
        </form>
      </div>

      <!-- Requests Table -->
       <div class="flex-1 max-w-3xl h-[400px] w-full bg-white p-4 rounded-xl shadow-lg overflow-y-auto overflow-x-auto">
        <h2 class="text-2xl font-bold mb-6 text-primary">My Document Requests</h2>
        <?php if (count($requests) === 0): ?>
          <p class="text-gray-600">No document requests found.</p>
        <?php else: ?>
          <table class="min-w-full border border-gray-300">
            <thead class="bg-primary text-white">
              <tr>
                <th class="p-3 border border-gray-300 text-left">Document Type</th>
                <th class="p-3 border border-gray-300 text-left">Purpose</th>
                <th class="p-3 border border-gray-300 text-left">Status</th>
                <th class="p-3 border border-gray-300 text-left">Request Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($requests as $req): ?>
                <tr class="even:bg-gray-100">
                  <td class="p-3 border border-gray-300"><?= htmlspecialchars($req['document_type']) ?></td>
                  <td class="p-3 border border-gray-300"><?= htmlspecialchars($req['purpose']) ?></td>
                  <td class="p-3 border border-gray-300"><?= htmlspecialchars($req['status']) ?></td>
                  <td class="p-3 border border-gray-300"><?= htmlspecialchars(date("F j, Y", strtotime($req['request_date']))) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <script>
    const steps = document.querySelectorAll('.step');
    const indicators = document.querySelectorAll('.step-indicator');
    const businessFields = document.getElementById('businessFields');
    const cedulaFields = document.getElementById('cedulaFields');
    const documentType = document.getElementById('documentType');
    let currentStep = 0;

    function showStep(index) {
      steps.forEach((step, i) => {
        step.classList.toggle('hidden', i !== index);
        indicators[i].classList.toggle('active', i === index);
      });
      document.getElementById('prevBtn').classList.toggle('hidden', index === 0);
      document.getElementById('nextBtn').textContent = index === steps.length - 1 ? 'Submit' : 'Next';
    }

    document.getElementById('nextBtn').addEventListener('click', () => {
      const currentFields = steps[currentStep].querySelectorAll('input, select, textarea');
      const valid = Array.from(currentFields).every(field => {
        if (!field.closest('.hidden')) return field.checkValidity();
        return true;
      });
      if (!valid) {
        currentFields.forEach(field => {
          if (!field.closest('.hidden')) field.reportValidity();
        });
        return;
      }

      if (currentStep === steps.length - 1) {
        document.getElementById('requestForm').submit();
        return;
      }
      currentStep++;
      showStep(currentStep);
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
      if (currentStep > 0) {
        currentStep--;
        showStep(currentStep);
      }
    });

    documentType.addEventListener('change', () => {
      businessFields.classList.toggle('hidden', documentType.value !== 'Business Permit');
      cedulaFields.classList.toggle('hidden', documentType.value !== 'Cedula');
    });

    // Initialize
    showStep(currentStep);
  </script>
</body>
</html>
