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

// Fetch all reports
$reports = $conn->query("SELECT * FROM community_reports ORDER BY created_at DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Barangay Officials Dashboard</title>
    <script src="https://cdn.tailwindcss.com "></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
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
    <h1 class="text-3xl font-bold text-white">Community Reports</h1>
    <div class="relative">
      <button class="relative focus:outline-none">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M15 17h5l-1.405-1.405A2 2 0 0118 14V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C8.67 6.165 8 7.388 8 9v5c0 .386-.149.735-.405 1.001L6 17h5m4 0v1a2 2 0 11-4 0v-1m4 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="absolute top-0 right-0 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
      </button>
    </div>
  </div>

  <!-- Reports List -->
  <div class="space-y-6">
    <?php if ($reports->num_rows === 0): ?>
      <p class="text-gray-700 bg-white p-4 rounded shadow">Walang narekord na report.</p>
    <?php else: ?>
      <?php while ($report = $reports->fetch_assoc()): ?>
        <div class="bg-white p-6 rounded shadow">
          <div class="flex justify-between items-start mb-4">
            <h2 class="text-xl font-semibold"><?= htmlspecialchars($report['title']) ?></h2>
            <span class="px-3 py-1 text-sm rounded-full 
              <?= $report['status'] === 'Resolved' ? 'bg-green-100 text-green-800' : 
                 ($report['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                 ($report['status'] === 'Ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) ?>">
              <?= htmlspecialchars($report['status']) ?>
            </span>
          </div>

          <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mb-4">
            <div><strong>Uri ng Report:</strong> <?= htmlspecialchars($report['report_type']) ?></div>
            <div><strong>Kategorya:</strong> <?= htmlspecialchars($report['category']) ?></div>
            <div><strong>Lokasyon:</strong> <?= htmlspecialchars($report['location']) ?></div>
            <div><strong>Petsa:</strong> <?= date("F j, Y, g:i a", strtotime($report['created_at'])) ?></div>
            <div><strong>Nagsumite:</strong> <?= htmlspecialchars($report['submitter_name']) ?></div>
            <div><strong>Kontak:</strong> <?= htmlspecialchars($report['submitter_contact']) ?></div>
            <div><strong>Priority:</strong> <?= htmlspecialchars($report['priority_level']) ?></div>
          </div>

          <div class="mb-4">
            <strong>Deskripsyon:</strong>
            <p class="mt-1"><?= nl2br(htmlspecialchars($report['description'])) ?></p>
          </div>

          <!-- Evidence Section -->
          <?php if (!empty($report['evidence'])):
            $evidence_files = json_decode($report['evidence']); ?>
            <div class="mb-4">
              <strong>Ebidensya:</strong>
              <div class="mt-2 flex flex-wrap gap-2">
                <?php foreach ($evidence_files as $file):
                  $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                  if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="<?= $file ?>" alt="Ebidensya" class="max-w-xs rounded border">
                  <?php elseif (in_array($ext, ['mp4', 'webm', 'ogg'])): ?>
                    <video controls class="max-w-xs rounded border">
                      <source src="<?= $file ?>" type="video/<?= $ext ?>">
                      Your browser does not support the video tag.
                    </video>
                  <?php else: ?>
                    <a href="<?= $file ?>" target="_blank" class="text-blue-500 underline"><?= basename($file) ?></a>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Action Buttons (Optional for Future Use) -->
          <!-- You can add buttons here later for updating status or viewing more info -->

        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

</div>

</body>
</html>