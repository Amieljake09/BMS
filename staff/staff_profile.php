<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "barangay_management_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch user and staff data
$sql = "SELECT u.full_name, u.email, u.password, u.role, s.address, s.phone, s.email AS staff_email, s.position
        FROM users u
        JOIN staff s ON u.id = s.user_id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No record found.");
}
$row = $result->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Editable fields
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $staff_email = $conn->real_escape_string($_POST['staff_email']);
$position = isset($_POST['position']) ? $conn->real_escape_string($_POST['position']) : '';

    // Password fields
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Only validate password if user wants to change it
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $error = "Please enter your current password to change your password.";
        } elseif (!password_verify($current_password, $row['password'])) {
            $error = "Current password is incorrect.";
        }
        if (empty($new_password)) {
            $error = "New password is required.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        }
    }

    if (empty($error)) {
        // Update password only if new password is provided
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_user_sql = "UPDATE users SET full_name = ?, email = ?, password = ? WHERE id = ?";
            $update_user_stmt = $conn->prepare($update_user_sql);
            $update_user_stmt->bind_param("sssi", $full_name, $email, $hashed_password, $user_id);
        } else {
            $update_user_sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
            $update_user_stmt = $conn->prepare($update_user_sql);
            $update_user_stmt->bind_param("ssi", $full_name, $email, $user_id);
        }
        $update_user_stmt->execute();

        // Update staff table
        $update_staff_sql = "UPDATE staff SET address = ?, phone = ?, email = ?, position = ? WHERE user_id = ?";
        $update_staff_stmt = $conn->prepare($update_staff_sql);
        $update_staff_stmt->bind_param("ssssi", $address, $phone, $staff_email, $position, $user_id);

        if ($update_staff_stmt->execute()) {
            if (!empty($new_password)) {
                echo "<script>alert('Password changed successfully!'); window.location.href='staff_profile.php';</script>";
            } else {
                echo "<script>alert('Personal information updated successfully!'); window.location.href='staff_profile.php';</script>";
            }
            exit();
        } else {
            $error = "Error updating profile.";
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Barangay Staff Profile</title>
    <script src="https://cdn.tailwindcss.com "></script>
    <style>
        canvas { max-width: 100%; }
        #sidebar { position: fixed; top: 0; left: 0; height: 100%; z-index: 50; }
        .main-content { margin-left: 16rem; padding: 2rem; overflow-y: auto; height: 100vh; }
    </style>
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
<?php include 'staff_sidebar.php'; ?>
<!-- Main Content -->
<div class="p-6 ml-64 mt-46">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">My Staff Profile</h1>

    <!-- Success / Error Messages -->
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-inner"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-inner"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" class="bg-white shadow-lg rounded-xl p-6 space-y-6 transition-all duration-300 transform hover:shadow-xl">
        <!-- User Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($row['full_name']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
        </div>

        <!-- Role (Fixed) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <input type="text" value="<?= htmlspecialchars($row['role']) ?>" class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-md" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                <input type="text" value="<?= htmlspecialchars($row['position']) ?>" class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-md" readonly>
            </div>
        </div>

        <!-- Password Change Section -->
        <div class="border-t pt-5 mt-5">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Change Password</h2>
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 text-red-800 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Current Password -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" id="current_password" name="current_password" placeholder="••••••••" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <button type="button" onclick="togglePassword('current_password')" class="absolute right-3 top-9 transform -translate-y-1/2 text-gray-500 text-xs underline">Show</button>
                </div>
                <!-- New Password -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400">
                    <button type="button" onclick="togglePassword('new_password')" class="absolute right-3 top-9 transform -translate-y-1/2 text-gray-500 text-xs underline">Show</button>
                </div>
                <!-- Confirm Password -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter new password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400">
                    <button type="button" onclick="togglePassword('confirm_password')" class="absolute right-3 top-9 transform -translate-y-1/2 text-gray-500 text-xs underline">Show</button>
                </div>
            </div>

            <!-- Password Strength Indicator -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Strength</label>
                <div id="password-strength-meter" class="w-full h-2 bg-gray-200 rounded mb-1">
                    <div id="password-strength-fill" class="h-full rounded" style="width: 0;"></div>
                </div>
                <p id="password-strength-text" class="text-sm text-gray-600"></p>
            </div>
        </div>

        <!-- Staff Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($row['address']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Official Email</label>
                <input type="email" name="staff_email" value="<?= htmlspecialchars($row['staff_email']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition-all duration-300">
                Update Profile
            </button>
        </div>
    </form>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const button = event.target;
        input.type = input.type === 'password' ? 'text' : 'password';
        button.textContent = input.type === 'password' ? 'Show' : 'Hide';
    }

    // Password Strength Checker
    document.getElementById('new_password').addEventListener('input', function () {
        const password = this.value;
        const strengthMeter = document.getElementById('password-strength-fill');
        const strengthText = document.getElementById('password-strength-text');
        let strength = 0;

        if (password.length >= 8) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;

        if (password === '') {
            strengthMeter.style.width = '0%';
            strengthMeter.style.backgroundColor = 'transparent';
            strengthText.textContent = '';
        } else if (strength < 2) {
            strengthMeter.style.width = '30%';
            strengthMeter.style.backgroundColor = '#ef4444';
            strengthText.textContent = 'Weak';
            strengthText.className = 'text-red-500 text-sm';
        } else if (strength === 2 || strength === 3) {
            strengthMeter.style.width = '60%';
            strengthMeter.style.backgroundColor = '#f59e0b';
            strengthText.textContent = 'Medium';
            strengthText.className = 'text-amber-600 text-sm';
        } else {
            strengthMeter.style.width = '100%';
            strengthMeter.style.backgroundColor = '#10b981';
            strengthText.textContent = 'Strong';
            strengthText.className = 'text-green-600 text-sm';
        }
    });
</script>
</body>
</html>