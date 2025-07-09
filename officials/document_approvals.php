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

// Define document types including the new ones
$document_sections = [
    'Clearance', 
    'Business Permit', 
    'Residency', 
    'Cedula', 
    'Indigency', 
    'Solo Parent',
    'Barangay ID',
    'First Time Job Seeker'
];

// Define fields to hide for each document type
$hidden_fields = [
    'Clearance' => ['business_name', 'business_address', 'tin_number', 'profession'],
    'Business Permit' => ['tin_number', 'profession'],
    'Residency' => ['business_name', 'business_address', 'tin_number', 'profession'],
    'Cedula' => ['business_name', 'business_address'],
    'Indigency' => ['business_name', 'business_address', 'tin_number', 'profession'],
    'Solo Parent' => ['business_name', 'business_address', 'tin_number', 'profession'],
    'Barangay ID' => ['business_name', 'business_address', 'tin_number', 'profession'],
    'First Time Job Seeker' => ['business_name', 'business_address', 'tin_number', 'profession']
];

// Fetch all requests grouped by document type
$requests = [];
foreach ($document_sections as $doc_type) {
    $stmt = $conn->prepare("
        SELECT id, resident_id, resident_name, document_type, business_name, business_address, tin_number, profession, notes, purpose, request_date, status, dob, pob, citizenship, educ_attainment, course_graduated 
        FROM document_requests 
        WHERE document_type = ? AND hidden_for_official = FALSE 
        ORDER BY request_date DESC
    ");
    $stmt->bind_param("s", $doc_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $requests[$doc_type] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Document Approvals</title>
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
</head>

<!-- Success/Reject Alerts -->
<?php if (isset($_GET['reject']) && $_GET['reject'] == 1): ?>
<div id="rejectMessage" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded shadow-lg z-50 transition-opacity duration-300">
    Request rejected successfully!
</div>
<script>
    setTimeout(() => {
        const msg = document.getElementById('rejectMessage');
        if (msg) msg.style.opacity = '0';
        setTimeout(() => {
            if (msg) msg.remove();
        }, 300);
    }, 3000);
</script>
<?php endif; ?>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<div id="successMessage" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded shadow-lg z-50 transition-opacity duration-300">
    Status updated successfully!
</div>
<script>
    setTimeout(() => {
        const msg = document.getElementById('successMessage');
        if (msg) msg.style.opacity = '0';
        setTimeout(() => {
            if (msg) msg.remove();
        }, 300);
    }, 3000);
</script>
<?php endif; ?>

<body class="flex min-h-screen bg-gradient-to-br from-blue-700 via-white to-blue-500">
<?php include 'officials_sidebar.php'; ?>
<div class="flex-1 p-8 overflow-y-auto">
    <h1 class="text-3xl font-bold text-white mb-6">Document Approvals</h1>

    <!-- Tabs -->
    <div class="flex space-x-2 mb-4">
        <?php foreach ($document_sections as $index => $section): ?>
            <div 
                onclick="showSection('section<?= $index ?>', <?= $index ?>)" 
                class="cursor-pointer px-4 py-2 rounded-t-md text-white transition 
                <?= $index === 0 ? 'bg-blue-800' : 'bg-blue-600 hover:bg-blue-700' ?>" 
                id="tab<?= $index ?>">
                <?= htmlspecialchars($section) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Tables -->
    <?php foreach ($document_sections as $index => $section): ?>
        <?php $hide = $hidden_fields[$section]; ?>
        <div id="section<?= $index ?>" class="<?= $index === 0 ? '' : 'hidden' ?> bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-blue-800 mb-4"><?= htmlspecialchars($section) ?> Requests</h2>
            <?php if (empty($requests[$section])): ?>
                <p class="text-gray-600 italic">No requests found for <?= htmlspecialchars($section) ?>.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-100 text-blue-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Resident Name</th>
                                <?php if (!in_array('business_name', $hide)): ?><th class="px-4 py-2 text-left text-sm font-semibold">Business Name</th><?php endif; ?>
                                <?php if (!in_array('business_address', $hide)): ?><th class="px-4 py-2 text-left text-sm font-semibold">Business Address</th><?php endif; ?>
                                <?php if (!in_array('tin_number', $hide)): ?><th class="px-4 py-2 text-left text-sm font-semibold">TIN Number</th><?php endif; ?>
                                <?php if (!in_array('profession', $hide)): ?><th class="px-4 py-2 text-left text-sm font-semibold">Profession</th><?php endif; ?>
                                
                                <!-- NEW COLUMNS -->
                                <?php if ($section === 'Barangay ID'): ?>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">Date of Birth</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">Place of Birth</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">Citizenship</th>
                                <?php elseif ($section === 'First Time Job Seeker'): ?>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">Educational Attainment</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">Course Graduated</th>
                                <?php endif; ?>
                                
                                <th class="px-4 py-2 text-left text-sm font-semibold">Notes</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Purpose</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Request Date</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Status</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-300 text-sm">
                            <?php foreach ($requests[$section] as $request): ?>
                                <tr>
                                    <td class="px-4 py-2"><?= htmlspecialchars($request['resident_name']) ?></td>
                                    
                                    <?php if (!in_array('business_name', $hide)): ?>
                                        <td class="px-4 py-2"><?= htmlspecialchars($request['business_name']) ?></td>
                                    <?php endif; ?>
                                    <?php if (!in_array('business_address', $hide)): ?>
                                        <td class="px-4 py-2"><?= htmlspecialchars($request['business_address']) ?></td>
                                    <?php endif; ?>
                                    <?php if (!in_array('tin_number', $hide)): ?>
                                        <td class="px-4 py-2"><?= htmlspecialchars($request['tin_number']) ?></td>
                                    <?php endif; ?>
                                    <?php if (!in_array('profession', $hide)): ?>
                                        <td class="px-4 py-2"><?= htmlspecialchars($request['profession']) ?></td>
                                    <?php endif; ?>

                                    <!-- NEW DATA FIELDS -->
                                    <?php if ($section === 'Barangay ID'): ?>
                                        <td><?= htmlspecialchars($request['dob']) ?></td>
                                        <td><?= htmlspecialchars($request['pob']) ?></td>
                                        <td><?= htmlspecialchars($request['citizenship']) ?></td>
                                    <?php elseif ($section === 'First Time Job Seeker'): ?>
                                        <td><?= htmlspecialchars($request['educ_attainment']) ?></td>
                                        <td><?= htmlspecialchars($request['course_graduated']) ?></td>
                                    <?php endif; ?>
                                    
                                    <td class="px-4 py-2"><?= htmlspecialchars($request['notes']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($request['purpose']) ?></td>
                                    <td class="px-4 py-2"><?= date("Y-m-d h:i A", strtotime($request['request_date'])) ?></td>
                                    <td class="px-4 py-2 
                                        <?php
                                            if ($request['status'] === 'Pending') echo 'text-yellow-600 font-medium';
                                            elseif ($request['status'] === 'Approved') echo 'text-green-600 font-medium';
                                            elseif ($request['status'] === 'Rejected') echo 'text-red-600 font-medium';
                                        ?>">
                                        <?= htmlspecialchars($request['status']) ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?php if ($request['status'] === 'Pending'): ?>
                                            <form method="post" action="handle_approvals.php" class="inline">
                                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                                <input type="hidden" name="tab" value="<?= $index ?>">
                                                <button type="submit" name="action" value="approve" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm mr-2">Approve</button>
                                            </form>
                                            <form method="post" action="handle_approvals.php" class="inline">
                                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                                <input type="hidden" name="tab" value="<?= $index ?>">
                                                <button type="submit" name="action" value="reject" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <a href="view_request.php?id=<?= $request['id'] ?>" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium mr-4">View</a>
                                            <a href="hide_request.php?id=<?= $request['id'] ?>&tab=<?= $index ?>" 
                                               onclick="return confirm('Remove this request from your list?')" 
                                               class="text-gray-500 hover:text-gray-700 font-medium">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Script for Tab Switching -->
<script>
function showSection(idToShow, activeIndex) {
    // Hide all sections and remove tab active style
    <?php foreach ($document_sections as $index => $section): ?>
        document.getElementById('section<?= $index ?>').classList.add('hidden');
        document.getElementById('tab<?= $index ?>').classList.remove('bg-blue-800', 'text-white');
        document.getElementById('tab<?= $index ?>').classList.add('bg-blue-600', 'hover:bg-blue-700');
    <?php endforeach; ?>
    // Show selected section and highlight active tab
    document.getElementById(idToShow).classList.remove('hidden');
    const activeTab = document.getElementById('tab' + activeIndex);
    activeTab.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    activeTab.classList.add('bg-blue-800', 'text-white');
}

// Auto-show the tab from URL on page load
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') !== null ? parseInt(urlParams.get('tab')) : 0;
    const tabId = 'section' + tab;
    showSection(tabId, tab);
};
</script>

<!-- Remove ?success=1 or ?reject=1 from URL after showing once -->
<script>
    if (window.location.search.includes('success=1') || window.location.search.includes('reject=1')) {
        const url = new URL(window.location);
        url.searchParams.delete('success');
        url.searchParams.delete('reject');
        window.history.replaceState({}, document.title, url.toString());
    }
</script>

</body>
</html>