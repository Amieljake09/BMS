<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If user found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $full_name, $email_db, $hashed_password, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {

            // ✅ Check if the account is approved
            $status_query = $conn->prepare("SELECT status FROM users WHERE id = ?");
            $status_query->bind_param("i", $id);
            $status_query->execute();
            $status_result = $status_query->get_result();

            if ($status_result->num_rows === 1) {
                $user_status = $status_result->fetch_assoc()['status'];

                if ($user_status !== 'approved') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Your account is still pending approval.'
                    ]);
                    $stmt->close();
                    $conn->close();
                    exit();
                }
            }

            // ✅ Proceed with login if approved
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email_db;
            $_SESSION['role'] = $role;

            // Normalize and route
            $redirect_url = '';
            switch (strtolower(trim($role))) {
                case 'resident':
                    $redirect_url = '../resident/resident_dashboard.php';
                    break;
                case 'admin':
                    $redirect_url = '../admin/admin_dashboard.php';
                    break;
                case 'official':
                    $redirect_url = '../officials/officials_dashboard.php';
                    break;
                case 'staff':
                    $redirect_url = '../staff/staff_dashboard.php';
                    break;
                default:
                    echo json_encode([
                        'success' => false,
                        'message' => 'Unknown user role: ' . htmlspecialchars($role)
                    ]);
                    $stmt->close();
                    $conn->close();
                    exit();
            }

            echo json_encode([
                'success' => true,
                'redirect_url' => $redirect_url
            ]);

        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Incorrect password.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No account found with that email.'
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>