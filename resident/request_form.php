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
$full_name = "";
$sql = "SELECT full_name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name);
$stmt->fetch();
$stmt->close();

$requests = [];
$sql_req = "SELECT id, document_type, purpose, status, request_date, business_name, business_address, tin_number, profession, dob, pob, citizenship, educ_attainment, course_graduated FROM document_requests WHERE resident_id = ? ORDER BY request_date DESC";
$stmt_req = $conn->prepare($sql_req);
$stmt_req->bind_param("i", $user_id);
$stmt_req->execute();
$result_req = $stmt_req->get_result();
while ($row = $result_req->fetch_assoc()) {
    $requests[] = $row;
}

// Average Processing Time
$avgProcessingTime = 0;
$processingResult = $conn->query("
    SELECT 
        AVG(DATEDIFF(NOW(), request_date)) AS avg_days
    FROM document_requests
    WHERE status = 'Approved'
");
if ($processingRow = $processingResult->fetch_assoc()) {
    $avgProcessingTime = round($processingRow['avg_days'] ?? 0, 2);
}
$stmt_req->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Resident Request Form</title>
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
  <style>
    @keyframes fadeSlide {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-slide {
      animation: fadeSlide 0.3s ease-out forwards;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-800 via-white to-blue-600 min-h-screen">
  <div id="sidebar"><?php include 'sidebar.php'; ?></div>
  <div id="main-content" class="ml-64 p-8">
    <h1 class="text-2xl font-bold mb-6">Request Documents</h1>
    <div class="flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto">
      <!-- Request Form -->
      <div class="flex-1 max-w-3xl h-[430px] w-full bg-white p-4 rounded-xl shadow-lg overflow-y-auto">
        <h2 class="text-2xl font-bold text-primary mb-8">Request a Barangay Document</h2>
        <!-- Step Indicators -->
        <div class="flex items-center mb-8 justify-center gap-4">
          <div id="stepCircle1" class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold">1</div>
          <div class="h-1 w-12 bg-primary"></div>
          <div id="stepCircle2" class="w-10 h-10 rounded-full bg-gray-300 text-white flex items-center justify-center font-bold">2</div>
        </div>
        <form id="requestForm" method="POST" action="submit_request.php" novalidate>
          <input type="hidden" name="resident_id" value="<?= htmlspecialchars($user_id) ?>">
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Resident Name</label>
            <input type="text" name="resident_name" value="<?= htmlspecialchars($full_name) ?>" readonly class="w-full border border-blue-300 rounded-lg p-3 bg-gray-100 text-blue-800 font-medium" />
          </div>
          <!-- Step 1 -->
          <div class="step">
            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Document Type</label>
              <select id="documentType" name="document_type" required class="w-full border border-blue-300 rounded-lg p-3">
                <option value="">-- Select Document --</option>
                <option value="Clearance">Barangay Clearance</option>
                <option value="Business Permit">Business Permit</option>
                <option value="Residency">Certificate of Residency</option>
                <option value="Cedula">Cedula</option>
                <option value="Indigency">Certificate of Indigency</option>
                <option value="Solo Parent">Solo Parent Certificate</option>
                <option value="Barangay ID">Barangay ID</option>
                <option value="First Time Job Seeker">First Time Job Seeker Certificate</option>
                <option value="Others">Others</option>
              </select>
            </div>

            <!-- Existing Fields -->
            <div id="businessFields" class="hidden">
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Business Name</label>
                <input type="text" name="business_name" class="w-full border border-blue-300 rounded-lg p-3" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Business Address</label>
                <input type="text" name="business_address" class="w-full border border-blue-300 rounded-lg p-3" />
              </div>
            </div>
            <div id="cedulaFields" class="hidden">
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">TIN Number</label>
                <input type="text" name="tin_number" class="w-full border border-blue-300 rounded-lg p-3" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Profession</label>
                <input type="text" name="profession" class="w-full border border-blue-300 rounded-lg p-3" />
              </div>
            </div>

            <!-- NEW FIELDS -->
            <div id="barangayIdFields" class="hidden">
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Date of Birth</label>
                <input type="date" name="dob" class="w-full border border-blue-300 rounded-lg p-3" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Place of Birth</label>
                <input type="text" name="pob" class="w-full border border-blue-300 rounded-lg p-3" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Citizenship</label>
                <input type="text" name="citizenship" class="w-full border border-blue-300 rounded-lg p-3" />
              </div>
            </div>

            <div id="jobSeekerFields" class="hidden">
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Educational Attainment</label>
                <input type="text" name="educ_attainment" class="w-full border border-blue-300 rounded-lg p-3" />
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Course Graduated</label>
                <input type="text" name="course_graduated" class="w-full border border-blue-300 rounded-lg p-3" />
              </div>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="step hidden">
            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Purpose</label>
              <textarea name="purpose" required class="w-full border border-blue-300 rounded-lg p-3 h-28"></textarea>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Additional Notes (optional)</label>
              <textarea name="notes" class="w-full border border-blue-300 rounded-lg p-3 h-20"></textarea>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-between mt-6">
            <button type="button" id="prevBtn" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded hidden">Back</button>
            <button type="button" id="nextBtn" class="bg-primary hover:bg-blue-900 text-white font-bold py-2 px-6 rounded">Next</button>
          </div>
        </form>
      </div>

      <!-- Requests Table -->
      <div class="flex-1 max-w-3xl h-[430px] w-full bg-white p-4 rounded-xl shadow-lg overflow-y-auto">
        <h2 class="text-2xl font-bold text-primary mb-6">My Document Requests</h2>
        <?php if (count($requests) === 0): ?>
          <p class="text-gray-600">No document requests found.</p>
        <?php else: ?>
          <div class="overflow-auto">
            <table class="min-w-full border border-gray-300 text-sm">
              <thead class="bg-primary text-white">
                <tr>
                  <th class="p-3 text-left">Document Type</th>
                  <th class="p-3 text-left">Purpose</th>
                  <th class="p-3 text-left">Status</th>
                  <th class="p-3 text-left">Request Date</th>
                  <th class="p-3 text-left">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($requests as $req): ?>
                  <tr class="even:bg-gray-100">
                    <td class="p-3"><?= htmlspecialchars($req['document_type']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($req['purpose']) ?></td>
                    <td class="p-3">
                      <?php
                        $status = htmlspecialchars($req['status']);
                        $color = match($status) {
                          'Pending' => 'bg-yellow-400 text-yellow-800',
                          'Approved' => 'bg-green-400 text-green-800',
                          'Rejected' => 'bg-red-400 text-red-800',
                          default => 'bg-gray-200 text-gray-800',
                        };
                      ?>
                      <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>"><?= $status ?></span>
                    </td>
                    <td class="p-3">
                      <?= htmlspecialchars(date("F j, Y", strtotime($req['request_date']))) ?>
                    </td>
                    <td class="p-3">
                      <?php if ($req['status'] === 'Approved'): ?>
                        <a href="view_request.php?id=<?= $req['id'] ?>" target="_blank" class="text-green-600 hover:underline mr-2">View</a>
                      <?php else: ?>
                        <button onclick="openEditModal(
                            <?= $req['id'] ?>,
                            '<?= htmlspecialchars($req['document_type']) ?>',
                            '<?= htmlspecialchars($req['purpose']) ?>',
                            '<?= htmlspecialchars($req['business_name'] ?? '') ?>',
                            '<?= htmlspecialchars($req['business_address'] ?? '') ?>',
                            '<?= htmlspecialchars($req['tin_number'] ?? '') ?>',
                            '<?= htmlspecialchars($req['profession'] ?? '') ?>',
                            '<?= htmlspecialchars($req['dob'] ?? '') ?>',
                            '<?= htmlspecialchars($req['pob'] ?? '') ?>',
                            '<?= htmlspecialchars($req['citizenship'] ?? '') ?>',
                            '<?= htmlspecialchars($req['educ_attainment'] ?? '') ?>',
                            '<?= htmlspecialchars($req['course_graduated'] ?? '') ?>'
                        )" class="text-blue-600 hover:underline mr-2">Edit</button>
                        <a href="cancel_request.php?id=<?= $req['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Cancel this request?')">Cancel</a>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div><br>

    <!-- Single Column Below -->
    <div class="grid grid-cols-1 gap-6">
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Average Processing Time</h2>
        <p class="text-lg">Average processing time: <strong><?= $avgProcessingTime ?> days</strong></p>
      </div>
    </div>
  </div>

  <!-- Modal for Editing -->
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative animate-fade-in">
      <h3 class="text-xl font-bold text-primary mb-4">Edit Request</h3>
      <form id="editForm" method="POST" action="update_request.php">
        <input type="hidden" name="request_id" id="request_id" />
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Document Type</label>
          <input type="text" id="edit_document_type" class="w-full border border-blue-300 rounded-lg p-3 bg-gray-100" readonly />
          <input type="hidden" name="document_type" id="edit_document_type_input" />
        </div>

        <!-- Business Fields -->
        <div id="edit_businessFields" class="hidden space-y-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Business Name</label>
            <input type="text" name="business_name" id="edit_business_name" class="w-full border border-blue-300 rounded-lg p-3" />
          </div>
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Business Address</label>
            <input type="text" name="business_address" id="edit_business_address" class="w-full border border-blue-300 rounded-lg p-3" />
          </div>
        </div>

        <!-- Cedula Fields -->
        <div id="edit_cedulaFields" class="hidden space-y-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">TIN Number</label>
            <input type="text" name="tin_number" id="edit_tin_number" class="w-full border border-blue-300 rounded-lg p-3" />
          </div>
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Profession</label>
            <input type="text" name="profession" id="edit_profession" class="w-full border border-blue-300 rounded-lg p-3" />
          </div>
        </div>

        <!-- Barangay ID Fields -->
        <div id="edit_barangayIdFields" class="hidden space-y-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Date of Birth</label>
            <input type="date" name="dob" id="edit_dob" class="w-full border border-blue-300 rounded-lg p-3" />
          </div>
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Place of Birth</label>
            <input type="text" name="pob" id="edit_pob" class="w-full border border-blue-300 rounded-lg p-3" />
          </div>
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Citizenship</label>
            <input type="text" name="citizenship" id="edit_citizenship" class="w-full border border-blue-300 rounded-lg p-3" />
          </div>
        </div>

        <!-- Job Seeker Fields -->
        <div id="edit_jobSeekerFields" class="hidden space-y-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Educational Attainment</label>
            <input type="text" name="educ_attainment" id="edit_educ_attainment" class="w-full border border-blue-300 rounded-lg p-3" />
          </div>
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Course Graduated</label>
            <input type="text" name="course_graduated" id="edit_course_graduated" class="w-full border border-blue-300 rounded-lg p-3" />
          </div>
        </div>

        <!-- Purpose -->
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Purpose</label>
          <textarea name="purpose" id="edit_purpose" required class="w-full border border-blue-300 rounded-lg p-3 h-28"></textarea>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button type="button" onclick="closeEditModal()" class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded">Cancel</button>
          <button type="submit" class="bg-primary hover:bg-blue-900 text-white py-2 px-4 rounded">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Success Alert -->
  <div id="editSuccessAlert" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg hidden items-center gap-2 z-50 animate-fade-slide">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span>Edit successful!</span>
  </div>

  <script>
    function openEditModal(id, docType, purpose, businessName = '', businessAddress = '', tinNumber = '', profession = '', dob = '', pob = '', citizenship = '', educAttain = '', courseGrad = '') {
      document.getElementById('request_id').value = id;
      document.getElementById('edit_document_type').value = docType;
      document.getElementById('edit_document_type_input').value = docType;
      document.getElementById('edit_purpose').value = purpose;

      document.getElementById('edit_businessFields').classList.add('hidden');
      document.getElementById('edit_cedulaFields').classList.add('hidden');
      document.getElementById('edit_barangayIdFields').classList.add('hidden');
      document.getElementById('edit_jobSeekerFields').classList.add('hidden');

      if (docType === 'Business Permit') {
        document.getElementById('edit_business_name').value = businessName;
        document.getElementById('edit_business_address').value = businessAddress;
        document.getElementById('edit_businessFields').classList.remove('hidden');
      } else if (docType === 'Cedula') {
        document.getElementById('edit_tin_number').value = tinNumber;
        document.getElementById('edit_profession').value = profession;
        document.getElementById('edit_cedulaFields').classList.remove('hidden');
      } else if (docType === 'Barangay ID') {
        document.getElementById('edit_dob').value = dob;
        document.getElementById('edit_pob').value = pob;
        document.getElementById('edit_citizenship').value = citizenship;
        document.getElementById('edit_barangayIdFields').classList.remove('hidden');
      } else if (docType === 'First Time Job Seeker') {
        document.getElementById('edit_educ_attainment').value = educAttain;
        document.getElementById('edit_course_graduated').value = courseGrad;
        document.getElementById('edit_jobSeekerFields').classList.remove('hidden');
      }

      document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
    }

    window.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('edit_success')) {
        const alertBox = document.getElementById('editSuccessAlert');
        alertBox.classList.remove('hidden');
        setTimeout(() => alertBox.classList.add('hidden'), 3000);
        history.replaceState(null, '', window.location.pathname);
      }
    });

    // FORM STEP NAVIGATION
    const steps = document.querySelectorAll('.step');
    const stepCircle1 = document.getElementById('stepCircle1');
    const stepCircle2 = document.getElementById('stepCircle2');
    const businessFields = document.getElementById('businessFields');
    const cedulaFields = document.getElementById('cedulaFields');
    const barangayIdFields = document.getElementById('barangayIdFields');
    const jobSeekerFields = document.getElementById('jobSeekerFields');
    const documentType = document.getElementById('documentType');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let currentStep = 0;

    function updateStepIndicators() {
      if (currentStep === 0) {
        stepCircle1.classList.add('bg-primary');
        stepCircle1.classList.remove('bg-gray-300');
        stepCircle2.classList.remove('bg-primary');
        stepCircle2.classList.add('bg-gray-300');
      } else {
        stepCircle1.classList.remove('bg-primary');
        stepCircle1.classList.add('bg-gray-300');
        stepCircle2.classList.add('bg-primary');
        stepCircle2.classList.remove('bg-gray-300');
      }
    }

    function showStep(index) {
      steps.forEach((step, i) => step.classList.toggle('hidden', i !== index));
      prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
      nextBtn.textContent = index === steps.length - 1 ? 'Submit' : 'Next';
      updateStepIndicators();
    }

    function validateCurrentStep() {
      const visibleFields = steps[currentStep].querySelectorAll('[required]');
      let valid = true;
      visibleFields.forEach(field => {
        if (!field.closest('.hidden') && !field.checkValidity()) {
          field.reportValidity();
          valid = false;
        }
      });
      return valid;
    }

    nextBtn.addEventListener('click', () => {
      if (!validateCurrentStep()) return;
      if (currentStep === steps.length - 1) {
        document.getElementById('editSuccessAlert').classList.remove('hidden');
        setTimeout(() => document.getElementById('editSuccessAlert').classList.add('hidden'), 3000);
        document.getElementById('requestForm').submit();
        return;
      }
      currentStep++;
      showStep(currentStep);
    });

    prevBtn.addEventListener('click', () => {
      if (currentStep > 0) {
        currentStep--;
        showStep(currentStep);
      }
    });

    documentType.addEventListener('change', () => {
      const val = documentType.value;
      businessFields.classList.toggle('hidden', val !== 'Business Permit');
      cedulaFields.classList.toggle('hidden', val !== 'Cedula');
      barangayIdFields.classList.toggle('hidden', val !== 'Barangay ID');
      jobSeekerFields.classList.toggle('hidden', val !== 'First Time Job Seeker');
    });

    showStep(currentStep);
  </script>
</body>
</html>