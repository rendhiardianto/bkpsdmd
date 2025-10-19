<?php
include "../db.php";

if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    $result = $conn->query("SELECT * FROM users WHERE reset_token='$token'");

    if ($result->num_rows > 0) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$newPass', reset_token=NULL WHERE reset_token='$token'");
            echo "<script>alert('Reset Kata Sandi berhasil! Anda bisa login sekarang.'); window.location='../index.php';</script>";
        }
    } else {
        echo "<h2>Invalid atau link reset kadaluarsa ❌</h2>";
        exit;
    }
} else {
    echo "<h2>Reset token tidak tersedia</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <style>
    body { font-family: Arial; background:#f4f6f9; padding:30px; }
    .form-box { max-width:500px; margin:auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
    input, button { width:95%; padding:12px; margin:8px 0;  border:1px solid #ccc; }
    button { background:#3498db; margin: auto; display: block; border:none; width:50%; color:white; font-weight:bold; cursor:pointer; }
    button:hover { background:#2980b9; }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Reset Kata Sandi</h2>
    <form method="POST">
      <input type="password" name="password" placeholder="Masukkan Kata Sandi Baru" required>
      <button type="submit">Reset Kata Sandi</button>
    </form>
  </div>
</body>
</html>
