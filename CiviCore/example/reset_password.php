<?php
include "db.php";

if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    $result = $conn->query("SELECT * FROM users WHERE reset_token='$token'");

    if ($result->num_rows > 0) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$newPass', reset_token=NULL WHERE reset_token='$token'");
            echo "<script>alert('Password reset successful! You can now login.'); window.location='index.php';</script>";
        }
    } else {
        echo "<h2>Invalid or expired reset link ❌</h2>";
        exit;
    }
} else {
    echo "<h2>No reset token provided</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reset Password</title>
  <style>
    body { font-family: Arial; background:#f4f6f9; padding:30px; }
    .form-box { max-width:400px; margin:auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
    input, button { width:100%; padding:12px; margin:8px 0; border-radius:6px; border:1px solid #ccc; }
    button { background:#3498db; border:none; color:white; font-weight:bold; cursor:pointer; }
    button:hover { background:#2980b9; }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Reset Password</h2>
    <form method="POST">
      <input type="password" name="password" placeholder="Enter new password" required>
      <button type="submit">Reset Password</button>
    </form>
  </div>
</body>
</html>
