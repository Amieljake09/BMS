<?php
session_start();
require 'db_connect.php'; // update with your DB connection script

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST['email'];

  // Check if email exists
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    $_SESSION['message'] = "Email not found.";
    header("Location: forgot_password.php");
    exit();
  }

  // Generate and store token
  $token = bin2hex(random_bytes(32));
  $url = "http://yourdomain.com/reset_password.php?token=$token";

  $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
  $stmt->bind_param("ss", $email, $token);
  $stmt->execute();

  // Send email (update with your mailer logic)
  $subject = "Reset Your Password";
  $message = "Click the link to reset your password: $url";
  $headers = "From: yoursystem@example.com";

  mail($email, $subject, $message, $headers); // or use PHPMailer

  $_SESSION['message'] = "Reset link sent to your email.";
  header("Location: forgot_password.php");
  exit();
}
?>
