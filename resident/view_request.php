<?php
session_start();

// Only Officials can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Resident') {
    header("Location: login.php");
    exit();
}

$request_id = intval($_GET['id']);

$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch request with resident and official info
$stmt = $conn->prepare("
    SELECT 
        r.id,
        r.document_type,
        r.purpose,
        r.status,
        r.request_date,
        r.business_name,
        r.business_address,
        r.tin_number,
        r.profession,
        r.approved_by,
        r.dob,
        r.pob,
        r.citizenship,
        r.educ_attainment,
        r.course_graduated,

        u.full_name AS resident_name,
        res.address AS resident_address,
        res.age AS resident_age,
        res.gender AS resident_gender,
        res.dob AS resident_dob,

        approver_user.full_name AS approved_by_name,
        approver_off.position AS approved_by_position
    FROM document_requests r
    JOIN users u ON r.resident_id = u.id
    LEFT JOIN residents res ON r.resident_id = res.user_id
    LEFT JOIN officials approver_off ON r.approved_by = approver_off.user_id
    LEFT JOIN users approver_user ON r.approved_by = approver_user.id
    WHERE r.id = ?
");

$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Request not found.");
}

$request = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Document</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com "></script>

    <!-- html2pdf.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js "></script>

    <style>
        @media print {
            body { padding: 20px; }
            .no-print { display: none !important; }
        }

        .signature-flex {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 5rem;
        }

        .signature-flex div {
            width: 48%;
        }

        .signature-flex div.right {
            text-align: right;
        }

        .signature-flex div.left {
            text-align: left;
        }

        /* Barangay ID Card */
        .id-card {
            border: 2px solid #333;
            border-radius: 10px;
            padding: 20px;
            max-width: 300px;
            margin: auto;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .id-card h2 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .id-card p {
            font-size: 14px;
            margin: 5px 0;
        }

        /* First Time Job Seeker Certificate */
        .ftjs-cert {
            line-height: 1.8;
        }

        .ftjs-cert strong {
            display: inline-block;
            min-width: 120px;
        }
    </style>
</head>
<body class="bg-white text-gray-800 p-10 max-w-4xl mx-auto">

<div id="document-content" class="bg-white p-8">

    <!-- Header -->
    <div class="text-center mb-10">
        <img src="../images/Gaid.png" alt="Barangay Logo" class="w-20 mx-auto mb-4">
        <h1 class="text-xl font-bold">Republic of the Philippines</h1>
        <p class="text-lg">Province of Masbate<br>Municipality of Dimasalang</p>
        <h2 class="text-2xl font-bold mt-2 uppercase">Barangay Gaid, Dimasalang, Masbate</h2>
        <hr class="my-4 border-t-2 border-gray-400">
        <h3 class="text-xl font-semibold underline"><?= htmlspecialchars($request['document_type']) ?></h3>
    </div>

    <!-- Content -->
    <div class="mb-10 leading-relaxed">
        <?php if ($request['document_type'] == 'Clearance'): ?>
            <p>To Whom It May Concern:</p>
            <p>This is to certify that <strong><?= htmlspecialchars($request['resident_name']) ?></strong>, a <strong><?= $request['resident_age'] ?>-year-old <?= strtolower($request['resident_gender']) ?></strong>, residing at <strong><?= htmlspecialchars($request['resident_address']) ?></strong>, is a bonafide resident of Barangay Gaid, Dimasalang, Masbate.</p>
            <p>This certification is being issued upon the request of the person mentioned above for <strong><?= htmlspecialchars($request['purpose']) ?></strong>.</p>

        <?php elseif ($request['document_type'] == 'Residency'): ?>
            <p>This is to certify that <strong><?= htmlspecialchars($request['resident_name']) ?></strong> is a permanent resident of Barangay Gaid, Dimasalang, Masbate.</p>
            <p>This certificate is issued upon the request of the person mentioned above for <strong><?= htmlspecialchars($request['purpose']) ?></strong>.</p>

        <?php elseif ($request['document_type'] == 'Cedula'): ?>
            <p>This certifies that <strong><?= htmlspecialchars($request['resident_name']) ?></strong>, age <?= $request['resident_age'] ?>, residing at <strong><?= htmlspecialchars($request['resident_address']) ?></strong>, has paid the community tax and other fees accordingly.</p>
            <p><strong>TIN No.:</strong> <?= htmlspecialchars($request['tin_number']) ?><br>
            <strong>Profession:</strong> <?= htmlspecialchars($request['profession']) ?></p>

        <?php elseif ($request['document_type'] == 'Business Permit'): ?>
            <p>This is to certify that the business named <strong><?= htmlspecialchars($request['business_name']) ?></strong>, located at <strong><?= htmlspecialchars($request['business_address']) ?></strong>, is authorized to operate within the jurisdiction of Barangay Gaid, Dimasalang, Masbate.</p>
            <p>Issued this <strong><?= date('F j, Y') ?></strong>.</p>

        <?php elseif ($request['document_type'] == 'Barangay ID'): ?>
    <!-- Barangay ID Card -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden max-w-md mx-auto my-8">
        <!-- Header with Logo -->
        <div class="flex items-center justify-between p-4 bg-green-600 text-white">
            <img src="../images/Gaid.png" alt="Barangay Logo" class="w-16 h-16 mr-4">
            <div>
                <h2 class="text-xl font-bold">Republic of the Philippines</h2>
                <p class="text-sm">Province of Masbate<br>Municipality of Dimasalang</p>
                <h3 class="text-lg font-semibold">Barangay Gaid, Dimasalang, Masbate</h3>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-4">
            <!-- Name and Address -->
            <div class="mb-4">
                <h2 class="text-xl font-bold"><?= htmlspecialchars($request['resident_name']) ?></h2>
                <p class="text-sm"><strong>Address:</strong> #227 Mayon St., Bgy. N.S. Amoranto</p>
            </div>

            <!-- Personal Info -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($request['dob']) ?></p>
                    <p><strong>Place of Birth:</strong> <?= htmlspecialchars($request['pob']) ?></p>
                    <p><strong>Citizenship:</strong> <?= htmlspecialchars($request['citizenship']) ?></p>
                </div>
                <div>
                    <!-- Placeholder for Photo -->
                    <img src="https://via.placeholder.com/150 " alt="Photo" class="w-32 h-32 rounded-full object-cover">
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-4 text-sm italic">
                This ID is valid for official purposes only.
            </div>
        </div>
    </div>

        <?php elseif ($request['document_type'] == 'First Time Job Seeker'): 
    // Get current date
    $issuedDate = new DateTime(); // Today's date
    $validUntil = clone $issuedDate;
    $validUntil->modify('+1 year'); // Add 1 year

    // Format dates
    $issuedDateStr = $issuedDate->format('F jS, Y');
    $validUntilStr = $validUntil->format('F jS, Y');
?>
    <div class="ftjs-cert">
        <p><strong>This is to certify that Mr./Ms.</strong> <?= strtoupper(htmlspecialchars($request['resident_name'])) ?>, a resident of #227 Mayon St., Bgy. N.S. Amoranto for 7 months, is a qualified availor of RA 11262 or the First Time Jobseekers Act of 2019.</p>

        <p><strong>I further certify that the holder/bearer has been informed of his/her rights,</strong> including the duties and responsibilities accorded by RA 11262 through the Oath of Undertaking he/she has signed and executed in the presence of our Barangay Officials.</p>

        <p><strong>Signed this <?= $issuedDateStr ?></strong>, in Quezon City, Metro Manila.</p>

        <p><strong>This certification is valid only until <?= $validUntilStr ?> (one year from the date of issuance).</strong></p>
    </div>

        <?php else: ?>
            <p>This is to certify that <strong><?= htmlspecialchars($request['resident_name']) ?></strong> is a resident of Barangay Gaid, Dimasalang, Masbate and this certificate is issued for <strong><?= htmlspecialchars($request['purpose']) ?></strong>.</p>
        <?php endif; ?>
    </div>

    <!-- Signature Section -->
    <div class="signature-flex mt-10">
        <!-- Left - Approved By -->
        <?php if ($request['status'] === 'Approved' && !empty($request['approved_by_name'])): ?>
            <div class="left">
                <p>Approved by:</p>
                <p class="font-bold mt-2"><?= htmlspecialchars($request['approved_by_name']) ?></p>
                <p><?= htmlspecialchars($request['approved_by_position']) ?></p>
            </div>
        <?php else: ?>
            <div class="left">&nbsp;</div>
        <?php endif; ?>

        <!-- Right - Prepared By -->
        <div class="right">
            <p>Prepared by:</p>
            <p class="font-bold mt-2">Punong Barangay Amiel Jake Baril</p>
            <p>Punong Barangay</p>
        </div>
    </div>

</div>

<!-- Buttons -->
<div class="flex justify-end gap-4 mt-10 no-print">
    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow">
        🖨️ Print / Save as PDF
    </button>
    <button onclick="downloadPDF()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow">
        💾 Download PDF
    </button>
</div>

<!-- Script para sa PDF download -->
<script>
    function downloadPDF() {
        const element = document.getElementById('document-content');
        const opt = {
            margin:       0.5,
            filename:     '<?= htmlspecialchars($request['document_type']) ?>_<?= date('YmdHis') ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>

</body>
</html>