<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $conn->real_escape_string($_POST['token']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $result = $conn->query("SELECT * FROM users WHERE reset_token='$token' AND reset_expiry > NOW() LIMIT 1");

    if ($result->num_rows > 0) {
        $conn->query("UPDATE users SET password='$password', reset_token=NULL, reset_expiry=NULL WHERE reset_token='$token'");
        echo "<script>alert('Password updated! You can now login.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Invalid or expired reset link.'); window.location='forgot_password.php';</script>";
    }
}
?>
