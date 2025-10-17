<?php
session_start();
include "../db.php";
include "../config.php";

// Load PHPMailer classes manually (no Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $fullname = $user['fullname'];

        $token = bin2hex(random_bytes(16));
        $conn->query("UPDATE users SET reset_token='$token' WHERE email='$email'");

        // Send reset email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bkd.merangin@gmail.com';  // 🔹 replace with your Gmail
            $mail->Password = 'dlmh zkgz awku aokg';   // 🔹 use Gmail App Password (not normal password!)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('yourgmail@gmail.com', 'BKPSDMD Kab. Merangin');
            $mail->addAddress($email, $fullname);

            $resetLink = $baseUrl . "/forgot_password/reset_password.php?token=" . urlencode($token);
            $mail->isHTML(true);
            $mail->Subject = "Reset Password Akun CMS";
            $mail->Body = "
                Halo #KantiASN $fullname,<br><br>
                Klik tombol di bawah ini untuk reset password akun Anda:<br><br>
                <a href='$resetLink' 
                  style='display:inline-block; padding:12px 24px; 
                          background-color:#007bff; color:#ffffff; 
                          text-decoration:none; font-size:16px; 
                          border-radius:5px;'>
                          Reset Password
                </a>
                
                <br><br>Jika Anda tidak meminta akses ini, abaikan saja.

                <br><br>
                Jika tombol di atas tidak berfungsi, Anda juga bisa klik link ini:<br>
                <br><a href='$resetLink'>$resetLink</a><br><br>

                Best Regards,<br>
                Tim PUSDATIN BKPSDMD Kab. Merangin
                ";
                

            $mail->send();
            echo "<script>alert('Link Reset Passsword sudah terkirim! Periksa email Anda.'); window.location='../index.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Email ini tidak terdaftar pada akun CMS.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Kata Sandi</title>
  <link href="forgot_password.css" rel="stylesheet">
</head>
<body>
  <div class="form-box">
    <h2>Lupa Kata Sandi</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Masukkan email Anda" required>
      <button type="submit">Kirim Link Reset Kata Sandi</button>
    </form>
    <p><a href="../index.php">Kembali ke halaman Login</a></p>
  </div>
</body>
</html>
