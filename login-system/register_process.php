<?php
// Database configuration
$host = 'localhost:3307';
$username = 'root';
$password = '';
$dbname = 'barangay_management_system';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Common fields
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $conn->real_escape_string($_POST['role']);

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

    if (!$stmt->execute()) {
        die("Error registering user: " . $conn->error);
    }

    $user_id = $stmt->insert_id;

    if ($role === 'Resident') {
        $dob = $conn->real_escape_string($_POST['dob'] ?? '');
        $pob = $conn->real_escape_string($_POST['pob'] ?? '');
        $age = intval($_POST['age'] ?? 0);
        $gender = $conn->real_escape_string($_POST['gender'] ?? '');
        $civil_status = $conn->real_escape_string($_POST['civil_status'] ?? '');
        $nationality = $conn->real_escape_string($_POST['nationality'] ?? '');
        $religion = $conn->real_escape_string($_POST['religion'] ?? '');
        $address = $conn->real_escape_string($_POST['address'] ?? '');
        $phone = $conn->real_escape_string($_POST['phone'] ?? '');
        $res_email = $conn->real_escape_string($_POST['res_email'] ?? '');
        $resident_type = $conn->real_escape_string($_POST['resident_type'] ?? '');
        $stay_length = $conn->real_escape_string($_POST['stay_length'] ?? '');
        $employment_status = $conn->real_escape_string($_POST['employment_status'] ?? '');

        // File Upload Handling for Proof of Residency
        $proof = '';

        if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            $fileType = mime_content_type($_FILES['proof']['tmp_name']);
            $fileSize = $_FILES['proof']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                die("Invalid file type. Only JPG, PNG, and PDF are allowed.");
            }

            if ($fileSize > 2 * 1024 * 1024) { // 2MB limit
                die("File size exceeds 2MB.");
            }

            $uploadDir = 'uploads/';
            $fileName = uniqid('proof_') . '_' . basename($_FILES['proof']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetPath)) {
                $proof = $targetPath;
            } else {
                die("Failed to move uploaded file.");
            }
        } else {
            die("Proof of residency is required.");
        }

        $stmt2 = $conn->prepare("
            INSERT INTO residents (
                user_id, dob, pob, age, gender, civil_status,
                nationality, religion, address, phone, res_email,
                resident_type, stay_length, proof, employment_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt2->bind_param(
            "issssssssssssss",
            $user_id, $dob, $pob, $age, $gender, $civil_status,
            $nationality, $religion, $address, $phone, $res_email,
            $resident_type, $stay_length, $proof, $employment_status
        );

        if (!$stmt2->execute()) {
            die("Error saving resident info: " . $conn->error);
        }
    } elseif ($role === 'Official') {
        $dob_off = $conn->real_escape_string($_POST['dob_off'] ?? '');
        $pob_off = $conn->real_escape_string($_POST['pob_off'] ?? '');
        $age_off = intval($_POST['age_off'] ?? 0);
        $gender_off = $conn->real_escape_string($_POST['gender_off'] ?? '');
        $civil_status_off = $conn->real_escape_string($_POST['civil_status_off'] ?? '');
        $nationality_off = $conn->real_escape_string($_POST['nationality_off'] ?? '');
        $religion_off = $conn->real_escape_string($_POST['religion_off'] ?? '');
        $position = $conn->real_escape_string($_POST['position'] ?? '');
        $term_start = $conn->real_escape_string($_POST['term_start'] ?? '');
        $term_end = $conn->real_escape_string($_POST['term_end'] ?? '');
        $address_off = $conn->real_escape_string($_POST['address_off'] ?? '');
        $phone_off = $conn->real_escape_string($_POST['phone_off'] ?? '');
        $email_off = $conn->real_escape_string($_POST['email_off'] ?? '');

        $stmt2 = $conn->prepare("
            INSERT INTO officials (
                user_id, dob, pob, age, gender, civil_status,
                nationality, religion, position, term_start, term_end,
                address, phone, email_off
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt2->bind_param(
            "isssssssssssss",
            $user_id, $dob_off, $pob_off, $age_off, $gender_off, $civil_status_off,
            $nationality_off, $religion_off, $position, $term_start, $term_end,
            $address_off, $phone_off, $email_off
        );

        if (!$stmt2->execute()) {
            die("Error saving official info: " . $conn->error);
        }
    } elseif ($role === 'Staff') {
        $address = $conn->real_escape_string($_POST['complete_address'] ?? '');
        $phone = $conn->real_escape_string($_POST['phone_staff'] ?? '');
        $email_staff = $conn->real_escape_string($_POST['email_staff'] ?? '');
        $position = $conn->real_escape_string($_POST['position_staff'] ?? '');
        $date_started = $conn->real_escape_string($_POST['date_started'] ?? '');

        if (empty($address) || empty($phone) || empty($position) || empty($date_started)) {
            die("Incomplete staff information.");
        }

        $stmt2 = $conn->prepare("
            INSERT INTO staff (
                user_id, address, phone, email, position, date_started
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt2->bind_param(
            "isssss",
            $user_id, $address, $phone, $email_staff, $position, $date_started
        );

        if (!$stmt2->execute()) {
            die("Error saving staff info: " . $conn->error);
        }
    } elseif ($role === 'Admin') {
        $admin_position = $conn->real_escape_string($_POST['admin_position'] ?? '');
        $admin_phone = $conn->real_escape_string($_POST['admin_phone'] ?? '');
        $admin_email = $conn->real_escape_string($_POST['admin_email'] ?? '');

        if (empty($admin_position) || empty($admin_phone) || empty($admin_email)) {
            die("Please fill in all required admin fields.");
        }

        $stmt2 = $conn->prepare("
            INSERT INTO admins (
                full_name, email, password, position, phone
            ) VALUES (?, ?, ?, ?, ?)
        ");
        $stmt2->bind_param(
            "sssss",
            $full_name, $admin_email, $hashed_password, $admin_position, $admin_phone
        );

        if (!$stmt2->execute()) {
            die("Error saving admin info: " . $conn->error);
        }
    }

    echo "<script>alert('Registration successful, please wait for approval of admin!'); window.location.href='login.php';</script>";
}
?>