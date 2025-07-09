<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $token = $_POST['token'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  if ($new_password !== $confirm_password) {
    die("Passwords do not match.");
  }

  // Get email from token
  $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    die("Invalid or expired token.");
  }

  $row = $result->fetch_assoc();
  $email = $row['email'];

  // Update password
  $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
  $stmt->bind_param("ss", $hashed_password, $email);
  $stmt->execute();

  // Delete used token
  $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();

  $_SESSION['message'] = "Password updated successfully!";
  header("Location: login.php");
  exit();
}
?>
