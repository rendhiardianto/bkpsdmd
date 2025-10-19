<?php
include "db.php";

// Load PHPMailer classes manually (no Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

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
            $mail->setFrom('yourgmail@gmail.com', 'Apa ini');
            $mail->addAddress($email, $fullname);

            $resetLink = "http://localhost/bkpsdmd-cms/cms/example/reset_password.php?token=$token";
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body = "Hello $fullname,<br><br>
                          Click this link to reset your password:<br>
                          <a href='$resetLink'>$resetLink</a><br><br>
                          If you didn’t request this, please ignore.<br><br><br>
                          Best Regards,<br>Tim PUSDATIN BKPSDMD Kab. Merangin";
                

            $mail->send();
            echo "<script>alert('Password reset link sent! Check your email.'); window.location='index.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password</title>
  <style>
    body { font-family: Arial; background:#f4f6f9; padding:30px; }
    .form-box { max-width:400px; margin:auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
    input, button { width:100%; padding:12px; margin:8px 0; border-radius:6px; border:1px solid #ccc; }
    button { background:#e67e22; border:none; color:white; font-weight:bold; cursor:pointer; }
    button:hover { background:#d35400; }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Forgot Password</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Enter your email" required>
      <button type="submit">Send Reset Link</button>
    </form>
    <p><a href="index.php">Back to Login</a></p>
  </div>
</body>
</html>
